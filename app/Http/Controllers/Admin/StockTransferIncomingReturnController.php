<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Item;
use App\Models\StockTransferIncomingReturnTransaction;
use App\Models\StockTransferIncomingReturnTransactionItem;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferIncomingReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransferIncomingReturnTransaction::query();

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'name':
                    $query->where('name', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'net_amount':
                    $query->where('net_amount', '>=', floatval($searchTerm));
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.stock-transfer-incoming-return.index', compact('transactions'));
    }

    public function transaction()
    {
        $nextTrnNo = StockTransferIncomingReturnTransaction::generateTrnNumber();
        $suppliers = \App\Models\Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.stock-transfer-incoming-return.transaction', compact('nextTrnNo', 'suppliers'));
    }

    public function modification()
    {
        $suppliers = \App\Models\Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.stock-transfer-incoming-return.modification', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $trnNo = StockTransferIncomingReturnTransaction::generateTrnNumber();

            $transaction = StockTransferIncomingReturnTransaction::create([
                'trn_no' => $trnNo,
                'series' => 'STIR',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'name' => $request->name,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'net_amount' => $request->net_amount ?? 0,
                'packing' => $request->packing,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

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
                $isNewBatch = $item['is_new_batch'] ?? false;
                $newBatchData = $item['new_batch_data'] ?? null;
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                
                // Handle new batch creation OR decrease existing batch
                if ($isNewBatch && $newBatchData) {
                    // Create new batch with NEGATIVE qty (return = stock going out)
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'batch_no' => $newBatchData['batch_no'] ?? $item['batch'] ?? '',
                        'expiry_date' => $expiryDate,
                        'mrp' => $newBatchData['mrp'] ?? 0,
                        's_rate' => $newBatchData['s_rate'] ?? $item['rate'] ?? 0,
                        'pur_rate' => $newBatchData['s_rate'] ?? $item['rate'] ?? 0,
                        'location' => $newBatchData['location'] ?? '',
                        'qty' => -$qty, // NEGATIVE qty for new batch (stock returned/out)
                        'total_qty' => -$qty, // NEGATIVE total_qty
                        'is_active' => 1,
                    ]);
                    $batchIdForItem = $newBatch->id;
                } elseif ($existingBatchId) {
                    // DECREASE quantity in existing batch (qty - return qty, minimum 0)
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty - $qty; // Can go negative if needed
                        $existingBatch->total_qty = ($existingBatch->total_qty ?? 0) - $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                }
                
                $transactionItem = StockTransferIncomingReturnTransactionItem::create([
                    'stock_transfer_incoming_return_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'rate' => $item['rate'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Stock ledger - OUT transaction
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'STOCK_TRANSFER_INCOMING_RETURN';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->rate;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Incoming Return saved successfully!',
                'trn_no' => $trnNo,
                'id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock Transfer Incoming Return Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = StockTransferIncomingReturnTransaction::with('items')->findOrFail($id);
            
            // Restore old batch quantities (reverse the previous decrease)
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->batch_id) {
                    $oldBatch = Batch::find($oldItem->batch_id);
                    if ($oldBatch) {
                        $oldBatch->qty = $oldBatch->qty + $oldItem->qty;
                        $oldBatch->total_qty = ($oldBatch->total_qty ?? 0) + $oldItem->qty;
                        $oldBatch->save();
                    }
                }
            }
            
            StockLedger::where('reference_type', 'STOCK_TRANSFER_INCOMING_RETURN')
                ->where('reference_id', $id)
                ->delete();

            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'name' => $request->name,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'net_amount' => $request->net_amount ?? 0,
                'packing' => $request->packing,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'updated_by' => Auth::id(),
            ]);

            $transaction->items()->delete();

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
                $isNewBatch = $item['is_new_batch'] ?? false;
                $newBatchData = $item['new_batch_data'] ?? null;
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                
                // Handle new batch creation OR decrease existing batch
                if ($isNewBatch && $newBatchData) {
                    // Create new batch with NEGATIVE qty (return = stock going out)
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'batch_no' => $newBatchData['batch_no'] ?? $item['batch'] ?? '',
                        'expiry_date' => $expiryDate,
                        'mrp' => $newBatchData['mrp'] ?? 0,
                        's_rate' => $newBatchData['s_rate'] ?? $item['rate'] ?? 0,
                        'pur_rate' => $newBatchData['s_rate'] ?? $item['rate'] ?? 0,
                        'location' => $newBatchData['location'] ?? '',
                        'qty' => -$qty, // NEGATIVE qty for new batch (stock returned/out)
                        'total_qty' => -$qty, // NEGATIVE total_qty
                        'is_active' => 1,
                    ]);
                    $batchIdForItem = $newBatch->id;
                } elseif ($existingBatchId) {
                    // DECREASE quantity in existing batch
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty - $qty;
                        $existingBatch->total_qty = ($existingBatch->total_qty ?? 0) - $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                }
                
                $transactionItem = StockTransferIncomingReturnTransactionItem::create([
                    'stock_transfer_incoming_return_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'rate' => $item['rate'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'STOCK_TRANSFER_INCOMING_RETURN';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->rate;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock Transfer Incoming Return Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $transaction = StockTransferIncomingReturnTransaction::with('items')->findOrFail($id);
            
            // Restore batch quantities
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $batch->qty = $batch->qty + $item->qty;
                        $batch->total_qty = ($batch->total_qty ?? 0) + $item->qty;
                        $batch->save();
                    }
                }
            }

            StockLedger::where('reference_type', 'STOCK_TRANSFER_INCOMING_RETURN')
                ->where('reference_id', $id)
                ->delete();

            $transaction->items()->delete();
            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $transaction = StockTransferIncomingReturnTransaction::with(['items.batch', 'items.item'])
                ->findOrFail($id);

            $items = $transaction->items->map(function($item) {
                $itemData = [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'batch_id' => $item->batch_id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry' => $item->expiry,
                    'expiry_date' => $item->expiry_date ? $item->expiry_date->format('Y-m-d') : null,
                    'qty' => $item->qty,
                    'rate' => $item->rate,
                    'amount' => $item->amount,
                ];

                if ($item->batch) {
                    $itemData['s_rate'] = $item->batch->s_rate ?? 0;
                    $itemData['location'] = $item->batch->location ?? '';
                }

                if ($item->item) {
                    $itemData['packing'] = $item->item->packing ?? '';
                    $itemData['unit'] = $item->item->unit ?? '';
                    $itemData['company_short_name'] = $item->item->company_short_name ?? '';
                }

                return $itemData;
            });

            // Format dates properly
            $transactionDate = $transaction->transaction_date;
            if ($transactionDate instanceof \DateTime) {
                $transactionDate = $transactionDate->format('Y-m-d');
            } elseif (is_string($transactionDate)) {
                $transactionDate = date('Y-m-d', strtotime($transactionDate));
            }
            
            $grDate = $transaction->gr_date;
            if ($grDate instanceof \DateTime) {
                $grDate = $grDate->format('Y-m-d');
            } elseif (is_string($grDate) && $grDate) {
                $grDate = date('Y-m-d', strtotime($grDate));
            }

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'trn_no' => $transaction->trn_no,
                    'transaction_date' => $transactionDate,
                    'name' => $transaction->name,
                    'gr_no' => $transaction->gr_no,
                    'gr_date' => $grDate,
                    'cases' => $transaction->cases,
                    'transport' => $transaction->transport,
                    'remarks' => $transaction->remarks,
                    'net_amount' => $transaction->net_amount ?? 0,
                    'packing' => $transaction->packing,
                    'unit' => $transaction->unit,
                    'cl_qty' => $transaction->cl_qty,
                    'comp' => $transaction->comp,
                    'lctn' => $transaction->lctn,
                    'srlno' => $transaction->srlno,
                ],
                'items' => $items
            ]);
        } catch (\Exception $e) {
            \Log::error('StockTransferIncomingReturn getDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPastTransactions()
    {
        try {
            $transactions = StockTransferIncomingReturnTransaction::orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get()
                ->map(function($t) {
                    $transDate = $t->transaction_date;
                    if ($transDate instanceof \DateTime) {
                        $transDate = $transDate->format('Y-m-d');
                    } elseif (is_string($transDate)) {
                        $transDate = date('Y-m-d', strtotime($transDate));
                    }
                    
                    return [
                        'id' => $t->id,
                        'trn_no' => $t->trn_no,
                        'transaction_date' => $transDate,
                        'name' => $t->name,
                        'net_amount' => $t->net_amount ?? 0
                    ];
                });

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaction = StockTransferIncomingReturnTransaction::with(['items'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.stock-transfer-incoming-return.show', compact('transaction'));
    }
}
