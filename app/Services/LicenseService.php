<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseLog;
use App\Models\Organization;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    /**
     * Generate a unique license key
     * Format: XXXX-XXXX-XXXX-XXXX
     */
    public function generateLicenseKey(): string
    {
        do {
            $key = strtoupper(
                $this->randomSegment() . '-' .
                $this->randomSegment() . '-' .
                $this->randomSegment() . '-' .
                $this->randomSegment()
            );
        } while (License::where('license_key', $key)->exists());

        return $key;
    }

    /**
     * Generate random 4-character segment
     */
    protected function randomSegment(): string
    {
        return substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }

    /**
     * Create a new license for an organization
     */
    public function createLicense(Organization $organization, array $data): License
    {
        DB::beginTransaction();

        try {
            $plan = null;
            if (isset($data['plan_id'])) {
                $plan = SubscriptionPlan::find($data['plan_id']);
            } elseif (isset($data['plan_type'])) {
                $plan = SubscriptionPlan::where('code', $data['plan_type'])->first();
            }

            $license = License::create([
                'organization_id' => $organization->id,
                'plan_id' => $plan?->id,
                'license_key' => $this->generateLicenseKey(),
                'plan_type' => $data['plan_type'] ?? $plan?->code ?? 'basic',
                'max_users' => $data['max_users'] ?? $plan?->max_users ?? 5,
                'max_items' => $data['max_items'] ?? $plan?->max_items ?? 1000,
                'max_transactions_per_month' => $data['max_transactions_per_month'] ?? $plan?->max_transactions_per_month ?? 10000,
                'features' => $data['features'] ?? $plan?->features ?? null,
                'starts_at' => $data['starts_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? now()->addDays($plan?->validity_days ?? 30),
                'is_active' => true,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Log the creation
            $license->logs()->create([
                'action' => 'created',
                'performed_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'metadata' => [
                    'plan_type' => $license->plan_type,
                    'expires_at' => $license->expires_at->toDateTimeString(),
                ],
            ]);

            DB::commit();

            Log::info('License created', [
                'license_id' => $license->id,
                'organization_id' => $organization->id,
                'key' => $license->license_key,
            ]);

            return $license;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create license', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate a license key
     */
    public function validateLicense(string $key): array
    {
        $license = License::where('license_key', $key)->first();

        if (!$license) {
            return [
                'valid' => false,
                'code' => 'not_found',
                'message' => 'Invalid license key. Please check and try again.',
            ];
        }

        if (!$license->is_active) {
            return [
                'valid' => false,
                'code' => 'suspended',
                'message' => 'This license has been suspended. Please contact support.',
                'license' => $license,
            ];
        }

        if ($license->starts_at > now()) {
            return [
                'valid' => false,
                'code' => 'not_started',
                'message' => 'This license is not yet active. It starts on ' . $license->starts_at->format('d M Y'),
                'license' => $license,
            ];
        }

        if ($license->expires_at < now()) {
            return [
                'valid' => false,
                'code' => 'expired',
                'message' => 'This license has expired on ' . $license->expires_at->format('d M Y') . '. Please renew.',
                'license' => $license,
            ];
        }

        return [
            'valid' => true,
            'code' => 'valid',
            'message' => 'License is valid.',
            'license' => $license,
            'organization' => $license->organization,
            'days_remaining' => $license->daysUntilExpiry(),
        ];
    }

    /**
     * Activate a license
     */
    public function activateLicense(string $key, string $ip = null, string $domain = null): array
    {
        $validation = $this->validateLicense($key);

        if (!$validation['valid']) {
            return $validation;
        }

        $license = $validation['license'];

        // Check if already activated
        if ($license->activated_at) {
            return [
                'success' => true,
                'message' => 'License was already activated.',
                'license' => $license,
            ];
        }

        $license->activate($ip ?? request()->ip(), $domain);

        return [
            'success' => true,
            'message' => 'License activated successfully.',
            'license' => $license->fresh(),
            'organization' => $license->organization,
        ];
    }

    /**
     * Suspend a license
     */
    public function suspendLicense(License $license, string $reason = null): bool
    {
        return $license->suspend($reason);
    }

    /**
     * Reactivate a suspended license
     */
    public function reactivateLicense(License $license): bool
    {
        return $license->reactivate();
    }

    /**
     * Extend a license
     */
    public function extendLicense(License $license, int $days): bool
    {
        return $license->extend($days);
    }

    /**
     * Renew a license with a new plan
     */
    public function renewLicense(License $license, array $data): License
    {
        DB::beginTransaction();

        try {
            $plan = isset($data['plan_id']) 
                ? SubscriptionPlan::find($data['plan_id']) 
                : null;

            $newExpiry = $license->expires_at > now() 
                ? $license->expires_at->addDays($data['days'] ?? $plan?->validity_days ?? 30)
                : now()->addDays($data['days'] ?? $plan?->validity_days ?? 30);

            $license->update([
                'plan_id' => $plan?->id ?? $license->plan_id,
                'plan_type' => $data['plan_type'] ?? $plan?->code ?? $license->plan_type,
                'max_users' => $data['max_users'] ?? $plan?->max_users ?? $license->max_users,
                'max_items' => $data['max_items'] ?? $plan?->max_items ?? $license->max_items,
                'max_transactions_per_month' => $data['max_transactions_per_month'] ?? $plan?->max_transactions_per_month ?? $license->max_transactions_per_month,
                'features' => $data['features'] ?? $plan?->features ?? $license->features,
                'expires_at' => $newExpiry,
                'is_active' => true,
            ]);

            $license->logs()->create([
                'action' => 'renewed',
                'performed_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'metadata' => [
                    'new_expiry' => $newExpiry->toDateTimeString(),
                    'plan_type' => $license->plan_type,
                ],
            ]);

            DB::commit();

            return $license->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to renew license', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get license statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => License::count(),
            'active' => License::active()->count(),
            'expired' => License::expired()->count(),
            'expiring_soon' => License::expiringSoon()->count(),
            'suspended' => License::where('is_active', false)->count(),
        ];
    }

    /**
     * Check usage limits for a license
     */
    public function checkUsageLimits(License $license): array
    {
        $organization = $license->organization;

        $currentUsers = $organization->users()->count();
        $currentItems = $organization->items()->count();

        return [
            'users' => [
                'current' => $currentUsers,
                'limit' => $license->max_users,
                'remaining' => max(0, $license->max_users - $currentUsers),
                'exceeded' => $currentUsers > $license->max_users,
            ],
            'items' => [
                'current' => $currentItems,
                'limit' => $license->max_items,
                'remaining' => max(0, $license->max_items - $currentItems),
                'exceeded' => $currentItems > $license->max_items,
            ],
        ];
    }
}
