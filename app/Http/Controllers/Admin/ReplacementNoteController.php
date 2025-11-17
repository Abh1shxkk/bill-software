<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Item;
use App\Models\ReplacementNoteTransaction;
use App\Models\ReplacementNoteTransactionItem;
use App\Models\StockLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReplacementNoteController extends Controller
{
    /**
     * Display list of replacement note transactions
     */
    public function index(Request $request)
    {
        $query = ReplacementNoteTransaction::with(['supplier:supplier_id,name']);

        // Apply filters
        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'supplier_name':
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('supplier_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhereHas('supplier', function ($sq) use ($searchTerm) {
                              $sq->where('name', 'LIKE', "%{$searchTerm}%");
                          });
                    });
                    break;
                case 'rn_no':
                    $query->where('rn_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'net_amount':
                    $query->where('net_amount', '>=', floatval($searchTerm));
                    break;
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.replacement-note.index', compact('transactions'));
    }

    /**
     * Display replacement note transaction form
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $nextRnNo = ReplacementNoteTransaction::generateRNNumber();
        
        return view('admin.replacement-note.transaction', compact('suppliers', 'nextRnNo'));
    }

    /**
     * Display replacement note modification form
     */
    public function modification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.replacement-note.modification', compact('suppliers'));
    }

    /**
     * Store a new replacement note transaction
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Generate RN number
            $rnNo = ReplacementNoteTransaction::generateRNNumber();

            // Create replacement note transaction
            $transaction = ReplacementNoteTransaction::create([
                'rn_no' => $rnNo,
                'series' => 'RN',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'pending_br_expiry' => $request->pending_br_expiry ?? 0,
                'balance_amount' => $request->balance_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'pack' => $request->pack,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'case_no' => $request->case_no,
                'box' => $request->box,
                'remarks' => $request->remarks,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Save items
            $items = $request->items ?? [];
            $rowOrder = 0;
            
            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                // Handle expiry date format
                $expiryDate = null;
                $expiryInput = $item['expiry'] ?? null;
                if ($expiryInput) {
                    if (preg_match('/^(\d{1,2})\/(\d{4})$/', $expiryInput, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        $expiryDate = "{$year}-{$month}-01";
                    } else {
                        try {
                            $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                }
                
                $existingBatchId = $item['batch_id'] ?? null;
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                
                // Check if existing batch was selected
                if ($existingBatchId) {
                    // Reduce quantity from existing batch
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty - $qty;
                        $existingBatch->total_qty = $existingBatch->total_qty - $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                        
                        \Log::info('Replacement Note - Existing batch qty reduced', [
                            'batch_id' => $existingBatch->id,
                            'batch_no' => $existingBatch->batch_no,
                            'reduced_qty' => $qty,
                            'new_qty' => $existingBatch->qty
                        ]);
                    }
                } else {
                    // Create new batch with NEGATIVE quantity (for new batch entries)
                    $sRate = isset($item['new_batch_s_rate']) ? floatval($item['new_batch_s_rate']) : 0;
                    $location = $item['new_batch_location'] ?? '';
                    
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                        'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                        'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => -$qty, // Negative quantity
                        'total_qty' => -$qty,
                        'mrp' => $item['mrp'] ?? 0,
                        'pur_rate' => 0,
                        's_rate' => $sRate,
                        'ws_rate' => 0,
                        'spl_rate' => 0,
                        'location' => $location,
                        'purchase_transaction_id' => null,
                        'replacement_note_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                    
                    \Log::info('Replacement Note - New batch created with negative qty', [
                        'batch_id' => $newBatch->id,
                        'batch_no' => $newBatch->batch_no,
                        'qty' => $newBatch->qty
                    ]);
                }
                
                $transactionItem = ReplacementNoteTransactionItem::create([
                    'replacement_note_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'mrp' => $item['mrp'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Create stock ledger entry
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'REPLACEMENT_NOTE';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->mrp;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Replacement note saved successfully!',
                'rn_no' => $rnNo,
                'id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Replacement Note Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving replacement note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display replacement note details
     */
    public function show($id)
    {
        $transaction = ReplacementNoteTransaction::with(['items', 'supplier:supplier_id,name'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.replacement-note.show', compact('transaction'));
    }

    /**
     * Get replacement note details for modification
     */
    public function getDetails($id)
    {
        try {
            $transaction = ReplacementNoteTransaction::with(['items.batch:id,s_rate,mrp,location', 'supplier:supplier_id,name'])
                ->findOrFail($id);

            // Add s_rate from batch to items
            $items = $transaction->items->map(function($item) {
                if ($item->batch) {
                    $item->s_rate = $item->batch->s_rate;
                }
                return $item;
            });

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get replacement note by RN number
     */
    public function getByRnNo($rnNo)
    {
        try {
            $transaction = ReplacementNoteTransaction::where('rn_no', $rnNo)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Replacement note not found'
                ], 404);
            }

            return $this->getDetails($transaction->id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get past replacement notes
     */
    public function getPastNotes(Request $request)
    {
        try {
            $date = $request->get('date');
            $supplierId = $request->get('supplier_id');

            $query = ReplacementNoteTransaction::with('supplier:supplier_id,name')
                ->where('status', '!=', 'deleted')
                ->orderBy('id', 'desc');

            if ($date) {
                $query->whereDate('transaction_date', $date);
            }

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            if (!$date) {
                $query->limit(100);
            }

            $transactions = $query->get()->map(function ($trn) {
                return [
                    'id' => $trn->id,
                    'rn_no' => $trn->rn_no,
                    'transaction_date' => $trn->transaction_date ? $trn->transaction_date->format('d-M-y') : '',
                    'supplier_name' => $trn->supplier ? $trn->supplier->name : ($trn->supplier_name ?? ''),
                    'amount' => number_format($trn->net_amount ?? 0, 2, '.', ''),
                    'status' => $trn->status ?? 'active',
                ];
            });

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update replacement note
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = ReplacementNoteTransaction::with('items')->findOrFail($id);
            
            // First, restore old batch quantities (reverse the previous reduction)
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->batch_id) {
                    $oldBatch = Batch::find($oldItem->batch_id);
                    if ($oldBatch) {
                        // Check if this batch was created by this replacement note (negative qty batch)
                        if ($oldBatch->replacement_note_id == $transaction->id) {
                            // This was a newly created negative batch, just delete it
                            $oldBatch->delete();
                        } else {
                            // This was an existing batch, restore the qty
                            $oldBatch->qty = $oldBatch->qty + $oldItem->qty;
                            $oldBatch->total_qty = ($oldBatch->total_qty ?? 0) + $oldItem->qty;
                            $oldBatch->save();
                        }
                    }
                }
            }
            
            // Delete old stock ledger entries
            StockLedger::where('reference_type', 'REPLACEMENT_NOTE')
                ->where('reference_id', $id)
                ->delete();

            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'pending_br_expiry' => $request->pending_br_expiry ?? 0,
                'balance_amount' => $request->balance_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'pack' => $request->pack,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'case_no' => $request->case_no,
                'box' => $request->box,
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $transaction->items()->delete();

            // Save new items
            $items = $request->items ?? [];
            $rowOrder = 0;
            
            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $expiryDate = null;
                $expiryInput = $item['expiry'] ?? null;
                if ($expiryInput) {
                    if (preg_match('/^(\d{1,2})\/(\d{4})$/', $expiryInput, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        $expiryDate = "{$year}-{$month}-01";
                    } else {
                        try {
                            $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                }
                
                $existingBatchId = $item['batch_id'] ?? null;
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                
                // Check if existing batch was selected
                if ($existingBatchId && !str_starts_with($existingBatchId, 'new_')) {
                    // Reduce quantity from existing batch
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty - $qty;
                        $existingBatch->total_qty = ($existingBatch->total_qty ?? 0) - $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                } else {
                    // Create new batch with NEGATIVE quantity
                    $sRate = isset($item['new_batch_s_rate']) ? floatval($item['new_batch_s_rate']) : 0;
                    $location = $item['new_batch_location'] ?? '';
                    
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                        'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                        'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => -$qty,
                        'total_qty' => -$qty,
                        'mrp' => $item['mrp'] ?? 0,
                        'pur_rate' => 0,
                        's_rate' => $sRate,
                        'ws_rate' => 0,
                        'spl_rate' => 0,
                        'location' => $location,
                        'replacement_note_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                }
                
                $transactionItem = ReplacementNoteTransactionItem::create([
                    'replacement_note_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'mrp' => $item['mrp'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Create stock ledger entry
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'REPLACEMENT_NOTE';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->mrp;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Replacement note updated successfully!',
                'rn_no' => $transaction->rn_no,
                'id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Replacement Note Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete replacement note
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $transaction = ReplacementNoteTransaction::with(['items'])->findOrFail($id);
            
            // Restore batch quantities
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        // Check if this batch was created by this replacement note
                        if ($batch->replacement_note_id == $transaction->id) {
                            // This was a newly created negative batch, just delete it
                            $batch->delete();
                        } else {
                            // This was an existing batch that had qty reduced, restore it
                            $batch->qty = $batch->qty + $item->qty;
                            $batch->total_qty = ($batch->total_qty ?? 0) + $item->qty;
                            $batch->save();
                            
                            \Log::info('Replacement Note Delete - Batch qty restored', [
                                'batch_id' => $batch->id,
                                'restored_qty' => $item->qty,
                                'new_qty' => $batch->qty
                            ]);
                        }
                    }
                }
            }
            
            // Delete stock ledger entries
            StockLedger::where('reference_type', 'REPLACEMENT_NOTE')
                ->where('reference_id', $id)
                ->delete();
            
            // Delete items
            ReplacementNoteTransactionItem::where('replacement_note_transaction_id', $id)->delete();
            
            // Delete transaction
            $transaction->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Replacement note deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting replacement note: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next transaction number
     */
    public function getNextTransactionNumber()
    {
        return response()->json([
            'next_rn_no' => ReplacementNoteTransaction::generateRNNumber()
        ]);
    }
}
