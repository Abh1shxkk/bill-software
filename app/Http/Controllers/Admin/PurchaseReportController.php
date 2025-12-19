<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseReportController extends Controller
{
    /**
     * Display the purchase report dashboard
     */
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        // Base query
        $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo]);
        
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        // Summary statistics
        $totalPurchases = (clone $query)->sum('net_amount');
        $totalInvoices = (clone $query)->count();
        $avgPurchaseValue = $totalInvoices > 0 ? $totalPurchases / $totalInvoices : 0;
        $totalTax = (clone $query)->sum('tax_amount');

        // Daily purchases for chart
        $dailyPurchases = (clone $query)
            ->select(DB::raw('DATE(bill_date) as purchase_date'), DB::raw('SUM(net_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->get();

        // Top suppliers
        $topSuppliers = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->select('supplier_id', DB::raw('SUM(net_amount) as total_purchases'), DB::raw('COUNT(*) as invoice_count'))
            ->with('supplier:supplier_id,name')
            ->groupBy('supplier_id')
            ->orderByDesc('total_purchases')
            ->limit(10)
            ->get();

        // Top purchased items
        $topItems = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            })
            ->select('item_id', 'item_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(net_amount) as total_amount'))
            ->groupBy('item_id', 'item_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Monthly comparison (last 6 months)
        $monthlyData = PurchaseTransaction::select(
                DB::raw('YEAR(bill_date) as year'),
                DB::raw('MONTH(bill_date) as month'),
                DB::raw('SUM(net_amount) as total')
            )
            ->where('bill_date', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Recent purchases
        $recentPurchases = (clone $query)
            ->with('supplier:supplier_id,name')
            ->orderByDesc('bill_date')
            ->limit(20)
            ->get();

        $suppliers = Supplier::select('supplier_id', 'name')->get();

        return view('admin.reports.purchase.index', compact(
            'totalPurchases', 'totalInvoices', 'avgPurchaseValue', 'totalTax',
            'dailyPurchases', 'topSuppliers', 'topItems', 'monthlyData',
            'recentPurchases', 'suppliers', 'dateFrom', 'dateTo', 'supplierId'
        ));
    }


    /**
     * Export purchase report as CSV
     */
    public function exportCsv(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $purchases = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->with(['supplier:supplier_id,name'])
            ->orderBy('bill_date')
            ->get();

        $filename = 'purchase_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date', 'Bill No', 'Supplier', 'NT Amount', 
                'Discount', 'Tax', 'Net Amount', 'Status'
            ]);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->bill_date->format('d-m-Y'),
                    $purchase->bill_no,
                    $purchase->supplier->name ?? 'N/A',
                    number_format($purchase->nt_amount ?? 0, 2),
                    number_format($purchase->dis_amount ?? 0, 2),
                    number_format($purchase->tax_amount ?? 0, 2),
                    number_format($purchase->net_amount ?? 0, 2),
                    $purchase->status ?? 'Completed'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export purchase report as PDF
     */
    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $purchases = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->with(['supplier:supplier_id,name'])
            ->orderBy('bill_date')
            ->get();

        $totalPurchases = $purchases->sum('net_amount');
        $totalTax = $purchases->sum('tax_amount');

        return view('admin.reports.purchase.pdf', compact('purchases', 'dateFrom', 'dateTo', 'totalPurchases', 'totalTax'));
    }
}
