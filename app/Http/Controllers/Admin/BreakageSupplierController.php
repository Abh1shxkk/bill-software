<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakageSupplierIssuedTransaction;
use App\Models\BreakageSupplierIssuedTransactionItem;
use App\Models\BreakageSupplierUnusedDumpTransaction;
use App\Models\BreakageSupplierUnusedDumpTransactionItem;
use App\Models\BreakageSupplierReceivedTransaction;
use App\Models\BreakageSupplierReceivedTransactionItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BreakageSupplierController extends Controller
{
    /**
     * Display index of issued transactions
     */
    public function issuedIndex(Request $request)
    {
        $query = BreakageSupplierIssuedTransaction::query()->active();

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'supplier':
                    $query->where('supplier_name', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'narration':
                    $query->where('narration', 'LIKE', "%{$searchTerm}%");
                    break;
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.breakage-supplier.issued-index', compact('transactions'));
    }

    /**
     * Display issued transaction form
     */
    public function issuedTransaction()
    {
        $trnNo = BreakageSupplierIssuedTransaction::generateTrnNumber();
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $brExTypes = BreakageSupplierIssuedTransaction::getBrExTypes();
        $noteTypes = BreakageSupplierIssuedTransaction::getNoteTypes();
        
        return view('admin.breakage-supplier.issued-transaction', compact('trnNo', 'suppliers', 'brExTypes', 'noteTypes'));
    }

    /**
     * Store issued transaction
     */
    public function storeIssued(Request $request)
    {
        try {
            DB::beginTransaction();

            $trnNo = BreakageSupplierIssuedTransaction::generateTrnNumber();

            $transaction = BreakageSupplierIssuedTransaction::create([
                'trn_no' => $trnNo,
                'series' => 'BSI',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'note_type' => $request->note_type ?? 'C',
                'tax_flag' => $request->tax_flag ?? 'N',
                'inc_flag' => $request->inc_flag ?? 'N',
                'gst_vno' => $request->gst_vno,
                'dis_count' => $request->dis_count ?? 0,
                'rpl_count' => $request->rpl_count ?? 0,
                'brk_count' => $request->brk_count ?? 0,
                'exp_count' => $request->exp_count ?? 0,
                'narration' => $request->narration,
                'total_nt_amt' => $request->total_nt_amt ?? 0,
                'total_sc' => $request->total_sc ?? 0,
                'total_dis_amt' => $request->total_dis_amt ?? 0,
                'total_scm_amt' => $request->total_scm_amt ?? 0,
                'total_half_scm' => $request->total_half_scm ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'total_inv_amt' => $request->total_inv_amt ?? 0,
                'total_qty' => $request->total_qty ?? 0,
                'status' => 'completed',
                'is_deleted' => 0,
            ]);

            // Process items - NO batch quantity changes
            $items = $request->input('items', []);
            $rowOrder = 1;

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $expiryInput = $item['expiry'] ?? null;
                $expiryDate = null;
                if ($expiryInput) {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiryDate = null;
                    }
                }

                // Get CGST/SGST from form (field name is 'cgst' and 'sgst')
                $cgstPercent = $item['cgst_percent'] ?? $item['cgst'] ?? 0;
                $sgstPercent = $item['sgst_percent'] ?? $item['sgst'] ?? 0;
                
                // Calculate amounts
                $qty = floatval($item['qty'] ?? 0);
                $rate = floatval($item['rate'] ?? 0);
                $disPercent = floatval($item['dis_percent'] ?? 0);
                
                $ntAmt = $qty * $rate;
                $disAmt = ($ntAmt * $disPercent) / 100;
                $netAmt = $ntAmt - $disAmt;
                $cgstAmt = ($netAmt * floatval($cgstPercent)) / 100;
                $sgstAmt = ($netAmt * floatval($sgstPercent)) / 100;
                $taxAmt = $cgstAmt + $sgstAmt;
                
                // Map br_ex to br_ex_type
                $brExType = 'BREAKAGE';
                if (isset($item['br_ex'])) {
                    $brExType = ($item['br_ex'] === 'E') ? 'EXPIRY' : 'BREAKAGE';
                } elseif (isset($item['br_ex_type'])) {
                    $brExType = $item['br_ex_type'];
                }

                BreakageSupplierIssuedTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $rate,
                    'dis_percent' => $disPercent,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'br_ex_type' => $brExType,
                    'amount' => $item['amount'] ?? $ntAmt,
                    'nt_amt' => $ntAmt,
                    'dis_amt' => $disAmt,
                    'scm_amt' => $item['scm_amt'] ?? 0,
                    'half_scm' => $item['half_scm'] ?? 0,
                    'tax_amt' => $taxAmt,
                    'net_amt' => $netAmt,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    'p_rate' => $item['purchase_rate'] ?? $item['p_rate'] ?? 0,
                    's_rate' => $item['sale_rate'] ?? $item['s_rate'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cgst_amt' => $cgstAmt,
                    'sgst_amt' => $sgstAmt,
                    'sc_percent' => $item['sc_percent'] ?? 0,
                    'tax_percent' => floatval($cgstPercent) + floatval($sgstPercent),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Breakage/Expiry to Supplier Issued saved successfully!',
                'trn_no' => $trnNo,
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display issued modification form
     */
    public function issuedModification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $brExTypes = BreakageSupplierIssuedTransaction::getBrExTypes();
        $noteTypes = BreakageSupplierIssuedTransaction::getNoteTypes();
        
        return view('admin.breakage-supplier.issued-modification', compact('suppliers', 'brExTypes', 'noteTypes'));
    }

    /**
     * Get transaction details for modification
     */
    public function showIssued($id)
    {
        try {
            $transaction = BreakageSupplierIssuedTransaction::with(['items' => function($query) {
                $query->with('item:id,cgst_percent,sgst_percent,hsn_code,packing,unit,company_short_name');
            }])->findOrFail($id);
            
            // Enrich items with data from item table
            if ($transaction->items) {
                $transaction->items->transform(function($item) {
                    if ($item->item) {
                        $item->cgst_percent = $item->cgst_percent ?? $item->item->cgst_percent ?? 0;
                        $item->sgst_percent = $item->sgst_percent ?? $item->item->sgst_percent ?? 0;
                        $item->hsn_code = $item->hsn_code ?? $item->item->hsn_code ?? '';
                        $item->packing = $item->packing ?? $item->item->packing ?? '';
                        $item->unit = $item->unit ?? $item->item->unit ?? '';
                        $item->company_name = $item->company_name ?? $item->item->company_short_name ?? '';
                    }
                    return $item;
                });
            }
            
            // Always return JSON for this endpoint since it's used by AJAX
            return response()->json($transaction);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get past invoices for Load Invoice modal
     */
    public function getIssuedPastInvoices(Request $request)
    {
        $search = $request->search;
        
        $query = BreakageSupplierIssuedTransaction::active()
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }
        
        $invoices = $query->get(['id', 'trn_no', 'transaction_date', 'supplier_name', 'total_inv_amt']);
        
        return response()->json($invoices);
    }

    /**
     * Update issued transaction
     */
    public function updateIssued(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = BreakageSupplierIssuedTransaction::with('items')->findOrFail($id);
            
            // Delete old items
            $transaction->items()->delete();

            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'note_type' => $request->note_type ?? 'C',
                'tax_flag' => $request->tax_flag ?? 'N',
                'inc_flag' => $request->inc_flag ?? 'N',
                'gst_vno' => $request->gst_vno,
                'dis_count' => $request->dis_count ?? 0,
                'rpl_count' => $request->rpl_count ?? 0,
                'brk_count' => $request->brk_count ?? 0,
                'exp_count' => $request->exp_count ?? 0,
                'narration' => $request->narration,
                'total_nt_amt' => $request->total_nt_amt ?? 0,
                'total_sc' => $request->total_sc ?? 0,
                'total_dis_amt' => $request->total_dis_amt ?? 0,
                'total_scm_amt' => $request->total_scm_amt ?? 0,
                'total_half_scm' => $request->total_half_scm ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'total_inv_amt' => $request->total_inv_amt ?? 0,
                'total_qty' => $request->total_qty ?? 0,
            ]);

            // Process new items - NO batch quantity changes
            $items = $request->input('items', []);
            $rowOrder = 1;

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $expiryInput = $item['expiry'] ?? null;
                $expiryDate = null;
                if ($expiryInput) {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiryDate = null;
                    }
                }

                // Get CGST/SGST from form (field name is 'cgst' and 'sgst')
                $cgstPercent = $item['cgst_percent'] ?? $item['cgst'] ?? 0;
                $sgstPercent = $item['sgst_percent'] ?? $item['sgst'] ?? 0;
                
                // Calculate amounts
                $qty = floatval($item['qty'] ?? 0);
                $rate = floatval($item['rate'] ?? 0);
                $disPercent = floatval($item['dis_percent'] ?? 0);
                
                $ntAmt = $qty * $rate;
                $disAmt = ($ntAmt * $disPercent) / 100;
                $netAmt = $ntAmt - $disAmt;
                $cgstAmt = ($netAmt * floatval($cgstPercent)) / 100;
                $sgstAmt = ($netAmt * floatval($sgstPercent)) / 100;
                $taxAmt = $cgstAmt + $sgstAmt;
                
                // Map br_ex to br_ex_type
                $brExType = 'BREAKAGE';
                if (isset($item['br_ex'])) {
                    $brExType = ($item['br_ex'] === 'E') ? 'EXPIRY' : 'BREAKAGE';
                } elseif (isset($item['br_ex_type'])) {
                    $brExType = $item['br_ex_type'];
                }

                BreakageSupplierIssuedTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $rate,
                    'dis_percent' => $disPercent,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'br_ex_type' => $brExType,
                    'amount' => $item['amount'] ?? $ntAmt,
                    'nt_amt' => $ntAmt,
                    'dis_amt' => $disAmt,
                    'scm_amt' => $item['scm_amt'] ?? 0,
                    'half_scm' => $item['half_scm'] ?? 0,
                    'tax_amt' => $taxAmt,
                    'net_amt' => $netAmt,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    'p_rate' => $item['purchase_rate'] ?? $item['p_rate'] ?? 0,
                    's_rate' => $item['sale_rate'] ?? $item['s_rate'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cgst_amt' => $cgstAmt,
                    'sgst_amt' => $sgstAmt,
                    'sc_percent' => $item['sc_percent'] ?? 0,
                    'tax_percent' => floatval($cgstPercent) + floatval($sgstPercent),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully!',
                'trn_no' => $transaction->trn_no,
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel/Delete issued transaction
     */
    public function destroyIssued($id)
    {
        try {
            DB::beginTransaction();

            $transaction = BreakageSupplierIssuedTransaction::findOrFail($id);

            // Soft delete - NO batch quantity restoration needed
            $transaction->update([
                'status' => 'cancelled',
                'is_deleted' => 1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction cancelled successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all items for item selection modal
     */
    public function getItems()
    {
        try {
            $items = Item::select('id', 'name', 'packing', 'mrp', 's_rate', 'pur_rate', 'cost', 'company_short_name', 'hsn_code', 'unit', 'cgst_percent', 'sgst_percent')
                ->where(function($query) {
                    $query->where('is_deleted', 0)
                          ->orWhere('is_deleted', '0')
                          ->orWhereNull('is_deleted');
                })
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_code' => $item->name ?? '',
                        'item_name' => $item->name,
                        'packing' => $item->packing,
                        'mrp' => $item->mrp ?? 0,
                        'sale_rate' => $item->s_rate ?? 0,
                        'purchase_rate' => $item->pur_rate ?? $item->cost ?? 0,
                        'company_name' => $item->company_short_name ?? '',
                        'hsn_code' => $item->hsn_code ?? '',
                        'unit' => $item->unit ?? '',
                        'cgst' => $item->cgst_percent ?? 0,
                        'sgst' => $item->sgst_percent ?? 0,
                    ];
                });

            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get batches for a specific item
     */
    public function getBatches($itemId)
    {
        try {
            $batches = Batch::where('item_id', $itemId)
                ->where('qty', '>', 0)
                ->where(function($query) {
                    $query->where('is_deleted', 0)
                          ->orWhere('is_deleted', '0')
                          ->orWhereNull('is_deleted');
                })
                ->orderBy('expiry_date', 'asc')
                ->get()
                ->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no ?? '',
                        'expiry_date' => $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '',
                        'quantity' => $batch->qty ?? 0,
                        'mrp' => $batch->mrp ?? 0,
                        'purchase_rate' => $batch->pur_rate ?? 0,
                        'sale_rate' => $batch->s_rate ?? 0,
                    ];
                });

            return response()->json($batches);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get next transaction number
     */
    public function getNextTrnNo()
    {
        return response()->json([
            'next_trn_no' => BreakageSupplierIssuedTransaction::generateTrnNumber()
        ]);
    }

    // Keep existing methods for other transaction types
    public function receivedTransaction()
    {
        // Get next transaction number
        $lastTrn = BreakageSupplierReceivedTransaction::orderBy('id', 'desc')->first();
        $trnNo = $lastTrn ? (int)$lastTrn->trn_no + 1 : 1;
        
        // Get suppliers list
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        
        return view('admin.breakage-supplier.received-transaction', compact('trnNo', 'suppliers'));
    }

    /**
     * Store received transaction
     */
    public function storeReceived(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Get next transaction number
            $lastTrn = BreakageSupplierReceivedTransaction::orderBy('id', 'desc')->first();
            $trnNo = $lastTrn ? (int)$lastTrn->trn_no + 1 : 1;
            
            // Create transaction
            $transaction = BreakageSupplierReceivedTransaction::create([
                'trn_no' => $trnNo,
                'series' => 'BSR',
                'transaction_date' => $request->transaction_date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'party_trn_no' => $request->party_trn_no,
                'party_date' => $request->party_date,
                'claim_transaction_id' => $request->claim_transaction_id,
                'claim_flag' => $request->claim_flag ?? 'N',
                'received_as_debit_note' => $request->received_as_debit_note ? 1 : 0,
                'claim_amount' => $request->claim_amount ?? 0,
                'gross_amt' => $request->gross_amt ?? 0,
                'total_gst' => $request->total_gst ?? 0,
                'net_amt' => $request->net_amt ?? 0,
                'round_off' => $request->round_off ?? 0,
                'final_amount' => $request->final_amount ?? 0,
                'remarks' => $request->remarks,
                'is_deleted' => 0,
            ]);
            
            // Save HSN items if any
            if ($request->has('hsn_items') && is_array($request->hsn_items)) {
                foreach ($request->hsn_items as $item) {
                    BreakageSupplierReceivedTransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'hsn_code' => $item['hsn_code'] ?? '',
                        'amount' => $item['amount'] ?? 0,
                        'gst_percent' => $item['gst_percent'] ?? 0,
                        'igst_percent' => $item['igst_percent'] ?? 0,
                        'gst_amount' => $item['gst_amount'] ?? 0,
                        'qty' => $item['qty'] ?? 0,
                    ]);
                }
            }
            
            // Process adjustments if any (against Purchase Transactions)
            if ($request->has('adjustments') && is_array($request->adjustments)) {
                foreach ($request->adjustments as $adjustment) {
                    // Update purchase balance
                    $purchase = \App\Models\PurchaseTransaction::find($adjustment['purchase_id']);
                    if ($purchase) {
                        $totalAmount = $purchase->inv_amount ?? $purchase->net_amount ?? 0;
                        $currentBalance = $purchase->balance_amount ?? $totalAmount;
                        $newBalance = $currentBalance - $adjustment['amount'];
                        $purchase->balance_amount = max(0, $newBalance);
                        $purchase->save();
                        
                        // Store adjustment record
                        \App\Models\BreakageSupplierReceivedAdjustment::create([
                            'received_transaction_id' => $transaction->id,
                            'purchase_transaction_id' => $purchase->id,
                            'adjusted_amount' => $adjustment['amount'],
                        ]);
                    }
                }
            }
            
            // Update claim balance if exists
            if ($request->claim_transaction_id) {
                $claim = \App\Models\ClaimToSupplierTransaction::find($request->claim_transaction_id);
                if ($claim) {
                    $netAmount = $claim->net_amount ?? 0;
                    $currentBalance = $claim->balance_amount ?? $netAmount;
                    $newBalance = $currentBalance - $request->final_amount;
                    $claim->balance_amount = max(0, $newBalance);
                    $claim->save();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction saved successfully!',
                'trn_no' => $trnNo,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get supplier purchases for adjustment
     */
    public function getSupplierPurchases($supplierId)
    {
        try {
            \Log::info('getSupplierPurchases called with supplierId: ' . $supplierId);
            
            $purchases = \App\Models\PurchaseTransaction::where('supplier_id', $supplierId)
            ->orderBy('bill_date', 'desc')
            ->limit(50)
            ->get()
            ->map(function($p) {
                // Calculate balance - use balance_amount if set, otherwise net_amount or inv_amount
                $totalAmount = $p->inv_amount ?? $p->net_amount ?? 0;
                $balanceAmount = $p->balance_amount ?? $totalAmount;
                
                return [
                    'id' => $p->id,
                    'purchase_no' => $p->bill_no ?? $p->trn_no ?? '',
                    'purchase_date' => $p->bill_date ? $p->bill_date->format('d-M-y') : '',
                    'total_amount' => $totalAmount,
                    'balance_amount' => $balanceAmount,
                ];
            })
            ->filter(function($p) {
                // Only show purchases with balance > 0
                return floatval($p['balance_amount']) > 0;
            })
            ->values();
            
            return response()->json([
                'success' => true,
                'purchases' => $purchases
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function receivedModification()
    {
        return view('admin.breakage-supplier.received-modification');
    }

    /**
     * Get past invoices for received modification Load Invoice modal
     */
    public function getReceivedPastInvoices(Request $request)
    {
        $search = $request->search;
        
        $query = BreakageSupplierReceivedTransaction::query()
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('remarks', 'LIKE', "%{$search}%");
            });
        }
        
        $invoices = $query->get()->map(function($inv) {
            return [
                'id' => $inv->id,
                'trn_no' => $inv->trn_no,
                'transaction_date' => $inv->transaction_date,
                'supplier_name' => $inv->supplier_name,
                'final_amount' => $inv->final_amount,
            ];
        });
        
        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    /**
     * Get received transaction details with items and adjustments
     */
    public function getReceivedDetails($id)
    {
        try {
            $transaction = BreakageSupplierReceivedTransaction::with(['items'])->findOrFail($id);
            
            // Get claim transaction details
            $claimTrnNo = '';
            if ($transaction->claim_transaction_id) {
                $claim = \App\Models\ClaimToSupplierTransaction::find($transaction->claim_transaction_id);
                $claimTrnNo = $claim ? $claim->trn_no : '';
            }
            
            // Get adjustments
            $adjustments = \App\Models\BreakageSupplierReceivedAdjustment::where('received_transaction_id', $id)
                ->get()
                ->map(function($adj) {
                    $purchase = \App\Models\PurchaseTransaction::find($adj->purchase_transaction_id);
                    return [
                        'id' => $adj->id,
                        'purchase_transaction_id' => $adj->purchase_transaction_id,
                        'bill_no' => $purchase ? $purchase->bill_no : '',
                        'bill_date' => $purchase ? $purchase->bill_date : '',
                        'adjusted_amount' => $adj->adjusted_amount,
                    ];
                });
            
            $data = [
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'trn_no' => $transaction->trn_no,
                    'transaction_date' => $transaction->transaction_date,
                    'supplier_id' => $transaction->supplier_id,
                    'supplier_name' => $transaction->supplier_name,
                    'party_trn_no' => $transaction->party_trn_no ?? '',
                    'party_date' => $transaction->party_date ?? '',
                    'claim_transaction_id' => $transaction->claim_transaction_id,
                    'claim_trn_no' => $claimTrnNo,
                    'os_amount' => $transaction->os_amount ?? 0,
                    'claim_flag' => $transaction->claim_flag ?? 'Y',
                    'received_as_debit_note' => $transaction->received_as_debit_note ?? false,
                    'claim_amount' => $transaction->claim_amount ?? 0,
                    'gross_amt' => $transaction->gross_amt ?? 0,
                    'total_gst' => $transaction->total_gst ?? 0,
                    'net_amt' => $transaction->net_amt ?? 0,
                    'round_off' => $transaction->round_off ?? 0,
                    'final_amount' => $transaction->final_amount ?? 0,
                    'remarks' => $transaction->remarks ?? '',
                    'items' => $transaction->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'hsn_code' => $item->hsn_code,
                            'amount' => $item->amount,
                            'gst_percent' => $item->gst_percent,
                            'igst_percent' => $item->igst_percent ?? 0,
                            'gst_amount' => $item->gst_amount,
                            'qty' => $item->qty,
                        ];
                    }),
                    'adjustments' => $adjustments,
                ]
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show received transaction details (for compatibility)
     */
    public function showReceived($id)
    {
        return $this->getReceivedDetails($id);
    }

    /**
     * Update received transaction
     */
    public function updateReceived(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $transaction = BreakageSupplierReceivedTransaction::findOrFail($id);
            
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'party_trn_no' => $request->party_trn_no,
                'party_date' => $request->party_date,
                'claim_flag' => $request->claim_flag ?? 'Y',
                'received_as_debit_note' => $request->received_as_debit_note ?? false,
                'claim_amount' => $request->claim_amount ?? 0,
                'gross_amt' => $request->gross_amt ?? 0,
                'total_gst' => $request->total_gst ?? 0,
                'net_amt' => $request->net_amt ?? 0,
                'round_off' => $request->round_off ?? 0,
                'final_amount' => $request->final_amount ?? 0,
                'remarks' => $request->remarks,
            ]);
            
            // Update HSN items
            if ($request->has('hsn_items')) {
                // Delete existing items
                $transaction->items()->delete();
                
                // Create new items
                foreach ($request->hsn_items as $item) {
                    if (!empty($item['hsn_code'])) {
                        BreakageSupplierReceivedTransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'hsn_code' => $item['hsn_code'],
                            'amount' => $item['amount'] ?? 0,
                            'gst_percent' => $item['gst_percent'] ?? 0,
                            'igst_percent' => $item['igst_percent'] ?? 0,
                            'gst_amount' => $item['gst_amount'] ?? 0,
                            'qty' => $item['qty'] ?? 0,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete received transaction
     */
    public function deleteReceived($id)
    {
        try {
            DB::beginTransaction();
            
            $transaction = BreakageSupplierReceivedTransaction::findOrFail($id);
            
            // Reverse purchase adjustments
            $adjustments = \App\Models\BreakageSupplierReceivedAdjustment::where('received_transaction_id', $id)->get();
            
            foreach ($adjustments as $adj) {
                // Restore balance to purchase transaction
                $purchase = \App\Models\PurchaseTransaction::find($adj->purchase_transaction_id);
                if ($purchase) {
                    $purchase->balance_amount = ($purchase->balance_amount ?? 0) + $adj->adjusted_amount;
                    $purchase->save();
                }
                $adj->delete();
            }
            
            // Restore claim balance if exists
            if ($transaction->claim_transaction_id) {
                $claim = \App\Models\ClaimToSupplierTransaction::find($transaction->claim_transaction_id);
                if ($claim) {
                    $claim->balance_amount = ($claim->balance_amount ?? 0) + ($transaction->final_amount ?? 0);
                    $claim->save();
                }
            }
            
            // Delete HSN items
            $transaction->items()->delete();
            
            // Delete transaction
            $transaction->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display index of unused dump transactions
     */
    public function unusedDumpIndex(Request $request)
    {
        $query = BreakageSupplierUnusedDumpTransaction::query()->with('items');

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'narration':
                    $query->where('narration', 'LIKE', "%{$searchTerm}%");
                    break;
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->withCount('items')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.breakage-supplier.unused-dump-index', compact('transactions'));
    }

    public function unusedDumpTransaction()
    {
        // Get next transaction number
        $lastTrn = BreakageSupplierUnusedDumpTransaction::orderBy('id', 'desc')->first();
        $trnNo = $lastTrn ? (int)$lastTrn->trn_no + 1 : 1;
        
        return view('admin.breakage-supplier.unused-dump-transaction', compact('trnNo'));
    }

    public function unusedDumpModification()
    {
        return view('admin.breakage-supplier.unused-dump-modification');
    }

    /**
     * Get past dump transactions for Load Dump modal
     */
    public function getDumpPastInvoices(Request $request)
    {
        $search = $request->search;
        
        $query = BreakageSupplierUnusedDumpTransaction::query()
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($search) {
            $query->where('trn_no', 'LIKE', "%{$search}%");
        }
        
        $dumps = $query->get()->map(function($d) {
            return [
                'id' => $d->id,
                'trn_no' => $d->trn_no,
                'transaction_date' => $d->transaction_date,
                'total_inv_amt' => $d->total_inv_amt,
            ];
        });
        
        return response()->json($dumps);
    }

    /**
     * Show unused dump transaction details
     */
    public function showUnusedDump($id)
    {
        $transaction = BreakageSupplierUnusedDumpTransaction::with('items')->findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Store new unused dump transaction
     */
    public function storeUnusedDump(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Generate transaction number
            $lastTransaction = BreakageSupplierUnusedDumpTransaction::orderBy('id', 'desc')->first();
            $lastNumber = $lastTransaction ? intval(substr($lastTransaction->trn_no, -6)) : 0;
            $trnNo = 'UDUMP-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            
            // Create transaction
            $transaction = BreakageSupplierUnusedDumpTransaction::create([
                'trn_no' => $trnNo,
                'transaction_date' => $request->transaction_date,
                'narration' => $request->narration,
                'total_nt_amt' => $request->total_nt_amt ?? 0,
                'total_sc' => $request->total_sc ?? 0,
                'total_dis_amt' => $request->total_dis_amt ?? 0,
                'total_scm_amt' => $request->total_scm_amt ?? 0,
                'total_half_scm' => $request->total_half_scm ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'total_inv_amt' => $request->total_inv_amt ?? 0,
                'created_by' => auth()->id(),
            ]);
            
            // Add items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $transaction->items()->create([
                        'item_id' => $item['item_id'],
                        'batch_id' => $item['batch_id'] ?? null,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $item['item_name'] ?? null,
                        'batch_no' => $item['batch_no'] ?? null,
                        'expiry_date' => $item['expiry'] ?? null,
                        'qty' => $item['qty'] ?? 0,
                        'free_qty' => $item['free_qty'] ?? 0,
                        'rate' => $item['rate'] ?? 0,
                        'dis_percent' => $item['dis_percent'] ?? 0,
                        'scm_percent' => $item['scm_percent'] ?? 0,
                        'br_ex' => $item['br_ex'] ?? 'B',
                        'amount' => $item['amount'] ?? 0,
                        'mrp' => $item['mrp'] ?? null,
                        'purchase_rate' => $item['purchase_rate'] ?? null,
                        'sale_rate' => $item['sale_rate'] ?? null,
                        'cgst' => $item['cgst'] ?? 0,
                        'sgst' => $item['sgst'] ?? 0,
                        'company_name' => $item['company_name'] ?? null,
                        'packing' => $item['packing'] ?? null,
                        'unit' => $item['unit'] ?? null,
                        'hsn_code' => $item['hsn_code'] ?? null,
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction saved successfully!',
                'trn_no' => $trnNo,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update unused dump transaction
     */
    public function updateUnusedDump(Request $request, $id)
    {
        try {
            $transaction = BreakageSupplierUnusedDumpTransaction::findOrFail($id);
            
            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'narration' => $request->narration,
                'total_nt_amt' => $request->total_nt_amt ?? 0,
                'total_sc' => $request->total_sc ?? 0,
                'total_dis_amt' => $request->total_dis_amt ?? 0,
                'total_scm_amt' => $request->total_scm_amt ?? 0,
                'total_half_scm' => $request->total_half_scm ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'total_inv_amt' => $request->total_inv_amt ?? 0,
                'updated_by' => auth()->id(),
            ]);
            
            // Delete existing items
            $transaction->items()->delete();
            
            // Add new items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $transaction->items()->create([
                        'item_id' => $item['item_id'],
                        'batch_id' => $item['batch_id'] ?? null,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $item['item_name'] ?? null,
                        'batch_no' => $item['batch_no'] ?? null,
                        'expiry_date' => $item['expiry'] ?? null,
                        'qty' => $item['qty'] ?? 0,
                        'free_qty' => $item['free_qty'] ?? 0,
                        'rate' => $item['rate'] ?? 0,
                        'dis_percent' => $item['dis_percent'] ?? 0,
                        'scm_percent' => $item['scm_percent'] ?? 0,
                        'br_ex' => $item['br_ex'] ?? 'B',
                        'amount' => $item['amount'] ?? 0,
                        'mrp' => $item['mrp'] ?? null,
                        'purchase_rate' => $item['purchase_rate'] ?? null,
                        'sale_rate' => $item['sale_rate'] ?? null,
                        'cgst' => $item['cgst'] ?? 0,
                        'sgst' => $item['sgst'] ?? 0,
                        'company_name' => $item['company_name'] ?? null,
                        'packing' => $item['packing'] ?? null,
                        'unit' => $item['unit'] ?? null,
                        'hsn_code' => $item['hsn_code'] ?? null,
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete unused dump transaction
     */
    public function destroyUnusedDump($id)
    {
        try {
            $transaction = BreakageSupplierUnusedDumpTransaction::findOrFail($id);
            $transaction->items()->delete();
            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
