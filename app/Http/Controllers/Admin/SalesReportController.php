<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SaleReturnTransaction;
use App\Models\SaleReturnTransactionItem;
use App\Models\SaleChallanTransaction;
use App\Models\SaleChallanTransactionItem;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Models\Item;
use App\Models\Company;
use App\Models\Area;
use App\Models\Route;
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
        return view('admin.reports.sales.index');
    }

    /**
     * Sales Book Report - Date wise all sales
     */
    public function salesBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');
        $series = $request->get('series');

        $query = SaleTransaction::with(['customer:id,name,code', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);
        if ($series) $query->where('series', $series);

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

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

        return view('admin.reports.sales.sales-book', compact(
            'sales', 'totals', 'customers', 'salesmen', 'seriesList',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId', 'series'
        ));
    }

    /**
     * Sales Book Party Wise - Grouped by customer
     */
    public function salesBookPartyWise(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');

        $query = SaleTransaction::with(['customer:id,name,code,area_name,route_name', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);

        $sales = $query->orderBy('customer_id')->orderBy('sale_date')->get();
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

        return view('admin.reports.sales.sales-book-party-wise', compact(
            'groupedSales', 'totals', 'customers', 'salesmen',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId'
        ));
    }

    /**
     * Day Sales Summary - Item Wise
     */
    public function daySalesSummaryItemWise(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        $query = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($date) {
            $q->whereDate('sale_date', $date);
        })->with(['item:id,name,code,company_id', 'saleTransaction:id,invoice_no,customer_id']);

        if ($companyId) {
            $query->whereHas('item', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $items = $query->select(
            'item_id', 'item_code', 'item_name', 'company_name',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free'),
            DB::raw('SUM(net_amount) as total_amount'),
            DB::raw('COUNT(DISTINCT sale_transaction_id) as bill_count')
        )
        ->groupBy('item_id', 'item_code', 'item_name', 'company_name')
        ->orderBy('item_name')
        ->get();

        $totals = [
            'qty' => $items->sum('total_qty'),
            'free' => $items->sum('total_free'),
            'amount' => $items->sum('total_amount'),
            'items' => $items->count()
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.day-sales-summary-item-wise', compact(
            'items', 'totals', 'companies', 'date', 'companyId'
        ));
    }

    /**
     * Sales Summary - Period wise summary
     */
    public function salesSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'date'); // date, customer, salesman, company

        $baseQuery = SaleTransaction::whereBetween('sale_date', [$dateFrom, $dateTo]);

        switch ($groupBy) {
            case 'customer':
                $summary = (clone $baseQuery)
                    ->select('customer_id', 
                        DB::raw('COUNT(*) as invoice_count'),
                        DB::raw('SUM(nt_amount) as total_nt'),
                        DB::raw('SUM(dis_amount) as total_dis'),
                        DB::raw('SUM(tax_amount) as total_tax'),
                        DB::raw('SUM(net_amount) as total_net'))
                    ->with('customer:id,name,code')
                    ->groupBy('customer_id')
                    ->orderByDesc('total_net')
                    ->get();
                break;

            case 'salesman':
                $summary = (clone $baseQuery)
                    ->select('salesman_id',
                        DB::raw('COUNT(*) as invoice_count'),
                        DB::raw('SUM(nt_amount) as total_nt'),
                        DB::raw('SUM(dis_amount) as total_dis'),
                        DB::raw('SUM(tax_amount) as total_tax'),
                        DB::raw('SUM(net_amount) as total_net'))
                    ->with('salesman:id,name')
                    ->groupBy('salesman_id')
                    ->orderByDesc('total_net')
                    ->get();
                break;

            case 'company':
                $summary = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
                        $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    })
                    ->select('company_name',
                        DB::raw('COUNT(DISTINCT sale_transaction_id) as invoice_count'),
                        DB::raw('SUM(qty) as total_qty'),
                        DB::raw('SUM(net_amount) as total_net'))
                    ->groupBy('company_name')
                    ->orderByDesc('total_net')
                    ->get();
                break;

            default: // date
                $summary = (clone $baseQuery)
                    ->select(DB::raw('DATE(sale_date) as sale_date'),
                        DB::raw('COUNT(*) as invoice_count'),
                        DB::raw('SUM(nt_amount) as total_nt'),
                        DB::raw('SUM(dis_amount) as total_dis'),
                        DB::raw('SUM(tax_amount) as total_tax'),
                        DB::raw('SUM(net_amount) as total_net'))
                    ->groupBy('sale_date')
                    ->orderBy('sale_date')
                    ->get();
        }

        $grandTotals = [
            'invoices' => $baseQuery->count(),
            'nt_amount' => (clone $baseQuery)->sum('nt_amount'),
            'dis_amount' => (clone $baseQuery)->sum('dis_amount'),
            'tax_amount' => (clone $baseQuery)->sum('tax_amount'),
            'net_amount' => (clone $baseQuery)->sum('net_amount')
        ];

        return view('admin.reports.sales.sales-summary', compact(
            'summary', 'grandTotals', 'dateFrom', 'dateTo', 'groupBy'
        ));
    }


    /**
     * Sales Bills Printing - For bulk printing
     */
    public function salesBillsPrinting(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $invoiceFrom = $request->get('invoice_from');
        $invoiceTo = $request->get('invoice_to');

        $query = SaleTransaction::with(['customer:id,name,code,address,gst_number', 'items', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($invoiceFrom) $query->where('invoice_no', '>=', $invoiceFrom);
        if ($invoiceTo) $query->where('invoice_no', '<=', $invoiceTo);

        $sales = $query->orderBy('invoice_no')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sales.sales-bills-printing', compact(
            'sales', 'customers', 'dateFrom', 'dateTo', 'customerId', 'invoiceFrom', 'invoiceTo'
        ));
    }

    /**
     * Sale Sheet - Detailed item-wise sale report
     */
    public function saleSheet(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $itemId = $request->get('item_id');
        $companyId = $request->get('company_id');

        $query = SaleTransactionItem::with([
            'saleTransaction:id,invoice_no,sale_date,customer_id',
            'saleTransaction.customer:id,name,code'
        ])->whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo, $customerId) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($customerId) $q->where('customer_id', $customerId);
        });

        if ($itemId) $query->where('item_id', $itemId);
        if ($companyId) {
            $query->whereHas('item', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $items = $query->orderBy('item_name')->get();

        $totals = [
            'qty' => $items->sum('qty'),
            'free_qty' => $items->sum('free_qty'),
            'amount' => $items->sum('amount'),
            'discount' => $items->sum('discount_amount'),
            'tax' => $items->sum('tax_amount'),
            'net_amount' => $items->sum('net_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $itemsList = Item::select('id', 'name', 'code')->orderBy('name')->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.sale-sheet', compact(
            'items', 'totals', 'customers', 'itemsList', 'companies',
            'dateFrom', 'dateTo', 'customerId', 'itemId', 'companyId'
        ));
    }

    /**
     * Dispatch Sheet - For dispatch/delivery tracking
     */
    public function dispatchSheet(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $areaId = $request->get('area_id');
        $routeId = $request->get('route_id');

        $query = SaleTransaction::with([
            'customer:id,name,code,address,area_name,route_name,mobile',
            'salesman:id,name',
            'items'
        ])->whereBetween('sale_date', [$dateFrom, $dateTo]);

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

        $sales = $query->orderBy('sale_date')->get();

        // Group by area/route for dispatch
        $groupedSales = $sales->groupBy(function($sale) {
            return $sale->customer->area_name ?? 'No Area';
        });

        $totals = [
            'invoices' => $sales->count(),
            'net_amount' => $sales->sum('net_amount'),
            'items_count' => $sales->sum(fn($s) => $s->items->count())
        ];

        $areas = Area::select('id', 'name')->orderBy('name')->get();
        $routes = \App\Models\Route::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.dispatch-sheet', compact(
            'groupedSales', 'totals', 'areas', 'routes',
            'dateFrom', 'dateTo', 'areaId', 'routeId'
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

        return view('admin.reports.sales.sale-return-book-item-wise', compact(
            'items', 'totals', 'itemsList', 'companies',
            'dateFrom', 'dateTo', 'itemId', 'companyId'
        ));
    }


    /**
     * Local / Central Sale Register
     */
    public function localCentralSaleRegister(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $saleType = $request->get('sale_type', 'all'); // local, central, all

        $query = SaleTransaction::with(['customer:id,name,code,local_central,gst_number,state_name', 'salesman:id,name'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($saleType === 'local') {
            $query->whereHas('customer', function($q) {
                $q->where('local_central', 'L');
            });
        } elseif ($saleType === 'central') {
            $query->whereHas('customer', function($q) {
                $q->where('local_central', 'C');
            });
        }

        $sales = $query->orderBy('sale_date')->orderBy('invoice_no')->get();

        // Group by local/central
        $localSales = $sales->filter(fn($s) => ($s->customer->local_central ?? 'L') === 'L');
        $centralSales = $sales->filter(fn($s) => ($s->customer->local_central ?? 'L') === 'C');

        $totals = [
            'local' => [
                'count' => $localSales->count(),
                'net_amount' => $localSales->sum('net_amount'),
                'tax_amount' => $localSales->sum('tax_amount')
            ],
            'central' => [
                'count' => $centralSales->count(),
                'net_amount' => $centralSales->sum('net_amount'),
                'tax_amount' => $centralSales->sum('tax_amount')
            ],
            'total' => [
                'count' => $sales->count(),
                'net_amount' => $sales->sum('net_amount'),
                'tax_amount' => $sales->sum('tax_amount')
            ]
        ];

        return view('admin.reports.sales.local-central-sale-register', compact(
            'sales', 'localSales', 'centralSales', 'totals',
            'dateFrom', 'dateTo', 'saleType'
        ));
    }

    /**
     * Sale Challan Reports Index
     */
    public function saleChallanReports(Request $request)
    {
        return view('admin.reports.sales.sale-challan-reports');
    }

    /**
     * Sale Challan Book
     */
    public function saleChallanBook(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $status = $request->get('status', 'all'); // all, pending, invoiced

        $query = SaleChallanTransaction::with(['customer:id,name,code', 'salesman:id,name'])
            ->whereBetween('challan_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($status === 'pending') $query->where('is_invoiced', false);
        if ($status === 'invoiced') $query->where('is_invoiced', true);

        $challans = $query->orderBy('challan_date')->orderBy('challan_no')->get();

        $totals = [
            'count' => $challans->count(),
            'pending' => $challans->where('is_invoiced', false)->count(),
            'invoiced' => $challans->where('is_invoiced', true)->count(),
            'net_amount' => $challans->sum('net_amount')
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sales.sale-challan-book', compact(
            'challans', 'totals', 'customers',
            'dateFrom', 'dateTo', 'customerId', 'status'
        ));
    }

    /**
     * Pending Challans Report
     */
    public function pendingChallans(Request $request)
    {
        $customerId = $request->get('customer_id');
        $daysOld = $request->get('days_old', 0);

        $query = SaleChallanTransaction::with(['customer:id,name,code,mobile', 'salesman:id,name', 'items'])
            ->where('is_invoiced', false);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($daysOld > 0) {
            $query->where('challan_date', '<=', Carbon::now()->subDays($daysOld));
        }

        $challans = $query->orderBy('challan_date')->get();

        $totals = [
            'count' => $challans->count(),
            'net_amount' => $challans->sum('net_amount'),
            'items_count' => $challans->sum(fn($c) => $c->items->sum('qty'))
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.reports.sales.pending-challans', compact(
            'challans', 'totals', 'customers', 'customerId', 'daysOld'
        ));
    }

    /**
     * Sales Stock Summary - Stock movement summary
     */
    public function salesStockSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        $query = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        });

        if ($companyId) {
            $query->whereHas('item', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $items = $query->select(
            'item_id', 'item_code', 'item_name', 'company_name', 'hsn_code',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(free_qty) as total_free'),
            DB::raw('AVG(sale_rate) as avg_rate'),
            DB::raw('SUM(net_amount) as total_amount'),
            DB::raw('COUNT(DISTINCT sale_transaction_id) as bill_count')
        )
        ->groupBy('item_id', 'item_code', 'item_name', 'company_name', 'hsn_code')
        ->orderByDesc('total_qty')
        ->get();

        $totals = [
            'qty' => $items->sum('total_qty'),
            'free' => $items->sum('total_free'),
            'amount' => $items->sum('total_amount'),
            'items' => $items->count()
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.sales-stock-summary', compact(
            'items', 'totals', 'companies', 'dateFrom', 'dateTo', 'companyId'
        ));
    }


    /**
     * Customer Visit Status - Track customer orders/visits
     */
    public function customerVisitStatus(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $salesmanId = $request->get('salesman_id');
        $areaId = $request->get('area_id');

        // Get all active customers
        $customersQuery = Customer::where('is_deleted', '!=', 1)
            ->select('id', 'name', 'code', 'area_name', 'route_name', 'sales_man_name', 'mobile');

        if ($salesmanId) {
            $customersQuery->where('sales_man_code', $salesmanId);
        }
        if ($areaId) {
            $customersQuery->where('area_code', $areaId);
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
                'visit_count' => $data->visit_count ?? 0,
                'total_amount' => $data->total_amount ?? 0,
                'last_visit' => $data->last_visit ?? null,
                'status' => ($data->visit_count ?? 0) > 0 ? 'Visited' : 'Not Visited'
            ];
        })->sortByDesc('visit_count');

        $totals = [
            'total_customers' => $customers->count(),
            'visited' => $report->where('visit_count', '>', 0)->count(),
            'not_visited' => $report->where('visit_count', 0)->count(),
            'total_amount' => $report->sum('total_amount')
        ];

        $salesmen = SalesMan::select('id', 'name')->orderBy('name')->get();
        $areas = Area::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.customer-visit-status', compact(
            'report', 'totals', 'salesmen', 'areas',
            'dateFrom', 'dateTo', 'salesmanId', 'areaId'
        ));
    }

    /**
     * Shortage Report - Items with low/no stock sold
     */
    public function shortageReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $companyId = $request->get('company_id');

        // Get items sold in period
        $soldItems = SaleTransactionItem::whereHas('saleTransaction', function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
        })
        ->select('item_id', 'item_code', 'item_name', 'company_name',
            DB::raw('SUM(qty) as sold_qty'))
        ->groupBy('item_id', 'item_code', 'item_name', 'company_name')
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
            $currentStock = $stockData->get($item->item_id)->current_stock ?? 0;
            return [
                'item_id' => $item->item_id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'company_name' => $item->company_name,
                'sold_qty' => $item->sold_qty,
                'current_stock' => $currentStock,
                'shortage' => $currentStock <= 0 ? 'Out of Stock' : ($currentStock < $item->sold_qty ? 'Low Stock' : 'OK')
            ];
        })->filter(fn($item) => $item['shortage'] !== 'OK')->sortBy('current_stock');

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.shortage-report', compact(
            'shortageItems', 'companies', 'dateFrom', 'dateTo', 'companyId'
        ));
    }

    /**
     * Sale Return List
     */
    public function saleReturnList(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $salesmanId = $request->get('salesman_id');

        $query = SaleReturnTransaction::with(['customer:id,name,code', 'salesman:id,name', 'items'])
            ->whereBetween('return_date', [$dateFrom, $dateTo]);

        if ($customerId) $query->where('customer_id', $customerId);
        if ($salesmanId) $query->where('salesman_id', $salesmanId);

        $returns = $query->orderBy('return_date')->orderBy('sr_no')->get();

        $totals = [
            'count' => $returns->count(),
            'nt_amount' => $returns->sum('nt_amount'),
            'dis_amount' => $returns->sum('dis_amount'),
            'tax_amount' => $returns->sum('tax_amount'),
            'net_amount' => $returns->sum('net_amount'),
            'items_count' => $returns->sum(fn($r) => $r->items->sum('qty'))
        ];

        $customers = Customer::where('is_deleted', '!=', 1)->select('id', 'name', 'code')->orderBy('name')->get();
        $salesmen = SalesMan::select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.sales.sale-return-list', compact(
            'returns', 'totals', 'customers', 'salesmen',
            'dateFrom', 'dateTo', 'customerId', 'salesmanId'
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

        return view('admin.reports.sales.pdf', compact('sales', 'dateFrom', 'dateTo', 'totalSales', 'totalTax', 'reportType'));
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
}
