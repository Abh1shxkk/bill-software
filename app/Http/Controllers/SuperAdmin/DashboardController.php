<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard
     */
    public function index()
    {
        // Organization statistics
        $organizationStats = [
            'total' => Organization::count(),
            'active' => Organization::where('status', 'active')->count(),
            'inactive' => Organization::where('status', 'inactive')->count(),
            'suspended' => Organization::where('status', 'suspended')->count(),
        ];

        // License statistics
        $licenseStats = [
            'total' => License::count(),
            'active' => License::where('is_active', true)->where('expires_at', '>', now())->count(),
            'expired' => License::where('expires_at', '<', now())->count(),
            'expiring_soon' => License::where('expires_at', '<=', now()->addDays(7))
                                      ->where('expires_at', '>', now())->count(),
        ];

        // User statistics
        $userStats = [
            'total' => User::where('role', '!=', 'super_admin')->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::whereIn('role', ['staff', 'user', 'manager'])->count(),
        ];

        // Recent organizations
        $recentOrganizations = Organization::with('activeLicense')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Licenses expiring soon
        $expiringLicenses = License::with('organization')
            ->where('is_active', true)
            ->where('expires_at', '<=', now()->addDays(30))
            ->where('expires_at', '>', now())
            ->orderBy('expires_at')
            ->take(10)
            ->get();

        // Revenue by plan (placeholder - actual calculation would need payment data)
        $planDistribution = License::select('plan_type', DB::raw('count(*) as count'))
            ->groupBy('plan_type')
            ->get()
            ->pluck('count', 'plan_type')
            ->toArray();

        return view('superadmin.dashboard', compact(
            'organizationStats',
            'licenseStats',
            'userStats',
            'recentOrganizations',
            'expiringLicenses',
            'planDistribution'
        ));
    }
}
