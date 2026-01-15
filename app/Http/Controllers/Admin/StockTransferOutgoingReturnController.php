<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransferOutgoingReturnTransaction;
use App\Models\StockTransferOutgoingReturnTransactionItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\StockLedger;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;

class StockTransferOutgoingReturnController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = StockTransferOutgoingReturnTransaction::query();

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'transfer_to':
                    $query->where('transfer_from_name', 'LIKE', "%{$searchTerm}%");
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

        // AJAX request for Load Invoices feature
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

        return view('admin.stock-transfer-outgoing-return.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = StockTransferOutgoingReturnTransaction::with(['items'])->findOrFail($id);
        
        // Return JSON for AJAX requests (stock ledger modal)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id' => $transaction->id,
                'trf_return_no' => $transaction->trf_return_no ?? $transaction->id,
                'transaction_date' => $transaction->transaction_date,
                'transfer_from_name' => $transaction->transfer_from_name,
                'remarks' => $transaction->remarks,
                'total_items' => $transaction->items->count(),
                'net_amount' => $transaction->net_amount,
                'status' => $transaction->status,
            ]);
        }
        
        return view('admin.stock-transfer-outgoing-return.show', compact('transaction'));
    }

    public function transaction()
    {
        $nextSrNo = $this->generateSrNo('STOR');
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        return view('admin.stock-transfer-outgoing-return.transaction', compact('nextSrNo', 'customers'));
    }

    public function modification()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        return view('admin.stock-transfer-outgoing-return.modification', compact('customers'));
    }


    public function storeTransaction(Request $request)
    {
        try {
            // Validate transaction date (no backdating, max 1 day future)
            $dateError = $this->validateTransactionDate($request, 'stock_transfer_outgoing_return', 'transaction_date');
            if ($dateError) {
                return $this->dateValidationErrorResponse($dateError);
            }
            
            $validated = $request->validate([
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
            ]);

            \DB::beginTransaction();

            $srNo = $this->generateSrNo($request->series ?? 'STOR');

            $transaction = StockTransferOutgoingReturnTransaction::create([
                'sr_no' => $srNo,
                'series' => $request->series ?? 'STOR',
                'transaction_date' => $request->transaction_date,
                'transfer_from' => $request->transfer_to,
                'transfer_from_name' => $request->transfer_to_name,
                'trf_return_no' => $request->trf_return_no,
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
                
                if (!empty($item['batch'])) {
                    $batchModel = Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                }

                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                $mrp = $item['mrp'] ?? ($batchModel ? $batchModel->mrp : ($itemModel ? $itemModel->mrp : 0));
                $sRate = $item['s_rate'] ?? ($batchModel ? $batchModel->s_rate : ($itemModel ? $itemModel->s_rate : 0));
                $pRate = $item['p_rate'] ?? ($batchModel ? $batchModel->pur_rate : ($itemModel ? $itemModel->pur_rate : 0));

                StockTransferOutgoingReturnTransactionItem::create([
                    'stock_transfer_outgoing_return_transaction_id' => $transaction->id,
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

                // Create stock ledger entry for return (IN transaction - stock coming back)
                StockLedger::create([
                    'trans_no' => $srNo,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'transaction_type' => 'IN',
                    'quantity' => $qty,
                    'free_quantity' => $fQty,
                    'reference_type' => 'StockTransferOutgoingReturn',
                    'reference_id' => $transaction->id,
                    'transaction_date' => $request->transaction_date,
                    'rate' => $sRate,
                    'remarks' => 'Stock Transfer Outgoing Return - ' . ($request->transfer_to_name ?? 'N/A'),
                    'created_by' => auth()->user()->user_id ?? null,
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Outgoing Return saved successfully',
                'transaction_id' => $transaction->id,
                'sr_no' => $srNo
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error storing stock transfer outgoing return: ' . $e->getMessage());
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

            $transaction = StockTransferOutgoingReturnTransaction::findOrFail($id);

            // Delete old stock ledger entries
            StockLedger::where('reference_type', 'StockTransferOutgoingReturn')
                ->where('reference_id', $transaction->id)
                ->delete();

            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'transfer_from' => $request->transfer_to,
                'transfer_from_name' => $request->transfer_to_name,
                'trf_return_no' => $request->trf_return_no,
                'challan_no' => $request->gr_no ?? $request->challan_no,
                'challan_date' => $request->gr_date ?? $request->challan_date,
                'cases' => $request->cases ?? 0,
                'transport' => $request->transport,
                'net_amount' => $request->summary_net ?? 0,
                'remarks' => $request->remarks,
                'updated_by' => auth()->user()->user_id ?? null,
            ]);

            StockTransferOutgoingReturnTransactionItem::where('stock_transfer_outgoing_return_transaction_id', $transaction->id)->delete();

            foreach ($request->items as $index => $item) {
                if (empty($item['code']) || empty($item['name'])) continue;

                $itemModel = Item::find($item['code']);
                $batchModel = null;
                $qty = floatval($item['qty'] ?? 0);
                $fQty = floatval($item['f_qty'] ?? 0);
                
                if (!empty($item['batch'])) {
                    $batchModel = Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                }

                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                $mrp = $item['mrp'] ?? ($batchModel ? $batchModel->mrp : ($itemModel ? $itemModel->mrp : 0));
                $sRate = $item['s_rate'] ?? ($batchModel ? $batchModel->s_rate : ($itemModel ? $itemModel->s_rate : 0));
                $pRate = $item['p_rate'] ?? ($batchModel ? $batchModel->pur_rate : ($itemModel ? $itemModel->pur_rate : 0));

                StockTransferOutgoingReturnTransactionItem::create([
                    'stock_transfer_outgoing_return_transaction_id' => $transaction->id,
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
                    'transaction_type' => 'IN',
                    'quantity' => $qty,
                    'free_quantity' => $fQty,
                    'reference_type' => 'StockTransferOutgoingReturn',
                    'reference_id' => $transaction->id,
                    'transaction_date' => $request->transaction_date,
                    'rate' => $sRate,
                    'remarks' => 'Stock Transfer Outgoing Return - ' . ($request->transfer_to_name ?? 'N/A'),
                    'created_by' => auth()->user()->user_id ?? null,
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock Transfer Outgoing Return updated successfully',
                'transaction_id' => $transaction->id,
                'sr_no' => $transaction->sr_no
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating stock transfer outgoing return: ' . $e->getMessage());
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
            
            $transaction = StockTransferOutgoingReturnTransaction::findOrFail($id);
            
            // Delete stock ledger entries
            StockLedger::where('reference_type', 'StockTransferOutgoingReturn')
                ->where('reference_id', $id)
                ->delete();
            
            StockTransferOutgoingReturnTransactionItem::where('stock_transfer_outgoing_return_transaction_id', $id)->delete();
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
            $transaction = StockTransferOutgoingReturnTransaction::with(['items'])
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
            // Map transfer_from to transfer_to for frontend compatibility
            $transactionData['transfer_to'] = $transaction->transfer_from;
            $transactionData['transfer_to_name'] = $transaction->transfer_from_name;
            $transactionData['trf_return_no'] = $transaction->trf_return_no;

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

    private function generateSrNo($series = 'STOR')
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        $lastTransaction = StockTransferOutgoingReturnTransaction::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('series', $series)
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
