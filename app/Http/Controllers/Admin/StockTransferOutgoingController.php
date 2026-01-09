<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransferOutgoingTransaction;
use App\Models\StockTransferOutgoingTransactionItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\StockLedger;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;

class StockTransferOutgoingController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = StockTransferOutgoingTransaction::query();

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'transfer_to':
                    $query->where('transfer_to_name', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'sr_no':
                    $query->where('sr_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'challan_no':
                    $query->where('challan_no', 'LIKE', "%{$searchTerm}%");
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        // AJAX request for Load Invoices feature - check BEFORE pagination
        if ($request->ajax() || $request->has('ajax')) {
            $allTransactions = (clone $query)->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();
            
            return response()->json([
                'success' => true,
                'transactions' => $allTransactions
            ]);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.stock-transfer-outgoing.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = StockTransferOutgoingTransaction::with(['items'])->findOrFail($id);
        
        // Return JSON for AJAX requests (stock ledger modal)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id' => $transaction->id,
                'transfer_no' => $transaction->transfer_no ?? $transaction->id,
                'transaction_date' => $transaction->transaction_date,
                'transfer_to_name' => $transaction->transfer_to_name,
                'remarks' => $transaction->remarks,
                'total_items' => $transaction->items->count(),
                'net_amount' => $transaction->net_amount,
                'status' => $transaction->status,
            ]);
        }
        
        return view('admin.stock-transfer-outgoing.show', compact('transaction'));
    }

    public function transaction()
    {
        $nextSrNo = $this->generateSrNo('STO');
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        return view('admin.stock-transfer-outgoing.transaction', compact('nextSrNo', 'customers'));
    }

    public function modification()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        return view('admin.stock-transfer-outgoing.modification', compact('customers'));
    }

    public function storeTransaction(Request $request)
    {
        try {
            // Validate transaction date (no backdating, max 1 day future)
            $dateError = $this->validateTransactionDate($request, 'stock_transfer_outgoing', 'transaction_date');
            if ($dateError) {
                return $this->dateValidationErrorResponse($dateError);
            }
            
            $validated = $request->validate([
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
            ]);

            \DB::beginTransaction();

            $srNo = $this->generateSrNo($request->series ?? 'STO');

            $transaction = StockTransferOutgoingTransaction::create([
                'sr_no' => $srNo,
                'series' => $request->series ?? 'STO',
                'transaction_date' => $request->transaction_date,
                'transfer_to' => $request->transfer_to,
                'transfer_to_name' => $request->transfer_to_name,
                'challan_no' => $request->gr_no ?? $request->challan_no,
                'challan_date' => $request->gr_date ?? $request->challan_date,
                'cases' => $request->cases ?? 0,
                'transport' => $request->transport,
                'gst_vno' => $request->gst_vno ?? 'N',
                'with_gst' => $request->with_gst ?? 'N',
                'mrp_value' => $request->summary_mrp_value ?? 0,
                'gross_amount' => $request->summary_gross ?? 0,
                'discount_amount' => $request->summary_discount ?? 0,
                'scheme_amount' => $request->summary_scheme ?? 0,
                'tax_amount' => $request->summary_tax ?? 0,
                'net_amount' => $request->summary_net ?? 0,
                'remarks' => $request->remarks,
                'status' => 'active',
                'created_by' => auth()->user()->user_id ?? null,
            ]);

            foreach ($request->items as $index => $item) {
                if (empty($item['code']) || empty($item['name'])) continue;

                $itemModel = Item::find($item['code']);
                $batchModel = null;
                $qty = floatval($item['qty'] ?? 0);
                $fQty = floatval($item['f_qty'] ?? 0);
                $totalQty = $qty + $fQty;
                
                if (!empty($item['batch'])) {
                    $batchModel = Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                    
                    // If batch doesn't exist and new_batch_data is provided, create new batch
                    if (!$batchModel && !empty($item['new_batch_data'])) {
                        $newBatchData = $item['new_batch_data'];
                        
                        // Parse expiry date
                        $expiryDate = null;
                        if (!empty($newBatchData['expiry'])) {
                            $parts = explode('/', $newBatchData['expiry']);
                            if (count($parts) == 2) {
                                $expiryDate = $parts[1] . '-' . $parts[0] . '-01';
                            }
                        }
                        
                        // Create new batch with zero quantity (StockLedger observer will handle qty)
                        $batchModel = Batch::create([
                            'item_id' => $item['code'],
                            'item_code' => $itemModel ? $itemModel->item_code : null,
                            'item_name' => $itemModel ? $itemModel->item_name : $item['name'],
                            'batch_no' => $item['batch'],
                            'expiry_date' => $expiryDate,
                            'mrp' => $newBatchData['mrp'] ?? 0,
                            's_rate' => $newBatchData['s_rate'] ?? 0,
                            'pur_rate' => $itemModel ? $itemModel->pur_rate : 0,
                            'qty' => 0,
                            'free_qty' => 0,
                            'total_qty' => 0,
                            'packing' => $itemModel ? $itemModel->packing : null,
                            'company_name' => $itemModel ? $itemModel->mfg_by : null,
                            'status' => 'active',
                            'is_deleted' => 0,
                        ]);
                    }
                    // Don't manually decrement - StockLedger observer will handle it
                }

                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                // Get rates - prefer request data, then batch rates, fallback to item rates
                $mrp = $item['mrp'] ?? ($batchModel ? $batchModel->mrp : ($itemModel ? $itemModel->mrp : 0));
                $sRate = $item['s_rate'] ?? ($batchModel ? $batchModel->s_rate : ($itemModel ? $itemModel->s_rate : 0));
                $pRate = $item['p_rate'] ?? ($batchModel ? $batchModel->pur_rate : ($itemModel ? $itemModel->pur_rate : 0));

                $transactionItem = StockTransferOutgoingTransactionItem::create([
                    'stock_transfer_outgoing_transaction_id' => $transaction->id,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'item_code' => $item['code'],
                    'item_name' => $item['name'],
                    'batch_no' => $item['batch'] ?? null,
                    'expiry' => $item['expiry'] ?? null,
                    'qty' => $qty,
                    'f_qty' => $fQty,
                    'mrp' => $mrp,
                    'p_rate' => $pRate,
                    's_rate' => $sRate,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'hsn_code' => $itemModel ? $itemModel->hsn_code : null,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'tax_percent' => $cgstPercent + $sgstPercent,
                    'packing' => $itemModel ? $itemModel->packing : null,
                    'company_name' => $itemModel ? $itemModel->mfg_by : null,
                    'row_order' => $index,
                ]);

                // Create stock ledger entry for outgoing transfer
                StockLedger::create([
                    'trans_no' => $srNo,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'transaction_type' => 'OUT',
                    'quantity' => $qty,
                    'free_quantity' => $fQty,
                    'reference_type' => 'StockTransferOutgoing',
                    'reference_id' => $transaction->id,
                    'transaction_date' => $request->transaction_date,
                    'rate' => $sRate,
                    'remarks' => 'Stock Transfer Outgoing - ' . ($request->transfer_to_name ?? 'N/A'),
                    'created_by' => auth()->user()->user_id ?? null,
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Outgoing saved successfully',
                'transaction_id' => $transaction->id,
                'sr_no' => $srNo
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error storing stock transfer outgoing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        try {
            \DB::beginTransaction();

            $transaction = StockTransferOutgoingTransaction::findOrFail($id);

            // Delete old stock ledger entries (observer will reverse batch quantities automatically)
            StockLedger::where('reference_type', 'StockTransferOutgoing')
                ->where('reference_id', $transaction->id)
                ->delete();

            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'transfer_to' => $request->transfer_to,
                'transfer_to_name' => $request->transfer_to_name,
                'challan_no' => $request->gr_no ?? $request->challan_no,
                'challan_date' => $request->gr_date ?? $request->challan_date,
                'cases' => $request->cases ?? 0,
                'transport' => $request->transport,
                'gst_vno' => $request->gst_vno ?? 'N',
                'with_gst' => $request->with_gst ?? 'N',
                'mrp_value' => $request->summary_mrp_value ?? 0,
                'gross_amount' => $request->summary_gross ?? 0,
                'discount_amount' => $request->summary_discount ?? 0,
                'scheme_amount' => $request->summary_scheme ?? 0,
                'tax_amount' => $request->summary_tax ?? 0,
                'net_amount' => $request->summary_net ?? 0,
                'remarks' => $request->remarks,
                'updated_by' => auth()->user()->user_id ?? null,
            ]);

            StockTransferOutgoingTransactionItem::where('stock_transfer_outgoing_transaction_id', $transaction->id)->delete();

            foreach ($request->items as $index => $item) {
                if (empty($item['code']) || empty($item['name'])) continue;

                $itemModel = Item::find($item['code']);
                $batchModel = null;
                $qty = floatval($item['qty'] ?? 0);
                $fQty = floatval($item['f_qty'] ?? 0);
                $totalQty = $qty + $fQty;
                
                if (!empty($item['batch'])) {
                    $batchModel = Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                    // Don't manually decrement - StockLedger observer will handle it
                }

                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                $mrp = $item['mrp'] ?? ($batchModel ? $batchModel->mrp : ($itemModel ? $itemModel->mrp : 0));
                $sRate = $item['s_rate'] ?? ($batchModel ? $batchModel->s_rate : ($itemModel ? $itemModel->s_rate : 0));
                $pRate = $item['p_rate'] ?? ($batchModel ? $batchModel->pur_rate : ($itemModel ? $itemModel->pur_rate : 0));

                StockTransferOutgoingTransactionItem::create([
                    'stock_transfer_outgoing_transaction_id' => $transaction->id,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'item_code' => $item['code'],
                    'item_name' => $item['name'],
                    'batch_no' => $item['batch'] ?? null,
                    'expiry' => $item['expiry'] ?? null,
                    'qty' => $qty,
                    'f_qty' => $fQty,
                    'mrp' => $mrp,
                    'p_rate' => $pRate,
                    's_rate' => $sRate,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'hsn_code' => $itemModel ? $itemModel->hsn_code : null,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'tax_percent' => $cgstPercent + $sgstPercent,
                    'packing' => $itemModel ? $itemModel->packing : null,
                    'company_name' => $itemModel ? $itemModel->mfg_by : null,
                    'row_order' => $index,
                ]);

                // Create new stock ledger entry
                StockLedger::create([
                    'trans_no' => $transaction->sr_no,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'transaction_type' => 'OUT',
                    'quantity' => $qty,
                    'free_quantity' => $fQty,
                    'reference_type' => 'StockTransferOutgoing',
                    'reference_id' => $transaction->id,
                    'transaction_date' => $request->transaction_date,
                    'rate' => $sRate,
                    'remarks' => 'Stock Transfer Outgoing - ' . ($request->transfer_to_name ?? 'N/A'),
                    'created_by' => auth()->user()->user_id ?? null,
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Outgoing updated successfully',
                'transaction_id' => $transaction->id,
                'sr_no' => $transaction->sr_no
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating stock transfer outgoing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            
            $transaction = StockTransferOutgoingTransaction::findOrFail($id);
            
            // Delete stock ledger entries (observer will reverse batch quantities automatically)
            StockLedger::where('reference_type', 'StockTransferOutgoing')
                ->where('reference_id', $id)
                ->delete();
            
            StockTransferOutgoingTransactionItem::where('stock_transfer_outgoing_transaction_id', $id)->delete();
            $transaction->delete();
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBySrNo($srNo)
    {
        try {
            $transaction = StockTransferOutgoingTransaction::with(['items'])
                ->where('sr_no', $srNo)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Enrich items with additional item details
            $enrichedItems = $transaction->items->map(function($item) {
                $itemModel = Item::with('company')->find($item->item_id);
                $batchModel = Batch::where('item_id', $item->item_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();
                
                // Get company name from multiple sources
                $companyName = $item->company_name;
                if (empty($companyName) && $itemModel) {
                    $companyName = $itemModel->mfg_by;
                    if (empty($companyName) && $itemModel->company) {
                        $companyName = $itemModel->company->short_name ?? $itemModel->company->name ?? '';
                    }
                }
                
                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry' => $item->expiry,
                    'qty' => $item->qty,
                    'f_qty' => $item->f_qty,
                    'mrp' => $item->mrp,
                    'p_rate' => $item->p_rate,
                    's_rate' => $item->s_rate,
                    'rate' => $item->s_rate,
                    'amount' => $item->amount,
                    'packing' => $item->packing ?? ($itemModel ? $itemModel->packing : ''),
                    'company_name' => $companyName ?? '',
                    'unit' => $itemModel ? ($itemModel->unit ?? '') : '',
                    'location' => $itemModel ? ($itemModel->location ?? '') : '',
                    'cl_qty' => $batchModel ? $batchModel->qty : 0,
                ];
            });

            $transactionData = $transaction->toArray();
            $transactionData['items'] = $enrichedItems;

            return response()->json([
                'success' => true,
                'transaction' => $transactionData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateSrNo($series = 'STO')
    {
        $lastTransaction = StockTransferOutgoingTransaction::where('series', $series)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->sr_no, strlen($series) + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $series . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
