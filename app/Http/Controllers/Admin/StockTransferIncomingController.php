<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Item;
use App\Models\StockTransferIncomingTransaction;
use App\Models\StockTransferIncomingTransactionItem;
use App\Models\StockLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferIncomingController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransferIncomingTransaction::with(['supplier:supplier_id,name']);

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
                case 'trf_no':
                    $query->where('trf_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'total_amount':
                    $query->where('total_amount', '>=', floatval($searchTerm));
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

        return view('admin.stock-transfer-incoming.index', compact('transactions'));
    }

    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $nextTrfNo = StockTransferIncomingTransaction::generateTrfNumber();
        
        return view('admin.stock-transfer-incoming.transaction', compact('suppliers', 'nextTrfNo'));
    }

    public function modification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.stock-transfer-incoming.modification', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $trfNo = StockTransferIncomingTransaction::generateTrfNumber();

            $transaction = StockTransferIncomingTransaction::create([
                'trf_no' => $trfNo,
                'series' => 'STI',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'st_date' => $request->st_date,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'total_amount' => $request->total_amount ?? 0,
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
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                $isNewBatch = isset($item['is_new_batch']) && $item['is_new_batch'] === true;
                $newBatchData = $item['new_batch_data'] ?? [];
                
                // INCREASE quantity in batch (Stock Transfer Incoming adds stock)
                if ($existingBatchId && !empty($existingBatchId) && !$isNewBatch) {
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty + $qty;
                        $existingBatch->total_qty = $existingBatch->total_qty + $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                } else {
                    // Create new batch with POSITIVE quantity
                    // Use rates from new_batch_data or item data
                    $mrp = $item['mrp'] ?? $newBatchData['mrp'] ?? 0;
                    $sRate = $item['s_rate'] ?? $newBatchData['s_rate'] ?? 0;
                    $wsRate = $item['ws_rate'] ?? $newBatchData['ws_rate'] ?? 0;
                    $splRate = $item['spl_rate'] ?? $newBatchData['spl_rate'] ?? 0;
                    $pRate = $item['p_rate'] ?? $newBatchData['pur_rate'] ?? 0;
                    $location = $newBatchData['location'] ?? '';
                    
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                        'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                        'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => $qty,
                        'total_qty' => $qty,
                        'mrp' => floatval($mrp),
                        'pur_rate' => floatval($pRate),
                        's_rate' => floatval($sRate),
                        'ws_rate' => floatval($wsRate),
                        'spl_rate' => floatval($splRate),
                        'location' => $location,
                        'stock_transfer_incoming_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                }
                
                $transactionItem = StockTransferIncomingTransactionItem::create([
                    'stock_transfer_incoming_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Stock ledger - IN transaction
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'IN';
                $stockLedger->reference_type = 'STOCK_TRANSFER_INCOMING';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->ft_rate;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Incoming saved successfully!',
                'trf_no' => $trfNo,
                'id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock Transfer Incoming Error: ' . $e->getMessage());
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

            $transaction = StockTransferIncomingTransaction::with('items')->findOrFail($id);
            
            // Restore old batch quantities (reverse the previous increase)
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->batch_id) {
                    $oldBatch = Batch::find($oldItem->batch_id);
                    if ($oldBatch) {
                        if ($oldBatch->stock_transfer_incoming_id == $transaction->id) {
                            $oldBatch->delete();
                        } else {
                            $oldBatch->qty = $oldBatch->qty - $oldItem->qty;
                            $oldBatch->total_qty = ($oldBatch->total_qty ?? 0) - $oldItem->qty;
                            $oldBatch->save();
                        }
                    }
                }
            }
            
            StockLedger::where('reference_type', 'STOCK_TRANSFER_INCOMING')
                ->where('reference_id', $id)
                ->delete();

            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'st_date' => $request->st_date,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'total_amount' => $request->total_amount ?? 0,
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
                $batchIdForItem = null;
                $qty = abs($item['qty']);
                
                if ($existingBatchId && !str_starts_with((string)$existingBatchId, 'new_')) {
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty + $qty;
                        $existingBatch->total_qty = ($existingBatch->total_qty ?? 0) + $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                } else {
                    $sRate = isset($item['new_batch_s_rate']) ? floatval($item['new_batch_s_rate']) : (isset($item['s_rate']) ? floatval($item['s_rate']) : 0);
                    $location = $item['new_batch_location'] ?? '';
                    $ws_rate = isset($item['new_batch_ws_rate']) ? floatval($item['new_batch_ws_rate']) : 0;
                    $spl_rate = isset($item['new_batch_spl_rate']) ? floatval($item['new_batch_spl_rate']) : 0;
                    $p_rate = isset($item['new_batch_p_rate']) ? floatval($item['new_batch_p_rate']) : (isset($item['p_rate']) ? floatval($item['p_rate']) : 0);
                    
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                        'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                        'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => $qty,
                        'total_qty' => $qty,
                        'mrp' => $item['mrp'] ?? 0,
                        'pur_rate' => $p_rate,
                        's_rate' => $sRate,
                        'ws_rate' => $ws_rate,
                        'spl_rate' => $spl_rate,
                        'location' => $location,
                        'stock_transfer_incoming_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                }
                
                $transactionItem = StockTransferIncomingTransactionItem::create([
                    'stock_transfer_incoming_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'IN';
                $stockLedger->reference_type = 'STOCK_TRANSFER_INCOMING';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->ft_rate;
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
            \Log::error('Stock Transfer Incoming Update Error: ' . $e->getMessage());
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

            $transaction = StockTransferIncomingTransaction::with('items')->findOrFail($id);
            
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        if ($batch->stock_transfer_incoming_id == $transaction->id) {
                            $batch->delete();
                        } else {
                            $batch->qty = $batch->qty - $item->qty;
                            $batch->total_qty = ($batch->total_qty ?? 0) - $item->qty;
                            $batch->save();
                        }
                    }
                }
            }

            StockLedger::where('reference_type', 'STOCK_TRANSFER_INCOMING')
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
            $transaction = StockTransferIncomingTransaction::with(['items.batch', 'items.item', 'supplier'])
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
                    'free_qty' => $item->free_qty ?? 0,
                    'p_rate' => $item->p_rate ?? 0,
                    'gst_percent' => $item->gst_percent ?? 0,
                    'ft_rate' => $item->ft_rate ?? 0,
                    'ft_amount' => $item->ft_amount ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    's_rate' => $item->s_rate ?? 0,
                    'ws_rate' => 0,
                    'spl_rate' => 0,
                    'packing' => '',
                    'unit' => '',
                    'company_short_name' => '',
                    'location' => '',
                ];

                // Get rates from batch if available
                if ($item->batch) {
                    $itemData['mrp'] = $item->batch->mrp ?? $itemData['mrp'];
                    $itemData['s_rate'] = $item->batch->s_rate ?? $itemData['s_rate'];
                    $itemData['ws_rate'] = $item->batch->ws_rate ?? 0;
                    $itemData['spl_rate'] = $item->batch->spl_rate ?? 0;
                    $itemData['location'] = $item->batch->location ?? '';
                }

                // Get item details
                if ($item->item) {
                    $itemData['packing'] = $item->item->packing ?? '';
                    $itemData['unit'] = $item->item->unit ?? '';
                    $itemData['company_short_name'] = $item->item->company_short_name ?? '';
                    // Use item MRP if batch doesn't have it
                    if (!$itemData['mrp'] && $item->item->mrp) {
                        $itemData['mrp'] = $item->item->mrp;
                    }
                }

                return $itemData;
            });

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'trf_no' => $transaction->trf_no,
                    'transaction_date' => $transaction->transaction_date ? (is_string($transaction->transaction_date) ? $transaction->transaction_date : $transaction->transaction_date->format('Y-m-d')) : '',
                    'supplier_id' => $transaction->supplier_id,
                    'supplier_name' => $transaction->supplier ? $transaction->supplier->name : $transaction->supplier_name,
                    'st_date' => $transaction->st_date ? (is_string($transaction->st_date) ? $transaction->st_date : $transaction->st_date->format('Y-m-d')) : '',
                    'gr_no' => $transaction->gr_no,
                    'gr_date' => $transaction->gr_date ? (is_string($transaction->gr_date) ? $transaction->gr_date : $transaction->gr_date->format('Y-m-d')) : '',
                    'cases' => $transaction->cases,
                    'transport' => $transaction->transport,
                    'remarks' => $transaction->remarks,
                    'total_amount' => $transaction->total_amount,
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
            \Log::error('StockTransferIncoming getDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPastTransactions()
    {
        try {
            $transactions = StockTransferIncomingTransaction::with('supplier:supplier_id,name')
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get()
                ->map(function($t) {
                    return [
                        'id' => $t->id,
                        'trf_no' => $t->trf_no,
                        'transaction_date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d') : '',
                        'supplier_name' => $t->supplier ? $t->supplier->name : $t->supplier_name,
                        'total_amount' => $t->total_amount ?? 0
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
        $transaction = StockTransferIncomingTransaction::with(['supplier', 'items'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.stock-transfer-incoming.show', compact('transaction'));
    }
}
