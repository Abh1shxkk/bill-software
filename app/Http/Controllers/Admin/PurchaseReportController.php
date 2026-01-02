<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\PurchaseReturnTransaction;
use App\Models\DebitNote;
use App\Models\CreditNote;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Company;
use App\Models\ItemCategory;
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
     * Report Types: 1=Purchase, 2=Purchase Return, 3=Debit Note, 4=Credit Note, 5=Consolidated Pur. Book, 6=All CN_DN
     */
    public function purchaseBook(Request $request)
    {
        // Basic filters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1'); // 1=Purchase, 2=Return, 3=DN, 4=CN, 5=Consolidated, 6=All CN_DN
        $reportFormat = $request->get('report_format', 'D'); // D=Detailed, S=Summarised, M=Monthly
        
        // Invoice filters
        $cancelled = $request->get('cancelled', 'N');
        $dayWiseTotal = $request->get('day_wise_total', 'N');
        $series = $request->get('series');
        $userId = $request->get('user_id');
        $firstLastUser = $request->get('first_last_user', 'F');
        
        // Party filters
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $gstnFilter = $request->get('gstn_filter', '3'); // 1=With GSTN, 2=Without GSTN, 3=All
        
        // Location filters
        $areaId = $request->get('area_id');
        $stateId = $request->get('state_id');
        
        // Other options
        $withBrExp = $request->get('with_br_exp', 'N');
        $withRcm = $request->get('with_rcm', 'Y');
        $withCustomerExp = $request->get('with_customer_exp', 'Y');
        $withoutStock = $request->boolean('without_stock');
        
        // Display options
        $showGstDetails = $request->boolean('show_gst_details');
        $grDetails = $request->boolean('gr_details');
        $orderBySupplier = $request->boolean('order_by_supplier');
        $partyWiseTotal = $request->boolean('party_wise_total');
        $showArea = $request->boolean('show_area');
        $withAddress = $request->boolean('with_address');

        $purchases = collect();
        $totals = [
            'count' => 0,
            'nt_amount' => 0,
            'dis_amount' => 0,
            'scm_amount' => 0,
            'tax_amount' => 0,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'igst_amount' => 0,
            'net_amount' => 0,
            'tcs_amount' => 0
        ];

        // Only run query if form was submitted (has date_from parameter)
        $formSubmitted = $request->has('date_from');

        // Build query based on report type
        if ($formSubmitted && in_array($reportType, ['1', '5'])) {
            // Purchase Transactions
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code,address,local_central_flag,gst_no,state_code',
                'creator:user_id,full_name'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo]);

            // Apply filters
            $this->applyPurchaseBookFilters($query, $request);

            // Ordering
            if ($orderBySupplier) {
                $query->orderBy('supplier_id')->orderBy('bill_date')->orderBy('bill_no');
            } else {
                $query->orderBy('bill_date')->orderBy('bill_no');
            }

            $purchases = $query->get();

            // Calculate totals
            $totals = [
                'count' => $purchases->count(),
                'nt_amount' => $purchases->sum('nt_amount'),
                'dis_amount' => $purchases->sum('dis_amount'),
                'scm_amount' => $purchases->sum('scm_amount'),
                'tax_amount' => $purchases->sum('tax_amount'),
                'net_amount' => $purchases->sum('net_amount'),
                'tcs_amount' => $purchases->sum('tcs_amount')
            ];
        }

        if ($formSubmitted && in_array($reportType, ['2', '5'])) {
            // Purchase Return Transactions
            $returnQuery = PurchaseReturnTransaction::with([
                'supplier:supplier_id,name,code,address,local_central_flag,gst_no,state_code'
            ])->whereBetween('return_date', [$dateFrom, $dateTo]);

            // Apply supplier filters to returns
            if ($supplierId) $returnQuery->where('supplier_id', $supplierId);
            if ($userId) $returnQuery->where('created_by', $userId);

            $returns = $returnQuery->orderBy('return_date')->get();

            if ($reportType == '2') {
                $purchases = $returns->map(function($item) {
                    $item->bill_date = $item->return_date;
                    $item->bill_no = $item->pr_no;
                    return $item;
                });
                $totals = [
                    'count' => $returns->count(),
                    'nt_amount' => $returns->sum('nt_amount'),
                    'dis_amount' => $returns->sum('dis_amount'),
                    'scm_amount' => $returns->sum('scm_amount'),
                    'tax_amount' => $returns->sum('tax_amount'),
                    'net_amount' => $returns->sum('net_amount'),
                    'tcs_amount' => $returns->sum('tcs_amount')
                ];
            } elseif ($reportType == '5') {
                // Consolidated - merge with purchases (subtract returns)
                $totals['count'] += $returns->count();
                $totals['nt_amount'] -= $returns->sum('nt_amount');
                $totals['dis_amount'] -= $returns->sum('dis_amount');
                $totals['tax_amount'] -= $returns->sum('tax_amount');
                $totals['net_amount'] -= $returns->sum('net_amount');
            }
        }

        if ($formSubmitted && in_array($reportType, ['3', '5', '6'])) {
            // Debit Notes (for suppliers)
            $dnQuery = DebitNote::with(['supplier:supplier_id,name,code,address,gst_no'])
                ->where('debit_party_type', 'S')
                ->whereBetween('debit_note_date', [$dateFrom, $dateTo]);

            if ($supplierId) $dnQuery->where('debit_party_id', $supplierId);

            $debitNotes = $dnQuery->orderBy('debit_note_date')->get();

            if ($reportType == '3') {
                $purchases = $debitNotes->map(function($item) {
                    $item->bill_date = $item->debit_note_date;
                    $item->bill_no = $item->debit_note_no;
                    $item->nt_amount = $item->gross_amount;
                    $item->tax_amount = $item->total_gst;
                    return $item;
                });
                $totals = [
                    'count' => $debitNotes->count(),
                    'nt_amount' => $debitNotes->sum('gross_amount'),
                    'dis_amount' => 0,
                    'scm_amount' => 0,
                    'tax_amount' => $debitNotes->sum('total_gst'),
                    'net_amount' => $debitNotes->sum('net_amount'),
                    'tcs_amount' => $debitNotes->sum('tcs_amount')
                ];
            }
        }

        if ($formSubmitted && in_array($reportType, ['4', '5', '6'])) {
            // Credit Notes (for suppliers)
            $cnQuery = CreditNote::with(['supplier:supplier_id,name,code,address,gst_no'])
                ->where('credit_party_type', 'S')
                ->whereBetween('credit_note_date', [$dateFrom, $dateTo]);

            if ($supplierId) $cnQuery->where('credit_party_id', $supplierId);

            $creditNotes = $cnQuery->orderBy('credit_note_date')->get();

            if ($reportType == '4') {
                $purchases = $creditNotes->map(function($item) {
                    $item->bill_date = $item->credit_note_date;
                    $item->bill_no = $item->credit_note_no;
                    $item->nt_amount = $item->gross_amount;
                    $item->tax_amount = $item->total_gst;
                    return $item;
                });
                $totals = [
                    'count' => $creditNotes->count(),
                    'nt_amount' => $creditNotes->sum('gross_amount'),
                    'dis_amount' => 0,
                    'scm_amount' => 0,
                    'tax_amount' => $creditNotes->sum('total_gst'),
                    'net_amount' => $creditNotes->sum('net_amount'),
                    'tcs_amount' => $creditNotes->sum('tcs_amount')
                ];
            }
        }

        if ($formSubmitted && $reportType == '6') {
            // All CN_DN - combine debit and credit notes
            $allNotes = collect();
            
            if (isset($debitNotes)) {
                $allNotes = $allNotes->merge($debitNotes->map(function($item) {
                    $item->bill_date = $item->debit_note_date;
                    $item->bill_no = $item->debit_note_no;
                    $item->note_type = 'DN';
                    $item->nt_amount = $item->gross_amount;
                    $item->tax_amount = $item->total_gst;
                    return $item;
                }));
            }
            
            if (isset($creditNotes)) {
                $allNotes = $allNotes->merge($creditNotes->map(function($item) {
                    $item->bill_date = $item->credit_note_date;
                    $item->bill_no = $item->credit_note_no;
                    $item->note_type = 'CN';
                    $item->nt_amount = $item->gross_amount;
                    $item->tax_amount = $item->total_gst;
                    return $item;
                }));
            }
            
            $purchases = $allNotes->sortBy('bill_date');
            $totals = [
                'count' => $allNotes->count(),
                'nt_amount' => $allNotes->sum('nt_amount'),
                'dis_amount' => 0,
                'scm_amount' => 0,
                'tax_amount' => $allNotes->sum('tax_amount'),
                'net_amount' => $allNotes->sum('net_amount'),
                'tcs_amount' => $allNotes->sum('tcs_amount')
            ];
        }

        // Get filter options for dropdowns
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $users = User::select('user_id', 'full_name')->orderBy('full_name')->get();
        $areas = Area::active()->get();
        $states = State::all();
        $seriesList = PurchaseTransaction::distinct()->pluck('voucher_type')->filter()->values();

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseBookToExcel($purchases, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view - open in new window
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-book.purchase-book-print', compact(
                'purchases', 'totals', 'suppliers', 'users', 'areas', 'states', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'cancelled', 'dayWiseTotal',
                'series', 'userId', 'firstLastUser', 'supplierCode', 'supplierId', 'localCentral',
                'gstnFilter', 'areaId', 'stateId', 'withBrExp', 'withRcm', 'withCustomerExp',
                'withoutStock', 'showGstDetails', 'grDetails', 'orderBySupplier', 'partyWiseTotal',
                'showArea', 'withAddress'
            ));
        }

        return view('admin.reports.purchase-report.purchase-book.purchase-book', compact(
            'purchases', 'totals', 'suppliers', 'users', 'areas', 'states', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'cancelled', 'dayWiseTotal',
            'series', 'userId', 'firstLastUser', 'supplierCode', 'supplierId', 'localCentral',
            'gstnFilter', 'areaId', 'stateId', 'withBrExp', 'withRcm', 'withCustomerExp',
            'withoutStock', 'showGstDetails', 'grDetails', 'orderBySupplier', 'partyWiseTotal',
            'showArea', 'withAddress'
        ));
    }

    /**
     * Apply filters to Purchase Book query
     */
    private function applyPurchaseBookFilters($query, Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $series = $request->get('series');
        $areaId = $request->get('area_id');
        $stateId = $request->get('state_id');
        $localCentral = $request->get('local_central', 'B');
        $gstnFilter = $request->get('gstn_filter', '3');
        $userId = $request->get('user_id');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($series) {
            $query->where('voucher_type', $series);
        }

        if ($userId) {
            $query->where('created_by', $userId);
        }

        // Location filters via supplier relationship
        if ($areaId || $stateId || $localCentral !== 'B' || $gstnFilter !== '3') {
            $query->whereHas('supplier', function($q) use ($areaId, $stateId, $localCentral, $gstnFilter) {
                if ($areaId) {
                    $q->where('area_id', $areaId);
                }
                if ($stateId) {
                    $q->where('state_code', $stateId);
                }
                if ($localCentral !== 'B') {
                    $q->where('local_central_flag', $localCentral);
                }
                if ($gstnFilter == '1') {
                    $q->whereNotNull('gst_no')->where('gst_no', '!=', '');
                } elseif ($gstnFilter == '2') {
                    $q->where(function($sq) {
                        $sq->whereNull('gst_no')->orWhere('gst_no', '');
                    });
                }
            });
        }
    }

    /**
     * Export Purchase Book to Excel
     */
    private function exportPurchaseBookToExcel($purchases, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_book_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Supplier Code', 'Supplier Name', 'Gross Amt', 'Discount', 'Tax', 'Net Amount']);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->bill_date->format('d-m-Y'),
                    ($purchase->series ?? $purchase->voucher_type ?? '') . $purchase->bill_no,
                    $purchase->supplier->code ?? '',
                    $purchase->supplier->name ?? 'N/A',
                    number_format($purchase->nt_amount ?? 0, 2),
                    number_format($purchase->dis_amount ?? 0, 2),
                    number_format($purchase->tax_amount ?? 0, 2),
                    number_format($purchase->net_amount ?? 0, 2)
                ]);
            }

            // Add totals row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', 
                number_format($totals['nt_amount'] ?? 0, 2),
                number_format($totals['dis_amount'] ?? 0, 2),
                number_format($totals['tax_amount'] ?? 0, 2),
                number_format($totals['net_amount'] ?? 0, 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase Book GSTR Report
     */
    public function purchaseBookGstr(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $gstnFilter = $request->get('gstn_filter', '3');
        $localCentral = $request->get('local_central', 'B');
        $reportType = $request->get('report_type', '1');
        $reportFormat = $request->get('report_format', 'D');
        $withCustomerExp = $request->get('with_customer_exp', 'Y');
        $withRcm = $request->get('with_rcm', 'B');
        $orderBySupplier = $request->boolean('order_by_supplier');
        $partyWiseTotal = $request->boolean('party_wise_total');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $purchases = collect();
        $totals = [
            'invoices' => 0,
            'taxable' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'igst' => 0,
            'tax' => 0,
            'net_amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted && in_array($reportType, ['1', '5', '7'])) {
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code,gst_no,local_central_flag,state_code'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo]);

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // GSTN and Local/Central filters
            if ($gstnFilter !== '3' || $localCentral !== 'B') {
                $query->whereHas('supplier', function($q) use ($gstnFilter, $localCentral) {
                    if ($gstnFilter == '1') {
                        $q->whereNotNull('gst_no')->where('gst_no', '!=', '');
                    } elseif ($gstnFilter == '2') {
                        $q->where(function($sq) {
                            $sq->whereNull('gst_no')->orWhere('gst_no', '');
                        });
                    }
                    if ($localCentral !== 'B') {
                        $q->where('local_central_flag', $localCentral);
                    }
                });
            }

            if ($orderBySupplier) {
                $query->orderBy('supplier_id')->orderBy('bill_date')->orderBy('bill_no');
            } else {
                $query->orderBy('bill_date')->orderBy('bill_no');
            }

            $purchases = $query->get();

            $totals = [
                'invoices' => $purchases->count(),
                'taxable' => $purchases->sum('nt_amount'),
                'cgst' => $purchases->sum('cgst_amount'),
                'sgst' => $purchases->sum('sgst_amount'),
                'igst' => $purchases->sum('igst_amount'),
                'tax' => $purchases->sum('tax_amount'),
                'net_amount' => $purchases->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportGstrToExcel($purchases, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-book.purchase-book-gstr-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
                'gstnFilter', 'localCentral', 'reportType', 'reportFormat', 'withCustomerExp',
                'withRcm', 'orderBySupplier', 'partyWiseTotal'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-book.purchase-book-gstr', compact(
            'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
            'gstnFilter', 'localCentral', 'reportType', 'reportFormat', 'withCustomerExp',
            'withRcm', 'orderBySupplier', 'partyWiseTotal'
        ));
    }

    /**
     * Export GSTR to Excel
     */
    private function exportGstrToExcel($purchases, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_book_gstr_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['GSTN', 'Supplier Name', 'Invoice No', 'Date', 'Taxable', 'CGST', 'SGST', 'IGST', 'Total']);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->supplier->gst_no ?? '',
                    $purchase->supplier->name ?? 'N/A',
                    $purchase->bill_no,
                    $purchase->bill_date->format('d-m-Y'),
                    number_format($purchase->nt_amount ?? 0, 2),
                    number_format($purchase->cgst_amount ?? 0, 2),
                    number_format($purchase->sgst_amount ?? 0, 2),
                    number_format($purchase->igst_amount ?? 0, 2),
                    number_format($purchase->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', 'TOTAL', '', '',
                number_format($totals['taxable'] ?? 0, 2),
                number_format($totals['cgst'] ?? 0, 2),
                number_format($totals['sgst'] ?? 0, 2),
                number_format($totals['igst'] ?? 0, 2),
                number_format($totals['net_amount'] ?? 0, 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase Book With TCS Report
     */
    public function purchaseBookTcs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $tcsFilter = $request->get('tcs_filter', '3'); // 1=With TCS, 2=Without TCS, 3=All
        $reportFormat = $request->get('report_format', '1'); // 1=Detailed, 2=Summary
        $transactionType = $request->get('transaction_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $fromType = $request->get('from_type', '1'); // 1=From Transaction, 2=From
        $orderBy = $request->get('order_by', 'bill_wise');
        $panNo = $request->get('pan_no');
        $gstNo = $request->get('gst_no');
        $orderBySupplier = $request->boolean('order_by_supplier');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code', 'pan', 'gst_no')->orderBy('name')->get();
        
        $purchases = collect();
        $totals = [
            'bills' => 0,
            'taxable' => 0,
            'tax_amount' => 0,
            'tcs' => 0,
            'net' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code,pan,gst_no'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo]);

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // TCS filter
            if ($tcsFilter == '1') {
                $query->where('tcs_amount', '>', 0);
            } elseif ($tcsFilter == '2') {
                $query->where(function($q) {
                    $q->whereNull('tcs_amount')->orWhere('tcs_amount', 0);
                });
            }

            // PAN filter
            if ($panNo) {
                $query->whereHas('supplier', function($q) use ($panNo) {
                    $q->where('pan', 'like', '%' . $panNo . '%');
                });
            }

            // GST filter
            if ($gstNo) {
                $query->whereHas('supplier', function($q) use ($gstNo) {
                    $q->where('gst_no', 'like', '%' . $gstNo . '%');
                });
            }

            if ($orderBySupplier || $orderBy === 'supplier_wise') {
                $query->orderBy('supplier_id')->orderBy('bill_date')->orderBy('bill_no');
            } else {
                $query->orderBy('bill_date')->orderBy('bill_no');
            }

            $purchases = $query->get();

            $totals = [
                'bills' => $purchases->count(),
                'taxable' => $purchases->sum('nt_amount'),
                'tax_amount' => $purchases->sum('tax_amount'),
                'tcs' => $purchases->sum('tcs_amount'),
                'net' => $purchases->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportTcsToExcel($purchases, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-book.purchase-book-tcs-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
                'tcsFilter', 'reportFormat', 'transactionType', 'fromType', 'orderBy',
                'panNo', 'gstNo', 'orderBySupplier'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-book.purchase-book-tcs', compact(
            'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
            'tcsFilter', 'reportFormat', 'transactionType', 'fromType', 'orderBy',
            'panNo', 'gstNo', 'orderBySupplier'
        ));
    }

    /**
     * Export TCS to Excel
     */
    private function exportTcsToExcel($purchases, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_book_tcs_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Trn No', 'Party Code', 'Party Name', 'PAN No', 'Taxable', 'Tax Amt', 'TCS %', 'TCS Amt', 'Net Amount']);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->bill_date->format('d-m-Y'),
                    $purchase->bill_no,
                    $purchase->supplier->code ?? '',
                    $purchase->supplier->name ?? 'N/A',
                    $purchase->supplier->pan ?? '',
                    number_format($purchase->nt_amount ?? 0, 2),
                    number_format($purchase->tax_amount ?? 0, 2),
                    $purchase->tcs_rate ?? 0,
                    number_format($purchase->tcs_amount ?? 0, 2),
                    number_format($purchase->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '',
                number_format($totals['taxable'] ?? 0, 2),
                number_format($totals['tax_amount'] ?? 0, 2),
                '',
                number_format($totals['tcs'] ?? 0, 2),
                number_format($totals['net'] ?? 0, 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * TDS OUTPUT Report
     */
    public function tdsOutput(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $orderBySupplier = $request->boolean('order_by_supplier');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code', 'pan')->orderBy('name')->get();
        
        $tdsRecords = collect();
        $totals = [
            'transactions' => 0,
            'gross' => 0,
            'taxable' => 0,
            'tds' => 0,
            'net' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code,pan'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo])
              ->whereHas('supplier', function($q) {
                  $q->where('tds_yn', 'Y');
              });

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            if ($orderBySupplier) {
                $query->orderBy('supplier_id')->orderBy('bill_date')->orderBy('bill_no');
            } else {
                $query->orderBy('bill_date')->orderBy('bill_no');
            }

            $tdsRecords = $query->get();

            $totals = [
                'transactions' => $tdsRecords->count(),
                'gross' => $tdsRecords->sum('nt_amount'),
                'taxable' => $tdsRecords->sum('nt_amount'),
                'tds' => $tdsRecords->sum('tds_amount'),
                'net' => $tdsRecords->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportTdsToExcel($tdsRecords, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-book.tds-output-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'tdsRecords', 'totals', 'supplierId',
                'supplierCode', 'orderBySupplier'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-book.tds-output', compact(
            'dateFrom', 'dateTo', 'suppliers', 'tdsRecords', 'totals', 'supplierId',
            'supplierCode', 'orderBySupplier'
        ));
    }

    /**
     * Export TDS to Excel
     */
    private function exportTdsToExcel($tdsRecords, $totals, $dateFrom, $dateTo)
    {
        $filename = 'tds_output_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($tdsRecords, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Code', 'Party Name', 'PAN', 'Amount', 'Taxable', 'TDS %', 'TDS Amt']);

            foreach ($tdsRecords as $record) {
                fputcsv($file, [
                    $record->bill_date->format('d-m-Y'),
                    $record->bill_no,
                    $record->supplier->code ?? '',
                    $record->supplier->name ?? 'N/A',
                    $record->supplier->pan ?? '',
                    number_format($record->nt_amount ?? 0, 2),
                    number_format($record->nt_amount ?? 0, 2),
                    $record->tds_rate ?? 0,
                    number_format($record->tds_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '',
                number_format($totals['gross'] ?? 0, 2),
                number_format($totals['taxable'] ?? 0, 2),
                '',
                number_format($totals['tds'] ?? 0, 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase Book with Sale Value
     */
    public function purchaseBookSaleValue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $taggedParties = $request->get('tagged_parties', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $purchases = collect();
        $totals = [
            'count' => 0,
            'purchase_amount' => 0,
            'sale_value' => 0,
            'margin' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code',
                'items'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo]);

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // Tagged parties filter
            if ($taggedParties === 'Y') {
                $query->whereHas('supplier', function($q) {
                    $q->where('tagged', 'Y');
                });
            }

            $query->orderBy('bill_date')->orderBy('bill_no');

            $purchaseData = $query->get();

            // Calculate sale value for each purchase
            $purchases = $purchaseData->map(function($purchase) {
                // Calculate sale value from items
                $saleValue = 0;
                if ($purchase->items) {
                    foreach ($purchase->items as $item) {
                        $saleValue += ($item->sale_rate ?? $item->mrp ?? 0) * ($item->qty ?? 0);
                    }
                }
                
                $purchase->sale_value = $saleValue;
                $purchase->margin = $saleValue - ($purchase->net_amount ?? 0);
                $purchase->margin_percent = ($purchase->net_amount ?? 0) > 0 
                    ? (($purchase->margin / $purchase->net_amount) * 100) 
                    : 0;
                
                return $purchase;
            });

            $totals = [
                'count' => $purchases->count(),
                'purchase_amount' => $purchases->sum('net_amount'),
                'sale_value' => $purchases->sum('sale_value'),
                'margin' => $purchases->sum('margin')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportSaleValueToExcel($purchases, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-book-sale-value-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
                'supplierCode', 'taggedParties', 'removeTags'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-book-sale-value', compact(
            'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'supplierId',
            'supplierCode', 'taggedParties', 'removeTags'
        ));
    }

    /**
     * Export Sale Value Report to Excel
     */
    private function exportSaleValueToExcel($purchases, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_book_sale_value_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Supplier Code', 'Supplier Name', 'Purchase Amt', 'Sale Value', 'Margin', 'Margin %']);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->bill_date->format('d-m-Y'),
                    $purchase->bill_no,
                    $purchase->supplier->code ?? '',
                    $purchase->supplier->name ?? 'N/A',
                    number_format($purchase->net_amount ?? 0, 2),
                    number_format($purchase->sale_value ?? 0, 2),
                    number_format($purchase->margin ?? 0, 2),
                    number_format($purchase->margin_percent ?? 0, 2) . '%'
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL',
                number_format($totals['purchase_amount'] ?? 0, 2),
                number_format($totals['sale_value'] ?? 0, 2),
                number_format($totals['margin'] ?? 0, 2),
                ''
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Party Wise Purchase Report
     */
    public function partyWisePurchase(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $taggedParties = $request->get('tagged_parties', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $selectiveParties = $request->get('selective_parties', 'Y');
        $printAddress = $request->get('print_address', 'N');
        $printStaxNo = $request->get('print_stax_no', 'N');
        $sortBy = $request->get('sort_by', 'P'); // P=Party, A=Amount
        $sortOrder = $request->get('sort_order', 'A'); // A=Asc, D=Desc
        $amountGreater = $request->get('amount_greater');
        $amountLessEqual = $request->get('amount_less_equal');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $withTax = $request->boolean('with_tax');
        $billAmount = $request->boolean('bill_amount');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code', 'address', 'gst_no', 'mobile')->orderBy('name')->get();
        
        $partyWise = collect();
        $totals = [
            'count' => 0,
            'bills' => 0,
            'gross_amount' => 0,
            'discount' => 0,
            'tax_amount' => 0,
            'net_amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransaction::select(
                    'supplier_id',
                    DB::raw('COUNT(*) as bill_count'),
                    DB::raw('SUM(nt_amount) as gross_amount'),
                    DB::raw('SUM(dis_amount) as discount'),
                    DB::raw('SUM(tax_amount) as tax_amount'),
                    DB::raw('SUM(net_amount) as net_amount')
                )
                ->whereBetween('bill_date', [$dateFrom, $dateTo])
                ->groupBy('supplier_id');

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // Tagged parties filter
            if ($taggedParties === 'Y') {
                $query->whereHas('supplier', function($q) {
                    $q->where('tagged', 'Y');
                });
            }

            // Amount filters
            if ($amountGreater) {
                $query->having('net_amount', '>', $amountGreater);
            }
            if ($amountLessEqual) {
                $query->having('net_amount', '<=', $amountLessEqual);
            }

            // Sorting
            if ($sortBy === 'A') {
                $query->orderBy('net_amount', $sortOrder === 'D' ? 'desc' : 'asc');
            } else {
                $query->orderBy('supplier_id', $sortOrder === 'D' ? 'desc' : 'asc');
            }

            $partyData = $query->get();

            // Get supplier details
            $supplierIds = $partyData->pluck('supplier_id')->toArray();
            $supplierDetails = Supplier::whereIn('supplier_id', $supplierIds)
                ->select('supplier_id', 'name', 'code', 'address', 'gst_no', 'mobile')
                ->get()
                ->keyBy('supplier_id');

            $partyWise = $partyData->map(function($item) use ($supplierDetails) {
                $supplier = $supplierDetails->get($item->supplier_id);
                $item->name = $supplier->name ?? 'N/A';
                $item->code = $supplier->code ?? '';
                $item->address = $supplier->address ?? '';
                $item->gst_no = $supplier->gst_no ?? '';
                $item->mobile = $supplier->mobile ?? '';
                return $item;
            });

            // Sort by party name if needed
            if ($sortBy === 'P') {
                $partyWise = $sortOrder === 'D' 
                    ? $partyWise->sortByDesc('name') 
                    : $partyWise->sortBy('name');
                $partyWise = $partyWise->values();
            }

            $totals = [
                'count' => $partyWise->count(),
                'bills' => $partyWise->sum('bill_count'),
                'gross_amount' => $partyWise->sum('gross_amount'),
                'discount' => $partyWise->sum('discount'),
                'tax_amount' => $partyWise->sum('tax_amount'),
                'net_amount' => $partyWise->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPartyWiseToExcel($partyWise, $totals, $dateFrom, $dateTo, $printAddress, $printStaxNo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.party-wise-purchase-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'partyWise', 'totals', 'supplierId',
                'supplierCode', 'taggedParties', 'removeTags', 'selectiveParties',
                'printAddress', 'printStaxNo', 'sortBy', 'sortOrder', 'amountGreater',
                'amountLessEqual', 'withBrExpiry', 'withTax', 'billAmount'
            ));
        }
        
        return view('admin.reports.purchase-report.party-wise-purchase', compact(
            'dateFrom', 'dateTo', 'suppliers', 'partyWise', 'totals', 'supplierId',
            'supplierCode', 'taggedParties', 'removeTags', 'selectiveParties',
            'printAddress', 'printStaxNo', 'sortBy', 'sortOrder', 'amountGreater',
            'amountLessEqual', 'withBrExpiry', 'withTax', 'billAmount'
        ));
    }

    /**
     * Export Party Wise Purchase to Excel
     */
    private function exportPartyWiseToExcel($partyWise, $totals, $dateFrom, $dateTo, $printAddress, $printStaxNo)
    {
        $filename = 'party_wise_purchase_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($partyWise, $totals, $printAddress, $printStaxNo) {
            $file = fopen('php://output', 'w');
            
            $header = ['Code', 'Supplier Name'];
            if ($printAddress === 'Y') $header[] = 'Address';
            if ($printStaxNo === 'Y') $header[] = 'GST No';
            $header = array_merge($header, ['Mobile', 'Bills', 'Gross Amt', 'Discount', 'Tax', 'Net Amount']);
            fputcsv($file, $header);

            foreach ($partyWise as $party) {
                $row = [$party->code ?? '', $party->name ?? 'N/A'];
                if ($printAddress === 'Y') $row[] = $party->address ?? '';
                if ($printStaxNo === 'Y') $row[] = $party->gst_no ?? '';
                $row = array_merge($row, [
                    $party->mobile ?? '',
                    $party->bill_count ?? 0,
                    number_format($party->gross_amount ?? 0, 2),
                    number_format($party->discount ?? 0, 2),
                    number_format($party->tax_amount ?? 0, 2),
                    number_format($party->net_amount ?? 0, 2)
                ]);
                fputcsv($file, $row);
            }

            fputcsv($file, []);
            $totalRow = ['', 'TOTAL'];
            if ($printAddress === 'Y') $totalRow[] = '';
            if ($printStaxNo === 'Y') $totalRow[] = '';
            $totalRow = array_merge($totalRow, [
                '',
                $totals['bills'] ?? 0,
                number_format($totals['gross_amount'] ?? 0, 2),
                number_format($totals['discount'] ?? 0, 2),
                number_format($totals['tax_amount'] ?? 0, 2),
                number_format($totals['net_amount'] ?? 0, 2)
            ]);
            fputcsv($file, $totalRow);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Monthly Purchase Summary
     */
    public function monthlyPurchaseSummary(Request $request)
    {
        // HTML5 month input sends YYYY-MM format
        $monthFromInput = $request->get('month_from');
        $monthToInput = $request->get('month_to');
        
        // Set defaults
        $monthFrom = $monthFromInput ?? Carbon::now()->startOfYear()->format('Y-m');
        $monthTo = $monthToInput ?? Carbon::now()->format('Y-m');
        
        $companyCode = $request->get('company_code');
        $companyId = $request->get('company_id');
        $showDnCn = $request->get('show_dn_cn', 'Y');
        $showBrExp = $request->get('show_br_exp', 'Y');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $monthlySummary = [];
        $totals = [
            'bills' => 0,
            'purchase' => 0,
            'return' => 0,
            'dn' => 0,
            'cn' => 0,
            'net' => 0,
            'tax' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('month_from');

        if ($formSubmitted && $monthFromInput && $monthToInput) {
            // Parse YYYY-MM format from HTML5 month input
            $fromParts = explode('-', $monthFrom);
            $toParts = explode('-', $monthTo);
            
            $fromYear = (int)($fromParts[0] ?? date('Y'));
            $fromMonth = (int)($fromParts[1] ?? 1);
            $toYear = (int)($toParts[0] ?? date('Y'));
            $toMonth = (int)($toParts[1] ?? 12);
            
            $startDate = Carbon::createFromDate($fromYear, $fromMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($toYear, $toMonth, 1)->endOfMonth();

            // Get purchase data grouped by month
            $purchaseQuery = PurchaseTransaction::select(
                    DB::raw('YEAR(bill_date) as year'),
                    DB::raw('MONTH(bill_date) as month'),
                    DB::raw('COUNT(*) as bill_count'),
                    DB::raw('SUM(nt_amount) as gross_amount'),
                    DB::raw('SUM(tax_amount) as tax_amount'),
                    DB::raw('SUM(net_amount) as net_amount')
                )
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->groupBy('year', 'month');

            if ($companyId) {
                $purchaseQuery->where('supplier_id', $companyId);
            }

            $purchaseData = $purchaseQuery->get()->keyBy(function($item) {
                return $item->month . '/' . $item->year;
            });

            // Get return data grouped by month
            $returnQuery = PurchaseReturnTransaction::select(
                    DB::raw('YEAR(return_date) as year'),
                    DB::raw('MONTH(return_date) as month'),
                    DB::raw('COUNT(*) as bill_count'),
                    DB::raw('SUM(net_amount) as net_amount')
                )
                ->whereBetween('return_date', [$startDate, $endDate])
                ->groupBy('year', 'month');

            if ($companyId) {
                $returnQuery->where('supplier_id', $companyId);
            }

            $returnData = $returnQuery->get()->keyBy(function($item) {
                return $item->month . '/' . $item->year;
            });

            // Get DN/CN data if enabled
            $dnData = collect();
            $cnData = collect();
            
            if ($showDnCn === 'Y') {
                $dnQuery = DebitNote::select(
                        DB::raw('YEAR(debit_note_date) as year'),
                        DB::raw('MONTH(debit_note_date) as month'),
                        DB::raw('SUM(net_amount) as net_amount')
                    )
                    ->where('debit_party_type', 'S')
                    ->whereBetween('debit_note_date', [$startDate, $endDate])
                    ->groupBy('year', 'month');

                $dnData = $dnQuery->get()->keyBy(function($item) {
                    return $item->month . '/' . $item->year;
                });

                $cnQuery = CreditNote::select(
                        DB::raw('YEAR(credit_note_date) as year'),
                        DB::raw('MONTH(credit_note_date) as month'),
                        DB::raw('SUM(net_amount) as net_amount')
                    )
                    ->where('credit_party_type', 'S')
                    ->whereBetween('credit_note_date', [$startDate, $endDate])
                    ->groupBy('year', 'month');

                $cnData = $cnQuery->get()->keyBy(function($item) {
                    return $item->month . '/' . $item->year;
                });
            }

            // Build monthly summary
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $key = $currentDate->month . '/' . $currentDate->year;
                $monthName = $currentDate->format('M-Y');
                
                $purchase = $purchaseData->get($key);
                $return = $returnData->get($key);
                $dn = $dnData->get($key);
                $cn = $cnData->get($key);
                
                $monthlySummary[$monthName] = [
                    'month' => $monthName,
                    'bills' => $purchase->bill_count ?? 0,
                    'purchase' => $purchase->net_amount ?? 0,
                    'return' => $return->net_amount ?? 0,
                    'dn' => $dn->net_amount ?? 0,
                    'cn' => $cn->net_amount ?? 0,
                    'tax' => $purchase->tax_amount ?? 0,
                    'net' => ($purchase->net_amount ?? 0) - ($return->net_amount ?? 0) + ($dn->net_amount ?? 0) - ($cn->net_amount ?? 0)
                ];
                
                $totals['bills'] += $purchase->bill_count ?? 0;
                $totals['purchase'] += $purchase->net_amount ?? 0;
                $totals['return'] += $return->net_amount ?? 0;
                $totals['dn'] += $dn->net_amount ?? 0;
                $totals['cn'] += $cn->net_amount ?? 0;
                $totals['tax'] += $purchase->tax_amount ?? 0;
                
                $currentDate->addMonth();
            }
            
            $totals['net'] = $totals['purchase'] - $totals['return'] + $totals['dn'] - $totals['cn'];
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.monthly-purchase-summary-print', compact(
                'monthFrom', 'monthTo', 'suppliers', 'monthlySummary', 'totals',
                'companyCode', 'companyId', 'showDnCn', 'showBrExp'
            ));
        }
        
        return view('admin.reports.purchase-report.monthly-purchase-summary', compact(
            'monthFrom', 'monthTo', 'suppliers', 'monthlySummary', 'totals',
            'companyCode', 'companyId', 'showDnCn', 'showBrExp'
        ));
    }

    /**
     * Debit/Credit Note Report
     */
    public function debitCreditNote(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Filters
        $partyType = $request->get('party_type', 'A'); // S=Sale, P=Purchase, G=General, A=All
        $noteType = $request->get('note_type', 'A'); // D=Debit Note, C=Credit Note, A=All
        $customerCode = $request->get('customer_code');
        $customerId = $request->get('customer_id');
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        $ledgerCode = $request->get('ledger_code');
        $ledgerId = $request->get('ledger_id');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $customers = Customer::select('id', 'name', 'code')->orderBy('name')->get();
        
        $notes = collect();
        $totals = [
            'count' => 0,
            'dn_count' => 0,
            'cn_count' => 0,
            'dn_amount' => 0,
            'cn_amount' => 0,
            'net_amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Get Debit Notes
            if ($noteType === 'A' || $noteType === 'D') {
                $dnQuery = DebitNote::with(['supplier:supplier_id,name,code', 'customer:id,name,code'])
                    ->whereBetween('debit_note_date', [$dateFrom, $dateTo]);
                
                // Party type filter
                if ($partyType === 'S') {
                    $dnQuery->where('debit_party_type', 'C'); // Customer for Sale
                } elseif ($partyType === 'P') {
                    $dnQuery->where('debit_party_type', 'S'); // Supplier for Purchase
                } elseif ($partyType === 'G') {
                    $dnQuery->where('debit_party_type', 'G'); // General Ledger
                }
                
                // Specific party filters
                if ($customerId) {
                    $dnQuery->where('debit_party_type', 'C')->where('debit_party_id', $customerId);
                }
                if ($supplierId) {
                    $dnQuery->where('debit_party_type', 'S')->where('debit_party_id', $supplierId);
                }
                
                $debitNotes = $dnQuery->orderBy('debit_note_date')->orderBy('debit_note_no')->get();
                
                $debitNotes = $debitNotes->map(function($item) {
                    $item->note_type = 'DN';
                    $item->note_date = $item->debit_note_date;
                    $item->note_no = $item->debit_note_no;
                    $item->party_name = $item->debit_party_name;
                    $item->party_type_label = $item->debit_party_type === 'S' ? 'Supplier' : ($item->debit_party_type === 'C' ? 'Customer' : 'Ledger');
                    return $item;
                });
                
                $notes = $notes->merge($debitNotes);
                $totals['dn_count'] = $debitNotes->count();
                $totals['dn_amount'] = $debitNotes->sum('net_amount');
            }
            
            // Get Credit Notes
            if ($noteType === 'A' || $noteType === 'C') {
                $cnQuery = CreditNote::with(['supplier:supplier_id,name,code', 'customer:id,name,code'])
                    ->whereBetween('credit_note_date', [$dateFrom, $dateTo]);
                
                // Party type filter
                if ($partyType === 'S') {
                    $cnQuery->where('credit_party_type', 'C'); // Customer for Sale
                } elseif ($partyType === 'P') {
                    $cnQuery->where('credit_party_type', 'S'); // Supplier for Purchase
                } elseif ($partyType === 'G') {
                    $cnQuery->where('credit_party_type', 'G'); // General Ledger
                }
                
                // Specific party filters
                if ($customerId) {
                    $cnQuery->where('credit_party_type', 'C')->where('credit_party_id', $customerId);
                }
                if ($supplierId) {
                    $cnQuery->where('credit_party_type', 'S')->where('credit_party_id', $supplierId);
                }
                
                $creditNotes = $cnQuery->orderBy('credit_note_date')->orderBy('credit_note_no')->get();
                
                $creditNotes = $creditNotes->map(function($item) {
                    $item->note_type = 'CN';
                    $item->note_date = $item->credit_note_date;
                    $item->note_no = $item->credit_note_no;
                    $item->party_name = $item->credit_party_name;
                    $item->party_type_label = $item->credit_party_type === 'S' ? 'Supplier' : ($item->credit_party_type === 'C' ? 'Customer' : 'Ledger');
                    return $item;
                });
                
                $notes = $notes->merge($creditNotes);
                $totals['cn_count'] = $creditNotes->count();
                $totals['cn_amount'] = $creditNotes->sum('net_amount');
            }
            
            // Sort by date
            $notes = $notes->sortBy('note_date')->values();
            
            $totals['count'] = $totals['dn_count'] + $totals['cn_count'];
            $totals['net_amount'] = $totals['dn_amount'] - $totals['cn_amount'];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportDebitCreditNoteToExcel($notes, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.debit-credit-note-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'customers', 'notes', 'totals',
                'partyType', 'noteType', 'customerCode', 'customerId', 'supplierCode', 
                'supplierId', 'ledgerCode', 'ledgerId'
            ));
        }
        
        return view('admin.reports.purchase-report.debit-credit-note', compact(
            'dateFrom', 'dateTo', 'suppliers', 'customers', 'notes', 'totals',
            'partyType', 'noteType', 'customerCode', 'customerId', 'supplierCode', 
            'supplierId', 'ledgerCode', 'ledgerId'
        ));
    }

    /**
     * Export Debit/Credit Note to Excel
     */
    private function exportDebitCreditNoteToExcel($notes, $totals, $dateFrom, $dateTo)
    {
        $filename = 'debit_credit_note_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($notes, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Type', 'Note No', 'Date', 'Party Type', 'Party Name', 'Reason', 'Gross Amt', 'GST', 'Net Amount']);

            foreach ($notes as $note) {
                fputcsv($file, [
                    $note->note_type,
                    $note->note_no,
                    $note->note_date->format('d-m-Y'),
                    $note->party_type_label ?? '',
                    $note->party_name ?? '',
                    $note->reason ?? '',
                    number_format($note->gross_amount ?? 0, 2),
                    number_format($note->total_gst ?? 0, 2),
                    number_format($note->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'DN Total', '', '', '', number_format($totals['dn_amount'] ?? 0, 2)]);
            fputcsv($file, ['', '', '', '', 'CN Total', '', '', '', number_format($totals['cn_amount'] ?? 0, 2)]);
            fputcsv($file, ['', '', '', '', 'Net Total', '', '', '', number_format($totals['net_amount'] ?? 0, 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Day Purchase Summary Item Wise
     */
    public function dayPurchaseSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $showValue = $request->get('show_value', 'Y');
        $groupBy = $request->get('group_by', 'C'); // C=Company, I=Item, B=Balance Stock
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $categoryCode = $request->get('category_code');
        $categoryId = $request->get('category_id');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $items = collect();
        $totals = [
            'qty' => 0,
            'free' => 0,
            'value' => 0,
            'balance' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransactionItem::select(
                    'purchase_transaction_items.company_name',
                    'purchase_transaction_items.item_name',
                    'purchase_transaction_items.item_code',
                    'purchase_transaction_items.packing',
                    DB::raw('SUM(purchase_transaction_items.qty) as total_qty'),
                    DB::raw('SUM(purchase_transaction_items.free_qty) as total_free'),
                    DB::raw('SUM(purchase_transaction_items.net_amount) as total_value'),
                    DB::raw('0 as balance_qty')
                )
                ->join('purchase_transactions', 'purchase_transactions.id', '=', 'purchase_transaction_items.purchase_transaction_id')
                ->whereBetween('purchase_transactions.bill_date', [$dateFrom, $dateTo]);

            // Local/Central filter
            if ($localCentral !== 'B') {
                $query->whereHas('transaction.supplier', function($q) use ($localCentral) {
                    $q->where('local_central_flag', $localCentral);
                });
            }

            // Group by selection
            if ($groupBy === 'C') {
                $query->groupBy('purchase_transaction_items.company_name', 'purchase_transaction_items.item_name', 
                    'purchase_transaction_items.item_code', 'purchase_transaction_items.packing')
                    ->orderBy('purchase_transaction_items.company_name')
                    ->orderBy('purchase_transaction_items.item_name');
            } else {
                $query->groupBy('purchase_transaction_items.item_name', 'purchase_transaction_items.item_code', 
                    'purchase_transaction_items.packing', 'purchase_transaction_items.company_name')
                    ->orderBy('purchase_transaction_items.item_name');
            }

            $items = $query->get();

            // Calculate totals
            $totals = [
                'qty' => $items->sum('total_qty'),
                'free' => $items->sum('total_free'),
                'value' => $items->sum('total_value'),
                'balance' => $items->sum('balance_qty')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportDayPurchaseSummaryToExcel($items, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.day-purchase-summary-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'showValue', 
                'groupBy', 'localCentral', 'categoryCode', 'categoryId'
            ));
        }
        
        return view('admin.reports.purchase-report.day-purchase-summary', compact(
            'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'showValue', 
            'groupBy', 'localCentral', 'categoryCode', 'categoryId'
        ));
    }

    /**
     * Export Day Purchase Summary to Excel
     */
    private function exportDayPurchaseSummaryToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'day_purchase_summary_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Company', 'Item Name', 'Pack', 'Purchase Qty', 'Value', 'Balance']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->company_name ?? '',
                    $item->item_name ?? '',
                    $item->packing ?? '',
                    $item->total_qty ?? 0,
                    number_format($item->total_value ?? 0, 2),
                    $item->balance_qty ?? 0
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', 'TOTAL', '', $totals['qty'], number_format($totals['value'], 2), $totals['balance']]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase/Return Book Item Wise
     */
    public function purchaseReturnItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $showValue = $request->get('show_value', 'Y');
        $groupBy = $request->get('group_by', 'C'); // C=Company, I=Item, B=Balance Stock
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $categoryCode = $request->get('category_code');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $items = collect();
        $totals = [
            'purchase_qty' => 0,
            'purchase_value' => 0,
            'return_qty' => 0,
            'return_value' => 0,
            'net_qty' => 0,
            'net_value' => 0,
            'balance' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Get Purchase Items
            $purchaseQuery = PurchaseTransactionItem::select(
                    'purchase_transaction_items.company_name',
                    'purchase_transaction_items.item_name',
                    'purchase_transaction_items.item_code',
                    'purchase_transaction_items.packing',
                    DB::raw('SUM(purchase_transaction_items.qty) as purchase_qty'),
                    DB::raw('SUM(purchase_transaction_items.net_amount) as purchase_value')
                )
                ->join('purchase_transactions', 'purchase_transactions.id', '=', 'purchase_transaction_items.purchase_transaction_id')
                ->whereBetween('purchase_transactions.bill_date', [$dateFrom, $dateTo]);

            // Local/Central filter
            if ($localCentral !== 'B') {
                $purchaseQuery->whereHas('transaction.supplier', function($q) use ($localCentral) {
                    $q->where('local_central_flag', $localCentral);
                });
            }

            // Group by selection
            if ($groupBy === 'C') {
                $purchaseQuery->groupBy('purchase_transaction_items.company_name', 'purchase_transaction_items.item_name', 
                    'purchase_transaction_items.item_code', 'purchase_transaction_items.packing')
                    ->orderBy('purchase_transaction_items.company_name')
                    ->orderBy('purchase_transaction_items.item_name');
            } else {
                $purchaseQuery->groupBy('purchase_transaction_items.item_name', 'purchase_transaction_items.item_code', 
                    'purchase_transaction_items.packing', 'purchase_transaction_items.company_name')
                    ->orderBy('purchase_transaction_items.item_name');
            }

            $purchaseItems = $purchaseQuery->get()->keyBy(function($item) {
                return $item->company_name . '|' . $item->item_name;
            });

            // Get Return Items (if PurchaseReturnTransactionItem exists)
            $returnItems = collect();
            if (class_exists('App\Models\PurchaseReturnTransactionItem')) {
                // Similar query for returns - simplified for now
            }

            // Merge purchase and return data
            $items = $purchaseItems->map(function($item) use ($returnItems) {
                $key = $item->company_name . '|' . $item->item_name;
                $return = $returnItems->get($key);
                
                $item->return_qty = $return->return_qty ?? 0;
                $item->return_value = $return->return_value ?? 0;
                $item->net_qty = $item->purchase_qty - $item->return_qty;
                $item->net_value = $item->purchase_value - $item->return_value;
                $item->balance_qty = 0; // Would need stock calculation
                
                return $item;
            })->values();

            // Calculate totals
            $totals = [
                'purchase_qty' => $items->sum('purchase_qty'),
                'purchase_value' => $items->sum('purchase_value'),
                'return_qty' => $items->sum('return_qty'),
                'return_value' => $items->sum('return_value'),
                'net_qty' => $items->sum('net_qty'),
                'net_value' => $items->sum('net_value'),
                'balance' => $items->sum('balance_qty')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseReturnItemWiseToExcel($items, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-return-item-wise-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'showValue', 
                'groupBy', 'localCentral', 'categoryCode'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-return-item-wise', compact(
            'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'showValue', 
            'groupBy', 'localCentral', 'categoryCode'
        ));
    }

    /**
     * Export Purchase/Return Item Wise to Excel
     */
    private function exportPurchaseReturnItemWiseToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_return_item_wise_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Company', 'Item Name', 'Pack', 'Purchase Qty', 'Purchase Value', 'Return Qty', 'Return Value', 'Net Qty', 'Net Value', 'Balance']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->company_name ?? '',
                    $item->item_name ?? '',
                    $item->packing ?? '',
                    $item->purchase_qty ?? 0,
                    number_format($item->purchase_value ?? 0, 2),
                    $item->return_qty ?? 0,
                    number_format($item->return_value ?? 0, 2),
                    $item->net_qty ?? 0,
                    number_format($item->net_value ?? 0, 2),
                    $item->balance_qty ?? 0
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', 'TOTAL', '', 
                $totals['purchase_qty'], number_format($totals['purchase_value'], 2),
                $totals['return_qty'], number_format($totals['return_value'], 2),
                $totals['net_qty'], number_format($totals['net_value'], 2),
                $totals['balance']
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Local/Central Purchase Register
     */
    public function localCentralRegister(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '5'); // 1=Purchase, 2=Return, 3=DN, 4=CN, 5=Consolidated
        $partyCode = $request->get('party_code');
        $supplierId = $request->get('supplier_id');
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $selectiveAll = $request->get('selective_all', 'A'); // S=Selective, A=All
        $taxPercent = $request->get('tax_percent', '0.00');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code', 'local_central_flag')->orderBy('name')->get();
        
        $purchases = collect();
        $totals = [
            'count' => 0,
            'local_count' => 0,
            'central_count' => 0,
            'taxable' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'igst' => 0,
            'total' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Build query based on report type
            if (in_array($reportType, ['1', '5'])) {
                $query = PurchaseTransaction::with(['supplier:supplier_id,name,code,gst_no,local_central_flag,state_code'])
                    ->whereBetween('bill_date', [$dateFrom, $dateTo]);

                // Local/Central filter
                if ($localCentral !== 'B') {
                    $query->whereHas('supplier', function($q) use ($localCentral) {
                        $q->where('local_central_flag', $localCentral);
                    });
                }

                // Supplier filter
                if ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                }

                $query->orderBy('bill_date')->orderBy('bill_no');
                $purchases = $query->get();

                // Add type indicator
                $purchases = $purchases->map(function($item) {
                    $item->record_type = 'Purchase';
                    $item->is_local = ($item->supplier->local_central_flag ?? 'L') === 'L';
                    // Split tax amount 50/50 for CGST/SGST (local) or full IGST (central)
                    if ($item->is_local) {
                        $item->cgst_amount = ($item->tax_amount ?? 0) / 2;
                        $item->sgst_amount = ($item->tax_amount ?? 0) / 2;
                        $item->igst_amount = 0;
                    } else {
                        $item->cgst_amount = 0;
                        $item->sgst_amount = 0;
                        $item->igst_amount = $item->tax_amount ?? 0;
                    }
                    return $item;
                });

                // Calculate totals
                $totals['count'] = $purchases->count();
                $totals['local_count'] = $purchases->where('is_local', true)->count();
                $totals['central_count'] = $purchases->where('is_local', false)->count();
                $totals['taxable'] = $purchases->sum('nt_amount');
                $totals['cgst'] = $purchases->sum('cgst_amount');
                $totals['sgst'] = $purchases->sum('sgst_amount');
                $totals['igst'] = $purchases->sum('igst_amount');
                $totals['total'] = $purchases->sum('net_amount');
            }
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportLocalCentralRegisterToExcel($purchases, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.local-central-register-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'reportType',
                'partyCode', 'supplierId', 'localCentral', 'selectiveAll', 'taxPercent'
            ));
        }
        
        return view('admin.reports.purchase-report.local-central-register', compact(
            'dateFrom', 'dateTo', 'suppliers', 'purchases', 'totals', 'reportType',
            'partyCode', 'supplierId', 'localCentral', 'selectiveAll', 'taxPercent'
        ));
    }

    /**
     * Export Local/Central Register to Excel
     */
    private function exportLocalCentralRegisterToExcel($purchases, $totals, $dateFrom, $dateTo)
    {
        $filename = 'local_central_register_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Supplier', 'GSTN', 'Type', 'Taxable', 'CGST', 'SGST', 'IGST', 'Total']);

            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->bill_date->format('d-m-Y'),
                    $purchase->bill_no,
                    $purchase->supplier->name ?? '',
                    $purchase->supplier->gst_no ?? '',
                    $purchase->is_local ? 'Local' : 'Central',
                    number_format($purchase->nt_amount ?? 0, 2),
                    number_format($purchase->cgst_amount ?? 0, 2),
                    number_format($purchase->sgst_amount ?? 0, 2),
                    number_format($purchase->igst_amount ?? 0, 2),
                    number_format($purchase->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '',
                number_format($totals['taxable'], 2),
                number_format($totals['cgst'], 2),
                number_format($totals['sgst'], 2),
                number_format($totals['igst'], 2),
                number_format($totals['total'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase Voucher Detail
     */
    public function purchaseVoucherDetail(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $voucherNo = $request->get('voucher_no');
        $billNo = $request->get('bill_no');
        $localInterState = $request->get('local_inter_state', 'L'); // L=Local, I=Inter State, B=Both
        $rcm = $request->get('rcm', 'N'); // Y/N
        $drAccountCode = $request->get('dr_account_code');
        $drAccountId = $request->get('dr_account_id');
        $crAccountCode = $request->get('cr_account_code');
        $crAccountId = $request->get('cr_account_id');
        $hsnCode = $request->get('hsn_code', '1'); // 1=With, 2=Without
        $voucherType = $request->get('voucher_type', '3'); // 1=Voucher Sale, 2=Voucher Purchase, 3=All
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $vouchers = collect();
        $totals = [
            'count' => 0,
            'amount' => 0,
            'tax' => 0,
            'total' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransaction::with([
                'supplier:supplier_id,name,code,gst_no,local_central_flag',
                'items'
            ])->whereBetween('bill_date', [$dateFrom, $dateTo]);

            // Voucher No filter
            if ($voucherNo) {
                $query->where('trn_no', 'like', "%{$voucherNo}%");
            }

            // Bill No filter
            if ($billNo) {
                $query->where('bill_no', 'like', "%{$billNo}%");
            }

            // Local/Inter State filter
            if ($localInterState !== 'B') {
                $query->whereHas('supplier', function($q) use ($localInterState) {
                    $flag = $localInterState === 'L' ? 'L' : 'C';
                    $q->where('local_central_flag', $flag);
                });
            }

            // Supplier (Dr. Account) filter
            if ($drAccountId) {
                $query->where('supplier_id', $drAccountId);
            }

            $query->orderBy('bill_date')->orderBy('bill_no');
            $vouchers = $query->get();

            // Add computed fields
            $vouchers = $vouchers->map(function($item) {
                $item->is_local = ($item->supplier->local_central_flag ?? 'L') === 'L';
                $item->item_count = $item->items->count();
                return $item;
            });

            // Calculate totals
            $totals = [
                'count' => $vouchers->count(),
                'amount' => $vouchers->sum('nt_amount'),
                'tax' => $vouchers->sum('tax_amount'),
                'total' => $vouchers->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseVoucherDetailToExcel($vouchers, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-voucher-detail-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'vouchers', 'totals', 'voucherNo', 'billNo',
                'localInterState', 'rcm', 'drAccountCode', 'drAccountId', 'crAccountCode', 
                'crAccountId', 'hsnCode', 'voucherType'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-voucher-detail', compact(
            'dateFrom', 'dateTo', 'suppliers', 'vouchers', 'totals', 'voucherNo', 'billNo',
            'localInterState', 'rcm', 'drAccountCode', 'drAccountId', 'crAccountCode', 
            'crAccountId', 'hsnCode', 'voucherType'
        ));
    }

    /**
     * Export Purchase Voucher Detail to Excel
     */
    private function exportPurchaseVoucherDetailToExcel($vouchers, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_voucher_detail_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($vouchers, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Voucher No', 'Bill No', 'Supplier', 'GSTN', 'Type', 'Items', 'Amount', 'Tax', 'Total']);

            foreach ($vouchers as $voucher) {
                fputcsv($file, [
                    $voucher->bill_date->format('d-m-Y'),
                    $voucher->trn_no ?? '',
                    $voucher->bill_no ?? '',
                    $voucher->supplier->name ?? '',
                    $voucher->supplier->gst_no ?? '',
                    $voucher->is_local ? 'Local' : 'Inter',
                    $voucher->item_count ?? 0,
                    number_format($voucher->nt_amount ?? 0, 2),
                    number_format($voucher->tax_amount ?? 0, 2),
                    number_format($voucher->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL', '', '',
                number_format($totals['amount'], 2),
                number_format($totals['tax'], 2),
                number_format($totals['total'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Short Expiry Received Report
     */
    public function shortExpiryReceived(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $noOfMonths = $request->get('no_of_months', 6);
        $dateType = $request->get('date_type', 'B'); // B=Bill Date, R=Receive Date
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        $availableBatchQty = $request->boolean('available_batch_qty');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $shortExpiry = collect();
        $totals = [
            'count' => 0,
            'qty' => 0,
            'amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Calculate expiry cutoff date (items expiring within X months from today)
            $expiryCutoff = Carbon::now()->addMonths((int)$noOfMonths);
            
            $query = PurchaseTransactionItem::with([
                'transaction:id,bill_no,bill_date,supplier_id,receive_date',
                'transaction.supplier:supplier_id,name,code'
            ])
            ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $dateType, $supplierId) {
                $dateField = $dateType === 'R' ? 'receive_date' : 'bill_date';
                $q->whereBetween($dateField, [$dateFrom, $dateTo]);
                
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            })
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $expiryCutoff)
            ->where('expiry_date', '>=', Carbon::now()); // Only items not yet expired

            // If available batch qty is checked, only show items with remaining qty
            if ($availableBatchQty) {
                $query->where('qty', '>', 0);
            }

            $query->orderBy('expiry_date', 'asc');
            
            $items = $query->get();

            // Map items with computed fields
            $shortExpiry = $items->map(function($item) {
                $daysLeft = Carbon::now()->diffInDays($item->expiry_date, false);
                return (object)[
                    'received_date' => $item->transaction->receive_date ?? $item->transaction->bill_date,
                    'bill_no' => $item->transaction->bill_no ?? '-',
                    'supplier_name' => $item->transaction->supplier->name ?? 'N/A',
                    'supplier_code' => $item->transaction->supplier->code ?? '',
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry_date' => $item->expiry_date,
                    'days_left' => max(0, $daysLeft),
                    'qty' => $item->qty,
                    'amount' => $item->net_amount ?? $item->amount,
                    'mrp' => $item->mrp,
                    'pur_rate' => $item->pur_rate
                ];
            });

            // Calculate totals
            $totals = [
                'count' => $shortExpiry->count(),
                'qty' => $shortExpiry->sum('qty'),
                'amount' => $shortExpiry->sum('amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportShortExpiryReceivedToExcel($shortExpiry, $totals, $dateFrom, $dateTo, $noOfMonths);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.short-expiry-received-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'shortExpiry', 'totals', 'noOfMonths',
                'dateType', 'supplierCode', 'supplierId', 'availableBatchQty'
            ));
        }
        
        return view('admin.reports.purchase-report.short-expiry-received', compact(
            'dateFrom', 'dateTo', 'suppliers', 'shortExpiry', 'totals', 'noOfMonths',
            'dateType', 'supplierCode', 'supplierId', 'availableBatchQty'
        ));
    }

    /**
     * Export Short Expiry Received to Excel
     */
    private function exportShortExpiryReceivedToExcel($shortExpiry, $totals, $dateFrom, $dateTo, $noOfMonths)
    {
        $filename = 'short_expiry_received_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($shortExpiry, $totals, $noOfMonths) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Short Expiry Received Report - Items expiring within ' . $noOfMonths . ' months']);
            fputcsv($file, []);
            fputcsv($file, ['Recv Date', 'Bill No', 'Supplier', 'Item Name', 'Batch', 'Expiry', 'Days Left', 'Qty', 'Rate', 'Amount']);

            foreach ($shortExpiry as $item) {
                fputcsv($file, [
                    $item->received_date ? $item->received_date->format('d-m-Y') : '-',
                    $item->bill_no,
                    $item->supplier_name,
                    $item->item_name,
                    $item->batch_no ?? '-',
                    $item->expiry_date ? $item->expiry_date->format('M-Y') : '-',
                    $item->days_left . ' days',
                    number_format($item->qty, 2),
                    number_format($item->pur_rate ?? 0, 2),
                    number_format($item->amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', 'TOTAL', '', 
                number_format($totals['qty'], 2), '',
                number_format($totals['amount'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Purchase Return List
     */
    public function purchaseReturnList(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        $adjustedFilter = $request->get('adjusted_filter', 'A'); // A=All, Y=Adjusted, N=Unadjusted
        $salesmanCode = $request->get('salesman_code');
        $salesmanId = $request->get('salesman_id');
        $routeCode = $request->get('route_code');
        $routeId = $request->get('route_id');
        $areaCode = $request->get('area_code');
        $areaId = $request->get('area_id');
        $flag = $request->get('flag', '5'); // 1=PR, 2=CN, 3=DN, 4=PE, 5=ALL
        $replCreditNote = $request->get('repl_credit_note');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $salesmen = \App\Models\SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $routes = \App\Models\Route::select('id', 'name', 'alter_code as code')->orderBy('name')->get();
        $areas = Area::select('id', 'name', 'alter_code as area_code')->orderBy('name')->get();
        
        $returns = collect();
        $adjustments = collect();
        $references = collect();
        $totals = [
            'count' => 0,
            'amount' => 0,
            'taxable' => 0,
            'tax' => 0,
            'due_amount' => 0,
            'adj_amount' => 0
        ];
        $adjustmentTotal = 0;

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseReturnTransaction::with([
                'supplier:supplier_id,name,code'
            ])->whereBetween('return_date', [$dateFrom, $dateTo]);

            // Supplier filter
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // Adjusted filter
            if ($adjustedFilter === 'Y') {
                $query->where(function($q) {
                    $q->where('balance_amount', '<=', 0)->orWhereNull('balance_amount');
                });
            } elseif ($adjustedFilter === 'N') {
                $query->where('balance_amount', '>', 0);
            }

            // Flag filter (voucher type) - only apply if not ALL (5)
            if ($flag !== '5' && $flag !== '') {
                $voucherTypes = [
                    '1' => 'PR',
                    '2' => 'CN',
                    '3' => 'DN',
                    '4' => 'PE'
                ];
                if (isset($voucherTypes[$flag])) {
                    $query->where(function($q) use ($voucherTypes, $flag) {
                        $q->where('voucher_type', $voucherTypes[$flag])
                          ->orWhereNull('voucher_type')
                          ->orWhere('voucher_type', '');
                    });
                }
            }

            $query->orderBy('return_date')->orderBy('pr_no');
            $returns = $query->get();

            // Calculate totals
            $totals = [
                'count' => $returns->count(),
                'amount' => $returns->sum('net_amount'),
                'taxable' => $returns->sum('nt_amount'),
                'tax' => $returns->sum('tax_amount'),
                'due_amount' => $returns->sum('balance_amount'),
                'adj_amount' => $returns->sum(function($r) {
                    return $r->net_amount - ($r->balance_amount ?? 0);
                })
            ];

            // Get reference details (original purchase bills)
            $references = $returns->filter(function($r) {
                return !empty($r->invoice_no);
            })->map(function($r) {
                return (object)[
                    'trn_no' => $r->pr_no,
                    'pbill_no' => $r->invoice_no,
                    'pbill_date' => $r->invoice_date,
                    'amount' => $r->net_amount
                ];
            });
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseReturnListToExcel($returns, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.purchase-return-list-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'salesmen', 'routes', 'areas',
                'returns', 'adjustments', 'references', 'totals', 'adjustmentTotal',
                'supplierCode', 'supplierId', 'adjustedFilter', 'salesmanCode', 'salesmanId',
                'routeCode', 'routeId', 'areaCode', 'areaId', 'flag', 'replCreditNote'
            ));
        }
        
        return view('admin.reports.purchase-report.purchase-return-list', compact(
            'dateFrom', 'dateTo', 'suppliers', 'salesmen', 'routes', 'areas',
            'returns', 'adjustments', 'references', 'totals', 'adjustmentTotal',
            'supplierCode', 'supplierId', 'adjustedFilter', 'salesmanCode', 'salesmanId',
            'routeCode', 'routeId', 'areaCode', 'areaId', 'flag', 'replCreditNote'
        ));
    }

    /**
     * Export Purchase Return List to Excel
     */
    private function exportPurchaseReturnListToExcel($returns, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_return_list_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($returns, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Code', 'Party Name', 'Amount', 'Taxable', 'Tax', 'Due Amt', 'Adj. Amt']);

            foreach ($returns as $return) {
                $adjAmt = $return->net_amount - ($return->balance_amount ?? 0);
                fputcsv($file, [
                    $return->return_date->format('d-m-Y'),
                    $return->pr_no,
                    $return->supplier->code ?? '',
                    $return->supplier->name ?? '',
                    number_format($return->net_amount ?? 0, 2),
                    number_format($return->nt_amount ?? 0, 2),
                    number_format($return->tax_amount ?? 0, 2),
                    number_format($return->balance_amount ?? 0, 2),
                    number_format($adjAmt, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL',
                number_format($totals['amount'], 2),
                number_format($totals['taxable'], 2),
                number_format($totals['tax'], 2),
                number_format($totals['due_amount'], 2),
                number_format($totals['adj_amount'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GST SET OFF Report
     */
    public function gstSetOff(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $showDnCn = $request->get('show_dn_cn', 'Y');
        $showBrExp = $request->get('show_br_exp', 'Y');
        $withoutHsn = $request->boolean('without_hsn');
        
        // Initialize arrays
        $input = [
            'purchase_cgst' => 0, 'purchase_sgst' => 0, 'purchase_igst' => 0, 'purchase_total' => 0,
            'dn_cgst' => 0, 'dn_sgst' => 0, 'dn_igst' => 0, 'dn_total' => 0,
            'cn_cgst' => 0, 'cn_sgst' => 0, 'cn_igst' => 0, 'cn_total' => 0,
            'net_cgst' => 0, 'net_sgst' => 0, 'net_igst' => 0, 'net_total' => 0
        ];
        
        $output = [
            'sales_cgst' => 0, 'sales_sgst' => 0, 'sales_igst' => 0, 'sales_total' => 0,
            'cn_cgst' => 0, 'cn_sgst' => 0, 'cn_igst' => 0, 'cn_total' => 0,
            'dn_cgst' => 0, 'dn_sgst' => 0, 'dn_igst' => 0, 'dn_total' => 0,
            'net_cgst' => 0, 'net_sgst' => 0, 'net_igst' => 0, 'net_total' => 0
        ];
        
        $setoff = [
            'liability_cgst' => 0, 'liability_sgst' => 0, 'liability_igst' => 0, 'liability_total' => 0,
            'itc_cgst' => 0, 'itc_sgst' => 0, 'itc_igst' => 0, 'itc_total' => 0,
            'net_cgst' => 0, 'net_sgst' => 0, 'net_igst' => 0, 'net_total' => 0
        ];
        
        $totals = ['input_gst' => 0, 'output_gst' => 0, 'set_off' => 0, 'net' => 0];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // INPUT GST - Purchase (tax_amount split 50/50 for CGST/SGST as approximation)
            $purchaseGst = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->selectRaw('SUM(COALESCE(tax_amount, 0)) as total_tax')
                ->first();
            
            $purchaseTax = $purchaseGst->total_tax ?? 0;
            $input['purchase_cgst'] = $purchaseTax / 2;
            $input['purchase_sgst'] = $purchaseTax / 2;
            $input['purchase_igst'] = 0;
            $input['purchase_total'] = $purchaseTax;
            
            // INPUT GST - Debit Notes (from suppliers - increases ITC)
            if ($showDnCn === 'Y') {
                $dnGst = DebitNote::where('debit_party_type', 'S')
                    ->whereBetween('debit_note_date', [$dateFrom, $dateTo])
                    ->selectRaw('SUM(COALESCE(total_gst, 0)) as total_tax')
                    ->first();
                
                $dnTax = $dnGst->total_tax ?? 0;
                $input['dn_cgst'] = $dnTax / 2;
                $input['dn_sgst'] = $dnTax / 2;
                $input['dn_igst'] = 0;
                $input['dn_total'] = $dnTax;
                
                // INPUT GST - Credit Notes (from suppliers - reduces ITC)
                $cnGst = CreditNote::where('credit_party_type', 'S')
                    ->whereBetween('credit_note_date', [$dateFrom, $dateTo])
                    ->selectRaw('SUM(COALESCE(total_gst, 0)) as total_tax')
                    ->first();
                
                $cnTax = $cnGst->total_tax ?? 0;
                $input['cn_cgst'] = $cnTax / 2;
                $input['cn_sgst'] = $cnTax / 2;
                $input['cn_igst'] = 0;
                $input['cn_total'] = $cnTax;
            }
            
            // Net Input ITC
            $input['net_cgst'] = $input['purchase_cgst'] + $input['dn_cgst'] - $input['cn_cgst'];
            $input['net_sgst'] = $input['purchase_sgst'] + $input['dn_sgst'] - $input['cn_sgst'];
            $input['net_igst'] = $input['purchase_igst'] + $input['dn_igst'] - $input['cn_igst'];
            $input['net_total'] = $input['net_cgst'] + $input['net_sgst'] + $input['net_igst'];
            
            // OUTPUT GST - Sales (tax_amount split 50/50 for CGST/SGST as approximation)
            $salesGst = DB::table('sale_transactions')
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->selectRaw('SUM(COALESCE(tax_amount, 0)) as total_tax')
                ->first();
            
            $salesTax = $salesGst->total_tax ?? 0;
            $output['sales_cgst'] = $salesTax / 2;
            $output['sales_sgst'] = $salesTax / 2;
            $output['sales_igst'] = 0;
            $output['sales_total'] = $salesTax;
            
            // OUTPUT GST - Credit Notes (to customers - reduces liability)
            if ($showDnCn === 'Y') {
                $cnSalesGst = CreditNote::where('credit_party_type', 'C')
                    ->whereBetween('credit_note_date', [$dateFrom, $dateTo])
                    ->selectRaw('SUM(COALESCE(total_gst, 0)) as total_tax')
                    ->first();
                
                $cnSalesTax = $cnSalesGst->total_tax ?? 0;
                $output['cn_cgst'] = $cnSalesTax / 2;
                $output['cn_sgst'] = $cnSalesTax / 2;
                $output['cn_igst'] = 0;
                $output['cn_total'] = $cnSalesTax;
                
                // OUTPUT GST - Debit Notes (to customers - increases liability)
                $dnSalesGst = DebitNote::where('debit_party_type', 'C')
                    ->whereBetween('debit_note_date', [$dateFrom, $dateTo])
                    ->selectRaw('SUM(COALESCE(total_gst, 0)) as total_tax')
                    ->first();
                
                $dnSalesTax = $dnSalesGst->total_tax ?? 0;
                $output['dn_cgst'] = $dnSalesTax / 2;
                $output['dn_sgst'] = $dnSalesTax / 2;
                $output['dn_igst'] = 0;
                $output['dn_total'] = $dnSalesTax;
            }
            
            // Net Output Liability
            $output['net_cgst'] = $output['sales_cgst'] - $output['cn_cgst'] + $output['dn_cgst'];
            $output['net_sgst'] = $output['sales_sgst'] - $output['cn_sgst'] + $output['dn_sgst'];
            $output['net_igst'] = $output['sales_igst'] - $output['cn_igst'] + $output['dn_igst'];
            $output['net_total'] = $output['net_cgst'] + $output['net_sgst'] + $output['net_igst'];
            
            // GST Set Off Computation
            $setoff['liability_cgst'] = $output['net_cgst'];
            $setoff['liability_sgst'] = $output['net_sgst'];
            $setoff['liability_igst'] = $output['net_igst'];
            $setoff['liability_total'] = $output['net_total'];
            
            $setoff['itc_cgst'] = min($input['net_cgst'], max(0, $setoff['liability_cgst']));
            $setoff['itc_sgst'] = min($input['net_sgst'], max(0, $setoff['liability_sgst']));
            $setoff['itc_igst'] = min($input['net_igst'], max(0, $setoff['liability_igst']));
            $setoff['itc_total'] = $setoff['itc_cgst'] + $setoff['itc_sgst'] + $setoff['itc_igst'];
            
            $setoff['net_cgst'] = $setoff['liability_cgst'] - $setoff['itc_cgst'];
            $setoff['net_sgst'] = $setoff['liability_sgst'] - $setoff['itc_sgst'];
            $setoff['net_igst'] = $setoff['liability_igst'] - $setoff['itc_igst'];
            $setoff['net_total'] = $setoff['net_cgst'] + $setoff['net_sgst'] + $setoff['net_igst'];
            
            // Summary totals
            $totals['input_gst'] = $input['net_total'];
            $totals['output_gst'] = $output['net_total'];
            $totals['set_off'] = $setoff['itc_total'];
            $totals['net'] = $setoff['net_total'];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportGstSetOffToExcel($input, $output, $setoff, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.gst-set-off.gst-set-off-print', compact(
                'dateFrom', 'dateTo', 'showDnCn', 'showBrExp', 'withoutHsn',
                'input', 'output', 'setoff', 'totals'
            ));
        }
        
        return view('admin.reports.purchase-report.gst-set-off.gst-set-off', compact(
            'dateFrom', 'dateTo', 'showDnCn', 'showBrExp', 'withoutHsn',
            'input', 'output', 'setoff', 'totals'
        ));
    }

    /**
     * Export GST Set Off to Excel
     */
    private function exportGstSetOffToExcel($input, $output, $setoff, $totals, $dateFrom, $dateTo)
    {
        $filename = 'gst_set_off_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($input, $output, $setoff, $totals) {
            $file = fopen('php://output', 'w');
            
            // Input GST Section
            fputcsv($file, ['INPUT GST (ITC Available)']);
            fputcsv($file, ['Particulars', 'CGST', 'SGST', 'IGST', 'Total']);
            fputcsv($file, ['Purchase B2B', $input['purchase_cgst'], $input['purchase_sgst'], $input['purchase_igst'], $input['purchase_total']]);
            fputcsv($file, ['Debit Note', $input['dn_cgst'], $input['dn_sgst'], $input['dn_igst'], $input['dn_total']]);
            fputcsv($file, ['Less: Credit Note', $input['cn_cgst'], $input['cn_sgst'], $input['cn_igst'], $input['cn_total']]);
            fputcsv($file, ['Net Input ITC', $input['net_cgst'], $input['net_sgst'], $input['net_igst'], $input['net_total']]);
            fputcsv($file, []);
            
            // Output GST Section
            fputcsv($file, ['OUTPUT GST (Liability)']);
            fputcsv($file, ['Particulars', 'CGST', 'SGST', 'IGST', 'Total']);
            fputcsv($file, ['Sales B2B/B2C', $output['sales_cgst'], $output['sales_sgst'], $output['sales_igst'], $output['sales_total']]);
            fputcsv($file, ['Credit Note', $output['cn_cgst'], $output['cn_sgst'], $output['cn_igst'], $output['cn_total']]);
            fputcsv($file, ['Less: Debit Note', $output['dn_cgst'], $output['dn_sgst'], $output['dn_igst'], $output['dn_total']]);
            fputcsv($file, ['Net Output Liability', $output['net_cgst'], $output['net_sgst'], $output['net_igst'], $output['net_total']]);
            fputcsv($file, []);
            
            // Set Off Section
            fputcsv($file, ['GST SET OFF COMPUTATION']);
            fputcsv($file, ['Description', 'CGST', 'SGST', 'IGST', 'Total']);
            fputcsv($file, ['Output Liability', $setoff['liability_cgst'], $setoff['liability_sgst'], $setoff['liability_igst'], $setoff['liability_total']]);
            fputcsv($file, ['Less: Input ITC Set Off', $setoff['itc_cgst'], $setoff['itc_sgst'], $setoff['itc_igst'], $setoff['itc_total']]);
            fputcsv($file, ['Net GST Payable/(Refundable)', $setoff['net_cgst'], $setoff['net_sgst'], $setoff['net_igst'], $setoff['net_total']]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GST SET OFF GSTR Report
     */
    public function gstSetOffGstr(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $showDnCn = $request->get('show_dn_cn', 'Y');
        $showBrExp = $request->get('show_br_exp', 'Y');
        $withoutHsn = $request->boolean('without_hsn');
        
        // Initialize GSTR-3B data structure
        $gstr = [
            // 3.1 Outward Supplies
            'outward_igst' => 0, 'outward_cgst' => 0, 'outward_sgst' => 0, 'outward_cess' => 0,
            'zero_igst' => 0, 'zero_cgst' => 0, 'zero_sgst' => 0, 'zero_cess' => 0,
            'rcm_igst' => 0, 'rcm_cgst' => 0, 'rcm_sgst' => 0, 'rcm_cess' => 0,
            // 4. Eligible ITC
            'itc_igst' => 0, 'itc_cgst' => 0, 'itc_sgst' => 0, 'itc_cess' => 0,
            'import_igst' => 0, 'import_cess' => 0,
            'import_svc_igst' => 0,
            'rcm_input_igst' => 0, 'rcm_input_cgst' => 0, 'rcm_input_sgst' => 0, 'rcm_input_cess' => 0,
            'other_itc_igst' => 0, 'other_itc_cgst' => 0, 'other_itc_sgst' => 0, 'other_itc_cess' => 0,
            'reversed_igst' => 0, 'reversed_cgst' => 0, 'reversed_sgst' => 0, 'reversed_cess' => 0,
            'net_itc_igst' => 0, 'net_itc_cgst' => 0, 'net_itc_sgst' => 0, 'net_itc_cess' => 0,
            // 6. Payment of Tax
            'payable_igst' => 0, 'payable_cgst' => 0, 'payable_sgst' => 0, 'payable_cess' => 0,
            'paid_itc_igst' => 0, 'paid_itc_cgst' => 0, 'paid_itc_sgst' => 0, 'paid_itc_cess' => 0,
            'cash_igst' => 0, 'cash_cgst' => 0, 'cash_sgst' => 0, 'cash_cess' => 0,
        ];
        
        // ITC Reconciliation
        $recon = [
            'book_igst' => 0, 'book_cgst' => 0, 'book_sgst' => 0, 'book_total' => 0,
            'gstr_igst' => 0, 'gstr_cgst' => 0, 'gstr_sgst' => 0, 'gstr_total' => 0,
            'diff_igst' => 0, 'diff_cgst' => 0, 'diff_sgst' => 0, 'diff_total' => 0,
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // 3.1(a) Outward taxable supplies - Sales
            $salesGst = DB::table('sale_transactions')
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->selectRaw('SUM(COALESCE(tax_amount, 0)) as total_tax')
                ->first();
            
            $salesTax = $salesGst->total_tax ?? 0;
            $gstr['outward_cgst'] = $salesTax / 2;
            $gstr['outward_sgst'] = $salesTax / 2;
            $gstr['outward_igst'] = 0;
            
            // 4. Eligible ITC - Purchase
            $purchaseGst = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->selectRaw('SUM(COALESCE(tax_amount, 0)) as total_tax')
                ->first();
            
            $purchaseTax = $purchaseGst->total_tax ?? 0;
            $gstr['other_itc_cgst'] = $purchaseTax / 2;
            $gstr['other_itc_sgst'] = $purchaseTax / 2;
            $gstr['other_itc_igst'] = 0;
            
            // Total ITC Available
            $gstr['itc_cgst'] = $gstr['other_itc_cgst'] + $gstr['rcm_input_cgst'] + $gstr['import_igst'];
            $gstr['itc_sgst'] = $gstr['other_itc_sgst'] + $gstr['rcm_input_sgst'];
            $gstr['itc_igst'] = $gstr['other_itc_igst'] + $gstr['rcm_input_igst'];
            
            // ITC Reversed (from Credit Notes to suppliers)
            if ($showDnCn === 'Y') {
                $cnGst = CreditNote::where('credit_party_type', 'S')
                    ->whereBetween('credit_note_date', [$dateFrom, $dateTo])
                    ->selectRaw('SUM(COALESCE(total_gst, 0)) as total_tax')
                    ->first();
                
                $cnTax = $cnGst->total_tax ?? 0;
                $gstr['reversed_cgst'] = $cnTax / 2;
                $gstr['reversed_sgst'] = $cnTax / 2;
            }
            
            // Net ITC Available
            $gstr['net_itc_cgst'] = $gstr['itc_cgst'] - $gstr['reversed_cgst'];
            $gstr['net_itc_sgst'] = $gstr['itc_sgst'] - $gstr['reversed_sgst'];
            $gstr['net_itc_igst'] = $gstr['itc_igst'] - $gstr['reversed_igst'];
            
            // 6. Payment of Tax
            $gstr['payable_cgst'] = $gstr['outward_cgst'] + $gstr['rcm_cgst'];
            $gstr['payable_sgst'] = $gstr['outward_sgst'] + $gstr['rcm_sgst'];
            $gstr['payable_igst'] = $gstr['outward_igst'] + $gstr['rcm_igst'];
            
            // Paid through ITC
            $gstr['paid_itc_cgst'] = min($gstr['net_itc_cgst'], $gstr['payable_cgst']);
            $gstr['paid_itc_sgst'] = min($gstr['net_itc_sgst'], $gstr['payable_sgst']);
            $gstr['paid_itc_igst'] = min($gstr['net_itc_igst'], $gstr['payable_igst']);
            
            // Cash Payment
            $gstr['cash_cgst'] = max(0, $gstr['payable_cgst'] - $gstr['paid_itc_cgst']);
            $gstr['cash_sgst'] = max(0, $gstr['payable_sgst'] - $gstr['paid_itc_sgst']);
            $gstr['cash_igst'] = max(0, $gstr['payable_igst'] - $gstr['paid_itc_igst']);
            
            // ITC Reconciliation (As per Books)
            $recon['book_cgst'] = $gstr['net_itc_cgst'];
            $recon['book_sgst'] = $gstr['net_itc_sgst'];
            $recon['book_igst'] = $gstr['net_itc_igst'];
            $recon['book_total'] = $recon['book_cgst'] + $recon['book_sgst'] + $recon['book_igst'];
            
            // GSTR-2B values would come from uploaded data - for now showing as same (matched)
            $recon['gstr_cgst'] = $recon['book_cgst'];
            $recon['gstr_sgst'] = $recon['book_sgst'];
            $recon['gstr_igst'] = $recon['book_igst'];
            $recon['gstr_total'] = $recon['gstr_cgst'] + $recon['gstr_sgst'] + $recon['gstr_igst'];
            
            // Differences
            $recon['diff_cgst'] = $recon['book_cgst'] - $recon['gstr_cgst'];
            $recon['diff_sgst'] = $recon['book_sgst'] - $recon['gstr_sgst'];
            $recon['diff_igst'] = $recon['book_igst'] - $recon['gstr_igst'];
            $recon['diff_total'] = $recon['diff_cgst'] + $recon['diff_sgst'] + $recon['diff_igst'];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportGstSetOffGstrToExcel($gstr, $recon, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.gst-set-off.gst-set-off-gstr-print', compact(
                'dateFrom', 'dateTo', 'showDnCn', 'showBrExp', 'withoutHsn', 'gstr', 'recon'
            ));
        }
        
        return view('admin.reports.purchase-report.gst-set-off.gst-set-off-gstr', compact(
            'dateFrom', 'dateTo', 'showDnCn', 'showBrExp', 'withoutHsn', 'gstr', 'recon'
        ));
    }

    /**
     * Export GST Set Off GSTR to Excel
     */
    private function exportGstSetOffGstrToExcel($gstr, $recon, $dateFrom, $dateTo)
    {
        $filename = 'gst_set_off_gstr_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($gstr, $recon) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['GSTR-3B Summary']);
            fputcsv($file, ['Description', 'IGST', 'CGST', 'SGST', 'Cess']);
            fputcsv($file, ['3.1(a) Outward taxable supplies', $gstr['outward_igst'], $gstr['outward_cgst'], $gstr['outward_sgst'], $gstr['outward_cess']]);
            fputcsv($file, ['4(A) ITC Available', $gstr['itc_igst'], $gstr['itc_cgst'], $gstr['itc_sgst'], $gstr['itc_cess']]);
            fputcsv($file, ['4(B) ITC Reversed', $gstr['reversed_igst'], $gstr['reversed_cgst'], $gstr['reversed_sgst'], $gstr['reversed_cess']]);
            fputcsv($file, ['4(C) Net ITC Available', $gstr['net_itc_igst'], $gstr['net_itc_cgst'], $gstr['net_itc_sgst'], $gstr['net_itc_cess']]);
            fputcsv($file, ['6. Tax Payable', $gstr['payable_igst'], $gstr['payable_cgst'], $gstr['payable_sgst'], $gstr['payable_cess']]);
            fputcsv($file, ['Paid through ITC', $gstr['paid_itc_igst'], $gstr['paid_itc_cgst'], $gstr['paid_itc_sgst'], $gstr['paid_itc_cess']]);
            fputcsv($file, ['Tax paid in Cash', $gstr['cash_igst'], $gstr['cash_cgst'], $gstr['cash_sgst'], $gstr['cash_cess']]);
            fputcsv($file, []);
            fputcsv($file, ['ITC Reconciliation']);
            fputcsv($file, ['Particulars', 'As Per Books', 'As Per GSTR-2B', 'Difference']);
            fputcsv($file, ['IGST', $recon['book_igst'], $recon['gstr_igst'], $recon['diff_igst']]);
            fputcsv($file, ['CGST', $recon['book_cgst'], $recon['gstr_cgst'], $recon['diff_cgst']]);
            fputcsv($file, ['SGST', $recon['book_sgst'], $recon['gstr_sgst'], $recon['diff_sgst']]);
            fputcsv($file, ['Total', $recon['book_total'], $recon['gstr_total'], $recon['diff_total']]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
        $selectiveAll = $request->get('selective_all', 'A'); // S=Selective (specific supplier), A=All
        $purchaseReplacement = $request->get('purchase_replacement', 'P'); // P=Purchase, R=Replacement
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $purchases = collect();
        $totals = [
            'bills' => 0,
            'items' => 0,
            'quantity' => 0,
            'free_qty' => 0,
            'amount' => 0,
            'tax_amount' => 0,
            'net_amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            if ($purchaseReplacement === 'P') {
                // Purchase Transactions with Item Details
                $query = PurchaseTransactionItem::with([
                    'transaction:id,bill_no,bill_date,supplier_id',
                    'transaction.supplier:supplier_id,name,code'
                ])
                ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId, $selectiveAll) {
                    $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                    
                    // If Selective mode and supplier selected
                    if ($selectiveAll === 'S' && $supplierId) {
                        $q->where('supplier_id', $supplierId);
                    }
                });

                $query->orderBy('item_name');

                $results = $query->get();

                // Map to purchase records
                $purchases = $results->map(function($item) {
                    $gstPercent = ($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0);
                    return (object)[
                        'bill_date' => $item->transaction->bill_date ?? null,
                        'bill_no' => $item->transaction->bill_no ?? '-',
                        'supplier' => $item->transaction->supplier ?? null,
                        'item_name' => $item->item_name ?? '-',
                        'packing' => $item->packing ?? '-',
                        'batch_no' => $item->batch_no ?? '-',
                        'expiry_date' => $item->expiry_date ? $item->expiry_date->format('m/Y') : '-',
                        'quantity' => $item->qty ?? 0,
                        'free_qty' => $item->free_qty ?? 0,
                        'rate' => $item->pur_rate ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'discount_percent' => $item->dis_percent ?? 0,
                        'gst_percent' => $gstPercent,
                        'amount' => $item->amount ?? 0,
                        'tax_amount' => $item->tax_amount ?? 0,
                        'net_amount' => $item->net_amount ?? 0
                    ];
                });
            } else {
                // Replacement Received Transactions
                $query = \App\Models\ReplacementReceivedTransactionItem::with([
                    'transaction:id,replacement_no,replacement_date,supplier_id',
                    'transaction.supplier:supplier_id,name,code'
                ])
                ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId, $selectiveAll) {
                    $q->whereBetween('replacement_date', [$dateFrom, $dateTo]);
                    
                    if ($selectiveAll === 'S' && $supplierId) {
                        $q->where('supplier_id', $supplierId);
                    }
                });

                $query->orderBy('item_name');

                $results = $query->get();

                // Map to purchase records
                $purchases = $results->map(function($item) {
                    $gstPercent = ($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0);
                    return (object)[
                        'bill_date' => $item->transaction->replacement_date ?? null,
                        'bill_no' => $item->transaction->replacement_no ?? '-',
                        'supplier' => $item->transaction->supplier ?? null,
                        'item_name' => $item->item_name ?? '-',
                        'packing' => $item->packing ?? '-',
                        'batch_no' => $item->batch_no ?? '-',
                        'expiry_date' => $item->expiry_date ? $item->expiry_date->format('m/Y') : '-',
                        'quantity' => $item->qty ?? 0,
                        'free_qty' => $item->free_qty ?? 0,
                        'rate' => $item->pur_rate ?? $item->rate ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'discount_percent' => $item->dis_percent ?? 0,
                        'gst_percent' => $gstPercent,
                        'amount' => $item->amount ?? 0,
                        'tax_amount' => $item->tax_amount ?? 0,
                        'net_amount' => $item->net_amount ?? 0
                    ];
                });
            }

            // Get unique bill count
            $uniqueBills = $purchases->pluck('bill_no')->unique()->count();

            // Calculate totals
            $totals = [
                'bills' => $uniqueBills,
                'items' => $purchases->count(),
                'quantity' => $purchases->sum('quantity'),
                'free_qty' => $purchases->sum('free_qty'),
                'amount' => $purchases->sum('amount'),
                'tax_amount' => $purchases->sum('tax_amount'),
                'net_amount' => $purchases->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseWithItemDetailsToExcel($purchases, $totals, $dateFrom, $dateTo, $purchaseReplacement);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.purchase-with-item-details-print', compact(
                'dateFrom', 'dateTo', 'purchases', 'totals', 'suppliers',
                'selectiveAll', 'purchaseReplacement', 'supplierId', 'supplierCode'
            ));
        }
        
        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.purchase-with-item-details', compact(
            'dateFrom', 'dateTo', 'purchases', 'totals', 'suppliers',
            'selectiveAll', 'purchaseReplacement', 'supplierId', 'supplierCode'
        ));
    }

    /**
     * Export Purchase with Item Details to Excel
     */
    private function exportPurchaseWithItemDetailsToExcel($purchases, $totals, $dateFrom, $dateTo, $purchaseReplacement)
    {
        $type = $purchaseReplacement === 'P' ? 'purchase' : 'replacement';
        $filename = $type . '_with_item_details_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($purchases, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Bill No', 'Supplier', 'Item Name', 'Packing', 'Batch', 'Expiry', 'Qty', 'Free', 'Rate', 'MRP', 'Disc%', 'GST%', 'Amount', 'Tax', 'Net Amount']);

            $sno = 1;
            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $sno++,
                    $purchase->bill_date ? $purchase->bill_date->format('d-m-Y') : '-',
                    $purchase->bill_no,
                    $purchase->supplier->name ?? 'N/A',
                    $purchase->item_name,
                    $purchase->packing,
                    $purchase->batch_no,
                    $purchase->expiry_date,
                    number_format($purchase->quantity, 0),
                    number_format($purchase->free_qty, 0),
                    number_format($purchase->rate, 2),
                    number_format($purchase->mrp, 2),
                    number_format($purchase->discount_percent, 2),
                    number_format($purchase->gst_percent, 2),
                    number_format($purchase->amount, 2),
                    number_format($purchase->tax_amount, 2),
                    number_format($purchase->net_amount, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '', '', '', '',
                number_format($totals['quantity'], 0),
                number_format($totals['free_qty'], 0),
                '', '', '', '',
                number_format($totals['amount'], 2),
                number_format($totals['tax_amount'], 2),
                number_format($totals['net_amount'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // --- Supplier Wise Purchase Submodule Methods ---

    /**
     * All Supplier Purchase Summary
     */
    public function supplierAllSupplier(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1'); // 1=Purchase, 2=Return, 3=DN, 4=CN, 5=Consolidated
        $taggedParties = $request->get('tagged_parties', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $orderBy = $request->get('order_by', 'N'); // N=Name, V=Value
        $sortOrder = $request->get('sort_order', 'A'); // A=Ascending, D=Descending
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $suppliers = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            // Build query based on report type
            if ($reportType == '1') {
                // Purchase
                $suppliers = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                    ->select(
                        'supplier_id',
                        DB::raw('COUNT(*) as total_bills'),
                        DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                        DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                        DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                    )
                    ->groupBy('supplier_id')
                    ->with('supplier:supplier_id,name,mobile,address')
                    ->get();
            } elseif ($reportType == '2') {
                // Purchase Return - uses return_date
                $suppliers = PurchaseReturnTransaction::whereBetween('return_date', [$dateFrom, $dateTo])
                    ->select(
                        'supplier_id',
                        DB::raw('COUNT(*) as total_bills'),
                        DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                        DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                        DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                    )
                    ->groupBy('supplier_id')
                    ->with('supplier:supplier_id,name,mobile,address')
                    ->get();
            } elseif ($reportType == '3') {
                // Debit Note - uses debit_note_date and debit_party_id for supplier
                $suppliers = DebitNote::whereBetween('debit_note_date', [$dateFrom, $dateTo])
                    ->where('debit_party_type', 'S') // Only supplier debit notes
                    ->select(
                        'debit_party_id as supplier_id',
                        DB::raw('COUNT(*) as total_bills'),
                        DB::raw('SUM(COALESCE(gross_amount, 0)) as total_amount'),
                        DB::raw('SUM(COALESCE(total_gst, 0)) as tax_amount'),
                        DB::raw('SUM(COALESCE(dn_amount, 0)) as net_payable')
                    )
                    ->groupBy('debit_party_id')
                    ->get()
                    ->map(function($item) {
                        $item->supplier = Supplier::select('supplier_id', 'name', 'mobile', 'address')
                            ->where('supplier_id', $item->supplier_id)->first();
                        return $item;
                    });
            } elseif ($reportType == '4') {
                // Credit Note - uses credit_note_date and credit_party_id for supplier
                $suppliers = CreditNote::whereBetween('credit_note_date', [$dateFrom, $dateTo])
                    ->where('credit_party_type', 'S') // Only supplier credit notes
                    ->select(
                        'credit_party_id as supplier_id',
                        DB::raw('COUNT(*) as total_bills'),
                        DB::raw('SUM(COALESCE(gross_amount, 0)) as total_amount'),
                        DB::raw('SUM(COALESCE(total_gst, 0)) as tax_amount'),
                        DB::raw('SUM(COALESCE(cn_amount, 0)) as net_payable')
                    )
                    ->groupBy('credit_party_id')
                    ->get()
                    ->map(function($item) {
                        $item->supplier = Supplier::select('supplier_id', 'name', 'mobile', 'address')
                            ->where('supplier_id', $item->supplier_id)->first();
                        return $item;
                    });
            } else {
                // Consolidated Purchase Book (all types combined)
                $suppliers = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                    ->select(
                        'supplier_id',
                        DB::raw('COUNT(*) as total_bills'),
                        DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                        DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                        DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                    )
                    ->groupBy('supplier_id')
                    ->with('supplier:supplier_id,name,mobile,address')
                    ->get();
            }

            // Sort results
            if ($orderBy == 'V') {
                $suppliers = $sortOrder == 'D' 
                    ? $suppliers->sortByDesc('net_payable')->values()
                    : $suppliers->sortBy('net_payable')->values();
            } else {
                $suppliers = $sortOrder == 'D'
                    ? $suppliers->sortByDesc(fn($s) => $s->supplier->name ?? '')->values()
                    : $suppliers->sortBy(fn($s) => $s->supplier->name ?? '')->values();
            }
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.all-supplier', compact(
            'suppliers', 'dateFrom', 'dateTo', 'reportType', 'taggedParties', 'removeTags', 'orderBy', 'sortOrder', 'withBrExpiry'
        ));
    }

    /**
     * All Supplier Purchase Summary - Print View
     */
    public function supplierAllSupplierPrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1');
        $orderBy = $request->get('order_by', 'N');
        $sortOrder = $request->get('sort_order', 'A');

        $suppliers = collect();

        // Build query based on report type
        if ($reportType == '1') {
            $suppliers = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->select(
                    'supplier_id',
                    DB::raw('COUNT(*) as total_bills'),
                    DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                )
                ->groupBy('supplier_id')
                ->with('supplier:supplier_id,name,mobile,address')
                ->get();
        } elseif ($reportType == '2') {
            $suppliers = PurchaseReturnTransaction::whereBetween('return_date', [$dateFrom, $dateTo])
                ->select(
                    'supplier_id',
                    DB::raw('COUNT(*) as total_bills'),
                    DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                )
                ->groupBy('supplier_id')
                ->with('supplier:supplier_id,name,mobile,address')
                ->get();
        } elseif ($reportType == '3') {
            $suppliers = DebitNote::whereBetween('debit_note_date', [$dateFrom, $dateTo])
                ->where('debit_party_type', 'S')
                ->select(
                    'debit_party_id as supplier_id',
                    DB::raw('COUNT(*) as total_bills'),
                    DB::raw('SUM(COALESCE(gross_amount, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(total_gst, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(dn_amount, 0)) as net_payable')
                )
                ->groupBy('debit_party_id')
                ->get()
                ->map(function($item) {
                    $item->supplier = Supplier::select('supplier_id', 'name', 'mobile', 'address')
                        ->where('supplier_id', $item->supplier_id)->first();
                    return $item;
                });
        } elseif ($reportType == '4') {
            $suppliers = CreditNote::whereBetween('credit_note_date', [$dateFrom, $dateTo])
                ->where('credit_party_type', 'S')
                ->select(
                    'credit_party_id as supplier_id',
                    DB::raw('COUNT(*) as total_bills'),
                    DB::raw('SUM(COALESCE(gross_amount, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(total_gst, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(cn_amount, 0)) as net_payable')
                )
                ->groupBy('credit_party_id')
                ->get()
                ->map(function($item) {
                    $item->supplier = Supplier::select('supplier_id', 'name', 'mobile', 'address')
                        ->where('supplier_id', $item->supplier_id)->first();
                    return $item;
                });
        } else {
            $suppliers = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->select(
                    'supplier_id',
                    DB::raw('COUNT(*) as total_bills'),
                    DB::raw('SUM(COALESCE(nt_amount, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(net_amount, 0)) as net_payable')
                )
                ->groupBy('supplier_id')
                ->with('supplier:supplier_id,name,mobile,address')
                ->get();
        }

        // Sort results
        if ($orderBy == 'V') {
            $suppliers = $sortOrder == 'D' 
                ? $suppliers->sortByDesc('net_payable')->values()
                : $suppliers->sortBy('net_payable')->values();
        } else {
            $suppliers = $sortOrder == 'D'
                ? $suppliers->sortByDesc(fn($s) => $s->supplier->name ?? '')->values()
                : $suppliers->sortBy(fn($s) => $s->supplier->name ?? '')->values();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.all-supplier-print', compact(
            'suppliers', 'dateFrom', 'dateTo', 'reportType'
        ));
    }

    /**
     * Supplier Bill Wise Purchase
     */
    public function supplierBillWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $taggedParties = $request->get('tagged_parties', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $withBrExpiry = $request->get('with_br_expiry', 'Y');
        $amountType = $request->get('amount_type', '1'); // 1=Taxable, 2=Bill Amount

        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $bills = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->with('supplier:supplier_id,name,code,mobile,address');

            // Filter by supplier
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            $bills = $query->orderBy('bill_date', 'desc')->orderBy('bill_no')->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.bill-wise', compact(
            'bills', 'suppliers', 'dateFrom', 'dateTo', 'supplierId', 'supplierCode', 
            'taggedParties', 'removeTags', 'withBrExpiry', 'amountType'
        ));
    }

    /**
     * Supplier Bill Wise Purchase - Print View
     */
    public function supplierBillWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->with('supplier:supplier_id,name,code,mobile,address');

        $supplierName = null;
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
            $supplier = Supplier::find($supplierId);
            $supplierName = $supplier ? $supplier->name : null;
        }

        $bills = $query->orderBy('bill_date', 'desc')->orderBy('bill_no')->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.bill-wise-print', compact(
            'bills', 'dateFrom', 'dateTo', 'supplierName'
        ));
    }

    /**
     * Supplier Item Wise Purchase
     */
    public function supplierItemWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $groupBy = $request->get('group_by', 'C'); // C=Company, I=Item
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');
        $divisionId = $request->get('division_id');
        $divisionCode = $request->get('division_code');
        $categoryId = $request->get('category_id');
        $categoryCode = $request->get('category_code');

        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();
        $items = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            });

            // Group by item and aggregate
            $items = $query->select(
                    'item_id',
                    'item_name',
                    'company_name',
                    DB::raw('SUM(qty) as total_qty'),
                    DB::raw('SUM(free_qty) as total_free_qty'),
                    DB::raw('AVG(pur_rate) as avg_rate'),
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('SUM(tax_amount) as total_tax'),
                    DB::raw('SUM(net_amount) as total_net')
                )
                ->groupBy('item_id', 'item_name', 'company_name')
                ->orderBy('item_name')
                ->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-wise', compact(
            'items', 'suppliers', 'companies', 'categories', 'dateFrom', 'dateTo', 'reportType',
            'supplierId', 'supplierCode', 'groupBy', 'taggedCompanies', 'removeTags',
            'companyId', 'companyCode', 'divisionId', 'divisionCode', 'categoryId', 'categoryCode'
        ));
    }

    /**
     * Supplier Item Wise Purchase - Print View
     */
    public function supplierItemWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            if ($supplierId) {
                $q->where('supplier_id', $supplierId);
            }
        });

        $supplierName = null;
        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            $supplierName = $supplier ? $supplier->name : null;
        }

        $items = $query->select(
                'item_id',
                'item_name',
                'company_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(free_qty) as total_free_qty'),
                DB::raw('AVG(pur_rate) as avg_rate'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(net_amount) as total_net')
            )
            ->groupBy('item_id', 'item_name', 'company_name')
            ->orderBy('item_name')
            ->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-wise-print', compact(
            'items', 'dateFrom', 'dateTo', 'supplierName'
        ));
    }

    /**
     * Supplier Item - Invoice Wise Purchase
     */
    public function supplierItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $groupBy = $request->get('group_by', 'C'); // C=Company, I=Item
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');
        $divisionId = $request->get('division_id');
        $divisionCode = $request->get('division_code');
        $categoryId = $request->get('category_id');
        $categoryCode = $request->get('category_code');

        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();
        $items = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            // Get items with their invoice details - grouped by item first, then showing invoices
            $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            })
            ->with(['transaction' => function($q) {
                $q->select('id', 'bill_date', 'bill_no', 'supplier_id')
                  ->with('supplier:supplier_id,name');
            }]);

            $items = $query->orderBy('item_name')->orderBy('purchase_transaction_id')->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-invoice-wise', compact(
            'items', 'suppliers', 'companies', 'categories', 'dateFrom', 'dateTo', 'reportType',
            'supplierId', 'supplierCode', 'groupBy', 'taggedCompanies', 'removeTags',
            'companyId', 'companyCode', 'divisionId', 'divisionCode', 'categoryId', 'categoryCode'
        ));
    }

    /**
     * Supplier Item - Invoice Wise Purchase - Print View
     */
    public function supplierItemInvoiceWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            if ($supplierId) {
                $q->where('supplier_id', $supplierId);
            }
        })
        ->with(['transaction' => function($q) {
            $q->select('id', 'bill_date', 'bill_no', 'supplier_id')
              ->with('supplier:supplier_id,name');
        }]);

        $supplierName = null;
        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            $supplierName = $supplier ? $supplier->name : null;
        }

        $items = $query->orderBy('item_name')->orderBy('purchase_transaction_id')->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.item-invoice-wise-print', compact(
            'items', 'dateFrom', 'dateTo', 'supplierName'
        ));
    }

    /**
     * Supplier Invoice - Item Wise Purchase
     * Shows invoices first, then items under each invoice
     */
    public function supplierInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $supplierId = $request->get('supplier_id');
        $supplierCode = $request->get('supplier_code');
        $groupBy = $request->get('group_by', 'C'); // C=Company, I=Item
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');
        $divisionId = $request->get('division_id');
        $divisionCode = $request->get('division_code');
        $categoryId = $request->get('category_id');
        $categoryCode = $request->get('category_code');

        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();
        $invoices = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            // Get invoices with their items - grouped by invoice first
            $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->with(['supplier:supplier_id,name', 'items' => function($q) {
                    $q->orderBy('item_name');
                }]);

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            $invoices = $query->orderBy('bill_date', 'desc')->orderBy('bill_no')->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.invoice-item-wise', compact(
            'invoices', 'suppliers', 'companies', 'categories', 'dateFrom', 'dateTo', 'reportType',
            'supplierId', 'supplierCode', 'groupBy', 'taggedCompanies', 'removeTags',
            'companyId', 'companyCode', 'divisionId', 'divisionCode', 'categoryId', 'categoryCode'
        ));
    }

    /**
     * Supplier Invoice - Item Wise Purchase - Print View
     */
    public function supplierInvoiceItemWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->with(['supplier:supplier_id,name', 'items' => function($q) {
                $q->orderBy('item_name');
            }]);

        $supplierName = null;
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
            $supplier = Supplier::find($supplierId);
            $supplierName = $supplier ? $supplier->name : null;
        }

        $invoices = $query->orderBy('bill_date', 'desc')->orderBy('bill_no')->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.supplier-wise-purchase.invoice-item-wise-print', compact(
            'invoices', 'dateFrom', 'dateTo', 'supplierName'
        ));
    }

    // --- Company Wise Purchase Submodule Methods ---

    /**
     * All Company Purchase Summary
     */
    public function companyAllCompany(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1'); // 1=Purchase, 2=Return, 3=Both

        $companies = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            // Get company-wise purchase summary from transaction items
            $companies = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            })
            ->select(
                'company_name',
                DB::raw('COUNT(DISTINCT item_id) as total_items'),
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(free_qty) as total_free_qty'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(net_amount) as total_net')
            )
            ->groupBy('company_name')
            ->orderBy('company_name')
            ->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.all-company', compact(
            'companies', 'dateFrom', 'dateTo', 'reportType'
        ));
    }

    /**
     * All Company Purchase Summary - Print View
     */
    public function companyAllCompanyPrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $companies = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
        })
        ->select(
            'company_name',
            DB::raw('COUNT(DISTINCT item_id) as total_items'),
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free_qty'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(net_amount) as total_net')
        )
        ->groupBy('company_name')
        ->orderBy('company_name')
        ->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.all-company-print', compact(
            'companies', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Company Item Wise Purchase
     */
    public function companyItemWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');

        $companyList = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $items = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            });

            if ($companyId) {
                $company = Company::find($companyId);
                if ($company) {
                    $query->where('company_name', $company->name);
                }
            }

            $items = $query->select(
                'company_name',
                'item_id',
                'item_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(free_qty) as total_free_qty'),
                DB::raw('AVG(pur_rate) as avg_rate'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(net_amount) as total_net')
            )
            ->groupBy('company_name', 'item_id', 'item_name')
            ->orderBy('company_name')
            ->orderBy('item_name')
            ->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.item-wise', compact(
            'items', 'companyList', 'dateFrom', 'dateTo', 'companyId', 'companyCode'
        ));
    }

    /**
     * Company Item Wise Purchase - Print View
     */
    public function companyItemWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
        });

        $companyName = null;
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
                $companyName = $company->name;
            }
        }

        $items = $query->select(
            'company_name',
            'item_id',
            'item_name',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free_qty'),
            DB::raw('AVG(pur_rate) as avg_rate'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(net_amount) as total_net')
        )
        ->groupBy('company_name', 'item_id', 'item_name')
        ->orderBy('company_name')
        ->orderBy('item_name')
        ->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.item-wise-print', compact(
            'items', 'dateFrom', 'dateTo', 'companyName'
        ));
    }

    /**
     * Company Party Wise Purchase
     */
    public function companyPartyWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');

        $companyList = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $parties = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            // Get company-wise party (supplier) purchase summary
            $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
                ->with('supplier:supplier_id,name')
                ->join('purchase_transaction_items', 'purchase_transactions.id', '=', 'purchase_transaction_items.purchase_transaction_id');

            if ($companyId) {
                $company = Company::find($companyId);
                if ($company) {
                    $query->where('purchase_transaction_items.company_name', $company->name);
                }
            }

            $parties = $query->select(
                'purchase_transaction_items.company_name',
                'purchase_transactions.supplier_id',
                DB::raw('COUNT(DISTINCT purchase_transactions.id) as total_bills'),
                DB::raw('SUM(purchase_transaction_items.qty) as total_qty'),
                DB::raw('SUM(purchase_transaction_items.amount) as total_amount'),
                DB::raw('SUM(purchase_transaction_items.net_amount) as total_net')
            )
            ->groupBy('purchase_transaction_items.company_name', 'purchase_transactions.supplier_id')
            ->orderBy('purchase_transaction_items.company_name')
            ->get()
            ->map(function($item) {
                $item->supplier = Supplier::select('supplier_id', 'name')->where('supplier_id', $item->supplier_id)->first();
                return $item;
            });
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.party-wise', compact(
            'parties', 'companyList', 'dateFrom', 'dateTo', 'companyId', 'companyCode'
        ));
    }

    /**
     * Company Party Wise Purchase - Print View
     */
    public function companyPartyWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        $query = PurchaseTransaction::whereBetween('bill_date', [$dateFrom, $dateTo])
            ->join('purchase_transaction_items', 'purchase_transactions.id', '=', 'purchase_transaction_items.purchase_transaction_id');

        $companyName = null;
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('purchase_transaction_items.company_name', $company->name);
                $companyName = $company->name;
            }
        }

        $parties = $query->select(
            'purchase_transaction_items.company_name',
            'purchase_transactions.supplier_id',
            DB::raw('COUNT(DISTINCT purchase_transactions.id) as total_bills'),
            DB::raw('SUM(purchase_transaction_items.qty) as total_qty'),
            DB::raw('SUM(purchase_transaction_items.amount) as total_amount'),
            DB::raw('SUM(purchase_transaction_items.net_amount) as total_net')
        )
        ->groupBy('purchase_transaction_items.company_name', 'purchase_transactions.supplier_id')
        ->orderBy('purchase_transaction_items.company_name')
        ->get()
        ->map(function($item) {
            $item->supplier = Supplier::select('supplier_id', 'name')->where('supplier_id', $item->supplier_id)->first();
            return $item;
        });

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.company-wise-purchase.party-wise-print', compact(
            'parties', 'dateFrom', 'dateTo', 'companyName'
        ));
    }

    // --- Item Wise Purchase Submodule Methods ---

    /**
     * Item Bill Wise Purchase (Item Wise Purchase - second screenshot)
     */
    public function itemBillWise(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $selectiveItem = $request->get('selective_item', 'Y');
        $taggedItems = $request->get('tagged_items', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $itemId = $request->get('item_id');
        $divisionCode = $request->get('division_code');
        $batchNo = $request->get('batch_no');
        $partyCode = $request->get('party_code');
        $supplierId = $request->get('supplier_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $withAddress = $request->get('with_address', 'N');
        $withValue = $request->get('with_value', 'N');
        $categoryId = $request->get('category_id');

        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();
        $items = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            })
            ->with(['transaction' => function($q) {
                $q->select('id', 'bill_date', 'bill_no', 'supplier_id')
                  ->with('supplier:supplier_id,name');
            }]);

            if ($batchNo) {
                $query->where('batch_no', 'like', "%{$batchNo}%");
            }

            $items = $query->orderBy('item_name')->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.bill-wise', compact(
            'items', 'suppliers', 'categories', 'dateFrom', 'dateTo', 'reportType',
            'selectiveItem', 'taggedItems', 'removeTags', 'itemId', 'divisionCode',
            'batchNo', 'partyCode', 'supplierId', 'withBrExpiry', 'withAddress', 'withValue', 'categoryId'
        ));
    }

    /**
     * Item Bill Wise Purchase - Print View
     */
    public function itemBillWisePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $batchNo = $request->get('batch_no');

        $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            if ($supplierId) {
                $q->where('supplier_id', $supplierId);
            }
        })
        ->with(['transaction' => function($q) {
            $q->select('id', 'bill_date', 'bill_no', 'supplier_id')
              ->with('supplier:supplier_id,name');
        }]);

        if ($batchNo) {
            $query->where('batch_no', 'like', "%{$batchNo}%");
        }

        $items = $query->orderBy('item_name')->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.bill-wise-print', compact(
            'items', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * All Item Purchase Summary (first screenshot)
     */
    public function itemAllItemPurchase(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3'); // 1=Purchase, 2=Return, 3=Both
        $billNoFrom = $request->get('bill_no_from', 0);
        $billNoTo = $request->get('bill_no_to', 0);
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $companyId = $request->get('company_id');
        $companyCode = $request->get('company_code');
        $divisionCode = $request->get('division_code');
        $itemId = $request->get('item_id');
        $itemCode = $request->get('item_code');
        $taggedCategories = $request->get('tagged_categories', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $categoryId = $request->get('category_id');
        $categoryCode = $request->get('category_code');
        $rangeYN = $request->get('range_yn', 'N');
        $valueFrom = $request->get('value_from', -999999999);
        $valueTo = $request->get('value_to', 999999999);
        $orderBy = $request->get('order_by', 'V'); // Q=Qty, V=Value
        $sortOrder = $request->get('sort_order', 'D'); // A=Asc, D=Desc
        $topItems = $request->get('top_items', 0);
        $batchWise = $request->get('batch_wise', 'N');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $withReturnDet = $request->get('with_return_det', 'N');

        $companies = Company::select('id', 'name', 'short_name')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();
        $items = collect();

        if ($request->has('from_date') || $request->has('to_date')) {
            $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
            });

            if ($companyId) {
                $company = Company::find($companyId);
                if ($company) {
                    $query->where('company_name', $company->name);
                }
            }

            $items = $query->select(
                'item_id',
                'item_name',
                'company_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(free_qty) as total_free_qty'),
                DB::raw('AVG(pur_rate) as avg_rate'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(net_amount) as total_net')
            )
            ->groupBy('item_id', 'item_name', 'company_name');

            // Apply sorting
            if ($orderBy == 'Q') {
                $items = $items->orderBy('total_qty', $sortOrder == 'D' ? 'desc' : 'asc');
            } else {
                $items = $items->orderBy('total_net', $sortOrder == 'D' ? 'desc' : 'asc');
            }

            // Apply top items limit
            if ($topItems > 0) {
                $items = $items->limit($topItems);
            }

            $items = $items->get();
        }

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.all-item-purchase', compact(
            'items', 'companies', 'categories', 'dateFrom', 'dateTo', 'reportType',
            'billNoFrom', 'billNoTo', 'taggedCompanies', 'companyId', 'companyCode',
            'divisionCode', 'itemId', 'itemCode', 'taggedCategories', 'removeTags',
            'categoryId', 'categoryCode', 'rangeYN', 'valueFrom', 'valueTo',
            'orderBy', 'sortOrder', 'topItems', 'batchWise', 'withBrExpiry', 'withReturnDet'
        ));
    }

    /**
     * All Item Purchase Summary - Print View
     */
    public function itemAllItemPurchasePrint(Request $request)
    {
        $dateFrom = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $orderBy = $request->get('order_by', 'V');
        $sortOrder = $request->get('sort_order', 'D');
        $topItems = $request->get('top_items', 0);

        $query = PurchaseTransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
        });

        $companyName = null;
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
                $companyName = $company->name;
            }
        }

        $items = $query->select(
            'item_id',
            'item_name',
            'company_name',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free_qty'),
            DB::raw('AVG(pur_rate) as avg_rate'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(net_amount) as total_net')
        )
        ->groupBy('item_id', 'item_name', 'company_name');

        if ($orderBy == 'Q') {
            $items = $items->orderBy('total_qty', $sortOrder == 'D' ? 'desc' : 'asc');
        } else {
            $items = $items->orderBy('total_net', $sortOrder == 'D' ? 'desc' : 'asc');
        }

        if ($topItems > 0) {
            $items = $items->limit($topItems);
        }

        $items = $items->get();

        return view('admin.reports.purchase-report.miscellaneous-purchase-analysis.item-wise-purchase.all-item-purchase-print', compact(
            'items', 'dateFrom', 'dateTo', 'companyName'
        ));
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
        $purchaseTransfer = $request->get('purchase_transfer', 'P'); // P=Purchase, T=Transfer, B=Both
        $taggedPartiesOnly = $request->get('tagged_parties_only', 'N'); // Y/N
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        $replacementReceived = $request->get('replacement_received', 'N'); // Y/N
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $items = collect();
        $totals = [
            'count' => 0,
            'qty' => 0,
            'free_qty' => 0,
            'amount' => 0,
            'tax' => 0,
            'net_amount' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransactionItem::with([
                'transaction:id,bill_no,bill_date,supplier_id,voucher_type',
                'transaction.supplier:supplier_id,name,code'
            ])
            ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId, $purchaseTransfer) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
                
                // Purchase/Transfer filter
                if ($purchaseTransfer === 'P') {
                    $q->where(function($sq) {
                        $sq->where('voucher_type', 'P')
                           ->orWhereNull('voucher_type')
                           ->orWhere('voucher_type', '');
                    });
                } elseif ($purchaseTransfer === 'T') {
                    $q->where('voucher_type', 'T');
                }
            });

            $query->orderBy('item_name');
            $items = $query->get();

            // Calculate totals
            $totals = [
                'count' => $items->count(),
                'qty' => $items->sum('qty'),
                'free_qty' => $items->sum('free_qty'),
                'amount' => $items->sum('amount'),
                'tax' => $items->sum('tax_amount'),
                'net_amount' => $items->sum('net_amount')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPurchaseBookItemDetailsToExcel($items, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.other-reports.purchase-book-item-details-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'purchaseTransfer',
                'taggedPartiesOnly', 'supplierCode', 'supplierId', 'replacementReceived'
            ));
        }
        
        return view('admin.reports.purchase-report.other-reports.purchase-book-item-details', compact(
            'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'purchaseTransfer',
            'taggedPartiesOnly', 'supplierCode', 'supplierId', 'replacementReceived'
        ));
    }

    /**
     * Export Purchase Book Item Details to Excel
     */
    private function exportPurchaseBookItemDetailsToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'purchase_book_item_details_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Supplier', 'Item Name', 'Pack', 'Batch', 'Expiry', 'MRP', 'Rate', 'Qty', 'Free', 'Amount', 'Tax', 'Net Amount']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->transaction->bill_date ? $item->transaction->bill_date->format('d-m-Y') : '-',
                    $item->transaction->bill_no ?? '-',
                    $item->transaction->supplier->name ?? '',
                    $item->item_name ?? '',
                    $item->packing ?? '-',
                    $item->batch_no ?? '-',
                    $item->expiry_date ? $item->expiry_date->format('M-Y') : '-',
                    number_format($item->mrp ?? 0, 2),
                    number_format($item->pur_rate ?? 0, 2),
                    number_format($item->qty ?? 0, 2),
                    number_format($item->free_qty ?? 0, 2),
                    number_format($item->amount ?? 0, 2),
                    number_format($item->tax_amount ?? 0, 2),
                    number_format($item->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', 'TOTAL', '', '',
                number_format($totals['qty'], 2),
                number_format($totals['free_qty'], 2),
                number_format($totals['amount'], 2),
                number_format($totals['tax'], 2),
                number_format($totals['net_amount'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Central Purchase with Local Value
     */
    public function centralPurchaseLocalValue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierCode = $request->get('supplier_code');
        $supplierId = $request->get('supplier_id');
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        $items = collect();
        $totals = [
            'count' => 0,
            'qty' => 0,
            'central_value' => 0,
            'local_value' => 0,
            'difference' => 0,
            'savings_percent' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Get all purchases and compare purchase rate vs sale rate (local value)
            $query = PurchaseTransactionItem::with([
                'transaction:id,bill_no,bill_date,supplier_id',
                'transaction.supplier:supplier_id,name,code,local_central_flag'
            ])
            ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            });

            $query->orderBy('item_name');
            $rawItems = $query->get();

            // Map items with local value calculation
            $items = $rawItems->map(function($item) {
                $centralRate = $item->pur_rate ?? 0;
                // Use sale rate (s_rate) as local value - this represents what we'd sell it for locally
                $localRate = $item->s_rate ?? $item->mrp ?? $centralRate;
                $qty = $item->qty ?? 0;
                $centralValue = $qty * $centralRate;
                $localValue = $qty * $localRate;
                
                return (object)[
                    'bill_date' => $item->transaction->bill_date ?? null,
                    'bill_no' => $item->transaction->bill_no ?? '-',
                    'supplier_name' => $item->transaction->supplier->name ?? 'N/A',
                    'supplier_code' => $item->transaction->supplier->code ?? '',
                    'item_name' => $item->item_name,
                    'qty' => $qty,
                    'central_rate' => $centralRate,
                    'local_rate' => $localRate,
                    'central_value' => $centralValue,
                    'local_value' => $localValue,
                    'difference' => $localValue - $centralValue
                ];
            });

            // Calculate totals
            $totalCentral = $items->sum('central_value');
            $totalLocal = $items->sum('local_value');
            $totalDiff = $totalLocal - $totalCentral;
            
            $totals = [
                'count' => $items->count(),
                'qty' => $items->sum('qty'),
                'central_value' => $totalCentral,
                'local_value' => $totalLocal,
                'difference' => $totalDiff,
                'savings_percent' => $totalLocal > 0 ? ($totalDiff / $totalLocal) * 100 : 0
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportCentralPurchaseLocalValueToExcel($items, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.other-reports.central-purchase-local-value-print', compact(
                'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'supplierCode', 'supplierId'
            ));
        }
        
        return view('admin.reports.purchase-report.other-reports.central-purchase-local-value', compact(
            'dateFrom', 'dateTo', 'suppliers', 'items', 'totals', 'supplierCode', 'supplierId'
        ));
    }

    /**
     * Export Central Purchase Local Value to Excel
     */
    private function exportCentralPurchaseLocalValueToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'central_purchase_local_value_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Supplier', 'Item Name', 'Qty', 'Central Rate', 'Local Rate', 'Central Value', 'Local Value', 'Difference']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->bill_date ? $item->bill_date->format('d-m-Y') : '-',
                    $item->bill_no ?? '-',
                    $item->supplier_name ?? '',
                    $item->item_name ?? '',
                    number_format($item->qty ?? 0, 2),
                    number_format($item->central_rate ?? 0, 2),
                    number_format($item->local_rate ?? 0, 2),
                    number_format($item->central_value ?? 0, 2),
                    number_format($item->local_value ?? 0, 2),
                    number_format($item->difference ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL',
                number_format($totals['qty'], 2), '', '',
                number_format($totals['central_value'], 2),
                number_format($totals['local_value'], 2),
                number_format($totals['difference'], 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Party Wise All Purchase Details
     */
    public function partyWiseAllPurchaseDetails(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'purchase_book'); // purchase_book, with_gst_details
        $withGst = $request->boolean('with_gst');
        
        $partyDetails = collect();
        $totals = [
            'count' => 0,
            'bills' => 0,
            'gross_amount' => 0,
            'discount' => 0,
            'tax_amount' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'igst' => 0,
            'net_amount' => 0,
            'returns' => 0,
            'net_purchase' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            // Get purchases grouped by supplier
            $purchases = PurchaseTransaction::with(['supplier:supplier_id,name,code,address'])
                ->whereBetween('bill_date', [$dateFrom, $dateTo])
                ->select(
                    'supplier_id',
                    DB::raw('COUNT(*) as bill_count'),
                    DB::raw('SUM(COALESCE(nt_amount, 0)) as gross_amount'),
                    DB::raw('SUM(COALESCE(dis_amount, 0)) as discount'),
                    DB::raw('SUM(COALESCE(tax_amount, 0)) as tax_amount'),
                    DB::raw('SUM(COALESCE(net_amount, 0)) as net_amount')
                )
                ->groupBy('supplier_id')
                ->get();

            // Get returns grouped by supplier
            $returns = PurchaseReturnTransaction::whereBetween('return_date', [$dateFrom, $dateTo])
                ->select(
                    'supplier_id',
                    DB::raw('SUM(COALESCE(net_amount, 0)) as return_amount')
                )
                ->groupBy('supplier_id')
                ->pluck('return_amount', 'supplier_id');

            // Map party details
            $partyDetails = $purchases->map(function($purchase) use ($returns) {
                $returnAmt = $returns[$purchase->supplier_id] ?? 0;
                $netPurchase = $purchase->net_amount - $returnAmt;
                
                // Split tax 50/50 for CGST/SGST approximation
                $halfTax = $purchase->tax_amount / 2;
                
                return (object)[
                    'supplier_id' => $purchase->supplier_id,
                    'supplier_code' => $purchase->supplier->code ?? '',
                    'supplier_name' => $purchase->supplier->name ?? 'N/A',
                    'city' => $purchase->supplier->address ?? '-',
                    'bill_count' => $purchase->bill_count,
                    'gross_amount' => $purchase->gross_amount,
                    'discount' => $purchase->discount,
                    'tax_amount' => $purchase->tax_amount,
                    'cgst' => $halfTax,
                    'sgst' => $halfTax,
                    'igst' => 0,
                    'net_amount' => $purchase->net_amount,
                    'returns' => $returnAmt,
                    'net_purchase' => $netPurchase
                ];
            })->sortBy('supplier_name')->values();

            // Calculate totals
            $totals = [
                'count' => $partyDetails->count(),
                'bills' => $partyDetails->sum('bill_count'),
                'gross_amount' => $partyDetails->sum('gross_amount'),
                'discount' => $partyDetails->sum('discount'),
                'tax_amount' => $partyDetails->sum('tax_amount'),
                'cgst' => $partyDetails->sum('cgst'),
                'sgst' => $partyDetails->sum('sgst'),
                'igst' => $partyDetails->sum('igst'),
                'net_amount' => $partyDetails->sum('net_amount'),
                'returns' => $partyDetails->sum('returns'),
                'net_purchase' => $partyDetails->sum('net_purchase')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportPartyWiseAllPurchaseDetailsToExcel($partyDetails, $totals, $dateFrom, $dateTo, $withGst);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.other-reports.party-wise-all-purchase-details-print', compact(
                'dateFrom', 'dateTo', 'partyDetails', 'totals', 'reportType', 'withGst'
            ));
        }
        
        return view('admin.reports.purchase-report.other-reports.party-wise-all-purchase-details', compact(
            'dateFrom', 'dateTo', 'partyDetails', 'totals', 'reportType', 'withGst'
        ));
    }

    /**
     * Export Party Wise All Purchase Details to Excel
     */
    private function exportPartyWiseAllPurchaseDetailsToExcel($partyDetails, $totals, $dateFrom, $dateTo, $withGst)
    {
        $filename = 'party_wise_all_purchase_details_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($partyDetails, $totals, $withGst) {
            $file = fopen('php://output', 'w');
            
            if ($withGst) {
                fputcsv($file, ['Code', 'Supplier Name', 'City', 'Bills', 'Gross Amt', 'Discount', 'CGST', 'SGST', 'IGST', 'Net Amount', 'Returns', 'Net Purchase']);
            } else {
                fputcsv($file, ['Code', 'Supplier Name', 'City', 'Bills', 'Gross Amt', 'Discount', 'Tax', 'Net Amount', 'Returns', 'Net Purchase']);
            }

            foreach ($partyDetails as $party) {
                if ($withGst) {
                    fputcsv($file, [
                        $party->supplier_code,
                        $party->supplier_name,
                        $party->city,
                        $party->bill_count,
                        number_format($party->gross_amount, 2),
                        number_format($party->discount, 2),
                        number_format($party->cgst, 2),
                        number_format($party->sgst, 2),
                        number_format($party->igst, 2),
                        number_format($party->net_amount, 2),
                        number_format($party->returns, 2),
                        number_format($party->net_purchase, 2)
                    ]);
                } else {
                    fputcsv($file, [
                        $party->supplier_code,
                        $party->supplier_name,
                        $party->city,
                        $party->bill_count,
                        number_format($party->gross_amount, 2),
                        number_format($party->discount, 2),
                        number_format($party->tax_amount, 2),
                        number_format($party->net_amount, 2),
                        number_format($party->returns, 2),
                        number_format($party->net_purchase, 2)
                    ]);
                }
            }

            fputcsv($file, []);
            if ($withGst) {
                fputcsv($file, ['', 'TOTAL', '', $totals['bills'],
                    number_format($totals['gross_amount'], 2),
                    number_format($totals['discount'], 2),
                    number_format($totals['cgst'], 2),
                    number_format($totals['sgst'], 2),
                    number_format($totals['igst'], 2),
                    number_format($totals['net_amount'], 2),
                    number_format($totals['returns'], 2),
                    number_format($totals['net_purchase'], 2)
                ]);
            } else {
                fputcsv($file, ['', 'TOTAL', '', $totals['bills'],
                    number_format($totals['gross_amount'], 2),
                    number_format($totals['discount'], 2),
                    number_format($totals['tax_amount'], 2),
                    number_format($totals['net_amount'], 2),
                    number_format($totals['returns'], 2),
                    number_format($totals['net_purchase'], 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Register of Schedule H1 Drugs
     */
    public function registerScheduleH1Drugs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $itemId = $request->get('item_id');
        $itemCode = $request->get('item_code');
        $itemType = $request->get('item_type', 'H1'); // H1, H, G, X, etc.
        $supplierFlag = $request->get('supplier_flag', '5'); // 1-5, 5=ALL
        $itemStatus = $request->get('item_status', 'A'); // A=Active, D=Discontinued, B=Both
        
        $suppliers = Supplier::select('supplier_id', 'name', 'code')->orderBy('name')->get();
        
        // Fetch items - get all items for dropdown (schedule filter applied in query)
        $items = \App\Models\Item::select('id', 'name', 'packing', 'schedule')
            ->where(function($q) {
                $q->where('is_deleted', 0)
                  ->orWhereNull('is_deleted');
            })
            ->orderBy('name')
            ->limit(1000)
            ->get();
        
        $drugs = collect();
        $totals = [
            'count' => 0,
            'total_qty' => 0
        ];

        // Only run query if form was submitted
        $formSubmitted = $request->has('date_from');

        if ($formSubmitted) {
            $query = PurchaseTransactionItem::with([
                'transaction:id,bill_no,bill_date,supplier_id',
                'transaction.supplier:supplier_id,name,code,dl_no,dl_no_1',
                'item:id,name,packing,schedule,company_short_name,mfg_by,status'
            ])
            ->whereHas('transaction', function($q) use ($dateFrom, $dateTo, $supplierId) {
                $q->whereBetween('bill_date', [$dateFrom, $dateTo]);
                if ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                }
            });

            // Filter by item type (schedule) - only apply if not ALL and not default H1
            // For H1 report, we show all items by default since schedule column may not be populated
            if ($itemType && $itemType !== 'ALL' && $itemType !== 'H1') {
                $query->whereHas('item', function($q) use ($itemType) {
                    $q->where('schedule', $itemType)
                      ->orWhere('schedule', 'LIKE', '%' . $itemType . '%');
                });
            }

            // Filter by specific item
            if ($itemId) {
                $query->where('item_id', $itemId);
            }

            // Filter by item status
            if ($itemStatus !== 'B') {
                $query->whereHas('item', function($q) use ($itemStatus) {
                    if ($itemStatus === 'A') {
                        $q->where(function($sq) {
                            $sq->where('status', 'active')
                               ->orWhere('status', 'A')
                               ->orWhereNull('status');
                        });
                    } else {
                        $q->where('status', 'D')
                          ->orWhere('status', 'discontinued');
                    }
                });
            }

            // Filter by supplier flag
            if ($supplierFlag !== '5') {
                $query->whereHas('transaction.supplier', function($q) use ($supplierFlag) {
                    $q->where('flag', $supplierFlag);
                });
            }

            $query->orderBy('item_name');

            $results = $query->get();

            // Map to drug records
            $drugs = $results->map(function($item) {
                return (object)[
                    'bill_date' => $item->transaction->bill_date ?? null,
                    'bill_no' => $item->transaction->bill_no ?? '-',
                    'drug_name' => $item->item_name ?? $item->item->name ?? '-',
                    'packing' => $item->packing ?? $item->item->packing ?? '-',
                    'batch_no' => $item->batch_no ?? '-',
                    'expiry_date' => $item->expiry_date ? $item->expiry_date->format('m/Y') : '-',
                    'quantity' => $item->qty ?? 0,
                    'manufacturer' => $item->company_name ?? $item->item->mfg_by ?? $item->item->company_short_name ?? '-',
                    'supplier' => $item->transaction->supplier ?? null,
                    'schedule' => $item->item->schedule ?? 'H1'
                ];
            });

            // Calculate totals
            $totals = [
                'count' => $drugs->count(),
                'total_qty' => $drugs->sum('quantity')
            ];
        }

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportRegisterScheduleH1DrugsToExcel($drugs, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view
        if ($request->get('view_type') === 'print') {
            return view('admin.reports.purchase-report.other-reports.register-schedule-h1-drugs-print', compact(
                'dateFrom', 'dateTo', 'drugs', 'totals', 'suppliers', 'items',
                'supplierId', 'itemId', 'itemCode', 'itemType', 'supplierFlag', 'itemStatus'
            ));
        }
        
        return view('admin.reports.purchase-report.other-reports.register-schedule-h1-drugs', compact(
            'dateFrom', 'dateTo', 'drugs', 'totals', 'suppliers', 'items',
            'supplierId', 'itemId', 'itemCode', 'itemType', 'supplierFlag', 'itemStatus'
        ));
    }

    /**
     * Export Register Schedule H1 Drugs to Excel
     */
    private function exportRegisterScheduleH1DrugsToExcel($drugs, $totals, $dateFrom, $dateTo)
    {
        $filename = 'register_schedule_h1_drugs_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($drugs, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Date', 'Invoice No', 'Name of Drug', 'Batch No', 'Expiry', 'Qty Received', 'Manufacturer', 'Supplier Name', 'D.L. No.']);

            $sno = 1;
            foreach ($drugs as $drug) {
                fputcsv($file, [
                    $sno++,
                    $drug->bill_date ? $drug->bill_date->format('d-m-Y') : '-',
                    $drug->bill_no,
                    $drug->drug_name,
                    $drug->batch_no,
                    $drug->expiry_date,
                    number_format($drug->quantity, 0),
                    $drug->manufacturer,
                    $drug->supplier->name ?? 'N/A',
                    $drug->supplier->dl_no ?? '-'
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL RECORDS: ' . $totals['count'], '', '', number_format($totals['total_qty'], 0), '', '', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

