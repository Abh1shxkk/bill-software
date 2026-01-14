<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Batch;
use App\Models\SaleTransaction;
use App\Models\PurchaseTransaction;
use App\Models\SaleReturnTransaction;
use App\Models\BreakageExpiryTransaction;
use App\Models\CustomerLedger;
use App\Models\SalesMan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get the current user's organization ID for filtering
     */
    private function getOrganizationId()
    {
        return auth()->user()->organization_id ?? null;
    }

    public function index()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;
        
        // Get organization ID for filtering raw queries
        $organizationId = $this->getOrganizationId();

        // Total counts (Eloquent models with BelongsToOrganization trait auto-filter)
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        $totalItems = Item::count();
        $totalSalesmen = SalesMan::count();

        // Sales data for growth calculation
        $totalSales = SaleTransaction::count();
        $currentMonthSales = SaleTransaction::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->count();
        $lastMonthSales = SaleTransaction::whereMonth('sale_date', $lastMonth)
            ->whereYear('sale_date', $lastMonthYear)
            ->count();
        $salesGrowth = $this->calculateGrowth($currentMonthSales, $lastMonthSales);

        // Revenue data (last 7 days)
        $revenueData = $this->getRevenueData();

        // Purchase data
        $totalPurchases = PurchaseTransaction::count();
        $currentMonthPurchases = PurchaseTransaction::whereMonth('bill_date', $currentMonth)
            ->whereYear('bill_date', $currentYear)
            ->count();

        // Returns data
        $totalSaleReturns = SaleReturnTransaction::count();
        $totalBreakageExpiry = BreakageExpiryTransaction::count();

        // Recent transactions (last 5)
        $recentSales = SaleTransaction::with(['customer', 'salesman'])
            ->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top customers by sales value
        $topCustomersQuery = DB::table('sale_transactions')
            ->join('customers', 'sale_transactions.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('COUNT(*) as total_sales'), DB::raw('SUM(sale_transactions.net_amount) as total_amount'))
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5);
        
        if ($organizationId) {
            $topCustomersQuery->where('sale_transactions.organization_id', $organizationId);
        }
        $topCustomers = $topCustomersQuery->get();

        // Top selling items
        $topItemsQuery = DB::table('sale_transaction_items')
            ->join('items', 'sale_transaction_items.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('SUM(sale_transaction_items.qty) as total_quantity'))
            ->groupBy('items.id', 'items.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5);
        
        if ($organizationId) {
            $topItemsQuery->where('sale_transaction_items.organization_id', $organizationId);
        }
        $topItems = $topItemsQuery->get();

        // Recent activities
        $recentActivities = $this->getRecentActivities();

        // Monthly sales vs purchases comparison (last 6 months)
        $monthlyComparison = $this->getMonthlyComparison();

        // Payment status distribution
        $paymentStatus = $this->getPaymentStatusDistribution();

        // Low stock items (using batch-based inventory with organization filtering)
        $lowStockItems = Batch::select(
                'items.name',
                DB::raw('SUM(batches.total_qty) as current_stock'),
                'items.min_level as minimum_stock'
            )
            ->join('items', 'batches.item_id', '=', 'items.id')
            ->where('batches.is_deleted', 0)
            ->when($organizationId, function ($query) use ($organizationId) {
                $query->where('batches.organization_id', $organizationId)
                      ->where('items.organization_id', $organizationId);
            })
            ->groupBy('items.id', 'items.name', 'items.min_level')
            ->havingRaw('SUM(batches.total_qty) <= items.min_level')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // Category-wise sales distribution (for pie chart)
        // Note: items table has 'category' column (string), not category_id
        $categorySalesQuery = DB::table('sale_transaction_items')
            ->join('items', 'sale_transaction_items.item_id', '=', 'items.id')
            ->select(
                DB::raw('COALESCE(NULLIF(items.category, ""), "Uncategorized") as category'),
                DB::raw('SUM(sale_transaction_items.net_amount) as total_amount')
            )
            ->groupBy('items.category')
            ->orderBy('total_amount', 'desc')
            ->limit(5);
        
        if ($organizationId) {
            $categorySalesQuery->where('sale_transaction_items.organization_id', $organizationId);
        }
        $categorySales = $categorySalesQuery->get();

        // Salesman performance (for bar chart)
        $salesmanPerformanceQuery = DB::table('sale_transactions')
            ->join('sales_men', 'sale_transactions.salesman_id', '=', 'sales_men.id')
            ->select(
                'sales_men.name',
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(sale_transactions.net_amount) as total_amount')
            )
            ->whereMonth('sale_transactions.sale_date', Carbon::now()->month)
            ->whereYear('sale_transactions.sale_date', Carbon::now()->year)
            ->groupBy('sales_men.id', 'sales_men.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5);
        
        if ($organizationId) {
            $salesmanPerformanceQuery->where('sale_transactions.organization_id', $organizationId);
        }
        $salesmanPerformance = $salesmanPerformanceQuery->get();

        // Today's quick stats
        $todayStats = [
            'sales' => SaleTransaction::whereDate('sale_date', Carbon::today())->count(),
            'sales_amount' => SaleTransaction::whereDate('sale_date', Carbon::today())->sum('net_amount'),
            'purchases' => PurchaseTransaction::whereDate('bill_date', Carbon::today())->count(),
            'purchases_amount' => PurchaseTransaction::whereDate('bill_date', Carbon::today())->sum('net_amount'),
        ];

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalSuppliers',
            'totalItems',
            'totalSalesmen',
            'totalSales',
            'totalPurchases',
            'totalSaleReturns',
            'totalBreakageExpiry',
            'salesGrowth',
            'revenueData',
            'recentSales',
            'topCustomers',
            'topItems',
            'recentActivities',
            'monthlyComparison',
            'paymentStatus',
            'lowStockItems',
            'categorySales',
            'salesmanPerformance',
            'todayStats'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getRevenueData()
    {
        $days = [];
        $sales = [];
        $purchases = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D');

            $dailySales = SaleTransaction::whereDate('sale_date', $date->format('Y-m-d'))
                ->sum('net_amount');
            $sales[] = $dailySales ?? 0;

            $dailyPurchases = PurchaseTransaction::whereDate('bill_date', $date->format('Y-m-d'))
                ->sum('net_amount');
            $purchases[] = $dailyPurchases ?? 0;
        }

        return [
            'labels' => $days,
            'sales' => $sales,
            'purchases' => $purchases
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];
        $organizationId = $this->getOrganizationId();

        // Recent sales
        $recentSale = SaleTransaction::with('customer')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($recentSale) {
            $activities[] = [
                'type' => 'sale',
                'title' => 'New Sale Transaction',
                'description' => 'Sale to ' . ($recentSale->customer->name ?? 'N/A') . ' - ₹' . number_format($recentSale->net_amount, 2),
                'time' => $recentSale->created_at->diffForHumans(),
                'icon' => 'fa-shopping-cart',
                'color' => 'success'
            ];
        }

        // Recent customer (use ID ordering since no timestamps)
        $recentCustomer = Customer::orderBy('id', 'desc')->first();
        if ($recentCustomer) {
            $activities[] = [
                'type' => 'customer',
                'title' => 'Customer in System',
                'description' => $recentCustomer->name,
                'time' => 'Recently added',
                'icon' => 'fa-user-plus',
                'color' => 'primary'
            ];
        }

        // Recent purchase
        $recentPurchase = PurchaseTransaction::with('supplier')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($recentPurchase) {
            $activities[] = [
                'type' => 'purchase',
                'title' => 'New Purchase Transaction',
                'description' => 'Purchase from ' . ($recentPurchase->supplier->name ?? 'N/A') . ' - ₹' . number_format($recentPurchase->net_amount, 2),
                'time' => $recentPurchase->created_at->diffForHumans(),
                'icon' => 'fa-truck',
                'color' => 'info'
            ];
        }

        // Low stock alert (using batch-based inventory with organization filtering)
        $lowStockQuery = DB::table('batches')
            ->join('items', 'batches.item_id', '=', 'items.id')
            ->select('items.id')
            ->where('batches.is_deleted', 0)
            ->groupBy('items.id', 'items.min_level')
            ->havingRaw('SUM(batches.total_qty) <= items.min_level');
        
        if ($organizationId) {
            $lowStockQuery->where('batches.organization_id', $organizationId)
                          ->where('items.organization_id', $organizationId);
        }
        $lowStockCount = $lowStockQuery->count();
        
        if ($lowStockCount > 0) {
            $activities[] = [
                'type' => 'alert',
                'title' => 'Low Stock Alert',
                'description' => $lowStockCount . ' items need restocking',
                'time' => 'Now',
                'icon' => 'fa-exclamation-triangle',
                'color' => 'warning'
            ];
        }

        // Sort by time and limit to 5
        usort($activities, function($a, $b) {
            return strcmp($b['time'], $a['time']);
        });

        return array_slice($activities, 0, 5);
    }

    private function getMonthlyComparison()
    {
        $months = [];
        $sales = [];
        $purchases = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');

            $monthlySales = SaleTransaction::whereMonth('sale_date', $date->month)
                ->whereYear('sale_date', $date->year)
                ->sum('net_amount');
            $sales[] = $monthlySales ?? 0;

            $monthlyPurchases = PurchaseTransaction::whereMonth('bill_date', $date->month)
                ->whereYear('bill_date', $date->year)
                ->sum('net_amount');
            $purchases[] = $monthlyPurchases ?? 0;
        }

        return [
            'labels' => $months,
            'sales' => $sales,
            'purchases' => $purchases
        ];
    }

    private function getPaymentStatusDistribution()
    {
        $organizationId = $this->getOrganizationId();
        
        // Get unique customers and their latest running balance
        $query = DB::table('customer_ledgers')
            ->select('customer_id', DB::raw('MAX(running_balance) as latest_balance'))
            ->groupBy('customer_id');
        
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }
        
        $customersWithBalance = $query->get();

        $totalDue = $customersWithBalance->where('latest_balance', '>', 0)->sum('latest_balance');
        $customersWithDue = $customersWithBalance->where('latest_balance', '>', 0)->count();
        $customersCleared = $customersWithBalance->where('latest_balance', '<=', 0)->count();
        
        return [
            'paid' => $customersCleared,
            'pending' => $customersWithDue,
            'total_due' => $totalDue
        ];
    }
}
