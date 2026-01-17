<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BreakageExpiryTransaction;
use App\Models\BreakageSupplierIssuedTransaction;
use App\Models\BreakageSupplierReceivedTransaction;
use App\Models\BreakageSupplierUnusedDumpTransaction;
use App\Models\Company;
use App\Models\Customer;
use App\Models\GodownBreakageExpiryTransaction;
use App\Models\Item;
use App\Models\Route;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Traits\ReportHelperTrait;
use Illuminate\Http\Request;

class BreakageExpiryReportController extends Controller
{
    use ReportHelperTrait;
    // Breakage/Expiry from Customer - Pending
    public function fromCustomerPending(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageExpiryTransaction::query()
                ->where('status', 'pending');
            
            if ($request->filled('party_id')) {
                $query->where('customer_id', $request->party_id);
            }
            if ($request->filled('salesman_id')) {
                $query->where('salesman_id', $request->salesman_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-from-customer.pending-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-from-customer.pending', compact('customers', 'companies', 'salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }

    // Breakage/Expiry from Customer - All
    public function fromCustomerAll(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageExpiryTransaction::query();
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('party_id')) {
                $query->where('customer_id', $request->party_id);
            }
            if ($request->filled('salesman_id')) {
                $query->where('salesman_id', $request->salesman_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-from-customer.all-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-from-customer.all', compact('customers', 'companies', 'salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }


    // Breakage/Expiry to Supplier - Pending
    public function toSupplierPending(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageSupplierIssuedTransaction::active()
                ->where('status', 'pending');
            
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.pending-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.pending', compact('suppliers', 'companies', 'reportData', 'totalAmount'));
    }

    // Breakage/Expiry to Supplier - All
    public function toSupplierAll(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageSupplierIssuedTransaction::active();
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.all-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.all', compact('suppliers', 'companies', 'reportData', 'totalAmount'));
    }

    // Breakage/Expiry to Supplier - Difference
    public function toSupplierDifference(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            // Get issued and received to calculate difference
            $query = BreakageSupplierIssuedTransaction::active();
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.difference-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.difference', compact('suppliers', 'companies', 'reportData', 'totalAmount'));
    }

    // Breakage/Expiry to Supplier - Disallow
    public function toSupplierDisallow(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok') || $request->has('excel')) {
            $query = BreakageSupplierIssuedTransaction::active()
                ->where('status', 'disallowed');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.disallow-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.breakage-expiry-to-supplier.disallow', compact('suppliers', 'companies', 'reportData', 'totalAmount'));
    }

    // Godown Brk/Expiry Item Wise - Pending
    public function godownItemWisePending(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalValue = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = GodownBreakageExpiryTransaction::active()
                ->where('status', 'pending')
                ->with('items.item');
            
            $transactions = $query->orderBy('transaction_date', 'desc')->get();
            
            // Flatten items for item-wise report
            $reportData = collect();
            foreach ($transactions as $trn) {
                foreach ($trn->items as $item) {
                    $reportData->push((object)[
                        'company' => $item->item->company->name ?? '',
                        'name' => $item->item->name ?? '',
                        'pack' => $item->item->packing ?? '',
                        'batch' => $item->batch_no ?? '',
                        'expiry' => $item->expiry_date ?? '',
                        'mrp' => $item->mrp ?? 0,
                        'qty_recd' => $item->qty ?? 0,
                        'qty_pend' => $item->pending_qty ?? $item->qty ?? 0,
                        'amount' => $item->amount ?? 0,
                    ]);
                }
            }
            $totalValue = $reportData->sum('amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.pending-print', compact('reportData', 'totalValue'));
            }
        }
        return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.pending', compact('companies', 'reportData', 'totalValue'));
    }

    // Godown Brk/Expiry Item Wise - All
    public function godownItemWiseAll(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalValue = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = GodownBreakageExpiryTransaction::active()
                ->with('items.item');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            
            $transactions = $query->orderBy('transaction_date', 'desc')->get();
            
            $reportData = collect();
            foreach ($transactions as $trn) {
                foreach ($trn->items as $item) {
                    $reportData->push((object)[
                        'company' => $item->item->company->name ?? '',
                        'name' => $item->item->name ?? '',
                        'pack' => $item->item->packing ?? '',
                        'batch' => $item->batch_no ?? '',
                        'expiry' => $item->expiry_date ?? '',
                        'mrp' => $item->mrp ?? 0,
                        'qty_recd' => $item->qty ?? 0,
                        'qty_pend' => $item->pending_qty ?? $item->qty ?? 0,
                        'amount' => $item->amount ?? 0,
                    ]);
                }
            }
            $totalValue = $reportData->sum('amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.all-print', compact('reportData', 'totalValue'));
            }
        }
        return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.all', compact('companies', 'reportData', 'totalValue'));
    }

    // Godown Brk/Expiry Item Wise - Disallowed Items
    public function godownItemWiseDisallowedItems(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalValue = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = GodownBreakageExpiryTransaction::active()
                ->where('status', 'disallowed')
                ->with('items.item');
            
            $transactions = $query->orderBy('transaction_date', 'desc')->get();
            
            $reportData = collect();
            foreach ($transactions as $trn) {
                foreach ($trn->items as $item) {
                    $reportData->push((object)[
                        'company' => $item->item->company->name ?? '',
                        'name' => $item->item->name ?? '',
                        'pack' => $item->item->packing ?? '',
                        'batch' => $item->batch_no ?? '',
                        'expiry' => $item->expiry_date ?? '',
                        'mrp' => $item->mrp ?? 0,
                        'qty_recd' => $item->qty ?? 0,
                        'qty_pend' => $item->pending_qty ?? $item->qty ?? 0,
                        'amount' => $item->amount ?? 0,
                    ]);
                }
            }
            $totalValue = $reportData->sum('amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.disallowed-items-print', compact('reportData', 'totalValue'));
            }
        }
        return view('admin.reports.breakage-expiry-report.godown-brk-expiry-item-wise.disallowed-items', compact('companies', 'reportData', 'totalValue'));
    }

    // Replacement to Customer - Pending
    public function replacementToCustomerPending(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageExpiryTransaction::query()
                ->where('dis_rpl', 'R')
                ->where('status', 'pending');
            
            if ($request->filled('party_id')) {
                $query->where('customer_id', $request->party_id);
            }
            if ($request->filled('salesman_id')) {
                $query->where('salesman_id', $request->salesman_id);
            }
            
            $sortBy = $request->input('sort_by', 'name');
            if ($sortBy == 'name') {
                $query->orderBy('customer_name');
            } elseif ($sortBy == 'date') {
                $query->orderBy('transaction_date', 'desc');
            } else {
                $query->orderBy('sr_no');
            }
            
            $reportData = $query->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.replacement-to-customer.pending-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.replacement-to-customer.pending', compact('customers', 'companies', 'salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }

    // Replacement to Customer - All
    public function replacementToCustomerAll(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageExpiryTransaction::query()
                ->where('dis_rpl', 'R');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('party_id')) {
                $query->where('customer_id', $request->party_id);
            }
            if ($request->filled('salesman_id')) {
                $query->where('salesman_id', $request->salesman_id);
            }
            
            $sortBy = $request->input('sort_by', 'date');
            if ($sortBy == 'name') {
                $query->orderBy('customer_name');
            } elseif ($sortBy == 'date') {
                $query->orderBy('transaction_date', 'desc');
            } else {
                $query->orderBy('sr_no');
            }
            
            $reportData = $query->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.replacement-to-customer.all-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.replacement-to-customer.all', compact('customers', 'companies', 'salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }

    // Replacement to Customer - Item Wise
    public function replacementToCustomerItemWise(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $categories = \App\Models\ItemCategory::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print')) {
            $query = BreakageExpiryTransaction::query()
                ->where('dis_rpl', 'R')
                ->with('items.item');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->filled('company_id')) {
                $query->whereHas('items.item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }
            
            $transactions = $query->get();
            
            $reportData = collect();
            foreach ($transactions as $trn) {
                foreach ($trn->items as $item) {
                    $reportData->push((object)[
                        'date' => $trn->transaction_date,
                        'trn_no' => $trn->sr_no,
                        'customer' => $trn->customer_name,
                        'item_name' => $item->item->name ?? '',
                        'batch' => $item->batch_no ?? '',
                        'qty' => $item->qty ?? 0,
                        'amount' => $item->amount ?? 0,
                    ]);
                }
            }
            $totalAmount = $reportData->sum('amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.replacement-to-customer.item-wise-replacement-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.replacement-to-customer.item-wise-replacement', compact('customers', 'companies', 'items', 'categories', 'reportData', 'totalAmount'));
    }

    // Company Wise Expiry Return - From Customer
    public function companyWiseFromCustomer(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print')) {
            $query = BreakageExpiryTransaction::query()
                ->with('items.item.company');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('company_id')) {
                $query->whereHas('items.item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.company-wise-expiry-return.from-customer-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.company-wise-expiry-return.from-customer', compact('companies', 'customers', 'reportData', 'totalAmount'));
    }

    // Company Wise Expiry Return - To Supplier
    public function companyWiseToSupplier(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print')) {
            $query = BreakageSupplierIssuedTransaction::active()
                ->with('items.item.company');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('company_id')) {
                $query->whereHas('items.item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.company-wise-expiry-return.to-supplier-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.company-wise-expiry-return.to-supplier', compact('companies', 'suppliers', 'reportData', 'totalAmount'));
    }

    // Replacement from Supplier
    public function replacementFromSupplier(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageSupplierReceivedTransaction::active()
                ->where('note_type', 'R');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('party_id')) {
                $query->where('supplier_id', $request->party_id);
            }
            
            $sortBy = $request->input('sort_by', 'date');
            if ($sortBy == 'name') {
                $query->orderBy('supplier_name');
            } elseif ($sortBy == 'date') {
                $query->orderBy('transaction_date', 'desc');
            } else {
                $query->orderBy('trn_no');
            }
            
            $reportData = $query->get();
            $totalAmount = $reportData->sum('final_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.replacement-from-supplier-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.replacement-from-supplier', compact('suppliers', 'companies', 'salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }

    // Unused Dump
    public function unusedDump(Request $request)
    {
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print')) {
            $query = BreakageSupplierUnusedDumpTransaction::query();
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('total_inv_amt');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.unused-dump-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.unused-dump', compact('reportData', 'totalAmount'));
    }

    // List of Disallowed Items
    public function listOfDisallowedItems(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print') || $request->has('ok')) {
            $query = BreakageExpiryTransaction::query()
                ->where('status', 'disallowed')
                ->with('items');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('party_id')) {
                $query->where('customer_id', $request->party_id);
            }
            
            $reportData = $query->orderBy('transaction_date', 'desc')->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.list-of-disallowed-items-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.list-of-disallowed-items', compact('customers', 'reportData', 'totalAmount'));
    }

    // Customer Wise Expiry Return
    public function customerWiseExpiryReturn(Request $request)
    {
        $salesmen = Salesman::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();

        $reportData = collect();
        $totalAmount = 0;
        
        if ($request->has('view') || $request->has('print')) {
            $query = BreakageExpiryTransaction::query()
                ->with('customer');
            
            if ($request->filled('from_date')) {
                $query->whereDate('transaction_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('transaction_date', '<=', $request->to_date);
            }
            if ($request->filled('salesman_id')) {
                $query->where('salesman_id', $request->salesman_id);
            }
            
            // Order by
            $orderBy = $request->input('order_by', 'N');
            $orderDir = $request->input('order_dir', 'A') == 'A' ? 'asc' : 'desc';
            
            if ($orderBy == 'N') {
                $query->orderBy('customer_name', $orderDir);
            } else {
                $query->orderBy('net_amount', $orderDir);
            }
            
            $reportData = $query->get();
            $totalAmount = $reportData->sum('net_amount');
            
            if ($request->has('print')) {
                return view('admin.reports.breakage-expiry-report.customer-wise-expiry-return-print', compact('reportData', 'totalAmount'));
            }
        }
        return view('admin.reports.breakage-expiry-report.customer-wise-expiry-return', compact('salesmen', 'areas', 'routes', 'reportData', 'totalAmount'));
    }
}
