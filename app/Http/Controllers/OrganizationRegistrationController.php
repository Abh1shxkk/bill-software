<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\License;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\WelcomeOrganization;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class OrganizationRegistrationController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price_monthly')
            ->get();
        
        return view('auth.register-organization', compact('plans'));
    }

    /**
     * Handle organization registration
     */
    public function register(Request $request)
    {
        $request->validate([
            // Organization details
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'required|email|max:255',
            'organization_phone' => 'nullable|string|max:50',
            'organization_address' => 'nullable|string|max:500',
            'organization_city' => 'nullable|string|max:100',
            'organization_state' => 'nullable|string|max:100',
            'organization_gst_no' => 'nullable|string|max:50',
            
            // Admin user details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_username' => 'required|string|max:50|unique:users,username',
            'admin_password' => ['required', 'confirmed', Password::min(8)],
            
            // Plan selection
            'plan_type' => 'required|string|in:trial,basic,standard,premium',
            
            // Terms acceptance
            'agree_terms' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            // Create organization
            $organization = Organization::create([
                'name' => $request->organization_name,
                'code' => Organization::generateCode($request->organization_name),
                'email' => $request->organization_email,
                'phone' => $request->organization_phone,
                'address' => $request->organization_address,
                'city' => $request->organization_city,
                'state' => $request->organization_state,
                'gst_no' => $request->organization_gst_no,
                'status' => 'active',
            ]);

            // Create admin user
            $user = User::create([
                'full_name' => $request->admin_name,
                'username' => $request->admin_username,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
                'organization_id' => $organization->id,
                'is_organization_owner' => true,
                'is_active' => true,
            ]);

            // Get plan details
            $plan = SubscriptionPlan::where('code', $request->plan_type)->first();
            
            // Determine trial period
            $validityDays = $request->plan_type === 'trial' ? 14 : ($plan?->validity_days ?? 30);

            // Create license
            $license = $this->licenseService->createLicense($organization, [
                'plan_type' => $request->plan_type,
                'max_users' => $plan?->max_users ?? 3,
                'max_items' => $plan?->max_items ?? 500,
                'validity_days' => $validityDays,
            ]);

            // Activate the license immediately
            $this->licenseService->activateLicense(
                $license->license_key,
                $request->ip(),
                $request->getHost()
            );

            DB::commit();

            // Send welcome email
            try {
                $user->notify(new WelcomeOrganization($organization, $license));
            } catch (\Exception $e) {
                // Log but don't fail registration
                \Log::warning('Failed to send welcome email: ' . $e->getMessage());
            }

            // Log the user in
            auth()->login($user);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome to MediBill! Your organization has been created successfully. Your ' . 
                    ($request->plan_type === 'trial' ? '14-day trial' : $request->plan_type) . 
                    ' license is now active.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Organization registration failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again or contact support.');
        }
    }
}
