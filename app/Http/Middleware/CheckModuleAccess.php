<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Route name to permission module mapping
     */
    protected array $routeModuleMap = [
        // Masters
        'companies' => 'companies',
        'customers' => 'customers',
        'items' => 'items',
        'suppliers' => 'suppliers',
        'hsn-codes' => 'hsn-codes',
        'batches' => 'batches',
        
        // Ledgers
        'all-ledger' => 'all-ledger',
        'general-ledger' => 'general-ledger',
        'cash-bank-books' => 'cash-bank-books',
        'sale-ledger' => 'sale-ledger',
        'purchase-ledger' => 'purchase-ledger',
        
        // Sales
        'sale' => 'sale',
        'sale-challan' => 'sale-challan',
        'sale-return' => 'sale-return',
        'quotation' => 'quotation',
        'customer-receipt' => 'customer-receipt',
        
        // Purchase
        'purchase' => 'purchase',
        'purchase-challan' => 'purchase-challan',
        'purchase-return' => 'purchase-return',
        'supplier-payment' => 'supplier-payment',
        
        // Stock
        'stock-adjustment' => 'stock-adjustment',
        'stock-transfer-outgoing' => 'stock-transfer-outgoing',
        'stock-transfer-incoming' => 'stock-transfer-incoming',
        'breakage-expiry' => 'breakage-expiry',
        'godown-breakage-expiry' => 'godown-breakage-expiry',
        
        // Notes
        'credit-note' => 'credit-note',
        'debit-note' => 'debit-note',
        'claim-to-supplier' => 'claim-to-supplier',
        'replacement-note' => 'replacement-note',
        'replacement-received' => 'replacement-note',
        
        // Samples
        'sample-issued' => 'sample-issued',
        'sample-received' => 'sample-received',
        
        // Vouchers
        'voucher' => 'voucher-entry',
        'multi-voucher' => 'multi-voucher',
        'bank-transaction' => 'bank-transaction',
        'cheque-return' => 'cheque-return',
        'deposit-slip' => 'deposit-slip',
        
        // HR
        'sales-men' => 'sales-men',
        'areas' => 'areas',
        'routes' => 'routes',
        'states' => 'states',
        'area-managers' => 'area-managers',
        'regional-managers' => 'regional-managers',
        'marketing-managers' => 'marketing-managers',
        'general-managers' => 'general-managers',
        'divisional-managers' => 'divisional-managers',
        'country-managers' => 'country-managers',
        
        // Utilities
        'personal-directory' => 'personal-directory',
        'general-reminders' => 'general-reminders',
        'general-notebook' => 'general-notebook',
        'item-category' => 'item-category',
        'transport-master' => 'transport-master',
        
        // Reports
        'reports.sales' => 'reports-sales',
        'reports.purchase' => 'reports-purchase',
        
        // User Management
        'users' => 'user-management',
    ];

    /**
     * Routes that should be accessible to all authenticated users
     */
    protected array $publicRoutes = [
        'admin.dashboard',
        'profile.settings',
        'profile.update',
        'profile.password',
        'password.change.form',
        'password.change',
        'logout',
        // API routes that may be needed across modules
        'admin.api.hotkeys',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Admin has all permissions
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Get current route name
        $routeName = $request->route()->getName();
        
        if (!$routeName) {
            return $next($request);
        }

        // Allow public routes for all authenticated users
        if (in_array($routeName, $this->publicRoutes)) {
            return $next($request);
        }

        // Extract module from route name (e.g., 'admin.companies.index' -> 'companies')
        $module = $this->getModuleFromRoute($routeName);
        
        if (!$module) {
            // If no module mapping found, allow access (might be API or utility route)
            // Only block if it looks like a module route
            return $next($request);
        }

        // Determine action from route name
        $action = $this->getActionFromRoute($routeName);

        // Check permission
        if (!$user->hasPermission($module, $action)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this module.',
                ], 403);
            }

            $moduleName = ucfirst(str_replace('-', ' ', $module));
            return redirect()->route('admin.dashboard')
                ->with('access_denied', $moduleName);
        }

        return $next($request);
    }

    /**
     * Get module name from route name
     */
    protected function getModuleFromRoute(string $routeName): ?string
    {
        // Remove 'admin.' prefix
        $routeName = preg_replace('/^admin\./', '', $routeName);
        
        // Check direct mapping first
        foreach ($this->routeModuleMap as $routeKey => $module) {
            if (str_starts_with($routeName, $routeKey)) {
                return $module;
            }
        }

        // Extract first segment as module name
        $parts = explode('.', $routeName);
        $firstPart = $parts[0] ?? null;
        
        if ($firstPart && isset($this->routeModuleMap[$firstPart])) {
            return $this->routeModuleMap[$firstPart];
        }

        return null;
    }

    /**
     * Get action from route name
     */
    protected function getActionFromRoute(string $routeName): string
    {
        // Check for specific actions in route name
        if (str_contains($routeName, '.create') || str_contains($routeName, '.store')) {
            return 'create';
        }
        
        if (str_contains($routeName, '.edit') || str_contains($routeName, '.update')) {
            return 'edit';
        }
        
        if (str_contains($routeName, '.destroy') || str_contains($routeName, 'delete') || str_contains($routeName, 'multiple-delete')) {
            return 'delete';
        }

        // Default to view
        return 'view';
    }
}
