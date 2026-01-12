<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    /**
     * Handle an incoming request.
     * Checks if user's organization has a valid license.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // No user logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypasses license check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // User must belong to an organization
        if (!$user->organization_id) {
            return redirect()->route('license.required')
                ->with('error', 'No organization assigned. Please contact administrator.');
        }

        // Check if organization has an active license
        $license = $user->getActiveLicense();

        if (!$license) {
            return redirect()->route('license.required')
                ->with('error', 'No active license found. Please contact administrator to activate your license.');
        }

        if (!$license->isValid()) {
            if ($license->isExpired()) {
                return redirect()->route('license.expired')
                    ->with('error', 'Your license has expired. Please renew to continue using the software.');
            }

            if (!$license->is_active) {
                return redirect()->route('license.suspended')
                    ->with('error', 'Your license has been suspended. Please contact administrator.');
            }
        }

        // Add license info to view data
        view()->share('currentLicense', $license);
        view()->share('licenseExpiringWarning', $license->isExpiringSoon());

        return $next($request);
    }
}
