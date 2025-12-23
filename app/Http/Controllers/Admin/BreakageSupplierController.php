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

        $transactions = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

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

                BreakageSupplierIssuedTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $item['rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'br_ex_type' => $item['br_ex_type'] ?? 'BREAKAGE',
                    'amount' => $item['amount'] ?? 0,
                    'nt_amt' => $item['nt_amt'] ?? 0,
                    'dis_amt' => $item['dis_amt'] ?? 0,
                    'scm_amt' => $item['scm_amt'] ?? 0,
                    'half_scm' => $item['half_scm'] ?? 0,
                    'tax_amt' => $item['tax_amt'] ?? 0,
                    'net_amt' => $item['net_amt'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cgst_amt' => $item['cgst_amt'] ?? 0,
                    'sgst_amt' => $item['sgst_amt'] ?? 0,
                    'sc_percent' => $item['sc_percent'] ?? 0,
                    'tax_percent' => $item['tax_percent'] ?? 0,
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
        $transaction = BreakageSupplierIssuedTransaction::with('items')->findOrFail($id);
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($transaction);
        }
        
        return view('admin.breakage-supplier.issued-show', compact('transaction'));
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

                BreakageSupplierIssuedTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $item['rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'scm_percent' => $item['scm_percent'] ?? 0,
                    'br_ex_type' => $item['br_ex_type'] ?? 'BREAKAGE',
                    'amount' => $item['amount'] ?? 0,
                    'nt_amt' => $item['nt_amt'] ?? 0,
                    'dis_amt' => $item['dis_amt'] ?? 0,
                    'scm_amt' => $item['scm_amt'] ?? 0,
                    'half_scm' => $item['half_scm'] ?? 0,
                    'tax_amt' => $item['tax_amt'] ?? 0,
                    'net_amt' => $item['net_amt'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cgst_amt' => $item['cgst_amt'] ?? 0,
                    'sgst_amt' => $item['sgst_amt'] ?? 0,
                    'sc_percent' => $item['sc_percent'] ?? 0,
                    'tax_percent' => $item['tax_percent'] ?? 0,
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
        return view('admin.breakage-supplier.received-transaction');
    }

    public function receivedModification()
    {
        return view('admin.breakage-supplier.received-modification');
    }

    /**
     * Get past invoices for received modification Load Invoice modal
     * Note: This is a placeholder - update when received transaction model is created
     */
    public function getReceivedPastInvoices(Request $request)
    {
        $search = $request->search;
        
        // Using issued transactions as placeholder since received model doesn't exist yet
        // Replace with actual received transaction model when available
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
        
        $invoices = $query->get()->map(function($inv) {
            return [
                'id' => $inv->id,
                'transaction_date' => $inv->transaction_date,
                'supplier_name' => $inv->supplier_name,
                'amount' => $inv->total_inv_amt,
            ];
        });
        
        return response()->json($invoices);
    }

    /**
     * Show received transaction details
     */
    public function showReceived($id)
    {
        // Using issued transaction as placeholder
        $transaction = BreakageSupplierIssuedTransaction::with('items')->findOrFail($id);
        
        $data = [
            'id' => $transaction->id,
            'transaction_date' => $transaction->transaction_date,
            'supplier_name' => $transaction->supplier_name,
            'party_trn_no' => $transaction->gst_vno ?? '',
            'party_date' => $transaction->transaction_date,
            'claim_flag' => 'N',
            'claim_amount' => 0,
            'received_debit_note' => false,
            'os_amount' => 0,
            'remarks' => $transaction->narration ?? '',
            'round_off' => 0,
            'gross_amt' => $transaction->total_nt_amt ?? 0,
            'total_gst' => $transaction->total_tax ?? 0,
            'amount' => $transaction->total_inv_amt ?? 0,
            'items' => $transaction->items->map(function($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry' => $item->expiry,
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty ?? 0,
                    'rate' => $item->rate,
                    'dis_percent' => $item->dis_percent ?? 0,
                    'tax_percent' => $item->tax_percent ?? 0,
                    'amount' => $item->amount ?? 0,
                    'packing' => $item->packing ?? '',
                    'unit' => $item->unit ?? '',
                    'company_name' => $item->company_name ?? '',
                    'mrp' => $item->mrp ?? 0,
                    'p_rate' => $item->p_rate ?? 0,
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'tax_amount' => $item->tax_amt ?? 0,
                    'dis_amount' => $item->dis_amt ?? 0,
                ];
            }),
        ];
        
        return response()->json($data);
    }

    /**
     * Update received transaction
     */
    public function updateReceived(Request $request, $id)
    {
        // Placeholder - implement when received transaction model is created
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully!'
        ]);
    }

    public function unusedDumpTransaction()
    {
        return view('admin.breakage-supplier.unused-dump-transaction');
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
}
