<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
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
        return view('admin.reports.management-report.gross-profit-reports.bill-wise');
    }

    public function grossProfitItemBillWise(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.item-bill-wise');
    }

    public function grossProfitSelectiveAllItems(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.selective-all-items');
    }

    public function grossProfitCompanyBillWise(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.company-bill-wise');
    }

    public function grossProfitSelectiveAllCompanies(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.selective-all-companies');
    }

    public function grossProfitCustomerBillWise(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.customer-bill-wise');
    }

    public function grossProfitSelectiveAllCustomers(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.selective-all-customers');
    }

    public function grossProfitSelectiveAllSuppliers(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.selective-all-suppliers');
    }

    public function grossProfitSaltWise(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.salt-wise');
    }

    public function claimItemsSoldOnLoss(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.claim-items-sold-on-loss');
    }

    public function grossProfitSelectiveAllSalesman(Request $request)
    {
        return view('admin.reports.management-report.gross-profit-reports.selective-all-salesman');
    }

    // Direct Reports
    public function listOfExpiredItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.list-of-expired-items', compact('companies', 'reportData'));
    }

    public function salePurchaseSchemes(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.sale-purchase-schemes', compact('companies', 'reportData'));
    }

    public function suppliersPendingOrder(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.suppliers-pending-order', compact('suppliers', 'reportData'));
    }

    public function customersPendingOrder(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.customers-pending-order', compact('customers', 'reportData'));
    }

    public function nonMovingItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.non-moving-items', compact('companies', 'reportData'));
    }

    public function slowMovingItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        return view('admin.reports.management-report.slow-moving-items', compact('companies', 'reportData'));
    }

    public function performanceReport(Request $request)
    {
        $reportData = collect();
        return view('admin.reports.management-report.performance-report', compact('reportData'));
    }

    // Others
    public function dayCheckList(Request $request)
    {
        return view('admin.reports.management-report.others.day-check-list');
    }

    public function prescriptionReminderList(Request $request)
    {
        return view('admin.reports.management-report.others.prescription-reminder-list');
    }

    public function ledgerDueListMismatchReport(Request $request)
    {
        return view('admin.reports.management-report.others.ledger-due-list-mismatch-report');
    }

    public function salepurchase1DueListMismatchReport(Request $request)
    {
        return view('admin.reports.management-report.others.salepurchase1-due-list-mismatch-report');
    }

    public function attendenceSheet(Request $request)
    {
        return view('admin.reports.management-report.others.attendence-sheet');
    }

    public function listOfModifications(Request $request)
    {
        return view('admin.reports.management-report.others.list-of-modifications');
    }

    public function listOfMasterModifications(Request $request)
    {
        return view('admin.reports.management-report.others.list-of-master-modifications');
    }

    public function clSlDateWiseLedgerSummary(Request $request)
    {
        return view('admin.reports.management-report.others.cl-sl-date-wise-ledger-summary');
    }

    public function userWorkSummary(Request $request)
    {
        return view('admin.reports.management-report.others.user-work-summary');
    }

    public function hsnWiseSalePurchaseReport(Request $request)
    {
        return view('admin.reports.management-report.others.hsn-wise-sale-purchase-report');
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
