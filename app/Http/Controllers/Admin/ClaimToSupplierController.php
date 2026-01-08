<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Batch;
use App\Models\ClaimToSupplierTransaction;
use App\Models\ClaimToSupplierTransactionItem;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ValidatesTransactionDate;

class ClaimToSupplierController extends Controller
{
    use ValidatesTransactionDate;
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        return view('admin.claim-to-supplier.transaction', compact('suppliers'));
    }

    public function modification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        return view('admin.claim-to-supplier.modification', compact('suppliers'));
    }

    public function index(Request $request)
    {
        $query = ClaimToSupplierTransaction::query();

        if ($request->filled('search')) {
            $filterBy = $request->get('filter_by', 'supplier_name');
            $searchTerm = $request->get('search');

            switch ($filterBy) {
                case 'supplier_name':
                    $query->where('supplier_name', 'LIKE', '%' . $searchTerm . '%');
                    break;
                case 'claim_no':
                    $query->where('claim_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                case 'invoice_no':
                    $query->where('invoice_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
            }
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('claim_date', [$request->get('date_from'), $request->get('date_to')]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('claim_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('claim_date', '<=', $request->get('date_to'));
        }

        $query->orderBy('claim_date', 'desc')->orderBy('id', 'desc');
        $transactions = $query->paginate(10)->withQueryString();

        return view('admin.claim-to-supplier.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = ClaimToSupplierTransaction::with(['items', 'supplier'])->findOrFail($id);
        return view('admin.claim-to-supplier.show', compact('transaction'));
    }

    public function getNextTransactionNumber()
    {
        return response()->json(['next_trn_no' => ClaimToSupplierTransaction::generateClaimNumber()]);
    }

    public function getBatches(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        $itemId = $request->input('item_id');

        if (!$supplierId || !$itemId) {
            return response()->json(['batches' => []]);
        }

        $item = Item::with('company:id,name')->find($itemId);
        $totalClQty = Batch::where('item_id', $itemId)->where('is_deleted', 0)->sum('qty');

        $batches = Batch::where('item_id', $itemId)
            ->where('is_deleted', 0)
            ->where('qty', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function($batch) use ($item, $totalClQty) {
                return [
                    'batch_id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'expiry' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '',
                    'qty' => $batch->qty ?? 0,
                    'available_qty' => max(0, $batch->qty ?? 0),
                    'mrp' => $batch->mrp ?? 0,
                    'purchase_rate' => $batch->pur_rate ?? 0,
                    's_rate' => $batch->s_rate ?? 0,
                    'ws_rate' => $batch->ws_rate ?? 0,
                    'spl_rate' => $batch->spl_rate ?? 0,
                    'hsn_code' => $item->hsn_code ?? '',
                    'packing' => $item->packing ?? '',
                    'unit' => $item->unit ?? 'PCS',
                    'company_name' => $item->company->name ?? '',
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'total_cl_qty' => $totalClQty,
                ];
            });

        return response()->json(['batches' => $batches]);
    }

    public function store(Request $request)
    {
        // Validate transaction date
        $dateError = $this->validateTransactionDate($request, 'claim_to_supplier', 'claim_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }

        try {
            DB::beginTransaction();

            $claimNo = ClaimToSupplierTransaction::generateClaimNumber();

            $claim = ClaimToSupplierTransaction::create([
                'claim_no' => $claimNo,
                'series' => 'CTS',
                'claim_date' => $request->claim_date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'gst_vno' => $request->gst_vno,
                'tax_flag' => $request->tax_flag ?? 'Y',
                'narration' => $request->narration,
                // Additional Details
                'blank_statement' => $request->blank_statement ?? 'Y',
                'rate_type' => $request->rate_type ?? 'R',
                'filter_from_date' => $request->filter_from_date,
                'filter_to_date' => $request->filter_to_date,
                'company_code' => $request->company_code,
                'division' => $request->division ?? '00',
                // Amount fields
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'dis1_amount' => $request->dis1_amount ?? 0,
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
                $expiryInput = $item['expiry'] ?? $item['expiry_date'] ?? null;
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
                
                $claimItem = ClaimToSupplierTransactionItem::create([
                    'claim_to_supplier_transaction_id' => $claim->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'pur_rate' => $item['purchase_rate'] ?? $item['pur_rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'ws_rate' => $item['ws_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'spl_rate' => $item['spl_rate'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cess_percent' => $item['cess_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'cess_amount' => $item['cess_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Update batch quantity (decrease for claim)
                if ($claimItem->batch_id) {
                    $batch = Batch::find($claimItem->batch_id);
                    if ($batch) {
                        $claimQty = ($claimItem->qty ?? 0) + ($claimItem->free_qty ?? 0);
                        $batch->qty = $batch->qty - $claimQty;
                        $batch->save();
                    }
                    
                    $stockLedger = new StockLedger();
                    $stockLedger->item_id = $claimItem->item_id;
                    $stockLedger->batch_id = $claimItem->batch_id;
                    $stockLedger->transaction_type = 'OUT';
                    $stockLedger->reference_type = 'CLAIM_TO_SUPPLIER';
                    $stockLedger->reference_id = $claim->id;
                    $stockLedger->transaction_date = $claim->claim_date;
                    $stockLedger->quantity = $claimItem->qty;
                    $stockLedger->free_quantity = $claimItem->free_qty;
                    $stockLedger->rate = $claimItem->pur_rate;
                    $stockLedger->created_by = Auth::id();
                    $stockLedger->saveQuietly();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim to supplier saved successfully!',
                'claim_no' => $claimNo,
                'id' => $claim->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Claim to Supplier Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving claim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPastClaims(Request $request)
    {
        try {
            $date = $request->get('date');
            $supplierId = $request->get('supplier_id');

            $query = ClaimToSupplierTransaction::with('supplier:supplier_id,name')
                ->where(function($q) {
                    $q->whereNull('status')
                      ->orWhere('status', '!=', 'deleted');
                })
                ->orderBy('id', 'desc');

            if ($date) {
                $query->whereDate('claim_date', $date);
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
                    'claim_no' => $trn->claim_no,
                    'claim_date' => $trn->claim_date ? $trn->claim_date->format('d-M-y') : '',
                    'supplier_id' => $trn->supplier_id,
                    'supplier_name' => $trn->supplier ? $trn->supplier->name : ($trn->supplier_name ?? ''),
                    'time' => $trn->created_at ? $trn->created_at->format('H:i') : '',
                    'amount' => number_format($trn->net_amount ?? 0, 2, '.', ''),
                    'net_amount' => $trn->net_amount ?? 0,
                    'balance_amount' => $trn->balance_amount ?? $trn->net_amount ?? 0,
                    'status' => $trn->status ?? 'active',
                ];
            });

            return response()->json(['success' => true, 'transactions' => $transactions]);
        } catch (\Exception $e) {
            Log::error('Get Past Claims Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading past claims'], 500);
        }
    }

    public function getClaimDetails($id)
    {
        try {
            $transaction = ClaimToSupplierTransaction::with(['items', 'supplier:supplier_id,name'])->findOrFail($id);

            $header = [
                'id' => $transaction->id,
                'claim_no' => $transaction->claim_no,
                'claim_date' => $transaction->claim_date ? $transaction->claim_date->format('Y-m-d') : null,
                'supplier_id' => $transaction->supplier_id,
                'supplier_name' => $transaction->supplier_name ?? ($transaction->supplier->name ?? ''),
                'invoice_no' => $transaction->invoice_no,
                'invoice_date' => $transaction->invoice_date ? $transaction->invoice_date->format('Y-m-d') : null,
                'gst_vno' => $transaction->gst_vno,
                'tax_flag' => $transaction->tax_flag,
                'narration' => $transaction->narration,
                'nt_amount' => $transaction->nt_amount ?? 0,
                'sc_amount' => $transaction->sc_amount ?? 0,
                'dis_amount' => $transaction->dis_amount ?? 0,
                'scm_amount' => $transaction->scm_amount ?? 0,
                'scm_percent' => $transaction->scm_percent ?? 0,
                'tax_amount' => $transaction->tax_amount ?? 0,
                'net_amount' => $transaction->net_amount ?? 0,
                'tcs_amount' => $transaction->tcs_amount ?? 0,
                'dis1_amount' => $transaction->dis1_amount ?? 0,
                'status' => $transaction->status,
            ];

            $items = $transaction->items->map(function ($item) {
                $totalClQty = Batch::where('item_id', $item->item_id)->where('is_deleted', 0)->sum('qty');
                
                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'batch_id' => $item->batch_id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry' => $item->expiry_date ? $item->expiry_date->format('m/Y') : null,
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty,
                    'pur_rate' => $item->pur_rate,
                    'dis_percent' => $item->dis_percent,
                    'ft_rate' => $item->ft_rate,
                    'ft_amount' => $item->ft_amount,
                    'mrp' => $item->mrp,
                    'ws_rate' => $item->ws_rate,
                    's_rate' => $item->s_rate,
                    'spl_rate' => $item->spl_rate,
                    'cgst_percent' => $item->cgst_percent,
                    'sgst_percent' => $item->sgst_percent,
                    'cess_percent' => $item->cess_percent,
                    'cgst_amount' => $item->cgst_amount,
                    'sgst_amount' => $item->sgst_amount,
                    'cess_amount' => $item->cess_amount,
                    'tax_amount' => $item->tax_amount,
                    'net_amount' => $item->net_amount,
                    'hsn_code' => $item->hsn_code,
                    'packing' => $item->packing,
                    'unit' => $item->unit,
                    'company_name' => $item->company_name,
                    'total_cl_qty' => $totalClQty,
                ];
            });

            return response()->json(['success' => true, 'header' => $header, 'items' => $items]);
        } catch (\Exception $e) {
            Log::error('Get Claim Details Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading claim details'], 500);
        }
    }

    public function getByClaimNo($claimNo)
    {
        try {
            $transaction = ClaimToSupplierTransaction::where('claim_no', $claimNo)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$transaction) {
                return response()->json(['success' => false, 'message' => 'Claim not found'], 404);
            }

            return $this->getClaimDetails($transaction->id);
        } catch (\Exception $e) {
            Log::error('Get Claim By No Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error searching claim'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $claim = ClaimToSupplierTransaction::findOrFail($id);
            $oldItems = $claim->items;
            
            // Revert old batch quantities
            foreach ($oldItems as $oldItem) {
                if ($oldItem->batch_id) {
                    $batch = Batch::find($oldItem->batch_id);
                    if ($batch) {
                        $claimQty = ($oldItem->qty ?? 0) + ($oldItem->free_qty ?? 0);
                        $batch->qty = $batch->qty + $claimQty;
                        $batch->save();
                    }
                }
            }

            $claim->update([
                'claim_date' => $request->claim_date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'gst_vno' => $request->gst_vno,
                'tax_flag' => $request->tax_flag ?? 'Y',
                'narration' => $request->narration,
                // Additional Details
                'blank_statement' => $request->blank_statement ?? 'Y',
                'rate_type' => $request->rate_type ?? 'R',
                'filter_from_date' => $request->filter_from_date,
                'filter_to_date' => $request->filter_to_date,
                'company_code' => $request->company_code,
                'division' => $request->division ?? '00',
                // Amount fields
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'dis1_amount' => $request->dis1_amount ?? 0,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items and stock ledger entries
            StockLedger::where('reference_type', 'CLAIM_TO_SUPPLIER')
                ->where('reference_id', $claim->id)
                ->delete();
            $claim->items()->delete();

            // Create new items
            $items = $request->items ?? [];
            $rowOrder = 0;
            
            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $expiryDate = null;
                $expiryInput = $item['expiry'] ?? $item['expiry_date'] ?? null;
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
                
                $claimItem = ClaimToSupplierTransactionItem::create([
                    'claim_to_supplier_transaction_id' => $claim->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'pur_rate' => $item['purchase_rate'] ?? $item['pur_rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'ws_rate' => $item['ws_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'spl_rate' => $item['spl_rate'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cess_percent' => $item['cess_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'cess_amount' => $item['cess_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                if ($claimItem->batch_id) {
                    $batch = Batch::find($claimItem->batch_id);
                    if ($batch) {
                        $claimQty = ($claimItem->qty ?? 0) + ($claimItem->free_qty ?? 0);
                        $batch->qty = $batch->qty - $claimQty;
                        $batch->save();
                    }
                    
                    $stockLedger = new StockLedger();
                    $stockLedger->item_id = $claimItem->item_id;
                    $stockLedger->batch_id = $claimItem->batch_id;
                    $stockLedger->transaction_type = 'OUT';
                    $stockLedger->reference_type = 'CLAIM_TO_SUPPLIER';
                    $stockLedger->reference_id = $claim->id;
                    $stockLedger->transaction_date = $claim->claim_date;
                    $stockLedger->quantity = $claimItem->qty;
                    $stockLedger->free_quantity = $claimItem->free_qty;
                    $stockLedger->rate = $claimItem->pur_rate;
                    $stockLedger->created_by = Auth::id();
                    $stockLedger->saveQuietly();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim updated successfully!',
                'claim_no' => $claim->claim_no,
                'id' => $claim->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Claim Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating claim: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $claim = ClaimToSupplierTransaction::findOrFail($id);
            
            // Revert batch quantities
            foreach ($claim->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $claimQty = ($item->qty ?? 0) + ($item->free_qty ?? 0);
                        $batch->qty = $batch->qty + $claimQty;
                        $batch->save();
                    }
                }
            }
            
            // Delete stock ledger entries
            StockLedger::where('reference_type', 'CLAIM_TO_SUPPLIER')
                ->where('reference_id', $claim->id)
                ->delete();
            
            $claim->delete();
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Claim deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Claim Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting claim'], 500);
        }
    }
}
