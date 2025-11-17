<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\PurchaseChallanTransaction;
use App\Models\PurchaseChallanTransactionItem;
use App\Models\Batch;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SalesMan;
use App\Models\PendingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = PurchaseTransaction::with('supplier')
            ->orderBy('bill_date', 'desc')
            ->paginate(20);
        
        return view('admin.purchase.transactions.index', compact('transactions'));
    }

    /**
     * Display purchase transaction form (alias for create)
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextTrnNo = $this->generateTrnNo();
        
        return view('admin.purchase.transaction', compact('suppliers', 'salesmen', 'items', 'nextTrnNo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextTrnNo = $this->generateTrnNo();
        
        return view('admin.purchase.transaction', compact('suppliers', 'salesmen', 'items', 'nextTrnNo'));
    }

    /**
     * Display purchase modification form
     */
    public function modification($trn_no = null)
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        
        // If trn_no is provided, we can pre-populate the form
        $preloadTrnNo = $trn_no;
        
        return view('admin.purchase.modification', compact('suppliers', 'salesmen', 'items', 'preloadTrnNo'));
    }

    /**
     * Display purchase invoices listing page
     */
    public function invoices(Request $request)
    {
        $query = PurchaseTransaction::with('supplier');

        // Apply search filter
        if ($request->filled('search')) {
            $filterBy = $request->get('filter_by', 'supplier_name');
            $searchTerm = $request->get('search');

            switch ($filterBy) {
                case 'supplier_name':
                    $query->whereHas('supplier', function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    });
                    break;
                    
                case 'bill_no':
                    $query->where('bill_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'invoice_amount':
                    if (is_numeric($searchTerm)) {
                        $query->where('inv_amount', '>=', $searchTerm);
                    }
                    break;
                    
                case 'bill_date':
                    $query->whereDate('bill_date', 'LIKE', '%' . $searchTerm . '%');
                    break;
            }
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('bill_date', [
                $request->get('date_from'),
                $request->get('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('bill_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('bill_date', '<=', $request->get('date_to'));
        }

        // Order by latest first
        $query->orderBy('bill_date', 'desc')->orderBy('id', 'desc');

        // Get paginated results (10 per page for Load More)
        $purchases = $query->paginate(10)->withQueryString();

        // When AJAX, return the full view
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.purchase.invoices', compact('purchases'));
        }

        return view('admin.purchase.invoices', compact('purchases'));
    }

    /**
     * Debug method to check purchase data
     */
    public function debugPurchases()
    {
        $totalCount = PurchaseTransaction::count();
        $samplePurchases = PurchaseTransaction::with('supplier')->take(5)->get();
        
        $debug = [
            'total_count' => $totalCount,
            'sample_purchases' => $samplePurchases->toArray(),
            'table_exists' => \Schema::hasTable('purchase_transactions'),
            'columns' => \Schema::getColumnListing('purchase_transactions')
        ];
        
        return response()->json($debug);
    }

    /**
     * Get invoice list for modification modal
     */
    public function getInvoiceList()
    {
        try {
            $invoices = PurchaseTransaction::with('supplier')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'trn_no' => $transaction->trn_no,
                        'bill_no' => $transaction->bill_no,
                        'bill_date' => $transaction->bill_date,
                        'receive_date' => $transaction->receive_date,
                        'supplier_name' => $transaction->supplier->name ?? 'N/A',
                        'net_amount' => $transaction->net_amount,
                        'created_by' => 'MASTER',
                        'modified_by' => 'MASTER',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching invoice list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice list'
            ], 500);
        }
    }

    /**
     * Fetch bill by transaction number or bill number for modification
     */
    public function fetchBill($identifier)
    {
        try {
            // Try to find by trn_no first, then by bill_no
            $transaction = PurchaseTransaction::with(['supplier', 'items'])
                ->where(function($query) use ($identifier) {
                    $query->where('trn_no', $identifier)
                          ->orWhere('bill_no', $identifier);
                })
                ->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found with Transaction No or Bill No: ' . $identifier
                ]);
            }
            
            // Format bill data
            $billData = [
                'transaction_id' => $transaction->id, // Add transaction ID for update
                'trn_no' => $transaction->trn_no,
                'bill_no' => $transaction->bill_no,
                'bill_date' => $transaction->bill_date ? $transaction->bill_date->format('Y-m-d') : null,
                'receive_date' => $transaction->receive_date ? $transaction->receive_date->format('Y-m-d') : null,
                'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                'supplier_id' => (string)($transaction->supplier_id ?? ''),
                'supplier_name' => $transaction->supplier->name ?? '',
                'cash' => strtoupper($transaction->cash_flag ?? 'N'),
                'transfer' => strtoupper($transaction->transfer_flag ?? 'N'),
                'remarks' => $transaction->remarks ?? '',
                
                // Summary amounts
                'nt_amt' => $transaction->nt_amount,
                'sc_amt' => $transaction->sc_amount,
                'scm_amt' => $transaction->scm_amount,
                'dis_amt' => $transaction->dis_amount,
                'less_amt' => $transaction->less_amount,
                'tax_amt' => $transaction->tax_amount,
                'net_amt' => $transaction->net_amount,
                'scm_percent' => $transaction->scm_percent,
                'tcs_amt' => $transaction->tcs_amount,
                'dis1_amt' => $transaction->dis1_amount,
                'tof_amt' => $transaction->tof_amount,
                'inv_amt' => $transaction->inv_amount,
                
                // Detailed info
                'unit' => '1',
                'location' => '',
                'cost' => 0,
                'cost_gst' => 0,
                'cl_qty' => '',
                'company' => '',
                'volume' => 0,
                'packing' => '',
                'hs_amt' => 0,
                'gross_amt' => $transaction->nt_amount,
                'dis1_percent' => 0,
                
                // Items
                'items' => $transaction->items->map(function($item) {
                    return [
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'batch_number' => $item->batch_no,
                        'expiry_date' => $item->expiry_date,
                        'quantity' => $item->qty,
                        'free_quantity' => $item->free_qty,
                        'p_rate' => $item->pur_rate,
                        'discount_percent' => $item->dis_percent,
                        'mrp' => $item->mrp,
                        's_rate' => $item->s_rate ?? 0,
                        'amount' => $item->amount,
                    ];
                })->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'bill' => $billData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching bill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bill: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier name by supplier_id (for AJAX requests)
     */
    public function getSupplierName($supplierId)
    {
        try {
            $supplier = Supplier::where('supplier_id', $supplierId)->first();
            
            if ($supplier) {
                return response()->json([
                    'success' => true,
                    'name' => $supplier->name,
                    'supplier_id' => $supplier->supplier_id
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier name: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching supplier name'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'header.bill_date' => 'required|date',
            'header.supplier_id' => 'required|exists:suppliers,supplier_id',
            'header.bill_no' => 'nullable|string|max:100',
            'header.challan_id' => 'nullable|integer', // Optional: if from challan
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.pur_rate' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');
            
            // Check if this is from a challan (stock already added)
            $isFromChallan = !empty($headerData['challan_id']);
            $challanId = $headerData['challan_id'] ?? null;
            
            // Generate Transaction Number if not provided
            if (empty($headerData['trn_no'])) {
                $headerData['trn_no'] = $this->generateTrnNo();
            }
            
            // Create Master Record
            $transaction = PurchaseTransaction::create([
                'trn_no' => $headerData['trn_no'],
                'bill_date' => $headerData['bill_date'],
                'bill_no' => $headerData['bill_no'] ?? null,
                'supplier_id' => $headerData['supplier_id'],
                'receive_date' => $headerData['receive_date'] ?? null,
                'due_date' => $headerData['due_date'] ?? null,
                'cash_flag' => $headerData['cash_flag'] ?? 'N',
                'transfer_flag' => $headerData['transfer_flag'] ?? 'N',
                'remarks' => $headerData['remarks'] ?? null,
                
                // Summary amounts
                'nt_amount' => $headerData['nt_amount'] ?? 0,
                'sc_amount' => $headerData['sc_amount'] ?? 0,
                'scm_amount' => $headerData['scm_amount'] ?? 0,
                'dis_amount' => $headerData['dis_amount'] ?? 0,
                'less_amount' => $headerData['less_amount'] ?? 0,
                'tax_amount' => $headerData['tax_amount'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'scm_percent' => $headerData['scm_percent'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'dis1_amount' => $headerData['dis1_amount'] ?? 0,
                'tof_amount' => $headerData['tof_amount'] ?? 0,
                'inv_amount' => $headerData['inv_amount'] ?? 0,
                
                'status' => 'completed',
                'created_by' => Auth::id(),
            ]);
            
            // Create Detail Records (Items)
            foreach ($itemsData as $itemData) {
                // Get item_id from item_code (item_code is actually the item's ID from getAllItems)
                // Try by ID first, then by bar_code as fallback
                $item = Item::where('id', $itemData['item_code'])->first();
                
                if (!$item) {
                    // Try by bar_code as fallback
                    $item = Item::where('bar_code', $itemData['item_code'])->first();
                }
                
                if (!$item) {
                    throw new \Exception("Item not found: " . $itemData['item_code']);
                }
                
                // Convert expiry date from MM/YY format to YYYY-MM-DD format
                $expiryDate = null;
                if (!empty($itemData['expiry_date'])) {
                    $expiryDate = $this->convertExpiryDate($itemData['expiry_date']);
                }
                
                // Create Purchase Transaction Item
                $purchaseItem = PurchaseTransactionItem::create([
                    'purchase_transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'item_code' => $itemData['item_code'],
                    'item_name' => $itemData['item_name'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $itemData['qty'],
                    'free_qty' => $itemData['free_qty'] ?? 0,
                    'pur_rate' => $itemData['pur_rate'],
                    'mrp' => $itemData['mrp'] ?? 0,
                    's_rate' => $itemData['s_rate'] ?? 0,
                    'ws_rate' => $itemData['ws_rate'] ?? 0,
                    'spl_rate' => $itemData['spl_rate'] ?? 0,
                    'dis_percent' => $itemData['dis_percent'] ?? 0,
                    'amount' => $itemData['amount'],
                    
                    // GST data
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'cess_percent' => $itemData['cess_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'cess_amount' => $itemData['cess_amount'] ?? 0,
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'net_amount' => $itemData['net_amount'] ?? 0,
                    'cost' => $itemData['cost'] ?? 0,
                    'cost_gst' => $itemData['cost_gst'] ?? 0,
                    
                    // Additional fields
                    'unit' => $item->unit ?? null,
                    'packing' => $item->packing ?? null,
                    'company_name' => $item->company->name ?? null,
                    
                    'row_order' => $itemData['row_order'] ?? 0,
                ]);
                
                // Only create batch if NOT from challan (challan already created batch)
                if (!$isFromChallan) {
                    // Also save to Batches table (NEW)
                    $totalQty = ($itemData['qty'] ?? 0) + ($itemData['free_qty'] ?? 0);
                    
                    // Calculate additional fields
                    $gstPercent = ($itemData['cgst_percent'] ?? 0) + ($itemData['sgst_percent'] ?? 0);
                    $gstPts = ($itemData['s_rate'] ?? 0) * ($gstPercent / 100);
                    $costWfq = ($itemData['cost'] ?? 0); // Can be enhanced with freight/quality charges
                    $rateDiff = ($itemData['s_rate'] ?? 0) - ($itemData['pur_rate'] ?? 0);
                    
                    Batch::create([
                        'purchase_transaction_id' => $transaction->id,
                        'purchase_transaction_item_id' => $purchaseItem->id,
                        'item_id' => $item->id,
                        'item_code' => $itemData['item_code'],
                        'item_name' => $itemData['item_name'],
                        'batch_no' => $itemData['batch_no'] ?? null,
                        'bc' => 'N', // Default BC flag
                        'expiry_date' => $expiryDate,
                        'manufacturing_date' => null,
                        'qty' => $itemData['qty'],
                        'free_qty' => $itemData['free_qty'] ?? 0,
                        'total_qty' => $totalQty,
                        'pur_rate' => $itemData['pur_rate'],
                        's_rate' => $itemData['s_rate'] ?? 0,
                        'mrp' => $itemData['mrp'] ?? 0,
                        'ws_rate' => $itemData['ws_rate'] ?? 0,
                        'spl_rate' => $itemData['spl_rate'] ?? 0,
                        'sale_scheme' => null, // Will be set from item master or manually
                        'inc' => 'Y', // Default Inc flag
                        'n_rate' => 0, // Can be set manually
                        'dis_percent' => $itemData['dis_percent'] ?? 0,
                        'sc_amount' => 0, // S.C. Amount (if applicable)
                        'amount' => $itemData['amount'],
                        'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                        'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                        'cess_percent' => $itemData['cess_percent'] ?? 0,
                        'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                        'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                        'cess_amount' => $itemData['cess_amount'] ?? 0,
                        'tax_amount' => $itemData['tax_amount'] ?? 0,
                        'gst_pts' => $gstPts,
                        'net_amount' => $itemData['net_amount'] ?? 0,
                        'cost' => $itemData['cost'] ?? 0,
                        'cost_gst' => $itemData['cost_gst'] ?? 0,
                        'cost_wfq' => $costWfq,
                        'rate_diff' => $rateDiff,
                        'unit' => $item->unit ?? null,
                        'packing' => $item->packing ?? null,
                        'company_name' => $item->company->name ?? null,
                        'godown' => null,
                        'status' => 'active',
                        'is_deleted' => 0,
                        'remarks' => null,
                    ]);
                } else {
                    // Update existing batch with purchase_transaction_id
                    if (!empty($itemData['batch_id'])) {
                        Batch::where('id', $itemData['batch_id'])->update([
                            'purchase_transaction_id' => $transaction->id,
                            'purchase_transaction_item_id' => $purchaseItem->id,
                        ]);
                    }
                }
            }
            
            // Update Item Master with rates from this purchase (latest batch rates)
            foreach ($itemsData as $itemData) {
                $item = Item::find($itemData['item_code']);
                
                if ($item) {
                    // Update rates from purchase transaction
                    $item->update([
                        'mrp' => $itemData['mrp'] ?? $item->mrp,
                        's_rate' => $itemData['s_rate'] ?? $item->s_rate,
                        'ws_rate' => $itemData['ws_rate'] ?? $item->ws_rate,
                        'spl_rate' => $itemData['spl_rate'] ?? $item->spl_rate,
                        'pur_rate' => $itemData['pur_rate'] ?? $item->pur_rate,
                        'cost' => $itemData['pur_rate'] ?? $item->cost, // Cost = Purchase Rate
                    ]);
                }
            }
            
            // Remove pending orders for items that were saved in this transaction
            $this->removePendingOrders($headerData['supplier_id'], $itemsData);
            
            // If from challan, mark challan as invoiced
            if ($isFromChallan && $challanId) {
                PurchaseChallanTransaction::where('id', $challanId)->update([
                    'is_invoiced' => true,
                    'purchase_transaction_id' => $transaction->id,
                    'status' => 'invoiced'
                ]);
                Log::info("Challan {$challanId} marked as invoiced with Purchase Transaction {$transaction->id}");
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $isFromChallan ? 'Purchase invoice created from challan successfully' : 'Purchase transaction saved successfully',
                'trn_no' => $transaction->trn_no,
                'bill_no' => $transaction->bill_no,
                'id' => $transaction->id,
                'from_challan' => $isFromChallan
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Transaction Save Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = PurchaseTransaction::with(['items.item', 'supplier'])->findOrFail($id);
        
        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id' => $transaction->id,
                'bill_no' => $transaction->bill_no,
                'bill_date' => $transaction->bill_date ? \Carbon\Carbon::parse($transaction->bill_date)->format('d-M-y') : null,
                'supplier_name' => $transaction->supplier->name ?? 'N/A',
                'net_amount' => $transaction->net_amount,
                'items' => $transaction->items->map(function($item) {
                    return [
                        'item_name' => $item->item->name ?? 'N/A',
                        'pack' => $item->item->packing ?? '',
                        'batch' => $item->batch_no ?? '',
                        'expiry' => $item->expiry_date ?? '',
                        'qty' => $item->qty ?? 0,
                        'free_qty' => $item->free_qty ?? 0,
                        'rate' => $item->pur_rate ?? 0,
                        'discount' => $item->discount ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'amount' => $item->amount ?? 0,
                    ];
                })
            ]);
        }
        
        return view('admin.purchase.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaction = PurchaseTransaction::with(['items', 'supplier'])->findOrFail($id);
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        
        return view('admin.purchase.transaction', [
            'transaction' => $transaction,
            'suppliers' => $suppliers,
            'nextTrnNo' => $transaction->trn_no
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = PurchaseTransaction::findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'header.bill_date' => 'required|date',
            'header.supplier_id' => 'required|exists:suppliers,supplier_id',
            'header.bill_no' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.pur_rate' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');
            
            // Update master record
            $transaction->update([
                'bill_date' => $headerData['bill_date'],
                'bill_no' => $headerData['bill_no'] ?? null,
                'supplier_id' => $headerData['supplier_id'],
                'receive_date' => $headerData['receive_date'] ?? null,
                'due_date' => $headerData['due_date'] ?? null,
                'cash_flag' => $headerData['cash_flag'] ?? 'N',
                'transfer_flag' => $headerData['transfer_flag'] ?? 'N',
                'remarks' => $headerData['remarks'] ?? null,
                
                // Summary amounts
                'nt_amount' => $headerData['nt_amount'] ?? 0,
                'sc_amount' => $headerData['sc_amount'] ?? 0,
                'scm_amount' => $headerData['scm_amount'] ?? 0,
                'dis_amount' => $headerData['dis_amount'] ?? 0,
                'less_amount' => $headerData['less_amount'] ?? 0,
                'tax_amount' => $headerData['tax_amount'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'scm_percent' => $headerData['scm_percent'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'dis1_amount' => $headerData['dis1_amount'] ?? 0,
                'tof_amount' => $headerData['tof_amount'] ?? 0,
                'inv_amount' => $headerData['inv_amount'] ?? 0,
                
                'updated_by' => Auth::id(),
            ]);
            
            // Delete old items and batches
            $transaction->items()->delete();
            Batch::where('purchase_transaction_id', $transaction->id)->delete();
            
            // Insert new items
            foreach ($itemsData as $itemData) {
                // Get item_id from item_code (item_code is actually the item's ID from getAllItems)
                // Try by ID first, then by bar_code as fallback
                $item = Item::where('id', $itemData['item_code'])->first();
                
                if (!$item) {
                    // Try by bar_code as fallback
                    $item = Item::where('bar_code', $itemData['item_code'])->first();
                }
                
                if (!$item) {
                    throw new \Exception("Item not found: " . $itemData['item_code']);
                }
                
                // Convert expiry date from MM/YY format to YYYY-MM-DD format
                $expiryDate = null;
                if (!empty($itemData['expiry_date'])) {
                    $expiryDate = $this->convertExpiryDate($itemData['expiry_date']);
                }
                
                // Create Purchase Transaction Item
                $purchaseItem = PurchaseTransactionItem::create([
                    'purchase_transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'item_code' => $itemData['item_code'],
                    'item_name' => $itemData['item_name'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $itemData['qty'],
                    'free_qty' => $itemData['free_qty'] ?? 0,
                    'pur_rate' => $itemData['pur_rate'],
                    'mrp' => $itemData['mrp'] ?? 0,
                    's_rate' => $itemData['s_rate'] ?? 0,
                    'ws_rate' => $itemData['ws_rate'] ?? 0,
                    'spl_rate' => $itemData['spl_rate'] ?? 0,
                    'dis_percent' => $itemData['dis_percent'] ?? 0,
                    'amount' => $itemData['amount'],
                    
                    // GST data
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'cess_percent' => $itemData['cess_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'cess_amount' => $itemData['cess_amount'] ?? 0,
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'net_amount' => $itemData['net_amount'] ?? 0,
                    'cost' => $itemData['cost'] ?? 0,
                    'cost_gst' => $itemData['cost_gst'] ?? 0,
                    
                    // Additional fields
                    'unit' => $item->unit ?? null,
                    'packing' => $item->packing ?? null,
                    'company_name' => $item->company->name ?? null,
                    
                    'row_order' => $itemData['row_order'] ?? 0,
                ]);
                
                // Also save to Batches table (NEW)
                $totalQty = ($itemData['qty'] ?? 0) + ($itemData['free_qty'] ?? 0);
                
                // Calculate additional fields
                $gstPercent = ($itemData['cgst_percent'] ?? 0) + ($itemData['sgst_percent'] ?? 0);
                $gstPts = ($itemData['s_rate'] ?? 0) * ($gstPercent / 100);
                $costWfq = ($itemData['cost'] ?? 0);
                $rateDiff = ($itemData['s_rate'] ?? 0) - ($itemData['pur_rate'] ?? 0);
                
                Batch::create([
                    'purchase_transaction_id' => $transaction->id,
                    'purchase_transaction_item_id' => $purchaseItem->id,
                    'item_id' => $item->id,
                    'item_code' => $itemData['item_code'],
                    'item_name' => $itemData['item_name'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'bc' => 'N',
                    'expiry_date' => $expiryDate,
                    'manufacturing_date' => null,
                    'qty' => $itemData['qty'],
                    'free_qty' => $itemData['free_qty'] ?? 0,
                    'total_qty' => $totalQty,
                    'pur_rate' => $itemData['pur_rate'],
                    's_rate' => $itemData['s_rate'] ?? 0,
                    'mrp' => $itemData['mrp'] ?? 0,
                    'ws_rate' => $itemData['ws_rate'] ?? 0,
                    'spl_rate' => $itemData['spl_rate'] ?? 0,
                    'sale_scheme' => null,
                    'inc' => 'Y',
                    'n_rate' => 0,
                    'dis_percent' => $itemData['dis_percent'] ?? 0,
                    'sc_amount' => 0,
                    'amount' => $itemData['amount'],
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'cess_percent' => $itemData['cess_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'cess_amount' => $itemData['cess_amount'] ?? 0,
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'gst_pts' => $gstPts,
                    'net_amount' => $itemData['net_amount'] ?? 0,
                    'cost' => $itemData['cost'] ?? 0,
                    'cost_gst' => $itemData['cost_gst'] ?? 0,
                    'cost_wfq' => $costWfq,
                    'rate_diff' => $rateDiff,
                    'unit' => $item->unit ?? null,
                    'packing' => $item->packing ?? null,
                    'company_name' => $item->company->name ?? null,
                    'godown' => null,
                    'status' => 'active',
                    'is_deleted' => 0,
                    'remarks' => null,
                ]);
            }
            
            // Update Item Master with rates from this purchase (latest batch rates)
            foreach ($itemsData as $itemData) {
                $item = Item::find($itemData['item_code']);
                
                if ($item) {
                    // Update rates from purchase transaction
                    $item->update([
                        'mrp' => $itemData['mrp'] ?? $item->mrp,
                        's_rate' => $itemData['s_rate'] ?? $item->s_rate,
                        'ws_rate' => $itemData['ws_rate'] ?? $item->ws_rate,
                        'spl_rate' => $itemData['spl_rate'] ?? $item->spl_rate,
                        'pur_rate' => $itemData['pur_rate'] ?? $item->pur_rate,
                        'cost' => $itemData['pur_rate'] ?? $item->cost, // Cost = Purchase Rate
                    ]);
                }
            }
            
            // Remove pending orders for items that were saved in this transaction
            $this->removePendingOrders($headerData['supplier_id'], $itemsData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase transaction updated successfully',
                'trn_no' => $transaction->trn_no,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Transaction Update Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $transaction = PurchaseTransaction::findOrFail($id);
            $transaction->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase transaction deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate next transaction number
     */
    private function generateTrnNo()
    {
        $lastTransaction = PurchaseTransaction::orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? (intval($lastTransaction->trn_no) + 1) : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Convert expiry date from MM/YY format to YYYY-MM-DD format
     * 
     * @param string $expiryDate Date in MM/YY or MM/YYYY format
     * @return string|null Date in YYYY-MM-DD format or null
     */
    private function convertExpiryDate($expiryDate)
    {
        if (empty($expiryDate)) {
            return null;
        }
        
        // Remove any spaces
        $expiryDate = trim($expiryDate);
        
        // Check if it's already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiryDate)) {
            return $expiryDate;
        }
        
        // Try to parse MM/YY or MM/YYYY format
        // Formats: 11/28, 11/2028, 11-28, 11-2028
        if (preg_match('/^(\d{1,2})[\/\-](\d{2,4})$/', $expiryDate, $matches)) {
            $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $year = $matches[2];
            
            // Convert 2-digit year to 4-digit year
            if (strlen($year) == 2) {
                // Assume 20xx for years 00-99
                $year = '20' . $year;
            }
            
            // Validate month
            if ($month < 1 || $month > 12) {
                Log::warning("Invalid month in expiry date: {$expiryDate}");
                return null;
            }
            
            // Return as YYYY-MM-01 (first day of the month)
            return $year . '-' . $month . '-01';
        }
        
        Log::warning("Could not parse expiry date format: {$expiryDate}");
        return null;
    }

    /**
     * Remove pending orders for items that were saved in purchase transaction
     * 
     * @param int $supplierId
     * @param array $itemsData
     * @return void
     */
    private function removePendingOrders($supplierId, $itemsData)
    {
        try {
            // Get all item IDs from the transaction items
            $itemIds = [];
            foreach ($itemsData as $itemData) {
                // item_code is actually the item's ID
                $itemId = $itemData['item_code'];
                
                // If it's not numeric, try to find by bar_code
                if (!is_numeric($itemId)) {
                    $item = Item::where('bar_code', $itemId)->first();
                    if ($item) {
                        $itemId = $item->id;
                    } else {
                        continue; // Skip if item not found
                    }
                }
                
                $itemIds[] = $itemId;
            }
            
            if (empty($itemIds)) {
                return; // No items to process
            }
            
            // Find and delete pending orders for this supplier and these items
            $deletedCount = PendingOrder::where('supplier_id', $supplierId)
                ->whereIn('item_id', $itemIds)
                ->delete();
            
            if ($deletedCount > 0) {
                Log::info("Removed {$deletedCount} pending order(s) for supplier {$supplierId} after saving purchase transaction");
            }
            
        } catch (\Exception $e) {
            // Log error but don't fail the transaction
            Log::error('Error removing pending orders: ' . $e->getMessage());
        }
    }

    /**
     * Get pending challans for a supplier (not yet invoiced)
     */
    public function getPendingChallans($supplierId)
    {
        try {
            $challans = PurchaseChallanTransaction::where('supplier_id', $supplierId)
                ->where('is_invoiced', false)
                ->where('status', 'active')
                ->orderBy('challan_date', 'desc')
                ->get(['id', 'challan_no', 'challan_date', 'supplier_invoice_no', 'net_amount']);

            return response()->json([
                'success' => true,
                'challans' => $challans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pending challans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pending challans'
            ], 500);
        }
    }

    /**
     * Get challan details with items for loading into purchase form
     */
    public function getChallanDetails($challanId)
    {
        try {
            $challan = PurchaseChallanTransaction::with(['items.item', 'supplier'])
                ->findOrFail($challanId);

            // Format items for the purchase form
            $items = $challan->items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_code' => $item->item ? $item->item->id : '',
                    'item_name' => $item->item ? $item->item->name : '',
                    'batch_no' => $item->batch_no,
                    'expiry_date' => $item->expiry_date ? date('m/y', strtotime($item->expiry_date)) : '',
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty ?? 0,
                    'pur_rate' => $item->purchase_rate,
                    'dis_percent' => $item->discount_percent ?? 0,
                    'mrp' => $item->mrp,
                    's_rate' => $item->sale_rate ?? 0,
                    'ws_rate' => $item->ws_rate ?? 0,
                    'spl_rate' => $item->spl_rate ?? 0,
                    'amount' => $item->net_amount,
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'cgst_amount' => $item->cgst_amount ?? 0,
                    'sgst_amount' => $item->sgst_amount ?? 0,
                    'cess_amount' => $item->cess_amount ?? 0,
                    'batch_id' => $item->batch_id,
                    'row_order' => $item->row_order ?? 0
                ];
            });

            return response()->json([
                'success' => true,
                'challan' => [
                    'id' => $challan->id,
                    'challan_no' => $challan->challan_no,
                    'challan_date' => $challan->challan_date,
                    'supplier_id' => $challan->supplier_id,
                    'supplier_name' => $challan->supplier ? $challan->supplier->name : '',
                    'supplier_invoice_no' => $challan->supplier_invoice_no,
                    'supplier_invoice_date' => $challan->supplier_invoice_date,
                    'nt_amount' => $challan->nt_amount,
                    'tax_amount' => $challan->tax_amount,
                    'net_amount' => $challan->net_amount,
                    'remarks' => $challan->remarks
                ],
                'items' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching challan details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching challan details: ' . $e->getMessage()
            ], 500);
        }
    }
}
