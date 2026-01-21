<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesMan;
use App\Models\Batch;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SaleTransactionController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = SaleTransaction::with('customer')
            ->orderBy('sale_date', 'desc')
            ->paginate(20);
        
        return view('admin.sale.transactions.index', compact('transactions'));
    }

    /**
     * Display sale transaction form
     */
    public function transaction()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextInvoiceNo = $this->generateInvoiceNo();
        
        return view('admin.sale.transaction', compact('customers', 'salesmen', 'items', 'nextInvoiceNo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextInvoiceNo = $this->generateInvoiceNo();
        
        return view('admin.sale.transaction', compact('customers', 'salesmen', 'items', 'nextInvoiceNo'));
    }

    /**
     * Get all items for Choose Items modal
     */
    public function getItems()
    {
        try {
            $items = Item::select('id', 'name', 'bar_code', 'hsn_code', 'packing', 'company_id', 'company_short_name', 's_rate', 'mrp', 'cgst_percent', 'sgst_percent', 'cess_percent', 'unit', 'case_qty', 'box_qty', 'fixed_dis_percent')
                ->with(['batches' => function($query) {
                    $query->where('is_deleted', 0);
                }, 'company:id,dis_on_sale_percent'])
                ->get()
                ->map(function($item) {
                    // Calculate total quantity from batches table
                    $totalQty = $item->getTotalQuantity(); // Uses batches table: sum(total_qty) where is_deleted = 0
                    
                    // Get discount - item discount takes priority over company discount
                    $itemDiscount = floatval($item->fixed_dis_percent ?? 0);
                    $companyDiscount = floatval($item->company->dis_on_sale_percent ?? 0);
                    
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'bar_code' => $item->bar_code,
                        'hsn_code' => $item->hsn_code,
                        'packing' => $item->packing,
                        'company_id' => $item->company_id,
                        'company_name' => $item->company_short_name ?? 'N/A',
                        'company' => $item->company_short_name ?? 'N/A',
                        's_rate' => $item->s_rate ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'cgst_percent' => $item->cgst_percent ?? 0,
                        'sgst_percent' => $item->sgst_percent ?? 0,
                        'cess_percent' => $item->cess_percent ?? 0,
                        'unit' => $item->unit ?? '1',
                        'case_qty' => $item->case_qty ?? 0,
                        'box_qty' => $item->box_qty ?? 0,
                        'qty' => $totalQty, // 🔥 Total available quantity from batches table
                        'available_qty' => $totalQty, // Also include as available_qty for compatibility
                        'fixed_dis_percent' => $itemDiscount, // Item-level discount
                        'company_discount' => $companyDiscount, // Company-level discount
                    ];
                });
            
            return response()->json($items);
        } catch (\Exception $e) {
            Log::error('Error fetching items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request for debugging
            Log::info('Sale Transaction Store Request', [
                'data' => $request->all()
            ]);
            
            // Validate transaction date (no backdating, max 1 day future)
            $dateError = $this->validateTransactionDate($request, 'sale', 'date');
            if ($dateError) {
                return $this->dateValidationErrorResponse($dateError);
            }
            
            // Check if this is a TEMP (receipt-only) transaction
            $isTempTransaction = $request->input('series') === 'TEMP';
            
            // Different validation for TEMP vs normal transactions
            if ($isTempTransaction) {
                // TEMP transactions don't require items
                $validated = $request->validate([
                    'date' => 'required|date',
                    'customer_id' => 'required|exists:customers,id',
                    'invoice_no' => 'required|string|max:100',
                ]);
            } else {
                // Normal transactions require items
                $validated = $request->validate([
                    'date' => 'required|date',
                    'customer_id' => 'required|exists:customers,id',
                    'invoice_no' => 'required|string|max:100',
                    'items' => 'required|array|min:1',
                    'items.*.qty' => 'required|numeric|min:0',
                    'items.*.rate' => 'required|numeric|min:0',
                ]);
            }
            
            // No need to validate item_code - it's optional
            // Items can be saved with just name, even without code
            
            Log::info('Validation passed', ['is_temp' => $isTempTransaction]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Handle items - may be array (JSON request) or JSON string (FormData request)
            $itemsData = $request->input('items', []);
            if (is_string($itemsData)) {
                $itemsData = json_decode($itemsData, true) ?? [];
            }
            
            $challanId = $request->input('challan_id'); // Challan ID if converting from challan
            
            // Generate appropriate invoice number based on series
            if ($isTempTransaction) {
                $invoiceNo = $this->generateTempInvoiceNo();
            } else {
                $invoiceNo = $this->generateInvoiceNo();
            }
            
            // Handle receipt file upload for TEMP transactions
            $receiptPaths = [];
            $receiptDescriptions = [];
            
            if ($isTempTransaction) {
                $customerId = $request->input('customer_id');
                
                // Get customer name for folder
                $customer = \App\Models\Customer::find($customerId);
                $customerName = $customer ? preg_replace('/[^A-Za-z0-9\-]/', '_', $customer->name) : 'Unknown';
                
                // Create folder name: CustomerName_Date (e.g., "Aryan_Medical_Store_17-01-2026")
                $currentDate = date('d-m-Y');
                $folderName = $customerName . '_' . $currentDate;
                
                // Handle single receipt file (legacy support)
                if ($request->hasFile('receipt_file')) {
                    $file = $request->file('receipt_file');
                    $filename = 'receipt_' . time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs("receipts/{$folderName}", $filename, 'public');
                    $receiptPaths[] = 'storage/' . $path;
                    
                    Log::info('📷 Single receipt file uploaded', ['path' => end($receiptPaths)]);
                }
                
                // Handle multiple receipt files
                if ($request->hasFile('receipt_files')) {
                    $files = $request->file('receipt_files');
                    $descriptions = $request->input('receipt_descriptions', []);
                    
                    foreach ($files as $index => $file) {
                        $filename = 'receipt_' . time() . '_' . ($index + 1) . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs("receipts/{$folderName}", $filename, 'public');
                        $receiptPaths[] = 'storage/' . $path;
                        
                        if (isset($descriptions[$index])) {
                            $receiptDescriptions[] = $descriptions[$index];
                        }
                        
                        Log::info('📷 Multiple receipt file uploaded', [
                            'index' => $index,
                            'path' => end($receiptPaths)
                        ]);
                    }
                }
            }
            
            // Combine receipt paths as comma-separated string
            $receiptPath = !empty($receiptPaths) ? implode(',', $receiptPaths) : null;
            $receiptDescription = !empty($receiptDescriptions) 
                ? implode(' | ', $receiptDescriptions) 
                : $request->input('receipt_description');
            
            // Create Master Record (using summary data from frontend)
            $transaction = SaleTransaction::create([
                'invoice_no' => $invoiceNo,
                'series' => $request->input('series', 'SB'),
                'sale_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'customer_id' => $request->input('customer_id'),
                'salesman_id' => $request->input('salesman_id'),
                'cash_flag' => $request->input('cash', 'N'),
                'transfer_flag' => $request->input('transfer', 'N'),
                'remarks' => $request->input('remarks'),
                'challan_id' => $challanId, // Link to challan if converting
                'receipt_path' => $receiptPath, // Receipt path for TEMP transactions
                'receipt_description' => $receiptDescription,
                
                // Summary amounts (from frontend calculations)
                'nt_amount' => $request->input('nt_amount', 0),
                'sc_amount' => $request->input('sc_amount', 0),
                'ft_amount' => $request->input('ft_amount', 0),
                'dis_amount' => $request->input('dis_amount', 0),
                'scm_amount' => $request->input('scm_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'net_amount' => $request->input('net_amount', 0),
                'scm_percent' => $request->input('scm_percent', 0),
                'tcs_amount' => $request->input('tcs_amount', 0),
                'excise_amount' => $request->input('excise_amount', 0),
                
                // Payment info - If Cash='Y', payment received so balance=0, else balance=net_amount
                // For TEMP transactions, no payment is recorded yet
                'paid_amount' => $isTempTransaction ? 0 : ($request->input('cash', 'N') === 'Y' ? $request->input('net_amount', 0) : 0),
                'balance_amount' => $isTempTransaction ? 0 : ($request->input('cash', 'N') === 'Y' ? 0 : $request->input('net_amount', 0)),
                'payment_status' => $isTempTransaction ? 'pending' : ($request->input('cash', 'N') === 'Y' ? 'paid' : 'pending'),
                
                // TEMP transactions are marked as 'pending' until items are added
                'status' => $isTempTransaction ? 'pending' : 'completed',
                'created_by' => Auth::id(),
            ]);
            
            // Create Detail Records (Items) - Only if items exist
            // TEMP transactions may not have items initially
            if (!empty($itemsData)) {
            foreach ($itemsData as $index => $itemData) {
                // 🔥 DEBUG: Log batch_id
                Log::info('Processing sale item', [
                    'item_name' => $itemData['item_name'] ?? 'N/A',
                    'batch_id' => $itemData['batch_id'] ?? 'NULL',
                    'batch_no' => $itemData['batch'] ?? 'N/A',
                    'qty' => $itemData['qty'] ?? 0
                ]);
                
                // Support both 'item_code' and 'code' fields (both optional)
                $itemCode = $itemData['item_code'] ?? $itemData['code'] ?? '';
                
                // Try to find item from Item Master
                $item = null;
                if (!empty($itemCode)) {
                    // Try by bar_code first, then by id
                    $item = Item::where('bar_code', $itemCode)->first();
                    if (!$item) {
                        $item = Item::find($itemCode);
                    }
                }
                
                // If still not found but item_name exists, try finding by name
                if (!$item && !empty($itemData['item_name'])) {
                    $item = Item::where('name', $itemData['item_name'])->first();
                }
                
                // Calculate item amounts
                $qty = floatval($itemData['qty']);
                $rate = floatval($itemData['rate']);
                $discountPercent = floatval($itemData['discount'] ?? 0);
                
                $amount = $qty * $rate; // Line total before discount
                $discountAmount = $amount * ($discountPercent / 100);
                $amountAfterDiscount = $amount - $discountAmount;
                
                // Get GST from item if exists, otherwise use 0
                $cgstPercent = $item ? floatval($item->cgst_percent ?? 0) : 0;
                $sgstPercent = $item ? floatval($item->sgst_percent ?? 0) : 0;
                $cessPercent = $item ? floatval($item->cess_percent ?? 0) : 0;
                
                // Calculate GST amounts on discounted amount
                $cgstAmount = $amountAfterDiscount * ($cgstPercent / 100);
                $sgstAmount = $amountAfterDiscount * ($sgstPercent / 100);
                $cessAmount = $amountAfterDiscount * ($cessPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount + $cessAmount;
                
                $netAmount = $amountAfterDiscount + $taxAmount;
                
                $saleItem = SaleTransactionItem::create([
                    'sale_transaction_id' => $transaction->id,
                    'item_id' => $item ? $item->id : null,  // Null if item not found
                    'batch_id' => $itemData['batch_id'] ?? null, // 🔥 Batch ID for quantity tracking
                    'item_code' => $itemCode ?: '',  // Empty string if no code
                    'item_name' => $itemData['item_name'] ?? ($item ? $item->name : ''),
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $itemData['expiry'] ?? null,
                    'qty' => $qty,
                    'free_qty' => floatval($itemData['free_qty'] ?? 0),
                    'sale_rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? ($item ? $item->mrp : 0)),
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'amount' => $amount,
                    'net_amount' => $netAmount,
                    
                    // GST data
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cess_percent' => $cessPercent,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'cess_amount' => $cessAmount,
                    'tax_amount' => $taxAmount,
                    
                    // Additional fields - from item if exists, otherwise null
                    'unit' => $item ? ($item->unit ?? null) : null,
                    'packing' => $item ? ($item->packing ?? null) : null,
                    'company_name' => $item ? ($item->company_short_name ?? null) : null,
                    'hsn_code' => $item ? ($item->hsn_code ?? null) : null,
                    
                    'row_order' => $itemData['row_order'] ?? $index,
                ]);
                
                // 🔥 REDUCE BATCH QUANTITY - IMPORTANT: Update batches table
                // SKIP if item is from challan (stock already deducted when challan was created)
                $isFromChallan = isset($itemData['from_challan']) && ($itemData['from_challan'] === 'true' || $itemData['from_challan'] === true);
                
                if ($isFromChallan) {
                    Log::info('⏭️ Skipping batch quantity reduction - item from challan (already deducted)', [
                        'item_name' => $itemData['item_name'] ?? 'N/A',
                        'batch_no' => $itemData['batch'] ?? 'N/A',
                        'qty' => $qty
                    ]);
                } else {
                    $batchId = null;
                    
                    // Get batch_id from itemData (can be string or int)
                    if (isset($itemData['batch_id']) && !empty($itemData['batch_id'])) {
                        $batchId = is_numeric($itemData['batch_id']) ? (int)$itemData['batch_id'] : null;
                    }
                    
                    // If batch_id not in itemData, try to find by batch_no
                    if (!$batchId && !empty($itemData['batch']) && $item) {
                        $batch = Batch::where('batch_no', $itemData['batch'])
                            ->where('item_id', $item->id)
                            ->where('is_deleted', 0)
                            ->where('total_qty', '>', 0)
                            ->first();
                        
                        if ($batch) {
                            $batchId = $batch->id;
                            Log::info('Batch found by batch_no', [
                                'batch_no' => $itemData['batch'],
                                'batch_id' => $batchId
                            ]);
                        }
                    }
                    
                    // Reduce batch quantity if batch_id found
                    if ($batchId) {
                        try {
                            $batch = Batch::find($batchId);
                            
                            if ($batch) {
                                $soldQty = floatval($qty); // Only qty, not free_qty
                                
                                Log::info('Attempting to reduce batch quantity', [
                                    'batch_id' => $batchId,
                                    'batch_no' => $batch->batch_no,
                                    'current_total_qty' => $batch->total_qty,
                                    'current_qty' => $batch->qty,
                                    'sold_qty' => $soldQty
                                ]);
                                
                                // ALWAYS reduce batch quantity - ALLOW NEGATIVE QUANTITIES
                                $oldTotalQty = $batch->total_qty;
                                $oldQty = $batch->qty;
                                
                                $batch->total_qty = $batch->total_qty - $soldQty;
                                $batch->qty = $batch->qty - $soldQty;
                                
                                $saved = $batch->save();
                                
                                if ($saved) {
                                    $logLevel = $batch->total_qty < 0 ? 'warning' : 'info';
                                    Log::$logLevel('✅ Batch quantity reduced successfully', [
                                        'batch_id' => $batchId,
                                        'batch_no' => $batch->batch_no,
                                        'item_name' => $batch->item_name,
                                        'old_total_qty' => $oldTotalQty,
                                        'old_qty' => $oldQty,
                                        'sold_qty' => $soldQty,
                                        'new_total_qty' => $batch->total_qty,
                                        'new_qty' => $batch->qty,
                                        'is_negative' => $batch->total_qty < 0
                                    ]);
                                    
                                    if ($batch->total_qty < 0) {
                                        Log::warning('⚠️ Batch quantity is now NEGATIVE - oversold!', [
                                            'batch_id' => $batchId,
                                            'batch_no' => $batch->batch_no,
                                            'item_name' => $batch->item_name,
                                            'negative_qty' => $batch->total_qty
                                        ]);
                                    }
                                } else {
                                    Log::error('❌ Failed to save batch quantity reduction', [
                                        'batch_id' => $batchId,
                                        'batch_no' => $batch->batch_no
                                    ]);
                                }
                            } else {
                                Log::warning('⚠️ Batch not found for quantity reduction', [
                                    'batch_id' => $batchId
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('❌ Error reducing batch quantity', [
                                'batch_id' => $batchId,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    } else {
                        Log::warning('⚠️ No batch_id found for sale item', [
                            'item_name' => $itemData['item_name'] ?? 'N/A',
                            'batch_no' => $itemData['batch'] ?? 'N/A',
                            'itemData' => $itemData
                        ]);
                    }
                } // End of else (not from challan)
            }
            } // End of if (!empty($itemsData))
            
            // Mark challan as invoiced if this sale was converted from a challan
            if ($challanId) {
                $challan = \App\Models\SaleChallanTransaction::find($challanId);
                if ($challan) {
                    $challan->is_invoiced = true;
                    $challan->sale_transaction_id = $transaction->id;
                    $challan->save();
                    
                    Log::info('✅ Challan marked as invoiced', [
                        'challan_id' => $challanId,
                        'challan_no' => $challan->challan_no,
                        'sale_transaction_id' => $transaction->id
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale transaction saved successfully',
                'invoice_no' => $invoiceNo,  // Return generated invoice number
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Transaction Save Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale transaction: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaction = SaleTransaction::with(['items', 'customer'])->findOrFail($id);
        $customers = Customer::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        
        return view('admin.sale.transaction', [
            'transaction' => $transaction,
            'customers' => $customers,
            'salesmen' => $salesmen,
            'items' => $items,
            'nextInvoiceNo' => $transaction->invoice_no
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = SaleTransaction::findOrFail($id);
        
        // Validate request (item_code is optional)
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'invoice_no' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $itemsData = $request->input('items');
            
            // Update master record (using summary data from frontend)
            $transaction->update([
                'sale_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'customer_id' => $request->input('customer_id'),
                'salesman_id' => $request->input('salesman_id'),
                'cash_flag' => $request->input('cash', 'N'),
                'transfer_flag' => $request->input('transfer', 'N'),
                'remarks' => $request->input('remarks'),
                
                // Summary amounts (from frontend calculations)
                'nt_amount' => $request->input('nt_amount', 0),
                'sc_amount' => $request->input('sc_amount', 0),
                'ft_amount' => $request->input('ft_amount', 0),
                'dis_amount' => $request->input('dis_amount', 0),
                'scm_amount' => $request->input('scm_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'net_amount' => $request->input('net_amount', 0),
                'scm_percent' => $request->input('scm_percent', 0),
                'tcs_amount' => $request->input('tcs_amount', 0),
                'excise_amount' => $request->input('excise_amount', 0),
                
                // Payment info - If Cash='Y', payment received so balance=0, else balance=net_amount
                'paid_amount' => $request->input('cash', 'N') === 'Y' ? $request->input('net_amount', 0) : 0,
                'balance_amount' => $request->input('cash', 'N') === 'Y' ? 0 : $request->input('net_amount', 0),
                'payment_status' => $request->input('cash', 'N') === 'Y' ? 'paid' : 'pending',
                
                'updated_by' => Auth::id(),
            ]);
            
            // Delete old items
            $transaction->items()->delete();
            
            // Insert new items
            foreach ($itemsData as $index => $itemData) {
                // Support both 'item_code' and 'code' fields (both optional)
                $itemCode = $itemData['item_code'] ?? $itemData['code'] ?? '';
                
                // Try to find item from Item Master
                $item = null;
                if (!empty($itemCode)) {
                    // Try by bar_code first, then by id
                    $item = Item::where('bar_code', $itemCode)->first();
                    if (!$item) {
                        $item = Item::find($itemCode);
                    }
                }
                
                // If still not found but item_name exists, try finding by name
                if (!$item && !empty($itemData['item_name'])) {
                    $item = Item::where('name', $itemData['item_name'])->first();
                }
                
                // Calculate item amounts
                $qty = floatval($itemData['qty']);
                $rate = floatval($itemData['rate']);
                $discountPercent = floatval($itemData['discount'] ?? 0);
                
                $amount = $qty * $rate;
                $discountAmount = $amount * ($discountPercent / 100);
                $amountAfterDiscount = $amount - $discountAmount;
                
                // Get GST from item if exists, otherwise use 0
                $cgstPercent = $item ? floatval($item->cgst_percent ?? 0) : 0;
                $sgstPercent = $item ? floatval($item->sgst_percent ?? 0) : 0;
                $cessPercent = $item ? floatval($item->cess_percent ?? 0) : 0;
                
                $cgstAmount = $amountAfterDiscount * ($cgstPercent / 100);
                $sgstAmount = $amountAfterDiscount * ($sgstPercent / 100);
                $cessAmount = $amountAfterDiscount * ($cessPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount + $cessAmount;
                
                $netAmount = $amountAfterDiscount + $taxAmount;
                
                SaleTransactionItem::create([
                    'sale_transaction_id' => $transaction->id,
                    'item_id' => $item ? $item->id : null,  // Null if item not found
                    'item_code' => $itemCode ?: '',  // Empty string if no code
                    'item_name' => $itemData['item_name'] ?? ($item ? $item->name : ''),
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $itemData['expiry'] ?? null,
                    'qty' => $qty,
                    'free_qty' => floatval($itemData['free_qty'] ?? 0),
                    'sale_rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? ($item ? $item->mrp : 0)),
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'amount' => $amount,
                    'net_amount' => $netAmount,
                    
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cess_percent' => $cessPercent,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'cess_amount' => $cessAmount,
                    'tax_amount' => $taxAmount,
                    
                    // Additional fields - from item if exists, otherwise null
                    'unit' => $item ? ($item->unit ?? null) : null,
                    'packing' => $item ? ($item->packing ?? null) : null,
                    'company_name' => $item ? ($item->company_short_name ?? null) : null,
                    'hsn_code' => $item ? ($item->hsn_code ?? null) : null,
                    
                    'row_order' => $itemData['row_order'] ?? $index,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale transaction updated successfully',
                'invoice_no' => $transaction->invoice_no,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Transaction Update Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next invoice number (API endpoint)
     */
    public function getNextInvoiceNo()
    {
        try {
            $nextInvoiceNo = $this->generateInvoiceNo();
            
            return response()->json([
                'success' => true,
                'next_invoice_no' => $nextInvoiceNo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating invoice number'
            ], 500);
        }
    }

    /**
     * Show Sale Modification page
     */
    public function modification()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        
        return view('admin.sale.modification', compact('customers', 'salesmen'));
    }
    
    /**
     * Get list of invoices for modification (with optional date filter)
     */
    public function getInvoices(Request $request)
    {
        try {
            $query = SaleTransaction::with('customer')
                ->orderBy('sale_date', 'desc')
                ->orderBy('invoice_no', 'desc');
            
            // Apply date filter if provided
            if ($request->has('from_date') && $request->has('to_date')) {
                $fromDate = $request->input('from_date');
                $toDate = $request->input('to_date');
                
                $query->whereBetween('sale_date', [$fromDate, $toDate]);
            }
            
            $transactions = $query->limit(100)->get();
            
            $invoices = $transactions->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'sale_date' => $transaction->sale_date->format('d-m-Y'),
                    'customer_name' => $transaction->customer ? $transaction->customer->name : 'N/A',
                    'net_amount' => $transaction->net_amount,
                    'status' => $transaction->status,
                    'status_badge' => '<span class="badge bg-' . ($transaction->status === 'completed' ? 'success' : 'warning') . '">' . strtoupper($transaction->status) . '</span>',
                    'payment_status' => $transaction->payment_status ?? 'pending',
                ];
            });
            
            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search transaction by invoice number
     */
    public function searchByInvoiceNo(Request $request)
    {
        try {
            $invoiceNo = $request->input('invoice_no');
            
            if (!$invoiceNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice number is required'
                ], 400);
            }
            
            $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])
                ->where('invoice_no', $invoiceNo)
                ->where('organization_id', auth()->user()->organization_id)
                ->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found: ' . $invoiceNo
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'series' => $transaction->series,
                    'sale_date' => $transaction->sale_date->format('Y-m-d'),
                    'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                    'customer_id' => $transaction->customer_id,
                    'salesman_id' => $transaction->salesman_id,
                    'cash_flag' => $transaction->cash_flag,
                    'transfer_flag' => $transaction->transfer_flag,
                    'remarks' => $transaction->remarks,
                    'nt_amount' => $transaction->nt_amount,
                    'sc_amount' => $transaction->sc_amount,
                    'ft_amount' => $transaction->ft_amount,
                    'dis_amount' => $transaction->dis_amount,
                    'scm_amount' => $transaction->scm_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'net_amount' => $transaction->net_amount,
                    'scm_percent' => $transaction->scm_percent,
                    // TEMP transaction fields
                    'is_temp' => $transaction->series === 'TEMP' || str_starts_with($transaction->invoice_no, 'TEMP-'),
                    'receipt_path' => $transaction->receipt_path,
                    'receipt_description' => $transaction->receipt_description,
                    'items' => $transaction->items->map(function($item) {
                        return [
                            'item_code' => $item->item_code,
                            'item_name' => $item->item_name,
                            'batch_no' => $item->batch_no,
                            'batch_id' => $item->batch_id,
                            'expiry_date' => $item->expiry_date,
                            'qty' => $item->qty,
                            'free_qty' => $item->free_qty,
                            'sale_rate' => $item->sale_rate,
                            'discount_percent' => $item->discount_percent,
                            'mrp' => $item->mrp,
                            'amount' => $item->amount,
                            'cgst_percent' => $item->cgst_percent,
                            'sgst_percent' => $item->sgst_percent,
                            'cess_percent' => $item->cess_percent,
                            'hsn_code' => $item->hsn_code,
                            'packing' => $item->packing,
                            'unit' => $item->unit,
                            'company_name' => $item->company_name,
                            'case_qty' => 0, // Not stored in transaction items
                            'box_qty' => 0, // Not stored in transaction items
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching by invoice number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get single transaction details for modification
     */
    public function getTransaction($id)
    {
        try {
            $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'series' => $transaction->series,
                    'sale_date' => $transaction->sale_date->format('Y-m-d'),
                    'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                    'customer_id' => $transaction->customer_id,
                    'salesman_id' => $transaction->salesman_id,
                    'cash_flag' => $transaction->cash_flag,
                    'transfer_flag' => $transaction->transfer_flag,
                    'remarks' => $transaction->remarks,
                    'nt_amount' => $transaction->nt_amount,
                    'sc_amount' => $transaction->sc_amount,
                    'ft_amount' => $transaction->ft_amount,
                    'dis_amount' => $transaction->dis_amount,
                    'scm_amount' => $transaction->scm_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'net_amount' => $transaction->net_amount,
                    'scm_percent' => $transaction->scm_percent,
                    // TEMP transaction fields
                    'is_temp' => $transaction->series === 'TEMP' || str_starts_with($transaction->invoice_no, 'TEMP-'),
                    'receipt_path' => $transaction->receipt_path,
                    'receipt_description' => $transaction->receipt_description,
                    'items' => $transaction->items->map(function($item) {
                        return [
                            'item_id' => $item->item_id,
                            'item_code' => $item->item_code,
                            'item_name' => $item->item_name,
                            'batch_no' => $item->batch_no,
                            'batch_id' => $item->batch_id,
                            'expiry_date' => $item->expiry_date,
                            'qty' => $item->qty,
                            'free_qty' => $item->free_qty,
                            'sale_rate' => $item->sale_rate,
                            'discount_percent' => $item->discount_percent,
                            'mrp' => $item->mrp,
                            'amount' => $item->amount,
                            'cgst_percent' => $item->cgst_percent,
                            'sgst_percent' => $item->sgst_percent,
                            'cess_percent' => $item->cess_percent,
                            'hsn_code' => $item->hsn_code,
                            'packing' => $item->packing,
                            'unit' => $item->unit,
                            'company_name' => $item->company_name,
                            'case_qty' => 0, // Not stored in transaction items
                            'box_qty' => 0, // Not stored in transaction items
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transaction details'
            ], 500);
        }
    }
    
    /**
     * Update Sale Transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        try {
            $transaction = SaleTransaction::findOrFail($id);
            
            // Validate request
            $validated = $request->validate([
                'date' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
            ]);
            
            DB::beginTransaction();
            
            // Check if this is a TEMP transaction being converted to a real invoice
            $wasTemp = $transaction->series === 'TEMP' || str_starts_with($transaction->invoice_no, 'TEMP-');
            $newSeries = $request->input('series', 'SB');
            $newInvoiceNo = $transaction->invoice_no;
            
            // If it was a TEMP transaction and is being modified with actual items,
            // generate a new proper invoice number
            if ($wasTemp && $newSeries !== 'TEMP') {
                $newInvoiceNo = $this->generateInvoiceNo();
                $newSeries = 'SB'; // Default series for regular invoices
                
                Log::info('Converting TEMP transaction to regular invoice', [
                    'old_invoice_no' => $transaction->invoice_no,
                    'new_invoice_no' => $newInvoiceNo,
                    'transaction_id' => $transaction->id
                ]);
            }
            
            // Update master record
            $transaction->update([
                'invoice_no' => $newInvoiceNo,
                'series' => $newSeries,
                'sale_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'customer_id' => $request->input('customer_id'),
                'salesman_id' => $request->input('salesman_id'),
                'cash_flag' => $request->input('cash', 'N'),
                'transfer_flag' => $request->input('transfer', 'N'),
                'remarks' => $request->input('remarks'),
                'nt_amount' => $request->input('nt_amount', 0),
                'sc_amount' => $request->input('sc_amount', 0),
                'ft_amount' => $request->input('ft_amount', 0),
                'dis_amount' => $request->input('dis_amount', 0),
                'scm_amount' => $request->input('scm_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'net_amount' => $request->input('net_amount', 0),
                'scm_percent' => $request->input('scm_percent', 0),
                'balance_amount' => $request->input('net_amount', 0),
                'updated_by' => Auth::id(),
            ]);
            
            // Get old items to restore batch quantities
            $oldItems = $transaction->items()->get();
            
            // Restore batch quantities for old items
            foreach ($oldItems as $oldItem) {
                if ($oldItem->batch_id) {
                    $batch = Batch::find($oldItem->batch_id);
                    if ($batch) {
                        $restoredQty = floatval($oldItem->qty);
                        $batch->total_qty = $batch->total_qty + $restoredQty;
                        $batch->qty = $batch->qty + $restoredQty;
                        $batch->save();
                        
                        Log::info('✅ Restored batch quantity for old item', [
                            'batch_id' => $batch->id,
                            'batch_no' => $batch->batch_no,
                            'restored_qty' => $restoredQty,
                            'new_total_qty' => $batch->total_qty
                        ]);
                    }
                }
            }
            
            // Delete old items
            $transaction->items()->delete();
            
            // Create new items
            foreach ($request->input('items') as $index => $itemData) {
                $itemCode = $itemData['item_code'] ?? '';
                
                // Try to find item
                $item = null;
                if (!empty($itemCode)) {
                    $item = Item::where('bar_code', $itemCode)->first();
                    if (!$item) {
                        $item = Item::find($itemCode);
                    }
                }
                
                if (!$item && !empty($itemData['item_name'])) {
                    $item = Item::where('name', $itemData['item_name'])->first();
                }
                
                // Calculate amounts
                $qty = floatval($itemData['qty']);
                $rate = floatval($itemData['rate']);
                $discountPercent = floatval($itemData['discount'] ?? 0);
                
                $amount = $qty * $rate;
                $discountAmount = $amount * ($discountPercent / 100);
                $amountAfterDiscount = $amount - $discountAmount;
                
                $cgstPercent = $item ? floatval($item->cgst_percent ?? 0) : 0;
                $sgstPercent = $item ? floatval($item->sgst_percent ?? 0) : 0;
                $cessPercent = $item ? floatval($item->cess_percent ?? 0) : 0;
                
                $cgstAmount = $amountAfterDiscount * ($cgstPercent / 100);
                $sgstAmount = $amountAfterDiscount * ($sgstPercent / 100);
                $cessAmount = $amountAfterDiscount * ($cessPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount + $cessAmount;
                
                $netAmount = $amountAfterDiscount + $taxAmount;
                
                // Reduce batch quantity if batch_id is provided
                $batchId = $itemData['batch_id'] ?? null;
                if ($batchId) {
                    $batch = Batch::find($batchId);
                    if ($batch) {
                        $soldQty = floatval($qty);
                        
                        Log::info('Attempting to reduce batch quantity', [
                            'batch_id' => $batchId,
                            'batch_no' => $batch->batch_no,
                            'current_total_qty' => $batch->total_qty,
                            'current_qty' => $batch->qty,
                            'sold_qty' => $soldQty
                        ]);
                        
                        // Check if sufficient quantity available
                        if ($batch->total_qty >= $soldQty) {
                            // Reduce batch quantity
                            $oldTotalQty = $batch->total_qty;
                            $oldQty = $batch->qty;
                            
                            $batch->total_qty = $batch->total_qty - $soldQty;
                            $batch->qty = max(0, $batch->qty - $soldQty);
                            
                            $saved = $batch->save();
                            
                            if ($saved) {
                                Log::info('✅ Batch quantity reduced successfully', [
                                    'batch_id' => $batchId,
                                    'batch_no' => $batch->batch_no,
                                    'item_name' => $batch->item_name,
                                    'old_total_qty' => $oldTotalQty,
                                    'old_qty' => $oldQty,
                                    'sold_qty' => $soldQty,
                                    'new_total_qty' => $batch->total_qty,
                                    'new_qty' => $batch->qty
                                ]);
                            }
                        } else {
                            Log::warning('⚠️ Insufficient batch quantity', [
                                'batch_id' => $batchId,
                                'batch_no' => $batch->batch_no,
                                'available_qty' => $batch->total_qty,
                                'requested_qty' => $soldQty
                            ]);
                        }
                    }
                }
                
                SaleTransactionItem::create([
                    'sale_transaction_id' => $transaction->id,
                    'item_id' => $item ? $item->id : null,
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'item_code' => $itemCode ?: '',
                    'item_name' => $itemData['item_name'] ?? ($item ? $item->name : ''),
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $itemData['expiry'] ?? null,
                    'qty' => $qty,
                    'free_qty' => floatval($itemData['free_qty'] ?? 0),
                    'sale_rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? ($item ? $item->mrp : 0)),
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'amount' => $amount,
                    'net_amount' => $netAmount,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cess_percent' => $cessPercent,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'cess_amount' => $cessAmount,
                    'tax_amount' => $taxAmount,
                    'unit' => $item ? ($item->unit ?? null) : null,
                    'packing' => $item ? ($item->packing ?? null) : null,
                    'company_name' => $item ? ($item->company_short_name ?? null) : null,
                    'hsn_code' => $item ? ($item->hsn_code ?? null) : null,
                    'row_order' => $itemData['row_order'] ?? $index,
                ]);
            }
            
            DB::commit();
            
            $message = 'Sale transaction updated successfully';
            if ($wasTemp && $newSeries !== 'TEMP') {
                $message = 'Transaction converted from temporary to Invoice No: ' . $newInvoiceNo;
                
                // Send email notification
                try {
                    $user = Auth::user();
                    $notificationEmail = $user->notification_email ?? $user->email;
                    
                    if ($notificationEmail) {
                        \Illuminate\Support\Facades\Mail::to($notificationEmail)->send(
                            new \App\Mail\TransactionFinalizedMail($transaction->fresh(['items', 'customer', 'salesman']))
                        );
                        
                        Log::info('Transaction finalization email sent', [
                            'transaction_id' => $transaction->id,
                            'invoice_no' => $newInvoiceNo,
                            'email' => $notificationEmail
                        ]);
                    }
                } catch (\Exception $e) {
                    // Don't fail the transaction if email fails
                    Log::error('Failed to send transaction email', [
                        'error' => $e->getMessage(),
                        'transaction_id' => $transaction->id
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'invoice_no' => $transaction->invoice_no,
                'converted_from_temp' => $wasTemp && $newSeries !== 'TEMP',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Generate next invoice number (per organization)
 */
private function generateInvoiceNo()
{
    $orgId = auth()->user()->organization_id ?? 1;
    
    // Only consider invoices with proper INV-XXXXXX format
    $lastTransaction = SaleTransaction::withoutGlobalScopes()
        ->where('organization_id', $orgId)
        ->where('invoice_no', 'LIKE', 'INV-%')
        ->orderByRaw('CAST(SUBSTRING(invoice_no, 5) AS UNSIGNED) DESC')
        ->first();
    $nextNumber = $lastTransaction ? (intval(preg_replace('/[^0-9]/', '', $lastTransaction->invoice_no)) + 1) : 1;
    return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}

/**
 * Generate TEMP series invoice number (API endpoint)
 */
public function getNextTempInvoiceNo()
{
    try {
        $nextInvoiceNo = $this->generateTempInvoiceNo();
        
        return response()->json([
            'success' => true,
            'next_invoice_no' => $nextInvoiceNo
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generating TEMP invoice number'
        ], 500);
    }
}

/**
 * Generate TEMP series invoice number (per organization)
 */
private function generateTempInvoiceNo()
{
    $orgId = auth()->user()->organization_id ?? 1;
    
    // Only consider invoices with TEMP-XXXXXX format
    $lastTransaction = SaleTransaction::withoutGlobalScopes()
        ->where('organization_id', $orgId)
        ->where('invoice_no', 'LIKE', 'TEMP-%')
        ->orderByRaw('CAST(SUBSTRING(invoice_no, 6) AS UNSIGNED) DESC')
        ->first();
    $nextNumber = $lastTransaction ? (intval(preg_replace('/[^0-9]/', '', $lastTransaction->invoice_no)) + 1) : 1;
    return 'TEMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}

    /**
     * Calculate summary amounts from items data
     */
    private function calculateSummaryAmounts($itemsData)
    {
        $ntAmount = 0;      // Total before discount
        $disAmount = 0;     // Total discount
        $ftAmount = 0;      // Total after discount (before tax)
        $taxAmount = 0;     // Total tax
        $netAmount = 0;     // Final amount
        
        foreach ($itemsData as $itemData) {
            $qty = floatval($itemData['qty'] ?? 0);
            $rate = floatval($itemData['rate'] ?? 0);
            $discountPercent = floatval($itemData['discount'] ?? 0);
            
            // Support both 'item_code' and 'code' fields
            $itemCode = $itemData['item_code'] ?? $itemData['code'] ?? null;
            
            // Get item for GST info
            $item = null;
            if ($itemCode) {
                $item = Item::where('bar_code', $itemCode)
                    ->orWhere('id', $itemCode)
                    ->first();
            }
            
            $amount = $qty * $rate;
            $discountAmt = $amount * ($discountPercent / 100);
            $amountAfterDiscount = $amount - $discountAmt;
            
            if ($item) {
                $cgst = floatval($item->cgst_percent ?? 0);
                $sgst = floatval($item->sgst_percent ?? 0);
                $cess = floatval($item->cess_percent ?? 0);
                
                $tax = $amountAfterDiscount * (($cgst + $sgst + $cess) / 100);
            } else {
                $tax = 0;
            }
            
            $ntAmount += $amount;
            $disAmount += $discountAmt;
            $ftAmount += $amountAfterDiscount;
            $taxAmount += $tax;
        }
        
        $netAmount = $ftAmount + $taxAmount;
        
        return [
            'nt_amount' => round($ntAmount, 2),
            'dis_amount' => round($disAmount, 2),
            'ft_amount' => round($ftAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'net_amount' => round($netAmount, 2),
        ];
    }

    /**
     * Display list of all sale invoices with filters
     */
    public function invoices(Request $request)
    {
        $query = SaleTransaction::with(['customer', 'salesman'])
            ->orderBy('sale_date', 'desc')
            ->orderBy('invoice_no', 'desc');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filterBy = $request->input('filter_by', 'customer_name');
            
            switch ($filterBy) {
                case 'customer_name':
                    $query->whereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                    break;
                case 'invoice_no':
                    $query->where('invoice_no', 'like', '%' . $search . '%');
                    break;
                case 'salesman_name':
                    $query->whereHas('salesman', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                    break;
                case 'invoice_amount':
                    $query->where('net_amount', '>=', floatval($search));
                    break;
            }
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('sale_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('sale_date', '<=', $request->input('date_to'));
        }
        
        $sales = $query->paginate(10);
        
        return view('admin.sale.invoices', compact('sales'));
    }

    /**
     * Display detailed view of a sale transaction
     */
    public function show($id)
    {
        $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])->findOrFail($id);
        
        return view('admin.sale.show', compact('transaction'));
    }

    /**
     * Delete a sale transaction
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $transaction = SaleTransaction::findOrFail($id);
            
            // Restore batch quantities before deleting
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $batch->total_qty += $item->qty;
                        $batch->qty += $item->qty;
                        $batch->save();
                        
                        Log::info('Batch quantity restored', [
                            'batch_id' => $batch->id,
                            'batch_no' => $batch->batch_no,
                            'restored_qty' => $item->qty,
                            'new_total_qty' => $batch->total_qty
                        ]);
                    }
                }
            }
            
            // Delete items first
            $transaction->items()->delete();
            
            // Delete transaction
            $transaction->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale transaction deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Transaction Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sale transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer's total outstanding due amount
     */
    public function getCustomerDue($customerId)
    {
        try {
            // Sum of all balance_amount for this customer
            $totalDue = SaleTransaction::where('customer_id', $customerId)
                ->sum('balance_amount');
            
            return response()->json([
                'success' => true,
                'due_amount' => $totalDue ?? 0,
                'formatted_due' => number_format($totalDue ?? 0, 2)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching customer due: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer due'
            ], 500);
        }
    }

    /**
     * Save discount to company
     */
    public function saveCompanyDiscount(Request $request)
    {
        try {
            $companyId = $request->input('company_id');
            $discountPercent = $request->input('discount_percent');
            
            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company ID is required'
                ], 400);
            }
            
            $company = \App\Models\Company::find($companyId);
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }
            
            $company->dis_on_sale_percent = $discountPercent;
            $company->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Company discount saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving company discount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving company discount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save discount to item
     */
    public function saveItemDiscount(Request $request)
    {
        try {
            $itemId = $request->input('item_id');
            $discountPercent = $request->input('discount_percent');
            
            if (!$itemId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ID is required'
                ], 400);
            }
            
            $item = Item::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }
            
            $item->fixed_dis_percent = $discountPercent;
            $item->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Item discount saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving item discount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving item discount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print invoice in specified format
     * Format 1: Full A4 Tax Invoice
     * Format 2: Half Page (A5)
     */
    public function printInvoice($id, Request $request)
    {
        try {
            $format = $request->get('format', 1);
            $autoPrint = $request->get('auto_print', false);
            
            $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])
                ->findOrFail($id);
            
            $customer = $transaction->customer;
            $organization = \App\Models\Organization::find(auth()->user()->organization_id);
            
            $viewName = $format == 2 
                ? 'admin.sale.invoice-print-format2' 
                : 'admin.sale.invoice-print-format1';
            
            return view($viewName, [
                'transaction' => $transaction,
                'customer' => $customer,
                'organization' => $organization,
                'autoPrint' => $autoPrint
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error printing invoice: ' . $e->getMessage());
            return back()->with('error', 'Error printing invoice: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP for email verification before sending transaction email
     */
    public function sendEmailOtp($id)
    {
        try {
            $user = Auth::user();
            $notificationEmail = $user->notification_email ?? $user->email;
            
            if (!$notificationEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'No notification email configured. Please update your profile settings.'
                ], 400);
            }
            
            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Delete old OTPs for this user
            \DB::table('email_otps')
                ->where('user_id', $user->user_id)
                ->where('verified', false)
                ->delete();
            
            // Store OTP in database
            \DB::table('email_otps')->insert([
                'user_id' => $user->user_id,
                'email' => $notificationEmail,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
                'verified' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Send OTP email
            \Illuminate\Support\Facades\Mail::to($notificationEmail)->send(
                new \App\Mail\OtpMail($otp, $user->full_name ?? $user->username)
            );
            
            Log::info('OTP sent for transaction email', [
                'user_id' => $user->user_id,
                'email' => $notificationEmail,
                'transaction_id' => $id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email: ' . $notificationEmail,
                'email' => $notificationEmail
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'transaction_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP and send transaction email
     */
    public function verifyOtpAndSendEmail(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'otp' => 'required|string|size:6'
            ]);
            
            $user = Auth::user();
            $notificationEmail = $user->notification_email ?? $user->email;
            
            // Find valid OTP
            $otpRecord = \DB::table('email_otps')
                ->where('user_id', $user->user_id)
                ->where('email', $notificationEmail)
                ->where('otp', $validated['otp'])
                ->where('verified', false)
                ->where('expires_at', '>', now())
                ->first();
            
            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please try again.'
                ], 400);
            }
            
            // Mark OTP as verified
            \DB::table('email_otps')
                ->where('id', $otpRecord->id)
                ->update(['verified' => true]);
            
            // Now send the transaction email
            $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])->findOrFail($id);
            
            \Illuminate\Support\Facades\Mail::to($notificationEmail)->send(
                new \App\Mail\TransactionFinalizedMail($transaction)
            );
            
            Log::info('Transaction email sent after OTP verification', [
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'email' => $notificationEmail,
                'user_id' => $user->user_id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully!',
                'email' => $notificationEmail
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to verify OTP and send email', [
                'error' => $e->getMessage(),
                'transaction_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Share transaction on WhatsApp
     */
    public function shareOnWhatsApp($id)
    {
        try {
            $transaction = SaleTransaction::with(['items', 'customer', 'salesman'])->findOrFail($id);
            
            // Check if customer has phone number
            if (!$transaction->customer || !$transaction->customer->mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer phone number not found. Please update customer details.'
                ], 400);
            }
            
            // Generate PDF and get shareable link
            $pdfData = $this->generateShareablePDF($transaction);
            
            // Format phone number
            $phone = preg_replace('/[^0-9]/', '', $transaction->customer->mobile);
            if (!str_starts_with($phone, '91') && strlen($phone) == 10) {
                $phone = '91' . $phone;
            }
            
            // Create WhatsApp message
            $message = $this->createWhatsAppMessage($transaction, $pdfData['url']);
            
            // Log share activity
            \Log::info('WhatsApp share initiated', [
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'customer' => $transaction->customer->name,
                'phone' => $phone
            ]);
            
            return response()->json([
                'success' => true,
                'whatsapp_url' => "https://wa.me/{$phone}?text=" . urlencode($message),
                'pdf_url' => $pdfData['url'],
                'phone' => $phone
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to prepare WhatsApp share', [
                'error' => $e->getMessage(),
                'transaction_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare WhatsApp share: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate shareable PDF and return public URL
     */
    private function generateShareablePDF($transaction)
    {
        // Find receipt image path
        $receiptImagePath = null;
        
        if ($transaction->receipt_path) {
            $receiptPath = $transaction->receipt_path;
            $pathsToTry = [
                public_path($receiptPath),
                storage_path('app/public/' . str_replace('storage/', '', $receiptPath)),
            ];
            
            foreach ($pathsToTry as $path) {
                if (file_exists($path)) {
                    $receiptImagePath = $path;
                    break;
                }
            }
        }
        
        // Generate PDF
        $pdf = \PDF::loadView('pdf.invoice-with-receipt', [
            'transaction' => $transaction,
            'receiptImagePath' => $receiptImagePath
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        // Create temp directory if it doesn't exist
        $tempDir = public_path('temp/invoices');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Save PDF to public/temp/invoices
        $filename = 'invoice_' . $transaction->invoice_no . '_' . time() . '.pdf';
        $path = $tempDir . '/' . $filename;
        $pdf->save($path);
        
        // Generate public URL
        $url = url('temp/invoices/' . $filename);
        
        return [
            'url' => $url,
            'filename' => $filename,
            'path' => $path
        ];
    }
    
    /**
     * Create formatted WhatsApp message
     */
    private function createWhatsAppMessage($transaction, $pdfUrl)
    {
        $customerName = $transaction->customer->name ?? 'Customer';
        $invoiceNo = $transaction->invoice_no;
        $date = $transaction->sale_date->format('d-m-Y');
        $total = number_format($transaction->net_amount, 2);
        
        $message = "Hi *{$customerName}*,\n\n";
        $message .= "Your invoice is ready! 📄\n\n";
        $message .= "📋 *Invoice Details:*\n";
        $message .= "• Invoice No: {$invoiceNo}\n";
        $message .= "• Date: {$date}\n";
        $message .= "• Total Amount: ₹{$total}\n\n";
        
        // Add top 5 items
        if ($transaction->items && $transaction->items->count() > 0) {
            $message .= "📦 *Items:*\n";
            foreach ($transaction->items->take(5) as $item) {
                $qty = $item->qty;
                if ($item->free_qty > 0) {
                    $qty .= ' + ' . $item->free_qty . ' Free';
                }
                $message .= "• {$item->item_name} - {$qty} × ₹" . number_format($item->sale_rate, 2) . "\n";
            }
            
            if ($transaction->items->count() > 5) {
                $remaining = $transaction->items->count() - 5;
                $message .= "• ... and {$remaining} more item(s)\n";
            }
            $message .= "\n";
        }
        
        $message .= "📥 *Download PDF Invoice:*\n";
        $message .= "{$pdfUrl}\n\n";
        $message .= "Thank you for your business! 🙏\n";
        $message .= "- " . config('app.name');
        
        return $message;
    }

}
