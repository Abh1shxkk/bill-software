<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Organization;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseActivationController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Show license required page
     */
    public function required()
    {
        return view('license.required');
    }

    /**
     * Show license expired page
     */
    public function expired()
    {
        $user = auth()->user();
        $license = $user->organization?->licenses()->latest()->first();
        
        return view('license.expired', compact('license'));
    }

    /**
     * Show license suspended page
     */
    public function suspended()
    {
        $user = auth()->user();
        $license = $user->organization?->licenses()->latest()->first();
        
        return view('license.suspended', compact('license'));
    }

    /**
     * Show license activation form
     */
    public function showActivationForm()
    {
        return view('license.activate');
    }

    /**
     * Activate a license
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|size:19', // XXXX-XXXX-XXXX-XXXX
        ]);

        $result = $this->licenseService->validateLicense($request->license_key);

        if (!$result['valid']) {
            return back()->with('error', $result['message'])->withInput();
        }

        $license = $result['license'];
        $user = auth()->user();

        // Check if license already belongs to another organization
        if ($license->organization_id && $license->activated_at) {
            // Check if user belongs to this organization
            if ($user->organization_id !== $license->organization_id) {
                return back()->with('error', 'This license is already assigned to another organization.')
                    ->withInput();
            }
        }

        // If user doesn't have an organization, assign them to the license's organization
        if (!$user->organization_id && $license->organization_id) {
            $user->update([
                'organization_id' => $license->organization_id,
                'is_organization_owner' => !$license->organization->users()->exists(),
            ]);
        }

        // Activate the license
        $this->licenseService->activateLicense(
            $license->license_key,
            $request->ip(),
            $request->getHost()
        );

        return redirect()->route('admin.dashboard')
            ->with('success', 'License activated successfully! Welcome to MediBill.');
    }

    /**
     * Show license status
     */
    public function status()
    {
        $user = auth()->user();
        $license = $user->getActiveLicense();
        $organization = $user->organization;
        
        $usageLimits = null;
        if ($license) {
            $usageLimits = $this->licenseService->checkUsageLimits($license);
        }

        return view('license.status', compact('license', 'organization', 'usageLimits'));
    }
}
