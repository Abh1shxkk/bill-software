<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use CrudNotificationTrait;
    public function index()
    {
        $search = request('search');
        $searchField = request('search_field', 'all');
        $status = request('status');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        // Debug logging
        \Log::info('Company search', [
            'search' => $search,
            'search_field' => $searchField
        ]);

        $companies = Company::query()
            ->when($search && trim($search) !== '', function ($query) use ($search, $searchField) {
                $search = trim($search);
                
                if ($searchField === 'all') {
                    // Search across all fields
                    $query->where(function ($q) use ($search) {
                        $q->where('alter_code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile_1', 'like', "%{$search}%")
                            ->orWhere('mobile_2', 'like', "%{$search}%")
                            ->orWhere('contact_person_1', 'like', "%{$search}%")
                            ->orWhere('contact_person_2', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
                } elseif ($searchField === 'mobile') {
                    // Search in both mobile fields
                    $query->where(function ($q) use ($search) {
                        $q->where('mobile_1', 'like', "%{$search}%")
                            ->orWhere('mobile_2', 'like', "%{$search}%");
                    });
                } else {
                    // Search in specific field - ensure field name is valid
                    $validFields = ['alter_code', 'name', 'telephone', 'address'];
                    if (in_array($searchField, $validFields)) {
                        $query->where($searchField, 'like', "%{$search}%");
                    }
                }
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->whereRaw('LOWER(status) = ?', [strtolower($status)]);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // AJAX request ke liye sirf table return karo
        if (request()->ajax()) {
            return view('admin.companies.index', compact('companies', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
        }

        return view('admin.companies.index', compact('companies', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        // Validate required fields
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies,email',
            'address' => 'required|string',
            // gst_number removed
            'telephone' => 'nullable|string|max:255|unique:companies,telephone',
            'mobile_1' => 'nullable|string|max:255|unique:companies,mobile_1',
            'mobile_2' => 'nullable|string|max:255|unique:companies,mobile_2',
            'generic' => 'nullable|in:y,n,Y,N',
            'expiry' => 'nullable|in:y,n,Y,N',
            'lock_aiocd' => 'nullable|in:y,n,Y,N',
            'notes' => 'nullable|string',
            'lock_ims' => 'nullable|in:y,n,Y,N',
            'surcharge_after_dis_yn' => 'nullable|in:y,n,Y,N',
            'add_surcharge_yn' => 'nullable|in:y,n,Y,N',
            'inclusive_yn' => 'nullable|in:y,n,Y,N',
            'direct_indirect' => 'nullable|in:d,i,D,I',
            'fixed_maximum' => 'nullable|in:f,m,F,M',
            'status' => 'nullable|string|max:5',
        ]);

        // Get all data and merge validated fields
        $data = array_merge($request->all(), $validated);
        // status becomes string (max 5)
        $data['status'] = $request->input('status');
        // Map Y/N inputs to booleans for boolean columns
        $data['surcharge_after_dis_yn'] = strtolower($request->input('surcharge_after_dis_yn', 'n')) === 'y';
        $data['add_surcharge_yn'] = strtolower($request->input('add_surcharge_yn', 'n')) === 'y';
        $data['inclusive_yn'] = strtolower($request->input('inclusive_yn', 'n')) === 'y';
        // fixed_maximum becomes char f/m
        $data['fixed_maximum'] = strtolower($request->input('fixed_maximum', 'f'));
        
        // Convert y/n fields to lowercase
        $data['generic'] = strtolower($request->input('generic', 'n'));
        $data['expiry'] = strtolower($request->input('expiry', 'n'));
        $data['lock_aiocd'] = strtolower($request->input('lock_aiocd', 'n'));
        $data['lock_ims'] = strtolower($request->input('lock_ims', 'n'));
        $data['direct_indirect'] = strtolower($request->input('direct_indirect', 'd'));
        // Lock discount to 0
        $data['discount'] = 0;
        
        $company = Company::create($data);
        $this->notifyCreated($company->name);
        return redirect()->route('admin.companies.index');
    }

    public function show(Company $company)
    {
        if (request()->ajax()) {
            return response()->json($company);
        }
        return view('admin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        // Validate y/n fields
        $validated = $request->validate([
            'generic' => 'nullable|in:y,n,Y,N',
            'expiry' => 'nullable|in:y,n,Y,N',
            'lock_aiocd' => 'nullable|in:y,n,Y,N',
            'lock_ims' => 'nullable|in:y,n,Y,N',
            'surcharge_after_dis_yn' => 'nullable|in:y,n,Y,N',
            'add_surcharge_yn' => 'nullable|in:y,n,Y,N',
            'inclusive_yn' => 'nullable|in:y,n,Y,N',
            'notes' => 'nullable|string',
            'direct_indirect' => 'nullable|in:d,i,D,I',
            'fixed_maximum' => 'nullable|in:f,m,F,M',
            'status' => 'nullable|string|max:5',
        ]);
        
        $data = $request->all();
        // status becomes string (max 5)
        $data['status'] = $request->input('status');
        // Map Y/N inputs to booleans for boolean columns
        $data['surcharge_after_dis_yn'] = strtolower($request->input('surcharge_after_dis_yn', 'n')) === 'y';
        $data['add_surcharge_yn'] = strtolower($request->input('add_surcharge_yn', 'n')) === 'y';
        $data['inclusive_yn'] = strtolower($request->input('inclusive_yn', 'n')) === 'y';
        // fixed_maximum becomes char f/m
        $data['fixed_maximum'] = strtolower($request->input('fixed_maximum', 'f'));
        
        // Convert y/n fields to lowercase
        $data['generic'] = strtolower($request->input('generic', 'n'));
        $data['expiry'] = strtolower($request->input('expiry', 'n'));
        $data['lock_aiocd'] = strtolower($request->input('lock_aiocd', 'n'));
        $data['lock_ims'] = strtolower($request->input('lock_ims', 'n'));
        $data['direct_indirect'] = strtolower($request->input('direct_indirect', 'd'));
        // Lock discount to 0
        $data['discount'] = 0;
        
        $company->update($data);
        $this->notifyUpdated($company->name);
        return redirect()->route('admin.companies.index');
    }

    public function destroy(Company $company)
    {
        $companyName = $company->name;
        
        // Check if company has related items
        $itemCount = \App\Models\Item::where('company_id', $company->id)
            ->where('is_deleted', '!=', 1)
            ->count();
        
        if ($itemCount > 0) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete company "' . $companyName . '". It has ' . $itemCount . ' item(s) associated with it.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete company "' . $companyName . '". It has ' . $itemCount . ' item(s) associated with it.');
            return back();
        }
        
        try {
            $company->delete();
            
            // For AJAX requests, return JSON success
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company "' . $companyName . '" deleted successfully.'
                ]);
            }
            
            $this->notifyDeleted($companyName);
            return back();
        } catch (\Exception $e) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete company "' . $companyName . '". It has related records in the database.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete company "' . $companyName . '". It has related records in the database.');
            return back();
        }
    }

    // Local validation rules helper
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'contact_person_1' => 'nullable|string|max:255',
            'contact_person_2' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'alter_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'telephone' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:255',
            'pur_sc' => 'nullable|string|max:255',
            'sale_sc' => 'nullable|string|max:255',
            'expiry' => 'nullable|string|max:255',
            'dis_on_sale_percent' => 'nullable|numeric',
            'min_gp' => 'nullable|numeric',
            'pur_tax' => 'nullable|numeric',
            'sale_tax' => 'nullable|numeric',
            // gst_number removed
            'generic' => 'nullable|string|max:255',
            'invoice_print_order' => 'nullable|string|max:255',
            'direct_indirect' => 'nullable|in:d,i,D,I',
            'surcharge_after_dis_yn' => 'nullable|in:y,n,Y,N',
            'add_surcharge_yn' => 'nullable|in:y,n,Y,N',
            'vat_percent' => 'nullable|numeric',
            'inclusive_yn' => 'nullable|in:y,n,Y,N',
            'disallow_expiry_after_months' => 'nullable|integer',
            'fixed_maximum' => 'nullable|in:f,m,F,M',
            'discount' => 'nullable|numeric',
            'flag' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:5',
            'country_code' => 'nullable|string|max:10',
            'country_name' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|max:10',
            'state_name' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get all companies for dropdown
     */
    public function getAll()
    {
        try {
            $companies = Company::select('id', 'name', 'short_name', 'alter_code')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'companies' => $companies
            ]);
        } catch (\Exception $e) {
            \Log::error('Get all companies error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading companies'
            ], 500);
        }
    }

    /**
     * Get company by code (alter_code or id)
     * Used for AJAX lookup in Additional Details modal
     */
    public function getByCode($code)
    {
        try {
            // Try to find by alter_code first, then by id, then by short_name
            $company = Company::where('alter_code', $code)
                ->orWhere('id', $code)
                ->orWhere('short_name', $code)
                ->first();
            
            if ($company) {
                return response()->json([
                    'success' => true,
                    'company' => [
                        'id' => $company->id,
                        'name' => $company->name,
                        'short_name' => $company->short_name,
                        'alter_code' => $company->alter_code,
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Company lookup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error looking up company'
            ], 500);
        }
    }

    /**
     * Delete multiple companies
     */
    public function multipleDelete(Request $request)
    {
        $request->validate([
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'required|integer|exists:companies,id'
        ]);

        try {
            $companyIds = $request->company_ids;
            $companies = Company::whereIn('id', $companyIds)->get();
            
            if ($companies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No companies found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $skippedCompanies = [];

            foreach ($companies as $company) {
                // Check if company has related items
                $itemCount = \App\Models\Item::where('company_id', $company->id)
                    ->where('is_deleted', '!=', 1)
                    ->count();
                
                if ($itemCount > 0) {
                    $skippedCompanies[] = $company->name . ' (has ' . $itemCount . ' items)';
                    continue;
                }
                
                $company->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                $message = $deletedCount === 1 
                    ? "1 company deleted successfully"
                    : "{$deletedCount} companies deleted successfully";
                
                if (!empty($skippedCompanies)) {
                    $message .= ". However, " . count($skippedCompanies) . " companies were skipped due to existing relations.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'skipped_items' => $skippedCompanies
                ]);
            }
            
            if (!empty($skippedCompanies)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete selected companies. They have existing relations: ' . implode(', ', array_slice($skippedCompanies, 0, 3)) . (count($skippedCompanies) > 3 ? ' and ' . (count($skippedCompanies) - 3) . ' more' : '')
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'No companies were deleted.'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Multiple company deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete companies. Please try again.'
            ], 500);
        }
    }

    /**
     * Search company by code (AJAX)
     */
    public function search(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return response()->json(['name' => '']);
        }

        $company = Company::where('code', $code)
            ->where('is_deleted', '!=', 1)
            ->first();

        return response()->json([
            'id' => $company->id ?? null,
            'name' => $company->name ?? '',
            'code' => $company->code ?? ''
        ]);
    }
}
