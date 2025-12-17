<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GodownBreakageExpiryTransaction;
use App\Models\GodownBreakageExpiryTransactionItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GodownBreakageExpiryController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $query = GodownBreakageExpiryTransaction::query()->active();

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

        $transactions = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.godown-breakage-expiry.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction
     */
    public function create()
    {
        $trnNo = GodownBreakageExpiryTransaction::generateTrnNumber();
        $brExTypes = GodownBreakageExpiryTransaction::getBrExTypes();
        
        return view('admin.godown-breakage-expiry.transaction', compact('trnNo', 'brExTypes'));
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $trnNo = GodownBreakageExpiryTransaction::generateTrnNumber();

            $transaction = GodownBreakageExpiryTransaction::create([
                'trn_no' => $trnNo,
                'series' => 'GBE',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'narration' => $request->narration,
                'total_qty' => $request->total_qty ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'status' => 'completed',
                'is_deleted' => 0,
            ]);

            // Process items
            $items = $request->input('items', []);
            $rowOrder = 1;

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $qty = (float)$item['qty'];
                $batchIdForItem = $item['batch_id'] ?? null;

                // Parse expiry date
                $expiryInput = $item['expiry'] ?? null;
                $expiryDate = null;
                if ($expiryInput) {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiryDate = null;
                    }
                }

                // Create transaction item
                $transactionItem = GodownBreakageExpiryTransactionItem::create([
                    'godown_breakage_expiry_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'br_ex_type' => $item['br_ex_type'] ?? 'BREAKAGE',
                    'qty' => $qty,
                    'cost' => $item['cost'] ?? $item['rate'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'location' => $item['location'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? $item['cost'] ?? 0,
                    'row_order' => $rowOrder++,
                ]);

                // Create Stock Ledger entry - OUT transaction (StockLedgerObserver will decrease batch qty)
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'GODOWN_BREAKAGE_EXPIRY';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $item['cost'] ?? 0;
                $stockLedger->bill_number = $trnNo;
                $stockLedger->bill_date = $transaction->transaction_date;
                $stockLedger->remarks = 'Godown ' . ($item['br_ex_type'] ?? 'Breakage/Expiry') . ' - ' . $trnNo;
                $stockLedger->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Godown Breakage/Expiry saved successfully!',
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
     * Display the specified transaction
     */
    public function show($id)
    {
        $transaction = GodownBreakageExpiryTransaction::with('items')->findOrFail($id);
        $brExTypes = GodownBreakageExpiryTransaction::getBrExTypes();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($transaction);
        }
        
        return view('admin.godown-breakage-expiry.show', compact('transaction', 'brExTypes'));
    }

    /**
     * Show blank modification page
     */
    public function modification()
    {
        $brExTypes = GodownBreakageExpiryTransaction::getBrExTypes();
        
        return view('admin.godown-breakage-expiry.modification', compact('brExTypes'));
    }

    /**
     * Get past invoices for Load Invoice modal
     */
    public function getPastInvoices(Request $request)
    {
        $search = $request->search;
        
        $query = GodownBreakageExpiryTransaction::active()
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'LIKE', "%{$search}%")
                  ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }
        
        $invoices = $query->get(['id', 'trn_no', 'transaction_date', 'narration', 'total_amount']);
        
        return response()->json($invoices);
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = GodownBreakageExpiryTransaction::with('items')->findOrFail($id);
            
            // Delete old stock ledger entries one by one (so StockLedgerObserver triggers and restores batch quantities)
            $oldStockLedgers = StockLedger::where('reference_type', 'GODOWN_BREAKAGE_EXPIRY')
                ->where('reference_id', $transaction->id)
                ->get();
            foreach ($oldStockLedgers as $oldLedger) {
                $oldLedger->delete(); // This triggers the observer
            }

            // Delete old items
            $transaction->items()->delete();

            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'narration' => $request->narration,
                'total_qty' => $request->total_qty ?? 0,
                'total_amount' => $request->total_amount ?? 0,
            ]);

            // Process new items
            $items = $request->input('items', []);
            $rowOrder = 1;

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $qty = (float)$item['qty'];
                $batchIdForItem = !empty($item['batch_id']) ? $item['batch_id'] : null;

                $expiryInput = $item['expiry'] ?? null;
                $expiryDate = null;
                if ($expiryInput) {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiryDate = null;
                    }
                }

                $transactionItem = GodownBreakageExpiryTransactionItem::create([
                    'godown_breakage_expiry_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'br_ex_type' => $item['br_ex_type'] ?? 'BREAKAGE',
                    'qty' => $qty,
                    'cost' => $item['cost'] ?? $item['rate'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'location' => $item['location'] ?? '',
                    'mrp' => $item['mrp'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'p_rate' => $item['p_rate'] ?? $item['cost'] ?? 0,
                    'row_order' => $rowOrder++,
                ]);

                // Create Stock Ledger entry (StockLedgerObserver will decrease batch qty)
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'GODOWN_BREAKAGE_EXPIRY';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $item['cost'] ?? 0;
                $stockLedger->bill_number = $transaction->trn_no;
                $stockLedger->bill_date = $transaction->transaction_date;
                $stockLedger->remarks = 'Godown ' . ($item['br_ex_type'] ?? 'Breakage/Expiry') . ' (Modified) - ' . $transaction->trn_no;
                $stockLedger->save();
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
     * Cancel/Delete the specified transaction
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $transaction = GodownBreakageExpiryTransaction::with('items')->findOrFail($id);

            // Delete stock ledger entries one by one (so StockLedgerObserver triggers and restores batch quantities)
            $oldStockLedgers = StockLedger::where('reference_type', 'GODOWN_BREAKAGE_EXPIRY')
                ->where('reference_id', $transaction->id)
                ->get();
            foreach ($oldStockLedgers as $oldLedger) {
                $oldLedger->delete(); // This triggers the observer
            }

            // Soft delete
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
            $items = Item::select('id', 'name', 'packing', 'mrp', 's_rate', 'pur_rate', 'cost', 'company_short_name', 'hsn_code', 'unit')
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
                    ];
                });

            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
