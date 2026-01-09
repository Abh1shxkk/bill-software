<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseChallanTransaction;
use App\Models\PurchaseChallanTransactionItem;
use App\Models\Batch;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SalesMan;
use App\Models\PendingOrder;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseChallanTransactionController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display purchase challan transaction form
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextChallanNo = $this->generateChallanNo();
        
        return view('admin.purchase-challan.transaction', compact('suppliers', 'salesmen', 'items', 'nextChallanNo'));
    }

    /**
     * Display purchase challan modification form
     */
    public function modification($challan_no = null)
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        
        // If challan_no is provided, we can pre-populate the form
        $preloadChallanNo = $challan_no;
        
        return view('admin.purchase-challan.modification', compact('suppliers', 'salesmen', 'items', 'preloadChallanNo'));
    }

    /**
     * Display purchase challan invoices listing page
     */
    public function invoices(Request $request)
    {
        $query = PurchaseChallanTransaction::with('supplier');

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
                    
                case 'challan_no':
                    $query->where('challan_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'supplier_invoice_no':
                    $query->where('supplier_invoice_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'challan_amount':
                    if (is_numeric($searchTerm)) {
                        $query->where('net_amount', '>=', $searchTerm);
                    }
                    break;
            }
        }

        // Apply invoiced status filter
        if ($request->filled('invoiced_status')) {
            $isInvoiced = $request->get('invoiced_status') === 'yes';
            $query->where('is_invoiced', $isInvoiced);
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('challan_date', [
                $request->get('date_from'),
                $request->get('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('challan_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('challan_date', '<=', $request->get('date_to'));
        }

        // Order by latest first
        $query->orderBy('challan_date', 'desc')->orderBy('id', 'desc');

        // Get paginated results (10 per page for Load More)
        $challans = $query->paginate(10)->withQueryString();

        return view('admin.purchase-challan.invoices', compact('challans'));
    }

    /**
     * Show challan details page
     */
    public function show($id)
    {
        $challan = PurchaseChallanTransaction::with(['supplier', 'items.item.company'])
            ->findOrFail($id);
        
        return view('admin.purchase-challan.show', compact('challan'));
    }

    /**
     * Get invoice list for modification modal
     */
    public function getInvoiceList()
    {
        try {
            $invoices = PurchaseChallanTransaction::with('supplier')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'challan_no' => $transaction->challan_no,
                        'supplier_invoice_no' => $transaction->supplier_invoice_no,
                        'challan_date' => $transaction->challan_date,
                        'supplier_name' => $transaction->supplier->name ?? 'N/A',
                        'net_amount' => $transaction->net_amount,
                        'is_invoiced' => $transaction->is_invoiced,
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
     * Get purchase challan invoices by supplier for modal
     */
    public function getSupplierChallans($supplierId)
    {
        try {
            $challans = PurchaseChallanTransaction::with(['supplier', 'items'])
                ->where('supplier_id', $supplierId)
                ->where('is_invoiced', false) // Only non-invoiced challans
                ->orderBy('challan_date', 'desc')
                ->get()
                ->map(function($challan) {
                    // Calculate total amount from items
                    $totalAmount = $challan->items->sum('net_amount');
                    
                    return [
                        'id' => $challan->id,
                        'challan_no' => $challan->challan_no,
                        'supplier_invoice_no' => $challan->supplier_invoice_no,
                        'challan_date' => $challan->challan_date ? $challan->challan_date->format('Y-m-d') : null,
                        'supplier_invoice_date' => $challan->supplier_invoice_date ? $challan->supplier_invoice_date->format('Y-m-d') : null,
                        'net_amount' => $totalAmount > 0 ? $totalAmount : $challan->net_amount,
                        'status' => $challan->status,
                        'is_invoiced' => $challan->is_invoiced,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'challans' => $challans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier challans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching supplier challans'
            ], 500);
        }
    }

    /**
     * Get all pending challans (not invoiced) for modification modal
     */
    public function getAllPendingChallans()
    {
        try {
            $challans = PurchaseChallanTransaction::with(['supplier', 'items'])
                ->where('is_invoiced', false) // Only non-invoiced challans
                ->orderBy('challan_date', 'desc')
                ->orderBy('challan_no', 'desc')
                ->get()
                ->map(function($challan) {
                    // Calculate total amount from items
                    $totalAmount = $challan->items->sum('net_amount');
                    
                    return [
                        'id' => $challan->id,
                        'challan_no' => $challan->challan_no,
                        'supplier_invoice_no' => $challan->supplier_invoice_no,
                        'challan_date' => $challan->challan_date ? $challan->challan_date->format('Y-m-d') : null,
                        'supplier_name' => $challan->supplier->name ?? 'N/A',
                        'net_amount' => $totalAmount > 0 ? $totalAmount : $challan->net_amount,
                        'status' => $challan->status,
                        'is_invoiced' => $challan->is_invoiced,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'challans' => $challans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all pending challans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pending challans'
            ], 500);
        }
    }

    /**
     * Get ALL challans (pending and invoiced) for modification modal
     */
    public function getAllChallans()
    {
        try {
            $challans = PurchaseChallanTransaction::with(['supplier', 'items'])
                ->orderBy('challan_date', 'desc')
                ->orderBy('challan_no', 'desc')
                ->get()
                ->map(function($challan) {
                    // Calculate total amount from items
                    $totalAmount = $challan->items->sum('net_amount');
                    
                    return [
                        'id' => $challan->id,
                        'challan_no' => $challan->challan_no,
                        'supplier_invoice_no' => $challan->supplier_invoice_no,
                        'challan_date' => $challan->challan_date ? $challan->challan_date->format('Y-m-d') : null,
                        'supplier_name' => $challan->supplier->name ?? 'N/A',
                        'net_amount' => $totalAmount > 0 ? $totalAmount : $challan->net_amount,
                        'status' => $challan->status,
                        'is_invoiced' => $challan->is_invoiced,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'challans' => $challans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all challans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching challans'
            ], 500);
        }
    }

    /**
     * Fetch bill by challan number for modification
     */
    public function fetchBill($identifier)
    {
        try {
            // Try to find by challan_no first, then by supplier_invoice_no
            $transaction = PurchaseChallanTransaction::with(['supplier', 'items'])
                ->where(function($query) use ($identifier) {
                    $query->where('challan_no', $identifier)
                          ->orWhere('supplier_invoice_no', $identifier);
                })
                ->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challan not found with Challan No or Supplier Invoice No: ' . $identifier
                ]);
            }
            
            // Format bill data
            $billData = [
                'transaction_id' => $transaction->id,
                'challan_no' => $transaction->challan_no,
                'supplier_invoice_no' => $transaction->supplier_invoice_no,
                'challan_date' => $transaction->challan_date ? $transaction->challan_date->format('Y-m-d') : null,
                'supplier_invoice_date' => $transaction->supplier_invoice_date ? $transaction->supplier_invoice_date->format('Y-m-d') : null,
                'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                'supplier_id' => (string)($transaction->supplier_id ?? ''),
                'supplier_name' => $transaction->supplier->name ?? '',
                'cash' => strtoupper($transaction->cash_flag ?? 'N'),
                'transfer' => strtoupper($transaction->transfer_flag ?? 'N'),
                'remarks' => $transaction->remarks ?? '',
                
                // Summary amounts
                'nt_amt' => $transaction->nt_amount,
                'sc_amt' => $transaction->sc_amount,
                'ft_amt' => $transaction->ft_amount,
                'dis_amt' => $transaction->dis_amount,
                'scm_amt' => $transaction->scm_amount,
                'tax_amt' => $transaction->tax_amount,
                'net_amt' => $transaction->net_amount,
                'scm_percent' => $transaction->scm_percent,
                'tcs_amt' => $transaction->tcs_amount,
                'excise_amt' => $transaction->excise_amount,
                'is_invoiced' => $transaction->is_invoiced,
                
                // Items
                'items' => $transaction->items->map(function($item) {
                    return [
                        'item_code' => $item->item->id ?? $item->item_id,
                        'item_name' => $item->item->name ?? '',
                        'batch_number' => $item->batch_no,
                        'expiry_date' => $item->expiry_date,
                        'quantity' => $item->qty,
                        'free_quantity' => $item->free_qty,
                        'p_rate' => $item->purchase_rate,
                        'discount_percent' => $item->discount_percent,
                        'mrp' => $item->mrp,
                        's_rate' => $item->sale_rate ?? 0,
                        'cgst_percent' => $item->cgst_percent,
                        'sgst_percent' => $item->sgst_percent,
                        'cess_percent' => $item->cess_percent,
                        'net_amount' => $item->net_amount,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'purchase_challan', 'header.challan_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        // Validate request
        $validated = $request->validate([
            'header.challan_date' => 'required|date',
            'header.supplier_id' => 'required|exists:suppliers,supplier_id',
            'header.supplier_invoice_no' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.pur_rate' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');
            
            // Generate Challan Number if not provided
            if (empty($headerData['challan_no'])) {
                $headerData['challan_no'] = $this->generateChallanNo();
            }
            
            // Create Master Record
            $transaction = PurchaseChallanTransaction::create([
                'challan_no' => $headerData['challan_no'],
                'series' => $headerData['series'] ?? '01',
                'challan_date' => $headerData['challan_date'],
                'supplier_invoice_no' => $headerData['supplier_invoice_no'] ?? null,
                'supplier_invoice_date' => $headerData['supplier_invoice_date'] ?? null,
                'supplier_id' => $headerData['supplier_id'],
                'due_date' => $headerData['due_date'] ?? null,
                'cash_flag' => $headerData['cash_flag'] ?? 'N',
                'transfer_flag' => $headerData['transfer_flag'] ?? 'N',
                'remarks' => $headerData['remarks'] ?? null,
                
                // Summary amounts
                'nt_amount' => $headerData['nt_amount'] ?? 0,
                'sc_amount' => $headerData['sc_amount'] ?? 0,
                'ft_amount' => $headerData['ft_amount'] ?? 0,
                'dis_amount' => $headerData['dis_amount'] ?? 0,
                'scm_amount' => $headerData['scm_amount'] ?? 0,
                'tax_amount' => $headerData['tax_amount'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'scm_percent' => $headerData['scm_percent'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'excise_amount' => $headerData['excise_amount'] ?? 0,
                
                'is_invoiced' => false,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);
            
            // Create Detail Records (Items)
            foreach ($itemsData as $index => $itemData) {
                // Get item_id from item_code
                $item = Item::where('id', $itemData['item_code'])->first();
                
                if (!$item) {
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
                
                // Create Purchase Challan Transaction Item
                $challanItem = PurchaseChallanTransactionItem::create([
                    'purchase_challan_transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $itemData['qty'],
                    'free_qty' => $itemData['free_qty'] ?? 0,
                    'purchase_rate' => $itemData['pur_rate'],
                    'mrp' => $itemData['mrp'] ?? 0,
                    'sale_rate' => $itemData['s_rate'] ?? 0,
                    'discount_percent' => $itemData['dis_percent'] ?? 0,
                    'discount_amount' => $itemData['dis_amount'] ?? 0,
                    
                    // GST data
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'cess_percent' => $itemData['cess_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'cess_amount' => $itemData['cess_amount'] ?? 0,
                    'net_amount' => $itemData['net_amount'] ?? 0,
                    
                    'row_order' => $index + 1,
                ]);
                
                // Create or update batch for this item
                $this->createOrUpdateBatch($transaction, $challanItem, $item, $itemData, $expiryDate);
            }
            
            // Remove pending orders for items that were saved in this transaction
            $this->removePendingOrders($headerData['supplier_id'], $itemsData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase challan saved successfully',
                'challan_no' => $transaction->challan_no,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Challan Save Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = PurchaseChallanTransaction::findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'header.challan_date' => 'required|date',
            'header.supplier_id' => 'required|exists:suppliers,supplier_id',
            'header.supplier_invoice_no' => 'nullable|string|max:100',
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
                'challan_date' => $headerData['challan_date'],
                'supplier_invoice_no' => $headerData['supplier_invoice_no'] ?? null,
                'supplier_invoice_date' => $headerData['supplier_invoice_date'] ?? null,
                'supplier_id' => $headerData['supplier_id'],
                'due_date' => $headerData['due_date'] ?? null,
                'cash_flag' => $headerData['cash_flag'] ?? 'N',
                'transfer_flag' => $headerData['transfer_flag'] ?? 'N',
                'remarks' => $headerData['remarks'] ?? null,
                
                // Summary amounts
                'nt_amount' => $headerData['nt_amount'] ?? 0,
                'sc_amount' => $headerData['sc_amount'] ?? 0,
                'ft_amount' => $headerData['ft_amount'] ?? 0,
                'dis_amount' => $headerData['dis_amount'] ?? 0,
                'scm_amount' => $headerData['scm_amount'] ?? 0,
                'tax_amount' => $headerData['tax_amount'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'scm_percent' => $headerData['scm_percent'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'excise_amount' => $headerData['excise_amount'] ?? 0,
                
                'updated_by' => Auth::id(),
            ]);
            
            // Store old items data with their batch_ids for reference
            $oldItems = $transaction->items()->get()->keyBy(function($item) {
                // Key by item_id + batch_no + expiry for matching
                return $item->item_id . '_' . ($item->batch_no ?? '') . '_' . ($item->expiry_date ?? '');
            });
            
            // Delete old items (but don't touch batches yet)
            $transaction->items()->delete();
            
            // Insert new items and update batches with qty difference
            foreach ($itemsData as $index => $itemData) {
                $item = Item::where('id', $itemData['item_code'])->first();
                
                if (!$item) {
                    $item = Item::where('bar_code', $itemData['item_code'])->first();
                }
                
                if (!$item) {
                    throw new \Exception("Item not found: " . $itemData['item_code']);
                }
                
                $expiryDate = null;
                if (!empty($itemData['expiry_date'])) {
                    $expiryDate = $this->convertExpiryDate($itemData['expiry_date']);
                }
                
                // Find matching old item to get batch_id
                $matchKey = $item->id . '_' . ($itemData['batch_no'] ?? '') . '_' . ($expiryDate ?? '');
                $oldItem = $oldItems->get($matchKey);
                $existingBatchId = $oldItem ? $oldItem->batch_id : null;
                
                $newQty = $itemData['qty'] ?? 0;
                $newFreeQty = $itemData['free_qty'] ?? 0;
                
                $challanItem = PurchaseChallanTransactionItem::create([
                    'purchase_challan_transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'batch_id' => $existingBatchId,
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $newQty,
                    'free_qty' => $newFreeQty,
                    'purchase_rate' => $itemData['pur_rate'],
                    'mrp' => $itemData['mrp'] ?? 0,
                    'sale_rate' => $itemData['s_rate'] ?? 0,
                    'discount_percent' => $itemData['dis_percent'] ?? 0,
                    'discount_amount' => $itemData['dis_amount'] ?? 0,
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'cess_percent' => $itemData['cess_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'cess_amount' => $itemData['cess_amount'] ?? 0,
                    'net_amount' => $itemData['net_amount'] ?? 0,
                    'row_order' => $index + 1,
                ]);
                
                // Update batch with qty difference (not full qty)
                if ($existingBatchId) {
                    // Existing item - update batch with difference
                    $batch = Batch::find($existingBatchId);
                    if ($batch) {
                        $oldQty = $oldItem->qty ?? 0;
                        $oldFreeQty = $oldItem->free_qty ?? 0;
                        
                        // Calculate difference
                        $qtyDiff = $newQty - $oldQty;
                        $freeQtyDiff = $newFreeQty - $oldFreeQty;
                        $totalDiff = $qtyDiff + $freeQtyDiff;
                        
                        // Update batch with difference only
                        $batch->update([
                            'qty' => max(0, $batch->qty + $qtyDiff),
                            'free_qty' => max(0, $batch->free_qty + $freeQtyDiff),
                            'total_qty' => max(0, $batch->total_qty + $totalDiff),
                            // Update rates
                            'pur_rate' => $itemData['pur_rate'] ?? $batch->pur_rate,
                            's_rate' => $itemData['s_rate'] ?? $batch->s_rate,
                            'mrp' => $itemData['mrp'] ?? $batch->mrp,
                        ]);
                        
                        Log::info("Updated batch ID: {$batch->id} with qty diff: {$qtyDiff}, free_qty diff: {$freeQtyDiff}");
                    }
                } else {
                    // New item added during modification - create new batch
                    $this->createOrUpdateBatch($transaction, $challanItem, $item, $itemData, $expiryDate);
                }
                
                // Remove from oldItems collection (remaining will be deleted items)
                $oldItems->forget($matchKey);
            }
            
            // Handle removed items - subtract their qty from batches
            foreach ($oldItems as $removedItem) {
                if ($removedItem->batch_id) {
                    $batch = Batch::find($removedItem->batch_id);
                    if ($batch) {
                        $removedQty = $removedItem->qty ?? 0;
                        $removedFreeQty = $removedItem->free_qty ?? 0;
                        $removedTotalQty = $removedQty + $removedFreeQty;
                        
                        $batch->update([
                            'qty' => max(0, $batch->qty - $removedQty),
                            'free_qty' => max(0, $batch->free_qty - $removedFreeQty),
                            'total_qty' => max(0, $batch->total_qty - $removedTotalQty),
                        ]);
                        
                        Log::info("Removed item - subtracted from batch ID: {$batch->id}, qty: {$removedTotalQty}");
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase challan updated successfully',
                'challan_no' => $transaction->challan_no,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Challan Update Error: ' . $e->getMessage());
            
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
            $transaction = PurchaseChallanTransaction::findOrFail($id);
            
            // Check if already invoiced
            if ($transaction->is_invoiced) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete an invoiced challan'
                ], 400);
            }
            
            $transaction->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase challan deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Purchase Challan Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate next challan number
     */
    private function generateChallanNo()
    {
        $lastChallan = PurchaseChallanTransaction::orderBy('id', 'desc')->first();
        
        if ($lastChallan && $lastChallan->challan_no) {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastChallan->challan_no);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'PC' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Convert expiry date from MM/YY to YYYY-MM-DD
     */
    private function convertExpiryDate($expiryDate)
    {
        // If already in YYYY-MM-DD format, return as is
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiryDate)) {
            return $expiryDate;
        }
        
        // If in MM/YY format
        if (preg_match('/^(\d{2})\/(\d{2})$/', $expiryDate, $matches)) {
            $month = $matches[1];
            $year = '20' . $matches[2];
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            return "$year-$month-$day";
        }
        
        return null;
    }

    /**
     * Remove pending orders
     */
    private function removePendingOrders($supplierId, $items)
    {
        foreach ($items as $item) {
            // Get item_id from item_code
            $itemModel = Item::where('id', $item['item_code'])->first();
            if (!$itemModel) {
                $itemModel = Item::where('bar_code', $item['item_code'])->first();
            }
            
            if ($itemModel) {
                PendingOrder::where('supplier_id', $supplierId)
                    ->where('item_id', $itemModel->id)
                    ->delete();
            }
        }
    }

    /**
     * Get next transaction number
     */
    public function getNextChallanNo()
    {
        return response()->json([
            'success' => true,
            'challan_no' => $this->generateChallanNo()
        ]);
    }

    /**
     * Create or update batch for purchase challan item
     */
    private function createOrUpdateBatch($transaction, $challanItem, $item, $itemData, $expiryDate)
    {
        $batchNo = $itemData['batch_no'] ?? null;
        $qty = $itemData['qty'] ?? 0;
        $freeQty = $itemData['free_qty'] ?? 0;
        $totalQty = $qty + $freeQty;
        
        // Check if batch already exists for this item with same batch number and expiry
        $existingBatch = Batch::where('item_id', $item->id)
            ->where('batch_no', $batchNo)
            ->where('expiry_date', $expiryDate)
            ->where('is_deleted', 0)
            ->first();
        
        if ($existingBatch) {
            // Update existing batch - add quantity
            $existingBatch->update([
                'qty' => $existingBatch->qty + $qty,
                'free_qty' => $existingBatch->free_qty + $freeQty,
                'total_qty' => $existingBatch->total_qty + $totalQty,
                // Update rates if provided
                'pur_rate' => $itemData['pur_rate'] ?? $existingBatch->pur_rate,
                's_rate' => $itemData['s_rate'] ?? $existingBatch->s_rate,
                'mrp' => $itemData['mrp'] ?? $existingBatch->mrp,
                'ws_rate' => $itemData['ws_rate'] ?? $existingBatch->ws_rate,
                'spl_rate' => $itemData['spl_rate'] ?? $existingBatch->spl_rate,
            ]);
            
            // Update challan item with batch_id
            $challanItem->update(['batch_id' => $existingBatch->id]);
            
            Log::info("Updated existing batch ID: {$existingBatch->id} for item: {$item->id}, added qty: {$totalQty}");
        } else {
            // Create new batch
            $newBatch = Batch::create([
                'purchase_transaction_id' => null, // Will be set when actual invoice is created
                'purchase_transaction_item_id' => null,
                'item_id' => $item->id,
                'item_code' => $item->bar_code ?? $item->id,
                'item_name' => $item->name,
                'batch_no' => $batchNo,
                'bc' => $batchNo,
                'expiry_date' => $expiryDate,
                'qty' => $qty,
                'free_qty' => $freeQty,
                'total_qty' => $totalQty,
                'pur_rate' => $itemData['pur_rate'] ?? 0,
                's_rate' => $itemData['s_rate'] ?? $item->s_rate ?? 0,
                'mrp' => $itemData['mrp'] ?? $item->mrp ?? 0,
                'ws_rate' => $itemData['ws_rate'] ?? $item->ws_rate ?? 0,
                'spl_rate' => $itemData['spl_rate'] ?? $item->spl_rate ?? 0,
                'dis_percent' => $itemData['dis_percent'] ?? 0,
                'amount' => $itemData['amount'] ?? 0,
                'cgst_percent' => $itemData['cgst_percent'] ?? $item->cgst_percent ?? 0,
                'sgst_percent' => $itemData['sgst_percent'] ?? $item->sgst_percent ?? 0,
                'cess_percent' => $itemData['cess_percent'] ?? $item->cess_percent ?? 0,
                'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                'cess_amount' => $itemData['cess_amount'] ?? 0,
                'tax_amount' => ($itemData['cgst_amount'] ?? 0) + ($itemData['sgst_amount'] ?? 0) + ($itemData['cess_amount'] ?? 0),
                'net_amount' => $itemData['net_amount'] ?? 0,
                'unit' => $item->unit ?? '1',
                'packing' => $item->packing ?? '',
                'company_name' => $item->company->short_name ?? $item->company->name ?? '',
                'status' => 'active',
                'is_deleted' => 0,
                'remarks' => 'Created from Purchase Challan: ' . $transaction->challan_no,
            ]);
            
            // Update challan item with batch_id
            $challanItem->update(['batch_id' => $newBatch->id]);
            
            Log::info("Created new batch ID: {$newBatch->id} for item: {$item->id}, qty: {$totalQty}");
        }
    }

    /**
     * Get challan details for loading into purchase invoice
     */
    public function getChallanDetails($challanId)
    {
        try {
            $challan = PurchaseChallanTransaction::with(['supplier', 'items.item.company'])
                ->findOrFail($challanId);
            
            // Format the response for purchase module
            $challanData = [
                'id' => $challan->id,
                'challan_no' => $challan->challan_no,
                'supplier_invoice_no' => $challan->supplier_invoice_no,
                'challan_date' => $challan->challan_date ? $challan->challan_date->format('Y-m-d') : null,
                'supplier_invoice_date' => $challan->supplier_invoice_date ? $challan->supplier_invoice_date->format('Y-m-d') : null,
                'due_date' => $challan->due_date ? $challan->due_date->format('Y-m-d') : null,
                'supplier_id' => $challan->supplier_id,
                'supplier_name' => $challan->supplier->name ?? '',
                'cash_flag' => $challan->cash_flag,
                'transfer_flag' => $challan->transfer_flag,
                'remarks' => $challan->remarks,
                
                // Summary amounts
                'nt_amount' => $challan->nt_amount,
                'sc_amount' => $challan->sc_amount,
                'ft_amount' => $challan->ft_amount,
                'dis_amount' => $challan->dis_amount,
                'scm_amount' => $challan->scm_amount,
                'tax_amount' => $challan->tax_amount,
                'net_amount' => $challan->net_amount,
                'scm_percent' => $challan->scm_percent,
                'tcs_amount' => $challan->tcs_amount,
                'excise_amount' => $challan->excise_amount,
                
                // Items
                'items' => $challan->items->map(function($item) {
                    return [
                        'item_id' => $item->item_id,
                        'item_code' => $item->item->bar_code ?? $item->item->id,
                        'item_name' => $item->item->name ?? '',
                        'batch_no' => $item->batch_no,
                        'batch_id' => $item->batch_id,
                        'expiry_date' => $item->expiry_date ? date('m/y', strtotime($item->expiry_date)) : '',
                        'qty' => $item->qty,
                        'free_qty' => $item->free_qty,
                        'purchase_rate' => $item->purchase_rate,
                        'mrp' => $item->mrp,
                        'sale_rate' => $item->sale_rate,
                        'discount_percent' => $item->discount_percent,
                        'discount_amount' => $item->discount_amount,
                        'cgst_percent' => $item->cgst_percent,
                        'sgst_percent' => $item->sgst_percent,
                        'cess_percent' => $item->cess_percent,
                        'cgst_amount' => $item->cgst_amount,
                        'sgst_amount' => $item->sgst_amount,
                        'cess_amount' => $item->cess_amount,
                        'net_amount' => $item->net_amount,
                        'hsn_code' => $item->item->hsn_code ?? '',
                        'packing' => $item->item->packing ?? '',
                        'company_name' => $item->item->company->short_name ?? $item->item->company->name ?? '',
                    ];
                })->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'challan' => $challanData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching challan details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching challan details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark challan as invoiced
     */
    public function markAsInvoiced(Request $request, $challanId)
    {
        try {
            $challan = PurchaseChallanTransaction::findOrFail($challanId);
            
            $purchaseTransactionId = $request->input('purchase_transaction_id');
            
            $challan->update([
                'is_invoiced' => true,
                'status' => 'invoiced',
                'purchase_transaction_id' => $purchaseTransactionId,
            ]);
            
            Log::info("Challan {$challan->challan_no} marked as invoiced. Purchase Transaction ID: {$purchaseTransactionId}");
            
            return response()->json([
                'success' => true,
                'message' => 'Challan marked as invoiced'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking challan as invoiced: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking challan as invoiced'
            ], 500);
        }
    }
}
