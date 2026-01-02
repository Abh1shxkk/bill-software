<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    use CrudNotificationTrait;
    public function index()
    {
        $search = request('search');
        $searchField = request('search_field', 'all');
        $status = request('status'); // 'active' | 'inactive'
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $customers = Customer::query()
            ->when($search && trim($search) !== '', function ($query) use ($search, $searchField) {
                $search = trim($search);
                
                if ($searchField === 'all') {
                    // Search across all fields
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%")
                          ->orWhere('telephone_office', 'like', "%{$search}%")
                          ->orWhere('address', 'like', "%{$search}%")
                          ->orWhere('dl_number', 'like', "%{$search}%")
                          ->orWhere('gst_name', 'like', "%{$search}%")
                          ->orWhere('city', 'like', "%{$search}%");
                    });
                } else {
                    // Search in specific field
                    $validFields = ['name', 'code', 'mobile', 'telephone_office', 'address', 'dl_number', 'gst_name'];
                    if (in_array($searchField, $validFields)) {
                        $query->where($searchField, 'like', "%{$search}%");
                    }
                }
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status === 'active' ? 1 : 0);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByDesc('created_date')
            ->paginate(10)
            ->withQueryString();

        // AJAX request handling
        if (request()->ajax()) {
            return view('admin.customers.index', compact('customers', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
        }

        return view('admin.customers.index', compact('customers', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        // Fetch countries from API
        try {
            $apiKey = env('COUNTRY_STATE_CITY_API_KEY');
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey
            ])->get('https://api.countrystatecity.in/v1/countries');

            $countries = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $countries = [];
        }

        return view('admin.customers.create', compact('countries'));
    }

    public function store(Request $request)
    {
        // Validate required fields only
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'gst_name' => 'nullable|string|max:255',
        ]);

        // Prepare data for insertion
        $data = $request->except(['_token', '_method']);
        
        // Set timestamps
        $data['created_date'] = now();
        $data['modified_date'] = now();
        
        $customer = Customer::create($data);
        $this->notifyCreated($customer->name);
        return redirect()->route('admin.customers.index');
    }

    public function show(Customer $customer)
    {
        if (request()->ajax()) {
            return response()->json($customer);
        }
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        // Fetch countries from API
        try {
            $apiKey = env('COUNTRY_STATE_CITY_API_KEY');
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey
            ])->get('https://api.countrystatecity.in/v1/countries');

            $countries = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $countries = [];
        }

        return view('admin.customers.edit', compact('customer', 'countries'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Prepare data for update
        $data = $request->except(['_token', '_method']);
        
        // Update timestamp
        $data['modified_date'] = now();
        
        $customer->update($data);
        $this->notifyUpdated($customer->name);
        return redirect()->route('admin.customers.index');
    }

    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        
        // Check if customer has related sale transactions
        $saleCount = \App\Models\SaleTransaction::where('customer_id', $customer->id)->count();
        
        if ($saleCount > 0) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer "' . $customerName . '". It has ' . $saleCount . ' sale transaction(s) associated with it.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete customer "' . $customerName . '". It has ' . $saleCount . ' sale transaction(s) associated with it.');
            return back();
        }
        
        try {
            $customer->delete();
            
            // For AJAX requests, return JSON success
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer "' . $customerName . '" deleted successfully.'
                ]);
            }
            
            $this->notifyDeleted($customerName);
            return back();
        } catch (\Exception $e) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer "' . $customerName . '". It has related records in the database.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete customer "' . $customerName . '". It has related records in the database.');
            return back();
        }
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'tax_registration' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'telephone_office' => 'nullable|string|max:255',
            'telephone_residence' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'contact_person1' => 'nullable|string|max:255',
            'mobile_contact1' => 'nullable|string|max:255',
            'contact_person2' => 'nullable|string|max:255',
            'mobile_contact2' => 'nullable|string|max:255',
            'fax_number' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'balance_type' => 'nullable|string|max:255',
            'local_central' => 'nullable|string|max:255',
            'credit_days' => 'nullable|integer',
            'birth_day' => 'nullable|date',
            'status' => 'nullable|boolean',
            'flag' => 'nullable|string|max:255',
            'invoice_export' => 'nullable|boolean',
            'due_list_sequence' => 'nullable|string|max:255',
            'tan_number' => 'nullable|string|max:255',
            'msme_license' => 'nullable|string|max:255',
            'dl_number' => 'nullable|string|max:255',
            'dl_expiry' => 'nullable|date',
            'dl_number1' => 'nullable|string|max:255',
            'food_license' => 'nullable|string|max:255',
            'cst_number' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'sales_man_code' => 'nullable|string|max:255',
            'area_code' => 'nullable|string|max:255',
            'route_code' => 'nullable|string|max:255',
            'state_code' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order_required' => 'nullable|boolean',
            'aadhar_number' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'day_value' => 'nullable|integer',
            'cst_registration' => 'nullable|string|max:255',
            'gst_name' => 'nullable|string|max:255',
            'state_code_gst' => 'nullable|string|max:255',
            'registration_status' => 'nullable|string|max:255',
        ];
    }

    // API methods for country, state, city dropdowns
    public function getCountries()
    {
        try {
            $apiKey = env('COUNTRY_STATE_CITY_API_KEY');
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey
            ])->get('https://api.countrystatecity.in/v1/countries');

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch countries'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getStates($countryCode)
    {
        try {
            $apiKey = env('COUNTRY_STATE_CITY_API_KEY');
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey
            ])->get("https://api.countrystatecity.in/v1/countries/{$countryCode}/states");

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch states'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCities($countryCode, $stateCode)
    {
        try {
            $apiKey = env('COUNTRY_STATE_CITY_API_KEY');
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey
            ])->get("https://api.countrystatecity.in/v1/countries/{$countryCode}/states/{$stateCode}/cities");

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch cities'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete multiple customers
     */
    public function multipleDelete(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'required|integer|exists:customers,id'
        ]);

        try {
            $customerIds = $request->customer_ids;
            $customers = Customer::whereIn('id', $customerIds)->get();
            
            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customers found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $skippedCustomers = [];

            foreach ($customers as $customer) {
                // Check if customer has related sale transactions
                $saleCount = \App\Models\SaleTransaction::where('customer_id', $customer->id)->count();
                
                if ($saleCount > 0) {
                    $skippedCustomers[] = $customer->name . ' (has ' . $saleCount . ' sale transactions)';
                    continue;
                }
                
                $customer->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                $message = $deletedCount === 1 
                    ? "1 customer deleted successfully"
                    : "{$deletedCount} customers deleted successfully";
                
                if (!empty($skippedCustomers)) {
                    $message .= ". However, " . count($skippedCustomers) . " customers were skipped due to existing relations.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'skipped_items' => $skippedCustomers
                ]);
            }
            
            if (!empty($skippedCustomers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete selected customers. They have existing relations: ' . implode(', ', array_slice($skippedCustomers, 0, 3)) . (count($skippedCustomers) > 3 ? ' and ' . (count($skippedCustomers) - 3) . ' more' : '')
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'No customers were deleted.'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Multiple customer deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customers. Please try again.'
            ], 500);
        }
    }

    /**
     * Get customer's outstanding sales for credit note adjustment
     */
    public function getSales(Customer $customer)
    {
        try {
            // Get all sale transactions for this customer (for adjustment purposes)
            $sales = \App\Models\SaleTransaction::where('customer_id', $customer->id)
                ->select([
                    'id',
                    'series',
                    'invoice_no',
                    'sale_date as transaction_date',
                    'net_amount as bill_amount',
                    'balance_amount as balance'
                ])
                ->orderBy('sale_date', 'desc')
                ->limit(50) // Limit to recent 50 transactions
                ->get()
                ->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'trans_no' => ($sale->series ?? 'S') . ' / ' . ($sale->invoice_no ?? '---'),
                        'date' => $sale->transaction_date ? \Carbon\Carbon::parse($sale->transaction_date)->format('d-M-y') : '---',
                        'bill_amount' => $sale->bill_amount ?? 0,
                        'balance' => $sale->balance ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'sales' => $sales,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'code' => $customer->code ?? $customer->id,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching customer sales: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer sales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display customer's PENDING sale challans (not invoiced yet)
     */
    public function challans(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $query = \App\Models\SaleChallanTransaction::with(['salesman'])
            ->where('customer_id', $id)
            ->where('is_invoiced', false) // Only show pending challans
            ->orderBy('challan_date', 'desc')
            ->orderBy('challan_no', 'desc');
        
        // Search filter
        if ($request->filled('search')) {
            $query->where('challan_no', 'like', '%' . $request->input('search') . '%');
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('challan_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('challan_date', '<=', $request->input('date_to'));
        }
        
        // Amount filter
        if ($request->filled('amount_min')) {
            $query->where('net_amount', '>=', floatval($request->input('amount_min')));
        }
        
        $challans = $query->paginate(10);
        
        // Calculate summary - focus on pending challans
        $allChallansQuery = \App\Models\SaleChallanTransaction::where('customer_id', $id);
        $pendingCount = (clone $allChallansQuery)->where('is_invoiced', false)->count();
        $invoicedCount = (clone $allChallansQuery)->where('is_invoiced', true)->count();
        // Total amount of PENDING challans only
        $totalAmount = (clone $allChallansQuery)->where('is_invoiced', false)->sum('net_amount');
        
        return view('admin.customers.challans', compact(
            'customer', 
            'challans', 
            'pendingCount', 
            'invoicedCount', 
            'totalAmount'
        ));
    }

    /**
     * Get all customers for dropdown/autocomplete
     */
    public function getAllCustomers()
    {
        try {
            $customers = Customer::select('id as customer_id', 'name', 'code', 'mobile')
                ->where('status', 1) // Only active customers
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customer by code (AJAX)
     */
    public function search(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return response()->json(['name' => '']);
        }

        $customer = Customer::where('code', $code)
            ->where('is_deleted', '!=', 1)
            ->first();

        return response()->json([
            'id' => $customer->id ?? null,
            'name' => $customer->name ?? '',
            'code' => $customer->code ?? ''
        ]);
    }
}