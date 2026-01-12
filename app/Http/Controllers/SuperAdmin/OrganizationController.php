<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Organization;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Display a listing of organizations
     */
    public function index(Request $request)
    {
        $query = Organization::with(['activeLicense', 'owner']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // License status filter
        if ($request->filled('license_status')) {
            if ($request->license_status === 'active') {
                $query->whereHas('activeLicense');
            } elseif ($request->license_status === 'expired') {
                $query->whereDoesntHave('activeLicense');
            }
        }

        $organizations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('superadmin.organizations.index', compact('organizations'));
    }

    /**
     * Show create organization form
     */
    public function create()
    {
        return view('superadmin.organizations.create');
    }

    /**
     * Store a newly created organization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'gst_no' => 'nullable|string|max:50',
            'pan_no' => 'nullable|string|max:20',
            'dl_no' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,suspended',
            
            // Admin user details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
            
            // License details
            'plan_type' => 'required|in:trial,basic,standard,premium,enterprise',
            'license_days' => 'required|integer|min:1|max:3650',
        ]);

        DB::beginTransaction();

        try {
            // Create organization
            $organization = Organization::create([
                'name' => $validated['name'],
                'code' => Organization::generateCode($validated['name']),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'pin_code' => $validated['pin_code'],
                'gst_no' => $validated['gst_no'],
                'pan_no' => $validated['pan_no'],
                'dl_no' => $validated['dl_no'],
                'status' => $validated['status'],
            ]);

            // Create admin user
            $adminUser = User::create([
                'full_name' => $validated['admin_name'],
                'username' => strtolower(str_replace(' ', '', $validated['admin_name'])) . rand(100, 999),
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'admin',
                'organization_id' => $organization->id,
                'is_organization_owner' => true,
                'is_active' => true,
            ]);

            // Create license
            $license = $this->licenseService->createLicense($organization, [
                'plan_type' => $validated['plan_type'],
                'starts_at' => now(),
                'expires_at' => now()->addDays((int) $validated['license_days']),
            ]);

            DB::commit();

            return redirect()->route('superadmin.organizations.show', $organization)
                ->with('success', 'Organization created successfully. License key: ' . $license->license_key);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified organization
     */
    public function show(Organization $organization)
    {
        $organization->load(['licenses.logs', 'users', 'owner']);
        
        $stats = [
            'users_count' => $organization->users()->count(),
            'customers_count' => $organization->customers()->count(),
            'items_count' => $organization->items()->count(),
            'suppliers_count' => $organization->suppliers()->count(),
        ];

        return view('superadmin.organizations.show', compact('organization', 'stats'));
    }

    /**
     * Show the form for editing the specified organization
     */
    public function edit(Organization $organization)
    {
        return view('superadmin.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'gst_no' => 'nullable|string|max:50',
            'pan_no' => 'nullable|string|max:20',
            'dl_no' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $organization->update($validated);

        return redirect()->route('superadmin.organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization
     */
    public function destroy(Organization $organization)
    {
        // Prevent deletion of default organization
        if ($organization->code === 'DEFAULT') {
            return back()->with('error', 'Cannot delete the default organization.');
        }

        $organization->delete();

        return redirect()->route('superadmin.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    /**
     * Suspend an organization
     */
    public function suspend(Organization $organization)
    {
        $organization->update(['status' => 'suspended']);
        
        // Also suspend active licenses
        $organization->licenses()->where('is_active', true)->update(['is_active' => false]);

        return back()->with('success', 'Organization has been suspended.');
    }

    /**
     * Activate a suspended organization
     */
    public function activate(Organization $organization)
    {
        $organization->update(['status' => 'active']);
        
        // Reactivate the last license if not expired
        $lastLicense = $organization->licenses()->latest()->first();
        if ($lastLicense && $lastLicense->expires_at > now()) {
            $lastLicense->update(['is_active' => true]);
        }

        return back()->with('success', 'Organization has been activated.');
    }
}
