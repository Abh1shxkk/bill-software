<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\StockLedger;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display stock adjustment transaction form
     */
    public function transaction()
    {
        $nextTrnNo = $this->generateTrnNo();
        
        return view('admin.stock-adjustment.transaction', compact('nextTrnNo'));
    }

    /**
     * Display stock adjustment modification form
     */
    public function modification(Request $request, $trn_no = null)
    {
        $preloadTrnNo = $trn_no ?: $request->query('trn_no');
        
        return view('admin.stock-adjustment.modification', compact('preloadTrnNo'));
    }

    /**
     * Display stock adjustment invoices listing
     */
    public function invoices(Request $request)
    {
        $query = StockAdjustment::query();

        // Apply search filter
        if ($request->filled('search')) {
            $filterBy = $request->get('filter_by', 'trn_no');
            $searchTerm = $request->get('search');

            switch ($filterBy) {
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                case 'remarks':
                    $query->where('remarks', 'LIKE', '%' . $searchTerm . '%');
                    break;
            }
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('adjustment_date', [
                $request->get('date_from'),
                $request->get('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->get('date_to'));
        }

        // Order by latest first
        $query->orderBy('adjustment_date', 'desc')->orderBy('id', 'desc');

        // Get paginated results
        $adjustments = $query->paginate(10)->withQueryString();

        // For AJAX requests, return the full view (like Items index)
        // The JavaScript will parse the HTML and extract what it needs
        return view('admin.stock-adjustment.invoices', compact('adjustments'));
    }

    /**
     * Store a new stock adjustment
     */
    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'stock_adjustment', 'adjustment_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        $request->validate([
            'adjustment_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Generate transaction number
            $trnNo = $this->generateTrnNo();

            // Calculate totals
            $totalAmount = 0;
            $totalItems = 0;
            $shortageItems = 0;
            $excessItems = 0;

            foreach ($request->items as $item) {
                if (empty($item['item_id']) || empty($item['batch_id'])) {
                    continue;
                }
                $totalItems++;
                $amount = floatval($item['amount'] ?? 0);
                $totalAmount += $amount;
                
                if (($item['adjustment_type'] ?? 'S') === 'S') {
                    $shortageItems++;
                } else {
                    $excessItems++;
                }
            }

            // Create stock adjustment
            $stockAdjustment = StockAdjustment::create([
                'trn_no' => $trnNo,
                'adjustment_date' => $request->adjustment_date,
                'day_name' => $request->day_name ?? null,
                'remarks' => $request->remarks ?? null,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'shortage_items' => $shortageItems,
                'excess_items' => $excessItems,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Create items and update batches/stock ledger
            foreach ($request->items as $index => $itemData) {
                if (empty($itemData['item_id']) || empty($itemData['batch_id'])) {
                    continue;
                }

                $adjustmentType = $itemData['adjustment_type'] ?? 'S';
                $qty = floatval($itemData['qty'] ?? 0);
                $cost = floatval($itemData['cost'] ?? 0);
                $amount = floatval($itemData['amount'] ?? 0);

                // Create stock adjustment item
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'item_id' => $itemData['item_id'],
                    'batch_id' => $itemData['batch_id'],
                    'item_code' => $itemData['item_code'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'adjustment_type' => $adjustmentType,
                    'qty' => $qty,
                    'cost' => $cost,
                    'amount' => $amount,
                    'packing' => $itemData['packing'] ?? null,
                    'company_name' => $itemData['company_name'] ?? null,
                    'mrp' => $itemData['mrp'] ?? 0,
                    'row_order' => $index,
                ]);

                // Create stock ledger entry (batch qty is updated automatically by StockLedgerObserver)
                $batch = Batch::find($itemData['batch_id']);
                if ($batch) {
                    $this->createStockLedgerEntry($stockAdjustment, $itemData, $adjustmentType, $qty, $batch, $index);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment saved successfully',
                'trn_no' => $trnNo,
                'id' => $stockAdjustment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Adjustment Save Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing stock adjustment
     */
    public function update(Request $request, $id)
    {
        $stockAdjustment = StockAdjustment::findOrFail($id);

        $request->validate([
            'adjustment_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Delete old stock ledger entries (observer will automatically reverse batch qty)
            StockLedger::where('reference_type', 'STOCK_ADJUSTMENT')
                ->where('reference_id', $stockAdjustment->id)
                ->delete();

            // Delete old items
            $stockAdjustment->items()->delete();

            // Calculate new totals
            $totalAmount = 0;
            $totalItems = 0;
            $shortageItems = 0;
            $excessItems = 0;

            foreach ($request->items as $item) {
                if (empty($item['item_id']) || empty($item['batch_id'])) {
                    continue;
                }
                $totalItems++;
                $amount = floatval($item['amount'] ?? 0);
                $totalAmount += $amount;
                
                if (($item['adjustment_type'] ?? 'S') === 'S') {
                    $shortageItems++;
                } else {
                    $excessItems++;
                }
            }

            // Update stock adjustment
            $stockAdjustment->update([
                'adjustment_date' => $request->adjustment_date,
                'day_name' => $request->day_name ?? null,
                'remarks' => $request->remarks ?? null,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'shortage_items' => $shortageItems,
                'excess_items' => $excessItems,
                'updated_by' => Auth::id(),
            ]);

            // Create new items and update batches
            foreach ($request->items as $index => $itemData) {
                if (empty($itemData['item_id']) || empty($itemData['batch_id'])) {
                    continue;
                }

                $adjustmentType = $itemData['adjustment_type'] ?? 'S';
                $qty = floatval($itemData['qty'] ?? 0);
                $cost = floatval($itemData['cost'] ?? 0);
                $amount = floatval($itemData['amount'] ?? 0);

                // Create stock adjustment item
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'item_id' => $itemData['item_id'],
                    'batch_id' => $itemData['batch_id'],
                    'item_code' => $itemData['item_code'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'adjustment_type' => $adjustmentType,
                    'qty' => $qty,
                    'cost' => $cost,
                    'amount' => $amount,
                    'packing' => $itemData['packing'] ?? null,
                    'company_name' => $itemData['company_name'] ?? null,
                    'mrp' => $itemData['mrp'] ?? 0,
                    'row_order' => $index,
                ]);

                // Create stock ledger entry (batch qty is updated automatically by StockLedgerObserver)
                $batch = Batch::find($itemData['batch_id']);
                if ($batch) {
                    $this->createStockLedgerEntry($stockAdjustment, $itemData, $adjustmentType, $qty, $batch, $index);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment updated successfully',
                'trn_no' => $stockAdjustment->trn_no,
                'id' => $stockAdjustment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Adjustment Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a stock adjustment
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $stockAdjustment = StockAdjustment::findOrFail($id);

            // Delete stock ledger entries (observer will automatically reverse batch qty)
            StockLedger::where('reference_type', 'STOCK_ADJUSTMENT')
                ->where('reference_id', $stockAdjustment->id)
                ->delete();

            // Delete adjustment (soft delete)
            $stockAdjustment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Adjustment Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch stock adjustment by transaction number
     */
    public function fetchByTrnNo($trnNo)
    {
        try {
            $stockAdjustment = StockAdjustment::with(['items.item', 'items.batch'])
                ->where('trn_no', $trnNo)
                ->first();

            if (!$stockAdjustment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock adjustment not found'
                ], 404);
            }

            // Format items with additional details
            $formattedItems = $stockAdjustment->items->map(function($item) {
                $itemModel = $item->item;
                $batch = $item->batch;
                
                // Get company name - prefer company_short_name, fallback to mfg_by
                $companyName = '';
                if ($itemModel) {
                    $companyName = $itemModel->company_short_name ?: $itemModel->mfg_by ?: '';
                }
                
                // Get current batch quantity (closing qty)
                $clQty = $batch ? $batch->qty : 0;
                
                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'batch_id' => $item->batch_id,
                    'batch_no' => $item->batch_no,
                    'expiry_date' => $item->expiry_date,
                    'adjustment_type' => $item->adjustment_type,
                    'qty' => $item->qty,
                    'cost' => $item->cost,
                    'amount' => $item->amount,
                    'packing' => $itemModel ? $itemModel->packing : '',
                    'company_name' => $companyName,
                    'mrp' => $batch ? $batch->mrp : ($itemModel ? $itemModel->mrp : 0),
                    'unit' => $itemModel ? $itemModel->unit : '1',
                    'cl_qty' => $clQty
                ];
            });

            return response()->json([
                'success' => true,
                'adjustment' => [
                    'id' => $stockAdjustment->id,
                    'trn_no' => $stockAdjustment->trn_no,
                    'adjustment_date' => $stockAdjustment->adjustment_date,
                    'remarks' => $stockAdjustment->remarks,
                    'total_amount' => $stockAdjustment->total_amount,
                    'items' => $formattedItems
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Fetch Stock Adjustment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock adjustment'
            ], 500);
        }
    }

    /**
     * Get next transaction number
     */
    public function getNextTrnNo()
    {
        return response()->json([
            'success' => true,
            'trn_no' => $this->generateTrnNo()
        ]);
    }

    /**
 * Generate transaction number (per organization)
 */
private function generateTrnNo()
{
    $orgId = auth()->user()->organization_id ?? 1;
    
    $lastAdjustment = StockAdjustment::withTrashed()
        ->withoutGlobalScopes()
        ->where('organization_id', $orgId)
        ->orderBy('id', 'desc')
        ->first();

    if ($lastAdjustment) {
        $lastNumber = intval(preg_replace('/[^0-9]/', '', $lastAdjustment->trn_no));
        return $lastNumber + 1;
    }

    return 1;
}

    /**
     * Create stock ledger entry for adjustment
     */
    private function createStockLedgerEntry($stockAdjustment, $itemData, $adjustmentType, $qty, $batch, $index = 0)
    {
        $transactionType = $adjustmentType === 'S' ? 'OUT' : 'IN';
        
        // Make trans_no unique by appending item index (e.g., "9-0", "9-1", "9-2")
        $uniqueTransNo = $stockAdjustment->trn_no . '-' . $index;
        
        StockLedger::create([
            'trans_no' => $uniqueTransNo,
            'item_id' => $itemData['item_id'],
            'batch_id' => $itemData['batch_id'],
            'customer_id' => null,
            'supplier_id' => null,
            'transaction_type' => $transactionType,
            'quantity' => $qty,
            'free_quantity' => 0,
            'opening_qty' => $batch->qty + ($adjustmentType === 'S' ? $qty : -$qty),
            'closing_qty' => $batch->qty,
            'running_balance' => $batch->qty,
            'reference_type' => 'STOCK_ADJUSTMENT',
            'reference_id' => $stockAdjustment->id,
            'transaction_date' => $stockAdjustment->adjustment_date,
            'godown' => $batch->godown ?? 'Main',
            'remarks' => ($adjustmentType === 'S' ? 'Shortage' : 'Excess') . ' - Stock Adjustment',
            'salesman_id' => null,
            'bill_number' => $stockAdjustment->trn_no,
            'bill_date' => $stockAdjustment->adjustment_date,
            'rate' => $itemData['cost'] ?? 0,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Get past stock adjustments for listing
     */
    public function getPastAdjustments(Request $request)
    {
        try {
            $query = StockAdjustment::with(['items'])
                ->orderBy('adjustment_date', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('date_from')) {
                $query->where('adjustment_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('adjustment_date', '<=', $request->date_to);
            }

            $adjustments = $query->paginate(20);

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments->items(),
                'hasMorePages' => $adjustments->hasMorePages()
            ]);

        } catch (\Exception $e) {
            Log::error('Get Past Adjustments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching adjustments'
            ], 500);
        }
    }

    /**
     * Get stock adjustment details for modal view
     */
    public function getDetails($id)
    {
        try {
            $stockAdjustment = StockAdjustment::with(['items.item', 'items.batch', 'createdBy'])
                ->findOrFail($id);

            $items = $stockAdjustment->items->map(function($item) {
                return [
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no ?? '-',
                    'adjustment_type' => $item->adjustment_type === 'S' ? 'Shortage' : 'Excess',
                    'qty' => $item->qty,
                    'cost' => $item->cost,
                    'amount' => $item->amount,
                ];
            });

            return response()->json([
                'trn_no' => $stockAdjustment->trn_no,
                'adjustment_date' => $stockAdjustment->adjustment_date ? $stockAdjustment->adjustment_date->format('d-m-Y') : '-',
                'remarks' => $stockAdjustment->remarks ?? '-',
                'total_amount' => number_format($stockAdjustment->total_amount, 2),
                'total_items' => $stockAdjustment->total_items,
                'shortage_items' => $stockAdjustment->shortage_items,
                'excess_items' => $stockAdjustment->excess_items,
                'created_by' => $stockAdjustment->createdBy->name ?? 'System',
                'items' => $items,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Stock Adjustment Details Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching stock adjustment details'
            ], 500);
        }
    }
}
