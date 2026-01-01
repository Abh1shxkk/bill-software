<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Area;
use App\Models\State;

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

        return view('admin.reports.purchase-report.index', compact(
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

    /**
     * Purchase Book Report
     */
    public function purchaseBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        $users = User::select('user_id', 'full_name')->get();
        $areas = Area::active()->get();
        $states = State::all();
        $seriesList = PurchaseTransaction::distinct()->pluck('voucher_type')->filter()->values();
        
        return view('admin.reports.purchase-report.purchase-book.purchase-book', compact('dateFrom', 'dateTo', 'suppliers', 'users', 'areas', 'states', 'seriesList'));
    }

    /**
     * Purchase Book GSTR Report
     */
    public function purchaseBookGstr(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-book.purchase-book-gstr', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Purchase Book With TCS Report
     */
    public function purchaseBookTcs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-book.purchase-book-tcs', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * TDS OUTPUT Report
     */
    public function tdsOutput(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-book.tds-output', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Purchase Book with Sale Value
     */
    public function purchaseBookSaleValue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-book-sale-value', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Party Wise Purchase Report
     */
    public function partyWisePurchase(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.party-wise-purchase', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Monthly Purchase Summary
     */
    public function monthlyPurchaseSummary(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.monthly-purchase-summary', compact('year', 'suppliers'));
    }

    /**
     * Debit/Credit Note Report
     */
    public function debitCreditNote(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.debit-credit-note', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Day Purchase Summary Item Wise
     */
    public function dayPurchaseSummary(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.day-purchase-summary', compact('date', 'suppliers'));
    }

    /**
     * Purchase/Return Book Item Wise
     */
    public function purchaseReturnItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        return view('admin.reports.purchase-report.purchase-return-item-wise', compact('dateFrom', 'dateTo'));
    }

    /**
     * Local/Central Purchase Register
     */
    public function localCentralRegister(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        return view('admin.reports.purchase-report.local-central-register', compact('dateFrom', 'dateTo'));
    }

    /**
     * Purchase Voucher Detail
     */
    public function purchaseVoucherDetail(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        return view('admin.reports.purchase-report.purchase-voucher-detail', compact('dateFrom', 'dateTo'));
    }

    /**
     * Short Expiry Received Report
     */
    public function shortExpiryReceived(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.short-expiry-received', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Purchase Return List
     */
    public function purchaseReturnList(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-return-list', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * GST SET OFF Report
     */
    public function gstSetOff(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        return view('admin.reports.purchase-report.gst-set-off.gst-set-off', compact('dateFrom', 'dateTo'));
    }

    /**
     * GST SET OFF GSTR Report
     */
    public function gstSetOffGstr(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        return view('admin.reports.purchase-report.gst-set-off.gst-set-off-gstr', compact('dateFrom', 'dateTo'));
    }

    /**
     * Purchase Challan Book
     */
    public function purchaseChallanBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-challan-reports.purchase-challan-book', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Pending Challans
     */
    public function pendingChallans(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.purchase-challan-reports.pending-challans', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Supplier Wise Purchase
     */
    /**
     * Purchase with Item Details
     */
    public function purchaseWithItemDetails(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.purchase-with-item-details', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    // --- Supplier Wise Purchase Submodule Methods ---

    /**
     * All Supplier Purchase Summary
     */
    public function supplierAllSupplier(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.all-supplier');
    }

    /**
     * Supplier Bill Wise Purchase
     */
    public function supplierBillWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.bill-wise');
    }

    /**
     * Supplier Item Wise Purchase
     */
    public function supplierItemWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-wise');
    }

    /**
     * Supplier Item - Invoice Wise Purchase
     */
    public function supplierItemInvoiceWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-invoice-wise');
    }

    /**
     * Supplier Invoice - Item Wise Purchase
     */
    public function supplierInvoiceItemWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.invoice-item-wise');
    }

    // --- Company Wise Purchase Submodule Methods ---

    /**
     * All Company Purchase Summary
     */
    public function companyAllCompany(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.all-company');
    }

    /**
     * Company Item Wise Purchase
     */
    public function companyItemWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.item-wise');
    }

    /**
     * Company Party Wise Purchase
     */
    public function companyPartyWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.party-wise');
    }

    // --- Item Wise Purchase Submodule Methods ---

    /**
     * Item Bill Wise Purchase
     */
    public function itemBillWise(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.bill-wise');
    }

    /**
     * All Item Purchase Summary
     */
    public function itemAllItemPurchase(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.all-item-purchase');
    }

    // --- Schemed Received Submodule Methods ---

    /**
     * Free Schemed Received
     */
    public function schemedFreeSchemed(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.schemed-received.free-schemed-received');
    }

    /**
     * Half Schemed Received
     */
    public function schemedHalfSchemed(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.schemed-received.half-schemed-received');
    }

    /**
     * Free Received Without Qty
     */
    public function schemedFreeWithoutQty(Request $request)
    {
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.schemed-received.free-received-without-qty');
    }

    /**
     * Supplier Visit Report
     */
    public function supplierVisitReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.supplier-visit-report', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Supplier Wise Companies
     */
    public function supplierWiseCompanies(Request $request)
    {
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.supplier-wise-companies', compact('suppliers'));
    }

    /**
     * Purchase Book - Item Details
     */
    public function purchaseBookItemDetails(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.purchase-book-item-details', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Central Purchase with Local Value
     */
    public function centralPurchaseLocalValue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.central-purchase-local-value', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Party Wise All Purchase Details
     */
    public function partyWiseAllPurchaseDetails(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.party-wise-all-purchase-details', compact('dateFrom', 'dateTo', 'suppliers'));
    }

    /**
     * Register of Schedule H1 Drugs
     */
    public function registerScheduleH1Drugs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $suppliers = Supplier::select('supplier_id', 'name')->get();
        
        return view('admin.reports.purchase-report.other-reports.register-schedule-h1-drugs', compact('dateFrom', 'dateTo', 'suppliers'));
    }
}

