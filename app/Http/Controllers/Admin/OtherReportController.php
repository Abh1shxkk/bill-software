<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\SalesMan;
use App\Models\Area;
use App\Models\Route;
use App\Models\State;
use App\Models\ItemCategory;
use App\Models\PersonalDirectory;
use App\Models\CustomerPrescription;
use App\Models\Item;
use App\Models\HsnCode;
use App\Models\GeneralLedger;
use App\Models\TransportMaster;
use App\Traits\ReportHelperTrait;
use Illuminate\Http\Request;

class OtherReportController extends Controller
{
    use ReportHelperTrait;
    /**
     * Customer/Supplier List Report
     */
    public function customerSupplierList(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        
        $reportData = collect();
        $listType = $request->input('list_type', 'C'); // C = Customer, S = Supplier

        if ($request->has('view') || $request->has('print')) {
            if ($listType == 'C') {
                // Customer List
                $query = Customer::where('is_deleted', 0);
                
                // Tax/Retail filter (T = Tax, R = Retail)
                if ($request->filled('tax_retail') && $request->tax_retail != '') {
                    $query->where('tax_registration', $request->tax_retail);
                }
                
                // Status filter
                if ($request->filled('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }
                
                // Flag filter
                if ($request->filled('flag') && $request->flag != '') {
                    $query->where('flag', $request->flag);
                }
                
                // Business Type filter (W = Wholesale, R = Retail, I = Institution, D = Dept Store, O = Others)
                if ($request->filled('business_type') && $request->business_type != '') {
                    $query->where('business_type', $request->business_type);
                }
                
                // TIN filter (1 = With TIN, 2 = Without TIN, 3 = All)
                if ($request->filled('tin_filter') && $request->tin_filter != '3') {
                    if ($request->tin_filter == '1') {
                        $query->whereNotNull('tin_number')->where('tin_number', '!=', '');
                    } elseif ($request->tin_filter == '2') {
                        $query->where(function($q) {
                            $q->whereNull('tin_number')->orWhere('tin_number', '');
                        });
                    }
                }
                
                // Active/Inactive filter (1 = Active, 2 = Inactive, 3 = All)
                if ($request->filled('active_filter') && $request->active_filter != '3') {
                    if ($request->active_filter == '1') {
                        $query->where('status', 'A');
                    } elseif ($request->active_filter == '2') {
                        $query->where('status', 'I');
                    }
                }
                
                // Salesman filter
                if ($request->filled('salesman') && $request->salesman != '00' && $request->salesman != '') {
                    $query->where('sales_man_code', $request->salesman);
                }
                
                // Area filter
                if ($request->filled('area') && $request->area != '00' && $request->area != '') {
                    $query->where('area_code', $request->area);
                }
                
                // Route filter
                if ($request->filled('route') && $request->route != '00' && $request->route != '') {
                    $query->where('route_code', $request->route);
                }
                
                // Day filter
                if ($request->filled('day') && $request->day != '') {
                    $query->where('day_value', $request->day);
                }
                
                // State filter
                if ($request->filled('state') && $request->state != '00' && $request->state != '') {
                    $query->where('state_code', $request->state);
                }
                
                $reportData = $query->orderBy('name')->get();
            } else {
                // Supplier List
                $query = Supplier::where('is_deleted', 0);
                
                // Tax/Retail filter
                if ($request->filled('tax_retail') && $request->tax_retail != '') {
                    $query->where('tax_retail_flag', $request->tax_retail);
                }
                
                // Status filter
                if ($request->filled('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }
                
                // Flag filter
                if ($request->filled('flag') && $request->flag != '') {
                    $query->where('flag', $request->flag);
                }
                
                // TIN filter
                if ($request->filled('tin_filter') && $request->tin_filter != '3') {
                    if ($request->tin_filter == '1') {
                        $query->whereNotNull('tin_no')->where('tin_no', '!=', '');
                    } elseif ($request->tin_filter == '2') {
                        $query->where(function($q) {
                            $q->whereNull('tin_no')->orWhere('tin_no', '');
                        });
                    }
                }
                
                // Active/Inactive filter
                if ($request->filled('active_filter') && $request->active_filter != '3') {
                    if ($request->active_filter == '1') {
                        $query->where('status', 'A');
                    } elseif ($request->active_filter == '2') {
                        $query->where('status', 'I');
                    }
                }
                
                // State filter
                if ($request->filled('state') && $request->state != '00' && $request->state != '') {
                    $query->where('state_code', $request->state);
                }
                
                $reportData = $query->orderBy('name')->get();
            }

            if ($request->has('print')) {
                return view('admin.reports.other-reports.customer-supplier-list-print', compact('reportData', 'listType', 'request'));
            }
        }

        return view('admin.reports.other-reports.customer-supplier-list', compact('reportData', 'salesmen', 'areas', 'routes', 'states', 'listType'));
    }

    /**
     * Mailing Labels Report
     */
    public function mailingLabels(Request $request)
    {
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $categories = ItemCategory::orderBy('name')->get();
        
        $reportData = collect();
        $listType = $request->input('list_type', 'C'); // C = Customer, S = Supplier, P = Personal

        if ($request->has('view') || $request->has('print')) {
            if ($listType == 'C') {
                // Customer Labels
                $query = Customer::where('is_deleted', 0);
                
                // Salesman filter
                if ($request->filled('salesman') && $request->salesman != '00' && $request->salesman != '') {
                    $query->where('sales_man_code', $request->salesman);
                }
                
                // Area filter
                if ($request->filled('area') && $request->area != '00' && $request->area != '') {
                    $query->where('area_code', $request->area);
                }
                
                // Route filter
                if ($request->filled('route') && $request->route != '00' && $request->route != '') {
                    $query->where('route_code', $request->route);
                }
                
                $reportData = $query->orderBy('name')->get();
            } elseif ($listType == 'S') {
                // Supplier Labels
                $reportData = Supplier::where('is_deleted', 0)->orderBy('name')->get();
            } else {
                // Personal Directory Labels
                $reportData = PersonalDirectory::orderBy('name')->get();
            }

            if ($request->has('print')) {
                return view('admin.reports.other-reports.mailing-labels-print', compact('reportData', 'listType', 'request'));
            }
        }

        return view('admin.reports.other-reports.mailing-labels', compact('reportData', 'salesmen', 'areas', 'routes', 'categories', 'listType'));
    }

    /**
     * Doctor Wise Customers Report
     */
    public function doctorWiseCustomers(Request $request)
    {
        // Get unique doctors from prescriptions
        $doctors = CustomerPrescription::select('doctor_name')
            ->whereNotNull('doctor_name')
            ->where('doctor_name', '!=', '')
            ->distinct()
            ->orderBy('doctor_name')
            ->pluck('doctor_name');
        
        $reportData = collect();
        $selectiveDoctor = $request->input('selective', 'Y');

        if ($request->has('view') || $request->has('print')) {
            $query = CustomerPrescription::with('customer')
                ->whereNotNull('doctor_name')
                ->where('doctor_name', '!=', '');
            
            // If selective doctor is Y and doctor is selected
            if ($selectiveDoctor == 'Y' && $request->filled('doctor') && $request->doctor != '00') {
                $query->where('doctor_name', $request->doctor);
            }
            
            $prescriptions = $query->orderBy('doctor_name')->orderBy('patient_name')->get();
            
            // Group by doctor
            $reportData = $prescriptions->groupBy('doctor_name')->map(function($items, $doctorName) {
                return [
                    'doctor_name' => $doctorName,
                    'patients' => $items->map(function($p) {
                        return [
                            'patient_name' => $p->patient_name,
                            'customer_name' => $p->customer?->name ?? '',
                            'customer_code' => $p->customer?->code ?? '',
                            'customer_mobile' => $p->customer?->mobile ?? '',
                            'customer_address' => $p->customer?->address ?? '',
                            'prescription_date' => $p->prescription_date,
                            'validity_date' => $p->validity_date,
                        ];
                    })
                ];
            });

            if ($request->has('print')) {
                return view('admin.reports.other-reports.doctor-wise-customers-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.other-reports.doctor-wise-customers', compact('reportData', 'doctors'));
    }

    /**
     * List of Masters Report
     */
    public function listOfMasters(Request $request)
    {
        $masterTypes = [
            'COMPANY' => 'Company',
            'CUSTOMER' => 'Customer',
            'SUPPLIER' => 'Supplier',
            'ITEM' => 'Item',
            'SALESMAN' => 'Sales Man',
            'AREA' => 'Area',
            'ROUTE' => 'Route',
            'STATE' => 'State',
            'HSN' => 'HSN Code',
            'GENERAL_LEDGER' => 'General Ledger',
            'TRANSPORT' => 'Transport',
        ];
        
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        $selectedMaster = $request->input('master_type', 'COMPANY');

        if ($request->has('view') || $request->has('print')) {
            $printAddress = $request->input('print_address', 'N') == 'Y';
            $directIndirect = $request->input('direct_indirect', '');
            $status = $request->input('status', '');
            $companyCode = $request->input('company_code', '00');
            
            switch ($selectedMaster) {
                case 'COMPANY':
                    $query = Company::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'CUSTOMER':
                    $query = Customer::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'SUPPLIER':
                    $query = Supplier::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    if ($directIndirect) $query->where('direct_indirect', $directIndirect);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'ITEM':
                    $query = Item::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    if ($companyCode && $companyCode != '00') $query->where('company_code', $companyCode);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'SALESMAN':
                    $query = SalesMan::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'AREA':
                    $query = Area::where('is_deleted', 0);
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'ROUTE':
                    $query = Route::query();
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'STATE':
                    $query = State::query();
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
                    
                case 'HSN':
                    $reportData = HsnCode::orderBy('hsn_code')->get();
                    break;
                    
                case 'GENERAL_LEDGER':
                    $query = GeneralLedger::query();
                    $reportData = $query->orderBy('account_name')->get();
                    break;
                    
                case 'TRANSPORT':
                    $query = TransportMaster::query();
                    if ($status) $query->where('status', $status);
                    $reportData = $query->orderBy('name')->get();
                    break;
            }

            if ($request->has('print')) {
                return view('admin.reports.other-reports.list-of-masters-print', compact('reportData', 'selectedMaster', 'masterTypes', 'printAddress', 'request'));
            }
        }

        return view('admin.reports.other-reports.list-of-masters', compact('reportData', 'masterTypes', 'selectedMaster', 'companies'));
    }

    /**
     * Company Wise Discount Report
     */
    public function companyWiseDiscount(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $companies = Company::where('is_deleted', 0)->orderBy('name')->get();
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        
        $reportData = collect();
        $listType = $request->input('list_type', 'C'); // C = Customer, S = Supplier

        if ($request->has('view') || $request->has('print')) {
            $taggedParties = $request->input('tagged_parties', 'N') == 'Y';
            $customerId = $request->input('customer', '00');
            $companyId = $request->input('company', '00');
            $itemId = $request->input('item', '00');
            
            if ($listType == 'C') {
                // Customer wise discount - show companies with discount for each customer
                $query = Customer::where('is_deleted', 0);
                
                if ($customerId && $customerId != '00') {
                    $query->where('id', $customerId);
                }
                
                $customerList = $query->orderBy('name')->get();
                
                // Get all companies
                $companyQuery = Company::where('is_deleted', 0);
                if ($companyId && $companyId != '00') {
                    $companyQuery->where('id', $companyId);
                }
                $companyList = $companyQuery->orderBy('name')->get();
                
                // Build report data - customers with their company-wise discounts
                $reportData = $customerList->map(function($customer) use ($companyList) {
                    return [
                        'party_type' => 'Customer',
                        'party_code' => $customer->code,
                        'party_name' => $customer->name,
                        'companies' => $companyList->map(function($company) use ($customer) {
                            // Get discount from customer's fixed discount fields if available
                            return [
                                'company_code' => $company->code ?? $company->id,
                                'company_name' => $company->name,
                                'discount_brk' => $customer->fixed_discount ?? 0,
                                'discount_exp' => 0,
                            ];
                        })
                    ];
                });
            } else {
                // Supplier wise - similar structure
                $query = Supplier::where('is_deleted', 0);
                
                if ($customerId && $customerId != '00') {
                    $query->where('supplier_id', $customerId);
                }
                
                $supplierList = $query->orderBy('name')->get();
                
                $companyQuery = Company::where('is_deleted', 0);
                if ($companyId && $companyId != '00') {
                    $companyQuery->where('id', $companyId);
                }
                $companyList = $companyQuery->orderBy('name')->get();
                
                $reportData = $supplierList->map(function($supplier) use ($companyList) {
                    return [
                        'party_type' => 'Supplier',
                        'party_code' => $supplier->code,
                        'party_name' => $supplier->name,
                        'companies' => $companyList->map(function($company) {
                            return [
                                'company_code' => $company->code ?? $company->id,
                                'company_name' => $company->name,
                                'discount_brk' => 0,
                                'discount_exp' => 0,
                            ];
                        })
                    ];
                });
            }

            if ($request->has('print')) {
                return view('admin.reports.other-reports.company-wise-discount-print', compact('reportData', 'listType', 'request'));
            }
        }

        return view('admin.reports.other-reports.company-wise-discount', compact('reportData', 'customers', 'suppliers', 'companies', 'items', 'listType'));
    }

    /**
     * Customer List Report
     */
    public function customerList(Request $request)
    {
        $customers = Customer::where('is_deleted', 0)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', 0)->orderBy('name')->get();
        $areas = Area::where('is_deleted', 0)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        
        $reportData = collect();

        if ($request->has('view') || $request->has('print')) {
            $query = Customer::where('is_deleted', 0);
            
            // Tax/Retail filter
            if ($request->filled('tax_retail') && $request->tax_retail != '') {
                $query->where('tax_registration', $request->tax_retail);
            }
            
            // Status filter
            if ($request->filled('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            // Flag filter
            if ($request->filled('flag') && $request->flag != '') {
                $query->where('flag', $request->flag);
            }
            
            // Business Type filter (W/R/I/D/O)
            if ($request->filled('business_type') && $request->business_type != '') {
                $query->where('business_type', $request->business_type);
            }
            
            // Active/Inactive filter (1=Active, 2=Inactive, 3=All)
            if ($request->filled('active_filter') && $request->active_filter != '3') {
                if ($request->active_filter == '1') {
                    $query->where('status', 'A');
                } elseif ($request->active_filter == '2') {
                    $query->where('status', 'I');
                }
            }
            
            // GSTIN filter (1=With GSTIN, 2=Without GSTIN, 3=All)
            if ($request->filled('gstin_filter') && $request->gstin_filter != '3') {
                if ($request->gstin_filter == '1') {
                    $query->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($request->gstin_filter == '2') {
                    $query->where(function($q) {
                        $q->whereNull('gst_number')->orWhere('gst_number', '');
                    });
                }
            }
            
            // Customer filter
            if ($request->filled('customer') && $request->customer != '00') {
                $query->where('id', $request->customer);
            }
            
            // Salesman filter
            if ($request->filled('salesman') && $request->salesman != '00') {
                $query->where('sales_man_code', $request->salesman);
            }
            
            // Area filter
            if ($request->filled('area') && $request->area != '00') {
                $query->where('area_code', $request->area);
            }
            
            // Route filter
            if ($request->filled('route') && $request->route != '00') {
                $query->where('route_code', $request->route);
            }
            
            // State filter
            if ($request->filled('state') && $request->state != '00') {
                $query->where('state_code', $request->state);
            }
            
            // Day filter
            if ($request->filled('day') && $request->day != '') {
                $query->where('day_value', $request->day);
            }
            
            // Sort order
            $sortBy = $request->input('sort_by', 'PARTYNAME');
            switch ($sortBy) {
                case 'SALESMAN':
                    $query->orderBy('sales_man_name')->orderBy('name');
                    break;
                case 'AREA':
                    $query->orderBy('area_name')->orderBy('name');
                    break;
                case 'ROUTE':
                    $query->orderBy('route_name')->orderBy('name');
                    break;
                default:
                    $query->orderBy('name');
            }
            
            $reportData = $query->get();
            
            // Get selected columns
            $columns = $request->input('columns', []);

            if ($request->has('print')) {
                return view('admin.reports.other-reports.customer-list-print', compact('reportData', 'columns', 'request'));
            }
        }

        return view('admin.reports.other-reports.customer-list', compact('reportData', 'customers', 'salesmen', 'areas', 'routes', 'states'));
    }
}
