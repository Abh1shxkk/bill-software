<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SampleIssuedTransaction;
use App\Models\SampleIssuedTransactionItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\StockLedger;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Models\AreaManager;
use App\Models\RegionalManager;
use App\Models\MarketingManager;
use App\Models\GeneralManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampleIssuedController extends Controller
{
    /**
     * Display a listing of sample issued transactions
     */
    public function index(Request $request)
    {
        $query = SampleIssuedTransaction::query()->active();

        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'party_name':
                    $query->where('party_name', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'trn_no':
                    $query->where('trn_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'party_type':
                    $query->where('party_type', 'LIKE', "%{$searchTerm}%");
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

        return view('admin.sample-issued.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction
     */
    public function create()
    {
        $trnNo = SampleIssuedTransaction::generateTrnNumber();
        $partyTypes = SampleIssuedTransaction::getPartyTypes();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get(['id', 'name']);
        
        return view('admin.sample-issued.transaction', compact('trnNo', 'partyTypes', 'customers'));
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $trnNo = SampleIssuedTransaction::generateTrnNumber();

            $transaction = SampleIssuedTransaction::create([
                'trn_no' => $trnNo,
                'series' => 'SI',
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'party_type' => $request->party_type,
                'party_id' => $request->party_id,
                'party_name' => $request->party_name,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases ?? 0,
                'road_permit_no' => $request->road_permit_no,
                'truck_no' => $request->truck_no,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'on_field' => $request->on_field,
                'rate' => $request->rate ?? 0,
                'tag' => $request->tag,
                'total_qty' => $request->total_qty ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
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
                $transactionItem = SampleIssuedTransactionItem::create([
                    'sample_issued_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $item['rate'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // NOTE: Batch quantity is automatically updated by StockLedgerObserver
                // when the stock ledger entry is created below - DO NOT manually deduct here

                // Create Stock Ledger entry - OUT transaction (Observer will update batch qty)
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'SAMPLE_ISSUED';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $item['rate'] ?? 0;
                $stockLedger->bill_number = $trnNo;
                $stockLedger->bill_date = $transaction->transaction_date;
                $stockLedger->remarks = 'Sample Issued - ' . $trnNo;
                $stockLedger->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sample issued successfully!',
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
        $transaction = SampleIssuedTransaction::with('items')->findOrFail($id);
        $partyTypes = SampleIssuedTransaction::getPartyTypes();
        
        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json($transaction);
        }
        
        return view('admin.sample-issued.show', compact('transaction', 'partyTypes'));
    }

    /**
     * Show the form for editing the specified transaction
     */
    public function edit($id)
    {
        $transaction = SampleIssuedTransaction::with('items')->findOrFail($id);
        $partyTypes = SampleIssuedTransaction::getPartyTypes();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get(['id', 'name']);
        
        return view('admin.sample-issued.edit', compact('transaction', 'partyTypes', 'customers'));
    }

    /**
     * Show blank modification page with Load Invoice functionality
     */
    public function modification()
    {
        $partyTypes = SampleIssuedTransaction::getPartyTypes();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get(['id', 'name']);
        
        return view('admin.sample-issued.modification', compact('partyTypes', 'customers'));
    }

    /**
     * Get past invoices for Load Invoice modal
     */
    public function getPastInvoices(Request $request)
    {
        $search = $request->search;
        
        $query = SampleIssuedTransaction::active()
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'LIKE', "%{$search}%")
                  ->orWhere('party_name', 'LIKE', "%{$search}%");
            });
        }
        
        $invoices = $query->get(['id', 'trn_no', 'transaction_date', 'party_type', 'party_name', 'net_amount']);
        
        return response()->json($invoices);
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = SampleIssuedTransaction::with('items')->findOrFail($id);
            
            // Delete old stock ledger entries one by one (so StockLedgerObserver triggers and restores batch quantities)
            $oldStockLedgers = StockLedger::where('reference_type', 'SAMPLE_ISSUED')
                ->where('reference_id', $transaction->id)
                ->get();
            foreach ($oldStockLedgers as $oldLedger) {
                $oldLedger->delete(); // This triggers the observer which restores batch qty
            }

            // Delete old items
            $transaction->items()->delete();

            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'day_name' => $request->day_name,
                'party_type' => $request->party_type,
                'party_id' => $request->party_id,
                'party_name' => $request->party_name,
                'gr_no' => $request->gr_no,
                'gr_date' => $request->gr_date,
                'cases' => $request->cases ?? 0,
                'road_permit_no' => $request->road_permit_no,
                'truck_no' => $request->truck_no,
                'transport' => $request->transport,
                'remarks' => $request->remarks,
                'on_field' => $request->on_field,
                'rate' => $request->rate ?? 0,
                'tag' => $request->tag,
                'total_qty' => $request->total_qty ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
            ]);

            // Process new items
            $items = $request->input('items', []);
            $rowOrder = 1;

            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                $qty = (float)$item['qty'];
                // Handle batch_id - ensure it's not empty string
                $batchIdForItem = !empty($item['batch_id']) ? $item['batch_id'] : null;

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
                $transactionItem = SampleIssuedTransactionItem::create([
                    'sample_issued_transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $batchIdForItem,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry' => $expiryInput,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'rate' => $item['rate'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // NOTE: Batch quantity is automatically updated by StockLedgerObserver
                // when the stock ledger entry is created below - DO NOT manually deduct here

                // Create Stock Ledger entry (Observer will update batch qty)
                $stockLedger = new StockLedger();
                $stockLedger->item_id = $transactionItem->item_id;
                $stockLedger->batch_id = $batchIdForItem;
                $stockLedger->transaction_type = 'OUT';
                $stockLedger->reference_type = 'SAMPLE_ISSUED';
                $stockLedger->reference_id = $transaction->id;
                $stockLedger->transaction_date = $transaction->transaction_date;
                $stockLedger->quantity = $qty;
                $stockLedger->rate = $item['rate'] ?? 0;
                $stockLedger->bill_number = $transaction->trn_no;
                $stockLedger->bill_date = $transaction->transaction_date;
                $stockLedger->remarks = 'Sample Issued (Modified) - ' . $transaction->trn_no;
                $stockLedger->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sample issue updated successfully!',
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

            $transaction = SampleIssuedTransaction::with('items')->findOrFail($id);

            // Delete stock ledger entries one by one (so StockLedgerObserver triggers and restores batch quantities)
            $oldStockLedgers = StockLedger::where('reference_type', 'SAMPLE_ISSUED')
                ->where('reference_id', $transaction->id)
                ->get();
            foreach ($oldStockLedgers as $oldLedger) {
                $oldLedger->delete(); // This triggers the observer which restores batch qty
            }

            // Soft delete
            $transaction->update([
                'status' => 'cancelled',
                'is_deleted' => 1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sample issue cancelled successfully!'
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
     * Load transaction by TRN number (for modification page)
     */
    public function loadByTrnNo(Request $request)
    {
        $trnNo = $request->trn_no;
        
        $transaction = SampleIssuedTransaction::with('items')
            ->where('trn_no', $trnNo)
            ->active()
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    /**
     * Get all items for item selection modal
     */
    public function getItems()
    {
        $items = Item::select('id', 'name', 'packing', 'mrp', 's_rate', 'company_short_name', 'hsn_code')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'packing' => $item->packing,
                    'mrp' => $item->mrp,
                    's_rate' => $item->s_rate,
                    'company_name' => $item->company_short_name,
                    'hsn_code' => $item->hsn_code,
                ];
            });

        return response()->json($items);
    }

    /**
     * Get party list based on party type
     */
    public function getPartyList(Request $request)
    {
        $partyType = $request->party_type;
        $parties = [];

        switch ($partyType) {
            case 'CUSTOMER':
                $parties = Customer::where('is_deleted', '!=', 1)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                break;
            case 'SALES_MAN':
                $parties = SalesMan::where('is_deleted', '!=', 1)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                break;
            case 'AREA_MGR':
                $parties = AreaManager::orderBy('name')
                    ->get(['id', 'name']);
                break;
            case 'REG_MGR':
                $parties = RegionalManager::orderBy('name')
                    ->get(['id', 'name']);
                break;
            case 'MKT_MGR':
                $parties = MarketingManager::orderBy('name')
                    ->get(['id', 'name']);
                break;
            case 'GEN_MGR':
                $parties = GeneralManager::orderBy('name')
                    ->get(['id', 'name']);
                break;
            default:
                $parties = [];
        }

        return response()->json($parties);
    }
}
