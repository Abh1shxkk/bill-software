<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Organization;
use App\Models\SubscriptionPlan;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Display a listing of licenses
     */
    public function index(Request $request)
    {
        $query = License::with(['organization', 'plan']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                  ->orWhereHas('organization', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)->where('expires_at', '>', now());
                    break;
                case 'expired':
                    $query->where('expires_at', '<', now());
                    break;
                case 'suspended':
                    $query->where('is_active', false);
                    break;
                case 'expiring':
                    $query->where('expires_at', '<=', now()->addDays(7))
                          ->where('expires_at', '>', now());
                    break;
            }
        }

        // Plan type filter
        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        $licenses = $query->orderBy('created_at', 'desc')->paginate(15);
        $plans = SubscriptionPlan::active()->get();

        return view('superadmin.licenses.index', compact('licenses', 'plans'));
    }

    /**
     * Show the form for creating a new license
     */
    public function create(Request $request)
    {
        $organizations = Organization::orderBy('name')->get();
        $plans = SubscriptionPlan::active()->get();
        $selectedOrg = $request->organization_id 
            ? Organization::find($request->organization_id) 
            : null;

        return view('superadmin.licenses.create', compact('organizations', 'plans', 'selectedOrg'));
    }

    /**
     * Store a newly created license
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'plan_type' => 'required|in:trial,basic,standard,premium,enterprise',
            'validity_days' => 'required|integer|min:1|max:3650',
            'max_users' => 'nullable|integer|min:1',
            'max_items' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $organization = Organization::findOrFail($validated['organization_id']);
        $plan = SubscriptionPlan::where('code', $validated['plan_type'])->first();

        $license = $this->licenseService->createLicense($organization, [
            'plan_type' => $validated['plan_type'],
            'max_users' => $validated['max_users'] ?? $plan?->max_users ?? 5,
            'max_items' => $validated['max_items'] ?? $plan?->max_items ?? 1000,
            'starts_at' => now(),
            'expires_at' => now()->addDays((int) $validated['validity_days']),
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('superadmin.licenses.show', $license)
            ->with('success', 'License created successfully. Key: ' . $license->license_key);
    }

    /**
     * Display the specified license
     */
    public function show(License $license)
    {
        $license->load(['organization', 'plan', 'logs.performer']);
        $usageLimits = $this->licenseService->checkUsageLimits($license);

        return view('superadmin.licenses.show', compact('license', 'usageLimits'));
    }

    /**
     * Extend a license
     */
    public function extend(Request $request, License $license)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:3650',
        ]);

        $days = (int) $validated['days'];
        $this->licenseService->extendLicense($license, $days);

        return back()->with('success', "License extended by {$days} days.");
    }

    /**
     * Suspend a license
     */
    public function suspend(Request $request, License $license)
    {
        $reason = $request->input('reason', 'Suspended by super admin');
        $this->licenseService->suspendLicense($license, $reason);

        return back()->with('success', 'License has been suspended.');
    }

    /**
     * Reactivate a suspended license
     */
    public function reactivate(License $license)
    {
        if ($license->isExpired()) {
            return back()->with('error', 'Cannot reactivate an expired license. Please extend it first.');
        }

        $this->licenseService->reactivateLicense($license);

        return back()->with('success', 'License has been reactivated.');
    }

    /**
     * Renew a license with new terms
     */
    public function renew(Request $request, License $license)
    {
        $validated = $request->validate([
            'plan_type' => 'required|in:trial,basic,standard,premium,enterprise',
            'validity_days' => 'required|integer|min:1|max:3650',
        ]);

        $this->licenseService->renewLicense($license, [
            'plan_type' => $validated['plan_type'],
            'days' => (int) $validated['validity_days'],
        ]);

        return back()->with('success', 'License has been renewed successfully.');
    }

    /**
     * Generate a new license key (revoke old, create new)
     */
    public function regenerate(License $license)
    {
        $organization = $license->organization;
        
        // Revoke old license
        $license->update(['is_active' => false]);
        $license->logs()->create([
            'action' => 'revoked',
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'notes' => 'Regenerated with new key',
        ]);

        // Create new license with same terms
        $newLicense = $this->licenseService->createLicense($organization, [
            'plan_type' => $license->plan_type,
            'max_users' => $license->max_users,
            'max_items' => $license->max_items,
            'max_transactions_per_month' => $license->max_transactions_per_month,
            'features' => $license->features,
            'starts_at' => now(),
            'expires_at' => $license->expires_at > now() ? $license->expires_at : now()->addDays(30),
            'notes' => 'Regenerated from license: ' . $license->license_key,
        ]);

        return redirect()->route('superadmin.licenses.show', $newLicense)
            ->with('success', 'New license generated. Key: ' . $newLicense->license_key);
    }

    /**
     * Validate a license key (AJAX)
     */
    public function validate(Request $request)
    {
        $request->validate(['license_key' => 'required|string']);
        
        $result = $this->licenseService->validateLicense($request->license_key);

        return response()->json($result);
    }
}
