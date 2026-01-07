<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SaleReturnTransaction;
use App\Models\SaleReturnTransactionItem;
use App\Models\SaleChallanTransaction;
use App\Models\SaleChallanTransactionItem;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Models\Item;
use App\Models\Company;
use App\Models\Area;
use App\Models\Route;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    /**
     * Display the sales report dashboard/index with all report options
     */
    public function index(Request $request)
    {
        return view('admin.reports.sale-report.index');
    }

    /**
     * Sales Book Report - EasySol Style with all filters
     * Report Types: 1=Sale, 2=Sale Return, 3=Debit Note, 4=Credit Note, 5=Consolidated, 6=All CN_DN
     */
    public function salesBook(Request $request)
    {
        // Basic filters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1'); // 1=Sale, 2=Return, 3=DN, 4=CN, 5=Consolidated, 6=All CN_DN
        $reportFormat = $request->get('report_format', 'D'); // D=Detailed, S=Summarised, M=Monthly, G=Group
        
        // Invoice filters
        $cancelled = $request->get('cancelled', 'N');
        $withBrExp = $request->get('with_br_exp', 'N');
        $dayWiseTotal = $request->get('day_wise_total', 'N');
        $series = $request->get('series');
        $userId = $request->get('user_id');
        $firstLastUser = $request->get('first_last_user', 'F');
        
        // Party filters
        $partyCode = $request->get('party_code');
        $customerId = $request->get('customer_id');
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both, E=Export
        $businessType = $request->get('business_type'); // W=Wholesale, R=Retail, I=Institution, D=Dept, O=Others
        $gstnFilter = $request->get('gstn_filter', '3'); // 1=With GSTN, 2=Without GSTN, 3=All
        
        // Location filters
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        
        // Other options
        $vatRoff = $request->get('vat_roff', 'Y');
        $taxRetail = $request->get('tax_retail');
        $creditCard = $request->get('credit_card', 'Y');
        $smanFromMaster = $request->get('sman_from_master', 'N');
        
        // Display options
        $showGstDetails = $request->boolean('show_gst_details');
        $showGrDetails = $request->boolean('show_gr_details');
        $showCashCredit = $request->boolean('show_cash_credit');
        $showSalesman = $request->boolean('show_salesman');
        $orderByCustomer = $request->boolean('order_by_customer');
        $deductAddLess = $request->boolean('deduct_add_less');
        $showArea = $request->boolean('show_area');
        $withAddress = $request->boolean('with_address');

        $sales = collect();
        $totals = [
            'count' => 0,
            'gross_amount' => 0,
            'nt_amount' => 0,
            'dis_amount' => 0,
            'scm_amount' => 0,
            'tax_amount' => 0,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'igst_amount' => 0,
            'net_amount' => 0
        ];

        // Build query based on report type
        if (in_array($reportType, ['1', '5'])) {
            // Sale Transactions
            $query = SaleTransaction::with([
                'customer:id,name,code,address,area_name,route_name,state_name,local_central,business_type,gst_number',
                'salesman:id,name',
                'creator:user_id,full_name'
            ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

            // Apply filters
            $this->applySalesBookFilters($query, $request);

            // Ordering
            if ($orderByCustomer) {
                $query->orderBy('customer_id')->orderBy('sale_date')->orderBy('invoice_no');
            } else {
                $query->orderBy('sale_date')->orderBy('invoice_no');
            }

            $sales = $query->get();

            // Calculate totals
            $totals = [
                'count' => $sales->count(),
                'gross_amount' => $sales->sum('nt_amount'),
                'nt_amount' => $sales->sum('nt_amount'),
                'dis_amount' => $sales->sum('dis_amount'),
                'scm_amount' => $sales->sum('scm_amount'),
                'tax_amount' => $sales->sum('tax_amount'),
                'cgst_amount' => $sales->sum('cgst_amount'),
                'sgst_amount' => $sales->sum('sgst_amount'),
                'igst_amount' => $sales->sum('igst_amount'),
                'net_amount' => $sales->sum('net_amount')
            ];
        }

        if (in_array($reportType, ['2', '5'])) {
            // Sale Return Transactions
            $returnQuery = SaleReturnTransaction::with([
                'customer:id,name,code,address,area_name,route_name,state_name,local_central,business_type,gst_number',
                'salesman:id,name'
            ])->whereBetween('return_date', [$dateFrom, $dateTo]);

            // Apply customer filters to returns
            if ($customerId) $returnQuery->where('customer_id', $customerId);
            if ($salesmanId) $returnQuery->where('salesman_id', $salesmanId);

            $returns = $returnQuery->orderBy('return_date')->get();

            if ($reportType == '2') {
                $sales = $returns;
                $totals = [
                    'count' => $returns->count(),
                    'gross_amount' => $returns->sum('nt_amount'),
                    'nt_amount' => $returns->sum('nt_amount'),
                    'dis_amount' => $returns->sum('dis_amount'),
                    'tax_amount' => $returns->sum('tax_amount'),
                    'cgst_amount' => $returns->sum('cgst_amount'),
                    'sgst_amount' => $returns->sum('sgst_amount'),
                    'igst_amount' => $returns->sum('igst_amount'),
                    'net_amount' => $returns->sum('net_amount')
                ];
            }
        }

        // Get filter options for dropdowns
        $customers = Customer::where('is_deleted', '!=', 1)
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $users = User::select('user_id', 'full_name')->orderBy('full_name')->get();

        // Handle Excel export
        if ($request->get('export') === 'excel') {
            return $this->exportSalesBookToExcel($sales, $totals, $dateFrom, $dateTo);
        }

        // Handle Print view - open in new window
        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.sales-book-print', compact(
                'sales', 'totals', 'customers', 'salesmen', 'seriesList', 'areas', 'routes', 'states', 'users',
                'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'cancelled', 'withBrExp', 'dayWiseTotal',
                'series', 'userId', 'firstLastUser', 'partyCode', 'customerId', 'localCentral', 'businessType',
                'gstnFilter', 'salesmanId', 'areaId', 'routeId', 'stateId', 'vatRoff', 'taxRetail', 'creditCard',
                'smanFromMaster', 'showGstDetails', 'showGrDetails', 'showCashCredit', 'showSalesman',
                'orderByCustomer', 'deductAddLess', 'showArea', 'withAddress'
            ));
        }

        return view('admin.reports.sale-report.sale-book.sales-book', compact(
            'sales', 'totals', 'customers', 'salesmen', 'seriesList', 'areas', 'routes', 'states', 'users',
            'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'cancelled', 'withBrExp', 'dayWiseTotal',
            'series', 'userId', 'firstLastUser', 'partyCode', 'customerId', 'localCentral', 'businessType',
            'gstnFilter', 'salesmanId', 'areaId', 'routeId', 'stateId', 'vatRoff', 'taxRetail', 'creditCard',
            'smanFromMaster', 'showGstDetails', 'showGrDetails', 'showCashCredit', 'showSalesman',
            'orderByCustomer', 'deductAddLess', 'showArea', 'withAddress'
        ));
    }

    /**
     * Sales Book GSTR Report - GST wise detailed report
     * Report Types: 1=Sale, 2=Sale Ret, 3=D.Note, 4=C.Note, 5=Consolidated, 6=All CN_DN, 7=Expiry Sale, 8=Voucher Sale
     */
    public function salesBookGstr(Request $request)
    {
        // Basic filters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '8'); // Default to Voucher Sale (8)
        $reportFormat = $request->get('report_format', 'D'); // D=Detailed, S=Summarised, M=Monthly, G=Group
        
        // GST specific filters
        $series = $request->get('series');
        $withSuppExp = $request->get('with_supp_exp', 'Y');
        $withCustExp = $request->get('with_cust_exp', 'Y');
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $wost = $request->get('wost');
        
        // Party filters
        $partyCode = $request->get('party_code');
        $customerId = $request->get('customer_id');
        $gstnFilter = $request->get('gstn_filter', '3'); // 1=With GSTN, 2=Without GSTN, 3=All
        
        // Location filters
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        
        // Display options
        $smanFromMaster = $request->get('sman_from_master', 'N');
        $showSalesman = $request->boolean('show_salesman');
        $showArea = $request->boolean('show_area');
        $deductAddLess = $request->boolean('deduct_add_less');

        $sales = collect();
        $totals = [
            'count' => 0,
            'taxable_amount' => 0,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'igst_amount' => 0,
            'cess_amount' => 0,
            'total_tax' => 0,
            'net_amount' => 0
        ];

        // Build query based on report type
        $query = SaleTransaction::with([
            'customer:id,name,code,address,area_name,route_name,state_name,state_code,local_central,gst_number',
            'salesman:id,name',
            'items'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Apply filters
        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);
        if ($series) $query->where('series', $series);

        // Location filters via customer relationship
        if ($areaId || $routeId || $stateId || $localCentral !== 'B' || $gstnFilter !== '3') {
            $query->whereHas('customer', function($q) use ($areaId, $routeId, $stateId, $localCentral, $gstnFilter) {
                if ($areaId) $q->where('area_code', $areaId);
                if ($routeId) $q->where('route_code', $routeId);
                if ($stateId) $q->where('state_code', $stateId);
                if ($localCentral !== 'B') $q->where('local_central', $localCentral);
                if ($gstnFilter == '1') {
                    $q->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($gstnFilter == '2') {
                    $q->where(function($sq) {
                        $sq->whereNull('gst_number')->orWhere('gst_number', '');
                    });
                }
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Calculate GST-wise totals from items
        $totalTaxable = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalCess = 0;

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalTaxable += $item->taxable_amount ?? $item->amount ?? 0;
                $totalCgst += $item->cgst_amount ?? 0;
                $totalSgst += $item->sgst_amount ?? 0;
                $totalIgst += $item->igst_amount ?? 0;
                $totalCess += $item->cess_amount ?? 0;
            }
        }

        $totals = [
            'count' => $sales->count(),
            'taxable_amount' => $totalTaxable,
            'cgst_amount' => $totalCgst,
            'sgst_amount' => $totalSgst,
            'igst_amount' => $totalIgst,
            'cess_amount' => $totalCess,
            'total_tax' => $totalCgst + $totalSgst + $totalIgst + $totalCess,
            'net_amount' => $sales->sum('net_amount')
        ];

        // Get filter options for dropdowns
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        // Handle Print view - open in new window
        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.sales-book-gstr-print', compact(
                'sales', 'totals', 'customers', 'salesmen', 'seriesList', 'areas', 'routes', 'states',
                'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'series', 'withSuppExp', 'withCustExp',
                'localCentral', 'wost', 'partyCode', 'customerId', 'gstnFilter', 'salesmanId', 'areaId',
                'routeId', 'stateId', 'smanFromMaster', 'showSalesman', 'showArea', 'deductAddLess'
            ));
        }

        return view('admin.reports.sale-report.sale-book.sales-book-gstr', compact(
            'sales', 'totals', 'customers', 'salesmen', 'seriesList', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'reportType', 'reportFormat', 'series', 'withSuppExp', 'withCustExp',
            'localCentral', 'wost', 'partyCode', 'customerId', 'gstnFilter', 'salesmanId', 'areaId',
            'routeId', 'stateId', 'smanFromMaster', 'showSalesman', 'showArea', 'deductAddLess'
        ));
    }

    /**
     * Apply filters to Sales Book query
     */
    private function applySalesBookFilters($query, Request $request)
    {
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $series = $request->get('series');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $localCentral = $request->get('local_central', 'B');
        $businessType = $request->get('business_type');
        $gstnFilter = $request->get('gstn_filter', '3');
        $userId = $request->get('user_id');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($salesmanId) {
            $query->where('salesman_id', $salesmanId);
        }

        if ($series) {
            $query->where('series', $series);
        }

        if ($userId) {
            $query->where('created_by', $userId);
        }

        // Location filters via customer relationship
        if ($areaId || $routeId || $stateId || $localCentral !== 'B' || $businessType || $gstnFilter !== '3') {
            $query->whereHas('customer', function($q) use ($areaId, $routeId, $stateId, $localCentral, $businessType, $gstnFilter) {
                if ($areaId) {
                    $q->where('area_code', $areaId);
                }
                if ($routeId) {
                    $q->where('route_code', $routeId);
                }
                if ($stateId) {
                    $q->where('state_code', $stateId);
                }
                if ($localCentral !== 'B') {
                    $q->where('local_central', $localCentral);
                }
                if ($businessType) {
                    $q->where('business_type', $businessType);
                }
                if ($gstnFilter == '1') {
                    $q->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($gstnFilter == '2') {
                    $q->where(function($sq) {
                        $sq->whereNull('gst_number')->orWhere('gst_number', '');
                    });
                }
            });
        }
    }

    /**
     * Export Sales Book to Excel
     */
    private function exportSalesBookToExcel($sales, $totals, $dateFrom, $dateTo)
    {
        $filename = 'sales_book_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Party Code', 'Party Name', 'Area', 'Gross Amt', 'Discount', 'Tax', 'Net Amount']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_date->format('d-m-Y'),
                    ($sale->series ?? '') . $sale->invoice_no,
                    $sale->customer->code ?? '',
                    $sale->customer->name ?? 'N/A',
                    $sale->customer->area_name ?? '',
                    number_format($sale->nt_amount ?? 0, 2),
                    number_format($sale->dis_amount ?? 0, 2),
                    number_format($sale->tax_amount ?? 0, 2),
                    number_format($sale->net_amount ?? 0, 2)
                ]);
            }

            // Add totals row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '', 
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
     * Sales Book Party Wise - EasySol Style with all filters
     */
    public function salesBookPartyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '1'); // 1=Sale, 2=Return, 3=DN, 4=CN, 5=Consolidated
        $series = $request->get('series');
        $customerId = $request->get('customer_id');
        $selective = $request->get('selective', 'Y');
        $billWise = $request->get('bill_wise', 'Y');
        $taxRetail = $request->get('tax_retail');
        $taggedParties = $request->get('tagged_parties', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $flag = $request->get('flag');
        $printAddress = $request->get('print_address', 'N');
        $printStax = $request->get('print_stax', 'N');
        $sortBy = $request->get('sort_by', 'P'); // P=Party, A=Amount
        $ascDesc = $request->get('asc_desc', 'A');
        $amountFrom = $request->get('amount_from', 0);
        $amountTo = $request->get('amount_to', 0);
        $withVat = $request->boolean('with_vat');
        $billAmount = $request->boolean('bill_amount');
        $gstSummary = $request->boolean('gst_summary');

        $query = SaleTransaction::with(['customer:id,name,code,area_name,route_name,address', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Apply filters
        if ($series) $query->where('series', $series);
        if ($customerId) $query->where('customer_id', $customerId);
        if ($amountFrom > 0) $query->where('net_amount', '>=', $amountFrom);
        if ($amountTo > 0) $query->where('net_amount', '<=', $amountTo);

        // Ordering
        if ($sortBy === 'A') {
            $query->orderBy('net_amount', $ascDesc === 'D' ? 'desc' : 'asc');
        } else {
            $query->orderBy('customer_id')->orderBy('sale_date');
        }

        $sales = $query->get();
        $groupedSales = $sales->groupBy('customer_id');

        $totals = [
            'nt_amount' => $sales->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'net_amount' => $sales->sum('net_amount'),
            'count' => $sales->count()
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        // Handle Print view
        if ($request->has('print')) {
            return view('admin.reports.sale-report.sales-book-party-wise-print', compact(
                'groupedSales', 'totals', 'customers', 'salesmen', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'series', 'customerId', 'selective', 'billWise',
                'taxRetail', 'taggedParties', 'removeTags', 'flag', 'printAddress', 'printStax',
                'sortBy', 'ascDesc', 'amountFrom', 'amountTo', 'withVat', 'billAmount', 'gstSummary'
            ));
        }

        return view('admin.reports.sale-report.sales-book-party-wise', compact(
            'groupedSales', 'totals', 'customers', 'salesmen', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'series', 'customerId', 'selective', 'billWise',
            'taxRetail', 'taggedParties', 'removeTags', 'flag', 'printAddress', 'printStax',
            'sortBy', 'ascDesc', 'amountFrom', 'amountTo', 'withVat', 'billAmount', 'gstSummary'
        ));
    }

    /**
     * Day Sales Summary - Item Wise (EasySol Style)
     */
    public function daySalesSummaryItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $invoiceFrom = $request->get('invoice_from', 0);
        $invoiceTo = $request->get('invoice_to', 9999999);
        $localCentral = $request->get('local_central', 'B');
        $categoryId = $request->get('category_id');
        $showValue = $request->get('show_value', 'N');
        $withVat = $request->get('with_vat', 'B');
        $saleType = $request->get('sale_type', '1'); // 1=Sale, 2=Return, 3=Both
        $orderBy = $request->get('order_by', 'company');
        $ascDesc = $request->get('asc_desc', 'A');
        $addFreeQty = $request->get('add_free_qty', 'N');

        $query = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $invoiceFrom, $invoiceTo, $localCentral) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($invoiceFrom > 0) $q->where('invoice_no', '>=', $invoiceFrom);
            if ($invoiceTo < 9999999) $q->where('invoice_no', '<=', $invoiceTo);
            
            // Local/Central filter via customer
            if ($localCentral !== 'B') {
                $q->whereHas('customer', function($cq) use ($localCentral) {
                    $cq->where('local_central', $localCentral);
                });
            }
        });

        // Category filter
        if ($categoryId) {
            $query->whereHas('item', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Order by
        $orderColumn = match($orderBy) {
            'item' => 'item_name',
            'qty' => 'total_qty',
            'value' => 'total_amount',
            default => 'company_name'
        };
        $orderDirection = $ascDesc === 'D' ? 'desc' : 'asc';

        $items = $query->select(
            'item_id', 'item_code', 'item_name', 'company_name', 'packing',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free'),
            DB::raw('SUM(net_amount) as total_amount'),
            DB::raw('AVG(mrp) as mrp'),
            DB::raw('COUNT(DISTINCT sale_transaction_id) as bill_count')
        )
        ->groupBy('item_id', 'item_code', 'item_name', 'company_name', 'packing')
        ->orderBy($orderColumn, $orderDirection)
        ->get();

        // Add free qty to sale qty if requested
        if ($addFreeQty === 'Y') {
            $items->each(function($item) {
                $item->total_qty += $item->total_free;
            });
        }

        $totals = [
            'qty' => $items->sum('total_qty'),
            'free' => $items->sum('total_free'),
            'amount' => $items->sum('total_amount'),
            'items' => $items->count()
        ];

        // Get categories for dropdown
        $categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();

        // Handle Print view
        if ($request->has('print')) {
            return view('admin.reports.sale-report.day-sales-summary-item-wise-print', compact(
                'items', 'totals', 'categories', 'dateFrom', 'dateTo', 'invoiceFrom', 'invoiceTo',
                'localCentral', 'categoryId', 'showValue', 'withVat', 'saleType', 'orderBy', 'ascDesc', 'addFreeQty'
            ));
        }

        return view('admin.reports.sale-report.day-sales-summary-item-wise', compact(
            'items', 'totals', 'categories', 'dateFrom', 'dateTo', 'invoiceFrom', 'invoiceTo',
            'localCentral', 'categoryId', 'showValue', 'withVat', 'saleType', 'orderBy', 'ascDesc', 'addFreeQty'
        ));
    }

    /**
     * Sales Summary - EasySol Style with Series and Number filters
     */
    public function salesSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series');
        $numberFrom = $request->get('number_from', 0);
        $numberTo = $request->get('number_to', 0);

        $query = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Apply filters
        if ($series) {
            $query->where('series', $series);
        }
        if ($numberFrom > 0) {
            $query->where('invoice_no', '>=', $numberFrom);
        }
        if ($numberTo > 0) {
            $query->where('invoice_no', '<=', $numberTo);
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        $grandTotals = [
            'invoices' => $sales->count(),
            'nt_amount' => $sales->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'net_amount' => $sales->sum('net_amount')
        ];

        // Get series list for dropdown
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        // Handle Print view
        if ($request->has('print')) {
            return view('admin.reports.sale-report.sales-summary-print', compact(
                'sales', 'grandTotals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'numberFrom', 'numberTo'
            ));
        }

        return view('admin.reports.sale-report.sales-summary', compact(
            'sales', 'grandTotals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'numberFrom', 'numberTo'
        ));
    }


    /**
     * Sales Bills Printing - For bulk printing (EasySol Style)
     */
    public function salesBillsPrinting(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');
        $printGridFormat = $request->get('print_grid_format', 'N'); // Y/N
        $billSalesmanWise = $request->get('bill_salesman_wise', 'S'); // B=Bill Wise, S=Salesman Wise
        $remarks = $request->get('remarks', '');

        $query = SaleTransaction::with([
            'customer:id,name,code,address,gst_number,mobile,area_name,route_name',
            'items',
            'salesman:id,name,code'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($salesmanId) $query->where('salesman_id', $salesmanId);

        // Order based on Bill/Salesman wise
        if ($billSalesmanWise === 'S') {
            $query->orderBy('salesman_id')->orderBy('sale_date')->orderBy('invoice_no');
        } else {
            $query->orderBy('sale_date')->orderBy('invoice_no');
        }

        $sales = $query->get();

        // Group by salesman if salesman wise
        $groupedSales = $billSalesmanWise === 'S' 
            ? $sales->groupBy(fn($s) => $s->salesman->name ?? 'No Salesman')
            : collect(['All Bills' => $sales]);

        $totals = [
            'count' => $sales->count(),
            'net_amount' => $sales->sum('net_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'dis_amount' => $sales->sum('dis_amount')
        ];

        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sales-bills-printing-print', compact(
                'groupedSales', 'totals', 'salesmen', 'dateFrom', 'dateTo', 'salesmanId',
                'printGridFormat', 'billSalesmanWise', 'remarks'
            ));
        }

        return view('admin.reports.sale-report.sales-bills-printing', compact(
            'groupedSales', 'totals', 'salesmen', 'dateFrom', 'dateTo', 'salesmanId',
            'printGridFormat', 'billSalesmanWise', 'remarks'
        ));
    }

    /**
     * Sale Sheet - Detailed item-wise sale report (EasySol Style - Sale Book With Item Details)
     */
    public function saleSheet(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $reportType = $request->get('report_type', '1'); // 1=Sale Book, 2=Sales Return, 3=Expiry
        $series = $request->get('series', '');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,salesman_id,net_amount',
            'saleTransaction.customer:id,name,code,area_name,route_name,state_name',
            'saleTransaction.salesman:id,name'
        ])->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $customerId, $series, $salesmanId, $areaId, $routeId, $stateId) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($customerId) $q->where('customer_id', $customerId);
            if ($series) $q->where('series', $series);
            if ($salesmanId) $q->where('salesman_id', $salesmanId);
            
            if ($areaId || $routeId || $stateId) {
                $q->whereHas('customer', function($cq) use ($areaId, $routeId, $stateId) {
                    if ($areaId) $cq->where('area_code', $areaId);
                    if ($routeId) $cq->where('route_code', $routeId);
                    if ($stateId) $cq->where('state_code', $stateId);
                });
            }
        });

        $items = $query->orderBy('item_name')->get();

        $totals = [
            'qty' => $items->sum('qty'),
            'free_qty' => $items->sum('free_qty'),
            'amount' => $items->sum('amount'),
            'discount' => $items->sum('discount_amount'),
            'tax' => $items->sum('tax_amount'),
            'net_amount' => $items->sum('net_amount'),
            'items_count' => $items->count()
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-sheet-print', compact(
                'items', 'totals', 'customers', 'seriesList', 'salesmen', 'areas', 'routes', 'states',
                'dateFrom', 'dateTo', 'customerId', 'reportType', 'series', 'salesmanId', 'areaId', 'routeId', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.sale-sheet', compact(
            'items', 'totals', 'customers', 'seriesList', 'salesmen', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'customerId', 'reportType', 'series', 'salesmanId', 'areaId', 'routeId', 'stateId'
        ));
    }

    /**
     * Dispatch Sheet - For dispatch/delivery tracking (EasySol Style)
     */
    public function dispatchSheet(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $remarks = $request->get('remarks', '');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,sale_date,customer_id,salesman_id,net_amount',
            'saleTransaction.customer:id,name,code,address,area_name,route_name,mobile',
            'saleTransaction.salesman:id,name'
        ])->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        // Filter by company
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $items = $query->orderBy('company_name')->orderBy('item_name')->get();

        // Group by company
        $groupedItems = $items->groupBy('company_name');

        // Calculate totals
        $totals = [
            'qty' => $items->sum('qty'),
            'free_qty' => $items->sum('free_qty'),
            'amount' => $items->sum('net_amount'),
            'items_count' => $items->count(),
            'companies' => $groupedItems->count()
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.dispatch-sheet-print', compact(
                'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'remarks'
            ));
        }

        return view('admin.reports.sale-report.dispatch-sheet', compact(
            'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'remarks'
        ));
    }

    /**
     * Sale / Return Book Item Wise
     */
    public function saleReturnBookItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $companyId = $request->get('company_id');

        // Sales
        $salesQuery = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        // Returns
        $returnsQuery = SaleReturnTransactionItem::whereHas('saleReturnTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('return_date', [$dateFrom, $dateTo]);
        });

        if ($itemId) {
            $salesQuery->where('item_id', $itemId);
            $returnsQuery->where('item_id', $itemId);
        }

        $salesItems = $salesQuery->select(
            'item_id', 'item_code', 'item_name', 'company_name',
            DB::raw('SUM(qty) as sale_qty'),
            DB::raw('SUM(free_qty) as sale_free'),
            DB::raw('SUM(net_amount) as sale_amount')
        )->groupBy('item_id', 'item_code', 'item_name', 'company_name')->get()->keyBy('item_id');

        $returnItems = $returnsQuery->select(
            'item_id', 'item_code', 'item_name',
            DB::raw('SUM(qty) as return_qty'),
            DB::raw('SUM(free_qty) as return_free'),
            DB::raw('SUM(net_amount) as return_amount')
        )->groupBy('item_id', 'item_code', 'item_name')->get()->keyBy('item_id');

        // Merge sales and returns
        $allItemIds = $salesItems->keys()->merge($returnItems->keys())->unique();
        $combinedData = [];

        foreach ($allItemIds as $id) {
            $sale = $salesItems->get($id);
            $return = $returnItems->get($id);

            $combinedData[] = [
                'item_id' => $id,
                'item_code' => $sale->item_code ?? $return->item_code,
                'item_name' => $sale->item_name ?? $return->item_name,
                'company_name' => $sale->company_name ?? '',
                'sale_qty' => $sale->sale_qty ?? 0,
                'sale_free' => $sale->sale_free ?? 0,
                'sale_amount' => $sale->sale_amount ?? 0,
                'return_qty' => $return->return_qty ?? 0,
                'return_free' => $return->return_free ?? 0,
                'return_amount' => $return->return_amount ?? 0,
                'net_qty' => ($sale->sale_qty ?? 0) - ($return->return_qty ?? 0),
                'net_amount' => ($sale->sale_amount ?? 0) - ($return->return_amount ?? 0)
            ];
        }

        $items = collect($combinedData)->sortBy('item_name');
        $itemsList = Item::select('id', 'name')->orderBy('name')->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        $totals = [
            'sale_qty' => $items->sum('sale_qty'),
            'sale_amount' => $items->sum('sale_amount'),
            'return_qty' => $items->sum('return_qty'),
            'return_amount' => $items->sum('return_amount'),
            'net_qty' => $items->sum('net_qty'),
            'net_amount' => $items->sum('net_amount')
        ];

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-return-book-item-wise-print', compact(
                'items', 'totals', 'itemsList', 'companies',
                'dateFrom', 'dateTo', 'itemId', 'companyId'
            ));
        }

        return view('admin.reports.sale-report.sale-return-book-item-wise', compact(
            'items', 'totals', 'itemsList', 'companies',
            'dateFrom', 'dateTo', 'itemId', 'companyId'
        ));
    }


    /**
     * Local / Central Sale Register (EasySol Style)
     */
    public function localCentralSaleRegister(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '5'); // 1=Sale, 2=Sale Return, 3=Debit Note, 4=Credit Note, 5=Consolidated
        $customerId = $request->get('customer_id');
        $localCentral = $request->get('local_central', 'B'); // L=Local, C=Central, B=Both
        $cancelled = $request->get('cancelled', 'N');
        $taxRetail = $request->get('tax_retail', '');
        $series = $request->get('series', '');

        $query = SaleTransaction::with(['customer:id,name,code,local_central,gst_number,state_name', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($series) $query->where('series', $series);
        
        if ($localCentral !== 'B') {
            $query->whereHas('customer', function($q) use ($localCentral) {
                $q->where('local_central', $localCentral);
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Group by local/central
        $localSales = $sales->filter(fn($s) => ($s->customer->local_central ?? 'L') === 'L');
        $centralSales = $sales->filter(fn($s) => ($s->customer->local_central ?? 'L') === 'C');

        $totals = [
            'local' => [
                'count' => $localSales->count(),
                'nt_amount' => $localSales->sum('nt_amount'),
                'dis_amount' => $localSales->sum('dis_amount'),
                'tax_amount' => $localSales->sum('tax_amount'),
                'cgst_amount' => $localSales->sum('cgst_amount'),
                'sgst_amount' => $localSales->sum('sgst_amount'),
                'net_amount' => $localSales->sum('net_amount')
            ],
            'central' => [
                'count' => $centralSales->count(),
                'nt_amount' => $centralSales->sum('nt_amount'),
                'dis_amount' => $centralSales->sum('dis_amount'),
                'tax_amount' => $centralSales->sum('tax_amount'),
                'igst_amount' => $centralSales->sum('igst_amount'),
                'net_amount' => $centralSales->sum('net_amount')
            ],
            'total' => [
                'count' => $sales->count(),
                'nt_amount' => $sales->sum('nt_amount'),
                'dis_amount' => $sales->sum('dis_amount'),
                'tax_amount' => $sales->sum('tax_amount'),
                'net_amount' => $sales->sum('net_amount')
            ]
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.local-central-sale-register-print', compact(
                'sales', 'localSales', 'centralSales', 'totals', 'customers', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'customerId', 'localCentral', 'cancelled', 'taxRetail', 'series'
            ));
        }

        return view('admin.reports.sale-report.local-central-sale-register', compact(
            'sales', 'localSales', 'centralSales', 'totals', 'customers', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'customerId', 'localCentral', 'cancelled', 'taxRetail', 'series'
        ));
    }

    /**
     * Sale Challan Reports Index
     */
    public function saleChallanReports(Request $request)
    {
        return view('admin.reports.sale-report.sale-book.sale-challan-reports');
    }

    /**
     * Sale Challan Book (EasySol Style - Challan List)
     */
    public function saleChallanBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $routeId = $request->get('route_id');
        $areaId = $request->get('area_id');
        $flag = $request->get('flag', ''); // Cash/Credit flag
        $dsFormat = $request->get('ds_format', 'D'); // D=Detailed, S=Summarized
        $day = $request->get('day', ''); // Day filter
        $orderBy = $request->get('order_by', 'date'); // date, name, challan_no
        $holdOnly = $request->get('hold_only', false); // Hold Challans Only
        $taggedIds = $request->get('tagged_ids', ''); // Tagged challan IDs

        // Get filter options for dropdowns
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $challans = collect();
        $taggedArray = $taggedIds ? explode(',', $taggedIds) : [];
        $totals = [
            'count' => 0,
            'net_amount' => 0,
            'tagged_count' => count($taggedArray),
            'tagged_amount' => 0
        ];

        // Only fetch data when view or print is requested
        if ($request->has('view') || $request->has('print')) {
            $query = SaleChallanTransaction::with([
                'customer:id,name,code,area_code,route_code,area_name,route_name',
                'salesman:id,name,code'
            ])->whereBetween('challan_date', [$dateFrom, $dateTo]);

            if ($customerId) $query->where('customer_id', $customerId);
            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            
            if ($routeId) {
                $query->whereHas('customer', function($q) use ($routeId) {
                    $q->where('route_code', $routeId);
                });
            }
            if ($areaId) {
                $query->whereHas('customer', function($q) use ($areaId) {
                    $q->where('area_code', $areaId);
                });
            }
            
            if ($flag === 'C') $query->where('cash_flag', 'C');
            if ($flag === 'R') $query->where('cash_flag', 'R');
            
            if ($day) {
                $query->whereRaw('DAYNAME(challan_date) = ?', [$day]);
            }
            
            if ($holdOnly) {
                $query->where('status', 'hold');
            }

            // Order by
            $query = match($orderBy) {
                'name' => $query->orderBy(Customer::select('name')->whereColumn('customers.id', 'sale_challan_transactions.customer_id')),
                'challan_no' => $query->orderBy('challan_no'),
                default => $query->orderBy('challan_date')->orderBy('challan_no')
            };

            $challans = $query->get();

            $totals = [
                'count' => $challans->count(),
                'net_amount' => (float) $challans->sum('net_amount'),
                'tagged_count' => count($taggedArray),
                'tagged_amount' => (float) $challans->whereIn('id', $taggedArray)->sum('net_amount')
            ];

            // Handle Print view - open in new window
            if ($request->has('print')) {
                return view('admin.reports.sale-report.sale-challan-reports.sale-challan-book-print', compact(
                    'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas', 'taggedArray',
                    'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId',
                    'flag', 'dsFormat', 'day', 'orderBy', 'holdOnly', 'taggedIds'
                ));
            }
        }

        return view('admin.reports.sale-report.sale-challan-reports.sale-challan-book', compact(
            'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas', 'taggedArray',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId',
            'flag', 'dsFormat', 'day', 'orderBy', 'holdOnly', 'taggedIds'
        ));
    }

    /**
     * Pending Challans Report (EasySol Style - List of Pending Challans)
     */
    public function pendingChallans(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $routeId = $request->get('route_id');
        $areaId = $request->get('area_id');
        $flag = $request->get('flag', ''); // Cash/Credit flag

        // Get filter options for dropdowns
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $challans = collect();
        $totals = [
            'count' => 0,
            'net_amount' => 0
        ];

        // Only fetch data when view or print is requested
        if ($request->has('view') || $request->has('print')) {
            $query = SaleChallanTransaction::with([
                'customer:id,name,code,mobile,area_code,route_code,area_name,route_name',
                'salesman:id,name,code',
                'saleTransaction:id,invoice_no,sale_date'
            ])->where('is_invoiced', false)
              ->whereBetween('challan_date', [$dateFrom, $dateTo]);

            if ($customerId) $query->where('customer_id', $customerId);
            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            
            if ($routeId) {
                $query->whereHas('customer', function($q) use ($routeId) {
                    $q->where('route_code', $routeId);
                });
            }
            if ($areaId) {
                $query->whereHas('customer', function($q) use ($areaId) {
                    $q->where('area_code', $areaId);
                });
            }
            
            if ($flag === 'C') $query->where('cash_flag', 'C');
            if ($flag === 'R') $query->where('cash_flag', 'R');

            $challans = $query->orderBy('challan_date')->orderBy('challan_no')->get();

            $totals = [
                'count' => $challans->count(),
                'net_amount' => (float) $challans->sum('net_amount')
            ];

            // Handle Print view - open in new window
            if ($request->has('print')) {
                return view('admin.reports.sale-report.sale-challan-reports.pending-challans-print', compact(
                    'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas',
                    'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId', 'flag'
                ));
            }
        }

        return view('admin.reports.sale-report.sale-challan-reports.pending-challans', compact(
            'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId', 'flag'
        ));
    }

    /**
     * Sales Stock Summary - Stock movement summary (EasySol Style)
     */
    public function salesStockSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'S'); // S=Sale, R=Return, C=Challan
        $vouType = $request->get('vou_type', '00');
        $showTotal = $request->get('show_total', 'Y');
        $neverPrinted = $request->get('never_printed', 'Y');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $groupBy = $request->get('group_by', 'S'); // S=Salesman, A=Area, R=Route
        $acrossDown = $request->get('across_down', 'Y');

        $query = SaleTransaction::with([
            'customer:id,name,code,area_name,route_name',
            'salesman:id,name,code'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($salesmanId) $query->where('salesman_id', $salesmanId);
        
        if ($areaId) {
            $query->whereHas('customer', function($q) use ($areaId) {
                $q->where('area_code', $areaId);
            });
        }
        if ($routeId) {
            $query->whereHas('customer', function($q) use ($routeId) {
                $q->where('route_code', $routeId);
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Group by selected option
        $groupedSales = match($groupBy) {
            'A' => $sales->groupBy(fn($s) => $s->customer->area_name ?? 'No Area'),
            'R' => $sales->groupBy(fn($s) => $s->customer->route_name ?? 'No Route'),
            default => $sales->groupBy(fn($s) => $s->salesman->name ?? 'No Salesman')
        };

        $totals = [
            'count' => $sales->count(),
            'nt_amount' => $sales->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'net_amount' => $sales->sum('net_amount'),
            'tagged' => 0,
            'tagged_amount' => 0
        ];

        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sales-stock-summary-print', compact(
                'groupedSales', 'totals', 'salesmen', 'areas', 'routes',
                'dateFrom', 'dateTo', 'reportType', 'vouType', 'showTotal', 'neverPrinted',
                'salesmanId', 'areaId', 'routeId', 'groupBy', 'acrossDown'
            ));
        }

        return view('admin.reports.sale-report.sales-stock-summary', compact(
            'groupedSales', 'totals', 'salesmen', 'areas', 'routes',
            'dateFrom', 'dateTo', 'reportType', 'vouType', 'showTotal', 'neverPrinted',
            'salesmanId', 'areaId', 'routeId', 'groupBy', 'acrossDown'
        ));
    }


    /**
     * Customer Visit Status - Track customer orders/visits (EasySol Style)
     */
    public function customerVisitStatus(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');
        $visitFilter = $request->get('visit_filter', 'A'); // V=Visited, N=Not Visited, A=All
        $groupBy = $request->get('group_by', 'S'); // S=Salesman, A=Area, R=Route, All
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        // Get all active customers
        $customersQuery = Customer::where('is_deleted', '!=', 1)
            ->select('id', 'name', 'code', 'area_name', 'route_name', 'sales_man_name', 'sales_man_code', 'mobile', 'area_code', 'route_code');

        if ($salesmanId) {
            $customersQuery->where('sales_man_code', $salesmanId);
        }
        if ($areaId) {
            $customersQuery->where('area_code', $areaId);
        }
        if ($routeId) {
            $customersQuery->where('route_code', $routeId);
        }

        $customers = $customersQuery->get();

        // Get sales data for each customer
        $salesData = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereIn('customer_id', $customers->pluck('id'))
            ->select('customer_id',
                DB::raw('COUNT(*) as visit_count'),
                DB::raw('SUM(net_amount) as total_amount'),
                DB::raw('MAX(sale_date) as last_visit'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // Combine data
        $report = $customers->map(function($customer) use ($salesData) {
            $data = $salesData->get($customer->id);
            return [
                'customer' => $customer,
                'code' => $customer->code,
                'name' => $customer->name,
                'salesman' => $customer->sales_man_name,
                'area' => $customer->area_name,
                'route' => $customer->route_name,
                'mobile' => $customer->mobile,
                'visit_count' => $data->visit_count ?? 0,
                'total_amount' => (float) ($data->total_amount ?? 0),
                'last_visit' => $data->last_visit ?? null,
                'status' => ($data->visit_count ?? 0) > 0 ? 'V' : 'N'
            ];
        });

        // Apply visit filter
        if ($visitFilter === 'V') {
            $report = $report->filter(fn($r) => $r['visit_count'] > 0);
        } elseif ($visitFilter === 'N') {
            $report = $report->filter(fn($r) => $r['visit_count'] == 0);
        }

        // Group by selected option
        $groupedReport = match($groupBy) {
            'A' => $report->groupBy('area'),
            'R' => $report->groupBy('route'),
            'S' => $report->groupBy('salesman'),
            default => collect(['All' => $report])
        };

        $report = $report->sortByDesc('total_amount');

        $totals = [
            'total_customers' => $report->count(),
            'visited' => $report->where('visit_count', '>', 0)->count(),
            'not_visited' => $report->where('visit_count', 0)->count(),
            'total_amount' => $report->sum('total_amount'),
            'total_bills' => $report->sum('visit_count')
        ];

        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.customer-visit-status-print', compact(
                'report', 'groupedReport', 'totals', 'salesmen', 'areas', 'routes',
                'dateFrom', 'dateTo', 'salesmanId', 'visitFilter', 'groupBy', 'areaId', 'routeId'
            ));
        }

        return view('admin.reports.sale-report.customer-visit-status', compact(
            'report', 'groupedReport', 'totals', 'salesmen', 'areas', 'routes',
            'dateFrom', 'dateTo', 'salesmanId', 'visitFilter', 'groupBy', 'areaId', 'routeId'
        ));
    }

    /**
     * Shortage Report - Items with low/no stock sold (EasySol Style)
     */
    public function shortageReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $reportFormat = $request->get('report_format', 'D'); // D=Detailed, S=Summarized

        // Get items sold in period
        $query = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $soldItems = $query->select('item_id', 'item_code', 'item_name', 'company_name', 'packing',
            DB::raw('SUM(qty) as sold_qty'),
            DB::raw('SUM(free_qty) as free_qty'),
            DB::raw('SUM(net_amount) as total_amount'))
        ->groupBy('item_id', 'item_code', 'item_name', 'company_name', 'packing')
        ->get();

        // Get current stock for these items
        $itemIds = $soldItems->pluck('item_id');
        $stockData = \App\Models\Batch::whereIn('item_id', $itemIds)
            ->select('item_id', DB::raw('SUM(qty) as current_stock'))
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        // Build shortage report
        $shortageItems = $soldItems->map(function($item) use ($stockData) {
            $currentStock = (float) ($stockData->get($item->item_id)->current_stock ?? 0);
            $soldQty = (float) $item->sold_qty;
            return [
                'item_id' => $item->item_id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'company_name' => $item->company_name,
                'packing' => $item->packing,
                'sold_qty' => $soldQty,
                'free_qty' => (float) $item->free_qty,
                'total_amount' => (float) $item->total_amount,
                'current_stock' => $currentStock,
                'shortage_qty' => max(0, $soldQty - $currentStock),
                'status' => $currentStock <= 0 ? 'Out of Stock' : ($currentStock < $soldQty ? 'Low Stock' : 'OK')
            ];
        })->filter(fn($item) => $item['status'] !== 'OK')->sortBy('current_stock')->values();

        $totals = [
            'items' => $shortageItems->count(),
            'sold_qty' => $shortageItems->sum('sold_qty'),
            'current_stock' => $shortageItems->sum('current_stock'),
            'shortage_qty' => $shortageItems->sum('shortage_qty'),
            'total_amount' => $shortageItems->sum('total_amount'),
            'out_of_stock' => $shortageItems->where('status', 'Out of Stock')->count(),
            'low_stock' => $shortageItems->where('status', 'Low Stock')->count()
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.shortage-report-print', compact(
                'shortageItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'reportFormat'
            ));
        }

        return view('admin.reports.sale-report.shortage-report', compact(
            'shortageItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'reportFormat'
        ));
    }

    /**
     * Sale Return List (EasySol Style)
     */
    public function saleReturnList(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $remarks = $request->get('remarks', '');

        $query = SaleReturnTransaction::with(['customer:id,name,code', 'salesman:id,name', 'items'])
            ->whereBetween('return_date', [$dateFrom, $dateTo]);

        if ($companyId) {
            $query->whereHas('items', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $returns = $query->orderBy('return_date')->orderBy('sr_no')->get();

        $totals = [
            'count' => $returns->count(),
            'nt_amount' => $returns->sum('nt_amount'),
            'dis_amount' => $returns->sum('dis_amount'),
            'tax_amount' => $returns->sum('tax_amount'),
            'net_amount' => $returns->sum('net_amount'),
            'items_count' => $returns->sum(fn($r) => $r->items->sum('qty'))
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-return-list-print', compact(
                'returns', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'remarks'
            ));
        }

        return view('admin.reports.sale-report.sale-return-list', compact(
            'returns', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId', 'remarks'
        ));
    }

    /**
     * Export any report to CSV
     */
    public function exportCsv(Request $request)
    {
        $reportType = $request->get('report_type', 'sales-book');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with(['customer:id,name', 'salesman:id,name'])
            ->orderBy('sale_date')
            ->get();

        $filename = $reportType . '_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Invoice No', 'Customer', 'Salesman', 'NT Amount', 'Discount', 'Tax', 'Net Amount']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_date->format('d-m-Y'),
                    $sale->invoice_no,
                    $sale->customer->name ?? 'N/A',
                    $sale->salesman->name ?? 'N/A',
                    number_format($sale->nt_amount ?? 0, 2),
                    number_format($sale->dis_amount ?? 0, 2),
                    number_format($sale->tax_amount ?? 0, 2),
                    number_format($sale->net_amount ?? 0, 2)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->get('report_type', 'sales-book');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with(['customer:id,name', 'salesman:id,name'])
            ->orderBy('sale_date')
            ->get();

        $totalSales = $sales->sum('net_amount');
        $totalTax = $sales->sum('tax_amount');

        return view('admin.reports.sale-report.sale-book.pdf', compact('sales', 'dateFrom', 'dateTo', 'totalSales', 'totalTax', 'reportType'));
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
                    ->map(fn($item) => ['label' => $item->customer->name ?? 'Unknown', 'value' => $item->value]);
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    /**
     * TDS Input Report - Shows TDS deducted on sales
     */
    public function tdsInput(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportFormat = $request->get('report_format', 'D');
        $customerId = $request->get('customer_id');
        $localCentral = $request->get('local_central', 'B');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $query = SaleTransaction::with([
            'customer:id,name,code,pan_number,gst_number,tds,local_central,area_code,route_code,state_code',
            'salesman:id,name'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Filter only customers with TDS applicable
        $query->whereHas('customer', function($q) use ($localCentral, $areaId, $routeId, $stateId) {
            $q->where(function($sq) {
                $sq->where('tds', '>', 0)->orWhereNotNull('pan_number');
            });
            if ($localCentral !== 'B') $q->where('local_central', $localCentral);
            if ($areaId) $q->where('area_code', $areaId);
            if ($routeId) $q->where('route_code', $routeId);
            if ($stateId) $q->where('state_code', $stateId);
        });

        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Calculate TDS amounts
        $sales->each(function($sale) {
            $tdsPercent = (float) ($sale->customer->tds ?? 0);
            $taxableAmount = (float) ($sale->nt_amount ?? 0) - (float) ($sale->dis_amount ?? 0);
            $sale->taxable_amount = $taxableAmount;
            $sale->tds_percent = $tdsPercent;
            $sale->tds_amount = $tdsPercent > 0 ? $taxableAmount * ($tdsPercent / 100) : 0;
        });

        $totals = [
            'count' => $sales->count(),
            'amount' => $sales->sum('net_amount'),
            'taxable_amount' => $sales->sum('taxable_amount'),
            'tds_amount' => $sales->sum('tds_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)
            ->where(function($q) { $q->where('tds', '>', 0)->orWhereNotNull('pan_number'); })
            ->select('id', 'name', 'code', 'pan_number')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.tds-input-print', compact(
                'sales', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
                'dateFrom', 'dateTo', 'reportFormat', 'customerId', 'localCentral',
                'salesmanId', 'areaId', 'routeId', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.tds-input', compact(
            'sales', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'reportFormat', 'customerId', 'localCentral',
            'salesmanId', 'areaId', 'routeId', 'stateId'
        ));
    }

    /**
     * TCS Eligibility Report - Shows customers/suppliers eligible for TCS
     */
    public function tcsEligibility(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $partyType = $request->get('party_type', 'C'); // C=Customer, S=Supplier
        $amountThreshold = $request->get('amount_threshold', 5000000);
        $localCentral = $request->get('local_central', 'B');
        $stateId = $request->get('state_id');

        if ($partyType === 'C') {
            // Customer TCS Eligibility
            $query = Customer::where('is_deleted', '!=', 1)
                ->select('id', 'code', 'name', 'gst_number', 'pan_number', 'tcs_applicable', 'local_central', 'state_code');

            if ($localCentral !== 'B') $query->where('local_central', $localCentral);
            if ($stateId) $query->where('state_code', $stateId);

            $parties = $query->get();

            // Calculate total sales for each customer
            $parties->each(function($party) use ($dateFrom, $dateTo) {
                $totalSales = (float) SaleTransaction::where('customer_id', $party->id)
                    ->whereBetween('sale_date', [$dateFrom, $dateTo])
                    ->sum('net_amount');
                $party->total_amount = $totalSales;
                $party->tcs_rate = $party->tcs_applicable ? 0.1 : 0; // 0.1% TCS rate
                $party->tcs_amount = $party->tcs_rate > 0 ? $totalSales * ($party->tcs_rate / 100) : 0;
            });

            // Filter by threshold
            $parties = $parties->filter(fn($p) => $p->total_amount >= (float) $amountThreshold)->sortByDesc('total_amount');
        } else {
            // Supplier TCS Eligibility (from purchases)
            $parties = collect(); // Placeholder - implement if needed
        }

        $totals = [
            'count' => $parties->count(),
            'total_amount' => $parties->sum('total_amount'),
            'tcs_amount' => $parties->sum('tcs_amount')
        ];

        $states = State::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.tcs-eligibility-print', compact(
                'parties', 'totals', 'states', 'dateFrom', 'dateTo', 'partyType',
                'amountThreshold', 'localCentral', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.tcs-eligibility', compact(
            'parties', 'totals', 'states', 'dateFrom', 'dateTo', 'partyType',
            'amountThreshold', 'localCentral', 'stateId'
        ));
    }

    /**
     * Sale Book With TCS - Sales with TCS details
     */
    public function salesBookTcs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportFormat = $request->get('report_format', 'D'); // D=Detailed, S=Summarised
        $tcsFilter = $request->get('tcs_filter', 'A'); // T=With TCS, W=Without TCS, A=All
        $fromSource = $request->get('from_source', 'T'); // T=Transaction, M=Master
        $saleType = $request->get('sale_type', 'B'); // S=Sale, R=Return, B=Both
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $localCentral = $request->get('local_central', 'B');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $sales = collect();
        $returns = collect();

        // Sale Transactions
        if (in_array($saleType, ['S', 'B'])) {
            $query = SaleTransaction::with([
                'customer:id,name,code,pan_number,gst_number,tcs_applicable,local_central,area_code,route_code,state_code',
                'salesman:id,name'
            ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

            // TCS Filter
            if ($tcsFilter === 'T') {
                if ($fromSource === 'T') {
                    $query->where('tcs_amount', '>', 0);
                } else {
                    $query->whereHas('customer', fn($q) => $q->where('tcs_applicable', 1));
                }
            } elseif ($tcsFilter === 'W') {
                if ($fromSource === 'T') {
                    $query->where(function($q) { $q->whereNull('tcs_amount')->orWhere('tcs_amount', 0); });
                } else {
                    $query->whereHas('customer', fn($q) => $q->where(function($sq) {
                        $sq->whereNull('tcs_applicable')->orWhere('tcs_applicable', 0);
                    }));
                }
            }

            // Other filters
            if ($customerId) $query->where('customer_id', $customerId);
            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($localCentral !== 'B' || $areaId || $routeId || $stateId) {
                $query->whereHas('customer', function($q) use ($localCentral, $areaId, $routeId, $stateId) {
                    if ($localCentral !== 'B') $q->where('local_central', $localCentral);
                    if ($areaId) $q->where('area_code', $areaId);
                    if ($routeId) $q->where('route_code', $routeId);
                    if ($stateId) $q->where('state_code', $stateId);
                });
            }

            $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

            // Calculate TCS if from master
            if ($fromSource === 'M') {
                $sales->each(function($sale) {
                    if ($sale->customer && $sale->customer->tcs_applicable) {
                        $taxableAmount = (float) ($sale->nt_amount ?? 0) - (float) ($sale->dis_amount ?? 0);
                        $sale->calculated_tcs_percent = 0.1;
                        $sale->calculated_tcs_amount = $taxableAmount * 0.001;
                    } else {
                        $sale->calculated_tcs_percent = 0;
                        $sale->calculated_tcs_amount = 0;
                    }
                });
            }
        }

        // Sale Returns
        if (in_array($saleType, ['R', 'B'])) {
            $returnQuery = SaleReturnTransaction::with([
                'customer:id,name,code,pan_number,gst_number,tcs_applicable',
                'salesman:id,name'
            ])->whereBetween('return_date', [$dateFrom, $dateTo]);

            if ($customerId) $returnQuery->where('customer_id', $customerId);
            if ($salesmanId) $returnQuery->where('salesman_id', $salesmanId);

            $returns = $returnQuery->orderBy('return_date')->get();
        }

        // Combine and calculate totals
        $allTransactions = $sales->concat($returns->map(function($r) {
            $r->sale_date = $r->return_date;
            $r->invoice_no = $r->sr_no;
            $r->is_return = true;
            return $r;
        }));

        $totals = [
            'count' => $allTransactions->count(),
            'taxable_amount' => $sales->sum(fn($s) => (float) ($s->nt_amount ?? 0) - (float) ($s->dis_amount ?? 0)),
            'tax_amount' => $sales->sum('tax_amount'),
            'tcs_amount' => $fromSource === 'T' ? $sales->sum('tcs_amount') : $sales->sum('calculated_tcs_amount'),
            'net_amount' => $sales->sum('net_amount'),
            'return_amount' => $returns->sum('net_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code', 'pan_number')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.sales-book-tcs-print', compact(
                'sales', 'returns', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
                'dateFrom', 'dateTo', 'reportFormat', 'tcsFilter', 'fromSource', 'saleType',
                'customerId', 'salesmanId', 'localCentral', 'areaId', 'routeId', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.sales-book-tcs', compact(
            'sales', 'returns', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'reportFormat', 'tcsFilter', 'fromSource', 'saleType',
            'customerId', 'salesmanId', 'localCentral', 'areaId', 'routeId', 'stateId'
        ));
    }

    /**
     * Sale Book Extra Charges - Sales with additional charges breakdown
     */
    public function salesBookExtraCharges(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportFormat = $request->get('report_format', 'D');
        $localCentral = $request->get('local_central', 'B');
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $gstnFilter = $request->get('gstn_filter', '3');
        $tagCustomer = $request->boolean('tag_customer');
        $orderByCustomer = $request->boolean('order_by_customer');

        $query = SaleTransaction::with([
            'customer:id,name,code,address,area_name,route_name,state_name,local_central,gst_number',
            'salesman:id,name'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Apply filters
        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);

        if ($localCentral !== 'B' || $areaId || $routeId || $stateId || $gstnFilter !== '3') {
            $query->whereHas('customer', function($q) use ($localCentral, $areaId, $routeId, $stateId, $gstnFilter) {
                if ($localCentral !== 'B') $q->where('local_central', $localCentral);
                if ($areaId) $q->where('area_code', $areaId);
                if ($routeId) $q->where('route_code', $routeId);
                if ($stateId) $q->where('state_code', $stateId);
                if ($gstnFilter == '1') {
                    $q->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($gstnFilter == '2') {
                    $q->where(function($sq) { $sq->whereNull('gst_number')->orWhere('gst_number', ''); });
                }
            });
        }

        // Ordering
        if ($orderByCustomer) {
            $query->orderBy('customer_id')->orderBy('sale_date')->orderBy('invoice_no');
        } else {
            $query->orderBy('sale_date')->orderBy('invoice_no');
        }

        $sales = $query->get();

        $totals = [
            'count' => $sales->count(),
            'nt_amount' => $sales->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount'),
            'scm_amount' => $sales->sum('scm_amount'),
            'sc_amount' => $sales->sum('sc_amount'),
            'ft_amount' => $sales->sum('ft_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'tcs_amount' => $sales->sum('tcs_amount'),
            'net_amount' => $sales->sum('net_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        if ($request->has('print')) {
            return view('admin.reports.sale-report.sale-book.sales-book-extra-charges-print', compact(
                'sales', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
                'dateFrom', 'dateTo', 'reportFormat', 'localCentral', 'customerId', 'salesmanId',
                'areaId', 'routeId', 'stateId', 'gstnFilter', 'tagCustomer', 'orderByCustomer'
            ));
        }

        return view('admin.reports.sale-report.sale-book.sales-book-extra-charges', compact(
            'sales', 'totals', 'customers', 'salesmen', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'reportFormat', 'localCentral', 'customerId', 'salesmanId',
            'areaId', 'routeId', 'stateId', 'gstnFilter', 'tagCustomer', 'orderByCustomer'
        ));
    }

    // ==========================================
    // MISCELLANEOUS SALES ANALYSIS REPORTS
    // ==========================================

    /**
     * Sales Man Wise Sales Report
     */
    public function salesmanWiseSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'all');
        $transactionType = $request->get('transaction_type', '5'); // 1=Sale, 2=Return, 3=DN, 4=CN, 5=Consolidated
        
        // Filter options
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $companyId = $request->get('company_id');
        $divisionId = $request->get('division_id');
        $categoryId = $request->get('category_id');
        $customerId = $request->get('customer_id');
        $series = $request->get('series');
        
        // Y/N options
        $selective = $request->get('selective', 'Y');
        $taggedSalesman = $request->get('tagged_salesman', 'N');
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $taggedCustomers = $request->get('tagged_customers', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $withReplacementNote = $request->get('with_replacement_note', 'Y');
        $showIncentive = $request->get('show_incentive', 'N');
        $withBatchDetails = $request->get('with_batch_details', 'Y');
        $companyItem = $request->get('company_item', 'C'); // C=Company, I=Item
        $taxRetail = $request->get('tax_retail', '');
        $itemStatus = $request->get('item_status', '');
        $flag = $request->get('flag', '');
        $orderBy = $request->get('order_by', 'N'); // N=Name, V=Value
        $ascDesc = $request->get('asc_desc', 'A'); // A=Ascending, D=Descending

        // Get dropdown data
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        $data = collect();
        $totals = ['count' => 0, 'qty' => 0, 'free_qty' => 0, 'gross_amount' => 0, 'dis_amount' => 0, 'tax_amount' => 0, 'net_amount' => 0];

        // Build base query
        $query = SaleTransaction::with([
            'customer:id,name,code,area_code,area_name,route_code,route_name,state_code,state_name',
            'salesman:id,name,code',
            'items'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Apply filters
        if ($salesmanId) $query->where('salesman_id', $salesmanId);
        if ($series) $query->where('series', $series);
        if ($customerId) $query->where('customer_id', $customerId);
        
        // Location filters via customer
        if ($areaId || $routeId || $stateId) {
            $query->whereHas('customer', function($q) use ($areaId, $routeId, $stateId) {
                if ($areaId) $q->where('area_code', $areaId);
                if ($routeId) $q->where('route_code', $routeId);
                if ($stateId) $q->where('state_code', $stateId);
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Process data based on report type
        switch ($reportType) {
            case 'all':
                // All Salesman Summary
                $data = $sales->groupBy(fn($s) => $s->salesman_id)->map(function($group) {
                    $salesman = $group->first()->salesman;
                    return [
                        'salesman_id' => $salesman->id ?? 0,
                        'salesman_code' => $salesman->code ?? '',
                        'salesman_name' => $salesman->name ?? 'No Salesman',
                        'bill_count' => $group->count(),
                        'gross_amount' => (float) $group->sum('nt_amount'),
                        'dis_amount' => (float) $group->sum('dis_amount'),
                        'tax_amount' => (float) $group->sum('tax_amount'),
                        'net_amount' => (float) $group->sum('net_amount'),
                    ];
                })->sortBy('salesman_name')->values();
                
                $totals = [
                    'count' => $sales->count(),
                    'gross_amount' => (float) $sales->sum('nt_amount'),
                    'dis_amount' => (float) $sales->sum('dis_amount'),
                    'tax_amount' => (float) $sales->sum('tax_amount'),
                    'net_amount' => (float) $sales->sum('net_amount'),
                ];
                break;

            case 'bill_wise':
                // Bill Wise - Show all bills with salesman
                $data = $sales->map(function($sale) {
                    return [
                        'sale_date' => $sale->sale_date,
                        'invoice_no' => ($sale->series ?? '') . $sale->invoice_no,
                        'salesman_name' => $sale->salesman->name ?? 'No Salesman',
                        'customer_code' => $sale->customer->code ?? '',
                        'customer_name' => $sale->customer->name ?? '',
                        'gross_amount' => (float) $sale->nt_amount,
                        'dis_amount' => (float) $sale->dis_amount,
                        'tax_amount' => (float) $sale->tax_amount,
                        'net_amount' => (float) $sale->net_amount,
                    ];
                });
                
                $totals = [
                    'count' => $sales->count(),
                    'gross_amount' => (float) $sales->sum('nt_amount'),
                    'dis_amount' => (float) $sales->sum('dis_amount'),
                    'tax_amount' => (float) $sales->sum('tax_amount'),
                    'net_amount' => (float) $sales->sum('net_amount'),
                ];
                break;

            case 'customer_wise':
                // Customer Wise grouped by Salesman
                $data = $sales->groupBy('salesman_id')->map(function($salesmanGroup) {
                    $salesman = $salesmanGroup->first()->salesman;
                    $customerData = $salesmanGroup->groupBy('customer_id')->map(function($custGroup) {
                        $customer = $custGroup->first()->customer;
                        return [
                            'customer_code' => $customer->code ?? '',
                            'customer_name' => $customer->name ?? '',
                            'bill_count' => $custGroup->count(),
                            'gross_amount' => (float) $custGroup->sum('nt_amount'),
                            'dis_amount' => (float) $custGroup->sum('dis_amount'),
                            'tax_amount' => (float) $custGroup->sum('tax_amount'),
                            'net_amount' => (float) $custGroup->sum('net_amount'),
                        ];
                    })->values();
                    
                    return [
                        'salesman_name' => $salesman->name ?? 'No Salesman',
                        'customers' => $customerData,
                        'total_bills' => $salesmanGroup->count(),
                        'total_amount' => (float) $salesmanGroup->sum('net_amount'),
                    ];
                })->values();
                
                $totals = [
                    'count' => $sales->count(),
                    'net_amount' => (float) $sales->sum('net_amount'),
                ];
                break;

            case 'item_wise':
                // Item Wise grouped by Salesman
                $itemData = [];
                foreach ($sales as $sale) {
                    $salesmanName = $sale->salesman->name ?? 'No Salesman';
                    foreach ($sale->items as $item) {
                        $key = $salesmanName . '_' . $item->item_id;
                        if (!isset($itemData[$key])) {
                            $itemData[$key] = [
                                'salesman_name' => $salesmanName,
                                'item_code' => $item->item_code,
                                'item_name' => $item->item_name,
                                'company_name' => $item->company_name ?? '',
                                'packing' => $item->packing ?? '',
                                'qty' => 0,
                                'free_qty' => 0,
                                'amount' => 0,
                            ];
                        }
                        $itemData[$key]['qty'] += (float) $item->qty;
                        $itemData[$key]['free_qty'] += (float) ($item->free_qty ?? 0);
                        $itemData[$key]['amount'] += (float) $item->net_amount;
                    }
                }
                $data = collect($itemData)->groupBy('salesman_name');
                
                $totals = [
                    'qty' => collect($itemData)->sum('qty'),
                    'free_qty' => collect($itemData)->sum('free_qty'),
                    'amount' => collect($itemData)->sum('amount'),
                ];
                break;

            case 'company_wise':
                // Company Wise grouped by Salesman
                $companyData = [];
                foreach ($sales as $sale) {
                    $salesmanName = $sale->salesman->name ?? 'No Salesman';
                    foreach ($sale->items as $item) {
                        $companyName = $item->company_name ?? 'No Company';
                        $key = $salesmanName . '_' . $companyName;
                        if (!isset($companyData[$key])) {
                            $companyData[$key] = [
                                'salesman_name' => $salesmanName,
                                'company_name' => $companyName,
                                'qty' => 0,
                                'amount' => 0,
                            ];
                        }
                        $companyData[$key]['qty'] += (float) $item->qty;
                        $companyData[$key]['amount'] += (float) $item->net_amount;
                    }
                }
                $data = collect($companyData)->groupBy('salesman_name');
                
                $totals = [
                    'qty' => collect($companyData)->sum('qty'),
                    'amount' => collect($companyData)->sum('amount'),
                ];
                break;

            case 'area_wise':
                // Area Wise grouped by Salesman
                $data = $sales->groupBy('salesman_id')->map(function($salesmanGroup) {
                    $salesman = $salesmanGroup->first()->salesman;
                    $areaData = $salesmanGroup->groupBy(fn($s) => $s->customer->area_name ?? 'No Area')->map(function($areaGroup, $areaName) {
                        return [
                            'area_name' => $areaName,
                            'bill_count' => $areaGroup->count(),
                            'net_amount' => (float) $areaGroup->sum('net_amount'),
                        ];
                    })->values();
                    
                    return [
                        'salesman_name' => $salesman->name ?? 'No Salesman',
                        'areas' => $areaData,
                        'total_amount' => (float) $salesmanGroup->sum('net_amount'),
                    ];
                })->values();
                
                $totals = ['net_amount' => (float) $sales->sum('net_amount')];
                break;

            case 'route_wise':
                // Route Wise grouped by Salesman
                $data = $sales->groupBy('salesman_id')->map(function($salesmanGroup) {
                    $salesman = $salesmanGroup->first()->salesman;
                    $routeData = $salesmanGroup->groupBy(fn($s) => $s->customer->route_name ?? 'No Route')->map(function($routeGroup, $routeName) {
                        return [
                            'route_name' => $routeName,
                            'bill_count' => $routeGroup->count(),
                            'net_amount' => (float) $routeGroup->sum('net_amount'),
                        ];
                    })->values();
                    
                    return [
                        'salesman_name' => $salesman->name ?? 'No Salesman',
                        'routes' => $routeData,
                        'total_amount' => (float) $salesmanGroup->sum('net_amount'),
                    ];
                })->values();
                
                $totals = ['net_amount' => (float) $sales->sum('net_amount')];
                break;

            case 'state_wise':
                // State Wise grouped by Salesman
                $data = $sales->groupBy('salesman_id')->map(function($salesmanGroup) {
                    $salesman = $salesmanGroup->first()->salesman;
                    $stateData = $salesmanGroup->groupBy(fn($s) => $s->customer->state_name ?? 'No State')->map(function($stateGroup, $stateName) {
                        return [
                            'state_name' => $stateName,
                            'bill_count' => $stateGroup->count(),
                            'net_amount' => (float) $stateGroup->sum('net_amount'),
                        ];
                    })->values();
                    
                    return [
                        'salesman_name' => $salesman->name ?? 'No Salesman',
                        'states' => $stateData,
                        'total_amount' => (float) $salesmanGroup->sum('net_amount'),
                    ];
                })->values();
                
                $totals = ['net_amount' => (float) $sales->sum('net_amount')];
                break;

            default:
                $data = $sales;
                $totals = [
                    'count' => $sales->count(),
                    'net_amount' => (float) $sales->sum('net_amount'),
                ];
        }

        // Order data if applicable
        if ($orderBy === 'V' && in_array($reportType, ['all'])) {
            $data = $ascDesc === 'D' 
                ? $data->sortByDesc('net_amount')->values()
                : $data->sortBy('net_amount')->values();
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.all-salesman-print', compact(
                'data', 'totals', 'salesmen', 'areas', 'routes', 'states', 'companies', 'customers', 'categories', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'transactionType', 'salesmanId', 'areaId', 'routeId', 'stateId',
                'companyId', 'divisionId', 'categoryId', 'customerId', 'series', 'selective', 'taggedSalesman',
                'taggedCompanies', 'taggedCustomers', 'removeTags', 'withBrExpiry', 'withReplacementNote',
                'showIncentive', 'withBatchDetails', 'companyItem', 'taxRetail', 'itemStatus', 'flag', 'orderBy', 'ascDesc'
            ));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.all-salesman', compact(
            'data', 'totals', 'salesmen', 'areas', 'routes', 'states', 'companies', 'customers', 'categories', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'transactionType', 'salesmanId', 'areaId', 'routeId', 'stateId',
            'companyId', 'divisionId', 'categoryId', 'customerId', 'series', 'selective', 'taggedSalesman',
            'taggedCompanies', 'taggedCustomers', 'removeTags', 'withBrExpiry', 'withReplacementNote',
            'showIncentive', 'withBatchDetails', 'companyItem', 'taxRetail', 'itemStatus', 'flag', 'orderBy', 'ascDesc'
        ));
    }

    /**
     * Area Wise Sale Report
     */
    public function areaWiseSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $query = SaleTransaction::with(['customer:id,name,code,area_code,area_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($areaId) {
            $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
        }

        $sales = $query->orderBy('sale_date')->get();

        $groupedSales = $sales->groupBy(fn($s) => $s->customer->area_name ?? 'No Area');

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.area-wise-sale-print', compact(
                'groupedSales', 'totals', 'areas', 'dateFrom', 'dateTo', 'areaId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.area-wise-sale', compact(
            'groupedSales', 'totals', 'areas', 'dateFrom', 'dateTo', 'areaId'
        ));
    }

    /**
     * Route Wise Sale Report
     */
    public function routeWiseSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();

        $query = SaleTransaction::with(['customer:id,name,code,route_code,route_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($routeId) {
            $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
        }

        $sales = $query->orderBy('sale_date')->get();

        $groupedSales = $sales->groupBy(fn($s) => $s->customer->route_name ?? 'No Route');

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.route-wise-sale-print', compact(
                'groupedSales', 'totals', 'routes', 'dateFrom', 'dateTo', 'routeId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.route-wise-sale', compact(
            'groupedSales', 'totals', 'routes', 'dateFrom', 'dateTo', 'routeId'
        ));
    }

    /**
     * State Wise Sale Report
     */
    public function stateWiseSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');

        $states = State::select('id', 'name')->orderBy('name')->get();

        $query = SaleTransaction::with(['customer:id,name,code,state_code,state_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($stateId) {
            $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
        }

        $sales = $query->orderBy('sale_date')->get();

        $groupedSales = $sales->groupBy(fn($s) => $s->customer->state_name ?? 'No State');

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount'),
            'cgst' => (float) $sales->sum('cgst_amount'),
            'sgst' => (float) $sales->sum('sgst_amount'),
            'igst' => (float) $sales->sum('igst_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.state-wise-sale-print', compact(
                'groupedSales', 'totals', 'states', 'dateFrom', 'dateTo', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.state-wise-sale', compact(
            'groupedSales', 'totals', 'states', 'dateFrom', 'dateTo', 'stateId'
        ));
    }

    /**
     * Customer Wise Sale Report
     */
    public function customerWiseSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        $query = SaleTransaction::with(['customer:id,name,code,mobile,area_name,route_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy('sale_date')->get();

        $groupedSales = $sales->groupBy(fn($s) => $s->customer->name ?? 'Unknown');

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.customer-wise-sale-print', compact(
                'groupedSales', 'totals', 'customers', 'dateFrom', 'dateTo', 'customerId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.customer-wise-sale', compact(
            'groupedSales', 'totals', 'customers', 'dateFrom', 'dateTo', 'customerId'
        ));
    }

    /**
     * Company Wise Sales Report
     */
    public function companyWiseSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        $query = SaleTransactionItem::with(['saleTransaction', 'item:id,name,code,company_id', 'item.company:id,name,code'])
            ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));

        if ($companyId) {
            $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
        }

        $items = $query->get();

        $groupedItems = $items->groupBy(fn($i) => $i->item->company->name ?? 'No Company');

        $totals = [
            'qty' => (float) $items->sum('qty'),
            'amount' => (float) $items->sum('amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.company-wise-sales-print', compact(
                'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.company-wise-sales', compact(
            'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId'
        ));
    }

    /**
     * Item Wise Sales Report
     */
    public function itemWiseSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $companyId = $request->get('company_id');

        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        $query = SaleTransactionItem::with(['saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name,code', 'item:id,name,code,packing,company_id'])
            ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));

        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        if ($companyId) {
            $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
        }

        $items = $query->get();

        $groupedItems = $items->groupBy(fn($i) => $i->item->name ?? 'Unknown Item');

        $totals = [
            'qty' => (float) $items->sum('qty'),
            'amount' => (float) $items->sum('amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.item-wise-sales-print', compact(
                'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'itemId', 'companyId'
            ));
        }

        return view('admin.reports.sale-report.sale-book.item-wise-sales', compact(
            'groupedItems', 'totals', 'companies', 'dateFrom', 'dateTo', 'itemId', 'companyId'
        ));
    }

    /**
     * Discount Wise Sales Report
     */
    public function discountWiseSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->where('dis_amount', '>', 0)
            ->orderBy('sale_date')
            ->get();

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount'),
            'dis_amount' => (float) $sales->sum('dis_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.discount-wise-sales-print', compact(
                'sales', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.discount-wise-sales', compact(
            'sales', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    // ==========================================
    // OTHER SALES REPORTS
    // ==========================================

    /**
     * Sales Man and other Level Sale Report (Marketing Levels Report)
     */
    public function salesmanLevelSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '4');
        $level = $request->get('level', 'salesman');
        $salesmanId = $request->get('salesman_id');
        $companyId = $request->get('company_id');
        
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['sale_qty' => 0, 'sale_amount' => 0, 'return_qty' => 0, 'return_amount' => 0, 'net_qty' => 0, 'net_amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            // Get sales data grouped by salesman
            $salesQuery = SaleTransaction::with(['salesman:id,name,code', 'items'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('is_return', false);
            
            if ($salesmanId) $salesQuery->where('salesman_id', $salesmanId);
            
            $salesData = $salesQuery->get()->groupBy('salesman_id');
            
            // Get returns data
            $returnsQuery = SaleTransaction::with(['salesman:id,name,code', 'items'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('is_return', true);
            
            if ($salesmanId) $returnsQuery->where('salesman_id', $salesmanId);
            
            $returnsData = $returnsQuery->get()->groupBy('salesman_id');
            
            // Combine data
            $allSalesmanIds = $salesData->keys()->merge($returnsData->keys())->unique();
            
            foreach ($allSalesmanIds as $sId) {
                $sales = $salesData->get($sId, collect());
                $returns = $returnsData->get($sId, collect());
                $salesman = $sales->first()?->salesman ?? $returns->first()?->salesman;
                
                $saleQty = $sales->sum(fn($t) => $t->items->sum('qty'));
                $saleAmt = $sales->sum('net_amount');
                $returnQty = $returns->sum(fn($t) => $t->items->sum('qty'));
                $returnAmt = $returns->sum('net_amount');
                
                $data->push([
                    'name' => $salesman?->name ?? 'Unknown',
                    'code' => $salesman?->code ?? '-',
                    'sale_qty' => $saleQty,
                    'sale_amount' => $saleAmt,
                    'return_qty' => $returnQty,
                    'return_amount' => $returnAmt,
                    'net_qty' => $saleQty - $returnQty,
                    'net_amount' => $saleAmt - $returnAmt,
                ]);
            }
            
            $totals = [
                'sale_qty' => $data->sum('sale_qty'),
                'sale_amount' => $data->sum('sale_amount'),
                'return_qty' => $data->sum('return_qty'),
                'return_amount' => $data->sum('return_amount'),
                'net_qty' => $data->sum('net_qty'),
                'net_amount' => $data->sum('net_amount'),
            ];
            
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-level-sale-print', compact('data', 'totals', 'dateFrom', 'dateTo', 'level'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-level-sale', compact('dateFrom', 'dateTo', 'salesmen', 'companies'));
    }

    /**
     * Scheme Issued Report
     */
    public function schemeIssued(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get items with scheme qty > 0
        $items = SaleTransactionItem::with(['saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name,code', 'item:id,name,code,packing'])
            ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]))
            ->where('scheme_qty', '>', 0)
            ->get();

        $totals = [
            'count' => $items->count(),
            'scheme_qty' => (float) $items->sum('scheme_qty'),
            'scheme_amount' => (float) $items->sum(fn($i) => $i->scheme_qty * $i->rate)
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.scheme-issued-print', compact(
                'items', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.scheme-issued', compact(
            'items', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * MRP Wise Sales Report
     */
    public function mrpWiseSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $items = SaleTransactionItem::with(['saleTransaction:id,invoice_no,sale_date', 'item:id,name,code,packing'])
            ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]))
            ->get();

        $groupedItems = $items->groupBy('mrp');

        $totals = [
            'qty' => (float) $items->sum('qty'),
            'amount' => (float) $items->sum('amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.mrp-wise-sales-print', compact(
                'groupedItems', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.mrp-wise-sales', compact(
            'groupedItems', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Display Amount Report
     */
    public function displayAmountReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->orderBy('sale_date')
            ->get();

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.display-amount-report-print', compact(
                'sales', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.display-amount-report', compact(
            'sales', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * List of Cancelled Invoices
     */
    public function cancelledInvoices(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = SaleTransaction::with(['customer:id,name,code', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->where('is_cancelled', true)
            ->orderBy('sale_date')
            ->get();

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.cancelled-invoices-print', compact(
                'sales', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.cancelled-invoices', compact(
            'sales', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * List of Missing Invoices
     */
    public function missingInvoices(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series', '');

        // Get all invoice numbers in range
        $invoices = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->when($series, fn($q) => $q->where('series', $series))
            ->orderBy('invoice_no')
            ->pluck('invoice_no')
            ->toArray();

        // Find missing numbers
        $missingInvoices = [];
        if (count($invoices) > 1) {
            $min = min($invoices);
            $max = max($invoices);
            $allNumbers = range($min, $max);
            $missingInvoices = array_diff($allNumbers, $invoices);
        }

        $seriesList = SaleTransaction::distinct()->pluck('series')->filter()->values();

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.missing-invoices-print', compact(
                'missingInvoices', 'seriesList', 'dateFrom', 'dateTo', 'series'
            ));
        }

        return view('admin.reports.sale-report.sale-book.missing-invoices', compact(
            'missingInvoices', 'seriesList', 'dateFrom', 'dateTo', 'series'
        ));
    }

    /**
     * Salesman Wise Sales - All Salesman
     */
    public function salesmanWiseSalesAllSalesman(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $taggedSalesman = $request->get('tagged_salesman', 'N');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $orderBy = $request->get('order_by', 'N');
        $ascDesc = $request->get('asc_desc', 'A');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $series = $request->get('series');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->whereNotNull('series')->pluck('series')->toArray();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($series) $query->where('series', $series);

            $sales = $query->get();

            // Group by salesman
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $grossAmount = $smanSales->sum('gross_amount');
                $discount = $smanSales->sum('discount_amount');
                $netAmount = $smanSales->sum('net_amount');

                $data->push([
                    'salesman_name' => $salesman->name ?? '-',
                    'salesman_code' => $salesman->code ?? '-',
                    'bill_count' => $smanSales->count(),
                    'gross_amount' => $grossAmount,
                    'discount' => $discount,
                    'net_amount' => $netAmount,
                ]);
            }

            // Sort data
            if ($orderBy === 'N') {
                $data = $ascDesc === 'A' ? $data->sortBy('salesman_name') : $data->sortByDesc('salesman_name');
            } else {
                $data = $ascDesc === 'A' ? $data->sortBy('net_amount') : $data->sortByDesc('net_amount');
            }
            $data = $data->values();

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.all-salesman-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'transactionType', 'taggedSalesman',
                    'salesmanId', 'areaId', 'routeId', 'stateId', 'orderBy', 'ascDesc', 'withBrExpiry', 'series'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.all-salesman', compact(
            'salesmen', 'areas', 'routes', 'states', 'seriesList', 'dateFrom', 'dateTo', 'transactionType',
            'taggedSalesman', 'salesmanId', 'areaId', 'routeId', 'stateId', 'orderBy', 'ascDesc', 'withBrExpiry', 'series'
        ));
    }

    /**
     * Salesman Wise Sales - Bill Wise
     */
    public function salesmanWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'Y');
        $salesmanId = $request->get('salesman_id');
        $companyId = $request->get('company_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $customerId = $request->get('customer_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $series = $request->get('series');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->whereNotNull('series')->pluck('series')->toArray();

        $data = collect();
        $totals = ['gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,code,area_code,route_code,state_code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($series) $query->where('series', $series);

            $sales = $query->orderBy('salesman_id')->orderBy('sale_date')->orderBy('invoice_no')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'salesman_name' => $sale->salesman->name ?? '-',
                    'salesman_code' => $sale->salesman->code ?? '-',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'series' => $sale->series ?? '-',
                    'gross_amount' => $sale->gross_amount ?? 0,
                    'discount' => $sale->discount_amount ?? 0,
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = [
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.bill-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.bill-wise', compact(
            'salesmen', 'companies', 'customers', 'areas', 'routes', 'states', 'seriesList',
            'dateFrom', 'dateTo', 'transactionType', 'selective', 'salesmanId', 'companyId',
            'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'customerId', 'withBrExpiry', 'series'
        ));
    }

    /**
     * Salesman Wise Sales - Customer Wise
     */
    public function salesmanWiseSalesCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'S');
        $salesmanId = $request->get('salesman_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $customerId = $request->get('customer_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,code,area_code,route_code,state_code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by customer
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $customerGroups = $smanSales->groupBy('customer_id');

                foreach ($customerGroups as $custId => $custSales) {
                    $customer = $custSales->first()->customer;
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'customer_name' => $customer->name ?? '-',
                        'customer_code' => $customer->code ?? '-',
                        'bill_count' => $custSales->count(),
                        'gross_amount' => $custSales->sum('gross_amount'),
                        'discount' => $custSales->sum('discount_amount'),
                        'net_amount' => $custSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.customer-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.customer-wise', compact(
            'salesmen', 'customers', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'customerId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Item Wise
     */
    public function salesmanWiseSalesItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '1');
        $selective = $request->get('selective', 'Y');
        $salesmanId = $request->get('salesman_id');
        $companyId = $request->get('company_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'items.item:id,name,code,packing,company_id', 'items.item.company:id,name,code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by item
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $itemsCollection = collect();

                foreach ($smanSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $itemsCollection->push($item);
                    }
                }

                $itemGroups = $itemsCollection->groupBy('item_id');

                foreach ($itemGroups as $itemId => $itemRecords) {
                    $firstItem = $itemRecords->first();
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'item_name' => $firstItem->item_name ?? ($firstItem->item->name ?? '-'),
                        'company_name' => $firstItem->item->company->name ?? '-',
                        'packing' => $firstItem->item->packing ?? '-',
                        'qty' => $itemRecords->sum('qty'),
                        'free_qty' => $itemRecords->sum('free_qty'),
                        'amount' => $itemRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.item-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.item-wise', compact(
            'salesmen', 'items', 'companies', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'companyId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Company Wise
     */
    public function salesmanWiseSalesCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'Y');
        $salesmanId = $request->get('salesman_id');
        $companyId = $request->get('company_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['item_count' => 0, 'qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'items.item:id,name,company_id', 'items.item.company:id,name,code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by company
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $companyItems = collect();

                foreach ($smanSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $companyItems->push($item);
                    }
                }

                $companyGroups = $companyItems->groupBy(fn($i) => $i->item->company_id ?? 0);

                foreach ($companyGroups as $compId => $compRecords) {
                    $firstItem = $compRecords->first();
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'company_name' => $firstItem->item->company->name ?? '-',
                        'company_code' => $firstItem->item->company->code ?? '-',
                        'item_count' => $compRecords->unique('item_id')->count(),
                        'qty' => $compRecords->sum('qty'),
                        'amount' => $compRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'item_count' => $data->sum('item_count'),
                'qty' => $data->sum('qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.company-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.company-wise', compact(
            'salesmen', 'companies', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'companyId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Area Wise
     */
    public function salesmanWiseSalesAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'S');
        $salesmanId = $request->get('salesman_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by area
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $areaGroups = $smanSales->groupBy(fn($s) => $s->customer->area_code ?? 0);

                foreach ($areaGroups as $areaCode => $areaSales) {
                    $area = $areaSales->first()->customer->area ?? null;
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'area_name' => $area->name ?? 'No Area',
                        'bill_count' => $areaSales->count(),
                        'gross_amount' => $areaSales->sum('gross_amount'),
                        'discount' => $areaSales->sum('discount_amount'),
                        'net_amount' => $areaSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.area-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.area-wise', compact(
            'salesmen', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Route Wise
     */
    public function salesmanWiseSalesRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'S');
        $salesmanId = $request->get('salesman_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by route
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $routeGroups = $smanSales->groupBy(fn($s) => $s->customer->route_code ?? 0);

                foreach ($routeGroups as $routeCode => $routeSales) {
                    $route = $routeSales->first()->customer->route ?? null;
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'route_name' => $route->name ?? 'No Route',
                        'bill_count' => $routeSales->count(),
                        'gross_amount' => $routeSales->sum('gross_amount'),
                        'discount' => $routeSales->sum('discount_amount'),
                        'net_amount' => $routeSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.route-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.route-wise', compact(
            'salesmen', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - State Wise
     */
    public function salesmanWiseSalesStateWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $selective = $request->get('selective', 'S');
        $salesmanId = $request->get('salesman_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman then by state
            $grouped = $sales->groupBy('salesman_id');

            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $stateGroups = $smanSales->groupBy(fn($s) => $s->customer->state_code ?? 0);

                foreach ($stateGroups as $stateCode => $stateSales) {
                    $state = $stateSales->first()->customer->state ?? null;
                    $data->push([
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'state_name' => $state->name ?? 'No State',
                        'bill_count' => $stateSales->count(),
                        'gross_amount' => $stateSales->sum('gross_amount'),
                        'discount' => $stateSales->sum('discount_amount'),
                        'net_amount' => $stateSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.state-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'salesmanId'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.state-wise', compact(
            'salesmen', 'areas', 'routes', 'states', 'dateFrom', 'dateTo',
            'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Salesman Wise
     */
    public function salesmanWiseSalesSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.month-wise.salesman-wise', compact(
            'salesmen', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Salesman / Item Wise
     */
    public function salesmanWiseSalesSalesmanItemWise(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->format('Y'));
        $yearTo = $request->get('year_to', Carbon::now()->addYear()->format('Y'));
        $salesIn = $request->get('sales_in', '1');
        $taggedCompanies = $request->get('tagged_companies', 'N');
        $divisionId = $request->get('division_id');
        $companyId = $request->get('company_id');
        $salesmanId = $request->get('salesman_id');
        $customerId = $request->get('customer_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();

        if ($request->get('view_type') === 'print') {
            $startDate = Carbon::createFromFormat('Y', $yearFrom)->startOfYear();
            $endDate = Carbon::createFromFormat('Y', $yearTo)->endOfYear();

            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code', 'items'])
                ->whereBetween('sale_date', [$startDate, $endDate]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman, then by item, then by month
            $grouped = $sales->groupBy('salesman_id');
            
            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $itemData = [];
                
                foreach ($smanSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->company_id != $companyId) continue;
                        
                        $month = Carbon::parse($sale->sale_date)->month;
                        $itemKey = $item->item_id ?? $item->item_name;
                        
                        if (!isset($itemData[$itemKey])) {
                            $itemData[$itemKey] = [
                                'item_name' => $item->item_name,
                                'company_name' => $item->company_name ?? '-',
                                'monthly' => array_fill(1, 12, 0)
                            ];
                        }
                        
                        $amount = $item->net_amount ?? 0;
                        if ($salesIn == '1') $amount = $amount / 1000;
                        elseif ($salesIn == '2') $amount = $amount / 10000;
                        elseif ($salesIn == '3') $amount = $amount / 100000;
                        
                        $itemData[$itemKey]['monthly'][$month] += round($amount, 2);
                    }
                }
                
                $data->push([
                    'salesman_name' => $salesman->name ?? '-',
                    'items' => $itemData
                ]);
            }

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.month-wise.salesman-item-wise-print', compact(
                'data', 'salesmen', 'companies', 'customers', 'areas', 'routes', 'states',
                'yearFrom', 'yearTo', 'salesIn', 'taggedCompanies', 'divisionId', 'companyId',
                'salesmanId', 'customerId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
            ));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.month-wise.salesman-item-wise', compact(
            'salesmen', 'companies', 'customers', 'areas', 'routes', 'states',
            'yearFrom', 'yearTo', 'salesIn', 'taggedCompanies', 'divisionId', 'companyId',
            'salesmanId', 'customerId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Item Invoice Wise
     */
    public function salesmanWiseSalesItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');
        $selectiveItem = $request->get('selective_item', 'Y');
        $itemId = $request->get('item_id');
        $divisionId = $request->get('division_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,code', 'salesman:id,name,code', 'items'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) {
                $query->where('salesman_id', $salesmanId);
            }

            $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

            // Group by salesman, then by item
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    if ($itemId && $item->item_id != $itemId) continue;

                    $data->push([
                        'salesman_name' => $sale->salesman->name ?? '-',
                        'salesman_code' => $sale->salesman->code ?? '-',
                        'invoice_no' => $sale->invoice_no,
                        'invoice_date' => $sale->sale_date,
                        'customer_name' => $sale->customer->name ?? '-',
                        'item_name' => $item->item_name ?? '-',
                        'batch_no' => $withBrExpiry == 'Y' ? ($item->batch_no ?? '-') : '-',
                        'expiry_date' => $withBrExpiry == 'Y' ? ($item->expiry_date ?? '-') : '-',
                        'qty' => $item->qty ?? 0,
                        'free_qty' => $item->free_qty ?? 0,
                        'rate' => $item->rate ?? 0,
                        'amount' => $item->amount ?? 0,
                        'discount' => $item->discount_amount ?? 0,
                        'net_amount' => $item->net_amount ?? 0,
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('export') === 'excel') {
                // Excel export logic here
            }

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.item-invoice-wise-print', compact(
                    'data', 'totals', 'salesmen', 'items', 'divisions', 'dateFrom', 'dateTo', 'salesmanId', 'selectiveItem', 'itemId', 'divisionId', 'withBrExpiry'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.item-invoice-wise', compact(
            'salesmen', 'items', 'divisions', 'dateFrom', 'dateTo', 'salesmanId', 'selectiveItem', 'itemId', 'divisionId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Invoice Item Wise
     */
    public function salesmanWiseSalesInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '3');
        $selective = $request->get('selective', 'Y');
        $salesmanId = $request->get('salesman_id');
        $companyItem = $request->get('company_item', 'C');
        $companyId = $request->get('company_id');
        $divisionId = $request->get('division_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,code,area_code,route_code,state_code', 'salesman:id,name,code', 'items'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->orderBy('salesman_id')->orderBy('sale_date')->orderBy('invoice_no')->get();

            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    if ($companyId && $item->company_id != $companyId) continue;

                    $data->push([
                        'salesman_name' => $sale->salesman->name ?? '-',
                        'invoice_no' => $sale->invoice_no,
                        'invoice_date' => $sale->sale_date,
                        'customer_name' => $sale->customer->name ?? '-',
                        'item_name' => $item->item_name ?? '-',
                        'company_name' => $item->company_name ?? '-',
                        'batch_no' => $withBrExpiry == 'Y' ? ($item->batch_no ?? '-') : '-',
                        'expiry_date' => $withBrExpiry == 'Y' ? ($item->expiry_date ?? '-') : '-',
                        'qty' => $item->qty ?? 0,
                        'free_qty' => $item->free_qty ?? 0,
                        'rate' => $item->rate ?? 0,
                        'amount' => $item->amount ?? 0,
                        'discount' => $item->discount_amount ?? 0,
                        'net_amount' => $item->net_amount ?? 0,
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.invoice-item-wise-print', compact(
                    'data', 'totals', 'salesmen', 'companies', 'areas', 'routes', 'states',
                    'dateFrom', 'dateTo', 'transactionType', 'selective', 'salesmanId', 'companyItem',
                    'companyId', 'divisionId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.invoice-item-wise', compact(
            'salesmen', 'companies', 'areas', 'routes', 'states',
            'dateFrom', 'dateTo', 'transactionType', 'selective', 'salesmanId', 'companyItem',
            'companyId', 'divisionId', 'filterSalesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry'
        ));
    }

    /**
     * Salesman Wise Sales - Month Wise Summary
     */
    public function salesmanWiseSalesMonthWiseSummary(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->format('Y'));
        $yearTo = $request->get('year_to', Carbon::now()->addYear()->format('Y'));
        $salesIn = $request->get('sales_in', '4'); // 1=Thousand, 2=Ten Thousand, 3=Lacs, 4=Actual
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $withDnCn = $request->get('with_dn_cn', 'Y');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = [];

        if ($request->get('view_type') === 'print') {
            $startDate = Carbon::createFromFormat('Y', $yearFrom)->startOfYear();
            $endDate = Carbon::createFromFormat('Y', $yearTo)->endOfYear();

            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code,route_code,state_code'])
                ->whereBetween('sale_date', [$startDate, $endDate]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by salesman and month
            $grouped = $sales->groupBy('salesman_id');
            
            foreach ($grouped as $smanId => $smanSales) {
                $salesman = $smanSales->first()->salesman;
                $monthlyData = [];
                
                for ($m = 1; $m <= 12; $m++) {
                    $monthSales = $smanSales->filter(fn($s) => Carbon::parse($s->sale_date)->month == $m);
                    $amount = $monthSales->sum('net_amount');
                    
                    // Apply sales_in divisor
                    if ($salesIn == '1') $amount = $amount / 1000;
                    elseif ($salesIn == '2') $amount = $amount / 10000;
                    elseif ($salesIn == '3') $amount = $amount / 100000;
                    
                    $monthlyData[$m] = round($amount, 2);
                }
                
                $data->push([
                    'salesman_name' => $salesman->name ?? '-',
                    'salesman_code' => $salesman->code ?? '-',
                    'monthly' => $monthlyData,
                    'total' => array_sum($monthlyData)
                ]);
            }

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.month-wise.month-wise-summary-print', compact(
                'data', 'salesmen', 'areas', 'routes', 'states',
                'yearFrom', 'yearTo', 'salesIn', 'salesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry', 'withDnCn'
            ));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.month-wise.month-wise-summary', compact(
            'salesmen', 'areas', 'routes', 'states',
            'yearFrom', 'yearTo', 'salesIn', 'salesmanId', 'areaId', 'routeId', 'stateId', 'withBrExpiry', 'withDnCn'
        ));
    }

    /**
     * Salesman Wise Sales - Sale Book
     */
    public function salesmanWiseSalesSaleBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '1');
        $selective = $request->get('selective', 'N');
        $salesmanId = $request->get('salesman_id');
        $filterSalesmanId = $request->get('filter_salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bills' => 0, 'gross' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,code,area_code,route_code', 'salesman:id,name,code'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($filterSalesmanId) $query->where('salesman_id', $filterSalesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->orderBy('salesman_id')->orderBy('sale_date')->orderBy('invoice_no')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'salesman_name' => $sale->salesman->name ?? '-',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'gross_amount' => $sale->gross_amount ?? 0,
                    'discount' => $sale->discount_amount ?? 0,
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = [
                'bills' => $data->count(),
                'gross' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.sale-book-print', compact(
                'data', 'totals', 'salesmen', 'areas', 'routes',
                'dateFrom', 'dateTo', 'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId'
            ));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.sale-book', compact(
            'salesmen', 'areas', 'routes',
            'dateFrom', 'dateTo', 'transactionType', 'selective', 'salesmanId', 'filterSalesmanId', 'areaId', 'routeId'
        ));
    }

    /**
     * Salesman Wise Sales - Monthly Target
     */
    public function salesmanWiseSalesMonthlyTarget(Request $request)
    {
        $monthFrom = $request->get('month_from', Carbon::now()->startOfYear()->addMonths(3)->format('Y-m')); // April
        $monthTo = $request->get('month_to', Carbon::now()->format('Y-m'));
        $taggedSalesman = $request->get('tagged_salesman', 'N');
        $removeTagged = $request->get('remove_tagged', 'N');
        $salesmanId = $request->get('salesman_id');
        $salesmanCode = $request->get('salesman_code', '00');
        $targetAmount = $request->get('target_amount');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        $data = collect();
        $totals = ['target' => 0, 'actual' => 0, 'difference' => 0];

        if ($request->get('view_type') === 'print') {
            // Generate month range
            $startDate = Carbon::parse($monthFrom . '-01');
            $endDate = Carbon::parse($monthTo . '-01')->endOfMonth();
            
            $months = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $months[] = $current->format('Y-m');
                $current->addMonth();
            }

            // Get salesmen to report on
            $query = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code');
            if ($salesmanId) {
                $query->where('id', $salesmanId);
            }
            $reportSalesmen = $query->orderBy('name')->get();

            foreach ($reportSalesmen as $salesman) {
                $salesmanData = [
                    'salesman_code' => $salesman->code,
                    'salesman_name' => $salesman->name,
                    'months' => []
                ];

                $totalTarget = 0;
                $totalActual = 0;

                foreach ($months as $month) {
                    $monthStart = Carbon::parse($month . '-01')->startOfMonth();
                    $monthEnd = Carbon::parse($month . '-01')->endOfMonth();

                    // Get actual sales for this salesman in this month
                    $actualSales = SaleTransaction::where('salesman_id', $salesman->id)
                        ->whereBetween('sale_date', [$monthStart, $monthEnd])
                        ->sum('net_amount');

                    // Target would typically come from a targets table, using placeholder
                    $target = $targetAmount ?? 0;

                    $salesmanData['months'][] = [
                        'month' => Carbon::parse($month . '-01')->format('M-Y'),
                        'target' => $target,
                        'actual' => $actualSales,
                        'difference' => $actualSales - $target,
                        'percentage' => $target > 0 ? round(($actualSales / $target) * 100, 2) : 0
                    ];

                    $totalTarget += $target;
                    $totalActual += $actualSales;
                }

                $salesmanData['total_target'] = $totalTarget;
                $salesmanData['total_actual'] = $totalActual;
                $salesmanData['total_difference'] = $totalActual - $totalTarget;

                $data->push($salesmanData);

                $totals['target'] += $totalTarget;
                $totals['actual'] += $totalActual;
            }

            $totals['difference'] = $totals['actual'] - $totals['target'];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.monthly-target-print', compact(
                'data', 'totals', 'months', 'salesmen',
                'monthFrom', 'monthTo', 'taggedSalesman', 'removeTagged', 'salesmanId', 'salesmanCode', 'targetAmount'
            ));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.monthly-target', compact(
            'salesmen', 'monthFrom', 'monthTo', 'taggedSalesman', 'removeTagged', 'salesmanId', 'salesmanCode', 'targetAmount'
        ));
    }

    // ==================== AREA WISE SALES REPORTS ====================

    /**
     * Area Wise Sales - All Area
     */
    public function areaWiseSalesAllArea(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $areaId = $request->get('area_id');
        $salesmanId = $request->get('salesman_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,area_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            // Group by area
            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $data->push([
                    'area_name' => $area->name ?? 'No Area',
                    'bill_count' => $areaSales->count(),
                    'gross_amount' => $areaSales->sum('gross_amount'),
                    'discount' => $areaSales->sum('discount_amount'),
                    'net_amount' => $areaSales->sum('net_amount'),
                ]);
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.all-area-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.all-area', compact(
            'dateFrom', 'dateTo', 'areas', 'salesmen', 'routes', 'states', 'transactionType', 'areaId', 'salesmanId', 'routeId', 'stateId'
        ));
    }

    /**
     * Area Wise Sales - Bill Wise
     */
    public function areaWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $salesmanId = $request->get('salesman_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');
        $customerId = $request->get('customer_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,code,area_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->orderBy('sale_date')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'area_name' => $sale->customer->area->name ?? 'No Area',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'gross_amount' => $sale->gross_amount ?? 0,
                    'discount' => $sale->discount_amount ?? 0,
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = [
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.bill-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.bill-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Customer Wise
     */
    public function areaWiseSalesCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $salesmanId = $request->get('salesman_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,code,area_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $customerGroups = $areaSales->groupBy('customer_id');

                foreach ($customerGroups as $custId => $custSales) {
                    $customer = $custSales->first()->customer;
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'customer_name' => $customer->name ?? '-',
                        'customer_code' => $customer->code ?? '-',
                        'bill_count' => $custSales->count(),
                        'gross_amount' => $custSales->sum('gross_amount'),
                        'discount' => $custSales->sum('discount_amount'),
                        'net_amount' => $custSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.customer-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.customer-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Item Wise
     */
    public function areaWiseSalesItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,area_code', 'customer.area:id,name', 'items.item:id,name,packing'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $itemsCollection = collect();

                foreach ($areaSales as $sale) {
                    foreach ($sale->items as $item) {
                        $itemsCollection->push($item);
                    }
                }

                $itemGroups = $itemsCollection->groupBy('item_id');

                foreach ($itemGroups as $itemId => $itemRecords) {
                    $firstItem = $itemRecords->first();
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'item_name' => $firstItem->item_name ?? ($firstItem->item->name ?? '-'),
                        'packing' => $firstItem->item->packing ?? '-',
                        'qty' => $itemRecords->sum('qty'),
                        'free_qty' => $itemRecords->sum('free_qty'),
                        'amount' => $itemRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.item-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.item-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'items'
        ));
    }

    /**
     * Area Wise Sales - Company Wise
     */
    public function areaWiseSalesCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $companyId = $request->get('company_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['item_count' => 0, 'qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,area_code', 'customer.area:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $companyItems = collect();

                foreach ($areaSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $companyItems->push($item);
                    }
                }

                $companyGroups = $companyItems->groupBy(fn($i) => $i->item->company_id ?? 0);

                foreach ($companyGroups as $compId => $compRecords) {
                    $firstItem = $compRecords->first();
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'company_name' => $firstItem->item->company->name ?? '-',
                        'item_count' => $compRecords->unique('item_id')->count(),
                        'qty' => $compRecords->sum('qty'),
                        'amount' => $compRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'item_count' => $data->sum('item_count'),
                'qty' => $data->sum('qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.company-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.company-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'companies', 'divisions', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Salesman Wise
     */
    public function areaWiseSalesSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $salesmanId = $request->get('salesman_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,area_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $salesmanGroups = $areaSales->groupBy('salesman_id');

                foreach ($salesmanGroups as $smanId => $smanSales) {
                    $salesman = $smanSales->first()->salesman;
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'bill_count' => $smanSales->count(),
                        'gross_amount' => $smanSales->sum('gross_amount'),
                        'discount' => $smanSales->sum('discount_amount'),
                        'net_amount' => $smanSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.salesman-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.salesman-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Route Wise
     */
    public function areaWiseSalesRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,area_code,route_code', 'customer.area:id,name', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $routeGroups = $areaSales->groupBy(fn($s) => $s->customer->route_code ?? 0);

                foreach ($routeGroups as $rCode => $routeSales) {
                    $route = $routeSales->first()->customer->route ?? null;
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'route_name' => $route->name ?? 'No Route',
                        'bill_count' => $routeSales->count(),
                        'gross_amount' => $routeSales->sum('gross_amount'),
                        'discount' => $routeSales->sum('discount_amount'),
                        'net_amount' => $routeSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.route-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.route-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - State Wise
     */
    public function areaWiseSalesStateWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $stateId = $request->get('state_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,area_code,state_code', 'customer.area:id,name', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);

            foreach ($grouped as $aCode => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $stateGroups = $areaSales->groupBy(fn($s) => $s->customer->state_code ?? 0);

                foreach ($stateGroups as $sCode => $stateSales) {
                    $state = $stateSales->first()->customer->state ?? null;
                    $data->push([
                        'area_name' => $area->name ?? 'No Area',
                        'state_name' => $state->name ?? 'No State',
                        'bill_count' => $stateSales->count(),
                        'gross_amount' => $stateSales->sum('gross_amount'),
                        'discount' => $stateSales->sum('discount_amount'),
                        'net_amount' => $stateSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.state-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.state-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Item Invoice Wise
     */
    public function areaWiseSalesItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.item-invoice-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'companies', 'divisions', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Invoice Item Wise
     */
    public function areaWiseSalesInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.invoice-item-wise', compact(
            'dateFrom', 'dateTo', 'areas', 'items'
        ));
    }

    /**
     * Area Wise Sales - Sale Book
     */
    public function areaWiseSalesSaleBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $salesmanId = $request->get('salesman_id');

        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name', 'customer:id,name,area_code', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));

            $sales = $query->orderBy('sale_date')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'area_name' => $sale->customer->area->name ?? 'No Area',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'salesman_name' => $sale->salesman->name ?? '-',
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = ['net_amount' => $data->sum('net_amount')];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.sale-book-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.sale-book', compact(
            'dateFrom', 'dateTo', 'areas', 'salesmen', 'routes'
        ));
    }

    /**
     * Area Wise Sales - Month Wise Area Wise
     */
    public function areaWiseSalesMonthWiseAreaWise(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->year);
        $yearTo = $request->get('year_to', Carbon::now()->year + 1);
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.month-wise.area-wise', compact(
            'yearFrom', 'yearTo', 'areas', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Month Wise Area Item Wise
     */
    public function areaWiseSalesMonthWiseAreaItemWise(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->year);
        $yearTo = $request->get('year_to', Carbon::now()->year + 1);
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.month-wise.area-item-wise', compact(
            'yearFrom', 'yearTo', 'areas', 'companies', 'customers', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Route Wise Sale - All Route
     */
    public function routeWiseSaleAllRoute(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $stateId = $request->get('state_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $data->push([
                    'route_name' => $route->name ?? 'No Route',
                    'bill_count' => $routeSales->count(),
                    'gross_amount' => $routeSales->sum('gross_amount'),
                    'discount' => $routeSales->sum('discount_amount'),
                    'net_amount' => $routeSales->sum('net_amount'),
                ]);
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.all-route-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.all-route', compact(
            'dateFrom', 'dateTo', 'routes', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Bill Wise
     */
    public function routeWiseSaleBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $salesmanId = $request->get('salesman_id');
        $customerId = $request->get('customer_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->orderBy('sale_date')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'route_name' => $sale->customer->route->name ?? 'No Route',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'gross_amount' => $sale->gross_amount ?? 0,
                    'discount' => $sale->discount_amount ?? 0,
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = [
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.bill-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.bill-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'customers', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Customer Wise
     */
    public function routeWiseSaleCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $salesmanId = $request->get('salesman_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,code,route_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $customerGroups = $routeSales->groupBy('customer_id');

                foreach ($customerGroups as $custId => $custSales) {
                    $customer = $custSales->first()->customer;
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'customer_name' => $customer->name ?? '-',
                        'customer_code' => $customer->code ?? '-',
                        'bill_count' => $custSales->count(),
                        'gross_amount' => $custSales->sum('gross_amount'),
                        'discount' => $custSales->sum('discount_amount'),
                        'net_amount' => $custSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.customer-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.customer-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'customers', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Item Wise
     */
    public function routeWiseSaleItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code', 'customer.route:id,name', 'items.item:id,name,packing'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $itemsCollection = collect();

                foreach ($routeSales as $sale) {
                    foreach ($sale->items as $item) {
                        $itemsCollection->push($item);
                    }
                }

                $itemGroups = $itemsCollection->groupBy('item_id');

                foreach ($itemGroups as $itemId => $itemRecords) {
                    $firstItem = $itemRecords->first();
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'item_name' => $firstItem->item_name ?? ($firstItem->item->name ?? '-'),
                        'packing' => $firstItem->item->packing ?? '-',
                        'qty' => $itemRecords->sum('qty'),
                        'free_qty' => $itemRecords->sum('free_qty'),
                        'amount' => $itemRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.item-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.item-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'items', 'companies', 'divisions', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Company Wise
     */
    public function routeWiseSaleCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $companyId = $request->get('company_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['item_count' => 0, 'qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code', 'customer.route:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $companyItems = collect();

                foreach ($routeSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $companyItems->push($item);
                    }
                }

                $companyGroups = $companyItems->groupBy(fn($i) => $i->item->company_id ?? 0);

                foreach ($companyGroups as $compId => $compRecords) {
                    $firstItem = $compRecords->first();
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'company_name' => $firstItem->item->company->name ?? '-',
                        'item_count' => $compRecords->unique('item_id')->count(),
                        'qty' => $compRecords->sum('qty'),
                        'amount' => $compRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'item_count' => $data->sum('item_count'),
                'qty' => $data->sum('qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.company-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.company-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'companies', 'divisions', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Salesman Wise
     */
    public function routeWiseSaleSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $salesmanId = $request->get('salesman_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name,code', 'customer:id,name,route_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $salesmanGroups = $routeSales->groupBy('salesman_id');

                foreach ($salesmanGroups as $smanId => $smanSales) {
                    $salesman = $smanSales->first()->salesman;
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'salesman_name' => $salesman->name ?? '-',
                        'salesman_code' => $salesman->code ?? '-',
                        'bill_count' => $smanSales->count(),
                        'gross_amount' => $smanSales->sum('gross_amount'),
                        'discount' => $smanSales->sum('discount_amount'),
                        'net_amount' => $smanSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.salesman-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.salesman-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'customers', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Area Wise
     */
    public function routeWiseSaleAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $areaId = $request->get('area_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code,area_code', 'customer.route:id,name', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $areaGroups = $routeSales->groupBy(fn($s) => $s->customer->area_code ?? 0);

                foreach ($areaGroups as $aCode => $areaSales) {
                    $area = $areaSales->first()->customer->area ?? null;
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'area_name' => $area->name ?? 'No Area',
                        'bill_count' => $areaSales->count(),
                        'gross_amount' => $areaSales->sum('gross_amount'),
                        'discount' => $areaSales->sum('discount_amount'),
                        'net_amount' => $areaSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.area-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.area-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'salesmen', 'areas', 'states', 'customers'
        ));
    }

    /**
     * Route Wise Sale - State Wise
     */
    public function routeWiseSaleStateWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,route_code,state_code', 'customer.route:id,name', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->route_code ?? 0);

            foreach ($grouped as $rCode => $routeSales) {
                $route = $routeSales->first()->customer->route ?? null;
                $stateGroups = $routeSales->groupBy(fn($s) => $s->customer->state_code ?? 0);

                foreach ($stateGroups as $sCode => $stateSales) {
                    $state = $stateSales->first()->customer->state ?? null;
                    $data->push([
                        'route_name' => $route->name ?? 'No Route',
                        'state_name' => $state->name ?? 'No State',
                        'bill_count' => $stateSales->count(),
                        'gross_amount' => $stateSales->sum('gross_amount'),
                        'discount' => $stateSales->sum('discount_amount'),
                        'net_amount' => $stateSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.state-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.state-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'customers', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Item Invoice Wise
     */
    public function routeWiseSaleItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.item-invoice-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'items'
        ));
    }

    /**
     * Route Wise Sale - Invoice Item Wise
     */
    public function routeWiseSaleInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.invoice-item-wise', compact(
            'dateFrom', 'dateTo', 'routes', 'companies', 'divisions', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Sale Book
     */
    public function routeWiseSaleSaleBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $routeId = $request->get('route_id');
        $salesmanId = $request->get('salesman_id');

        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['salesman:id,name', 'customer:id,name,route_code', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));

            $sales = $query->orderBy('sale_date')->get();

            foreach ($sales as $sale) {
                $data->push([
                    'route_name' => $sale->customer->route->name ?? 'No Route',
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? '-',
                    'salesman_name' => $sale->salesman->name ?? '-',
                    'net_amount' => $sale->net_amount ?? 0,
                ]);
            }

            $totals = ['net_amount' => $data->sum('net_amount')];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.sale-book-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.sale-book', compact(
            'dateFrom', 'dateTo', 'routes', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Month Wise Route Wise
     */
    public function routeWiseSaleMonthWiseRouteWise(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->year);
        $yearTo = $request->get('year_to', Carbon::now()->year + 1);
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.month-wise.route-wise', compact(
            'yearFrom', 'yearTo', 'routes', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * Route Wise Sale - Month Wise Route Item Wise
     */
    public function routeWiseSaleMonthWiseRouteItemWise(Request $request)
    {
        $yearFrom = $request->get('year_from', Carbon::now()->year);
        $yearTo = $request->get('year_to', Carbon::now()->year + 1);
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.route-wise-sale.month-wise.route-item-wise', compact(
            'yearFrom', 'yearTo', 'routes', 'companies', 'customers', 'salesmen', 'areas', 'states'
        ));
    }

    /**
     * State Wise Sale - All State
     */
    public function stateWiseSaleAllState(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $salesmanId = $request->get('salesman_id');
        $salesmanCode = $request->get('salesman_code', '00');
        $areaId = $request->get('area_id');
        $areaCode = $request->get('area_code', '00');
        $routeId = $request->get('route_id');
        $routeCode = $request->get('route_code', '00');
        $stateId = $request->get('state_id');
        $stateCode = $request->get('state_code', '00');
        $orderBy = $request->get('order_by', 'N');
        $orderDir = $request->get('order_dir', 'A');
        $withBrExpiry = $request->get('with_br_expiry', 'N');
        $series = $request->get('series', '00');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($salesmanId) $query->where('salesman_id', $salesmanId);
            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($areaId) $query->whereHas('customer', fn($q) => $q->where('area_code', $areaId));
            if ($routeId) $query->whereHas('customer', fn($q) => $q->where('route_code', $routeId));
            if ($series && $series !== '00') $query->where('series', $series);

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $data->push([
                    'state_name' => $state->name ?? 'No State',
                    'bill_count' => $stateSales->count(),
                    'gross_amount' => $stateSales->sum('gross_amount'),
                    'discount' => $stateSales->sum('discount_amount'),
                    'net_amount' => $stateSales->sum('net_amount'),
                ]);
            }

            // Apply ordering
            if ($orderBy === 'N') {
                $data = $orderDir === 'A' ? $data->sortBy('state_name') : $data->sortByDesc('state_name');
            } else {
                $data = $orderDir === 'A' ? $data->sortBy('net_amount') : $data->sortByDesc('net_amount');
            }
            $data = $data->values();

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.all-state-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo', 'transactionType'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.all-state', compact(
            'dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes',
            'stateId', 'stateCode', 'salesmanId', 'salesmanCode', 'areaId', 'areaCode',
            'routeId', 'routeCode', 'transactionType', 'orderBy', 'orderDir', 'withBrExpiry', 'series'
        ));
    }

    /**
     * State Wise Sale - Bill Wise
     */
    public function stateWiseSaleBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $stateId = $request->get('state_id');
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'salesman:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($customerId) $query->where('customer_id', $customerId);
            if ($salesmanId) $query->where('salesman_id', $salesmanId);

            $sales = $query->orderBy('sale_date')->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                foreach ($stateSales as $sale) {
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'invoice_no' => $sale->invoice_no,
                        'invoice_date' => $sale->sale_date,
                        'customer_name' => $sale->customer->name ?? '-',
                        'salesman_name' => $sale->salesman->name ?? '-',
                        'gross_amount' => $sale->gross_amount ?? 0,
                        'discount' => $sale->discount_amount ?? 0,
                        'net_amount' => $sale->net_amount ?? 0,
                    ]);
                }
            }

            $totals = [
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.bill-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.bill-wise', compact(
            'dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'
        ));
    }

    /**
     * State Wise Sale - Customer Wise
     */
    public function stateWiseSaleCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '5');
        $stateId = $request->get('state_id');
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($customerId) $query->where('customer_id', $customerId);
            if ($salesmanId) $query->where('salesman_id', $salesmanId);

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $customerGroups = $stateSales->groupBy('customer_id');

                foreach ($customerGroups as $custId => $custSales) {
                    $customer = $custSales->first()->customer;
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'customer_name' => $customer->name ?? '-',
                        'bill_count' => $custSales->count(),
                        'gross_amount' => $custSales->sum('gross_amount'),
                        'discount' => $custSales->sum('discount_amount'),
                        'net_amount' => $custSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = [
                'bill_count' => $data->sum('bill_count'),
                'gross_amount' => $data->sum('gross_amount'),
                'discount' => $data->sum('discount'),
                'net_amount' => $data->sum('net_amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.customer-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.customer-wise', compact(
            'dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'
        ));
    }

    /**
     * State Wise Sale - Item Wise
     */
    public function stateWiseSaleItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '3');
        $stateId = $request->get('state_id');
        $companyId = $request->get('company_id');
        $salesmanId = $request->get('salesman_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $categories = ItemCategory::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print' || $request->has('export')) {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'items.item:id,name,packing,company_id'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));
            if ($salesmanId) $query->where('salesman_id', $salesmanId);

            $sales = $query->get();

            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $itemsCollection = collect();

                foreach ($stateSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $itemsCollection->push($item);
                    }
                }

                $itemGroups = $itemsCollection->groupBy('item_id');

                foreach ($itemGroups as $itemId => $itemRecords) {
                    $firstItem = $itemRecords->first();
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'item_name' => $firstItem->item_name ?? ($firstItem->item->name ?? '-'),
                        'packing' => $firstItem->item->packing ?? '-',
                        'qty' => $itemRecords->sum('qty'),
                        'free_qty' => $itemRecords->sum('free_qty'),
                        'amount' => $itemRecords->sum('amount'),
                    ]);
                }
            }

            $totals = [
                'qty' => $data->sum('qty'),
                'free_qty' => $data->sum('free_qty'),
                'amount' => $data->sum('amount'),
            ];

            if ($request->get('view_type') === 'print') {
                return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.item-wise-print', compact(
                    'data', 'totals', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.item-wise', compact(
            'dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'companies', 'divisions', 'categories'
        ));
    }

    /**
     * State Wise Sale - Company Wise
     */
    public function stateWiseSaleCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');
        $companyId = $request->get('company_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $companyData = collect();

                foreach ($stateSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $compKey = $item->item->company_id ?? 0;
                        if (!isset($companyData[$compKey])) {
                            $companyData[$compKey] = ['name' => $item->item->company->name ?? 'No Company', 'qty' => 0, 'free_qty' => 0, 'amount' => 0];
                        }
                        $companyData[$compKey]['qty'] += $item->qty ?? 0;
                        $companyData[$compKey]['free_qty'] += $item->free_qty ?? 0;
                        $companyData[$compKey]['amount'] += $item->amount ?? 0;
                    }
                }

                foreach ($companyData as $comp) {
                    $data->push(['state_name' => $state->name ?? 'No State', 'company_name' => $comp['name'], 'qty' => $comp['qty'], 'free_qty' => $comp['free_qty'], 'amount' => $comp['amount']]);
                }
            }

            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.company-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.company-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'companies'));
    }

    /**
     * State Wise Sale - Salesman Wise
     */
    public function stateWiseSaleSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'salesman:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $salesmanGroups = $stateSales->groupBy('salesman_id');

                foreach ($salesmanGroups as $smId => $smSales) {
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'salesman_name' => $smSales->first()->salesman->name ?? 'No Salesman',
                        'bill_count' => $smSales->count(),
                        'gross_amount' => $smSales->sum('gross_amount'),
                        'discount' => $smSales->sum('discount_amount'),
                        'net_amount' => $smSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = ['bill_count' => $data->sum('bill_count'), 'gross_amount' => $data->sum('gross_amount'), 'discount' => $data->sum('discount'), 'net_amount' => $data->sum('net_amount')];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.salesman-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.salesman-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    /**
     * State Wise Sale - Area Wise
     */
    public function stateWiseSaleAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,state_code,area_code', 'customer.state:id,name', 'customer.area:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $areaGroups = $stateSales->groupBy(fn($s) => $s->customer->area_code ?? 0);

                foreach ($areaGroups as $areaId => $areaSales) {
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'area_name' => $areaSales->first()->customer->area->name ?? 'No Area',
                        'bill_count' => $areaSales->count(),
                        'gross_amount' => $areaSales->sum('gross_amount'),
                        'discount' => $areaSales->sum('discount_amount'),
                        'net_amount' => $areaSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = ['bill_count' => $data->sum('bill_count'), 'gross_amount' => $data->sum('gross_amount'), 'discount' => $data->sum('discount'), 'net_amount' => $data->sum('net_amount')];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.area-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.area-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    /**
     * State Wise Sale - Route Wise
     */
    public function stateWiseSaleRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,state_code,route_code', 'customer.state:id,name', 'customer.route:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $routeGroups = $stateSales->groupBy(fn($s) => $s->customer->route_code ?? 0);

                foreach ($routeGroups as $routeId => $routeSales) {
                    $data->push([
                        'state_name' => $state->name ?? 'No State',
                        'route_name' => $routeSales->first()->customer->route->name ?? 'No Route',
                        'bill_count' => $routeSales->count(),
                        'gross_amount' => $routeSales->sum('gross_amount'),
                        'discount' => $routeSales->sum('discount_amount'),
                        'net_amount' => $routeSales->sum('net_amount'),
                    ]);
                }
            }

            $totals = ['bill_count' => $data->sum('bill_count'), 'gross_amount' => $data->sum('gross_amount'), 'discount' => $data->sum('discount'), 'net_amount' => $data->sum('net_amount')];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.route-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.route-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    /**
     * State Wise Sale - Invoice Item Wise
     */
    public function stateWiseSaleInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $stateId = $request->get('state_id');
        $companyId = $request->get('company_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'items.item:id,name,packing,company_id'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->orderBy('sale_date')->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;

                foreach ($stateSales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $data->push([
                            'state_name' => $state->name ?? 'No State',
                            'invoice_no' => $sale->invoice_no,
                            'customer_name' => $sale->customer->name ?? '-',
                            'item_name' => $item->item_name ?? ($item->item->name ?? '-'),
                            'packing' => $item->item->packing ?? '-',
                            'qty' => $item->qty ?? 0,
                            'free_qty' => $item->free_qty ?? 0,
                            'rate' => $item->rate ?? 0,
                            'amount' => $item->amount ?? 0,
                        ]);
                    }
                }
            }

            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.invoice-item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.invoice-item-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'companies'));
    }

    /**
     * State Wise Sale - Month Wise State
     */
    public function stateWiseSaleMonthWiseState(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '4');
        $stateId = $request->get('state_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Actual';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01';
            $dateTo = $yearTo . '-03-31';

            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $monthData = ['state_name' => $state->name ?? 'No State', 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];

                foreach ($stateSales as $sale) {
                    $month = Carbon::parse($sale->sale_date)->format('M');
                    $monthKey = strtolower($month);
                    if (isset($monthData[$monthKey])) {
                        $monthData[$monthKey] += ($sale->net_amount ?? 0) / $divisor;
                    }
                    $monthData['total'] += ($sale->net_amount ?? 0) / $divisor;
                }
                $data->push($monthData);
            }

            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) {
                $totals[$m] = $data->sum($m);
            }

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.month-wise.state-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.month-wise.state-wise', compact('yearFrom', 'yearTo', 'salesIn', 'states', 'salesmen', 'areas', 'routes'));
    }

    /**
     * State Wise Sale - Month Wise State Item
     */
    public function stateWiseSaleMonthWiseStateItem(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '1');
        $stateId = $request->get('state_id');
        $companyId = $request->get('company_id');

        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        $data = collect();
        $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Thousand';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1000;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01';
            $dateTo = $yearTo . '-03-31';

            $query = SaleTransaction::with(['customer:id,name,state_code', 'customer.state:id,name', 'items.item:id,name,company_id'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);

            if ($stateId) $query->whereHas('customer', fn($q) => $q->where('state_code', $stateId));

            $sales = $query->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->state_code ?? 0);

            foreach ($grouped as $sCode => $stateSales) {
                $state = $stateSales->first()->customer->state ?? null;
                $itemsGrouped = [];

                foreach ($stateSales as $sale) {
                    $month = Carbon::parse($sale->sale_date)->format('M');
                    $monthKey = strtolower($month);

                    foreach ($sale->items as $item) {
                        if ($companyId && $item->item && $item->item->company_id != $companyId) continue;
                        $itemId = $item->item_id ?? 0;
                        if (!isset($itemsGrouped[$itemId])) {
                            $itemsGrouped[$itemId] = ['item_name' => $item->item_name ?? ($item->item->name ?? '-'), 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
                        }
                        if (isset($itemsGrouped[$itemId][$monthKey])) {
                            $itemsGrouped[$itemId][$monthKey] += ($item->amount ?? 0) / $divisor;
                        }
                        $itemsGrouped[$itemId]['total'] += ($item->amount ?? 0) / $divisor;
                    }
                }

                foreach ($itemsGrouped as $itemData) {
                    $data->push(array_merge(['state_name' => $state->name ?? 'No State'], $itemData));
            }
        }

            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) {
                $totals[$m] = $data->sum($m);
            }

            return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.month-wise.state-item-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.state-wise-sale.month-wise.state-item-wise', compact('yearFrom', 'yearTo', 'salesIn', 'states', 'salesmen', 'areas', 'routes', 'companies', 'customers'));
    }

    // =============================================
    // CUSTOMER WISE SALE REPORTS
    // =============================================

    public function customerWiseSaleAllCustomer(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['bill_count' => 0, 'gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with('customer:id,name')->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $data->push(['customer_name' => $customer->name ?? '-', 'bill_count' => $custSales->count(), 'gross_amount' => $custSales->sum('gross_amount'), 'discount' => $custSales->sum('discount_amount'), 'net_amount' => $custSales->sum('net_amount')]);
            }
            $totals = ['bill_count' => $data->sum('bill_count'), 'gross_amount' => $data->sum('gross_amount'), 'discount' => $data->sum('discount'), 'net_amount' => $data->sum('net_amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.all-customer-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.all-customer', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSaleBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['gross_amount' => 0, 'discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with('customer:id,name')->whereBetween('sale_date', [$dateFrom, $dateTo])->orderBy('customer_id')->get();
            foreach ($sales as $sale) {
                $data->push(['customer_name' => $sale->customer->name ?? '-', 'invoice_no' => $sale->invoice_no, 'invoice_date' => $sale->sale_date, 'gross_amount' => $sale->gross_amount ?? 0, 'discount' => $sale->discount_amount ?? 0, 'net_amount' => $sale->net_amount ?? 0]);
            }
            $totals = ['gross_amount' => $data->sum('gross_amount'), 'discount' => $data->sum('discount'), 'net_amount' => $data->sum('net_amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.bill-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.bill-wise', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSaleItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,packing'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $itemsGrouped = [];
                foreach ($custSales as $sale) {
                    foreach ($sale->items as $item) {
                        $itemId = $item->item_id ?? 0;
                        if (!isset($itemsGrouped[$itemId])) { $itemsGrouped[$itemId] = ['item_name' => $item->item->name ?? '-', 'packing' => $item->item->packing ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                        $itemsGrouped[$itemId]['qty'] += $item->qty ?? 0;
                        $itemsGrouped[$itemId]['free_qty'] += $item->free_qty ?? 0;
                        $itemsGrouped[$itemId]['amount'] += $item->amount ?? 0;
                    }
                }
                foreach ($itemsGrouped as $itemData) { $data->push(array_merge(['customer_name' => $customer->name ?? '-'], $itemData)); }
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.item-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers', 'companies'));
    }

    public function customerWiseSaleCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $companyData = [];
                foreach ($custSales as $sale) {
                    foreach ($sale->items as $item) {
                        $compId = $item->item->company_id ?? 0;
                        if (!isset($companyData[$compId])) { $companyData[$compId] = ['company_name' => $item->item->company->name ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                        $companyData[$compId]['qty'] += $item->qty ?? 0;
                        $companyData[$compId]['free_qty'] += $item->free_qty ?? 0;
                        $companyData[$compId]['amount'] += $item->amount ?? 0;
                    }
                }
                foreach ($companyData as $cData) { $data->push(array_merge(['customer_name' => $customer->name ?? '-'], $cData)); }
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.company-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.company-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers', 'companies'));
    }

    public function customerWiseSaleItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,packing'])->whereBetween('sale_date', [$dateFrom, $dateTo])->orderBy('customer_id')->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $itemsGrouped = [];
                foreach ($custSales as $sale) {
                    foreach ($sale->items as $item) {
                        $key = ($item->item_id ?? 0) . '-' . $sale->id;
                        if (!isset($itemsGrouped[$key])) { $itemsGrouped[$key] = ['item_name' => $item->item->name ?? '-', 'invoice_no' => $sale->invoice_no, 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                        $itemsGrouped[$key]['qty'] += $item->qty ?? 0;
                        $itemsGrouped[$key]['free_qty'] += $item->free_qty ?? 0;
                        $itemsGrouped[$key]['amount'] += $item->amount ?? 0;
                    }
                }
                foreach ($itemsGrouped as $itemData) { $data->push(array_merge(['customer_name' => $customer->name ?? '-'], $itemData)); }
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.item-invoice-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.item-invoice-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers', 'companies'));
    }

    public function customerWiseSaleInvoiceItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,packing'])->whereBetween('sale_date', [$dateFrom, $dateTo])->orderBy('customer_id')->get();
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $data->push(['customer_name' => $sale->customer->name ?? '-', 'invoice_no' => $sale->invoice_no, 'item_name' => $item->item->name ?? '-', 'packing' => $item->item->packing ?? '-', 'qty' => $item->qty ?? 0, 'free_qty' => $item->free_qty ?? 0, 'rate' => $item->rate ?? 0, 'amount' => $item->amount ?? 0]);
                }
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.invoice-item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.invoice-item-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers', 'companies'));
    }

    public function customerWiseSaleQtySummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $qty = 0; $freeQty = 0; $amount = 0;
                foreach ($custSales as $sale) {
                    foreach ($sale->items as $item) { $qty += $item->qty ?? 0; $freeQty += $item->free_qty ?? 0; $amount += $item->amount ?? 0; }
                }
                $data->push(['customer_name' => $customer->name ?? '-', 'qty' => $qty, 'free_qty' => $freeQty, 'amount' => $amount]);
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.quantity-wise-summary-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.quantity-wise-summary', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSalePartyVolumeDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['gross_amount' => 0, 'volume_discount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with('customer:id,name')->whereBetween('sale_date', [$dateFrom, $dateTo])->orderBy('customer_id')->get();
            foreach ($sales as $sale) {
                $data->push(['customer_name' => $sale->customer->name ?? '-', 'invoice_no' => $sale->invoice_no, 'gross_amount' => $sale->gross_amount ?? 0, 'volume_discount' => $sale->volume_discount ?? 0, 'net_amount' => $sale->net_amount ?? 0]);
            }
            $totals = ['gross_amount' => $data->sum('gross_amount'), 'volume_discount' => $data->sum('volume_discount'), 'net_amount' => $data->sum('net_amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.party-billwise-volume-discount-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.party-billwise-volume-discount', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSaleWithArea(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['bill_count' => 0, 'gross_amount' => 0, 'net_amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name,area_code', 'customer.area:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy(fn($s) => $s->customer->area_code ?? 0);
            foreach ($grouped as $areaId => $areaSales) {
                $area = $areaSales->first()->customer->area ?? null;
                $customerGroups = $areaSales->groupBy('customer_id');
                foreach ($customerGroups as $custId => $custSales) {
                    $customer = $custSales->first()->customer;
                    $data->push(['area_name' => $area->name ?? 'No Area', 'customer_name' => $customer->name ?? '-', 'bill_count' => $custSales->count(), 'gross_amount' => $custSales->sum('gross_amount'), 'net_amount' => $custSales->sum('net_amount')]);
                }
            }
            $totals = ['bill_count' => $data->sum('bill_count'), 'gross_amount' => $data->sum('gross_amount'), 'net_amount' => $data->sum('net_amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.sale-with-area-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.sale-with-area', compact('dateFrom', 'dateTo', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSaleMonthWiseCustomer(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '4');
        $states = State::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Actual';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01'; $dateTo = $yearTo . '-03-31';
            $sales = SaleTransaction::with('customer:id,name')->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer;
                $monthData = ['customer_name' => $customer->name ?? '-', 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
                foreach ($custSales as $sale) { $monthKey = strtolower(Carbon::parse($sale->sale_date)->format('M')); if (isset($monthData[$monthKey])) { $monthData[$monthKey] += ($sale->net_amount ?? 0) / $divisor; } $monthData['total'] += ($sale->net_amount ?? 0) / $divisor; }
                $data->push($monthData);
            }
            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) { $totals[$m] = $data->sum($m); }
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.month-wise.customer-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.month-wise.customer-wise', compact('yearFrom', 'yearTo', 'salesIn', 'states', 'salesmen', 'areas', 'routes', 'customers'));
    }

    public function customerWiseSaleMonthWiseCustomerItem(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '1');
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Thousand';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1000;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01'; $dateTo = $yearTo . '-03-31';
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = $sales->groupBy('customer_id');
            foreach ($grouped as $custId => $custSales) {
                $customer = $custSales->first()->customer; $itemsGrouped = [];
                foreach ($custSales as $sale) { $monthKey = strtolower(Carbon::parse($sale->sale_date)->format('M'));
                    foreach ($sale->items as $item) { $itemId = $item->item_id ?? 0; if (!isset($itemsGrouped[$itemId])) { $itemsGrouped[$itemId] = ['item_name' => $item->item->name ?? '-', 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0]; } if (isset($itemsGrouped[$itemId][$monthKey])) { $itemsGrouped[$itemId][$monthKey] += ($item->amount ?? 0) / $divisor; } $itemsGrouped[$itemId]['total'] += ($item->amount ?? 0) / $divisor; } }
                foreach ($itemsGrouped as $itemData) { $data->push(array_merge(['customer_name' => $customer->name ?? '-'], $itemData)); }
            }
            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) { $totals[$m] = $data->sum($m); }
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.month-wise.customer-item-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.customer-wise-sale.month-wise.customer-item-wise', compact('yearFrom', 'yearTo', 'salesIn', 'salesmen', 'areas', 'routes', 'customers', 'companies'));
    }

    // =============================================
    // COMPANY WISE SALES REPORTS
    // =============================================

    public function companyWiseSalesAllCompany(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $companyData = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $compId = $item->item->company_id ?? 0;
                    if (!isset($companyData[$compId])) { $companyData[$compId] = ['company_name' => $item->item->company->name ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $companyData[$compId]['qty'] += $item->qty ?? 0;
                    $companyData[$compId]['free_qty'] += $item->free_qty ?? 0;
                    $companyData[$compId]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($companyData as $cData) { $data->push($cData); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.all-company-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.all-company', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . $sale->id;
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'invoice_no' => $sale->invoice_no, 'invoice_date' => $sale->sale_date, 'customer_name' => $sale->customer->name ?? '-', 'amount' => 0]; }
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.bill-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.bill-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['items.item:id,name,packing,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($item->item_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'item_name' => $item->item->name ?? '-', 'packing' => $item->item->packing ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.item-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['salesman:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($sale->salesman_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'salesman_name' => $sale->salesman->name ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.salesman-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.salesman-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name,area_code', 'customer.area:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($sale->customer->area_code ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'area_name' => $sale->customer->area->name ?? 'No Area', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.area-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.area-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name,route_code', 'customer.route:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($sale->customer->route_code ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'route_name' => $sale->customer->route->name ?? 'No Route', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.route-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.route-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($sale->customer_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'customer_name' => $sale->customer->name ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies', 'customers'));
    }

    public function companyWiseSalesCustomerItemInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $data->push(['company_name' => $item->item->company->name ?? '-', 'customer_name' => $sale->customer->name ?? '-', 'item_name' => $item->item->name ?? '-', 'invoice_no' => $sale->invoice_no, 'qty' => $item->qty ?? 0, 'free_qty' => $item->free_qty ?? 0, 'amount' => $item->amount ?? 0]);
                }
            }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-item-invoice-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-item-invoice-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies', 'customers'));
    }

    public function companyWiseSalesCustomerItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];

        if ($request->get('view_type') === 'print') {
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,packing,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $key = ($item->item->company_id ?? 0) . '-' . ($sale->customer_id ?? 0) . '-' . ($item->item_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'customer_name' => $sale->customer->name ?? '-', 'item_name' => $item->item->name ?? '-', 'packing' => $item->item->packing ?? '-', 'qty' => 0, 'free_qty' => 0, 'amount' => 0]; }
                    $grouped[$key]['qty'] += $item->qty ?? 0;
                    $grouped[$key]['free_qty'] += $item->free_qty ?? 0;
                    $grouped[$key]['amount'] += $item->amount ?? 0;
                }
            }
            foreach ($grouped as $row) { $data->push($row); }
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.customer-item-wise', compact('dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'companies', 'customers'));
    }

    public function companyWiseSalesMonthWiseCompanyItem(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '1');
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Thousand';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1000;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01'; $dateTo = $yearTo . '-03-31';
            $sales = SaleTransaction::with(['items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) { $monthKey = strtolower(Carbon::parse($sale->sale_date)->format('M'));
                foreach ($sale->items as $item) { $key = ($item->item->company_id ?? 0) . '-' . ($item->item_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'item_name' => $item->item->name ?? '-', 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0]; }
                    if (isset($grouped[$key][$monthKey])) { $grouped[$key][$monthKey] += ($item->amount ?? 0) / $divisor; } $grouped[$key]['total'] += ($item->amount ?? 0) / $divisor; } }
            foreach ($grouped as $row) { $data->push($row); }
            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) { $totals[$m] = $data->sum($m); }
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.month-wise.company-item-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.month-wise.company-item-wise', compact('yearFrom', 'yearTo', 'salesIn', 'salesmen', 'areas', 'routes', 'companies'));
    }

    public function companyWiseSalesMonthWiseCompanyCustomer(Request $request)
    {
        $yearFrom = $request->get('year_from', date('Y'));
        $yearTo = $request->get('year_to', date('Y'));
        $salesIn = $request->get('sales_in', '1');
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $data = collect(); $totals = ['apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0];
        $salesInLabel = ['1' => 'Thousand', '2' => 'Ten Thousand', '3' => 'Lacs', '4' => 'Actual'][$salesIn] ?? 'Thousand';
        $divisor = ['1' => 1000, '2' => 10000, '3' => 100000, '4' => 1][$salesIn] ?? 1000;

        if ($request->get('view_type') === 'print') {
            $dateFrom = $yearFrom . '-04-01'; $dateTo = $yearTo . '-03-31';
            $sales = SaleTransaction::with(['customer:id,name', 'items.item:id,name,company_id', 'items.item.company:id,name'])->whereBetween('sale_date', [$dateFrom, $dateTo])->get();
            $grouped = [];
            foreach ($sales as $sale) { $monthKey = strtolower(Carbon::parse($sale->sale_date)->format('M'));
                foreach ($sale->items as $item) { $key = ($item->item->company_id ?? 0) . '-' . ($sale->customer_id ?? 0);
                    if (!isset($grouped[$key])) { $grouped[$key] = ['company_name' => $item->item->company->name ?? '-', 'customer_name' => $sale->customer->name ?? '-', 'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0, 'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0, 'total' => 0]; }
                    if (isset($grouped[$key][$monthKey])) { $grouped[$key][$monthKey] += ($item->amount ?? 0) / $divisor; } $grouped[$key]['total'] += ($item->amount ?? 0) / $divisor; } }
            foreach ($grouped as $row) { $data->push($row); }
            foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'total'] as $m) { $totals[$m] = $data->sum($m); }
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.month-wise.company-customer-wise-print', compact('data', 'totals', 'yearFrom', 'yearTo', 'salesInLabel'));
        }
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.company-wise-sales.month-wise.company-customer-wise', compact('yearFrom', 'yearTo', 'salesIn', 'salesmen', 'areas', 'routes', 'companies', 'customers'));
    }

    // ==================== ITEM WISE SALES REPORTS ====================

    public function itemWiseSalesAllItemSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '3');
        $companyId = $request->get('company_id');
        $itemId = $request->get('item_id');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction'])
                ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $transactionType) {
                    $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    if ($transactionType == '1') $q->where('is_return', false);
                    elseif ($transactionType == '2') $q->where('is_return', true);
                });
            
            if ($companyId) $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
            if ($itemId) $query->where('item_id', $itemId);
            
            $salesItems = $query->get();
            $grouped = $salesItems->groupBy('item_id');
            
            foreach ($grouped as $itemGroup) {
                $first = $itemGroup->first();
                $data->push([
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $itemGroup->sum('qty'),
                    'free_qty' => $itemGroup->sum('free_qty'),
                    'amount' => $itemGroup->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.all-item-sale-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.all-item-sale', compact('dateFrom', 'dateTo', 'companies', 'items'));
    }

    public function itemWiseSalesAllItemSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', '1');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'gross_amount' => 0, 'tax_amount' => 0, 'net_amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));
            
            $salesItems = $query->get();
            $grouped = $salesItems->groupBy('item_id');
            
            foreach ($grouped as $itemGroup) {
                $first = $itemGroup->first();
                $data->push([
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $itemGroup->sum('qty'),
                    'free_qty' => $itemGroup->sum('free_qty'),
                    'gross_amount' => $itemGroup->sum('amount'),
                    'tax_amount' => $itemGroup->sum('tax_amount'),
                    'net_amount' => $itemGroup->sum('amount') + $itemGroup->sum('tax_amount'),
                ]);
            }
            
            $totals = [
                'qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'),
                'gross_amount' => $data->sum('gross_amount'), 'tax_amount' => $data->sum('tax_amount'),
                'net_amount' => $data->sum('net_amount')
            ];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.all-item-summary-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.all-item-summary', compact('dateFrom', 'dateTo', 'companies', 'salesmen', 'areas', 'routes', 'states', 'customers'));
    }

    public function itemWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name', 'saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));
            
            if ($itemId) $query->where('item_id', $itemId);
            
            foreach ($query->get() as $item) {
                $data->push([
                    'date' => $item->saleTransaction->sale_date ?? '-',
                    'bill_no' => $item->saleTransaction->invoice_no ?? '-',
                    'party_name' => $item->saleTransaction->customer->name ?? '-',
                    'item_name' => $item->item->name ?? '-',
                    'qty' => $item->qty ?? 0,
                    'free_qty' => $item->free_qty ?? 0,
                    'rate' => $item->rate ?? 0,
                    'amount' => $item->amount ?? 0,
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.bill-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.bill-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesSalesmanWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $salesmanId = $request->get('salesman_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction:id,salesman_id', 'saleTransaction.salesman:id,name'])
                ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $salesmanId) {
                    $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    if ($salesmanId) $q->where('salesman_id', $salesmanId);
                });
            
            if ($itemId) $query->where('item_id', $itemId);
            
            $grouped = $query->get()->groupBy(fn($i) => ($i->saleTransaction->salesman_id ?? 0) . '-' . $i->item_id);
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'salesman_name' => $first->saleTransaction->salesman->name ?? '-',
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'free_qty' => $group->sum('free_qty'),
                    'amount' => $group->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.salesman-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.salesman-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $areaId = $request->get('area_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction:id,customer_id', 'saleTransaction.customer:id,name,area_id', 'saleTransaction.customer.area:id,name'])
                ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $areaId) {
                    $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    if ($areaId) $q->whereHas('customer', fn($c) => $c->where('area_id', $areaId));
                });
            
            if ($itemId) $query->where('item_id', $itemId);
            
            $grouped = $query->get()->groupBy(fn($i) => ($i->saleTransaction->customer->area_id ?? 0) . '-' . $i->item_id);
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'area_name' => $first->saleTransaction->customer->area->name ?? '-',
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'free_qty' => $group->sum('free_qty'),
                    'amount' => $group->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.area-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.area-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesAreaWiseMatrix(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = [];
        
        if ($request->get('export') === 'excel' || $request->get('view_type') === 'print') {
            // Matrix logic would go here
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.area-wise-matrix-print', compact('data', 'totals', 'dateFrom', 'dateTo', 'areas'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.area-wise-matrix', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $routeId = $request->get('route_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print' || $request->get('export') === 'excel') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction:id,customer_id', 'saleTransaction.customer:id,name,route_id', 'saleTransaction.customer.route:id,name'])
                ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $routeId) {
                    $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    if ($routeId) $q->whereHas('customer', fn($c) => $c->where('route_id', $routeId));
                });
            
            if ($itemId) $query->where('item_id', $itemId);
            
            $grouped = $query->get()->groupBy(fn($i) => ($i->saleTransaction->customer->route_id ?? 0) . '-' . $i->item_id);
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'route_name' => $first->saleTransaction->customer->route->name ?? '-',
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'free_qty' => $group->sum('free_qty'),
                    'amount' => $group->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.route-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.route-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesStateWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        $stateId = $request->get('state_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print' || $request->get('export') === 'excel') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction:id,customer_id', 'saleTransaction.customer:id,name,state_code', 'saleTransaction.customer.state:id,name'])
                ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $stateId) {
                    $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    if ($stateId) $q->whereHas('customer', fn($c) => $c->where('state_code', $stateId));
                });
            
            if ($itemId) $query->where('item_id', $itemId);
            
            $grouped = $query->get()->groupBy(fn($i) => ($i->saleTransaction->customer->state_code ?? 0) . '-' . $i->item_id);
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'state_name' => $first->saleTransaction->customer->state->name ?? '-',
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'free_qty' => $group->sum('free_qty'),
                    'amount' => $group->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.state-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.state-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id');
        
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
        
        if ($request->get('view_type') === 'print' || $request->get('export') === 'excel') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction:id,customer_id', 'saleTransaction.customer:id,name'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));
            
            if ($itemId) $query->where('item_id', $itemId);
            
            $grouped = $query->get()->groupBy(fn($i) => ($i->saleTransaction->customer_id ?? 0) . '-' . $i->item_id);
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'customer_name' => $first->saleTransaction->customer->name ?? '-',
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'free_qty' => $group->sum('free_qty'),
                    'amount' => $group->sum('amount'),
                ]);
            }
            
            $totals = ['qty' => $data->sum('qty'), 'free_qty' => $data->sum('free_qty'), 'amount' => $data->sum('amount')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.customer-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.customer-wise', compact('dateFrom', 'dateTo', 'items', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function itemWiseSalesBelowCostItemSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'total_loss' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id,purchase_rate', 'item.company:id,name', 'saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]));
            
            if ($companyId) $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
            
            foreach ($query->get() as $item) {
                $costRate = $item->item->purchase_rate ?? 0;
                $saleRate = $item->rate ?? 0;
                if ($saleRate < $costRate) {
                    $lossPerUnit = $costRate - $saleRate;
                    $data->push([
                        'date' => $item->saleTransaction->sale_date ?? '-',
                        'bill_no' => $item->saleTransaction->invoice_no ?? '-',
                        'party_name' => $item->saleTransaction->customer->name ?? '-',
                        'item_name' => $item->item->name ?? '-',
                        'qty' => $item->qty ?? 0,
                        'sale_rate' => $saleRate,
                        'cost_rate' => $costRate,
                        'loss_per_unit' => $lossPerUnit,
                        'total_loss' => $lossPerUnit * ($item->qty ?? 0),
                    ]);
                }
            }
            
            $totals = ['qty' => $data->sum('qty'), 'total_loss' => $data->sum('total_loss')];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.below-cost-item-sale-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.item-wise-sales.below-cost-item-sale', compact('dateFrom', 'dateTo', 'companies'));
    }

    // ==================== DISCOUNT WISE SALES REPORTS ====================

    public function discountWiseSalesAllDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $itemId = $request->get('item_id');
        $customerId = $request->get('customer_id');
        $discountPercent = $request->get('discount_percent');
        $comparisonType = $request->get('comparison_type', '1');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'amount' => 0, 'discount_amount' => 0, 'net_amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name', 'saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]))
                ->where('discount_percent', '>', 0);
            
            if ($companyId) $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
            if ($itemId) $query->where('item_id', $itemId);
            if ($customerId) $query->whereHas('saleTransaction', fn($q) => $q->where('customer_id', $customerId));
            if ($discountPercent) {
                if ($comparisonType == '1') $query->where('discount_percent', '>=', $discountPercent);
                elseif ($comparisonType == '2') $query->where('discount_percent', '<=', $discountPercent);
                else $query->where('discount_percent', '=', $discountPercent);
            }
            
            foreach ($query->get() as $item) {
                $data->push([
                    'date' => $item->saleTransaction->sale_date ?? '-',
                    'bill_no' => $item->saleTransaction->invoice_no ?? '-',
                    'party_name' => $item->saleTransaction->customer->name ?? '-',
                    'item_name' => $item->item->name ?? '-',
                    'qty' => $item->qty ?? 0,
                    'rate' => $item->rate ?? 0,
                    'amount' => $item->amount ?? 0,
                    'discount_percent' => $item->discount_percent ?? 0,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'net_amount' => ($item->amount ?? 0) - ($item->discount_amount ?? 0),
                ]);
            }
            
            $totals = [
                'qty' => $data->sum('qty'), 'amount' => $data->sum('amount'),
                'discount_amount' => $data->sum('discount_amount'), 'net_amount' => $data->sum('net_amount')
            ];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.all-discount-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.all-discount', compact('dateFrom', 'dateTo', 'companies', 'items', 'customers'));
    }

    public function discountWiseSalesItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $itemId = $request->get('item_id');
        $customerId = $request->get('customer_id');
        $discountPercent = $request->get('discount_percent');
        $comparisonType = $request->get('comparison_type', '1');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'amount' => 0, 'discount_amount' => 0, 'net_amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name,company_id', 'item.company:id,name', 'saleTransaction'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]))
                ->where('discount_percent', '>', 0);
            
            if ($companyId) $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
            if ($itemId) $query->where('item_id', $itemId);
            if ($customerId) $query->whereHas('saleTransaction', fn($q) => $q->where('customer_id', $customerId));
            if ($discountPercent) {
                if ($comparisonType == '1') $query->where('discount_percent', '>=', $discountPercent);
                elseif ($comparisonType == '2') $query->where('discount_percent', '<=', $discountPercent);
                else $query->where('discount_percent', '=', $discountPercent);
            }
            
            $grouped = $query->get()->groupBy('item_id');
            
            foreach ($grouped as $group) {
                $first = $group->first();
                $data->push([
                    'item_name' => $first->item->name ?? '-',
                    'company_name' => $first->item->company->name ?? '-',
                    'qty' => $group->sum('qty'),
                    'amount' => $group->sum('amount'),
                    'discount_amount' => $group->sum('discount_amount'),
                    'net_amount' => $group->sum('amount') - $group->sum('discount_amount'),
                ]);
            }
            
            $totals = [
                'qty' => $data->sum('qty'), 'amount' => $data->sum('amount'),
                'discount_amount' => $data->sum('discount_amount'), 'net_amount' => $data->sum('net_amount')
            ];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.item-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.item-wise', compact('dateFrom', 'dateTo', 'companies', 'items', 'customers'));
    }

    public function discountWiseSalesItemWiseInvoiceWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');
        $itemId = $request->get('item_id');
        $customerId = $request->get('customer_id');
        $discountPercent = $request->get('discount_percent');
        $comparisonType = $request->get('comparison_type', '1');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        $data = collect();
        $totals = ['qty' => 0, 'amount' => 0, 'discount_amount' => 0, 'net_amount' => 0];
        
        if ($request->get('view_type') === 'print') {
            $query = SaleTransactionItem::with(['item:id,name', 'saleTransaction:id,invoice_no,sale_date,customer_id', 'saleTransaction.customer:id,name'])
                ->whereHas('saleTransaction', fn($q) => $q->whereBetween('sale_date', [$dateFrom, $dateTo]))
                ->where('discount_percent', '>', 0);
            
            if ($companyId) $query->whereHas('item', fn($q) => $q->where('company_id', $companyId));
            if ($itemId) $query->where('item_id', $itemId);
            if ($customerId) $query->whereHas('saleTransaction', fn($q) => $q->where('customer_id', $customerId));
            if ($discountPercent) {
                if ($comparisonType == '1') $query->where('discount_percent', '>=', $discountPercent);
                elseif ($comparisonType == '2') $query->where('discount_percent', '<=', $discountPercent);
                else $query->where('discount_percent', '=', $discountPercent);
            }
            
            foreach ($query->get() as $item) {
                $data->push([
                    'date' => $item->saleTransaction->sale_date ?? '-',
                    'bill_no' => $item->saleTransaction->invoice_no ?? '-',
                    'party_name' => $item->saleTransaction->customer->name ?? '-',
                    'item_name' => $item->item->name ?? '-',
                    'qty' => $item->qty ?? 0,
                    'rate' => $item->rate ?? 0,
                    'amount' => $item->amount ?? 0,
                    'discount_percent' => $item->discount_percent ?? 0,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'net_amount' => ($item->amount ?? 0) - ($item->discount_amount ?? 0),
                ]);
            }
            
            $totals = [
                'qty' => $data->sum('qty'), 'amount' => $data->sum('amount'),
                'discount_amount' => $data->sum('discount_amount'), 'net_amount' => $data->sum('net_amount')
            ];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.item-wise-invoice-wise-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.discount-wise-sales.item-wise-invoice-wise', compact('dateFrom', 'dateTo', 'companies', 'items', 'customers'));
    }

    // ==================== SCHEME ISSUED REPORTS ====================

    public function schemeIssuedFreeScheme(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        if ($request->get('view_type') === 'print') {
            $data = collect(); $totals = ['free_qty' => 0, 'amount' => 0];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.free-scheme-issued-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.free-scheme-issued', compact('dateFrom', 'dateTo', 'companies', 'customers', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function schemeIssuedHalfScheme(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        if ($request->get('view_type') === 'print') {
            $data = collect(); $totals = ['half_qty' => 0, 'amount' => 0];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.half-scheme-issued-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.half-scheme-issued', compact('dateFrom', 'dateTo', 'companies', 'customers', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function schemeIssuedItemWiseLess(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        
        if ($request->get('view_type') === 'print') {
            $data = collect(); $totals = ['qty' => 0, 'less_amount' => 0, 'net_amount' => 0];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.item-wise-less-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.item-wise-less', compact('dateFrom', 'dateTo', 'companies', 'salesmen'));
    }

    public function schemeIssuedFreeIssuesWithoutQty(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        if ($request->get('view_type') === 'print') {
            $data = collect(); $totals = ['free_qty' => 0, 'amount' => 0];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.free-issues-without-qty-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.free-issues-without-qty', compact('dateFrom', 'dateTo', 'companies', 'customers', 'salesmen', 'areas', 'routes', 'states'));
    }

    public function schemeIssuedInvalidFreeScheme(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        
        if ($request->get('view_type') === 'print' || $request->get('export') === 'excel') {
            $data = collect(); $totals = ['qty' => 0, 'free_qty' => 0, 'amount' => 0];
            return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.invalid-free-scheme-issued-print', compact('data', 'totals', 'dateFrom', 'dateTo'));
        }
        
        return view('admin.reports.sale-report.miscellaneous-sale-analysis.scheme-issued.invalid-free-scheme-issued', compact('dateFrom', 'dateTo', 'companies', 'customers', 'salesmen', 'areas', 'routes', 'states'));
    }
    /**
     * Cash Collection Transfer Sale Report
     * Shows cash collection and transfer details from sales
     */
    public function cashCollTrnfSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $sales = collect();
        $totals = [
            'count' => 0,
            'net_amount' => 0,
            'paid_amount' => 0,
            'balance_amount' => 0
        ];
        $dailySummary = collect();

        // Only fetch data when view or print is requested
        if ($request->has('view') || $request->has('print') || $request->get('export') === 'excel') {
            // Fetch sales with cash payment details
            $query = SaleTransaction::with([
                'customer:id,name,code,area_name,route_name',
                'salesman:id,name,code'
            ])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->orderBy('sale_date')
            ->orderBy('invoice_no');

            $sales = $query->get();

            // Calculate totals
            $totals = [
                'count' => $sales->count(),
                'net_amount' => $sales->sum('net_amount'),
                'paid_amount' => $sales->sum('paid_amount'),
                'balance_amount' => $sales->sum('balance_amount')
            ];

            // Group by date for daily summary
            $dailySummary = $sales->groupBy(function($sale) {
                return $sale->sale_date->format('Y-m-d');
            })->map(function($daySales) {
                return [
                    'count' => $daySales->count(),
                    'net_amount' => $daySales->sum('net_amount'),
                    'paid_amount' => $daySales->sum('paid_amount'),
                    'balance_amount' => $daySales->sum('balance_amount')
                ];
            });

            if ($request->get('export') === 'excel') {
                return $this->exportCashCollTrnfToExcel($sales, $totals, $dateFrom, $dateTo);
            }

            // Handle Print view - open in new window
            if ($request->has('print')) {
                return view('admin.reports.sale-report.other-reports.cash-coll-trnf-sale-print', compact(
                    'sales', 'totals', 'dailySummary', 'dateFrom', 'dateTo'
                ));
            }
        }

        return view('admin.reports.sale-report.other-reports.cash-coll-trnf-sale', compact(
            'dateFrom', 'dateTo', 'sales', 'totals', 'dailySummary'
        ));
    }

    /**
     * Export Cash Collection Transfer to Excel
     */
    private function exportCashCollTrnfToExcel($sales, $totals, $dateFrom, $dateTo)
    {
        $filename = 'cash_collection_transfer_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Party Code', 'Party Name', 'Salesman', 'Net Amount', 'Paid Amount', 'Balance']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_date->format('d-m-Y'),
                    ($sale->series ?? '') . $sale->invoice_no,
                    $sale->customer->code ?? '',
                    $sale->customer->name ?? 'N/A',
                    $sale->salesman->name ?? '',
                    number_format($sale->net_amount ?? 0, 2),
                    number_format($sale->paid_amount ?? 0, 2),
                    number_format($sale->balance_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '', 
                number_format($totals['net_amount'] ?? 0, 2),
                number_format($totals['paid_amount'] ?? 0, 2),
                number_format($totals['balance_amount'] ?? 0, 2)
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Discount On Sale - Bill Wise Report
     * Shows discount details for each sale bill
     */
    public function saleBillWiseDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $discountOption = $request->get('discount_option', '1'); // 1=With Dis, 2=W/o Dis, 3=All
        $salesmanId = $request->get('salesman_id', '');
        $areaId = $request->get('area_id', '');
        $routeId = $request->get('route_id', '');
        $stateId = $request->get('state_id', '');
        $customerId = $request->get('customer_id', '');
        $series = $request->get('series', '');
        
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        // Build query
        $query = SaleTransaction::with([
            'customer:id,name,code,area_name,route_name,state_name',
            'salesman:id,name,code'
        ])
        ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Discount filter
        if ($discountOption == '1') {
            $query->where('dis_amount', '>', 0);
        } elseif ($discountOption == '2') {
            $query->where(function($q) {
                $q->where('dis_amount', '<=', 0)->orWhereNull('dis_amount');
            });
        }

        // Apply filters
        if ($salesmanId && $salesmanId != '') {
            $query->where('salesman_id', $salesmanId);
        }
        if ($areaId && $areaId != '') {
            $query->whereHas('customer', function($q) use ($areaId) {
                $area = Area::find($areaId);
                if ($area) {
                    $q->where('area_name', $area->name);
                }
            });
        }
        if ($routeId && $routeId != '') {
            $query->whereHas('customer', function($q) use ($routeId) {
                $route = Route::find($routeId);
                if ($route) {
                    $q->where('route_name', $route->name);
                }
            });
        }
        if ($stateId && $stateId != '') {
            $query->whereHas('customer', function($q) use ($stateId) {
                $state = State::find($stateId);
                if ($state) {
                    $q->where('state_name', $state->name);
                }
            });
        }
        if ($customerId && $customerId != '') {
            $query->where('customer_id', $customerId);
        }
        if ($series && $series != '') {
            $query->where('series', $series);
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Calculate totals
        $totals = [
            'count' => $sales->count(),
            'gross_amount' => $sales->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount'),
            'dis_percent' => $sales->sum('nt_amount') > 0 ? ($sales->sum('dis_amount') / $sales->sum('nt_amount')) * 100 : 0,
            'scm_amount' => $sales->sum('scm_amount'),
            'tax_amount' => $sales->sum('tax_amount'),
            'net_amount' => $sales->sum('net_amount')
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportBillWiseDiscountToExcel($sales, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sale-bill-wise-discount-print', compact(
                'sales', 'totals', 'dateFrom', 'dateTo', 'discountOption', 'salesmanId', 'areaId', 
                'routeId', 'stateId', 'customerId', 'series'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sale-bill-wise-discount', compact(
            'dateFrom', 'dateTo', 'salesmen', 'areas', 'routes', 'states', 'customers', 'sales', 'totals',
            'discountOption', 'salesmanId', 'areaId', 'routeId', 'stateId', 'customerId', 'series'
        ));
    }

    /**
     * Export Bill Wise Discount to Excel
     */
    private function exportBillWiseDiscountToExcel($sales, $totals, $dateFrom, $dateTo)
    {
        $filename = 'bill_wise_discount_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Bill No', 'Party Code', 'Party Name', 'Area', 'Salesman', 'Gross Amt', 'Discount', 'Dis%', 'Scheme', 'Tax', 'Net Amount']);

            foreach ($sales as $sale) {
                $disPercent = $sale->nt_amount > 0 ? ($sale->dis_amount / $sale->nt_amount) * 100 : 0;
                fputcsv($file, [
                    $sale->sale_date->format('d-m-Y'),
                    ($sale->series ?? '') . $sale->invoice_no,
                    $sale->customer->code ?? '',
                    $sale->customer->name ?? 'N/A',
                    $sale->customer->area_name ?? '',
                    $sale->salesman->name ?? '',
                    number_format($sale->nt_amount ?? 0, 2),
                    number_format($sale->dis_amount ?? 0, 2),
                    number_format($disPercent, 2) . '%',
                    number_format($sale->scm_amount ?? 0, 2),
                    number_format($sale->tax_amount ?? 0, 2),
                    number_format($sale->net_amount ?? 0, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', '', '', 
                number_format($totals['gross_amount'] ?? 0, 2),
                number_format($totals['dis_amount'] ?? 0, 2),
                number_format($totals['dis_percent'] ?? 0, 2) . '%',
                number_format($totals['scm_amount'] ?? 0, 2),
                number_format($totals['tax_amount'] ?? 0, 2),
                number_format($totals['net_amount'] ?? 0, 2)
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sale Book With Sale Return Report
     * Shows sales and returns combined for a customer
     */
    public function salesBookWithReturn(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id', '');
        
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        // Build sales query
        $salesQuery = SaleTransaction::with(['customer:id,name,code,area_name,route_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Build returns query
        $returnsQuery = SaleReturnTransaction::with(['customer:id,name,code,area_name,route_name'])
            ->whereBetween('return_date', [$dateFrom, $dateTo]);

        // Apply customer filter
        if ($customerId && $customerId != '') {
            $salesQuery->where('customer_id', $customerId);
            $returnsQuery->where('customer_id', $customerId);
        }

        $sales = $salesQuery->orderBy('sale_date')->orderBy('invoice_no')->get();
        $returns = $returnsQuery->orderBy('return_date')->orderBy('sr_no')->get();

        // Combine and sort by date
        $combinedData = collect();
        
        foreach ($sales as $sale) {
            $combinedData->push([
                'date' => $sale->sale_date,
                'type' => 'Sale',
                'doc_no' => ($sale->series ?? '') . $sale->invoice_no,
                'customer_code' => $sale->customer->code ?? '',
                'customer_name' => $sale->customer->name ?? 'N/A',
                'area' => $sale->customer->area_name ?? '',
                'gross_amount' => $sale->nt_amount ?? 0,
                'dis_amount' => $sale->dis_amount ?? 0,
                'tax_amount' => $sale->tax_amount ?? 0,
                'net_amount' => $sale->net_amount ?? 0,
                'is_return' => false
            ]);
        }

        foreach ($returns as $return) {
            $combinedData->push([
                'date' => $return->return_date,
                'type' => 'Return',
                'doc_no' => ($return->series ?? '') . ($return->invoice_no ?? $return->sr_no),
                'customer_code' => $return->customer->code ?? '',
                'customer_name' => $return->customer->name ?? 'N/A',
                'area' => $return->customer->area_name ?? '',
                'gross_amount' => -($return->nt_amount ?? 0),
                'dis_amount' => -($return->dis_amount ?? 0),
                'tax_amount' => -($return->tax_amount ?? 0),
                'net_amount' => -($return->net_amount ?? 0),
                'is_return' => true
            ]);
        }

        // Sort by date
        $combinedData = $combinedData->sortBy('date')->values();

        // Calculate totals
        $totals = [
            'sale_count' => $sales->count(),
            'return_count' => $returns->count(),
            'sale_amount' => $sales->sum('net_amount'),
            'return_amount' => $returns->sum('net_amount'),
            'net_amount' => $sales->sum('net_amount') - $returns->sum('net_amount'),
            'gross_amount' => $sales->sum('nt_amount') - $returns->sum('nt_amount'),
            'dis_amount' => $sales->sum('dis_amount') - $returns->sum('dis_amount'),
            'tax_amount' => $sales->sum('tax_amount') - $returns->sum('tax_amount')
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportSalesBookWithReturnToExcel($combinedData, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sales-book-with-return-print', compact(
                'combinedData', 'totals', 'dateFrom', 'dateTo', 'customerId'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sales-book-with-return', compact(
            'dateFrom', 'dateTo', 'customers', 'combinedData', 'totals', 'customerId'
        ));
    }

    /**
     * Export Sales Book With Return to Excel
     */
    private function exportSalesBookWithReturnToExcel($combinedData, $totals, $dateFrom, $dateTo)
    {
        $filename = 'sales_book_with_return_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($combinedData, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Type', 'Doc No', 'Party Code', 'Party Name', 'Area', 'Gross Amt', 'Discount', 'Tax', 'Net Amount']);

            foreach ($combinedData as $row) {
                fputcsv($file, [
                    Carbon::parse($row['date'])->format('d-m-Y'),
                    $row['type'],
                    $row['doc_no'],
                    $row['customer_code'],
                    $row['customer_name'],
                    $row['area'],
                    number_format($row['gross_amount'], 2),
                    number_format($row['dis_amount'], 2),
                    number_format($row['tax_amount'], 2),
                    number_format($row['net_amount'], 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL', '', 
                number_format($totals['gross_amount'], 2),
                number_format($totals['dis_amount'], 2),
                number_format($totals['tax_amount'], 2),
                number_format($totals['net_amount'], 2)
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Rate Change Report (Rate Difference)
     * Shows rate differences in sales - Purchase Rate vs Sale Rate vs Cost
     */
    public function rateDifference(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $itemId = $request->get('item_id', '');
        $companyId = $request->get('company_id', '');
        $customerId = $request->get('customer_id', '');
        $rateType = $request->get('rate_type', 'R'); // P=Purchase, S=Sale, R=Rate Diff, C=Cost
        $groupBy = $request->get('group_by', 'I'); // I=Item Wise, B=Bill Wise, P=Party Wise
        $withVat = $request->boolean('with_vat');
        $withSc = $request->boolean('with_sc');
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name', 'short_name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        // Build query for sale items with rate details
        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id',
            'saleTransaction.customer:id,name,code',
            'item:id,name,pur_rate,s_rate,cost,mrp'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        // Apply filters using IDs
        if ($itemId && $itemId != '') {
            $query->where('item_id', $itemId);
        }
        if ($companyId && $companyId != '') {
            // Get company name for filtering since SaleTransactionItem stores company_name not company_id
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }
        if ($customerId && $customerId != '') {
            $query->whereHas('saleTransaction', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        $saleItems = $query->get();

        // Process data based on group by option
        $reportData = collect();

        if ($groupBy === 'I') {
            // Item Wise grouping
            $grouped = $saleItems->groupBy('item_id');
            foreach ($grouped as $grpItemId => $grpItems) {
                $firstItem = $grpItems->first();
                $totalQty = $grpItems->sum('qty');
                $totalAmount = $grpItems->sum('net_amount');
                $avgSaleRate = $totalQty > 0 ? $totalAmount / $totalQty : 0;
                $purchaseRate = $firstItem->item->pur_rate ?? 0;
                $costRate = $firstItem->item->cost ?? 0;
                $rateDiff = $avgSaleRate - $purchaseRate;

                $reportData->push([
                    'item_id' => $grpItemId,
                    'item_name' => $firstItem->item_name ?? $firstItem->item->name ?? '',
                    'company_name' => $firstItem->company_name ?? '',
                    'qty' => $totalQty,
                    'purchase_rate' => $purchaseRate,
                    'sale_rate' => $avgSaleRate,
                    'cost_rate' => $costRate,
                    'rate_diff' => $rateDiff,
                    'diff_amount' => $rateDiff * $totalQty,
                    'total_amount' => $totalAmount
                ]);
            }
        } elseif ($groupBy === 'B') {
            // Bill Wise
            foreach ($saleItems as $item) {
                $purchaseRate = $item->item->pur_rate ?? 0;
                $saleRate = $item->sale_rate ?? 0;
                $rateDiff = $saleRate - $purchaseRate;

                $reportData->push([
                    'date' => $item->saleTransaction->sale_date,
                    'bill_no' => ($item->saleTransaction->series ?? '') . $item->saleTransaction->invoice_no,
                    'party_name' => $item->saleTransaction->customer->name ?? '',
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name ?? $item->item->name ?? '',
                    'qty' => $item->qty,
                    'purchase_rate' => $purchaseRate,
                    'sale_rate' => $saleRate,
                    'rate_diff' => $rateDiff,
                    'diff_amount' => $rateDiff * $item->qty,
                    'total_amount' => $item->net_amount
                ]);
            }
        } else {
            // Party Wise
            $grouped = $saleItems->groupBy(function($item) {
                return $item->saleTransaction->customer_id;
            });
            foreach ($grouped as $grpCustomerId => $grpItems) {
                $firstItem = $grpItems->first();
                $totalQty = $grpItems->sum('qty');
                $totalAmount = $grpItems->sum('net_amount');
                $totalPurchaseValue = $grpItems->sum(function($i) {
                    return ($i->item->pur_rate ?? 0) * $i->qty;
                });
                $rateDiffTotal = $totalAmount - $totalPurchaseValue;

                $reportData->push([
                    'party_code' => $firstItem->saleTransaction->customer->code ?? '',
                    'party_name' => $firstItem->saleTransaction->customer->name ?? '',
                    'total_qty' => $totalQty,
                    'purchase_value' => $totalPurchaseValue,
                    'sale_value' => $totalAmount,
                    'rate_diff' => $rateDiffTotal,
                    'diff_percent' => $totalPurchaseValue > 0 ? ($rateDiffTotal / $totalPurchaseValue) * 100 : 0
                ]);
            }
        }

        // Calculate totals
        $totals = [
            'count' => $reportData->count(),
            'total_qty' => $reportData->sum('qty') ?: $reportData->sum('total_qty'),
            'total_diff_amount' => $reportData->sum('diff_amount') ?: $reportData->sum('rate_diff'),
            'total_amount' => $reportData->sum('total_amount') ?: $reportData->sum('sale_value')
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportRateDifferenceToExcel($reportData, $totals, $dateFrom, $dateTo, $groupBy);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.rate-difference-print', compact(
                'reportData', 'totals', 'dateFrom', 'dateTo', 'itemId', 'companyId', 'customerId',
                'rateType', 'groupBy', 'withVat', 'withSc'
            ));
        }

        return view('admin.reports.sale-report.other-reports.rate-difference', compact(
            'dateFrom', 'dateTo', 'companies', 'items', 'customers', 'reportData', 'totals',
            'itemId', 'companyId', 'customerId', 'rateType', 'groupBy', 'withVat', 'withSc'
        ));
    }

    /**
     * Export Rate Difference to Excel
     */
    private function exportRateDifferenceToExcel($reportData, $totals, $dateFrom, $dateTo, $groupBy)
    {
        $filename = 'rate_difference_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reportData, $totals, $groupBy) {
            $file = fopen('php://output', 'w');
            
            if ($groupBy === 'I') {
                fputcsv($file, ['Item Name', 'Company', 'Qty', 'Purchase Rate', 'Sale Rate', 'Rate Diff', 'Diff Amount', 'Total Amount']);
                foreach ($reportData as $row) {
                    fputcsv($file, [
                        $row['item_name'], $row['company_name'], $row['qty'],
                        number_format($row['purchase_rate'], 2), number_format($row['sale_rate'], 2),
                        number_format($row['rate_diff'], 2), number_format($row['diff_amount'], 2),
                        number_format($row['total_amount'], 2)
                    ]);
                }
            } elseif ($groupBy === 'B') {
                fputcsv($file, ['Date', 'Bill No', 'Party', 'Item Name', 'Qty', 'Purchase Rate', 'Sale Rate', 'Rate Diff', 'Diff Amount']);
                foreach ($reportData as $row) {
                    fputcsv($file, [
                        Carbon::parse($row['date'])->format('d-m-Y'), $row['bill_no'], $row['party_name'],
                        $row['item_name'], $row['qty'],
                        number_format($row['purchase_rate'], 2), number_format($row['sale_rate'], 2),
                        number_format($row['rate_diff'], 2), number_format($row['diff_amount'], 2)
                    ]);
                }
            } else {
                fputcsv($file, ['Party Code', 'Party Name', 'Total Qty', 'Purchase Value', 'Sale Value', 'Rate Diff', 'Diff %']);
                foreach ($reportData as $row) {
                    fputcsv($file, [
                        $row['party_code'], $row['party_name'], $row['total_qty'],
                        number_format($row['purchase_value'], 2), number_format($row['sale_value'], 2),
                        number_format($row['rate_diff'], 2), number_format($row['diff_percent'], 2) . '%'
                    ]);
                }
            }

            fputcsv($file, []);
            fputcsv($file, ['TOTAL', '', $totals['total_qty'], '', '', '', $totals['total_diff_amount'], $totals['total_amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sales Matrix Report
     * Cross-tabulation of Party/Item sales data
     */
    public function salesMatrix(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id', '');
        $divisionCode = $request->get('division_code', '00');
        $statusCode = $request->get('status_code', '');
        $showFor = $request->get('show_for', 'Party'); // Party, Area, Salesman, Route
        $salesmanId = $request->get('salesman_id', '');
        $areaId = $request->get('area_id', '');
        $routeId = $request->get('route_id', '');
        $valueOn = $request->get('value_on', 'NetSale'); // NetSale, Sale, WS, Spl, Cost
        $printSalesReturn = $request->boolean('print_sales_return');
        $addFreeQty = $request->get('add_free_qty', 'Y');
        $matrixType = $request->get('matrix_type', '1'); // 1=X->Party Y->Item, 2=X->Item Y->Party
        
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name', 'short_name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        // Build query
        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,sale_date,customer_id,salesman_id',
            'saleTransaction.customer:id,name,code,area_name,route_name',
            'saleTransaction.salesman:id,name,code'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        // Apply filters using IDs
        if ($companyId && $companyId != '') {
            // Get company name for filtering since SaleTransactionItem stores company_name not company_id
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }
        if ($salesmanId && $salesmanId != '') {
            $query->whereHas('saleTransaction', function($q) use ($salesmanId) {
                $q->where('salesman_id', $salesmanId);
            });
        }
        if ($areaId && $areaId != '') {
            $area = Area::find($areaId);
            if ($area) {
                $query->whereHas('saleTransaction.customer', function($q) use ($area) {
                    $q->where('area_name', $area->name);
                });
            }
        }
        if ($routeId && $routeId != '') {
            $route = Route::find($routeId);
            if ($route) {
                $query->whereHas('saleTransaction.customer', function($q) use ($route) {
                    $q->where('route_name', $route->name);
                });
            }
        }

        $saleItems = $query->get();

        // Get unique items and entities based on showFor
        $itemsList = $saleItems->pluck('item_name', 'item_id')->unique();
        
        $entitiesList = collect();
        if ($showFor === 'Party') {
            $entitiesList = $saleItems->map(function($item) {
                return [
                    'id' => $item->saleTransaction->customer_id,
                    'name' => $item->saleTransaction->customer->name ?? 'Unknown'
                ];
            })->unique('id')->pluck('name', 'id');
        } elseif ($showFor === 'Area') {
            $entitiesList = $saleItems->map(function($item) {
                return [
                    'id' => $item->saleTransaction->customer->area_name ?? 'NA',
                    'name' => $item->saleTransaction->customer->area_name ?? 'Unknown'
                ];
            })->unique('id')->pluck('name', 'id');
        } elseif ($showFor === 'Salesman') {
            $entitiesList = $saleItems->map(function($item) {
                return [
                    'id' => $item->saleTransaction->salesman_id,
                    'name' => $item->saleTransaction->salesman->name ?? 'Unknown'
                ];
            })->unique('id')->pluck('name', 'id');
        } elseif ($showFor === 'Route') {
            $entitiesList = $saleItems->map(function($item) {
                return [
                    'id' => $item->saleTransaction->customer->route_name ?? 'NA',
                    'name' => $item->saleTransaction->customer->route_name ?? 'Unknown'
                ];
            })->unique('id')->pluck('name', 'id');
        }

        // Build matrix data
        $matrixData = [];
        $rowTotals = [];
        $colTotals = [];
        $grandTotal = 0;

        foreach ($saleItems as $item) {
            $entityId = match($showFor) {
                'Party' => $item->saleTransaction->customer_id,
                'Area' => $item->saleTransaction->customer->area_name ?? 'NA',
                'Salesman' => $item->saleTransaction->salesman_id,
                'Route' => $item->saleTransaction->customer->route_name ?? 'NA',
                default => $item->saleTransaction->customer_id
            };

            $itemId = $item->item_id;
            
            // Calculate value based on valueOn option
            $value = match($valueOn) {
                'Sale' => $item->amount ?? 0,
                'WS' => ($item->ws_rate ?? 0) * $item->qty,
                'Spl' => ($item->spl_rate ?? 0) * $item->qty,
                'Cost' => ($item->cost_rate ?? 0) * $item->qty,
                default => $item->net_amount ?? 0
            };

            // Add free qty value if requested
            if ($addFreeQty === 'Y' && $item->free_qty > 0) {
                $freeValue = ($item->sale_rate ?? 0) * $item->free_qty;
                $value += $freeValue;
            }

            if ($matrixType === '1') {
                // X->Party Y->Item
                if (!isset($matrixData[$entityId])) {
                    $matrixData[$entityId] = [];
                }
                if (!isset($matrixData[$entityId][$itemId])) {
                    $matrixData[$entityId][$itemId] = 0;
                }
                $matrixData[$entityId][$itemId] += $value;
            } else {
                // X->Item Y->Party
                if (!isset($matrixData[$itemId])) {
                    $matrixData[$itemId] = [];
                }
                if (!isset($matrixData[$itemId][$entityId])) {
                    $matrixData[$itemId][$entityId] = 0;
                }
                $matrixData[$itemId][$entityId] += $value;
            }

            // Calculate totals
            if (!isset($rowTotals[$entityId])) $rowTotals[$entityId] = 0;
            if (!isset($colTotals[$itemId])) $colTotals[$itemId] = 0;
            
            $rowTotals[$entityId] += $value;
            $colTotals[$itemId] += $value;
            $grandTotal += $value;
        }

        // Handle sales returns if requested
        $returnData = collect();
        if ($printSalesReturn) {
            $returnQuery = SaleReturnTransactionItem::with([
                'saleReturnTransaction:id,sr_no,invoice_no,return_date,customer_id,salesman_id',
                'saleReturnTransaction.customer:id,name,code,area_name,route_name',
                'saleReturnTransaction.salesman:id,name,code'
            ])
            ->whereHas('saleReturnTransaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('return_date', [$dateFrom, $dateTo]);
            });

            if ($companyId && $companyId != '') {
                $company = Company::find($companyId);
                if ($company) {
                    $returnQuery->where('company_name', $company->name);
                }
            }

            $returnData = $returnQuery->get();
        }

        $totals = [
            'items_count' => $itemsList->count(),
            'entities_count' => $entitiesList->count(),
            'grand_total' => $grandTotal,
            'row_totals' => $rowTotals,
            'col_totals' => $colTotals
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sales-matrix-print', compact(
                'matrixData', 'itemsList', 'entitiesList', 'totals', 'dateFrom', 'dateTo',
                'companyId', 'divisionCode', 'showFor', 'salesmanId', 'areaId', 'routeId',
                'valueOn', 'printSalesReturn', 'addFreeQty', 'matrixType', 'returnData'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sales-matrix', compact(
            'dateFrom', 'dateTo', 'companies', 'salesmen', 'areas', 'routes',
            'matrixData', 'itemsList', 'entitiesList', 'totals',
            'companyId', 'divisionCode', 'showFor', 'salesmanId', 'areaId', 'routeId',
            'valueOn', 'printSalesReturn', 'addFreeQty', 'matrixType'
        ));
    }

    /**
     * Minus Qty Sale Report
     * Shows negative quantity items in sale invoices
     */
    public function minusQtySale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $includeCancelled = $request->boolean('include_cancelled');

        // Query sale transaction items with negative qty
        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,status',
            'saleTransaction.customer:id,name,code'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $includeCancelled) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if (!$includeCancelled) {
                $q->where(function($sq) {
                    $sq->whereNull('status')->orWhere('status', '!=', 'cancelled');
                });
            }
        })
        ->where('qty', '<', 0);

        $negativeItems = $query->orderBy('sale_transaction_id')->get();

        $items = $negativeItems->map(function($item) {
            return [
                'date' => $item->saleTransaction->sale_date->format('d-m-Y'),
                'bill_no' => ($item->saleTransaction->series ?? '') . $item->saleTransaction->invoice_no,
                'party_name' => $item->saleTransaction->customer->name ?? 'N/A',
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'qty' => (float) $item->qty,
                'rate' => (float) $item->sale_rate,
                'amount' => (float) $item->net_amount,
            ];
        });

        $totals = [
            'qty' => $items->sum('qty'),
            'amount' => $items->sum('amount'),
            'count' => $items->count()
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportMinusQtySaleToExcel($items, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.minus-qty-sale-print', compact(
                'items', 'totals', 'dateFrom', 'dateTo', 'includeCancelled'
            ));
        }

        return view('admin.reports.sale-report.other-reports.minus-qty-sale', compact(
            'items', 'totals', 'dateFrom', 'dateTo', 'includeCancelled'
        ));
    }

    /**
     * Export Minus Qty Sale to Excel
     */
    private function exportMinusQtySaleToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'minus_qty_sale_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Bill No', 'Party', 'Item Code', 'Item Name', 'Qty', 'Rate', 'Amount']);
            $i = 1;
            foreach ($items as $item) {
                fputcsv($file, [$i++, $item['date'], $item['bill_no'], $item['party_name'], $item['item_code'], $item['item_name'], $item['qty'], $item['rate'], $item['amount']]);
            }
            fputcsv($file, ['', '', '', '', '', 'Total:', $totals['qty'], '', $totals['amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sales Details Report
     * Detailed sales with cancelled invoices option
     */
    public function salesDetails(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $includeCancelled = $request->boolean('include_cancelled');
        $companyId = $request->get('company_id');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,status',
            'saleTransaction.customer:id,name,code'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $customerId, $includeCancelled) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($customerId) {
                $q->where('customer_id', $customerId);
            }
            if (!$includeCancelled) {
                $q->where(function($sq) {
                    $sq->whereNull('status')->orWhere('status', '!=', 'cancelled');
                });
            }
        });

        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }

        $saleItems = $query->orderBy('sale_transaction_id')->get();

        $details = $saleItems->map(function($item) {
            return [
                'date' => $item->saleTransaction->sale_date->format('d-m-Y'),
                'bill_no' => ($item->saleTransaction->series ?? '') . $item->saleTransaction->invoice_no,
                'party_name' => $item->saleTransaction->customer->name ?? 'N/A',
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'company_name' => $item->company_name ?? '',
                'qty' => (float) $item->qty,
                'free_qty' => (float) ($item->free_qty ?? 0),
                'rate' => (float) $item->sale_rate,
                'amount' => (float) $item->net_amount,
                'status' => $item->saleTransaction->status ?? 'active'
            ];
        });

        $totals = [
            'qty' => $details->sum('qty'),
            'free_qty' => $details->sum('free_qty'),
            'amount' => $details->sum('amount'),
            'count' => $details->count()
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        if ($request->get('export') === 'excel') {
            return $this->exportSalesDetailsToExcel($details, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sales-details-print', compact(
                'details', 'totals', 'customers', 'companies', 'dateFrom', 'dateTo', 'customerId', 'companyId', 'includeCancelled'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sales-details', compact(
            'details', 'totals', 'customers', 'companies', 'dateFrom', 'dateTo', 'customerId', 'companyId', 'includeCancelled'
        ));
    }

    /**
     * Export Sales Details to Excel
     */
    private function exportSalesDetailsToExcel($details, $totals, $dateFrom, $dateTo)
    {
        $filename = 'sales_details_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($details, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Bill No', 'Party', 'Item', 'Company', 'Qty', 'Free', 'Rate', 'Amount']);
            $i = 1;
            foreach ($details as $d) {
                fputcsv($file, [$i++, $d['date'], $d['bill_no'], $d['party_name'], $d['item_name'], $d['company_name'], $d['qty'], $d['free_qty'], $d['rate'], $d['amount']]);
            }
            fputcsv($file, ['', '', '', '', '', 'Total:', $totals['qty'], $totals['free_qty'], '', $totals['amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Invoice Documents Report
     * Shows invoice documents with advice date, fin year, series, bill no filters
     */
    public function invoiceDocuments(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series');
        $billNoFrom = $request->get('bill_no_from');
        $billNoTo = $request->get('bill_no_to');
        $finYear = $request->get('fin_year', date('Y') . '-' . (date('Y') + 1));

        $query = SaleTransaction::with(['customer:id,name,code,gst_number'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($series) {
            $query->where('series', $series);
        }
        if ($billNoFrom) {
            $query->where('invoice_no', '>=', $billNoFrom);
        }
        if ($billNoTo) {
            $query->where('invoice_no', '<=', $billNoTo);
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        $documents = $sales->map(function($sale, $index) {
            return [
                'sr_no' => $index + 1,
                'date' => $sale->sale_date->format('d-m-Y'),
                'invoice_no' => ($sale->series ?? '') . $sale->invoice_no,
                'party_code' => $sale->customer->code ?? '',
                'party_name' => $sale->customer->name ?? 'N/A',
                'gst_number' => $sale->customer->gst_number ?? '',
                'amount' => (float) $sale->net_amount,
                'eway_bill' => $sale->eway_bill_no ?? '',
                'irn_no' => $sale->irn_no ?? '',
                'status' => ($sale->eway_bill_no || $sale->irn_no) ? 'Generated' : 'Pending'
            ];
        });

        $totals = [
            'count' => $documents->count(),
            'amount' => $documents->sum('amount'),
            'generated' => $documents->where('status', 'Generated')->count(),
            'pending' => $documents->where('status', 'Pending')->count()
        ];

        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->get('export') === 'excel') {
            return $this->exportInvoiceDocumentsToExcel($documents, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.invoice-documents-print', compact(
                'documents', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'billNoFrom', 'billNoTo', 'finYear'
            ));
        }

        return view('admin.reports.sale-report.other-reports.invoice-documents', compact(
            'documents', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'billNoFrom', 'billNoTo', 'finYear'
        ));
    }

    /**
     * Export Invoice Documents to Excel
     */
    private function exportInvoiceDocumentsToExcel($documents, $totals, $dateFrom, $dateTo)
    {
        $filename = 'invoice_documents_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($documents, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sr.No', 'Date', 'Invoice No', 'Party Code', 'Party Name', 'GST No', 'Amount', 'E-Way Bill', 'IRN No', 'Status']);
            foreach ($documents as $doc) {
                fputcsv($file, [$doc['sr_no'], $doc['date'], $doc['invoice_no'], $doc['party_code'], $doc['party_name'], $doc['gst_number'], $doc['amount'], $doc['eway_bill'], $doc['irn_no'], $doc['status']]);
            }
            fputcsv($file, ['', '', '', '', '', 'Total:', $totals['amount'], '', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sale Remarks Report
     * Shows sales with remarks, P/N/A filter, stock filter
     */
    public function saleRemarks(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series');
        $pendingFilter = $request->get('pending_filter', 'A'); // P=Pending, N=Non-Pending, A=All
        $stockFilter = $request->get('stock_filter', '3'); // 1=With Stock, 2=Without Stock, 3=All

        $query = SaleTransaction::with(['customer:id,name,code', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereNotNull('remarks')
            ->where('remarks', '!=', '');

        if ($series) {
            $query->where('series', $series);
        }

        // Pending filter - based on balance_amount
        if ($pendingFilter === 'P') {
            $query->where('balance_amount', '>', 0);
        } elseif ($pendingFilter === 'N') {
            $query->where(function($q) {
                $q->whereNull('balance_amount')->orWhere('balance_amount', '<=', 0);
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        $remarks = $sales->map(function($sale) {
            return [
                'date' => $sale->sale_date->format('d-m-Y'),
                'bill_no' => ($sale->series ?? '') . $sale->invoice_no,
                'party_code' => $sale->customer->code ?? '',
                'party_name' => $sale->customer->name ?? 'N/A',
                'salesman' => $sale->salesman->name ?? '',
                'remarks' => $sale->remarks,
                'amount' => (float) $sale->net_amount,
                'balance' => (float) ($sale->balance_amount ?? 0),
                'status' => ($sale->balance_amount ?? 0) > 0 ? 'Pending' : 'Paid'
            ];
        });

        $totals = [
            'count' => $remarks->count(),
            'amount' => $remarks->sum('amount'),
            'balance' => $remarks->sum('balance'),
            'pending' => $remarks->where('status', 'Pending')->count(),
            'paid' => $remarks->where('status', 'Paid')->count()
        ];

        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->get('export') === 'excel') {
            return $this->exportSaleRemarksToExcel($remarks, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sale-remarks-print', compact(
                'remarks', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'pendingFilter', 'stockFilter'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sale-remarks', compact(
            'remarks', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'pendingFilter', 'stockFilter'
        ));
    }

    /**
     * Export Sale Remarks to Excel
     */
    private function exportSaleRemarksToExcel($remarks, $totals, $dateFrom, $dateTo)
    {
        $filename = 'sale_remarks_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($remarks, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Bill No', 'Party Code', 'Party Name', 'Salesman', 'Remarks', 'Amount', 'Balance', 'Status']);
            $i = 1;
            foreach ($remarks as $r) {
                fputcsv($file, [$i++, $r['date'], $r['bill_no'], $r['party_code'], $r['party_name'], $r['salesman'], $r['remarks'], $r['amount'], $r['balance'], $r['status']]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'Total:', $totals['amount'], $totals['balance'], '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Item Wise Discount Report
     * Shows item-wise discount with multiple filters
     */
    public function itemWiseDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'I'); // I=Item Wise, C=Company Wise
        $companyId = $request->get('company_id');
        $selectiveCompany = $request->get('selective_company', 'N');
        $itemWise = $request->get('item_wise', 'Y');
        $series = $request->get('series');
        $taggedCategories = $request->get('tagged_categories', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $categoryId = $request->get('category_id');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $customerId = $request->get('customer_id');
        $day = $request->get('day');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,salesman_id',
            'saleTransaction.customer:id,name,code,area_name,route_name',
            'saleTransaction.salesman:id,name'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $series, $customerId, $salesmanId, $areaId, $routeId, $day) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($series) $q->where('series', $series);
            if ($customerId) $q->where('customer_id', $customerId);
            if ($salesmanId) $q->where('salesman_id', $salesmanId);
            if ($day) $q->whereRaw('DAYNAME(sale_date) = ?', [$day]);
            
            if ($areaId || $routeId) {
                $q->whereHas('customer', function($cq) use ($areaId, $routeId) {
                    if ($areaId) {
                        $area = Area::find($areaId);
                        if ($area) $cq->where('area_name', $area->name);
                    }
                    if ($routeId) {
                        $route = Route::find($routeId);
                        if ($route) $cq->where('route_name', $route->name);
                    }
                });
            }
        })
        ->where(function($q) {
            $q->where('discount_percent', '>', 0)->orWhere('discount_amount', '>', 0);
        });

        // Company filter
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }

        // Category filter
        if ($categoryId) {
            $query->whereHas('item', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $saleItems = $query->get();

        // Group by item or company based on report type
        if ($reportType === 'C') {
            // Company Wise
            $groupedData = $saleItems->groupBy('company_name')->map(function($group, $companyName) {
                return [
                    'company_name' => $companyName ?: 'No Company',
                    'qty' => (float) $group->sum('qty'),
                    'gross' => (float) $group->sum('amount'),
                    'disc_amount' => (float) $group->sum('discount_amount'),
                    'net_amount' => (float) $group->sum('net_amount'),
                    'disc_percent' => $group->sum('amount') > 0 ? ($group->sum('discount_amount') / $group->sum('amount')) * 100 : 0
                ];
            })->values();
        } else {
            // Item Wise
            $groupedData = $saleItems->groupBy('item_id')->map(function($group) {
                $first = $group->first();
                return [
                    'item_code' => $first->item_code,
                    'item_name' => $first->item_name,
                    'company_name' => $first->company_name ?? '',
                    'qty' => (float) $group->sum('qty'),
                    'gross' => (float) $group->sum('amount'),
                    'disc_amount' => (float) $group->sum('discount_amount'),
                    'net_amount' => (float) $group->sum('net_amount'),
                    'disc_percent' => $group->sum('amount') > 0 ? ($group->sum('discount_amount') / $group->sum('amount')) * 100 : 0
                ];
            })->sortBy('item_name')->values();
        }

        $items = $groupedData;

        $totals = [
            'qty' => $items->sum('qty'),
            'gross' => $items->sum('gross'),
            'disc_amount' => $items->sum('disc_amount'),
            'net_amount' => $items->sum('net_amount'),
            'count' => $items->count()
        ];

        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->get('export') === 'excel') {
            return $this->exportItemWiseDiscountToExcel($items, $totals, $dateFrom, $dateTo, $reportType);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.item-wise-discount-print', compact(
                'items', 'totals', 'companies', 'salesmen', 'areas', 'routes', 'customers', 'categories', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'companyId', 'selectiveCompany', 'itemWise', 'series',
                'taggedCategories', 'removeTags', 'categoryId', 'salesmanId', 'areaId', 'routeId', 'customerId', 'day'
            ));
        }

        return view('admin.reports.sale-report.other-reports.item-wise-discount', compact(
            'items', 'totals', 'companies', 'salesmen', 'areas', 'routes', 'customers', 'categories', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'companyId', 'selectiveCompany', 'itemWise', 'series',
            'taggedCategories', 'removeTags', 'categoryId', 'salesmanId', 'areaId', 'routeId', 'customerId', 'day'
        ));
    }

    /**
     * Export Item Wise Discount to Excel
     */
    private function exportItemWiseDiscountToExcel($items, $totals, $dateFrom, $dateTo, $reportType)
    {
        $filename = 'item_wise_discount_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals, $reportType) {
            $file = fopen('php://output', 'w');
            if ($reportType === 'C') {
                fputcsv($file, ['#', 'Company', 'Qty', 'Gross Amt', 'Disc %', 'Disc Amt', 'Net Amt']);
            } else {
                fputcsv($file, ['#', 'Item Code', 'Item Name', 'Company', 'Qty', 'Gross Amt', 'Disc %', 'Disc Amt', 'Net Amt']);
            }
            $i = 1;
            foreach ($items as $item) {
                if ($reportType === 'C') {
                    fputcsv($file, [$i++, $item['company_name'], $item['qty'], $item['gross'], number_format($item['disc_percent'], 2) . '%', $item['disc_amount'], $item['net_amount']]);
                } else {
                    fputcsv($file, [$i++, $item['item_code'], $item['item_name'], $item['company_name'], $item['qty'], $item['gross'], number_format($item['disc_percent'], 2) . '%', $item['disc_amount'], $item['net_amount']]);
                }
            }
            fputcsv($file, ['', '', '', 'Total:', $totals['qty'], $totals['gross'], '', $totals['disc_amount'], $totals['net_amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Item Wise Scheme Report
     * Shows item-wise scheme/free qty details with multiple filters
     */
    public function itemWiseScheme(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'C'); // I=Item Wise, C=Company Wise
        $selectiveCompany = $request->get('selective_company', 'Y');
        $itemWise = $request->get('item_wise', 'Y');
        $series = $request->get('series');
        $companyId = $request->get('company_id');
        $taggedCategories = $request->get('tagged_categories', 'N');
        $removeTags = $request->get('remove_tags', 'N');
        $categoryId = $request->get('category_id');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $customerId = $request->get('customer_id');
        $day = $request->get('day');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,salesman_id',
            'saleTransaction.customer:id,name,code,area_name,route_name',
            'saleTransaction.salesman:id,name'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $series, $customerId, $salesmanId, $areaId, $routeId, $day) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($series) $q->where('series', $series);
            if ($customerId) $q->where('customer_id', $customerId);
            if ($salesmanId) $q->where('salesman_id', $salesmanId);
            if ($day) $q->whereRaw('DAYNAME(sale_date) = ?', [$day]);
            
            if ($areaId || $routeId) {
                $q->whereHas('customer', function($cq) use ($areaId, $routeId) {
                    if ($areaId) {
                        $area = Area::find($areaId);
                        if ($area) $cq->where('area_name', $area->name);
                    }
                    if ($routeId) {
                        $route = Route::find($routeId);
                        if ($route) $cq->where('route_name', $route->name);
                    }
                });
            }
        })
        ->where('free_qty', '>', 0); // Only items with free qty (scheme)

        // Company filter
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }

        // Category filter
        if ($categoryId) {
            $query->whereHas('item', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $saleItems = $query->get();

        // Group by item or company based on report type
        if ($reportType === 'C') {
            // Company Wise
            $groupedData = $saleItems->groupBy('company_name')->map(function($group, $companyName) {
                $saleQty = (float) $group->sum('qty');
                $freeQty = (float) $group->sum('free_qty');
                $schemePercent = $saleQty > 0 ? ($freeQty / $saleQty) * 100 : 0;
                $schemeAmount = (float) $group->sum(function($item) {
                    return ($item->free_qty ?? 0) * ($item->sale_rate ?? 0);
                });
                return [
                    'company_name' => $companyName ?: 'No Company',
                    'sale_qty' => $saleQty,
                    'free_qty' => $freeQty,
                    'scheme_percent' => $schemePercent,
                    'scheme_amount' => $schemeAmount
                ];
            })->values();
        } else {
            // Item Wise
            $groupedData = $saleItems->groupBy('item_id')->map(function($group) {
                $first = $group->first();
                $saleQty = (float) $group->sum('qty');
                $freeQty = (float) $group->sum('free_qty');
                $schemePercent = $saleQty > 0 ? ($freeQty / $saleQty) * 100 : 0;
                $schemeAmount = (float) $group->sum(function($item) {
                    return ($item->free_qty ?? 0) * ($item->sale_rate ?? 0);
                });
                return [
                    'item_code' => $first->item_code,
                    'item_name' => $first->item_name,
                    'company_name' => $first->company_name ?? '',
                    'sale_qty' => $saleQty,
                    'free_qty' => $freeQty,
                    'scheme_percent' => $schemePercent,
                    'scheme_amount' => $schemeAmount
                ];
            })->sortBy('item_name')->values();
        }

        $items = $groupedData;

        $totals = [
            'sale_qty' => $items->sum('sale_qty'),
            'free_qty' => $items->sum('free_qty'),
            'scheme_amount' => $items->sum('scheme_amount'),
            'count' => $items->count()
        ];

        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->get('export') === 'excel') {
            return $this->exportItemWiseSchemeToExcel($items, $totals, $dateFrom, $dateTo, $reportType);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.item-wise-scheme-print', compact(
                'items', 'totals', 'companies', 'salesmen', 'areas', 'routes', 'customers', 'categories', 'seriesList',
                'dateFrom', 'dateTo', 'reportType', 'companyId', 'selectiveCompany', 'itemWise', 'series',
                'taggedCategories', 'removeTags', 'categoryId', 'salesmanId', 'areaId', 'routeId', 'customerId', 'day'
            ));
        }

        return view('admin.reports.sale-report.other-reports.item-wise-scheme', compact(
            'items', 'totals', 'companies', 'salesmen', 'areas', 'routes', 'customers', 'categories', 'seriesList',
            'dateFrom', 'dateTo', 'reportType', 'companyId', 'selectiveCompany', 'itemWise', 'series',
            'taggedCategories', 'removeTags', 'categoryId', 'salesmanId', 'areaId', 'routeId', 'customerId', 'day'
        ));
    }

    /**
     * Export Item Wise Scheme to Excel
     */
    private function exportItemWiseSchemeToExcel($items, $totals, $dateFrom, $dateTo, $reportType)
    {
        $filename = 'item_wise_scheme_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals, $reportType) {
            $file = fopen('php://output', 'w');
            if ($reportType === 'C') {
                fputcsv($file, ['#', 'Company', 'Sale Qty', 'Free Qty', 'Scheme %', 'Scheme Amt']);
            } else {
                fputcsv($file, ['#', 'Item Code', 'Item Name', 'Company', 'Sale Qty', 'Free Qty', 'Scheme %', 'Scheme Amt']);
            }
            $i = 1;
            foreach ($items as $item) {
                if ($reportType === 'C') {
                    fputcsv($file, [$i++, $item['company_name'], $item['sale_qty'], $item['free_qty'], number_format($item['scheme_percent'], 2) . '%', $item['scheme_amount']]);
                } else {
                    fputcsv($file, [$i++, $item['item_code'], $item['item_name'], $item['company_name'], $item['sale_qty'], $item['free_qty'], number_format($item['scheme_percent'], 2) . '%', $item['scheme_amount']]);
                }
            }
            fputcsv($file, ['', '', 'Total:', $totals['sale_qty'], $totals['free_qty'], '', $totals['scheme_amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tax Percentage Wise Sale Report
     * Shows sales grouped by GST percentage
     */
    public function taxPercentageWiseSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get sale items grouped by GST percentage
        $saleItems = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        })->get();

        // Group by GST percentage
        $groupedData = $saleItems->groupBy(function($item) {
            return (float) ($item->gst_percent ?? 0);
        })->map(function($group, $gstPercent) {
            $taxableValue = (float) $group->sum('amount');
            $cgst = (float) $group->sum('cgst_amount');
            $sgst = (float) $group->sum('sgst_amount');
            $igst = (float) $group->sum(function($item) {
                return $item->igst_amount ?? 0;
            });
            $totalTax = $cgst + $sgst + $igst;
            
            return [
                'gst_percent' => $gstPercent,
                'taxable_value' => $taxableValue,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_tax' => $totalTax,
                'invoice_value' => $taxableValue + $totalTax
            ];
        })->sortBy('gst_percent')->values();

        $taxData = $groupedData;

        $totals = [
            'taxable_value' => $taxData->sum('taxable_value'),
            'cgst' => $taxData->sum('cgst'),
            'sgst' => $taxData->sum('sgst'),
            'igst' => $taxData->sum('igst'),
            'total_tax' => $taxData->sum('total_tax'),
            'invoice_value' => $taxData->sum('invoice_value'),
            'count' => $taxData->count()
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportTaxPercentageWiseSaleToExcel($taxData, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.tax-percentage-wise-sale-print', compact(
                'taxData', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.other-reports.tax-percentage-wise-sale', compact(
            'taxData', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Export Tax Percentage Wise Sale to Excel
     */
    private function exportTaxPercentageWiseSaleToExcel($taxData, $totals, $dateFrom, $dateTo)
    {
        $filename = 'tax_percentage_wise_sale_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($taxData, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'GST %', 'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Invoice Value']);
            $i = 1;
            foreach ($taxData as $tax) {
                fputcsv($file, [$i++, $tax['gst_percent'] . '%', $tax['taxable_value'], $tax['cgst'], $tax['sgst'], $tax['igst'], $tax['total_tax'], $tax['invoice_value']]);
            }
            fputcsv($file, ['', 'Total:', $totals['taxable_value'], $totals['cgst'], $totals['sgst'], $totals['igst'], $totals['total_tax'], $totals['invoice_value']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Transaction Book with Address Report
     * Shows sales with customer address details
     */
    public function transactionBookAddress(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series');
        $reportBy = $request->get('report_by', 'S'); // T=Transaction, S=Series
        $partyStatus = $request->get('party_status');

        $query = SaleTransaction::with(['customer:id,name,code,address,mobile,gst_number,area_name,route_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($series) {
            $query->where('series', $series);
        }

        // Party status filter (if applicable)
        if ($partyStatus) {
            $query->whereHas('customer', function($q) use ($partyStatus) {
                $q->where('status', $partyStatus);
            });
        }

        // Order by series or transaction
        if ($reportBy === 'S') {
            $query->orderBy('series')->orderBy('invoice_no');
        } else {
            $query->orderBy('sale_date')->orderBy('invoice_no');
        }

        $sales = $query->get();

        $totals = [
            'count' => $sales->count(),
            'amount' => (float) $sales->sum('net_amount')
        ];

        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        if ($request->get('export') === 'excel') {
            return $this->exportTransactionBookAddressToExcel($sales, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.transaction-book-address-print', compact(
                'sales', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'reportBy', 'partyStatus'
            ));
        }

        return view('admin.reports.sale-report.other-reports.transaction-book-address', compact(
            'sales', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series', 'reportBy', 'partyStatus'
        ));
    }

    /**
     * Export Transaction Book with Address to Excel
     */
    private function exportTransactionBookAddressToExcel($sales, $totals, $dateFrom, $dateTo)
    {
        $filename = 'transaction_book_address_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sales, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Bill No', 'Party Name', 'Address', 'Mobile', 'GST No', 'Amount']);
            $i = 1;
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $i++,
                    $sale->sale_date->format('d-m-Y'),
                    ($sale->series ?? '') . $sale->invoice_no,
                    $sale->customer->name ?? 'N/A',
                    $sale->customer->address ?? '-',
                    $sale->customer->mobile ?? '-',
                    $sale->customer->gst_number ?? '-',
                    $sale->net_amount
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'Total:', $totals['amount']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sale/Stock Detail Report
     * Shows sale vs stock comparison by company
     */
    public function saleStockDetail(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        // Get sale items grouped by item
        $query = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $query->where('company_name', $company->name);
            }
        }

        $saleItems = $query->select(
            'item_id', 'item_code', 'item_name', 'company_name',
            DB::raw('SUM(qty) as sold_qty'),
            DB::raw('SUM(net_amount) as sale_value')
        )->groupBy('item_id', 'item_code', 'item_name', 'company_name')->get();

        // Get current stock for these items
        $itemIds = $saleItems->pluck('item_id');
        $stockData = \App\Models\Batch::whereIn('item_id', $itemIds)
            ->select('item_id', DB::raw('SUM(qty) as current_stock'))
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        // Build report data
        $items = $saleItems->map(function($item) use ($stockData) {
            $currentStock = (float) ($stockData->get($item->item_id)->current_stock ?? 0);
            $soldQty = (float) $item->sold_qty;
            // Opening stock = current stock + sold qty (approximate)
            $openingStock = $currentStock + $soldQty;
            
            return [
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'company_name' => $item->company_name ?? '',
                'stock' => $openingStock,
                'sold' => $soldQty,
                'balance' => $currentStock,
                'sale_value' => (float) $item->sale_value
            ];
        })->sortBy('item_name')->values();

        $totals = [
            'stock' => $items->sum('stock'),
            'sold' => $items->sum('sold'),
            'balance' => $items->sum('balance'),
            'sale_value' => $items->sum('sale_value'),
            'count' => $items->count()
        ];

        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        if ($request->get('export') === 'excel') {
            return $this->exportSaleStockDetailToExcel($items, $totals, $dateFrom, $dateTo);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sale-stock-detail-print', compact(
                'items', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sale-stock-detail', compact(
            'items', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId'
        ));
    }

    /**
     * Export Sale/Stock Detail to Excel
     */
    private function exportSaleStockDetailToExcel($items, $totals, $dateFrom, $dateTo)
    {
        $filename = 'sale_stock_detail_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($items, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Item Code', 'Item Name', 'Company', 'Stock', 'Sold', 'Balance', 'Sale Value']);
            $i = 1;
            foreach ($items as $item) {
                fputcsv($file, [$i++, $item['item_code'], $item['item_name'], $item['company_name'], $item['stock'], $item['sold'], $item['balance'], $item['sale_value']]);
            }
            fputcsv($file, ['', '', '', 'Total:', $totals['stock'], $totals['sold'], $totals['balance'], $totals['sale_value']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Customer Stock Details Report
     * Shows customer-wise stock/sale details
     */
    public function customerStockDetails(Request $request)
    {
        $asOnDate = $request->get('as_on_date', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $itemId = $request->get('item_id');
        $flag = $request->get('flag');

        // Get sale items for the customer
        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,sale_date,customer_id',
            'saleTransaction.customer:id,name,code'
        ])
        ->whereHas('saleTransaction', function($q) use ($asOnDate, $customerId) {
            $q->where('sale_date', '<=', $asOnDate);
            if ($customerId) {
                $q->where('customer_id', $customerId);
            }
        });

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $saleItems = $query->get();

        // Group by customer and item
        $groupedData = $saleItems->groupBy(function($item) {
            return $item->saleTransaction->customer_id . '_' . $item->item_id;
        })->map(function($group) {
            $first = $group->first();
            $lastSale = $group->sortByDesc(function($item) {
                return $item->saleTransaction->sale_date;
            })->first();
            
            return [
                'customer_code' => $first->saleTransaction->customer->code ?? '',
                'customer_name' => $first->saleTransaction->customer->name ?? 'N/A',
                'item_code' => $first->item_code,
                'item_name' => $first->item_name,
                'qty_sold' => (float) $group->sum('qty'),
                'value' => (float) $group->sum('net_amount'),
                'last_sale' => $lastSale->saleTransaction->sale_date->format('d-m-Y')
            ];
        })->sortBy('customer_name')->values();

        $stockData = $groupedData;

        $totals = [
            'qty_sold' => $stockData->sum('qty_sold'),
            'value' => $stockData->sum('value'),
            'count' => $stockData->count()
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $itemsList = Item::select('id', 'name')->orderBy('name')->get();

        if ($request->get('export') === 'excel') {
            return $this->exportCustomerStockDetailsToExcel($stockData, $totals, $asOnDate);
        }

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.customer-stock-details-print', compact(
                'stockData', 'totals', 'customers', 'itemsList', 'asOnDate', 'customerId', 'itemId', 'flag'
            ));
        }

        return view('admin.reports.sale-report.other-reports.customer-stock-details', compact(
            'stockData', 'totals', 'customers', 'itemsList', 'asOnDate', 'customerId', 'itemId', 'flag'
        ));
    }

    /**
     * Export Customer Stock Details to Excel
     */
    private function exportCustomerStockDetailsToExcel($stockData, $totals, $asOnDate)
    {
        $filename = 'customer_stock_details_' . $asOnDate . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($stockData, $totals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Customer Code', 'Customer Name', 'Item', 'Qty Sold', 'Value', 'Last Sale']);
            $i = 1;
            foreach ($stockData as $data) {
                fputcsv($file, [$i++, $data['customer_code'], $data['customer_name'], $data['item_name'], $data['qty_sold'], $data['value'], $data['last_sale']]);
            }
            fputcsv($file, ['', '', '', 'Total:', $totals['qty_sold'], $totals['value'], '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GST Sale Book Report
     */
    public function gstSaleBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $saleType = $request->get('sale_type', '1');
        $gstDetail = $request->get('gst_detail', 'Y');

        $query = SaleTransaction::with('customer:id,name,gst_number')
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($saleType == '1') {
            $query->where('payment_mode', 'cash');
        } elseif ($saleType == '2') {
            $query->where('payment_mode', 'credit');
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        $totals = [
            'taxable' => $sales->sum('nt_amount'),
            'cgst' => $sales->sum('cgst_amount'),
            'sgst' => $sales->sum('sgst_amount'),
            'igst' => $sales->sum('igst_amount'),
            'total' => $sales->sum('net_amount')
        ];

        return view('admin.reports.sale-report.other-reports.gst-sale-book', compact(
            'sales', 'totals', 'dateFrom', 'dateTo', 'saleType', 'gstDetail'
        ));
    }

    /**
     * Customer Consistency Report
     */
    public function customerConsistency(Request $request)
    {
        $period1From = $request->get('period1_from', Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'));
        $period1To = $request->get('period1_to', Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d'));
        $period2From = $request->get('period2_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $period2To = $request->get('period2_to', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', '3');
        $itemId = $request->get('item_id');

        $itemsList = Item::select('id', 'name')->orderBy('name')->get();

        // Get Period 1 sales by customer
        $period1Sales = SaleTransaction::with('customer:id,name,code,area_id')
            ->whereBetween('sale_date', [$period1From, $period1To])
            ->select('customer_id', DB::raw('COUNT(*) as bill_count'), DB::raw('SUM(net_amount) as total_value'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // Get Period 2 sales by customer
        $period2Sales = SaleTransaction::with('customer:id,name,code,area_id')
            ->whereBetween('sale_date', [$period2From, $period2To])
            ->select('customer_id', DB::raw('COUNT(*) as bill_count'), DB::raw('SUM(net_amount) as total_value'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // Merge customer IDs from both periods
        $allCustomerIds = $period1Sales->keys()->merge($period2Sales->keys())->unique();
        $customersData = Customer::whereIn('id', $allCustomerIds)->with('area:id,name')->get()->keyBy('id');

        $customers = $allCustomerIds->map(function($customerId) use ($period1Sales, $period2Sales, $customersData) {
            $customer = $customersData->get($customerId);
            $p1 = $period1Sales->get($customerId);
            $p2 = $period2Sales->get($customerId);
            return [
                'code' => $customer->code ?? '',
                'name' => $customer->name ?? 'N/A',
                'area' => $customer->area->name ?? '',
                'period1_bills' => $p1->bill_count ?? 0,
                'period1_value' => $p1->total_value ?? 0,
                'period2_bills' => $p2->bill_count ?? 0,
                'period2_value' => $p2->total_value ?? 0,
            ];
        })->filter(function($c) use ($reportType) {
            if ($reportType == '1') return ($c['period1_bills'] > 0 && $c['period2_bills'] > 0);
            if ($reportType == '2') return ($c['period1_bills'] == 0 || $c['period2_bills'] == 0);
            return true;
        })->sortBy('name')->values();

        $totals = [
            'period1_bills' => $customers->sum('period1_bills'),
            'period1_value' => $customers->sum('period1_value'),
            'period2_bills' => $customers->sum('period2_bills'),
            'period2_value' => $customers->sum('period2_value'),
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.customer-consistency-print', compact(
                'customers', 'totals', 'itemsList', 'period1From', 'period1To', 'period2From', 'period2To', 'reportType', 'itemId'
            ));
        }

        return view('admin.reports.sale-report.other-reports.customer-consistency', compact(
            'customers', 'totals', 'itemsList', 'period1From', 'period1To', 'period2From', 'period2To', 'reportType', 'itemId'
        ));
    }

    /**
     * Sale Return Adjustment Report
     */
    public function saleReturnAdjustment(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $returns = SaleReturnTransaction::with('customer:id,name')
            ->whereBetween('return_date', [$dateFrom, $dateTo])
            ->orderBy('return_date')
            ->orderBy('sr_no')
            ->get();

        $adjustments = $returns->map(function($return, $index) {
            $adjustedAmount = 0; // Would come from adjustment table if exists
            return [
                'date' => $return->return_date->format('d-m-Y'),
                'trn_no' => $return->sr_no,
                'party_name' => $return->customer->name ?? 'N/A',
                'amount' => $return->net_amount ?? 0,
                'adj_bill' => '',
                'bal_amt' => ($return->net_amount ?? 0) - $adjustedAmount,
            ];
        });

        $totals = [
            'amount' => $adjustments->sum('amount'),
            'bal_amt' => $adjustments->sum('bal_amt'),
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.sale-return-adjustment-print', compact(
                'adjustments', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.other-reports.sale-return-adjustment', compact(
            'adjustments', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Pending Orders Report
     */
    public function pendingOrders(Request $request)
    {
        $orderType = $request->get('order_type', 'C');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');
        $stateId = $request->get('state_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        // Get pending orders from pending_orders table
        $query = \App\Models\PendingOrder::with(['supplier:supplier_id,name', 'item:id,name'])
            ->where('balance_qty', '>', 0);

        $pendingOrders = $query->orderBy('order_date', 'desc')->get();

        $orders = $pendingOrders->map(function($order) {
            return [
                'date' => $order->order_date ? $order->order_date->format('d-m-Y') : '',
                'order_no' => $order->order_no ?? '',
                'party_name' => $order->supplier->name ?? 'N/A',
                'item_name' => $order->item->name ?? 'N/A',
                'ordered' => $order->order_qty ?? 0,
                'delivered' => ($order->order_qty ?? 0) - ($order->balance_qty ?? 0),
                'pending' => $order->balance_qty ?? 0,
                'status' => ($order->balance_qty ?? 0) <= 0 ? 'Complete' : 'Pending',
            ];
        });

        $totals = [
            'ordered' => $orders->sum('ordered'),
            'delivered' => $orders->sum('delivered'),
            'pending' => $orders->sum('pending'),
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.pending-orders-print', compact(
                'orders', 'totals', 'orderType', 'salesmen', 'areas', 'routes', 'states',
                'salesmanId', 'areaId', 'routeId', 'stateId'
            ));
        }

        return view('admin.reports.sale-report.other-reports.pending-orders', compact(
            'orders', 'totals', 'orderType', 'salesmen', 'areas', 'routes', 'states',
            'salesmanId', 'areaId', 'routeId', 'stateId'
        ));
    }

    /**
     * ST-38 OutWord Report (Stock Transfer)
     */
    public function st38Outword(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get stock transfer outgoing transactions
        $stockTransfers = \App\Models\StockTransferOutgoingTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date')
            ->orderBy('sr_no')
            ->get();

        $transfers = $stockTransfers->map(function($txn) {
            return [
                'date' => $txn->transaction_date ? $txn->transaction_date->format('d-m-Y') : '',
                'st38_no' => $txn->sr_no ?? '',
                'party_name' => $txn->transfer_to_name ?? 'N/A',
                'state' => '',
                'taxable' => $txn->gross_amount ?? 0,
                'tax' => $txn->tax_amount ?? 0,
                'total' => $txn->net_amount ?? 0,
            ];
        });

        $totals = [
            'taxable' => $transfers->sum('taxable'),
            'tax' => $transfers->sum('tax'),
            'total' => $transfers->sum('total'),
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.other-reports.st38-outword-print', compact(
                'transfers', 'totals', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.other-reports.st38-outword', compact(
            'transfers', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Frige Item Report
     * Shows frige/cold storage items sold
     */
    public function frigeItem(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $billFrom = $request->get('bill_from');
        $billTo = $request->get('bill_to');
        $categoryId = $request->get('category_id');
        $status = $request->get('status');
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,salesman_id',
            'saleTransaction.customer:id,name,code',
            'item:id,name,category'
        ])
        ->whereHas('saleTransaction', function($q) use ($date, $billFrom, $billTo, $salesmanId, $areaId, $routeId) {
            $q->whereDate('sale_date', $date);
            if ($billFrom) $q->where('invoice_no', '>=', $billFrom);
            if ($billTo) $q->where('invoice_no', '<=', $billTo);
            if ($salesmanId) $q->where('salesman_id', $salesmanId);
            if ($areaId || $routeId) {
                $q->whereHas('customer', function($cq) use ($areaId, $routeId) {
                    if ($areaId) $cq->where('area_code', $areaId);
                    if ($routeId) $cq->where('route_code', $routeId);
                });
            }
        })
        ->whereHas('item', function($q) use ($categoryId, $status) {
            $q->where(function($sq) {
                $sq->where('category', 'like', '%frige%')
                   ->orWhere('category', 'like', '%cold%')
                   ->orWhere('category', 'like', '%refriger%');
            });
            if ($categoryId) $q->where('category_id', $categoryId);
            if ($status) $q->where('status', $status);
        });

        $saleItems = $query->get();

        $items = $saleItems->map(function($item) {
            return [
                'invoice_no' => ($item->saleTransaction->series ?? '') . $item->saleTransaction->invoice_no,
                'date' => $item->saleTransaction->sale_date->format('d-m-Y'),
                'customer' => $item->saleTransaction->customer->name ?? 'N/A',
                'item_name' => $item->item_name,
                'qty' => (float) $item->qty,
                'amount' => (float) $item->net_amount,
            ];
        });

        $totals = [
            'qty' => $items->sum('qty'),
            'amount' => $items->sum('amount'),
        ];

        $categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.other-reports.frige-item', compact(
            'items', 'totals', 'categories', 'salesmen', 'areas', 'routes',
            'date', 'billFrom', 'billTo', 'categoryId', 'status', 'salesmanId', 'areaId', 'routeId'
        ));
    }

    /**
     * Volume Discount Report
     * Shows volume-based discounts by party and company
     */
    public function volumeDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $companyId = $request->get('company_id');
        $volumeOnly = $request->get('volume_only');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,sale_date,customer_id',
            'saleTransaction.customer:id,name,code'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $customerId) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($customerId) $q->where('customer_id', $customerId);
        });

        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) $query->where('company_name', $company->name);
        }

        if ($volumeOnly) {
            $query->where('discount_amount', '>', 0);
        }

        $saleItems = $query->get();

        $groupedData = $saleItems->groupBy(function($item) {
            return ($item->saleTransaction->customer->name ?? 'N/A') . '|' . ($item->company_name ?? 'N/A');
        })->map(function($group, $key) {
            $parts = explode('|', $key);
            return [
                'party_name' => $parts[0],
                'company' => $parts[1],
                'qty' => (float) $group->sum('qty'),
                'sale_value' => (float) $group->sum('amount'),
                'discount' => (float) $group->sum('discount_amount'),
                'net_value' => (float) $group->sum('net_amount'),
            ];
        })->values();

        $discounts = $groupedData;

        $totals = [
            'qty' => $discounts->sum('qty'),
            'sale_value' => $discounts->sum('sale_value'),
            'discount' => $discounts->sum('discount'),
            'net_value' => $discounts->sum('net_value'),
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.other-reports.volume-discount', compact(
            'discounts', 'totals', 'customers', 'companies',
            'dateFrom', 'dateTo', 'customerId', 'companyId', 'volumeOnly'
        ));
    }

    /**
     * Party Volume Discount Report
     * Shows volume discounts grouped by party
     */
    public function partyVolumeDiscount(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');

        $query = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->get();

        $groupedData = $sales->groupBy('customer_id')->map(function($group) {
            $customer = $group->first()->customer;
            $totalSale = (float) $group->sum('net_amount');
            $volumeDiscount = (float) $group->sum('dis_amount');
            return [
                'party_code' => $customer->code ?? '',
                'party_name' => $customer->name ?? 'N/A',
                'total_sale' => $totalSale,
                'volume_discount' => $volumeDiscount,
                'net_amount' => $totalSale - $volumeDiscount,
            ];
        })->sortBy('party_name')->values();

        $discounts = $groupedData;

        $totals = [
            'total_sale' => $discounts->sum('total_sale'),
            'volume_discount' => $discounts->sum('volume_discount'),
            'net_amount' => $discounts->sum('net_amount'),
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.other-reports.party-volume-discount', compact(
            'discounts', 'totals', 'customers', 'dateFrom', 'dateTo', 'customerId'
        ));
    }

    /**
     * Schedule H1 Drugs Report
     * Shows sales of Schedule H1 drugs with patient/doctor details
     */
    public function scheduleH1Drugs(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,series,sale_date,customer_id,patient_name,doctor_name',
            'saleTransaction.customer:id,name,address',
            'item:id,name,schedule'
        ])
        ->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        })
        ->whereHas('item', function($q) {
            $q->where('schedule', 'H1')
              ->orWhere('schedule', 'like', '%H1%');
        });

        $saleItems = $query->get();

        $drugs = $saleItems->map(function($item) {
            return [
                'date' => $item->saleTransaction->sale_date->format('d-m-Y'),
                'bill_no' => ($item->saleTransaction->series ?? '') . $item->saleTransaction->invoice_no,
                'drug_name' => $item->item_name,
                'batch' => $item->batch_no ?? '',
                'qty' => (float) $item->qty,
                'patient_name' => $item->saleTransaction->patient_name ?? '',
                'doctor_name' => $item->saleTransaction->doctor_name ?? '',
                'address' => $item->saleTransaction->customer->address ?? '',
            ];
        });

        $totals = [
            'qty' => $drugs->sum('qty'),
        ];

        return view('admin.reports.sale-report.other-reports.schedule-h1-drugs', compact(
            'drugs', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Sale Book SC (Special Category) Report
     * Shows sales with special charges breakdown
     */
    public function saleBookSc(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $series = $request->get('series');

        $query = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($series) {
            $query->where('series', $series);
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        $totals = [
            'gross' => $sales->sum('gross_amount'),
            'disc' => $sales->sum('dis_amount'),
            'sc' => $sales->sum('sc_amount'),
            'tax' => $sales->sum('tax_amount'),
            'net' => $sales->sum('net_amount'),
        ];

        $seriesList = SaleTransaction::distinct()->pluck('series')->filter();

        return view('admin.reports.sale-report.other-reports.sale-book-sc', compact(
            'sales', 'totals', 'seriesList', 'dateFrom', 'dateTo', 'series'
        ));
    }

    /**
     * Sale Book Summarised Report
     * Shows summarised sales by customer
     */
    public function saleBookSummarised(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $selective = $request->get('selective', 'N');
        $customerCode = $request->get('customer_code', '00');
        $customerId = $request->get('customer_id');

        $query = SaleTransaction::with(['customer:id,name,code'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($selective === 'Y' && $customerId) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->get();

        // Group by customer
        $groupedData = $sales->groupBy('customer_id')->map(function($group) {
            $customer = $group->first()->customer;
            return [
                'customer_code' => $customer->code ?? '',
                'customer_name' => $customer->name ?? 'N/A',
                'bill_count' => $group->count(),
                'gross' => (float) $group->sum('gross_amount'),
                'disc' => (float) $group->sum('dis_amount'),
                'tax' => (float) $group->sum('tax_amount'),
                'net' => (float) $group->sum('net_amount'),
            ];
        })->sortBy('customer_name')->values();

        $summary = $groupedData;

        $totals = [
            'bills' => $summary->sum('bill_count'),
            'gross' => $summary->sum('gross'),
            'disc' => $summary->sum('disc'),
            'tax' => $summary->sum('tax'),
            'net' => $summary->sum('net'),
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.other-reports.sale-book-summarised', compact(
            'summary', 'totals', 'customers', 'dateFrom', 'dateTo', 'selective', 'customerCode', 'customerId'
        ));
    }
}
