<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrganizationSettingsController extends Controller
{
    /**
     * Show organization settings
     */
    public function index()
    {
        $user = auth()->user();
        $organization = $user->organization;
        $license = $user->getActiveLicense();
        
        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        $stats = [
            'users_count' => $organization->users()->count(),
            'customers_count' => $organization->customers()->count(),
            'items_count' => $organization->items()->count(),
            'suppliers_count' => $organization->suppliers()->count(),
        ];

        $usageLimits = null;
        if ($license) {
            $usageLimits = app(\App\Services\LicenseService::class)->checkUsageLimits($license);
        }

        return view('admin.organization.settings', compact('organization', 'license', 'stats', 'usageLimits'));
    }

    /**
     * Show organization profile edit form
     */
    public function editProfile()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        return view('admin.organization.edit-profile', compact('organization'));
    }

    /**
     * Update organization profile
     */
    public function updateProfile(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        // Only organization owner or admin can update
        if (!auth()->user()->is_organization_owner && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Only organization owner can update profile.');
        }

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
            'dl_no_1' => 'nullable|string|max:100',
            'food_license' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($organization->logo_path) {
                Storage::disk('public')->delete($organization->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('organizations/logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        unset($validated['logo']);
        $organization->update($validated);

        return redirect()->route('admin.organization.settings')
            ->with('success', 'Organization profile updated successfully.');
    }

    /**
     * Show users list
     */
    public function users()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        $users = $organization->users()->orderBy('created_at', 'desc')->paginate(15);
        $license = auth()->user()->getActiveLicense();

        return view('admin.organization.users', compact('users', 'organization', 'license'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        $organization = auth()->user()->organization;
        $license = auth()->user()->getActiveLicense();

        // Check user limit
        if ($license) {
            $currentUsers = $organization->users()->count();
            if ($currentUsers >= $license->max_users) {
                return redirect()->route('admin.organization.users')
                    ->with('error', 'User limit reached. Please upgrade your license.');
            }
        }

        return view('admin.organization.create-user', compact('organization'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $organization = auth()->user()->organization;
        $license = auth()->user()->getActiveLicense();

        // Check user limit
        if ($license) {
            $currentUsers = $organization->users()->count();
            if ($currentUsers >= $license->max_users) {
                return back()->with('error', 'User limit reached. Please upgrade your license.');
            }
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['manager', 'staff', 'readonly'])], // No admin role - only owner is admin
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'organization_id' => $organization->id,
            'is_organization_owner' => false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.organization.users')
            ->with('success', 'User created successfully.');
    }

    /**
     * Update user status
     */
    public function toggleUserStatus(User $user)
    {
        $organization = auth()->user()->organization;

        // Ensure user belongs to same organization
        if ($user->organization_id !== $organization->id) {
            return back()->with('error', 'User not found.');
        }

        // Cannot deactivate yourself or organization owner
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }

        if ($user->is_organization_owner) {
            return back()->with('error', 'Cannot deactivate organization owner.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User status updated successfully.');
    }

    /**
     * Remove user from organization
     */
    public function removeUser(User $user)
    {
        $organization = auth()->user()->organization;

        // Ensure user belongs to same organization
        if ($user->organization_id !== $organization->id) {
            return back()->with('error', 'User not found.');
        }

        // Cannot remove yourself or organization owner
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        if ($user->is_organization_owner) {
            return back()->with('error', 'Cannot remove organization owner.');
        }

        $user->delete();

        return back()->with('success', 'User removed from organization.');
    }

    /**
     * Show license information
     */
    public function license()
    {
        $user = auth()->user();
        $organization = $user->organization;
        $license = $user->getActiveLicense();

        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        $usageLimits = null;
        if ($license) {
            $usageLimits = app(\App\Services\LicenseService::class)->checkUsageLimits($license);
        }

        $licenseHistory = $organization->licenses()->orderBy('created_at', 'desc')->get();

        return view('admin.organization.license', compact('organization', 'license', 'usageLimits', 'licenseHistory'));
    }

    /**
     * Request license renewal
     */
    public function requestRenewal(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $organization = $user->organization;
        $license = $user->getActiveLicense();

        // In a real app, you'd send an email or create a support ticket
        // For now, we'll just log it and show a success message

        // Log the renewal request
        if ($license) {
            $license->logs()->create([
                'action' => 'renewal_requested',
                'performed_by' => $user->user_id,
                'ip_address' => $request->ip(),
                'metadata' => json_encode([
                    'message' => $request->message,
                    'organization' => $organization->name,
                    'current_expiry' => $license->expires_at?->format('Y-m-d'),
                ]),
                'notes' => 'Renewal request submitted by ' . $user->full_name,
            ]);
        }

        return back()->with('success', 'License renewal request submitted successfully. Our team will contact you shortly.');
    }
}
