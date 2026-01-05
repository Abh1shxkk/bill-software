<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerPrescription;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Batch;
use App\Models\SalesMan;
use App\Models\Area;
use App\Models\Route;
use App\Models\State;
use App\Models\Sale;
use App\Models\SaleTransaction;
use App\Models\CustomerLedger;
use App\Models\CustomerReceipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementReportController extends Controller
{
    // Due Reports
    public function dueList(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $query = CustomerLedger::with('customer');
            
            if ($request->party_type == 'C') {
                $query->whereHas('customer');
            }
            
            if ($request->customer_code) {
                $query->where('customer_id', $request->customer_code);
            }
            
            if ($request->from_date) {
                $query->where('transaction_date', '>=', $request->from_date);
            }
            
            if ($request->as_on_date) {
                $query->where('transaction_date', '<=', $request->as_on_date);
            }

            $reportData = $query->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list', compact(
            'customers', 'suppliers', 'salesmen', 'areas', 'routes', 'states', 'reportData'
        ));
    }

    public function billTagging(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $bills = collect();

        if ($request->has('get_bills') || $request->has('print') || $request->ajax()) {
            $query = SaleTransaction::with(['customer', 'items'])
                ->whereRaw('net_amount > COALESCE(paid_amount, 0)');
            
            if ($request->salesman_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('salesman_id', $request->salesman_id);
                });
            }

            $bills = $query->orderBy('sale_date', 'desc')->limit(100)->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.bill-tagging-print', compact('bills'));
            }
        }

        if ($request->ajax()) {
            return response()->json(['bills' => $bills]);
        }

        return view('admin.reports.management-report.due-reports.bill-tagging', compact('salesmen', 'areas', 'routes', 'bills'));
    }

    public function dueListWithPdc(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $query = SaleTransaction::with(['customer', 'salesman'])
                ->whereRaw('COALESCE(net_amount, 0) > COALESCE(paid_amount, 0)');

            if ($request->customer_code) {
                $query->where('customer_id', $request->customer_code);
            }

            if ($request->salesman_code) {
                $query->where('salesman_id', $request->salesman_code);
            }

            if ($request->area_code) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('area_id', $request->area_code);
                });
            }

            if ($request->route_code) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('route_id', $request->route_code);
                });
            }

            if ($request->as_on_date) {
                $query->where('sale_date', '<=', $request->as_on_date);
            }

            if ($request->series) {
                $query->where('series', $request->series);
            }

            $reportData = $query->orderBy('sale_date', 'desc')->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-with-pdc-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-with-pdc', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function dueListCompanyWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $query = SaleTransaction::with(['customer', 'salesman', 'items.item.company'])
                ->whereRaw('COALESCE(net_amount, 0) > COALESCE(paid_amount, 0)');

            if ($request->from_date) {
                $query->where('sale_date', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->where('sale_date', '<=', $request->to_date);
            }

            if ($request->company_code) {
                $query->whereHas('items.item', function($q) use ($request) {
                    $q->where('company_id', $request->company_code);
                });
            }

            if ($request->salesman_code) {
                $query->where('salesman_id', $request->salesman_code);
            }

            if ($request->area_code) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('area_code', $request->area_code);
                });
            }

            if ($request->party_code) {
                $query->where('customer_id', $request->party_code);
            }

            $reportData = $query->orderBy('sale_date', 'desc')->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-company-wise-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-company-wise', compact(
            'companies', 'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function dueListAccountLedger(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $ledgerData = collect();

        if ($request->has('view') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->when($request->customer_code && $request->customer_code != '00', fn($q) => $q->where('id', $request->customer_code))
                ->withSum('ledgers as balance', 'amount')
                ->having('balance', '!=', 0)
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-account-ledger-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-account-ledger', compact(
            'customers', 'suppliers', 'salesmen', 'areas', 'routes', 'reportData', 'ledgerData'
        ));
    }

    public function ageingAnalysis(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $asOnDate = $request->as_on_date ?? date('Y-m-d');
            
            // Try SaleTransaction first, if empty use CustomerLedger
            $reportData = SaleTransaction::with('customer')
                ->whereRaw('COALESCE(net_amount, 0) > COALESCE(paid_amount, 0)')
                ->where('sale_date', '<=', $asOnDate)
                ->when($request->party_code, fn($q) => $q->where('customer_id', $request->party_code))
                ->when($request->salesman_code, fn($q) => $q->where('salesman_id', $request->salesman_code))
                ->get()
                ->map(function($sale) use ($asOnDate) {
                    $days = \Carbon\Carbon::parse($sale->sale_date)->diffInDays($asOnDate);
                    $sale->days_overdue = $days;
                    $sale->due_amount = ($sale->net_amount ?? 0) - ($sale->paid_amount ?? 0);
                    return $sale;
                });

            // If no SaleTransaction data, use CustomerLedger
            if ($reportData->isEmpty()) {
                $reportData = CustomerLedger::with('customer')
                    ->where('amount', '>', 0)
                    ->where('transaction_date', '<=', $asOnDate)
                    ->when($request->party_code, fn($q) => $q->where('customer_id', $request->party_code))
                    ->orderBy('transaction_date', 'desc')
                    ->get()
                    ->map(function($ledger) use ($asOnDate) {
                        $days = \Carbon\Carbon::parse($ledger->transaction_date)->diffInDays($asOnDate);
                        $ledger->days_overdue = $days;
                        $ledger->due_amount = $ledger->amount;
                        $ledger->invoice_no = $ledger->trans_no;
                        $ledger->sale_date = $ledger->transaction_date;
                        return $ledger;
                    });
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.ageing-analysis-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.ageing-analysis', compact(
            'customers', 'suppliers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function ageingAnalysisAccountLedger(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->when($request->party_code && $request->party_code != '00', fn($q) => $q->where('id', $request->party_code))
                ->withSum('ledgers as balance', 'amount')
                ->having('balance', '>', 0)
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.ageing-analysis-account-ledger-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.ageing-analysis-account-ledger', compact(
            'customers', 'suppliers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function listOfPendingTags(Request $request)
    {
        $reportData = collect();
        return view('admin.reports.management-report.due-reports.list-of-pending-tags', compact('reportData'));
    }

    public function billHistory(Request $request)
    {
        $reportData = collect();

        if (($request->has('ok') || $request->has('print')) && $request->bill_no) {
            $reportData = SaleTransaction::with(['customer', 'items'])
                ->where('invoice_no', 'like', '%' . $request->bill_no . '%')
                ->when($request->series, fn($q) => $q->where('series', $request->series))
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.bill-history-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.bill-history', compact('reportData'));
    }

    public function dueListSummary(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->withSum('ledgers as balance', 'amount')
                ->when($request->salesman_code && $request->salesman_code != '00', fn($q) => $q->where('salesman_id', $request->salesman_code))
                ->when($request->area_code && $request->area_code != '00', fn($q) => $q->where('area_id', $request->area_code))
                ->when($request->route_code && $request->route_code != '00', fn($q) => $q->where('route_id', $request->route_code))
                ->having('balance', '>', 0)
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-summary-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-summary', compact(
            'salesmen', 'areas', 'routes', 'states', 'reportData'
        ));
    }

    public function dueListReminderLetter(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();

        if ($request->has('ok') || $request->has('print')) {
            $reportData = SaleTransaction::with('customer')
                ->whereRaw('net_amount > COALESCE(paid_amount, 0)')
                ->when($request->from_date, fn($q) => $q->where('sale_date', '>=', $request->from_date))
                ->when($request->as_on_date, fn($q) => $q->where('sale_date', '<=', $request->as_on_date))
                ->when($request->customer_code && $request->customer_code != '00', fn($q) => $q->where('customer_id', $request->customer_code))
                ->orderBy('sale_date', 'desc')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-reminder-letter-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-reminder-letter', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function balanceConfirmationLetter(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $ledgerData = collect();

        if ($request->has('ok') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->withSum('ledgers as balance', 'amount')
                ->when($request->customer_code && $request->customer_code != '00', fn($q) => $q->where('id', $request->customer_code))
                ->when($request->balance_type == 'D', fn($q) => $q->having('balance', '>', 0))
                ->when($request->balance_type == 'C', fn($q) => $q->having('balance', '<', 0))
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.balance-confirmation-letter-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.balance-confirmation-letter', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData', 'ledgerData'
        ));
    }

    public function balanceConfirmationLetterAccountLedger(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $ledgerData = collect();

        if ($request->has('ok') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->withSum('ledgers as balance', 'amount')
                ->when($request->customer_code && $request->customer_code != '00', fn($q) => $q->where('id', $request->customer_code))
                ->when($request->dr_balance_only, fn($q) => $q->having('balance', '>', 0))
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.balance-confirmation-letter-account-ledger-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.balance-confirmation-letter-account-ledger', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData', 'ledgerData'
        ));
    }

    public function dueListMonthly(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();

        if ($request->has('ok') || $request->has('print')) {
            $reportData = Customer::where('is_deleted', 0)
                ->withSum('ledgers as balance', 'amount')
                ->when($request->party_code && $request->party_code != '00', fn($q) => $q->where('id', $request->party_code))
                ->having('balance', '>', 0)
                ->orderBy('name')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-monthly-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-monthly', compact(
            'customers', 'companies', 'reportData'
        ));
    }

    public function dueListAdjustmentAnalysis(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();

        if ($request->has('view') || $request->has('excel') || $request->has('print')) {
            $reportData = CustomerReceipt::with(['customer', 'adjustments'])
                ->when($request->from_date, fn($q) => $q->where('date', '>=', $request->from_date))
                ->when($request->to_date, fn($q) => $q->where('date', '<=', $request->to_date))
                ->when($request->customer_code, fn($q) => $q->where('customer_id', $request->customer_code))
                ->orderBy('date', 'desc')
                ->get();

            if ($request->has('print')) {
                return view('admin.reports.management-report.due-reports.due-list-adjustment-analysis-print', compact('reportData'));
            }
        }

        return view('admin.reports.management-report.due-reports.due-list-adjustment-analysis', compact(
            'customers', 'suppliers', 'reportData'
        ));
    }

    // Gross Profit Reports
    public function grossProfitBillWise(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = SaleTransaction::with(['customer', 'salesman', 'items.batch'])
                ->whereBetween('sale_date', [$fromDate, $toDate]);

            // Series filter
            if ($request->series && $request->series != '00') {
                $query->where('series', $request->series);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('salesman_id', $request->salesman_id);
            }

            // Area filter (through customer)
            if ($request->area_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('area_id', $request->area_id);
                });
            }

            // Route filter (through customer)
            if ($request->route_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('route_id', $request->route_id);
                });
            }

            // Day filter
            if ($request->day) {
                $query->whereRaw("DAYNAME(sale_date) = ?", [$request->day]);
            }

            // Sort
            $sortBy = $request->sort_by ?? 'date';
            $order = $request->order ?? 'asc';
            
            switch ($sortBy) {
                case 'invoice_no':
                    $query->orderBy('invoice_no', $order);
                    break;
                case 'customer':
                    $query->join('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                          ->orderBy('customers.name', $order)
                          ->select('sale_transactions.*');
                    break;
                default:
                    $query->orderBy('sale_date', $order);
            }

            $transactions = $query->get();

            foreach ($transactions as $transaction) {
                $saleAmount = (float) $transaction->net_amount;
                $purchaseAmount = 0;

                // Calculate purchase cost from items
                foreach ($transaction->items as $item) {
                    $qty = (float) $item->qty + (float) $item->free_qty;
                    if ($item->batch) {
                        // Use cost or pur_rate from batch
                        $purchaseAmount += $qty * (float) ($item->batch->cost ?? $item->batch->pur_rate ?? 0);
                    }
                }

                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent) {
                    $filterGP = (float) $request->gp_percent;
                    if ($gpPercent < $filterGP) {
                        continue;
                    }
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'sale_date' => $transaction->sale_date,
                    'invoice_no' => $transaction->invoice_no,
                    'customer_name' => $transaction->customer->name ?? 'N/A',
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort by GP amount or percent if needed
            if ($sortBy == 'gp_amount') {
                usort($reportData, function($a, $b) use ($order) {
                    return $order == 'asc' ? $a['gp_amount'] <=> $b['gp_amount'] : $b['gp_amount'] <=> $a['gp_amount'];
                });
            } elseif ($sortBy == 'gp_percent') {
                usort($reportData, function($a, $b) use ($order) {
                    return $order == 'asc' ? $a['gp_percent'] <=> $b['gp_percent'] : $b['gp_percent'] <=> $a['gp_percent'];
                });
            }

            if ($request->has('print')) {
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.bill-wise-print', compact(
                    'reportData', 'request', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.bill-wise', compact(
            'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitItemBillWise(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = SaleTransaction::with(['customer', 'salesman', 'items.batch'])
                ->whereBetween('sale_date', [$fromDate, $toDate]);

            // Series filter
            if ($request->series && $request->series != '00') {
                $query->where('series', $request->series);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('salesman_id', $request->salesman_id);
            }

            // Area filter (through customer)
            if ($request->area_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('area_id', $request->area_id);
                });
            }

            // Route filter (through customer)
            if ($request->route_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('route_id', $request->route_id);
                });
            }

            // Day filter
            if ($request->day) {
                $query->whereRaw("DAYNAME(sale_date) = ?", [$request->day]);
            }

            $transactions = $query->orderBy('sale_date')->get();

            foreach ($transactions as $transaction) {
                foreach ($transaction->items as $item) {
                    $qty = (float) $item->qty + (float) $item->free_qty;
                    $saleRate = (float) $item->sale_rate;
                    $saleAmount = (float) $item->net_amount;
                    
                    // Get purchase rate from batch
                    $purRate = 0;
                    if ($item->batch) {
                        $purRate = (float) ($item->batch->cost ?? $item->batch->pur_rate ?? 0);
                    }
                    $purchaseAmount = $qty * $purRate;

                    $gpAmount = $saleAmount - $purchaseAmount;
                    $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                    // GP% filter
                    if ($request->gp_percent) {
                        $filterGP = (float) $request->gp_percent;
                        if ($gpPercent < $filterGP) {
                            continue;
                        }
                    }

                    // Negative filter
                    if ($request->negative == 'Y' && $gpAmount >= 0) {
                        continue;
                    }

                    $reportData[] = [
                        'sale_date' => $transaction->sale_date,
                        'invoice_no' => $transaction->invoice_no,
                        'item_name' => $item->item_name ?? 'N/A',
                        'qty' => $qty,
                        'sale_rate' => $saleRate,
                        'pur_rate' => $purRate,
                        'sale_amount' => $saleAmount,
                        'purchase_amount' => $purchaseAmount,
                        'gp_amount' => $gpAmount,
                        'gp_percent' => $gpPercent,
                    ];
                }
            }

            // Sort
            $sortBy = $request->sort_by ?? 'date';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'invoice_no' => 'invoice_no',
                    'item_name' => 'item_name',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'sale_date'
                };
                
                if ($order == 'asc') {
                    return $a[$field] <=> $b[$field];
                }
                return $b[$field] <=> $a[$field];
            });

            if ($request->has('print')) {
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.item-bill-wise-print', compact(
                    'reportData', 'request', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.item-bill-wise', compact(
            'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitSelectiveAllItems(Request $request)
    {
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $categories = \App\Models\ItemCategory::orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->leftJoin('items', 'sale_transaction_items.item_id', '=', 'items.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'sale_transaction_items.item_id',
                    'sale_transaction_items.item_name',
                    'sale_transaction_items.company_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('sale_transaction_items.item_id', 'sale_transaction_items.item_name', 'sale_transaction_items.company_name');

            // View type filter (selective items)
            if ($request->view_type == 'selective' && $request->selected_items) {
                $selectedIds = array_filter($request->selected_items);
                if (!empty($selectedIds)) {
                    $query->whereIn('sale_transaction_items.item_id', $selectedIds);
                }
            }

            // Company filter
            if ($request->company_id) {
                $query->where('items.company_id', $request->company_id);
            }

            // Category filter
            if ($request->category_id) {
                $query->where('items.category_id', $request->category_id);
            }

            // Customer filter
            if ($request->customer_id) {
                $query->where('sale_transactions.customer_id', $request->customer_id);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('sale_transactions.salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->where('customers.area_id', $request->area_id);
            }

            // Route filter
            if ($request->route_id) {
                $query->where('customers.route_id', $request->route_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP filter (P=Plus, M=Minus, B=Both)
                if ($request->gp_filter == 'P' && $gpAmount < 0) {
                    continue;
                }
                if ($request->gp_filter == 'M' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'item_id' => $row->item_id,
                    'item_name' => $row->item_name ?? 'N/A',
                    'company_name' => $row->company_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'name';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'code' => 'item_id',
                    'qty' => 'qty',
                    'sale_amount' => 'sale_amount',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'item_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $companyName = $request->company_id ? Company::find($request->company_id)?->name : null;
                $categoryName = $request->category_id ? \App\Models\ItemCategory::find($request->category_id)?->name : null;
                $customerName = $request->customer_id ? Customer::find($request->customer_id)?->name : null;
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.selective-all-items-print', compact(
                    'reportData', 'request', 'companyName', 'categoryName', 'customerName', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.selective-all-items', compact(
            'items', 'companies', 'categories', 'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitCompanyBillWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Get transactions with items grouped by company
            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'sale_transactions.id',
                    'sale_transactions.sale_date',
                    'sale_transactions.invoice_no',
                    'sale_transaction_items.company_name',
                    'customers.name as customer_name',
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('sale_transactions.id', 'sale_transactions.sale_date', 'sale_transactions.invoice_no', 'sale_transaction_items.company_name', 'customers.name');

            // Series filter
            if ($request->series && $request->series != '00') {
                $query->where('sale_transactions.series', $request->series);
            }

            // Company filter
            if ($request->company_id) {
                $query->where('sale_transaction_items.company_name', Company::find($request->company_id)?->name);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('sale_transactions.salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->where('customers.area_id', $request->area_id);
            }

            // Route filter
            if ($request->route_id) {
                $query->where('customers.route_id', $request->route_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent && $gpPercent < (float) $request->gp_percent) {
                    continue;
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'sale_date' => $row->sale_date,
                    'invoice_no' => $row->invoice_no,
                    'company_name' => $row->company_name ?? 'N/A',
                    'customer_name' => $row->customer_name ?? 'N/A',
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'date';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'invoice_no' => 'invoice_no',
                    'company' => 'company_name',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'sale_date'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $companyName = $request->company_id ? Company::find($request->company_id)?->name : null;
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.company-bill-wise-print', compact(
                    'reportData', 'request', 'companyName', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.company-bill-wise', compact(
            'companies', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitSelectiveAllCompanies(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Build query for company-wise gross profit
            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->leftJoin('items', 'sale_transaction_items.item_id', '=', 'items.id')
                ->leftJoin('companies', 'items.company_id', '=', 'companies.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'companies.id as company_id',
                    'companies.name as company_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('companies.id', 'companies.name');

            // View type filter (selective companies)
            if ($request->view_type == 'selective' && $request->selected_companies) {
                $selectedIds = array_filter($request->selected_companies);
                if (!empty($selectedIds)) {
                    $query->whereIn('companies.id', $selectedIds);
                }
            }

            // Customer filter
            if ($request->customer_id) {
                $query->where('sale_transactions.customer_id', $request->customer_id);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('sale_transactions.salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->where('customers.area_id', $request->area_id);
            }

            // Route filter
            if ($request->route_id) {
                $query->where('customers.route_id', $request->route_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'company_id' => $row->company_id,
                    'company_name' => $row->company_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'name';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'code' => 'company_id',
                    'sale_amount' => 'sale_amount',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'company_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                $customerName = $request->customer_id ? Customer::find($request->customer_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.selective-all-companies-print', compact(
                    'reportData', 'request', 'salesmanName', 'areaName', 'routeName', 'customerName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.selective-all-companies', compact(
            'companies', 'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitCustomerBillWise(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = SaleTransaction::with(['customer', 'items.batch'])
                ->whereBetween('sale_date', [$fromDate, $toDate]);

            // Customer filter
            if ($request->customer_id) {
                $query->where('customer_id', $request->customer_id);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('area_id', $request->area_id);
                });
            }

            // Route filter
            if ($request->route_id) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('route_id', $request->route_id);
                });
            }

            // Day filter
            if ($request->day) {
                $query->whereRaw("DAYNAME(sale_date) = ?", [$request->day]);
            }

            // Sort
            $sortBy = $request->sort_by ?? 'date';
            $order = $request->order ?? 'asc';
            
            switch ($sortBy) {
                case 'invoice_no':
                    $query->orderBy('invoice_no', $order);
                    break;
                case 'customer':
                    $query->join('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                          ->orderBy('customers.name', $order)
                          ->select('sale_transactions.*');
                    break;
                default:
                    $query->orderBy('sale_date', $order);
            }

            $transactions = $query->get();

            foreach ($transactions as $transaction) {
                $saleAmount = (float) $transaction->net_amount;
                $purchaseAmount = 0;

                foreach ($transaction->items as $item) {
                    $qty = (float) $item->qty + (float) $item->free_qty;
                    if ($item->batch) {
                        $purchaseAmount += $qty * (float) ($item->batch->cost ?? $item->batch->pur_rate ?? 0);
                    }
                }

                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent && $gpPercent < (float) $request->gp_percent) {
                    continue;
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'sale_date' => $transaction->sale_date,
                    'invoice_no' => $transaction->invoice_no,
                    'customer_name' => $transaction->customer->name ?? 'N/A',
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort by GP if needed
            if ($sortBy == 'gp_amount') {
                usort($reportData, function($a, $b) use ($order) {
                    return $order == 'asc' ? $a['gp_amount'] <=> $b['gp_amount'] : $b['gp_amount'] <=> $a['gp_amount'];
                });
            } elseif ($sortBy == 'gp_percent') {
                usort($reportData, function($a, $b) use ($order) {
                    return $order == 'asc' ? $a['gp_percent'] <=> $b['gp_percent'] : $b['gp_percent'] <=> $a['gp_percent'];
                });
            }

            if ($request->has('print')) {
                $customerName = $request->customer_id ? Customer::find($request->customer_id)?->name : null;
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.customer-bill-wise-print', compact(
                    'reportData', 'request', 'customerName', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.customer-bill-wise', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitSelectiveAllCustomers(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'customers.id as customer_id',
                    'customers.name as customer_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('customers.id', 'customers.name');

            // Customer filter
            if ($request->customer_id) {
                $query->where('sale_transactions.customer_id', $request->customer_id);
            }

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('sale_transactions.salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->where('customers.area_id', $request->area_id);
            }

            // Route filter
            if ($request->route_id) {
                $query->where('customers.route_id', $request->route_id);
            }

            // Day filter
            if ($request->day) {
                $query->whereRaw("DAYNAME(sale_transactions.sale_date) = ?", [$request->day]);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent && $gpPercent < (float) $request->gp_percent) {
                    continue;
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'customer_id' => $row->customer_id,
                    'customer_name' => $row->customer_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'date';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'name' => 'customer_name',
                    'sale_amount' => 'sale_amount',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'customer_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $customerName = $request->customer_id ? Customer::find($request->customer_id)?->name : null;
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.selective-all-customers-print', compact(
                    'reportData', 'request', 'customerName', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.selective-all-customers', compact(
            'customers', 'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    public function grossProfitSelectiveAllSuppliers(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Group by supplier through batch -> purchase_transaction -> supplier
            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->leftJoin('purchase_transactions', 'batches.purchase_transaction_id', '=', 'purchase_transactions.id')
                ->leftJoin('suppliers', 'purchase_transactions.supplier_id', '=', 'suppliers.id')
                ->leftJoin('items', 'sale_transaction_items.item_id', '=', 'items.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'suppliers.id as supplier_id',
                    'suppliers.name as supplier_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('suppliers.id', 'suppliers.name');

            // Supplier filter
            if ($request->supplier_id) {
                $query->where('suppliers.id', $request->supplier_id);
            }

            // Company filter
            if ($request->company_id) {
                $query->where('items.company_id', $request->company_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent && $gpPercent < (float) $request->gp_percent) {
                    continue;
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'supplier_id' => $row->supplier_id,
                    'supplier_name' => $row->supplier_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'name';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'sale_amount' => 'sale_amount',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'supplier_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $supplierName = $request->supplier_id ? Supplier::find($request->supplier_id)?->name : null;
                $companyName = $request->company_id ? Company::find($request->company_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.selective-all-suppliers-print', compact(
                    'reportData', 'request', 'supplierName', 'companyName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.selective-all-suppliers', compact(
            'suppliers', 'companies', 'reportData'
        ));
    }

    public function grossProfitSaltWise(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Collect salt names from inputs
            $saltNames = [];
            for ($i = 1; $i <= 4; $i++) {
                $saltName = $request->input("salt_name_$i");
                if ($saltName && trim($saltName) !== '') {
                    $saltNames[] = trim($saltName);
                }
            }

            // Build query - group by item's commodity/salt field
            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->leftJoin('items', 'sale_transaction_items.item_id', '=', 'items.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->whereNotNull('items.commodity')
                ->where('items.commodity', '!=', '')
                ->select(
                    'items.commodity as salt_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('items.commodity');

            // Filter by specific salt names if provided
            if (!empty($saltNames)) {
                $query->where(function($q) use ($saltNames) {
                    foreach ($saltNames as $salt) {
                        $q->orWhere('items.commodity', 'LIKE', "%$salt%");
                    }
                });
            }

            $results = $query->orderBy('items.commodity')->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'salt_name' => $row->salt_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.gross-profit-reports.salt-wise-print', compact(
                    'reportData', 'request'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.salt-wise', compact('reportData'));
    }

    public function claimItemsSoldOnLoss(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $minLossPercent = (float) ($request->min_loss_percent ?? 0);

            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->leftJoin('items', 'sale_transaction_items.item_id', '=', 'items.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'sale_transaction_items.item_id',
                    'sale_transaction_items.item_name',
                    'sale_transaction_items.company_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_cost')
                )
                ->groupBy('sale_transaction_items.item_id', 'sale_transaction_items.item_name', 'sale_transaction_items.company_name')
                ->havingRaw('total_sale < total_cost');

            // Company filter
            if ($request->company_id) {
                $query->where('items.company_id', $request->company_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $costAmount = (float) $row->total_cost;
                $lossAmount = $saleAmount - $costAmount; // Will be negative
                $lossPercent = $costAmount > 0 ? (abs($lossAmount) / $costAmount * 100) : 0;

                // Min loss % filter
                if ($lossPercent < $minLossPercent) {
                    continue;
                }

                $reportData[] = [
                    'item_id' => $row->item_id,
                    'item_name' => $row->item_name ?? 'N/A',
                    'company_name' => $row->company_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'cost_amount' => $costAmount,
                    'loss_amount' => $lossAmount,
                    'loss_percent' => $lossPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'item_name';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'company' => 'company_name',
                    'loss_amount' => 'loss_amount',
                    'loss_percent' => 'loss_percent',
                    default => 'item_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $companyName = $request->company_id ? Company::find($request->company_id)?->name : null;
                $supplierName = $request->supplier_id ? Supplier::find($request->supplier_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.claim-items-sold-on-loss-print', compact(
                    'reportData', 'request', 'companyName', 'supplierName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.claim-items-sold-on-loss', compact(
            'companies', 'suppliers', 'reportData'
        ));
    }

    public function grossProfitSelectiveAllSalesman(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->leftJoin('sales_men', 'sale_transactions.salesman_id', '=', 'sales_men.id')
                ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                ->leftJoin('batches', 'sale_transaction_items.batch_id', '=', 'batches.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'sales_men.id as salesman_id',
                    'sales_men.name as salesman_name',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_qty'),
                    DB::raw('SUM(sale_transaction_items.net_amount) as total_sale'),
                    DB::raw('SUM((sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) * COALESCE(batches.cost, batches.pur_rate, 0)) as total_purchase')
                )
                ->groupBy('sales_men.id', 'sales_men.name');

            // Salesman filter
            if ($request->salesman_id) {
                $query->where('sale_transactions.salesman_id', $request->salesman_id);
            }

            // Area filter
            if ($request->area_id) {
                $query->where('customers.area_id', $request->area_id);
            }

            // Route filter
            if ($request->route_id) {
                $query->where('customers.route_id', $request->route_id);
            }

            $results = $query->get();

            foreach ($results as $row) {
                $saleAmount = (float) $row->total_sale;
                $purchaseAmount = (float) $row->total_purchase;
                $gpAmount = $saleAmount - $purchaseAmount;
                $gpPercent = $saleAmount > 0 ? ($gpAmount / $saleAmount * 100) : 0;

                // GP% filter
                if ($request->gp_percent && $gpPercent < (float) $request->gp_percent) {
                    continue;
                }

                // Negative filter
                if ($request->negative == 'Y' && $gpAmount >= 0) {
                    continue;
                }

                $reportData[] = [
                    'salesman_id' => $row->salesman_id,
                    'salesman_name' => $row->salesman_name ?? 'N/A',
                    'qty' => (float) $row->total_qty,
                    'sale_amount' => $saleAmount,
                    'purchase_amount' => $purchaseAmount,
                    'gp_amount' => $gpAmount,
                    'gp_percent' => $gpPercent,
                ];
            }

            // Sort
            $sortBy = $request->sort_by ?? 'name';
            $order = $request->order ?? 'asc';
            
            usort($reportData, function($a, $b) use ($sortBy, $order) {
                $field = match($sortBy) {
                    'sale_amount' => 'sale_amount',
                    'gp_amount' => 'gp_amount',
                    'gp_percent' => 'gp_percent',
                    default => 'salesman_name'
                };
                $result = $a[$field] <=> $b[$field];
                return $order == 'asc' ? $result : -$result;
            });

            if ($request->has('print')) {
                $salesmanName = $request->salesman_id ? SalesMan::find($request->salesman_id)?->name : null;
                $areaName = $request->area_id ? Area::find($request->area_id)?->name : null;
                $routeName = $request->route_id ? Route::find($request->route_id)?->name : null;
                
                return view('admin.reports.management-report.gross-profit-reports.selective-all-salesman-print', compact(
                    'reportData', 'request', 'salesmanName', 'areaName', 'routeName'
                ));
            }
        }

        return view('admin.reports.management-report.gross-profit-reports.selective-all-salesman', compact(
            'salesmen', 'areas', 'routes', 'reportData'
        ));
    }

    // Direct Reports
    public function listOfExpiredItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            // Parse expiry dates (MM/YY format)
            $fromExpiry = $request->from_expiry ?? '01/90';
            $toExpiry = $request->to_expiry ?? date('m/y');
            
            // Convert MM/YY to date for comparison
            $fromParts = explode('/', $fromExpiry);
            $toParts = explode('/', $toExpiry);
            
            $fromYear = (int)$fromParts[1] < 50 ? 2000 + (int)$fromParts[1] : 1900 + (int)$fromParts[1];
            $toYear = (int)$toParts[1] < 50 ? 2000 + (int)$toParts[1] : 1900 + (int)$toParts[1];
            
            $fromDate = sprintf('%04d-%02d-01', $fromYear, (int)$fromParts[0]);
            $toDate = sprintf('%04d-%02d-31', $toYear, (int)$toParts[0]);

            $query = Batch::with(['item.company'])
                ->where('qty', '>', 0)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', $fromDate)
                ->where('expiry_date', '<=', $toDate);

            // Company filter
            if ($request->company_id) {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }

            // Supplier filter
            if ($request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }

            // Location filter
            if ($request->location) {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('location', 'LIKE', '%' . $request->location . '%');
                });
            }

            // Division filter
            if ($request->division) {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('division', 'LIKE', '%' . $request->division . '%');
                });
            }

            // Hide inactive items
            if ($request->hide_inactive == 'Y') {
                $query->whereHas('item', function($q) {
                    $q->where('status', '!=', 'inactive');
                });
            }

            $batches = $query->orderBy('expiry_date')->get();

            $valueOn = strtoupper($request->value_on ?? 'S');

            foreach ($batches as $batch) {
                // Determine rate based on value_on parameter
                $rate = match($valueOn) {
                    'P' => (float) ($batch->pur_rate ?? 0),
                    'C' => (float) ($batch->cost ?? 0),
                    'M' => (float) ($batch->mrp ?? 0),
                    default => (float) ($batch->sale_rate ?? $batch->mrp ?? 0)
                };

                $qty = (float) $batch->qty;
                $value = $qty * $rate;

                $reportData[] = [
                    'item_name' => $batch->item->name ?? 'N/A',
                    'company_name' => $batch->item->company->name ?? 'N/A',
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '',
                    'qty' => $qty,
                    'rate' => $rate,
                    'value' => $value,
                ];
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.list-of-expired-items-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.list-of-expired-items', compact('companies', 'suppliers', 'reportData'));
    }

    public function salePurchaseSchemes(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $schemeType = strtoupper($request->scheme_type ?? 'S');

            $query = Item::with('company')
                ->where('is_deleted', 0)
                ->where('current_scheme_flag', 'Y');

            if ($schemeType == 'S') {
                // Sale schemes - items with sale scheme
                $query->where(function($q) {
                    $q->whereNotNull('scheme_plus')
                      ->where('scheme_plus', '!=', '')
                      ->where('scheme_plus', '!=', '0')
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('scheme_minus')
                             ->where('scheme_minus', '!=', '')
                             ->where('scheme_minus', '!=', '0');
                      });
                });
            } else {
                // Purchase schemes
                $query->where(function($q) {
                    $q->whereNotNull('pur_scheme_plus')
                      ->where('pur_scheme_plus', '!=', '')
                      ->where('pur_scheme_plus', '!=', '0')
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('pur_scheme_minus')
                             ->where('pur_scheme_minus', '!=', '')
                             ->where('pur_scheme_minus', '!=', '0');
                      });
                });
            }

            $items = $query->orderBy('name')->get();

            foreach ($items as $item) {
                if ($schemeType == 'S') {
                    $schemePlus = $item->scheme_plus;
                    $schemeMinus = $item->scheme_minus;
                } else {
                    $schemePlus = $item->pur_scheme_plus;
                    $schemeMinus = $item->pur_scheme_minus;
                }

                $reportData[] = [
                    'item_name' => $item->name,
                    'company_name' => $item->company->name ?? 'N/A',
                    'packing' => $item->packing ?? '',
                    'scheme_plus' => $schemePlus ?? '',
                    'scheme_minus' => $schemeMinus ?? '',
                    'from_date' => $item->from_date ? \Carbon\Carbon::parse($item->from_date)->format('d-M-y') : '',
                    'to_date' => $item->to_date ? \Carbon\Carbon::parse($item->to_date)->format('d-M-y') : '',
                ];
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.sale-purchase-schemes-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.sale-purchase-schemes', compact('reportData'));
    }

    public function suppliersPendingOrder(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            $query = \App\Models\PendingOrder::with(['supplier', 'item.company'])
                ->whereBetween('order_date', [$fromDate, $toDate])
                ->where('balance_qty', '>', 0);

            // Supplier filter
            if ($request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }

            // Company filter
            if ($request->company_id) {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }

            // Item filter
            if ($request->item_id) {
                $query->where('item_id', $request->item_id);
            }

            // Division filter
            if ($request->division && $request->division != '00') {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('division', $request->division);
                });
            }

            $orders = $query->orderBy('order_date', 'desc')->get();

            foreach ($orders as $order) {
                $orderQty = (float) ($order->order_qty ?? 0);
                $balanceQty = (float) ($order->balance_qty ?? 0);
                $receivedQty = $orderQty - $balanceQty;
                $rate = (float) ($order->item->pur_rate ?? 0);

                if ($balanceQty <= 0) continue;

                $reportData[] = [
                    'order_date' => $order->order_date ? $order->order_date->format('d-M-y') : '',
                    'order_no' => $order->order_no ?? '',
                    'supplier_name' => $order->supplier->name ?? 'N/A',
                    'item_name' => $order->item->name ?? 'N/A',
                    'order_qty' => $orderQty,
                    'received_qty' => $receivedQty,
                    'pending_qty' => $balanceQty,
                    'rate' => $rate,
                    'amount' => $balanceQty * $rate,
                ];
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.suppliers-pending-order-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.suppliers-pending-order', compact('suppliers', 'companies', 'items', 'reportData'));
    }

    public function customersPendingOrder(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Query pending sale challans (orders not yet invoiced)
            $query = \App\Models\SaleChallanTransaction::with(['customer', 'items.item.company'])
                ->whereBetween('challan_date', [$fromDate, $toDate])
                ->where('is_invoiced', 0)
                ->where(function($q) {
                    $q->where('status', '!=', 'cancelled')
                      ->orWhereNull('status');
                });

            // Customer filter
            if ($request->customer_id) {
                $query->where('customer_id', $request->customer_id);
            }

            // Get challans and process items
            $challans = $query->orderBy('challan_date', 'desc')->get();

            foreach ($challans as $challan) {
                foreach ($challan->items as $challanItem) {
                    $item = $challanItem->item;
                    
                    // Company filter
                    if ($request->company_id && $item && $item->company_id != $request->company_id) {
                        continue;
                    }

                    // Item filter
                    if ($request->item_id && $challanItem->item_id != $request->item_id) {
                        continue;
                    }

                    // Division filter
                    if ($request->division && $request->division != '00') {
                        if (!$item || $item->division != $request->division) {
                            continue;
                        }
                    }

                    $orderQty = (float) ($challanItem->qty ?? 0);
                    $freeQty = (float) ($challanItem->free_qty ?? 0);
                    $totalOrderQty = $orderQty + $freeQty;
                    
                    // For pending orders, delivered qty is 0 since challan is not invoiced
                    $deliveredQty = 0;
                    $pendingQty = $totalOrderQty;
                    
                    $rate = (float) ($challanItem->sale_rate ?? $challanItem->mrp ?? 0);
                    $amount = $pendingQty * $rate;

                    if ($pendingQty <= 0) continue;

                    $reportData[] = [
                        'order_date' => $challan->challan_date ? $challan->challan_date->format('d-M-y') : '',
                        'order_no' => $challan->challan_no ?? '',
                        'customer_name' => $challan->customer->name ?? 'N/A',
                        'item_name' => $item->name ?? ($challanItem->item_name ?? 'N/A'),
                        'order_qty' => $totalOrderQty,
                        'delivered_qty' => $deliveredQty,
                        'pending_qty' => $pendingQty,
                        'rate' => $rate,
                        'amount' => $amount,
                    ];
                }
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.customers-pending-order-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.customers-pending-order', compact('customers', 'companies', 'items', 'reportData'));
    }

    public function nonMovingItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $withStock = $request->with_stock == 1;
            $withBatchDetail = $request->with_batch_detail == 1;

            // Get items that have NOT been sold within the date range
            // First, get all item IDs that WERE sold in the date range
            $soldItemIds = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->distinct()
                ->pluck('sale_transaction_items.item_id')
                ->toArray();

            // Query items that are NOT in the sold list
            $query = Item::with(['company', 'batches'])
                ->where('is_deleted', 0)
                ->whereNotIn('id', $soldItemIds);

            // Company filter
            if ($request->company_id) {
                $query->where('company_id', $request->company_id);
            }

            // If with stock is checked, only show items that have stock in batches
            if ($withStock) {
                $query->whereHas('batches', function($q) {
                    $q->where('qty', '>', 0);
                });
            }

            $items = $query->orderBy('name')->get();

            foreach ($items as $item) {
                // Get last sale date for this item
                $lastSale = DB::table('sale_transaction_items')
                    ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                    ->where('sale_transaction_items.item_id', $item->id)
                    ->orderBy('sale_transactions.sale_date', 'desc')
                    ->select('sale_transactions.sale_date')
                    ->first();

                $lastSaleDate = $lastSale ? \Carbon\Carbon::parse($lastSale->sale_date) : null;
                $daysSinceSale = $lastSaleDate ? $lastSaleDate->diffInDays(now()) : 999;

                // Supplier filter - check if item was purchased from this supplier
                if ($request->supplier_id) {
                    $hasSupplierPurchase = DB::table('batches')
                        ->where('item_id', $item->id)
                        ->where('supplier_id', $request->supplier_id)
                        ->exists();
                    
                    if (!$hasSupplierPurchase) {
                        continue;
                    }
                }

                // Calculate stock from batches
                $currentStock = (float) $item->batches->sum('qty');
                $avgPurRate = $item->batches->count() > 0 ? $item->batches->avg('pur_rate') : ($item->pur_rate ?? 0);
                $stockValue = $currentStock * (float) $avgPurRate;

                if ($withBatchDetail) {
                    // Show each batch separately
                    $batches = Batch::where('item_id', $item->id)
                        ->where('qty', '>', 0)
                        ->get();

                    if ($batches->count() > 0) {
                        foreach ($batches as $batch) {
                            $reportData[] = [
                                'item_id' => $item->id,
                                'item_name' => $item->name,
                                'company_name' => $item->company->name ?? 'N/A',
                                'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                                'days_since_sale' => $daysSinceSale,
                                'current_stock' => (float) $batch->qty,
                                'stock_value' => (float) $batch->qty * (float) ($batch->pur_rate ?? $batch->cost ?? 0),
                                'batch_no' => $batch->batch_no ?? '',
                                'expiry_date' => $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '',
                            ];
                        }
                    } else {
                        $reportData[] = [
                            'item_id' => $item->id,
                            'item_name' => $item->name,
                            'company_name' => $item->company->name ?? 'N/A',
                            'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                            'days_since_sale' => $daysSinceSale,
                            'current_stock' => $currentStock,
                            'stock_value' => $stockValue,
                            'batch_no' => '',
                            'expiry_date' => '',
                        ];
                    }
                } else {
                    $reportData[] = [
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'company_name' => $item->company->name ?? 'N/A',
                        'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                        'days_since_sale' => $daysSinceSale,
                        'current_stock' => $currentStock,
                        'stock_value' => $stockValue,
                        'batch_no' => '',
                        'expiry_date' => '',
                    ];
                }
            }

            // Sort by days since sale (descending - longest non-moving first)
            usort($reportData, function($a, $b) {
                return $b['days_since_sale'] <=> $a['days_since_sale'];
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.non-moving-items-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.non-moving-items', compact('companies', 'suppliers', 'reportData'));
    }

    public function slowMovingItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $ratioBelow = (float) ($request->ratio_below ?? 100);
            $withBatchDetail = $request->with_batch_detail == 1;

            // Get sale quantities for all items within date range
            $salesData = DB::table('sale_transaction_items')
                ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                ->select(
                    'sale_transaction_items.item_id',
                    DB::raw('SUM(sale_transaction_items.qty + COALESCE(sale_transaction_items.free_qty, 0)) as total_sale_qty')
                )
                ->groupBy('sale_transaction_items.item_id')
                ->pluck('total_sale_qty', 'item_id')
                ->toArray();

            // Get items with stock (from batches)
            $items = Item::with(['company', 'batches'])
                ->where('is_deleted', 0)
                ->whereHas('batches', function($q) {
                    $q->where('qty', '>', 0);
                })
                ->orderBy('name')
                ->get();

            foreach ($items as $item) {
                // Calculate stock from batches
                $stockQty = (float) $item->batches->sum('qty');
                $saleQty = (float) ($salesData[$item->id] ?? 0);
                
                // Calculate sale to stock ratio
                $ratio = $stockQty > 0 ? ($saleQty / $stockQty) * 100 : 0;

                // Filter by ratio threshold
                if ($ratio >= $ratioBelow && $ratioBelow > 0) {
                    continue; // Skip items with ratio above threshold
                }

                // Get last sale date
                $lastSale = DB::table('sale_transaction_items')
                    ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                    ->where('sale_transaction_items.item_id', $item->id)
                    ->orderBy('sale_transactions.sale_date', 'desc')
                    ->select('sale_transactions.sale_date')
                    ->first();

                $lastSaleDate = $lastSale ? \Carbon\Carbon::parse($lastSale->sale_date) : null;

                $avgPurRate = $item->batches->count() > 0 ? $item->batches->avg('pur_rate') : ($item->pur_rate ?? 0);
                $stockValue = $stockQty * (float) $avgPurRate;

                if ($withBatchDetail) {
                    // Show each batch separately
                    $batches = Batch::where('item_id', $item->id)
                        ->where('qty', '>', 0)
                        ->get();

                    if ($batches->count() > 0) {
                        foreach ($batches as $batch) {
                            $batchStockQty = (float) $batch->qty;
                            $batchStockValue = $batchStockQty * (float) ($batch->pur_rate ?? $batch->cost ?? 0);

                            $reportData[] = [
                                'item_id' => $item->id,
                                'item_name' => $item->name,
                                'company_name' => $item->company->name ?? 'N/A',
                                'stock_qty' => $batchStockQty,
                                'sale_qty' => $saleQty,
                                'ratio' => $ratio,
                                'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                                'stock_value' => $batchStockValue,
                                'batch_no' => $batch->batch_no ?? '',
                                'expiry_date' => $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '',
                            ];
                        }
                    } else {
                        $reportData[] = [
                            'item_id' => $item->id,
                            'item_name' => $item->name,
                            'company_name' => $item->company->name ?? 'N/A',
                            'stock_qty' => $stockQty,
                            'sale_qty' => $saleQty,
                            'ratio' => $ratio,
                            'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                            'stock_value' => $stockValue,
                            'batch_no' => '',
                            'expiry_date' => '',
                        ];
                    }
                } else {
                    $reportData[] = [
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'company_name' => $item->company->name ?? 'N/A',
                        'stock_qty' => $stockQty,
                        'sale_qty' => $saleQty,
                        'ratio' => $ratio,
                        'last_sale_date' => $lastSaleDate ? $lastSaleDate->format('d-M-y') : 'Never',
                        'stock_value' => $stockValue,
                        'batch_no' => '',
                        'expiry_date' => '',
                    ];
                }
            }

            // Sort by ratio (lowest first - slowest moving)
            usort($reportData, function($a, $b) {
                return $a['ratio'] <=> $b['ratio'];
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.slow-moving-items-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.slow-moving-items', compact('companies', 'reportData'));
    }

    public function performanceReport(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $reportType = strtoupper($request->report_type ?? 'S');

            if ($reportType == 'S') {
                // Salesman-wise performance
                $query = DB::table('sale_transactions')
                    ->leftJoin('sales_men', 'sale_transactions.salesman_id', '=', 'sales_men.id')
                    ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                    ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                    ->select(
                        'sales_men.id as entity_id',
                        'sales_men.name as entity_name',
                        DB::raw('COUNT(DISTINCT sale_transactions.id) as bills'),
                        DB::raw('SUM(sale_transactions.net_amount) as sale_amount'),
                        DB::raw('SUM(sale_transactions.paid_amount) as collection')
                    )
                    ->groupBy('sales_men.id', 'sales_men.name');

                // Salesman filter
                if ($request->salesman_id) {
                    $query->where('sale_transactions.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id) {
                    $query->where('customers.area_code', $request->area_id);
                }

                // Route filter
                if ($request->route_id) {
                    $query->where('customers.route_code', $request->route_id);
                }

                // Customer filter
                if ($request->customer_id) {
                    $query->where('sale_transactions.customer_id', $request->customer_id);
                }

            } else {
                // Customer-wise performance
                $query = DB::table('sale_transactions')
                    ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                    ->leftJoin('sales_men', 'sale_transactions.salesman_id', '=', 'sales_men.id')
                    ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                    ->select(
                        'customers.id as entity_id',
                        'customers.name as entity_name',
                        DB::raw('COUNT(DISTINCT sale_transactions.id) as bills'),
                        DB::raw('SUM(sale_transactions.net_amount) as sale_amount'),
                        DB::raw('SUM(sale_transactions.paid_amount) as collection')
                    )
                    ->groupBy('customers.id', 'customers.name');

                // Salesman filter
                if ($request->salesman_id) {
                    $query->where('sale_transactions.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id) {
                    $query->where('customers.area_code', $request->area_id);
                }

                // Route filter
                if ($request->route_id) {
                    $query->where('customers.route_code', $request->route_id);
                }

                // Customer filter
                if ($request->customer_id) {
                    $query->where('customers.id', $request->customer_id);
                }

                // Flag filter
                if ($request->flag) {
                    $query->where('customers.status', $request->flag);
                }
            }

            $results = $query->get();

            foreach ($results as $row) {
                $entityId = $row->entity_id;
                $saleAmount = (float) ($row->sale_amount ?? 0);
                $collection = (float) ($row->collection ?? 0);
                $bills = (int) ($row->bills ?? 0);

                // Get return amount for this entity
                if ($reportType == 'S') {
                    $returnAmount = DB::table('sale_return_transactions')
                        ->join('sale_transactions', 'sale_return_transactions.sale_transaction_id', '=', 'sale_transactions.id')
                        ->whereBetween('sale_return_transactions.sr_date', [$fromDate, $toDate])
                        ->where('sale_transactions.salesman_id', $entityId)
                        ->sum('sale_return_transactions.net_amount');
                } else {
                    $returnAmount = DB::table('sale_return_transactions')
                        ->whereBetween('sr_date', [$fromDate, $toDate])
                        ->where('customer_id', $entityId)
                        ->sum('net_amount');
                }

                $returnAmount = (float) $returnAmount;
                $netAmount = $saleAmount - $returnAmount;
                $avgBillValue = $bills > 0 ? $netAmount / $bills : 0;

                // Get outstanding
                if ($reportType == 'C') {
                    $outstanding = DB::table('customer_ledgers')
                        ->where('customer_id', $entityId)
                        ->sum(DB::raw('debit - credit'));
                } else {
                    // For salesman, sum outstanding of all customers under this salesman
                    $outstanding = DB::table('customer_ledgers')
                        ->join('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                        ->where('customers.sales_man_code', $entityId)
                        ->sum(DB::raw('customer_ledgers.debit - customer_ledgers.credit'));
                }
                $outstanding = (float) $outstanding;

                $reportData[] = [
                    'id' => $entityId,
                    'name' => $row->entity_name ?? 'N/A',
                    'sale_amount' => $saleAmount,
                    'return_amount' => $returnAmount,
                    'net_amount' => $netAmount,
                    'bills' => $bills,
                    'avg_bill_value' => $avgBillValue,
                    'collection' => $collection,
                    'outstanding' => $outstanding,
                ];
            }

            // Sort by net amount descending
            usort($reportData, function($a, $b) {
                return $b['net_amount'] <=> $a['net_amount'];
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.performance-report-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.performance-report', compact('salesmen', 'areas', 'routes', 'customers', 'reportData'));
    }

    // Others
    public function dayCheckList(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Sales
            $sales = DB::table('sale_transactions')
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => 'Sales',
                'value' => (float) ($sales->total ?? 0),
                'count' => (int) ($sales->count ?? 0),
                'is_header' => true,
            ];

            // Cash Sales
            $cashSales = DB::table('sale_transactions')
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->where('cash_flag', 'Y')
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => '   Cash Sales',
                'value' => (float) ($cashSales->total ?? 0),
                'count' => (int) ($cashSales->count ?? 0),
                'is_header' => false,
            ];

            // Credit Sales
            $creditSales = DB::table('sale_transactions')
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->where(function($q) {
                    $q->where('cash_flag', '!=', 'Y')
                      ->orWhereNull('cash_flag');
                })
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => '   Credit Sales',
                'value' => (float) ($creditSales->total ?? 0),
                'count' => (int) ($creditSales->count ?? 0),
                'is_header' => false,
            ];

            // Sale Returns
            $saleReturns = DB::table('sale_return_transactions')
                ->whereBetween('return_date', [$fromDate, $toDate])
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => 'Sale Returns',
                'value' => (float) ($saleReturns->total ?? 0),
                'count' => (int) ($saleReturns->count ?? 0),
                'is_header' => true,
            ];

            // Purchases
            $purchases = DB::table('purchase_transactions')
                ->whereBetween('bill_date', [$fromDate, $toDate])
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => 'Purchases',
                'value' => (float) ($purchases->total ?? 0),
                'count' => (int) ($purchases->count ?? 0),
                'is_header' => true,
            ];

            // Cash Purchases
            $cashPurchases = DB::table('purchase_transactions')
                ->whereBetween('bill_date', [$fromDate, $toDate])
                ->where('cash_flag', 'Y')
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => '   Cash Purchases',
                'value' => (float) ($cashPurchases->total ?? 0),
                'count' => (int) ($cashPurchases->count ?? 0),
                'is_header' => false,
            ];

            // Credit Purchases
            $creditPurchases = DB::table('purchase_transactions')
                ->whereBetween('bill_date', [$fromDate, $toDate])
                ->where(function($q) {
                    $q->where('cash_flag', '!=', 'Y')
                      ->orWhereNull('cash_flag');
                })
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => '   Credit Purchases',
                'value' => (float) ($creditPurchases->total ?? 0),
                'count' => (int) ($creditPurchases->count ?? 0),
                'is_header' => false,
            ];

            // Purchase Returns
            $purchaseReturns = DB::table('purchase_return_transactions')
                ->whereBetween('return_date', [$fromDate, $toDate])
                ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                ->first();

            $reportData[] = [
                'transaction' => 'Purchase Returns',
                'value' => (float) ($purchaseReturns->total ?? 0),
                'count' => (int) ($purchaseReturns->count ?? 0),
                'is_header' => true,
            ];

            // Customer Receipts
            try {
                $customerReceipts = DB::table('customer_ledgers')
                    ->whereBetween('transaction_date', [$fromDate, $toDate])
                    ->where('transaction_type', 'LIKE', '%receipt%')
                    ->selectRaw('SUM(ABS(amount)) as total, COUNT(*) as count')
                    ->first();

                $reportData[] = [
                    'transaction' => 'Customer Receipts',
                    'value' => (float) ($customerReceipts->total ?? 0),
                    'count' => (int) ($customerReceipts->count ?? 0),
                    'is_header' => true,
                ];
            } catch (\Exception $e) {
                $reportData[] = [
                    'transaction' => 'Customer Receipts',
                    'value' => 0,
                    'count' => 0,
                    'is_header' => true,
                ];
            }

            // Supplier Payments
            try {
                $supplierPayments = DB::table('supplier_ledgers')
                    ->whereBetween('transaction_date', [$fromDate, $toDate])
                    ->where('transaction_type', 'LIKE', '%payment%')
                    ->selectRaw('SUM(ABS(amount)) as total, COUNT(*) as count')
                    ->first();

                $reportData[] = [
                    'transaction' => 'Supplier Payments',
                    'value' => (float) ($supplierPayments->total ?? 0),
                    'count' => (int) ($supplierPayments->count ?? 0),
                    'is_header' => true,
                ];
            } catch (\Exception $e) {
                $reportData[] = [
                    'transaction' => 'Supplier Payments',
                    'value' => 0,
                    'count' => 0,
                    'is_header' => true,
                ];
            }

            // Sale Challans (if table exists)
            try {
                $saleChallans = DB::table('sale_challan_transactions')
                    ->whereBetween('challan_date', [$fromDate, $toDate])
                    ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                    ->first();

                $reportData[] = [
                    'transaction' => 'Sale Challans',
                    'value' => (float) ($saleChallans->total ?? 0),
                    'count' => (int) ($saleChallans->count ?? 0),
                    'is_header' => true,
                ];
            } catch (\Exception $e) {
                // Table doesn't exist, skip
            }

            // Purchase Challans (if table exists)
            try {
                $purchaseChallans = DB::table('purchase_challan_transactions')
                    ->whereBetween('challan_date', [$fromDate, $toDate])
                    ->selectRaw('SUM(net_amount) as total, COUNT(*) as count')
                    ->first();

                $reportData[] = [
                    'transaction' => 'Purchase Challans',
                    'value' => (float) ($purchaseChallans->total ?? 0),
                    'count' => (int) ($purchaseChallans->count ?? 0),
                    'is_header' => true,
                ];
            } catch (\Exception $e) {
                // Table doesn't exist, skip
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.day-check-list-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.day-check-list', compact('reportData'));
    }

    public function prescriptionReminderList(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $lastDate = $request->last_date == 1;

            // Get prescriptions that are due for renewal (validity_date within the date range)
            // These are prescriptions where the validity is expiring soon
            $query = CustomerPrescription::with(['customer'])
                ->whereBetween('validity_date', [$fromDate, $toDate]);

            if ($lastDate) {
                // Filter by prescription date if last_date is checked
                $query->whereBetween('prescription_date', [$fromDate, $toDate]);
            }

            $prescriptions = $query->orderBy('validity_date')->get();

            foreach ($prescriptions as $prescription) {
                // For each prescription, get the last sold items to this customer
                $lastSaleItems = DB::table('sale_transaction_items')
                    ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                    ->join('items', 'sale_transaction_items.item_id', '=', 'items.id')
                    ->where('sale_transactions.customer_id', $prescription->customer_id)
                    ->where('items.schedule', '!=', '')
                    ->whereNotNull('items.schedule')
                    ->select(
                        'sale_transactions.sale_date',
                        'items.name as item_name',
                        'items.packing'
                    )
                    ->orderBy('sale_transactions.sale_date', 'desc')
                    ->limit(10)
                    ->get();

                if ($lastSaleItems->count() > 0) {
                    foreach ($lastSaleItems as $item) {
                        $reportData[] = [
                            'date' => \Carbon\Carbon::parse($item->sale_date)->format('d-M-y'),
                            'party_name' => $prescription->customer->name ?? $prescription->patient_name ?? 'N/A',
                            'item_name' => $item->item_name,
                            'pack' => $item->packing ?? '',
                        ];
                    }
                } else {
                    // If no sale items found, show the prescription info
                    $reportData[] = [
                        'date' => $prescription->validity_date ? $prescription->validity_date->format('d-M-y') : '',
                        'party_name' => $prescription->customer->name ?? $prescription->patient_name ?? 'N/A',
                        'item_name' => $prescription->details ?? 'Prescription Due',
                        'pack' => '',
                    ];
                }
            }

            // If no prescriptions found, try to get from sale transactions directly
            // for items that have a schedule (prescription required items)
            if (count($reportData) == 0) {
                $saleItems = DB::table('sale_transaction_items')
                    ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                    ->join('items', 'sale_transaction_items.item_id', '=', 'items.id')
                    ->join('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                    ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                    ->where(function($q) {
                        $q->where('items.schedule', '!=', '')
                          ->orWhere('items.narcotic_flag', 'Y');
                    })
                    ->select(
                        'sale_transactions.sale_date',
                        'customers.name as customer_name',
                        'items.name as item_name',
                        'items.packing'
                    )
                    ->orderBy('sale_transactions.sale_date')
                    ->get();

                foreach ($saleItems as $item) {
                    $reportData[] = [
                        'date' => \Carbon\Carbon::parse($item->sale_date)->format('d-M-y'),
                        'party_name' => $item->customer_name ?? 'N/A',
                        'item_name' => $item->item_name,
                        'pack' => $item->packing ?? '',
                    ];
                }
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.prescription-reminder-list-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.prescription-reminder-list', compact('reportData'));
    }

    public function ledgerDueListMismatchReport(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $ledgerCode = $request->ledger_code ?? 'CL';

            if ($ledgerCode == 'CL') {
                // Customer Ledger Mismatch
                $customers = Customer::where('is_deleted', 0)->orderBy('code')->get();

                foreach ($customers as $customer) {
                    // Get ledger balance (sum of all transactions)
                    $ledgerBalance = DB::table('customer_ledgers')
                        ->where('customer_id', $customer->id)
                        ->sum('amount');

                    // Get due list amount (from customer dues: debit - credit)
                    try {
                        $dueDebit = DB::table('customer_dues')
                            ->where('customer_id', $customer->id)
                            ->sum('debit');
                        $dueCredit = DB::table('customer_dues')
                            ->where('customer_id', $customer->id)
                            ->sum('credit');
                        $dueListAmount = (float) $dueDebit - (float) $dueCredit;
                    } catch (\Exception $e) {
                        $dueListAmount = 0;
                    }

                    // If no customer_dues, try calculating from balance_amount in sales
                    if ($dueListAmount == 0) {
                        try {
                            $dueListAmount = DB::table('sale_transactions')
                                ->where('customer_id', $customer->id)
                                ->sum('balance_amount');
                        } catch (\Exception $e) {
                            $dueListAmount = 0;
                        }
                    }

                    $ledgerBalance = (float) $ledgerBalance;
                    $dueListAmount = (float) $dueListAmount;
                    $difference = $ledgerBalance - $dueListAmount;

                    // Only show if there's a mismatch (difference != 0)
                    if (abs($difference) > 0.01) {
                        $reportData[] = [
                            'code' => $customer->code ?? '',
                            'party_name' => $customer->name ?? 'N/A',
                            'ledger_amount' => $ledgerBalance,
                            'due_list_amount' => $dueListAmount,
                            'difference' => $difference,
                        ];
                    }
                }
            } else {
                // Supplier Ledger Mismatch
                $suppliers = Supplier::where('is_deleted', 0)->orderBy('code')->get();

                foreach ($suppliers as $supplier) {
                    // Get ledger balance
                    try {
                        $ledgerBalance = DB::table('supplier_ledgers')
                            ->where('supplier_id', $supplier->supplier_id)
                            ->sum('amount');
                    } catch (\Exception $e) {
                        $ledgerBalance = 0;
                    }

                    // Get due list amount from purchase transactions
                    $dueListAmount = DB::table('purchase_transactions')
                        ->where('supplier_id', $supplier->supplier_id)
                        ->sum('balance_amount');

                    $ledgerBalance = (float) $ledgerBalance;
                    $dueListAmount = (float) $dueListAmount;
                    $difference = $ledgerBalance - $dueListAmount;

                    // Only show if there's a mismatch
                    if (abs($difference) > 0.01) {
                        $reportData[] = [
                            'code' => $supplier->code ?? '',
                            'party_name' => $supplier->name ?? 'N/A',
                            'ledger_amount' => $ledgerBalance,
                            'due_list_amount' => $dueListAmount,
                            'difference' => $difference,
                        ];
                    }
                }
            }

            // Sort by difference (largest mismatch first)
            usort($reportData, function($a, $b) {
                return abs($b['difference']) <=> abs($a['difference']);
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.ledger-due-list-mismatch-report-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.ledger-due-list-mismatch-report', compact('reportData'));
    }

    public function salepurchase1DueListMismatchReport(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $type = strtoupper($request->type ?? 'C');

            if ($type == 'C') {
                // Customer Sales - show sale transactions with due amounts
                $transactions = DB::table('sale_transactions')
                    ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                    ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                    ->select(
                        'sale_transactions.sale_date',
                        'sale_transactions.invoice_no',
                        'customers.code',
                        'customers.name',
                        'sale_transactions.net_amount',
                        DB::raw('COALESCE(sale_transactions.paid_amount, 0) as paid_amount'),
                        'sale_transactions.balance_amount',
                        'sale_transactions.customer_id'
                    )
                    ->orderBy('sale_transactions.sale_date')
                    ->get();

                foreach ($transactions as $trn) {
                    $trnAmount = (float) ($trn->net_amount ?? 0);
                    $adjAmount = (float) ($trn->paid_amount ?? 0);
                    $osAmount = (float) ($trn->balance_amount ?? 0);

                    // Get due amount from customer_dues for this transaction
                    try {
                        $dueRecord = DB::table('customer_dues')
                            ->where('customer_id', $trn->customer_id)
                            ->where('trans_no', $trn->invoice_no)
                            ->first();
                        $dueAmount = $dueRecord ? ((float) ($dueRecord->debit ?? 0) - (float) ($dueRecord->credit ?? 0)) : $osAmount;
                    } catch (\Exception $e) {
                        $dueAmount = $osAmount;
                    }

                    $reportData[] = [
                        'date' => \Carbon\Carbon::parse($trn->sale_date)->format('d-M-y'),
                        'trn_no' => $trn->invoice_no ?? '',
                        'code' => $trn->code ?? '',
                        'party_name' => $trn->name ?? 'N/A',
                        'trn_amount' => $trnAmount,
                        'adj_amount' => $adjAmount,
                        'os_amount' => $osAmount,
                        'due_amount' => $dueAmount,
                    ];
                }
            } else {
                // Supplier Purchases - show purchase transactions with due amounts
                $transactions = DB::table('purchase_transactions')
                    ->leftJoin('suppliers', 'purchase_transactions.supplier_id', '=', 'suppliers.supplier_id')
                    ->whereBetween('purchase_transactions.bill_date', [$fromDate, $toDate])
                    ->select(
                        'purchase_transactions.bill_date',
                        'purchase_transactions.bill_no',
                        'suppliers.code',
                        'suppliers.name',
                        'purchase_transactions.net_amount',
                        DB::raw('(purchase_transactions.net_amount - purchase_transactions.balance_amount) as paid_amount'),
                        'purchase_transactions.balance_amount',
                        'purchase_transactions.supplier_id'
                    )
                    ->orderBy('purchase_transactions.bill_date')
                    ->get();

                foreach ($transactions as $trn) {
                    $trnAmount = (float) ($trn->net_amount ?? 0);
                    $adjAmount = (float) ($trn->paid_amount ?? 0);
                    $osAmount = (float) ($trn->balance_amount ?? 0);

                    // Get due amount from supplier ledger
                    try {
                        $dueAmount = DB::table('supplier_ledgers')
                            ->where('supplier_id', $trn->supplier_id)
                            ->where('trans_no', $trn->bill_no)
                            ->sum('amount');
                        $dueAmount = (float) $dueAmount ?: $osAmount;
                    } catch (\Exception $e) {
                        $dueAmount = $osAmount;
                    }

                    $reportData[] = [
                        'date' => \Carbon\Carbon::parse($trn->bill_date)->format('d-M-y'),
                        'trn_no' => $trn->bill_no ?? '',
                        'code' => $trn->code ?? '',
                        'party_name' => $trn->name ?? 'N/A',
                        'trn_amount' => $trnAmount,
                        'adj_amount' => $adjAmount,
                        'os_amount' => $osAmount,
                        'due_amount' => $dueAmount,
                    ];
                }
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.salepurchase1-due-list-mismatch-report-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.salepurchase1-due-list-mismatch-report', compact('reportData'));
    }

    public function attendenceSheet(Request $request)
    {
        $users = User::where('is_active', 1)->orderBy('full_name')->get();
        $reportData = [];
        $summary = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $selectionType = strtoupper($request->selection_type ?? 'A');
            $userId = $request->user_id;

            // Get users based on selection
            if ($selectionType == 'S' && $userId) {
                $selectedUsers = User::where('user_id', $userId)->get();
            } else {
                $selectedUsers = $users;
            }

            // Generate date range
            $startDate = \Carbon\Carbon::parse($fromDate);
            $endDate = \Carbon\Carbon::parse($toDate);
            $dates = [];
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
            }

            // For each user and date, check for activity
            foreach ($selectedUsers as $user) {
                $presentCount = 0;
                $absentCount = 0;

                foreach ($dates as $date) {
                    // Check if user had any transaction activity on this date
                    $hasActivity = false;
                    $firstActivity = null;
                    $lastActivity = null;

                    // Check sale transactions created by user
                    try {
                        $saleActivity = DB::table('sale_transactions')
                            ->where('created_by', $user->user_id)
                            ->whereDate('created_at', $date)
                            ->selectRaw('MIN(created_at) as first_time, MAX(created_at) as last_time')
                            ->first();

                        if ($saleActivity && $saleActivity->first_time) {
                            $hasActivity = true;
                            $firstActivity = $saleActivity->first_time;
                            $lastActivity = $saleActivity->last_time;
                        }
                    } catch (\Exception $e) {
                        // Table or column doesn't exist
                    }

                    // Check purchase transactions
                    if (!$hasActivity) {
                        try {
                            $purchaseActivity = DB::table('purchase_transactions')
                                ->where('created_by', $user->user_id)
                                ->whereDate('created_at', $date)
                                ->selectRaw('MIN(created_at) as first_time, MAX(created_at) as last_time')
                                ->first();

                            if ($purchaseActivity && $purchaseActivity->first_time) {
                                $hasActivity = true;
                                $firstActivity = $purchaseActivity->first_time;
                                $lastActivity = $purchaseActivity->last_time;
                            }
                        } catch (\Exception $e) {
                            // Table or column doesn't exist
                        }
                    }

                    $status = $hasActivity ? 'Present' : 'Absent';
                    $inTime = $firstActivity ? \Carbon\Carbon::parse($firstActivity)->format('h:i A') : '-';
                    $outTime = $lastActivity ? \Carbon\Carbon::parse($lastActivity)->format('h:i A') : '-';

                    // Don't show absent for weekends (optional)
                    $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                    $isWeekend = ($dayOfWeek == 0); // Sunday only
                    
                    if ($isWeekend && !$hasActivity) {
                        $status = 'Holiday';
                    }

                    if ($status == 'Present') {
                        $presentCount++;
                    } elseif ($status == 'Absent') {
                        $absentCount++;
                    }

                    $reportData[] = [
                        'user_name' => $user->full_name,
                        'date' => \Carbon\Carbon::parse($date)->format('d-M-Y'),
                        'in_time' => $inTime,
                        'out_time' => $outTime,
                        'status' => $status,
                        'remarks' => '',
                    ];
                }

                // Add to summary
                $totalDays = count($dates);
                $summary[] = [
                    'user_name' => $user->full_name,
                    'total_days' => $totalDays,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'percentage' => $totalDays > 0 ? ($presentCount / $totalDays) * 100 : 0,
                ];
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.attendence-sheet-print', compact('reportData', 'summary', 'request'));
            }
        }

        return view('admin.reports.management-report.others.attendence-sheet', compact('users', 'reportData', 'summary'));
    }

    public function listOfModifications(Request $request)
    {
        $users = User::where('is_active', 1)->orderBy('full_name')->get();
        $seriesList = ['A', 'B', 'C', 'D', 'E'];
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $dateFilter = strtoupper($request->date_filter ?? 'A');
            $type = $request->type ?? 'ALL';
            $status = $request->status ?? 'ALL';
            $userId = $request->user_id;
            $invoiceNo = $request->invoice_no;
            $itemName = $request->item_name;
            $customerName = $request->customer_name;

            // Get modified/updated sale transactions
            if ($type == 'ALL' || $type == 'SALE') {
                $saleQuery = DB::table('sale_transactions')
                    ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                    ->leftJoin('users', 'sale_transactions.updated_by', '=', 'users.user_id')
                    ->whereBetween('sale_transactions.updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->whereRaw('sale_transactions.updated_at != sale_transactions.created_at');

                if ($userId && $userId != 'ALL') {
                    $saleQuery->where('sale_transactions.updated_by', $userId);
                }
                if ($invoiceNo) {
                    $saleQuery->where('sale_transactions.invoice_no', 'like', "%$invoiceNo%");
                }
                if ($customerName) {
                    $saleQuery->where('customers.name', 'like', "%$customerName%");
                }

                $sales = $saleQuery->select(
                    DB::raw("'SALE' as type"),
                    'sale_transactions.invoice_no',
                    'sale_transactions.sale_date as trans_date',
                    'customers.name as party_name',
                    'sale_transactions.net_amount',
                    'sale_transactions.status',
                    'users.full_name as modified_by',
                    'sale_transactions.updated_at'
                )->get();

                foreach ($sales as $sale) {
                    $reportData[] = [
                        'type' => 'SALE',
                        'invoice_no' => $sale->invoice_no ?? '',
                        'date' => $sale->trans_date ? \Carbon\Carbon::parse($sale->trans_date)->format('d-M-y') : '',
                        'party_name' => $sale->party_name ?? 'N/A',
                        'amount' => (float) ($sale->net_amount ?? 0),
                        'status' => 'Modified',
                        'modified_by' => $sale->modified_by ?? 'N/A',
                        'modified_at' => $sale->updated_at ? \Carbon\Carbon::parse($sale->updated_at)->format('d-M-y H:i') : '',
                    ];
                }
            }

            // Get modified purchase transactions
            if ($type == 'ALL' || $type == 'PURCHASE') {
                $purchaseQuery = DB::table('purchase_transactions')
                    ->leftJoin('suppliers', 'purchase_transactions.supplier_id', '=', 'suppliers.supplier_id')
                    ->leftJoin('users', 'purchase_transactions.updated_by', '=', 'users.user_id')
                    ->whereBetween('purchase_transactions.updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->whereRaw('purchase_transactions.updated_at != purchase_transactions.created_at');

                if ($userId && $userId != 'ALL') {
                    $purchaseQuery->where('purchase_transactions.updated_by', $userId);
                }
                if ($invoiceNo) {
                    $purchaseQuery->where('purchase_transactions.bill_no', 'like', "%$invoiceNo%");
                }

                $purchases = $purchaseQuery->select(
                    DB::raw("'PURCHASE' as type"),
                    'purchase_transactions.bill_no as invoice_no',
                    'purchase_transactions.bill_date as trans_date',
                    'suppliers.name as party_name',
                    'purchase_transactions.net_amount',
                    'purchase_transactions.status',
                    'users.full_name as modified_by',
                    'purchase_transactions.updated_at'
                )->get();

                foreach ($purchases as $purchase) {
                    $reportData[] = [
                        'type' => 'PURCHASE',
                        'invoice_no' => $purchase->invoice_no ?? '',
                        'date' => $purchase->trans_date ? \Carbon\Carbon::parse($purchase->trans_date)->format('d-M-y') : '',
                        'party_name' => $purchase->party_name ?? 'N/A',
                        'amount' => (float) ($purchase->net_amount ?? 0),
                        'status' => 'Modified',
                        'modified_by' => $purchase->modified_by ?? 'N/A',
                        'modified_at' => $purchase->updated_at ? \Carbon\Carbon::parse($purchase->updated_at)->format('d-M-y H:i') : '',
                    ];
                }
            }

            // Get modified sale returns
            if ($type == 'ALL' || $type == 'SALE_RETURN') {
                try {
                    $srQuery = DB::table('sale_return_transactions')
                        ->leftJoin('customers', 'sale_return_transactions.customer_id', '=', 'customers.id')
                        ->leftJoin('users', 'sale_return_transactions.updated_by', '=', 'users.user_id')
                        ->whereBetween('sale_return_transactions.updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                        ->whereRaw('sale_return_transactions.updated_at != sale_return_transactions.created_at');

                    if ($userId && $userId != 'ALL') {
                        $srQuery->where('sale_return_transactions.updated_by', $userId);
                    }

                    $saleReturns = $srQuery->select(
                        DB::raw("'SALE_RETURN' as type"),
                        'sale_return_transactions.sr_no as invoice_no',
                        'sale_return_transactions.return_date as trans_date',
                        'customers.name as party_name',
                        'sale_return_transactions.net_amount',
                        'users.full_name as modified_by',
                        'sale_return_transactions.updated_at'
                    )->get();

                    foreach ($saleReturns as $sr) {
                        $reportData[] = [
                            'type' => 'SALE RETURN',
                            'invoice_no' => $sr->invoice_no ?? '',
                            'date' => $sr->trans_date ? \Carbon\Carbon::parse($sr->trans_date)->format('d-M-y') : '',
                            'party_name' => $sr->party_name ?? 'N/A',
                            'amount' => (float) ($sr->net_amount ?? 0),
                            'status' => 'Modified',
                            'modified_by' => $sr->modified_by ?? 'N/A',
                            'modified_at' => $sr->updated_at ? \Carbon\Carbon::parse($sr->updated_at)->format('d-M-y H:i') : '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Table doesn't exist
                }
            }

            // Sort by modified_at descending
            usort($reportData, function($a, $b) {
                return strcmp($b['modified_at'], $a['modified_at']);
            });

            // Filter by status if not ALL
            if ($status != 'ALL') {
                $reportData = array_filter($reportData, function($row) use ($status) {
                    return $row['status'] == $status;
                });
                $reportData = array_values($reportData);
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.list-of-modifications-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.list-of-modifications', compact('users', 'seriesList', 'reportData'));
    }

    public function listOfMasterModifications(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print') || $request->has('excel')) {
            $typeCode = $request->type_code ?? $request->type ?? '6';

            // 1 = Customer, 2 = Supplier, 3 = Item, 4 = Company, 5 = Salesman, 6 = All

            // Get Customer records
            if ($typeCode == '1' || $typeCode == '6') {
                try {
                    $customers = DB::table('customers')
                        ->leftJoin('users', 'customers.updated_by', '=', 'users.user_id')
                        ->select(
                            'customers.code',
                            'customers.name',
                            'customers.is_deleted',
                            'users.full_name as modified_by',
                            'customers.updated_by'
                        )
                        ->orderBy('customers.name')
                        ->get();

                    foreach ($customers as $c) {
                        $action = $c->is_deleted ? 'Deleted' : 'Active';

                        $reportData[] = [
                            'type' => 'Customer',
                            'code' => $c->code ?? '',
                            'name' => $c->name ?? 'N/A',
                            'action' => $action,
                            'modified_by' => $c->modified_by ?? ($c->updated_by ? 'User #' . $c->updated_by : 'N/A'),
                            'modified_at' => '-',
                            'changed_fields' => '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if table doesn't have expected columns
                }
            }

            // Get Supplier records
            if ($typeCode == '2' || $typeCode == '6') {
                try {
                    $suppliers = DB::table('suppliers')
                        ->leftJoin('users', 'suppliers.updated_by', '=', 'users.user_id')
                        ->select(
                            'suppliers.code',
                            'suppliers.name',
                            'suppliers.is_deleted',
                            'users.full_name as modified_by',
                            'suppliers.updated_by'
                        )
                        ->orderBy('suppliers.name')
                        ->get();

                    foreach ($suppliers as $s) {
                        $action = $s->is_deleted ? 'Deleted' : 'Active';

                        $reportData[] = [
                            'type' => 'Supplier',
                            'code' => $s->code ?? '',
                            'name' => $s->name ?? 'N/A',
                            'action' => $action,
                            'modified_by' => $s->modified_by ?? ($s->updated_by ? 'User #' . $s->updated_by : 'N/A'),
                            'modified_at' => '-',
                            'changed_fields' => '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if table doesn't have expected columns
                }
            }

            // Get Item records
            if ($typeCode == '3' || $typeCode == '6') {
                try {
                    $items = DB::table('items')
                        ->leftJoin('users', 'items.updated_by', '=', 'users.user_id')
                        ->select(
                            'items.code',
                            'items.name',
                            'items.is_deleted',
                            'users.full_name as modified_by',
                            'items.updated_by'
                        )
                        ->orderBy('items.name')
                        ->get();

                    foreach ($items as $i) {
                        $action = $i->is_deleted ? 'Deleted' : 'Active';

                        $reportData[] = [
                            'type' => 'Item',
                            'code' => $i->code ?? '',
                            'name' => $i->name ?? 'N/A',
                            'action' => $action,
                            'modified_by' => $i->modified_by ?? ($i->updated_by ? 'User #' . $i->updated_by : 'N/A'),
                            'modified_at' => '-',
                            'changed_fields' => '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if table doesn't have expected columns
                }
            }

            // Get Company records
            if ($typeCode == '4' || $typeCode == '6') {
                try {
                    $companies = DB::table('companies')
                        ->leftJoin('users', 'companies.updated_by', '=', 'users.user_id')
                        ->select(
                            'companies.code',
                            'companies.name',
                            'companies.is_deleted',
                            'users.full_name as modified_by',
                            'companies.updated_by'
                        )
                        ->orderBy('companies.name')
                        ->get();

                    foreach ($companies as $c) {
                        $action = $c->is_deleted ? 'Deleted' : 'Active';

                        $reportData[] = [
                            'type' => 'Company',
                            'code' => $c->code ?? '',
                            'name' => $c->name ?? 'N/A',
                            'action' => $action,
                            'modified_by' => $c->modified_by ?? ($c->updated_by ? 'User #' . $c->updated_by : 'N/A'),
                            'modified_at' => '-',
                            'changed_fields' => '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if table doesn't have expected columns
                }
            }

            // Get Salesman records
            if ($typeCode == '5' || $typeCode == '6') {
                try {
                    $salesmen = DB::table('sales_men')
                        ->leftJoin('users', 'sales_men.updated_by', '=', 'users.user_id')
                        ->select(
                            'sales_men.code',
                            'sales_men.name',
                            'sales_men.is_deleted',
                            'users.full_name as modified_by',
                            'sales_men.updated_by'
                        )
                        ->orderBy('sales_men.name')
                        ->get();

                    foreach ($salesmen as $sm) {
                        $action = $sm->is_deleted ? 'Deleted' : 'Active';

                        $reportData[] = [
                            'type' => 'Salesman',
                            'code' => $sm->code ?? '',
                            'name' => $sm->name ?? 'N/A',
                            'action' => $action,
                            'modified_by' => $sm->modified_by ?? ($sm->updated_by ? 'User #' . $sm->updated_by : 'N/A'),
                            'modified_at' => '-',
                            'changed_fields' => '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if table doesn't have expected columns
                }
            }

            // Sort by type and name
            usort($reportData, function($a, $b) {
                $typeCompare = strcmp($a['type'], $b['type']);
                return $typeCompare !== 0 ? $typeCompare : strcmp($a['name'], $b['name']);
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.list-of-master-modifications-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.list-of-master-modifications', compact('reportData'));
    }

    public function clSlDateWiseLedgerSummary(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $type = strtoupper($request->type ?? 'C');

            // Generate date range
            $startDate = \Carbon\Carbon::parse($fromDate);
            $endDate = \Carbon\Carbon::parse($toDate);

            if ($type == 'C') {
                // Customer Ledger Summary
                $tableName = 'customer_ledgers';
                $dateColumn = 'transaction_date';

                // Get opening balance (sum of all transactions before fromDate)
                try {
                    $openingBalance = DB::table($tableName)
                        ->where($dateColumn, '<', $fromDate)
                        ->sum('amount');
                } catch (\Exception $e) {
                    $openingBalance = 0;
                }

                $runningBalance = (float) $openingBalance;

                // Get daily transactions
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');

                    try {
                        // Get debit and credit for this date
                        $dayData = DB::table($tableName)
                            ->whereDate($dateColumn, $dateStr)
                            ->selectRaw("
                                SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as debit,
                                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as credit
                            ")
                            ->first();

                        $debit = (float) ($dayData->debit ?? 0);
                        $credit = (float) ($dayData->credit ?? 0);
                    } catch (\Exception $e) {
                        $debit = 0;
                        $credit = 0;
                    }

                    $opening = $runningBalance;
                    $closing = $opening + $debit - $credit;
                    $runningBalance = $closing;

                    // Only add if there's any transaction
                    if ($debit > 0 || $credit > 0) {
                        $reportData[] = [
                            'date' => $date->format('d-M-Y'),
                            'opening' => $opening,
                            'debit' => $debit,
                            'credit' => $credit,
                            'closing' => $closing,
                        ];
                    }
                }
            } else {
                // Supplier Ledger Summary
                $tableName = 'supplier_ledgers';
                $dateColumn = 'transaction_date';

                // Get opening balance
                try {
                    $openingBalance = DB::table($tableName)
                        ->where($dateColumn, '<', $fromDate)
                        ->sum('amount');
                } catch (\Exception $e) {
                    $openingBalance = 0;
                }

                $runningBalance = (float) $openingBalance;

                // Get daily transactions
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');

                    try {
                        $dayData = DB::table($tableName)
                            ->whereDate($dateColumn, $dateStr)
                            ->selectRaw("
                                SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as debit,
                                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as credit
                            ")
                            ->first();

                        $debit = (float) ($dayData->debit ?? 0);
                        $credit = (float) ($dayData->credit ?? 0);
                    } catch (\Exception $e) {
                        $debit = 0;
                        $credit = 0;
                    }

                    $opening = $runningBalance;
                    $closing = $opening + $debit - $credit;
                    $runningBalance = $closing;

                    if ($debit > 0 || $credit > 0) {
                        $reportData[] = [
                            'date' => $date->format('d-M-Y'),
                            'opening' => $opening,
                            'debit' => $debit,
                            'credit' => $credit,
                            'closing' => $closing,
                        ];
                    }
                }
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.cl-sl-date-wise-ledger-summary-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.cl-sl-date-wise-ledger-summary', compact('reportData'));
    }

    public function userWorkSummary(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            // Get all active users
            $users = User::where('is_active', 1)->orderBy('full_name')->get();

            foreach ($users as $user) {
                $sales = 0;
                $purchases = 0;
                $saleReturns = 0;
                $purchaseReturns = 0;
                $receipts = 0;
                $payments = 0;

                // Count sales created by user
                try {
                    $sales = DB::table('sale_transactions')
                        ->where('created_by', $user->user_id)
                        ->whereBetween('sale_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                // Count purchases created by user
                try {
                    $purchases = DB::table('purchase_transactions')
                        ->where('created_by', $user->user_id)
                        ->whereBetween('bill_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                // Count sale returns created by user
                try {
                    $saleReturns = DB::table('sale_return_transactions')
                        ->where('created_by', $user->user_id)
                        ->whereBetween('return_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                // Count purchase returns created by user
                try {
                    $purchaseReturns = DB::table('purchase_return_transactions')
                        ->where('created_by', $user->user_id)
                        ->whereBetween('return_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                // Count receipts created by user
                try {
                    $receipts = DB::table('customer_ledgers')
                        ->where('created_by', $user->user_id)
                        ->where('transaction_type', 'Receipt')
                        ->whereBetween('transaction_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                // Count payments created by user
                try {
                    $payments = DB::table('supplier_ledgers')
                        ->where('created_by', $user->user_id)
                        ->where('transaction_type', 'Payment')
                        ->whereBetween('transaction_date', [$fromDate, $toDate])
                        ->count();
                } catch (\Exception $e) {}

                $total = $sales + $purchases + $saleReturns + $purchaseReturns + $receipts + $payments;

                // Only add users who have done some work
                if ($total > 0) {
                    $reportData[] = [
                        'user_name' => $user->full_name,
                        'sales' => $sales,
                        'purchases' => $purchases,
                        'sale_returns' => $saleReturns,
                        'purchase_returns' => $purchaseReturns,
                        'receipts' => $receipts,
                        'payments' => $payments,
                        'total' => $total,
                    ];
                }
            }

            // Sort by total descending
            usort($reportData, function($a, $b) {
                return $b['total'] - $a['total'];
            });

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.user-work-summary-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.user-work-summary', compact('reportData'));
    }

    public function hsnWiseSalePurchaseReport(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $reportType = $request->report_type ?? '3'; // 1=Sale, 2=Purchase, 3=Both
            $partyType = strtoupper($request->party_type ?? 'C');
            $hsn = $request->hsn;
            $tax = $request->tax;
            $itemCode = $request->item;

            // Get company GSTIN
            $company = Company::first();
            $companyGstin = $company->gst_no ?? '';

            // Get Sales (if type is 1 or 3)
            if ($reportType == '1' || $reportType == '3') {
                try {
                    $salesQuery = DB::table('sale_transaction_items')
                        ->join('sale_transactions', 'sale_transaction_items.sale_transaction_id', '=', 'sale_transactions.id')
                        ->join('items', 'sale_transaction_items.item_id', '=', 'items.id')
                        ->leftJoin('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                        ->whereBetween('sale_transactions.sale_date', [$fromDate, $toDate])
                        ->select(
                            'items.hsn_code',
                            'items.name as product_name',
                            'sale_transactions.sale_date',
                            'sale_transactions.invoice_no',
                            'customers.gst_no as customer_gst'
                        );

                    if ($hsn) {
                        $salesQuery->where('items.hsn_code', 'like', $hsn . '%');
                    }
                    if ($itemCode) {
                        $salesQuery->where('items.code', $itemCode);
                    }

                    $salesQuery->orderBy('items.name')->orderBy('sale_transactions.sale_date');

                    $sales = $salesQuery->get();

                    foreach ($sales as $sale) {
                        $reportData[] = [
                            'gstin' => $companyGstin,
                            'hsn' => $sale->hsn_code ?? '',
                            'product_name' => $sale->product_name ?? '',
                            'is_sanitizer' => 'No',
                            'nature' => 'Sale',
                            'inv_date' => $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d-M-y') : '',
                            'inv_no' => $sale->invoice_no ?? '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if error
                }
            }

            // Get Purchases (if type is 2 or 3)
            if ($reportType == '2' || $reportType == '3') {
                try {
                    $purchasesQuery = DB::table('purchase_transaction_items')
                        ->join('purchase_transactions', 'purchase_transaction_items.purchase_transaction_id', '=', 'purchase_transactions.id')
                        ->join('items', 'purchase_transaction_items.item_id', '=', 'items.id')
                        ->leftJoin('suppliers', 'purchase_transactions.supplier_id', '=', 'suppliers.supplier_id')
                        ->whereBetween('purchase_transactions.bill_date', [$fromDate, $toDate])
                        ->select(
                            'items.hsn_code',
                            'items.name as product_name',
                            'purchase_transactions.bill_date',
                            'purchase_transactions.bill_no',
                            'suppliers.gst_no as supplier_gst'
                        );

                    if ($hsn) {
                        $purchasesQuery->where('items.hsn_code', 'like', $hsn . '%');
                    }
                    if ($itemCode) {
                        $purchasesQuery->where('items.code', $itemCode);
                    }

                    $purchasesQuery->orderBy('items.name')->orderBy('purchase_transactions.bill_date');

                    $purchases = $purchasesQuery->get();

                    foreach ($purchases as $purchase) {
                        $reportData[] = [
                            'gstin' => $purchase->supplier_gst ?? $companyGstin,
                            'hsn' => $purchase->hsn_code ?? '',
                            'product_name' => $purchase->product_name ?? '',
                            'is_sanitizer' => 'No',
                            'nature' => 'Purchase',
                            'inv_date' => $purchase->bill_date ? \Carbon\Carbon::parse($purchase->bill_date)->format('d-M-y') : '',
                            'inv_no' => $purchase->bill_no ?? '',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip if error
                }
            }

            // Sort based on order_by parameter
            $orderBy = $request->order_by ?? 'Item,Inv.Date,Inv.No';
            if ($orderBy == 'HSN,Item') {
                usort($reportData, function($a, $b) {
                    $hsnCmp = strcmp($a['hsn'], $b['hsn']);
                    return $hsnCmp !== 0 ? $hsnCmp : strcmp($a['product_name'], $b['product_name']);
                });
            }

            if ($request->has('print')) {
                return view('admin.reports.management-report.others.hsn-wise-sale-purchase-report-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.management-report.others.hsn-wise-sale-purchase-report', compact('reportData'));
    }

    // AJAX Lookup Methods
    public function getCustomerByCode(Request $request)
    {
        $customer = Customer::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->where('is_deleted', 0)
            ->first();
        
        return response()->json($customer);
    }

    public function getSupplierByCode(Request $request)
    {
        $supplier = Supplier::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->where('is_deleted', 0)
            ->first();
        
        return response()->json($supplier);
    }

    public function getSalesmanByCode(Request $request)
    {
        $salesman = SalesMan::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->where('is_deleted', 0)
            ->first();
        
        return response()->json($salesman);
    }

    public function getAreaByCode(Request $request)
    {
        $area = Area::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->where('is_deleted', 0)
            ->first();
        
        return response()->json($area);
    }

    public function getRouteByCode(Request $request)
    {
        $route = Route::where('id', $request->code)
            ->orWhere('alter_code', $request->code)
            ->first();
        
        return response()->json($route);
    }

    public function getStateByCode(Request $request)
    {
        $state = State::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->first();
        
        return response()->json($state);
    }

    public function getCompanyByCode(Request $request)
    {
        $company = Company::where('id', $request->code)
            ->orWhere('code', $request->code)
            ->where('is_deleted', 0)
            ->first();
        
        return response()->json($company);
    }
}
