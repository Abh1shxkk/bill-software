<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Check if user is active (if the column exists)
        if (isset($user->is_active) && !$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Your account has been deactivated. Please contact administrator.');
        }

        // Allow both admin and user roles to access admin routes
        // Permission middleware will handle specific module access
        if (!in_array($user->role, ['admin', 'user'])) {
            return redirect('/login');
        }

        return $next($request);
    }
}


