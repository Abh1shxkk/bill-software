<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\PurchaseTransactionItem;
use App\Models\Item;
use App\Models\PurchaseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BatchController extends Controller
{
    /**
     * Display a listing of batches grouped by item (Available Batches - qty != 0)
     * Shows batches with positive OR negative quantities (not zero)
     */
    public function index(Request $request)
    {
        $itemId = $request->get('item_id');
        
        // Get batches from batches table grouped by item, batch_no, and expiry_date
        // Show batches with qty != 0 (current available quantity after sales/returns)
        $query = Batch::select(
            'item_id',
            'item_code',
            'item_name',
            'batch_no',
            'expiry_date',
            DB::raw('SUM(qty) as total_qty'), // Use qty field (current available) instead of total_qty
            DB::raw('AVG(pur_rate) as avg_pur_rate'),
            DB::raw('AVG(s_rate) as avg_s_rate'),
            DB::raw('AVG(mrp) as avg_mrp'),
            DB::raw('AVG(ws_rate) as avg_ws_rate'),
            DB::raw('AVG(spl_rate) as avg_spl_rate'),
            DB::raw('AVG(cgst_percent) as avg_cgst_percent'),
            DB::raw('AVG(sgst_percent) as avg_sgst_percent'),
            DB::raw('MAX(pur_rate) as max_rate'),
            DB::raw('MIN(pur_rate) as min_rate'),
            DB::raw('MIN(id) as first_batch_id')
        )
        ->where('is_deleted', 0)
        ->whereNotNull('batch_no')
        ->where('batch_no', '!=', '')
        ->groupBy('item_id', 'item_code', 'item_name', 'batch_no', 'expiry_date')
        ->havingRaw('SUM(qty) != 0') // Available batches based on current qty (after sales/returns)
        ->orderBy('item_name')
        ->orderBy('batch_no');
        
        // Filter by item if provided
        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        
        $batches = $query->get();
        
        // Group batches by item
        $groupedBatches = $batches->groupBy('item_id');
        
        // Get all items for dropdown
        $items = Item::where('is_deleted', '!=', 1)
            ->orderBy('name')
            ->get();
        
        $viewType = 'available'; // Indicate this is available batches view
        
        return view('admin.batches.index', compact('groupedBatches', 'items', 'itemId', 'viewType'));
    }
    
    /**
     * Get batches for a specific item (AJAX)
     * Returns batches with qty != 0 (positive or negative, not zero) for sale transactions
     */
    public function getItemBatches($itemId)
    {
        try {
            // Fetch batches directly from batches table
            // Return batches with qty != 0 (current available quantity after sales/returns)
            $batches = Batch::select(
                'id',
                'item_id',
                'item_code',
                'item_name',
                'batch_no',
                'expiry_date',
                'manufacturing_date',
                'total_qty',
                'qty',
                'free_qty',
                'pur_rate',
                's_rate',
                'mrp',
                'ws_rate',
                'spl_rate',
                'cost',
                'cost_gst',
                'cost_wfq',
                'packing',
                'unit',
                'company_name',
                'purchase_transaction_id'
            )
            ->where('item_id', $itemId)
            ->where('is_deleted', 0)
            ->whereNotNull('batch_no')
            ->where('batch_no', '!=', '')
            // Show all batches including negative qty for stock adjustment
            ->with(['transaction.supplier', 'item'])
            ->orderBy('expiry_date') // Order by expiry date first (FIFO)
            ->orderBy('batch_no')
            ->get();
            
            // Calculate total closing quantity across all batches for this item
            $totalClQty = Batch::where('item_id', $itemId)
                ->where('is_deleted', 0)
                ->sum('qty');
            
            // Format batches for response
            $formattedBatches = [];
            
            foreach ($batches as $batch) {
                // Get purchase date and invoice details from transaction
                $purchaseDate = null;
                $purchaseDateDisplay = 'N/A';
                $invoiceNo = null;
                $invoiceDate = null;
                $supplierName = 'N/A';
                
                if ($batch->transaction) {
                    $purchaseDate = $batch->transaction->bill_date;
                    $invoiceNo = $batch->transaction->invoice_no ?? null;
                    $invoiceDate = $batch->transaction->invoice_date ?? null;
                    
                    // Format purchase date for display (dd-mm-yy)
                    if ($purchaseDate) {
                        try {
                            if ($purchaseDate instanceof \Carbon\Carbon) {
                                $purchaseDateDisplay = $purchaseDate->format('d-m-y');
                            } else {
                                $dateObj = \Carbon\Carbon::parse($purchaseDate);
                                $purchaseDateDisplay = $dateObj->format('d-m-y');
                            }
                        } catch (\Exception $e) {
                            $purchaseDateDisplay = is_string($purchaseDate) ? $purchaseDate : 'N/A';
                        }
                    }
                    
                    // Get supplier name
                    if ($batch->transaction->supplier) {
                        $supplierName = $batch->transaction->supplier->name;
                    }
                }
                
                // Get item name
                $itemName = $batch->item_name ?? 'N/A';
                if ($batch->item) {
                    $itemName = $batch->item->name;
                }
                
                // Format expiry date
                $expiryDisplay = '---';
                if ($batch->expiry_date) {
                    try {
                        if ($batch->expiry_date instanceof \Carbon\Carbon) {
                            $expiryDisplay = $batch->expiry_date->format('m/Y');
                        } else {
                            $dateObj = \Carbon\Carbon::parse($batch->expiry_date);
                            $expiryDisplay = $dateObj->format('m/Y');
                        }
                    } catch (\Exception $e) {
                        $expiryDisplay = '---';
                    }
                }
                
                $formattedBatches[] = [
                    'id' => $batch->id, // Important: batch ID for quantity reduction
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => $batch->expiry_date,
                    'expiry_display' => $expiryDisplay,
                    'purchase_date' => $purchaseDate ? ($purchaseDate instanceof \Carbon\Carbon ? $purchaseDate->format('Y-m-d') : (is_string($purchaseDate) ? $purchaseDate : null)) : null,
                    'purchase_date_display' => $purchaseDateDisplay,
                    'total_qty' => $batch->total_qty ?? 0,
                    'qty' => $batch->qty ?? 0,
                    'free_qty' => $batch->free_qty ?? 0,
                    'pur_rate' => $batch->pur_rate ?? 0,
                    's_rate' => $batch->s_rate ?? 0,
                    'mrp' => $batch->mrp ?? 0,
                    'ws_rate' => $batch->ws_rate ?? 0,
                    'spl_rate' => $batch->spl_rate ?? 0,
                    'cost' => $batch->cost ?? 0,
                    'cost_gst' => $batch->cost_gst ?? 0,
                    'cost_wfq' => $batch->cost_wfq ?? 0,
                    'avg_pur_rate' => $batch->pur_rate ?? 0, // For compatibility
                    'avg_s_rate' => $batch->s_rate ?? 0, // For compatibility
                    'avg_mrp' => $batch->mrp ?? 0, // For compatibility
                    'avg_cost_gst' => $batch->cost_gst ?? 0, // For compatibility
                    'item_name' => $itemName,
                    'item_code' => $batch->item_code,
                    'packing' => $batch->packing ?? '1*10',
                    'unit' => $batch->unit ?? '1',
                    'company_name' => $batch->company_name ?? 'N/A',
                    'supplier_name' => $supplierName,
                    'total_cl_qty' => $totalClQty, // Total closing qty across all batches for this item
                    'invoice_no' => $invoiceNo,
                    'invoice_date' => $invoiceDate
                ];
            }
            
            return response()->json([
                'success' => true,
                'batches' => $formattedBatches
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching item batches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching batches'
            ], 500);
        }
    }
    
    /**
     * Show the form for editing a specific batch
     */
    public function edit($id)
    {
        // Get the batch from batches table
        $batch = Batch::with(['transaction', 'item', 'transaction.supplier', 'transactionItem'])
            ->findOrFail($id);
        
        // Get the purchase transaction item for compatibility (if needed)
        $purchaseItem = $batch->transactionItem;
        
        // Get all batches with same batch_no and item_id for quantity calculation
        $allBatchItems = Batch::where('item_id', $batch->item_id)
            ->where('batch_no', $batch->batch_no)
            ->where('is_deleted', 0);
        
        if ($batch->expiry_date) {
            $allBatchItems->where('expiry_date', $batch->expiry_date);
        } else {
            $allBatchItems->whereNull('expiry_date');
        }
        
        $allBatchItems = $allBatchItems->get();
        
        // Calculate totals
        $totalQty = $allBatchItems->sum('qty');
        $fifoQty = $totalQty;
        $mstQty = $totalQty;
        $actualQty = $totalQty;
        
        // Pass batch instead of purchaseItem for the view
        return view('admin.batches.edit', compact('batch', 'purchaseItem', 'allBatchItems', 'totalQty', 'fifoQty', 'mstQty', 'actualQty'));
    }
    
    /**
     * Update the specified batch
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'batch_no' => 'required|string|max:100',
                'bc' => 'nullable|in:Y,N',
                'bill_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'manufacturing_date' => 'nullable|date',
                'qty' => 'required|numeric|min:0',
                'pur_rate' => 'required|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                's_rate' => 'nullable|numeric|min:0',
                'ws_rate' => 'nullable|numeric|min:0',
                'spl_rate' => 'nullable|numeric|min:0',
                'n_rate' => 'nullable|numeric|min:0',
                'sale_scheme_plus' => 'nullable|string|max:10',
                'sale_scheme_minus' => 'nullable|string|max:10',
                'inc' => 'nullable|in:Y,N',
                'sc_amount' => 'nullable|numeric|min:0',
                'dis_percent' => 'nullable|numeric|min:0|max:100',
                'gst_pts' => 'nullable|numeric|min:0',
                'cost' => 'nullable|numeric|min:0',
                'cost_wfq' => 'nullable|numeric|min:0',
                'rate_diff' => 'nullable|numeric',
                'hold_breakage_expiry' => 'nullable|in:H,B,E',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Batch validation failed: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Get the batch from batches table
            $batch = Batch::findOrFail($id);
            
            // Parse expiry_date if provided (might be in YYYY-MM-01 format from MM/YYYY)
            $expiryDate = null;
            if ($request->expiry_date) {
                try {
                    $expiryDate = \Carbon\Carbon::parse($request->expiry_date);
                } catch (\Exception $e) {
                    // If parsing fails, try to parse as MM/YYYY
                    Log::warning('Error parsing expiry_date: ' . $e->getMessage());
                }
            }
            
            // Parse manufacturing_date if provided
            $mfgDate = null;
            if ($request->manufacturing_date) {
                try {
                    $mfgDate = \Carbon\Carbon::parse($request->manufacturing_date);
                } catch (\Exception $e) {
                    Log::warning('Error parsing manufacturing_date: ' . $e->getMessage());
                }
            }
            
            // Combine sale_scheme_plus and sale_scheme_minus into "1+1" format
            $saleScheme = null;
            if ($request->sale_scheme_plus !== null || $request->sale_scheme_minus !== null) {
                $plus = $request->sale_scheme_plus ?? '0';
                $minus = $request->sale_scheme_minus ?? '0';
                $saleScheme = $plus . '+' . $minus;
            }
            
            // Recalculate amount and totals
            $baseAmount = $request->qty * $request->pur_rate;
            $discountAmount = $baseAmount * (($request->dis_percent ?? 0) / 100);
            $amount = $baseAmount - $discountAmount;
            
            // Calculate total_qty
            $totalQty = $request->qty + ($request->free_qty ?? $batch->free_qty ?? 0);
            
            // Calculate rate_diff
            $rateDiff = ($request->s_rate ?? 0) - ($request->pur_rate ?? 0);
            
            // Update the batch
            $batch->update([
                'batch_no' => $request->batch_no,
                'bc' => $request->bc ?? $batch->bc,
                'expiry_date' => $expiryDate,
                'manufacturing_date' => $mfgDate,
                'qty' => $request->qty,
                'total_qty' => $totalQty,
                'pur_rate' => $request->pur_rate,
                'mrp' => $request->mrp ?? $batch->mrp,
                's_rate' => $request->s_rate ?? $batch->s_rate ?? 0,
                'ws_rate' => $request->ws_rate ?? $batch->ws_rate ?? 0,
                'spl_rate' => $request->spl_rate ?? $batch->spl_rate ?? 0,
                'n_rate' => $request->n_rate ?? $batch->n_rate ?? 0,
                'sale_scheme' => $saleScheme ?? $batch->sale_scheme,
                'inc' => $request->inc ?? $batch->inc ?? 'Y',
                'sc_amount' => $request->sc_amount ?? $batch->sc_amount ?? 0,
                'dis_percent' => $request->dis_percent ?? $batch->dis_percent,
                'gst_pts' => $request->gst_pts ?? $batch->gst_pts ?? 0,
                'cost' => $request->cost ?? $batch->cost ?? 0,
                'cost_wfq' => $request->cost_wfq ?? $batch->cost_wfq ?? 0,
                'rate_diff' => $rateDiff,
                'hold_breakage_expiry' => $request->hold_breakage_expiry ?? $batch->hold_breakage_expiry,
                'amount' => $amount,
            ]);
            
            // Also update the corresponding purchase_transaction_item for consistency
            if ($batch->transactionItem) {
                $batch->transactionItem->update([
                    'batch_no' => $request->batch_no,
                    'expiry_date' => $expiryDate,
                    'qty' => $request->qty,
                    'pur_rate' => $request->pur_rate,
                    'mrp' => $request->mrp ?? $batch->transactionItem->mrp,
                    's_rate' => $request->s_rate ?? $batch->transactionItem->s_rate ?? 0,
                    'ws_rate' => $request->ws_rate ?? $batch->transactionItem->ws_rate ?? 0,
                    'spl_rate' => $request->spl_rate ?? $batch->transactionItem->spl_rate ?? 0,
                    'dis_percent' => $request->dis_percent ?? $batch->transactionItem->dis_percent,
                    'amount' => $amount,
                ]);
            }
            
            // Update bill_date in purchase_transaction if provided
            if ($request->bill_date && $batch->transaction) {
                try {
                    $billDate = \Carbon\Carbon::parse($request->bill_date);
                    $batch->transaction->update([
                        'bill_date' => $billDate
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Error updating bill_date: ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Batch updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating batch: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating batch: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
    
    /**
     * Display the specified batch details
     */
    public function show($id)
    {
        try {
            // Get the batch from batches table
            $batch = Batch::with(['transaction', 'item', 'transaction.supplier', 'transactionItem'])
                ->findOrFail($id);
            
            return view('admin.batches.show', compact('batch'));
            
        } catch (\Exception $e) {
            Log::error('Error fetching batch details: ' . $e->getMessage());
            return redirect()->route('admin.batches.index')
                ->with('error', 'Batch not found or error loading details.');
        }
    }
    
    /**
     * Get batch details for a specific batch (by batch_no and item_id)
     */
    public function getBatchDetails($itemId, $batchNo)
    {
        try {
            $batches = PurchaseTransactionItem::where('item_id', $itemId)
                ->where('batch_no', $batchNo)
                ->with(['transaction', 'transaction.supplier', 'item'])
                ->orderBy('expiry_date')
                ->get();
            
            if ($batches->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch not found'
                ], 404);
            }
            
            // Calculate totals
            $totalQty = $batches->sum('qty');
            $firstBatch = $batches->first();
            
            return response()->json([
                'success' => true,
                'batch' => [
                    'batch_no' => $batchNo,
                    'expiry_date' => $firstBatch->expiry_date,
                    'total_qty' => $totalQty,
                    'avg_pur_rate' => $batches->avg('pur_rate'),
                    'avg_mrp' => $batches->avg('mrp'),
                    'item_name' => $firstBatch->item_name,
                    'item_code' => $firstBatch->item_code,
                    'packing' => $firstBatch->packing ?? '1*10',
                    'items' => $batches->map(function($item) {
                        return [
                            'id' => $item->id,
                            'qty' => $item->qty,
                            'pur_rate' => $item->pur_rate,
                            'mrp' => $item->mrp,
                            'expiry_date' => $item->expiry_date,
                            'invoice_no' => $item->transaction->bill_no ?? '',
                            'invoice_date' => $item->transaction->bill_date ?? '',
                            'supplier_name' => $item->transaction->supplier->name ?? '',
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching batch details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching batch details'
            ], 500);
        }
    }
    
    /**
     * Display batches for a specific item (view page)
     */
    public function itemBatches($itemId)
    {
        try {
            $item = Item::findOrFail($itemId);
            
            // Get batches for this item
            $batches = PurchaseTransactionItem::select(
                'item_id',
                'item_code',
                'item_name',
                'batch_no',
                'expiry_date',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('AVG(pur_rate) as avg_pur_rate'),
                DB::raw('AVG(mrp) as avg_mrp'),
                DB::raw('MAX(pur_rate) as max_rate'),
                DB::raw('MIN(pur_rate) as min_rate')
            )
            ->where('item_id', $itemId)
            ->whereNotNull('batch_no')
            ->where('batch_no', '!=', '')
            ->groupBy('item_id', 'item_code', 'item_name', 'batch_no', 'expiry_date')
            ->orderBy('batch_no')
            ->orderBy('expiry_date')
            ->get();
            
            // Note: If batches is empty, view will show "No batches found" message
            
            // Get all items for dropdown
            $items = Item::where('is_deleted', '!=', 1)
                ->orderBy('name')
                ->get();
            
            return view('admin.batches.index', [
                'groupedBatches' => collect([$itemId => $batches]),
                'items' => $items,
                'itemId' => $itemId,
                'selectedItem' => $item
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching item batches: ' . $e->getMessage());
            return redirect()->route('admin.batches.index')
                ->with('error', 'Item not found or error loading batches.');
        }
    }
    
    /**
     * Display all batches (view page) - Shows ALL batches regardless of quantity
     */
    public function allBatches(Request $request)
    {
        $itemId = $request->get('item_id');
        
        // Get ALL batches from batches table grouped by item, batch_no, and expiry_date
        // NO quantity filter - show everything (positive, negative, and zero)
        $query = Batch::select(
            'item_id',
            'item_code',
            'item_name',
            'batch_no',
            'expiry_date',
            DB::raw('SUM(total_qty) as total_qty'),
            DB::raw('AVG(pur_rate) as avg_pur_rate'),
            DB::raw('AVG(s_rate) as avg_s_rate'),
            DB::raw('AVG(mrp) as avg_mrp'),
            DB::raw('AVG(ws_rate) as avg_ws_rate'),
            DB::raw('AVG(spl_rate) as avg_spl_rate'),
            DB::raw('AVG(cgst_percent) as avg_cgst_percent'),
            DB::raw('AVG(sgst_percent) as avg_sgst_percent'),
            DB::raw('MAX(pur_rate) as max_rate'),
            DB::raw('MIN(pur_rate) as min_rate'),
            DB::raw('MIN(id) as first_batch_id')
        )
        ->where('is_deleted', 0)
        ->whereNotNull('batch_no')
        ->where('batch_no', '!=', '')
        ->groupBy('item_id', 'item_code', 'item_name', 'batch_no', 'expiry_date')
        // NO havingRaw filter - show ALL batches regardless of quantity
        ->orderBy('item_name')
        ->orderBy('batch_no');
        
        // Filter by item if provided
        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        
        $batches = $query->get();
        
        // Group batches by item
        $groupedBatches = $batches->groupBy('item_id');
        
        // Get all items for dropdown
        $items = Item::where('is_deleted', '!=', 1)
            ->orderBy('name')
            ->get();
        
        $viewType = 'all'; // Indicate this is all batches view
        
        return view('admin.batches.index', compact('groupedBatches', 'items', 'itemId', 'viewType'));
    }
    
    /**
     * Display stock ledger for a specific batch
     */
    public function stockLedger($batchId)
    {
        try {
            // Get batch from purchase_transaction_items
            $batchItem = PurchaseTransactionItem::with(['transaction', 'item'])
                ->findOrFail($batchId);
            
            // Get all items with same batch_no and item_id
            $batchItems = PurchaseTransactionItem::where('item_id', $batchItem->item_id)
                ->where('batch_no', $batchItem->batch_no)
                ->with(['transaction', 'transaction.supplier'])
                ->orderBy('created_at')
                ->get();
            
            return view('admin.batches.stock-ledger', [
                'batchItem' => $batchItem,
                'batchItems' => $batchItems
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching batch stock ledger: ' . $e->getMessage());
            return redirect()->route('admin.batches.index')
                ->with('error', 'Batch not found or error loading stock ledger.');
        }
    }
    
    /**
     * Display expiry report
     */
    public function expiryReport(Request $request)
    {
        try {
            $days = $request->get('days', 30); // Default 30 days
            
            // Get batches expiring within specified days
            $batches = PurchaseTransactionItem::select(
                'item_id',
                'item_code',
                'item_name',
                'batch_no',
                'expiry_date',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('AVG(pur_rate) as avg_pur_rate'),
                DB::raw('AVG(mrp) as avg_mrp')
            )
            ->whereNotNull('batch_no')
            ->where('batch_no', '!=', '')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString()
            ])
            ->groupBy('item_id', 'item_code', 'item_name', 'batch_no', 'expiry_date')
            ->orderBy('expiry_date')
            ->orderBy('item_name')
            ->get();
            
            return view('admin.batches.expiry-report', [
                'batches' => $batches,
                'days' => $days
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching expiry report: ' . $e->getMessage());
            return redirect()->route('admin.batches.index')
                ->with('error', 'Error loading expiry report.');
        }
    }

    /**
     * Get last batch number for an item by code
     */
    public function getLastBatch($code)
    {
        try {
            // Get the most recent batch for this item code directly from batches table
            $lastBatch = Batch::where('item_code', $code)
                ->whereNotNull('batch_no')
                ->where('batch_no', '!=', '')
                ->where('is_deleted', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastBatch && $lastBatch->batch_no) {
                return response()->json([
                    'success' => true,
                    'batch_no' => $lastBatch->batch_no,
                    'item_code' => $code,
                    'item_name' => $lastBatch->item_name,
                    'expiry_date' => $lastBatch->expiry_date,
                    'mrp' => $lastBatch->mrp,
                    'purchase_rate' => $lastBatch->pur_rate
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No previous batch found for this item'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching last batch for item ' . $code . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching last batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if batch exists for an item (for breakage/expiry transaction)
     */
    public function checkBatch(Request $request)
    {
        try {
            $itemId = $request->get('item_id');
            $batchNo = $request->get('batch_no');

            if (!$itemId || !$batchNo) {
                return response()->json([
                    'success' => false,
                    'exists' => false,
                    'message' => 'Item ID and Batch No are required'
                ], 400);
            }

            // Get all batches for this item and batch number
            $batches = Batch::where('item_id', $itemId)
                ->where('batch_no', $batchNo)
                ->where('is_deleted', 0)
                ->with(['item', 'transaction'])
                ->orderBy('expiry_date')
                ->get();

            if ($batches->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'message' => 'Batch not found'
                ]);
            }

            // Get item details
            $item = $batches->first()->item;

            // Format batches for response
            $formattedBatches = $batches->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'item_code' => $batch->item_code,
                    'item_name' => $batch->item_name,
                    'expiry_date' => $batch->expiry_date,
                    'manufacturing_date' => $batch->manufacturing_date,
                    'total_qty' => $batch->total_qty ?? 0,
                    'qty' => $batch->qty ?? 0,
                    'free_qty' => $batch->free_qty ?? 0,
                    'pur_rate' => $batch->pur_rate ?? 0,
                    's_rate' => $batch->s_rate ?? 0,
                    'mrp' => $batch->mrp ?? 0,
                    'ws_rate' => $batch->ws_rate ?? 0,
                    'spl_rate' => $batch->spl_rate ?? 0,
                    'cost_gst' => $batch->cost_gst ?? 0,
                    'sale_scheme' => $batch->sale_scheme ?? 0,
                    'packing' => $batch->packing ?? '',
                    'company_name' => $batch->company_name ?? ''
                ];
            });

            return response()->json([
                'success' => true,
                'exists' => true,
                'batches' => $formattedBatches,
                'item_name' => $item->name ?? $batches->first()->item_name,
                'item_packing' => $item->packing ?? $batches->first()->packing
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking batch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'Error checking batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new batch
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Batch creation request:', $request->all());
            
            // Test database connection and table existence
            try {
                $tableExists = \Schema::hasTable('batches');
                \Log::info('Batches table exists: ' . ($tableExists ? 'Yes' : 'No'));
                
                if (!$tableExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batches table does not exist in database'
                    ], 500);
                }
                
                // Test if we can query the table
                $batchCount = \DB::table('batches')->count();
                \Log::info('Current batch count in database: ' . $batchCount);
                
            } catch (\Exception $dbTest) {
                \Log::error('Database connection test failed: ' . $dbTest->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed: ' . $dbTest->getMessage()
                ], 500);
            }
            
            $request->validate([
                'item_id' => 'required|integer|exists:items,id',
                'batch_no' => 'required|string|max:255',
                'mrp' => 'required|numeric|min:0',
                's_rate' => 'required|numeric|min:0',
                'pur_rate' => 'required|numeric|min:0',
                'expiry_date' => 'nullable|string',
                'total_qty' => 'nullable|numeric'
            ]);

            // Get item details
            $item = Item::findOrFail($request->item_id);
            \Log::info('Item found:', ['item' => $item->toArray()]);

            // Check if batch already exists for this item
            $existingBatch = Batch::where('item_id', $request->item_id)
                                  ->where('batch_no', $request->batch_no)
                                  ->first();
            
            if ($existingBatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch number already exists for this item'
                ], 422);
            }

            // Create new batch
            $batch = new Batch();
            $batch->purchase_transaction_id = null; // Not from a purchase transaction
            $batch->purchase_transaction_item_id = null; // Not from a purchase transaction
            $batch->item_id = $request->item_id;
            $batch->item_code = $item->code;
            $batch->item_name = $item->name;
            $batch->batch_no = $request->batch_no;
            $batch->mrp = $request->mrp;
            $batch->s_rate = $request->s_rate;
            $batch->pur_rate = $request->pur_rate;
            $batch->ws_rate = $request->ws_rate ?? $request->s_rate;
            $batch->spl_rate = $request->spl_rate ?? $request->s_rate;
            $batch->total_qty = $request->total_qty ?? 0;
            $batch->qty = $request->total_qty ?? 0;
            $batch->free_qty = 0;
            $batch->packing = $item->packing ?? '';
            $batch->company_name = $item->company_name ?? '';
            $batch->is_deleted = 0;
            $batch->status = 'active';
            $batch->godown = 'MAIN';
            
            // Set default values for other fields
            $batch->bc = '';
            $batch->sale_scheme = 0;
            $batch->inc = 0;
            $batch->n_rate = $request->s_rate;
            $batch->dis_percent = 0;
            $batch->sc_amount = 0;
            $batch->amount = 0;
            $batch->cgst_percent = 0;
            $batch->sgst_percent = 0;
            $batch->cess_percent = 0;
            $batch->cgst_amount = 0;
            $batch->sgst_amount = 0;
            $batch->cess_amount = 0;
            $batch->tax_amount = 0;
            $batch->gst_pts = 0;
            $batch->net_amount = 0;
            $batch->cost = $request->pur_rate;
            $batch->cost_gst = $request->pur_rate;
            $batch->cost_wfq = $request->pur_rate;
            $batch->rate_diff = 0;
            $batch->unit = 'PCS';
            $batch->hold_breakage_expiry = 0;
            $batch->remarks = 'Created from breakage/expiry transaction';

            // Handle expiry date
            if ($request->expiry_date) {
                // If expiry is in MM/YY format, convert to date
                if (preg_match('/^(\d{2})\/(\d{2})$/', $request->expiry_date, $matches)) {
                    $month = $matches[1];
                    $year = '20' . $matches[2]; // Convert YY to 20YY
                    $batch->expiry_date = $year . '-' . $month . '-01'; // First day of the month
                } else {
                    $batch->expiry_date = $request->expiry_date;
                }
            }

            \Log::info('About to save batch:', ['batch_data' => $batch->toArray()]);
            $batch->save();
            \Log::info('Batch saved successfully:', ['batch_id' => $batch->id]);

            return response()->json([
                'success' => true,
                'message' => 'Batch created successfully',
                'batch' => $batch
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error creating batch: ' . $e->getMessage());
            \Log::error('SQL Error Code: ' . $e->getCode());
            \Log::error('SQL State: ' . $e->errorInfo[0] ?? 'Unknown');
            \Log::error('Error Info: ' . json_encode($e->errorInfo));
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'sql_state' => $e->errorInfo[0] ?? 'Unknown'
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Batch validation failed: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating batch: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Exception type: ' . get_class($e));
            return response()->json([
                'success' => false,
                'message' => 'Error creating batch: ' . $e->getMessage(),
                'exception_type' => get_class($e)
            ], 500);
        }
    }
}

