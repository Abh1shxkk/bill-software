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
        if ($request->get('view_type') === 'print') {
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

        // Handle Print view
        if ($request->get('view_type') === 'print') {
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
        if ($request->get('view_type') === 'print') {
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
        if ($request->get('view_type') === 'print') {
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
        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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
        $itemsList = Item::select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        $totals = [
            'sale_qty' => $items->sum('sale_qty'),
            'sale_amount' => $items->sum('sale_amount'),
            'return_qty' => $items->sum('return_qty'),
            'return_amount' => $items->sum('return_amount'),
            'net_qty' => $items->sum('net_qty'),
            'net_amount' => $items->sum('net_amount')
        ];

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

        if ($request->get('view_type') === 'print') {
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

        // Handle tagging
        $taggedArray = $taggedIds ? explode(',', $taggedIds) : [];

        $totals = [
            'count' => $challans->count(),
            'net_amount' => (float) $challans->sum('net_amount'),
            'tagged_count' => count($taggedArray),
            'tagged_amount' => (float) $challans->whereIn('id', $taggedArray)->sum('net_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-challan-reports.sale-challan-book-print', compact(
                'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas', 'taggedArray',
                'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId',
                'flag', 'dsFormat', 'day', 'orderBy', 'holdOnly', 'taggedIds'
            ));
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

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-challan-reports.pending-challans-print', compact(
                'challans', 'totals', 'customers', 'salesmen', 'routes', 'areas',
                'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'routeId', 'areaId', 'flag'
            ));
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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
            ->select('item_id', DB::raw('SUM(current_qty) as current_stock'))
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        if ($request->get('view_type') === 'print') {
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

        $states = State::select('id', 'name', 'code')->orderBy('name')->get();

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
     * Sales Man and other Level Sale Report
     */
    public function salesmanLevelSale(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code', 'area_mgr_name')->orderBy('name')->get();

        $sales = SaleTransaction::with(['salesman:id,name,code,area_mgr_name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->get();

        $groupedSales = $sales->groupBy(fn($s) => $s->salesman->area_mgr_name ?? 'No Manager');

        $totals = [
            'count' => $sales->count(),
            'net_amount' => (float) $sales->sum('net_amount')
        ];

        if ($request->get('view_type') === 'print') {
            return view('admin.reports.sale-report.sale-book.salesman-level-sale-print', compact(
                'groupedSales', 'totals', 'salesmen', 'dateFrom', 'dateTo'
            ));
        }

        return view('admin.reports.sale-report.sale-book.salesman-level-sale', compact(
            'groupedSales', 'totals', 'salesmen', 'dateFrom', 'dateTo'
        ));
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
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.all-salesman', compact(
            'salesmen', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Bill Wise
     */
    public function salesmanWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.bill-wise', compact(
            'salesmen', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Customer Wise
     */
    public function salesmanWiseSalesCustomerWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.customer-wise', compact(
            'salesmen', 'customers', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Item Wise
     */
    public function salesmanWiseSalesItemWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.item-wise', compact(
            'salesmen', 'items', 'companies', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Company Wise
     */
    public function salesmanWiseSalesCompanyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.company-wise', compact(
            'salesmen', 'companies', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Area Wise
     */
    public function salesmanWiseSalesAreaWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.area-wise', compact(
            'salesmen', 'areas', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - Route Wise
     */
    public function salesmanWiseSalesRouteWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.route-wise', compact(
            'salesmen', 'routes', 'dateFrom', 'dateTo', 'salesmanId'
        ));
    }

    /**
     * Salesman Wise Sales - State Wise
     */
    public function salesmanWiseSalesStateWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');

        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.salesman-wise-sales.state-wise', compact(
            'salesmen', 'states', 'dateFrom', 'dateTo', 'salesmanId'
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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sale-report.miscellaneous-sale-analysis.area-wise-sales.all-area', compact(
            'dateFrom', 'dateTo', 'areas', 'salesmen', 'routes', 'states'
        ));
    }

    /**
     * Area Wise Sales - Bill Wise
     */
    public function areaWiseSalesBillWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $routes = Route::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $companies = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $divisions = Company::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
        $routes = Route::select('id', 'name')->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $areas = Area::where('is_deleted', '!=', 1)->select('id', 'name')->orderBy('name')->get();
        $states = State::select('id', 'name')->orderBy('name')->get();

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
}
