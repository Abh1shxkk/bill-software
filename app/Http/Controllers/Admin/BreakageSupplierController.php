<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakageSupplierIssuedTransaction;
use App\Models\BreakageSupplierIssuedTransactionItem;
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
            $items = Item::select('id', 'name', 'packing', 'mrp', 's_rate', 'pur_rate', 'cost', 'company_short_name', 'hsn_code', 'unit', 'cgst', 'sgst')
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
                        'name' => $item->name,
                        'packing' => $item->packing,
                        'mrp' => $item->mrp ?? 0,
                        's_rate' => $item->s_rate ?? 0,
                        'p_rate' => $item->pur_rate ?? $item->cost ?? 0,
                        'company_name' => $item->company_short_name ?? '',
                        'hsn_code' => $item->hsn_code ?? '',
                        'unit' => $item->unit ?? '',
                        'cgst' => $item->cgst ?? 0,
                        'sgst' => $item->sgst ?? 0,
                    ];
                });

            return response()->json($items);
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

    public function unusedDumpTransaction()
    {
        return view('admin.breakage-supplier.unused-dump-transaction');
    }

    public function unusedDumpModification()
    {
        return view('admin.breakage-supplier.unused-dump-modification');
    }
}
