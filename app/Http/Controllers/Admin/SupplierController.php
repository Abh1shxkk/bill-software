<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\PendingOrder;
use App\Models\Item;
use App\Models\PurchaseTransaction;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Helpers\StateHelper;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    use CrudNotificationTrait;
    public function index()
    {
        $searchField = request('search_field', 'all');
        $search = request('search');
        $status = request('status');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $suppliers = Supplier::query()
            ->when($search, function ($query) use ($search, $searchField) {
                if ($searchField === 'all') {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhere('dl_no', 'like', "%{$search}%")
                            ->orWhere('gst_no', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                } else {
                    $query->where($searchField, 'like', "%{$search}%");
                }
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        // Return view for both AJAX and regular requests
        return view('admin.suppliers.index', compact('suppliers', 'searchField', 'search', 'status', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $states = StateHelper::getStates();
        return view('admin.suppliers.create', compact('states'));
    }

    public function store(Request $request)
    {
        // Validate required fields
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|max:255|unique:suppliers,email',
            'telephone' => 'required|string|max:255|unique:suppliers,telephone',
            // Optional fields with unique constraint if provided
            'mobile' => 'nullable|string|max:255|unique:suppliers,mobile',
            'mobile_additional' => 'nullable|string|max:255|unique:suppliers,mobile_additional',
            'code' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:5',
        ]);

        // Prepare data for storage
        $data = $this->prepareSupplierData($request);

        $supplier = Supplier::create($data);
        $this->notifyCreated($supplier->name);
        return redirect()->route('admin.suppliers.index');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $states = StateHelper::getStates();
        return view('admin.suppliers.edit', compact('supplier', 'states'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Basic validation first (without unique checks)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'mobile_additional' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:5',
        ]);

        // Manual unique validation excluding current supplier
        // Skip email validation temporarily due to existing duplicates
        // $emailExists = Supplier::where('email', $request->email)
        //     ->where('supplier_id', '!=', $supplier->supplier_id)
        //     ->exists();
            
        // Skip telephone validation temporarily due to existing duplicates
        // $telephoneExists = Supplier::where('telephone', $request->telephone)
        //     ->where('supplier_id', '!=', $supplier->supplier_id)
        //     ->exists();

        // if ($emailExists) {
        //     return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        // }

        // if ($telephoneExists) {
        //     return back()->withErrors(['telephone' => 'The telephone has already been taken.'])->withInput();
        // }

        // Temporarily disable all unique validations
        // Check mobile if provided
        // if ($request->mobile) {
        //     $mobileExists = Supplier::where('mobile', $request->mobile)
        //         ->where('supplier_id', '!=', $supplier->supplier_id)
        //         ->exists();
        //     if ($mobileExists) {
        //         return back()->withErrors(['mobile' => 'The mobile has already been taken.'])->withInput();
        //     }
        // }

        // Check mobile_additional if provided
        // if ($request->mobile_additional) {
        //     $mobileAdditionalExists = Supplier::where('mobile_additional', $request->mobile_additional)
        //         ->where('supplier_id', '!=', $supplier->supplier_id)
        //         ->exists();
        //     if ($mobileAdditionalExists) {
        //         return back()->withErrors(['mobile_additional' => 'The mobile additional has already been taken.'])->withInput();
        //     }
        // }

        // Prepare data for update
        try {
            $data = $this->prepareSupplierData($request);
            
            // Debug: Log the data being prepared
            \Log::info('Prepared data for supplier update:', $data);
            
            $supplier->update($data);
            $this->notifyUpdated($supplier->name);
            return redirect()->route('admin.suppliers.index');
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Supplier update failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Show error notification
            $this->notifyError('Update failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(Supplier $supplier)
    {
        $supplierName = $supplier->name;
        
        // Check if supplier has related purchase transactions
        $purchaseCount = \App\Models\PurchaseTransaction::where('supplier_id', $supplier->supplier_id)->count();
        
        // Check if supplier has pending orders
        $pendingOrderCount = \App\Models\PendingOrder::where('supplier_id', $supplier->supplier_id)->count();
        
        $relations = [];
        if ($purchaseCount > 0) {
            $relations[] = $purchaseCount . ' purchase transaction(s)';
        }
        if ($pendingOrderCount > 0) {
            $relations[] = $pendingOrderCount . ' pending order(s)';
        }
        
        if (!empty($relations)) {
            $message = 'Cannot delete supplier "' . $supplierName . '". It has ' . implode(' and ', $relations) . ' associated with it.';
            
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            
            $this->notifyError($message);
            return back();
        }
        
        try {
            $supplier->delete();
            
            // For AJAX requests, return JSON success
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier "' . $supplierName . '" deleted successfully.'
                ]);
            }
            
            $this->notifyDeleted($supplierName);
            return back();
        } catch (\Exception $e) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete supplier "' . $supplierName . '". It has related records in the database.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete supplier "' . $supplierName . '". It has related records in the database.');
            return back();
        }
    }

    /**
     * Prepare supplier data from request
     */
    private function prepareSupplierData(Request $request): array
    {
        $data = [
            // Basic Information
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'address' => $request->input('address'),
            'telephone' => $request->input('telephone'),
            'email' => $request->input('email'),
            'fax' => $request->input('fax'),
            'tax_retail_flag' => $request->input('tax_retail_flag', 'T'),
            'status' => $request->input('status'),

            // Contact Information
            'b_day' => $request->input('b_day'),
            'a_day' => $request->input('a_day'),
            'contact_person_1' => $request->input('contact_person_1'),
            'contact_person_2' => $request->input('contact_person_2'),
            'mobile' => $request->input('mobile'),
            'mobile_additional' => $request->input('mobile_additional'),
            'flag' => $request->input('flag'),
            'visit_days' => $request->input('visit_days'),

            // License & Registration
            'dl_no' => $request->input('dl_no'),
            'dl_no_1' => $request->input('dl_no_1'),
            'food_lic' => $request->input('food_lic'),
            'msme_lic' => $request->input('msme_lic'),
            'cst_no' => $request->input('cst_no'),
            'tin_no' => $request->input('tin_no'),
            'gst_no' => $request->input('gst_no'),
            'pan' => $request->input('pan'),
            'tan_no' => $request->input('tan_no'),
            'state_code' => $request->input('state_code'),
            'local_central_flag' => $request->input('local_central_flag', 'L'),
            'full_name' => $request->input('full_name'),

            // Financial Information
            'opening_balance' => $request->input('opening_balance', 0.00),
            'opening_balance_type' => $request->input('opening_balance_type', 'C'),
            'credit_limit' => $request->input('credit_limit', 0.00),
            'invoice_roff' => $this->convertYNToDecimal($request->input('invoice_roff', 'N')),
            'direct_indirect' => $request->input('direct_indirect', 'T'),

            // Bank Details
            'bank' => $request->input('bank'),
            'branch' => $request->input('branch'),
            'account_no' => $request->input('account_no'),
            'ifsc_code' => $request->input('ifsc_code'),

            // Transaction & Scheme Details
            'discount_on_excise' => $this->convertYNToBoolean($request->input('discount_on_excise', 'N')),
            'discount_after_scheme' => $this->convertYNToBoolean($request->input('discount_after_scheme', 'N')),
            'scheme_type' => $request->input('scheme_type'),
            'invoice_on_trade_rate' => $this->convertYNToBoolean($request->input('invoice_on_trade_rate', 'N')),
            'net_rate_yn' => $request->input('net_rate_yn', 'N'), // Store as string Y/N/M
            'scheme_in_decimal' => $this->convertYNToBoolean($request->input('scheme_in_decimal', 'N')),
            'vat_on_bill_expiry' => $this->convertYNToBoolean($request->input('vat_on_bill_expiry', 'N')),
            'tax_on_fqty' => $this->convertYNToBoolean($request->input('tax_on_fqty', 'N')),
            'sale_purchase_status' => $request->input('sale_purchase_status', 'B'),
            'expiry_on_mrp_sale_rate_purchase_rate' => $request->input('expiry_on_mrp_sale_rate_purchase_rate', 'N'), // Store as string Y/N
            'composite_scheme' => $this->convertYNToBoolean($request->input('composite_scheme', 'N')),
            'stock_transfer' => $this->convertYNToBoolean($request->input('stock_transfer', 'N')),
            'cash_purchase' => $this->convertYNToBoolean($request->input('cash_purchase', 'N')),
            'add_charges_with_gst' => $this->convertYNToBoolean($request->input('add_charges_with_gst', 'N')),
            'purchase_import_box_conversion' => $this->convertYNToBoolean($request->input('purchase_import_box_conversion', 'N')),

            // Registration & Compliance
            'aadhar' => $request->input('aadhar'),
            'registration_date' => $request->input('registration_date'),
            'registered_unregistered_composite' => $request->input('registered_unregistered_composite', 'U'),
            'tcs_applicable' => $request->input('tcs_applicable', 'N'), // Store as string Y/N/#
            'tds_yn' => $this->convertYNToBoolean($request->input('tds_yn', 'N')),
            'tds_on_return' => $this->convertYNToBoolean($request->input('tds_on_return', 'N')),
            'tds_tcs_on_bill_amount' => $request->has('tds_tcs_on_bill_amount') ? true : false,

            // Additional Notes
            'notebook' => $request->input('notebook'),
            'remarks' => $request->input('remarks'),
        ];

        return $data;
    }

    /**
     * Convert Y/N values to decimal for database storage
     */
    private function convertYNToDecimal($value): float
    {
        return $value === 'Y' ? 1.00 : 0.00;
    }

    /**
     * Convert Y/N values to boolean for database storage
     */
    private function convertYNToBoolean($value): bool
    {
        return $value === 'Y';
    }

    /**
     * View Pending Orders for a supplier
     */
    public function pendingOrders(Supplier $supplier)
    {
        $orders = PendingOrder::where('supplier_id', $supplier->supplier_id)
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        // Get all items for dropdown with company and stock
        $items = Item::where('is_deleted', 0)
            ->with('company:id,short_name')
            ->select('id', 'bar_code', 'name', 'packing', 'company_id')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                // Calculate total stock from all non-deleted batches
                $item->total_stock = \App\Models\Batch::where('item_id', $item->id)
                    ->where('is_deleted', 0)
                    ->sum('qty');
                $item->company_short_name = $item->company->short_name ?? '---';
                return $item;
            });

        return view('admin.suppliers.pending-orders', compact(
            'supplier',
            'orders',
            'items'
        ));
    }

    /**
     * Store a new pending order for supplier
     */
    public function storePendingOrder(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'order_no' => 'required|string|max:50',
            'item_id' => 'required|exists:items,id',
            'order_date' => 'required|date',
            'balance_qty' => 'required|numeric|min:0',
            'order_qty' => 'required|numeric|min:0',
            'free_qty' => 'required|numeric|min:0',
        ]);

        // Check if this supplier already has an order for this item
        $existingOrder = PendingOrder::where('supplier_id', $supplier->supplier_id)
            ->where('item_id', $validated['item_id'])
            ->first();

        if ($existingOrder) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item already added! Please edit the existing order from Item Pending Orders page to update quantity.'
                ], 422);
            }

            return redirect()->route('admin.suppliers.pending-orders', $supplier)
                ->with('error', 'Item already added! Please edit the existing order from Item Pending Orders page to update quantity.');
        }

        // Check if this item already has orders from OTHER suppliers
        $otherOrders = PendingOrder::where('item_id', $validated['item_id'])
            ->where('supplier_id', '!=', $supplier->supplier_id)
            ->with('supplier')
            ->get();
        
        // If other suppliers have ordered this item and user hasn't confirmed, show confirmation
        if ($otherOrders->isNotEmpty() && $request->ajax() && !$request->input('force_create')) {
            $suppliersList = $otherOrders->map(function($order) {
                return [
                    'name' => $order->supplier->name ?? 'Unknown',
                    'order_no' => $order->order_no ?? 'N/A'
                ];
            })->unique('name')->values();
            
            return response()->json([
                'success' => false,
                'requires_confirmation' => true,
                'message' => 'Order Already Given',
                'suppliers' => $suppliersList
            ], 200);
        }
        
        $otherOrderQty = $otherOrders->sum(function($order) {
            return $order->order_qty + $order->free_qty;
        });

        // Set supplier_id and calculated other_order
        $validated['supplier_id'] = $supplier->supplier_id;
        $validated['other_order'] = $otherOrderQty;

        $order = PendingOrder::create($validated);
        $order->load('item');
        
        // Update other_order for all existing orders of this item (including other suppliers)
        $this->updateOtherOrderQuantities($validated['item_id']);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $totalOrders = PendingOrder::where('supplier_id', $supplier->supplier_id)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Order added successfully',
                'order' => [
                    'id' => $order->id,
                    'index' => $totalOrders,
                    'order_no' => $order->order_no,
                    'item_id' => $order->item_id,
                    'company' => $order->item->company_short_name ?? '---',
                    'balance_qty' => $order->balance_qty,
                    'item_code' => $order->item->bar_code ?? '---',
                    'item_name' => $order->item->name ?? '---',
                    'pack' => $order->item->packing ?? '---',
                    'order_qty' => $order->order_qty,
                    'free_qty' => $order->free_qty,
                    'other_order' => $order->other_order,
                    'order_date' => \Carbon\Carbon::parse($order->order_date)->format('d-M-y'),
                    'order_date_raw' => $order->order_date,
                    'item_cost' => $order->item->cost ?? 0,
                ]
            ]);
        }

        return redirect()->route('admin.suppliers.pending-orders', $supplier)
            ->with('success', 'Order added successfully');
    }

    /**
     * Update pending order
     */
    public function updatePendingOrder(Request $request, Supplier $supplier, PendingOrder $pendingOrder)
    {
        $validated = $request->validate([
            'order_no' => 'required|string|max:50',
            'item_id' => 'required|exists:items,id',
            'order_date' => 'required|date',
            'balance_qty' => 'required|numeric|min:0',
            'order_qty' => 'required|numeric|min:0',
            'free_qty' => 'required|numeric|min:0',
        ]);

        // Update the order
        $pendingOrder->update($validated);
        
        // Recalculate other_order for all orders of this item
        $this->updateOtherOrderQuantities($validated['item_id']);
        
        $pendingOrder->load('item');

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => [
                    'id' => $pendingOrder->id,
                    'order_no' => $pendingOrder->order_no,
                    'item_id' => $pendingOrder->item_id,
                    'company' => $pendingOrder->item->company_short_name ?? '---',
                    'balance_qty' => $pendingOrder->balance_qty,
                    'item_code' => $pendingOrder->item->bar_code ?? '---',
                    'item_name' => $pendingOrder->item->name ?? '---',
                    'pack' => $pendingOrder->item->packing ?? '---',
                    'order_qty' => $pendingOrder->order_qty,
                    'free_qty' => $pendingOrder->free_qty,
                    'other_order' => $pendingOrder->other_order,
                    'order_date' => \Carbon\Carbon::parse($pendingOrder->order_date)->format('d-M-y'),
                    'order_date_raw' => $pendingOrder->order_date,
                    'item_cost' => $pendingOrder->item->cost ?? 0,
                ]
            ]);
        }

        return redirect()->route('admin.suppliers.pending-orders', $supplier)
            ->with('success', 'Order updated successfully');
    }
    
    /**
     * Delete pending order
     */
    public function deletePendingOrder(Request $request, Supplier $supplier, PendingOrder $pendingOrder)
    {
        $itemId = $pendingOrder->item_id;
        $pendingOrder->delete();
        
        // Update other_order for remaining orders of this item
        $this->updateOtherOrderQuantities($itemId);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        }

        return redirect()->route('admin.suppliers.pending-orders', $supplier)
            ->with('success', 'Order deleted successfully');
    }
    
    /**
     * Print pending order by order number
     */
    public function printPendingOrder(Request $request, Supplier $supplier, $orderNo)
    {
        $orders = PendingOrder::where('supplier_id', $supplier->supplier_id)
            ->where('order_no', $orderNo)
            ->with('item')
            ->get();
        
        if ($orders->isEmpty()) {
            return redirect()->route('admin.suppliers.pending-orders', $supplier)
                ->with('error', 'No orders found for this order number.');
        }
        
        $withTotal = $request->query('with_total', '1') === '1';
        
        return view('admin.suppliers.print-pending-order', compact('supplier', 'orders', 'orderNo', 'withTotal'));
    }
    
    /**
     * Update other_order quantities for all orders of a specific item
     * This recalculates the "other suppliers order quantity + free quantity" for each supplier
     */
    private function updateOtherOrderQuantities($itemId)
    {
        // Get all orders for this item grouped by supplier
        $orders = PendingOrder::where('item_id', $itemId)->get();
        
        foreach ($orders as $order) {
            // Calculate total (order_qty + free_qty) from OTHER suppliers for this item
            $otherOrders = PendingOrder::where('item_id', $itemId)
                ->where('supplier_id', '!=', $order->supplier_id)
                ->get();
            
            $otherOrderQty = $otherOrders->sum(function($otherOrder) {
                return $otherOrder->order_qty + $otherOrder->free_qty;
            });
            
            // Update the other_order field
            $order->update(['other_order' => $otherOrderQty]);
        }
    }
    
    /**
     * Get pending orders data for supplier (for purchase transaction)
     */
    public function getPendingOrdersData($supplierId)
    {
        \Log::info('getPendingOrdersData called', ['supplier_id_param' => $supplierId]);
        
        // Find supplier manually to handle any binding issues
        $supplier = Supplier::where('supplier_id', $supplierId)->first();
        
        if (!$supplier) {
            \Log::error('Supplier not found', ['supplier_id' => $supplierId]);
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }
        
        \Log::info('Supplier found', [
            'supplier_id' => $supplier->supplier_id,
            'supplier_name' => $supplier->name
        ]);
        
        $orders = PendingOrder::where('supplier_id', $supplier->supplier_id)
            ->with('item')
            ->orderBy('order_date', 'desc')
            ->get();
        
        \Log::info('Pending orders count', ['count' => $orders->count()]);
        
        $ordersData = $orders->map(function($order) {
            $item = null;
            
            // Try to get item by relationship
            if ($order->item) {
                $item = $order->item;
            } 
            // If relationship fails, try direct query
            else if ($order->item_id) {
                $item = Item::find($order->item_id);
            }
            
            return [
                'order_no' => $order->order_no,
                'item_id' => $order->item_id, // Debug
                'item_code' => $item ? $item->item_code : '---',
                'item_name' => $item ? $item->name : '---', // Changed from item_name to name
                'order_qty' => $order->order_qty ?? 0,
                'free_qty' => $order->free_qty ?? 0,
                'order_date' => $order->order_date ? $order->order_date->format('Y-m-d') : '---',
            ];
        });
        
        return response()->json([
            'success' => true,
            'orders' => $ordersData
        ]);
    }
    
    /**
     * Get items for specific order number
     */
    public function getOrderItems($supplierId, $orderNo)
    {
        \Log::info('getOrderItems called', [
            'supplier_id' => $supplierId,
            'order_no' => $orderNo
        ]);
        
        // Find supplier manually
        $supplier = Supplier::where('supplier_id', $supplierId)->first();
        
        if (!$supplier) {
            \Log::error('Supplier not found in getOrderItems', ['supplier_id' => $supplierId]);
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }
        
        $orders = PendingOrder::where('supplier_id', $supplier->supplier_id)
            ->where('order_no', $orderNo)
            ->with('item')
            ->get();
        
        \Log::info('Order items count', ['count' => $orders->count()]);
        
        $items = $orders->map(function($order) {
            $item = null;
            
            // Try to get item by relationship
            if ($order->item) {
                $item = $order->item;
            } 
            // If relationship fails, try direct query
            else if ($order->item_id) {
                $item = Item::find($order->item_id);
            }
            
            return [
                'item_code' => $item ? $item->id : '', // Use item ID as code
                'item_name' => $item ? $item->name : '',
                'order_qty' => $order->order_qty ?? 0,
                'free_qty' => $order->free_qty ?? 0,
                'balance_qty' => $order->balance_qty ?? 0,
                'pur_rate' => $item ? $item->pur_rate : 0, // Purchase rate from item
                'mrp' => $item ? $item->mrp : 0, // MRP from item
            ];
        });
        
        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Delete multiple suppliers
     */
    public function multipleDelete(Request $request)
    {
        // Debug logging
        \Log::info('Supplier multiple delete request:', [
            'all_data' => $request->all(),
            'supplier_ids' => $request->input('supplier_ids'),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        $request->validate([
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'required|integer|exists:suppliers,supplier_id'
        ]);

        try {
            $supplierIds = $request->supplier_ids;
            $suppliers = Supplier::whereIn('supplier_id', $supplierIds)->get();
            
            if ($suppliers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No suppliers found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $skippedSuppliers = [];

            foreach ($suppliers as $supplier) {
                // Check if supplier has related purchase transactions
                $purchaseCount = \App\Models\PurchaseTransaction::where('supplier_id', $supplier->supplier_id)->count();
                
                // Check if supplier has pending orders
                $pendingOrderCount = \App\Models\PendingOrder::where('supplier_id', $supplier->supplier_id)->count();
                
                $relations = [];
                if ($purchaseCount > 0) {
                    $relations[] = $purchaseCount . ' purchase transaction(s)';
                }
                if ($pendingOrderCount > 0) {
                    $relations[] = $pendingOrderCount . ' pending order(s)';
                }
                
                if (!empty($relations)) {
                    $skippedSuppliers[] = $supplier->name . ' (has ' . implode(' and ', $relations) . ')';
                    continue;
                }
                
                $supplier->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                $message = $deletedCount === 1 
                    ? "1 supplier deleted successfully"
                    : "{$deletedCount} suppliers deleted successfully";
                
                if (!empty($skippedSuppliers)) {
                    $message .= ". However, " . count($skippedSuppliers) . " suppliers were skipped due to existing relations.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'skipped_items' => $skippedSuppliers
                ]);
            }
            
            if (!empty($skippedSuppliers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete selected suppliers. They have existing relations: ' . implode(', ', array_slice($skippedSuppliers, 0, 3)) . (count($skippedSuppliers) > 3 ? ' and ' . (count($skippedSuppliers) - 3) . ' more' : '')
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'No suppliers were deleted.'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Multiple supplier deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete suppliers. Please try again.'
            ], 500);
        }
    }

    /**
     * Display supplier ledger
     */
    public function ledger(Supplier $supplier, Request $request)
    {
        // Get date range from request or use defaults (1st Jan to current date)
        $fromDate = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');

        // Calculate opening balance (from 1st Jan to day before from_date)
        $yearStart = now()->startOfYear()->format('Y-m-d');
        $dayBeforeFromDate = \Carbon\Carbon::parse($fromDate)->subDay()->format('Y-m-d');
        
        $openingBalance = $supplier->opening_balance ?? 0;
        
        // If from_date is not 1st Jan, calculate opening balance
        if ($fromDate > $yearStart) {
            $openingTransactions = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
                ->whereBetween('bill_date', [$yearStart, $dayBeforeFromDate])
                ->get();
            
            foreach ($openingTransactions as $transaction) {
                // Purchase increases liability (add to opening)
                $openingBalance += $transaction->net_amount;
                
                // If paid in cash, reduce liability (subtract from opening)
                if ($transaction->cash_flag === 'Y') {
                    $openingBalance -= $transaction->net_amount;
                }
            }
            
            // Credit Notes for supplier (Debit - reduces liability)
            $creditNoteTotal = CreditNote::where('credit_party_type', 'S')
                ->where('credit_party_id', $supplier->supplier_id)
                ->whereBetween('credit_note_date', [$yearStart, $dayBeforeFromDate])
                ->sum('cn_amount');
            $openingBalance -= $creditNoteTotal;
            
            // Debit Notes for supplier (Debit - reduces liability)
            $debitNoteTotal = DebitNote::where('debit_party_type', 'S')
                ->where('debit_party_id', $supplier->supplier_id)
                ->whereBetween('debit_note_date', [$yearStart, $dayBeforeFromDate])
                ->sum('dn_amount');
            $openingBalance -= $debitNoteTotal;
        }

        // Fetch purchase transactions for selected date range
        $transactions = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->orderBy('bill_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Transform purchase transactions into ledger entries
        $ledgers = collect();
        
        foreach ($transactions as $transaction) {
            // Add PURCHASE entry
            $ledgers->push((object)[
                'id' => $transaction->id . '-purchase',
                'trans_no' => 'PB / ' . $transaction->trn_no,
                'transaction_date' => $transaction->bill_date,
                'account_name' => 'PURCHASE',
                'transaction_type' => 'Purchase',
                'amount' => $transaction->net_amount,
                'debit_credit' => 'credit', // Purchase increases supplier liability (credit)
                'remarks' => $transaction->bill_no,
                'purchase_transaction_id' => $transaction->id,
            ]);

            // If cash_flag is 'Y', add CASH BOOK entry
            if ($transaction->cash_flag === 'Y') {
                $ledgers->push((object)[
                    'id' => $transaction->id . '-cash',
                    'trans_no' => 'PB / ' . $transaction->trn_no,
                    'transaction_date' => $transaction->bill_date,
                    'account_name' => 'CASH BOOK',
                    'transaction_type' => 'Payment',
                    'amount' => $transaction->net_amount,
                    'debit_credit' => 'debit', // Payment reduces supplier liability (debit)
                    'remarks' => $transaction->bill_no,
                    'purchase_transaction_id' => $transaction->id,
                ]);
            }
        }

        // Fetch Credit Notes for supplier (Debit - reduces liability)
        $creditNotes = CreditNote::where('credit_party_type', 'S')
            ->where('credit_party_id', $supplier->supplier_id)
            ->whereBetween('credit_note_date', [$fromDate, $toDate])
            ->orderBy('credit_note_date', 'asc')
            ->get();

        foreach ($creditNotes as $cn) {
            $ledgers->push((object)[
                'id' => 'cn-' . $cn->id,
                'trans_no' => 'CN / ' . $cn->credit_note_no,
                'transaction_date' => $cn->credit_note_date,
                'account_name' => 'CREDIT NOTE',
                'transaction_type' => 'Credit Note',
                'amount' => $cn->cn_amount,
                'debit_credit' => 'debit', // Credit Note reduces supplier liability (debit)
                'remarks' => $cn->narration ?? '',
                'credit_note_id' => $cn->id,
            ]);
        }

        // Fetch Debit Notes for supplier (Debit - reduces liability/what we owe)
        $debitNotes = DebitNote::where('debit_party_type', 'S')
            ->where('debit_party_id', $supplier->supplier_id)
            ->whereBetween('debit_note_date', [$fromDate, $toDate])
            ->orderBy('debit_note_date', 'asc')
            ->get();

        foreach ($debitNotes as $dn) {
            $ledgers->push((object)[
                'id' => 'dn-' . $dn->id,
                'trans_no' => 'DN / ' . $dn->debit_note_no,
                'transaction_date' => $dn->debit_note_date,
                'account_name' => 'DEBIT NOTE',
                'transaction_type' => 'Debit Note',
                'amount' => $dn->dn_amount,
                'debit_credit' => 'debit', // Debit Note reduces supplier liability (debit)
                'remarks' => $dn->narration ?? '',
                'debit_note_id' => $dn->id,
            ]);
        }

        // Sort all entries by date
        $ledgers = $ledgers->sortBy('transaction_date')->values();

        // Calculate totals for selected period
        $totalDebit = $ledgers->where('debit_credit', 'debit')->sum('amount');
        $totalCredit = $ledgers->where('debit_credit', 'credit')->sum('amount');

        // Calculate running balance for each entry BEFORE pagination
        $runningBalance = $openingBalance;
        $ledgers = $ledgers->map(function($ledger) use (&$runningBalance) {
            if ($ledger->debit_credit === 'credit') {
                $runningBalance += $ledger->amount;
            } else {
                $runningBalance -= $ledger->amount;
            }
            $ledger->running_balance = $runningBalance;
            return $ledger;
        });

        // Paginate ledgers manually
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $ledgersCollection = $ledgers->slice(($currentPage - 1) * $perPage, $perPage);
        
        $paginatedLedgers = new \Illuminate\Pagination\LengthAwarePaginator(
            $ledgersCollection,
            $ledgers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.suppliers.ledger', [
            'supplier' => $supplier,
            'ledgers' => $paginatedLedgers,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'type' => $type,
            'openingBalance' => $openingBalance,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }

    /**
     * Display supplier dues
     */
    public function dues(Supplier $supplier)
    {
        // Get all unpaid purchases (where cash_flag = 'N')
        $dues = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
            ->where('cash_flag', 'N')
            ->orderBy('bill_date', 'desc')
            ->paginate(10);

        $totalDue = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
            ->where('cash_flag', 'N')
            ->sum('net_amount');

        return view('admin.suppliers.dues', compact('supplier', 'dues', 'totalDue'));
    }

    public function bills(Request $request, Supplier $supplier)
    {
        // Get date filters
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());

        // Get all purchases with filters
        $query = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
            ->with(['items.item', 'supplier'])
            ->whereBetween('bill_date', [$fromDate, $toDate]);

        $bills = $query->orderBy('bill_date', 'desc')
            ->paginate(10);

        // Calculate total amount for filtered bills only
        $totalAmount = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->sum('net_amount');

        // AJAX request for load more
        if ($request->ajax()) {
            $html = view('admin.suppliers.partials.bills-table-rows', compact('bills'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'has_more' => $bills->hasMorePages(),
                'next_page' => $bills->currentPage() + 1
            ]);
        }

        // AJAX request for filter
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'bills' => $bills->items(),
                'total' => $bills->total(),
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'total_amount' => $totalAmount
            ]);
        }

        return view('admin.suppliers.bills', compact('supplier', 'bills', 'totalAmount', 'fromDate', 'toDate'));
    }
}