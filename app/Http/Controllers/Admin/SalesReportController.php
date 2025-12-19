<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\Customer;
use App\Models\SalesMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    /**
     * Display the sales report dashboard
     */
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');

        // Base query
        $query = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo]);
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        if ($salesmanId) {
            $query->where('salesman_id', $salesmanId);
        }

        // Summary statistics
        $totalSales = (clone $query)->sum('net_amount');
        $totalInvoices = (clone $query)->count();
        $avgSaleValue = $totalInvoices > 0 ? $totalSales / $totalInvoices : 0;
        $totalTax = (clone $query)->sum('tax_amount');

        // Daily sales for chart
        $dailySales = (clone $query)
            ->select(DB::raw('DATE(sale_date) as sale_date'), DB::raw('SUM(net_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        // Top customers
        $topCustomers = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->select('customer_id', DB::raw('SUM(net_amount) as total_sales'), DB::raw('COUNT(*) as invoice_count'))
            ->with('customer:id,name')
            ->groupBy('customer_id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Top selling items
        $topItems = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->select('item_id', 'item_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(net_amount) as total_amount'))
            ->groupBy('item_id', 'item_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Monthly comparison (last 6 months)
        $monthlyData = SaleTransaction::select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(net_amount) as total')
            )
            ->where('sale_date', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Sales by salesman
        $salesBySalesman = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->select('salesman_id', DB::raw('SUM(net_amount) as total_sales'))
            ->with('salesman:id,name')
            ->groupBy('salesman_id')
            ->orderByDesc('total_sales')
            ->get();

        // Recent sales
        $recentSales = (clone $query)
            ->with('customer:id,name')
            ->orderByDesc('sale_date')
            ->limit(20)
            ->get();

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->get();
        $salesmen = SalesMan::select('id', 'name')->get();

        return view('admin.reports.sales.index', compact(
            'totalSales', 'totalInvoices', 'avgSaleValue', 'totalTax',
            'dailySales', 'topCustomers', 'topItems', 'monthlyData',
            'salesBySalesman', 'recentSales', 'customers', 'salesmen',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId'
        ));
    }

    /**
     * Export sales report as CSV
     */
    public function exportCsv(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with(['customer:id,name', 'salesman:id,name'])
            ->orderBy('sale_date')
            ->get();

        $filename = 'sales_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date', 'Invoice No', 'Customer', 'Salesman', 'NT Amount', 
                'Discount', 'Tax', 'Net Amount', 'Status'
            ]);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_date->format('d-m-Y'),
                    $sale->invoice_no,
                    $sale->customer->name ?? 'N/A',
                    $sale->salesman->name ?? 'N/A',
                    number_format($sale->nt_amount ?? 0, 2),
                    number_format($sale->dis_amount ?? 0, 2),
                    number_format($sale->tax_amount ?? 0, 2),
                    number_format($sale->net_amount ?? 0, 2),
                    $sale->status ?? 'Completed'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sales report as PDF
     */
    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with(['customer:id,name', 'salesman:id,name'])
            ->orderBy('sale_date')
            ->get();

        $totalSales = $sales->sum('net_amount');
        $totalTax = $sales->sum('tax_amount');

        return view('admin.reports.sales.pdf', compact('sales', 'dateFrom', 'dateTo', 'totalSales', 'totalTax'));
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $type = $request->get('type', 'daily');

        switch ($type) {
            case 'daily':
                $data = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
                    ->select(DB::raw('DATE(sale_date) as label'), DB::raw('SUM(net_amount) as value'))
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;

            case 'customer':
                $data = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
                    ->select('customer_id', DB::raw('SUM(net_amount) as value'))
                    ->with('customer:id,name')
                    ->groupBy('customer_id')
                    ->orderByDesc('value')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => $item->customer->name ?? 'Unknown',
                            'value' => $item->value
                        ];
                    });
                break;

            case 'item':
                $data = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
                        $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    })
                    ->select('item_name', DB::raw('SUM(net_amount) as value'))
                    ->groupBy('item_name')
                    ->orderByDesc('value')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => $item->item_name,
                            'value' => $item->value
                        ];
                    });
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }
}
