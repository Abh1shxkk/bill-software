<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaManager;
use App\Models\Company;
use App\Models\Customer;
use App\Models\GeneralManager;
use App\Models\Item;
use App\Models\Location;
use App\Models\MarketingManager;
use App\Models\PersonalDirectory;
use App\Models\RegionalManager;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\StockAdjustment;
use App\Models\SaleReturnReplacementTransaction;
use App\Traits\ReportHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MiscTransactionReportController extends Controller
{
    use ReportHelperTrait;
    // Misc Transaction Book
    public function miscTransactionBook(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        
        if ($request->has('view') || $request->has('print')) {
            // Fetch data based on transaction type
            $tranType = $request->input('tran_type', 'sale');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $customerId = $request->input('customer_id');
            
            // For now, returning empty collection - you'll need to add specific queries for each transaction type
            $reportData = collect();
            
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.misc-transaction-book-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.misc-transaction-book', compact('customers', 'reportData'));
    }

    // Stock Adjustment
    public function stockAdjustment(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        
        if ($request->has('view') || $request->has('print')) {
            $query = StockAdjustment::with(['items.item'])
                ->where('status', 'active');
            
            // Date filter
            if ($request->filled('from_date')) {
                $query->whereDate('adjustment_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('adjustment_date', '<=', $request->to_date);
            }
            
            // Company filter (through items)
            if ($request->filled('company_id')) {
                $query->whereHas('items.item', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }
            
            // Item filter
            if ($request->filled('item_id')) {
                $query->whereHas('items', function($q) use ($request) {
                    $q->where('item_id', $request->item_id);
                });
            }
            
            $reportData = $query->orderBy('adjustment_date', 'desc')->get();
            
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-adjustment-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-adjustment', compact('companies', 'items', 'reportData'));
    }

    // Stock Transfer Outgoing - Bill Wise
    public function stockTransferOutgoingBillWise(Request $request)
    {
        $locations = collect(); // Location table not yet created
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.bill-wise', compact('locations', 'customers', 'companies', 'reportData'));
    }

    // Stock Transfer Outgoing - Party - Bill Wise
    public function stockTransferOutgoingPartyBillWise(Request $request)
    {
        $locations = collect(); // Location table not yet created
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.party-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.party-bill-wise', compact('locations', 'customers', 'companies', 'reportData'));
    }

    // Stock Transfer Outgoing - Item - Bill Wise
    public function stockTransferOutgoingItemBillWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.item-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.item-bill-wise', compact('companies', 'customers', 'reportData'));
    }

    // Stock Transfer Outgoing - Item - Party - Bill Wise
    public function stockTransferOutgoingItemPartyBillWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.item-party-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-outgoing.item-party-bill-wise', compact('companies', 'customers', 'reportData'));
    }

    // Stock Transfer Incoming - Bill Wise
    public function stockTransferIncomingBillWise(Request $request)
    {
        $locations = collect(); // Location table not yet created
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-incoming.bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-incoming.bill-wise', compact('locations', 'suppliers', 'companies', 'reportData'));
    }

    // Stock Transfer Incoming - Party - Bill Wise
    public function stockTransferIncomingPartyBillWise(Request $request)
    {
        $locations = collect(); // Location table not yet created
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-incoming.party-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-incoming.party-bill-wise', compact('locations', 'suppliers', 'companies', 'reportData'));
    }

    // Stock Transfer Incoming - Item - Bill Wise
    public function stockTransferIncomingItemBillWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-incoming.item-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-incoming.item-bill-wise', compact('companies', 'suppliers', 'reportData'));
    }

    // Stock Transfer Incoming - Item - Party - Bill Wise
    public function stockTransferIncomingItemPartyBillWise(Request $request)
    {
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.stock-transfer-incoming.item-party-bill-wise-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.stock-transfer-incoming.item-party-bill-wise', compact('companies', 'suppliers', 'reportData'));
    }

    // Sale Return Replacement
    public function saleReturnReplacement(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        
        if ($request->has('view') || $request->has('print')) {
            $query = SaleReturnReplacementTransaction::with(['customer', 'items.item']);
            
            // Status filter - only if status column has values
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Date filter
            if ($request->filled('from_date')) {
                $query->whereDate('trn_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('trn_date', '<=', $request->to_date);
            }
            
            // Customer filter
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            
            $reportData = $query->orderBy('trn_date', 'desc')->get();
            
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.sale-return-replacement-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.sale-return-replacement', compact('customers', 'reportData'));
    }

    // Sample Reports - List of Sample Issued
    public function listOfSampleIssued(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::orderBy('name')->get();
        $doctors = PersonalDirectory::orderBy('name')->get(); // Using PersonalDirectory for doctors
        $areaManagers = AreaManager::orderBy('name')->get();
        $regionalManagers = RegionalManager::orderBy('name')->get();
        $marketingManagers = MarketingManager::orderBy('name')->get();
        $generalManagers = GeneralManager::orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.sample-reports.list-of-sample-issued-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.sample-reports.list-of-sample-issued', compact('customers', 'suppliers', 'salesmen', 'doctors', 'areaManagers', 'regionalManagers', 'marketingManagers', 'generalManagers', 'reportData'));
    }

    // Sample Reports - List of Sample Received
    public function listOfSampleReceived(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = Salesman::orderBy('name')->get();
        $doctors = PersonalDirectory::orderBy('name')->get(); // Using PersonalDirectory for doctors
        $areaManagers = AreaManager::orderBy('name')->get();
        $regionalManagers = RegionalManager::orderBy('name')->get();
        $marketingManagers = MarketingManager::orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.sample-reports.list-of-sample-received-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.sample-reports.list-of-sample-received', compact('customers', 'suppliers', 'salesmen', 'doctors', 'areaManagers', 'regionalManagers', 'marketingManagers', 'reportData'));
    }

    // Misc Tran Bill Printing
    public function billPrinting(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        if ($request->has('view') || $request->has('print')) {
            $reportData = collect();
            if ($request->has('print')) {
                return view('admin.reports.misc-transaction-report.misc-tran-bill-printing-print', compact('reportData'));
            }
        }
        return view('admin.reports.misc-transaction-report.misc-tran-bill-printing', compact('customers', 'reportData'));
    }
}
