<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Item;
use App\Models\ReplacementReceivedTransaction;
use App\Models\ReplacementReceivedTransactionItem;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\PurchaseReturnTransaction;
use App\Models\ReplacementReceivedAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReplacementReceivedController extends Controller
{
    public function index(Request $request)
    {
        $query = ReplacementReceivedTransaction::with(['supplier:supplier_id,name']);

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
                case 'rr_no':
                    $query->where('rr_no', 'LIKE', "%{$searchTerm}%");
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

        return view('admin.replacement-received.index', compact('transactions'));
    }

    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $nextRrNo = ReplacementReceivedTransaction::generateRRNumber();
        
        return view('admin.replacement-received.transaction', compact('suppliers', 'nextRrNo'));
    }

    public function modification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.replacement-received.modification', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rrNo = ReplacementReceivedTransaction::generateRRNumber();

            $transaction = ReplacementReceivedTransaction::create([
                'rr_no' => $rrNo,
                'series' => 'RR',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'total_amount' => $request->total_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'packing' => $request->packing,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'case_no' => $request->case_no,
                'box' => $request->box,
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
                
                // INCREASE quantity in batch (opposite of replacement note)
                if ($existingBatchId) {
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty + $qty;
                        $existingBatch->total_qty = $existingBatch->total_qty + $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                } else {
                    // Create new batch with POSITIVE quantity
                    $sRate = isset($item['new_batch_s_rate']) ? floatval($item['new_batch_s_rate']) : 0;
                    $location = $item['new_batch_location'] ?? '';
                    $ws_rate = isset($item['new_batch_ws_rate']) ? floatval($item['new_batch_ws_rate']) : 0;
                    $spl_rate = isset($item['new_batch_spl_rate']) ? floatval($item['new_batch_spl_rate']) : 0;
                    $p_rate = isset($item['new_batch_p_rate']) ? floatval($item['new_batch_p_rate']) : 0;
                    
                    $newBatch = Batch::create([
                        'item_id' => $item['item_id'],
                        'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                        'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                        'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => $qty, // POSITIVE quantity
                        'total_qty' => $qty,
                        'mrp' => $item['mrp'] ?? 0,
                        'pur_rate' => $p_rate,
                        's_rate' => $sRate,
                        'ws_rate' => $ws_rate,
                        'spl_rate' => $spl_rate,
                        'location' => $location,
                        'replacement_received_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                }
                
                $transactionItem = ReplacementReceivedTransactionItem::create([
                    'replacement_received_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
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
                $stockLedger->reference_type = 'REPLACEMENT_RECEIVED';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->ft_rate;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            // Save adjustments if provided
            $adjustments = $request->adjustments ?? [];
            $totalAdjusted = 0;
            
            foreach ($adjustments as $adjustment) {
                if (empty($adjustment['purchase_return_id']) || empty($adjustment['amount']) || $adjustment['amount'] <= 0) {
                    continue;
                }

                $adjustedAmount = floatval($adjustment['amount']);
                
                ReplacementReceivedAdjustment::create([
                    'replacement_received_id' => $transaction->id,
                    'purchase_return_id' => $adjustment['purchase_return_id'],
                    'adjusted_amount' => $adjustedAmount,
                    'adjustment_date' => $transaction->transaction_date,
                    'created_by' => Auth::id(),
                ]);
                
                $totalAdjusted += $adjustedAmount;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Replacement received saved successfully!',
                'rr_no' => $rrNo,
                'id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Replacement Received Error: ' . $e->getMessage());
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

            $transaction = ReplacementReceivedTransaction::with('items')->findOrFail($id);
            
            // Restore old batch quantities (reverse the previous increase)
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->batch_id) {
                    $oldBatch = Batch::find($oldItem->batch_id);
                    if ($oldBatch) {
                        if ($oldBatch->replacement_received_id == $transaction->id) {
                            $oldBatch->delete();
                        } else {
                            $oldBatch->qty = $oldBatch->qty - $oldItem->qty;
                            $oldBatch->total_qty = ($oldBatch->total_qty ?? 0) - $oldItem->qty;
                            $oldBatch->save();
                        }
                    }
                }
            }
            
            StockLedger::where('reference_type', 'REPLACEMENT_RECEIVED')
                ->where('reference_id', $id)
                ->delete();

            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'total_amount' => $request->total_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'packing' => $request->packing,
                'unit' => $request->unit,
                'cl_qty' => $request->cl_qty ?? 0,
                'comp' => $request->comp,
                'lctn' => $request->lctn,
                'srlno' => $request->srlno,
                'case_no' => $request->case_no,
                'box' => $request->box,
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
                
                if ($existingBatchId && !str_starts_with($existingBatchId, 'new_')) {
                    $existingBatch = Batch::find($existingBatchId);
                    if ($existingBatch) {
                        $existingBatch->qty = $existingBatch->qty + $qty;
                        $existingBatch->total_qty = ($existingBatch->total_qty ?? 0) + $qty;
                        $existingBatch->save();
                        $batchIdForItem = $existingBatch->id;
                    }
                } else {
                    $sRate = isset($item['new_batch_s_rate']) ? floatval($item['new_batch_s_rate']) : 0;
                    $location = $item['new_batch_location'] ?? '';
                    $ws_rate = isset($item['new_batch_ws_rate']) ? floatval($item['new_batch_ws_rate']) : 0;
                    $spl_rate = isset($item['new_batch_spl_rate']) ? floatval($item['new_batch_spl_rate']) : 0;
                    $p_rate = isset($item['new_batch_p_rate']) ? floatval($item['new_batch_p_rate']) : 0;
                    
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
                        'replacement_received_id' => $transaction->id,
                        'is_deleted' => 0,
                    ]);
                    $batchIdForItem = $newBatch->id;
                }
                
                $transactionItem = ReplacementReceivedTransactionItem::create([
                    'replacement_received_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
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
                $stockLedger->reference_type = 'REPLACEMENT_RECEIVED';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $transactionItem->ft_rate;
                $stockLedger->created_by = Auth::id();
                $stockLedger->saveQuietly();
            }

            // Delete old adjustments and save new ones
            $transaction->adjustments()->delete();
            
            $adjustments = $request->adjustments ?? [];
            foreach ($adjustments as $adjustment) {
                if (empty($adjustment['purchase_return_id']) || empty($adjustment['amount']) || $adjustment['amount'] <= 0) {
                    continue;
                }

                ReplacementReceivedAdjustment::create([
                    'replacement_received_id' => $transaction->id,
                    'purchase_return_id' => $adjustment['purchase_return_id'],
                    'adjusted_amount' => floatval($adjustment['amount']),
                    'adjustment_date' => $transaction->transaction_date,
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Replacement Received Update Error: ' . $e->getMessage());
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

            $transaction = ReplacementReceivedTransaction::with('items')->findOrFail($id);
            
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        if ($batch->replacement_received_id == $transaction->id) {
                            $batch->delete();
                        } else {
                            $batch->qty = $batch->qty - $item->qty;
                            $batch->total_qty = ($batch->total_qty ?? 0) - $item->qty;
                            $batch->save();
                        }
                    }
                }
            }

            StockLedger::where('reference_type', 'REPLACEMENT_RECEIVED')
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
            $transaction = ReplacementReceivedTransaction::with(['items.batch', 'items.item', 'supplier'])
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
                    'mrp' => $item->mrp,
                    'discount_percent' => $item->discount_percent ?? 0,
                    'ft_rate' => $item->ft_rate,
                    'ft_amount' => $item->ft_amount,
                ];

                // Add batch data if available
                if ($item->batch) {
                    $itemData['s_rate'] = $item->batch->s_rate ?? 0;
                    $itemData['ws_rate'] = $item->batch->ws_rate ?? 0;
                    $itemData['spl_rate'] = $item->batch->spl_rate ?? 0;
                    $itemData['pur_rate'] = $item->batch->pur_rate ?? 0;
                    $itemData['location'] = $item->batch->location ?? '';
                }

                // Add item data if available
                if ($item->item) {
                    $itemData['packing'] = $item->item->packing ?? '';
                    $itemData['unit'] = $item->item->unit ?? '';
                    $itemData['company_short_name'] = $item->item->company_short_name ?? '';
                }

                return $itemData;
            });

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'rr_no' => $transaction->rr_no,
                    'transaction_date' => $transaction->transaction_date,
                    'supplier_id' => $transaction->supplier_id,
                    'supplier_name' => $transaction->supplier ? $transaction->supplier->name : $transaction->supplier_name,
                    'total_amount' => $transaction->total_amount,
                    'p_scm_percent' => $transaction->p_scm_percent ?? 0,
                    'p_scm_amount' => $transaction->p_scm_amount ?? 0,
                    's_scm_percent' => $transaction->s_scm_percent ?? 0,
                    's_scm_amount' => $transaction->s_scm_amount ?? 0,
                    'packing' => $transaction->packing,
                    'unit' => $transaction->unit,
                    'cl_qty' => $transaction->cl_qty,
                    'comp' => $transaction->comp,
                    'lctn' => $transaction->lctn,
                    'amt' => $transaction->amt ?? 0,
                    'srlno' => $transaction->srlno,
                ],
                'items' => $items
            ]);
        } catch (\Exception $e) {
            \Log::error('ReplacementReceived getDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPastTransactions()
    {
        try {
            $transactions = ReplacementReceivedTransaction::with('supplier:supplier_id,name')
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get()
                ->map(function($t) {
                    return [
                        'id' => $t->id,
                        'rr_no' => $t->rr_no,
                        'transaction_date' => $t->transaction_date->format('Y-m-d'),
                        'supplier_name' => $t->supplier ? $t->supplier->name : $t->supplier_name,
                        'total_amount' => number_format($t->total_amount, 2)
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

    /**
     * Get supplier's purchase return transactions for adjustment
     */
    public function getSupplierPurchaseReturns($supplierId)
    {
        try {
            // Get all purchase return transactions for this supplier
            $purchaseReturns = PurchaseReturnTransaction::where('supplier_id', $supplierId)
                ->where('status', '!=', 'deleted')
                ->orderBy('return_date', 'desc')
                ->get()
                ->map(function ($pr) {
                    // Calculate already adjusted amount from replacement_received_adjustments
                    $adjustedAmount = ReplacementReceivedAdjustment::where('purchase_return_id', $pr->id)
                        ->sum('adjusted_amount');
                    
                    // Total amount (use net_amount)
                    $totalAmount = $pr->net_amount ?? 0;
                    
                    // Calculate balance (Total - Already Adjusted)
                    $balanceAmount = $totalAmount - $adjustedAmount;

                    return [
                        'id' => $pr->id,
                        'pr_no' => $pr->pr_no,
                        'return_date' => $pr->return_date ? $pr->return_date->format('d-M-y') : '',
                        'total_amount' => number_format($totalAmount, 2, '.', ''),
                        'adjusted_amount' => number_format($adjustedAmount, 2, '.', ''),
                        'balance_amount' => number_format(max(0, $balanceAmount), 2, '.', ''),
                    ];
                })
                ->filter(function ($pr) {
                    // Only show purchase returns with balance > 0
                    return floatval($pr['balance_amount']) > 0;
                })
                ->values();

            return response()->json([
                'success' => true,
                'purchase_returns' => $purchaseReturns
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Supplier Purchase Returns Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading purchase returns: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing adjustments for a replacement received transaction
     */
    public function getAdjustments($id)
    {
        try {
            $adjustments = ReplacementReceivedAdjustment::where('replacement_received_id', $id)
                ->with('purchaseReturn:id,pr_no,return_date,net_amount')
                ->get()
                ->map(function ($adj) {
                    return [
                        'id' => $adj->id,
                        'purchase_return_id' => $adj->purchase_return_id,
                        'pr_no' => $adj->purchaseReturn ? $adj->purchaseReturn->pr_no : '',
                        'return_date' => $adj->purchaseReturn && $adj->purchaseReturn->return_date 
                            ? $adj->purchaseReturn->return_date->format('d-M-y') : '',
                        'bill_amount' => $adj->purchaseReturn ? number_format($adj->purchaseReturn->net_amount, 2, '.', '') : '0.00',
                        'adjusted_amount' => number_format($adj->adjusted_amount, 2, '.', ''),
                    ];
                });

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Adjustments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading adjustments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show transaction details
     */
    public function show($id)
    {
        $transaction = ReplacementReceivedTransaction::with(['supplier', 'items', 'adjustments.purchaseReturn'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.replacement-received.show', compact('transaction'));
    }
}
