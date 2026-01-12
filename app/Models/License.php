<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'plan_id',
        'license_key',
        'plan_type',
        'max_users',
        'max_items',
        'max_transactions_per_month',
        'features',
        'issued_at',
        'starts_at',
        'expires_at',
        'is_active',
        'activated_at',
        'activation_ip',
        'activation_domain',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'features' => 'array',
        'issued_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'activated_at' => 'datetime',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'max_items' => 'integer',
        'max_transactions_per_month' => 'integer',
    ];

    /**
     * Get the organization this license belongs to
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the subscription plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Get the user who created this license
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get all logs for this license
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LicenseLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if license is valid
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->expires_at > now();
    }

    /**
     * Check if license is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Check if license is expiring soon (within 7 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expires_at <= now()->addDays(7) 
            && $this->expires_at > now();
    }

    /**
     * Get days until expiration
     */
    public function daysUntilExpiry(): int
    {
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Generate a unique license key
     */
    public static function generateKey(): string
    {
        do {
            $key = strtoupper(
                substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
                substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
                substr(md5(uniqid(mt_rand(), true)), 0, 4) . '-' .
                substr(md5(uniqid(mt_rand(), true)), 0, 4)
            );
        } while (self::where('license_key', $key)->exists());

        return $key;
    }

    /**
     * Activate the license
     */
    public function activate(string $ip = null, string $domain = null): bool
    {
        $this->update([
            'activated_at' => now(),
            'activation_ip' => $ip,
            'activation_domain' => $domain,
        ]);

        $this->logs()->create([
            'action' => 'activated',
            'performed_by' => auth()->id(),
            'ip_address' => $ip,
            'metadata' => ['domain' => $domain],
        ]);

        return true;
    }

    /**
     * Suspend the license
     */
    public function suspend(string $reason = null): bool
    {
        $this->update(['is_active' => false]);

        $this->logs()->create([
            'action' => 'suspended',
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'notes' => $reason,
        ]);

        return true;
    }

    /**
     * Reactivate a suspended license
     */
    public function reactivate(): bool
    {
        $this->update(['is_active' => true]);

        $this->logs()->create([
            'action' => 'reactivated',
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);

        return true;
    }

    /**
     * Extend the license by given days
     */
    public function extend(int $days): bool
    {
        $oldExpiry = $this->expires_at;
        $newExpiry = $this->expires_at > now() 
            ? $this->expires_at->addDays($days) 
            : now()->addDays($days);

        $this->update(['expires_at' => $newExpiry]);

        $this->logs()->create([
            'action' => 'extended',
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'metadata' => [
                'days_added' => $days,
                'old_expiry' => $oldExpiry->toDateTimeString(),
                'new_expiry' => $newExpiry->toDateTimeString(),
            ],
        ]);

        return true;
    }

    /**
     * Scope to filter active licenses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to filter expired licenses
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to filter expiring soon (within 7 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now());
    }

    /**
     * Check if a feature is enabled
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }
}
