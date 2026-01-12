<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures organization context is set for the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin doesn't need organization context for super admin routes
        if ($user->isSuperAdmin() && $request->is('superadmin/*')) {
            return $next($request);
        }

        // For regular admin routes, verify organization context
        if (!$user->isSuperAdmin() && !$user->organization_id) {
            return redirect()->route('organization.setup')
                ->with('error', 'Please complete organization setup first.');
        }

        // Share organization info with all views
        if ($user->organization_id) {
            $organization = $user->organization;
            view()->share('currentOrganization', $organization);
            
            // Set organization context in container for global access
            app()->instance('current_organization', $organization);
        }

        return $next($request);
    }
}
