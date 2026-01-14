<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SaleTransaction;
use App\Models\SaleReturnTransaction;
use App\Models\BreakageExpiryTransaction;
use App\Models\CreditNote;
use App\Models\DebitNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    /**
     * View customer ledger - Shows all transactions (Sales, Returns, Breakage/Expiry)
     */
    public function index(Customer $customer, Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $search = $request->get('search');

        // Calculate opening balance (before fromDate)
        $openingBalance = $this->calculateOpeningBalance($customer->id, $fromDate);

        // Fetch all transactions and combine them
        $ledgers = $this->getCombinedLedgerEntries($customer->id, $fromDate, $toDate, $search);

        // Calculate totals
        $totalDebit = $ledgers->where('debit_credit', 'debit')->sum('amount');
        $totalCredit = $ledgers->where('debit_credit', 'credit')->sum('amount');

        // Paginate results
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $ledgers = new \Illuminate\Pagination\LengthAwarePaginator(
            $ledgers->forPage($currentPage, $perPage),
            $ledgers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.customers.ledger', compact(
                'customer',
                'ledgers',
                'fromDate',
                'toDate',
                'openingBalance',
                'totalDebit',
                'totalCredit'
            ));
        }

        return view('admin.customers.ledger', compact(
            'customer',
            'ledgers',
            'fromDate',
            'toDate',
            'openingBalance',
            'totalDebit',
            'totalCredit'
        ));
    }

    /**
     * Calculate opening balance before the from date
     */
    private function calculateOpeningBalance($customerId, $fromDate)
    {
        $openingBalance = 0;

        // Sales (Debit - increases balance)
        $salesTotal = SaleTransaction::where('customer_id', $customerId)
            ->where('sale_date', '<', $fromDate)
            ->sum('net_amount');

        // Cash Sales (Credit - decreases balance, payment received)
        $cashSalesTotal = SaleTransaction::where('customer_id', $customerId)
            ->where('sale_date', '<', $fromDate)
            ->where('cash_flag', 'Y')
            ->sum('net_amount');

        // Sale Returns (Credit - decreases balance)
        $returnsTotal = SaleReturnTransaction::where('customer_id', $customerId)
            ->where('return_date', '<', $fromDate)
            ->sum('net_amount');

        // Cash Sale Returns (Debit - increases balance, payment made to customer)
        $cashReturnsTotal = SaleReturnTransaction::where('customer_id', $customerId)
            ->where('return_date', '<', $fromDate)
            ->where('cash_flag', 'Y')
            ->sum('net_amount');

        // Breakage/Expiry (Credit - decreases balance)
        $breakageTotal = BreakageExpiryTransaction::where('customer_id', $customerId)
            ->where('transaction_date', '<', $fromDate)
            ->sum('net_amount');

        // Credit Notes (Credit - decreases balance)
        $creditNoteTotal = CreditNote::where('credit_party_type', 'C')
            ->where('credit_party_id', $customerId)
            ->where('credit_note_date', '<', $fromDate)
            ->sum('cn_amount');

        // Debit Notes (Debit - increases balance)
        $debitNoteTotal = DebitNote::where('debit_party_type', 'C')
            ->where('debit_party_id', $customerId)
            ->where('debit_note_date', '<', $fromDate)
            ->sum('dn_amount');

        // Opening Balance Calculation:
        // Sales (Dr) - Cash Sales (Cr) - Returns (Cr) + Cash Returns (Dr) - Breakage (Cr) - Credit Notes (Cr) + Debit Notes (Dr)
        // = Sales + Cash Returns + Debit Notes - (Cash Sales + Returns + Breakage + Credit Notes)
        $openingBalance = ($salesTotal + $cashReturnsTotal + $debitNoteTotal) - ($cashSalesTotal + $returnsTotal + $breakageTotal + $creditNoteTotal);

        return $openingBalance;
    }

    /**
     * Get combined ledger entries from all transaction types
     */
    private function getCombinedLedgerEntries($customerId, $fromDate, $toDate, $search = null)
    {
        $entries = collect();

        // 1. Sales Transactions (Debit) + Cash Book entries if cash_flag = 'Y'
        $salesData = SaleTransaction::where('customer_id', $customerId)
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        foreach ($salesData as $sale) {
            // Main sale entry (always Debit)
            $entries->push([
                'transaction_date' => $sale->sale_date,
                'trans_no' => $sale->series . ' / ' . $sale->invoice_no,
                'account_name' => 'SALE',
                'debit_credit' => 'debit',
                'amount' => $sale->net_amount,
                'sale_transaction_id' => $sale->id,
                'type' => 'sale',
                'sort_order' => $sale->sale_date->timestamp . '_1_' . $sale->id,
            ]);

            // If cash sale, add cash book entry (Credit - payment received)
            if ($sale->cash_flag === 'Y') {
                $entries->push([
                    'transaction_date' => $sale->sale_date,
                    'trans_no' => $sale->series . ' / ' . $sale->invoice_no,
                    'account_name' => 'CASH BOOK',
                    'debit_credit' => 'credit',
                    'amount' => $sale->net_amount,
                    'sale_transaction_id' => $sale->id,
                    'type' => 'cash_book_sale',
                    'sort_order' => $sale->sale_date->timestamp . '_2_' . $sale->id,
                ]);
            }
        }

        // 2. Sale Return Transactions (Credit) + Cash Book entries if cash_flag = 'Y'
        $saleReturnsData = SaleReturnTransaction::where('customer_id', $customerId)
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sr_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        foreach ($saleReturnsData as $return) {
            // Main sale return entry (always Credit)
            $entries->push([
                'transaction_date' => $return->return_date,
                'trans_no' => $return->series . ' / ' . $return->sr_no,
                'account_name' => 'SALES RETURN',
                'debit_credit' => 'credit',
                'amount' => $return->net_amount,
                'sale_return_transaction_id' => $return->id,
                'type' => 'sale_return',
                'sort_order' => $return->return_date->timestamp . '_1_' . $return->id,
            ]);

            // If cash return, add cash book entry (Debit - payment made to customer)
            if ($return->cash_flag === 'Y') {
                $entries->push([
                    'transaction_date' => $return->return_date,
                    'trans_no' => $return->series . ' / ' . $return->sr_no,
                    'account_name' => 'CASH BOOK',
                    'debit_credit' => 'debit',
                    'amount' => $return->net_amount,
                    'sale_return_transaction_id' => $return->id,
                    'type' => 'cash_book_return',
                    'sort_order' => $return->return_date->timestamp . '_2_' . $return->id,
                ]);
            }
        }

        // 3. Breakage/Expiry Transactions (Credit only - no cash book entry)
        $breakageExpiry = BreakageExpiryTransaction::where('customer_id', $customerId)
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sr_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->get()
            ->map(function($be) {
                return [
                    'transaction_date' => $be->transaction_date,
                    'trans_no' => $be->series . ' / ' . $be->sr_no,
                    'account_name' => 'BREAKAGE & EXPIRY',
                    'debit_credit' => 'credit',
                    'amount' => $be->net_amount,
                    'breakage_expiry_transaction_id' => $be->id,
                    'type' => 'breakage_expiry',
                    'sort_order' => $be->transaction_date->timestamp . '_1_' . $be->id,
                ];
            });

        // Merge breakage/expiry entries
        $entries = $entries->concat($breakageExpiry);

        // 4. Credit Notes (Credit - decreases customer balance)
        $creditNotes = CreditNote::where('credit_party_type', 'C')
            ->where('credit_party_id', $customerId)
            ->whereBetween('credit_note_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where('credit_note_no', 'LIKE', "%{$search}%");
            })
            ->get()
            ->map(function($cn) {
                return [
                    'transaction_date' => $cn->credit_note_date,
                    'trans_no' => 'CN / ' . $cn->credit_note_no,
                    'account_name' => 'CREDIT NOTE',
                    'debit_credit' => 'credit',
                    'amount' => $cn->cn_amount,
                    'credit_note_id' => $cn->id,
                    'type' => 'credit_note',
                    'sort_order' => $cn->credit_note_date->timestamp . '_1_' . $cn->id,
                ];
            });

        // Merge credit note entries
        $entries = $entries->concat($creditNotes);

        // 5. Debit Notes (Debit - increases customer balance)
        $debitNotes = DebitNote::where('debit_party_type', 'C')
            ->where('debit_party_id', $customerId)
            ->whereBetween('debit_note_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where('debit_note_no', 'LIKE', "%{$search}%");
            })
            ->get()
            ->map(function($dn) {
                return [
                    'transaction_date' => $dn->debit_note_date,
                    'trans_no' => 'DN / ' . $dn->debit_note_no,
                    'account_name' => 'DEBIT NOTE',
                    'debit_credit' => 'debit',
                    'amount' => $dn->dn_amount,
                    'debit_note_id' => $dn->id,
                    'type' => 'debit_note',
                    'sort_order' => $dn->debit_note_date->timestamp . '_1_' . $dn->id,
                ];
            });

        // Merge debit note entries
        $entries = $entries->concat($debitNotes);

        // Sort by date and calculate running balance
        $entries = $entries->sortBy('sort_order')->values();

        // Calculate running balance
        $runningBalance = $this->calculateOpeningBalance($customerId, $fromDate);
        
        $entries = $entries->map(function($entry) use (&$runningBalance) {
            if ($entry['debit_credit'] === 'debit') {
                $runningBalance += $entry['amount'];
            } else {
                $runningBalance -= $entry['amount'];
            }
            $entry['running_balance'] = $runningBalance;
            return (object) $entry;
        });

        return $entries;
    }

    /**
     * Show sale transaction details (AJAX)
     */
    public function showSale($customerId, $id)
    {
        $sale = SaleTransaction::with(['items.item', 'customer', 'salesman'])->findOrFail($id);

        return response()->json([
            'customer_name' => $sale->customer->name ?? 'N/A',
            'invoice_no' => $sale->invoice_no,
            'sale_date' => $sale->sale_date->format('d-m-Y'),
            'net_amount' => $sale->net_amount,
            'items' => $sale->items->map(function($item) {
                return [
                    'item_name' => $item->item->name ?? 'N/A',
                    'pack' => $item->packing ?? '',
                    'batch' => $item->batch_no ?? '',
                    'expiry' => $item->expiry_date ?? '',
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty ?? 0,
                    'rate' => $item->sale_rate,
                    'discount' => $item->discount_percent ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'amount' => $item->amount,
                ];
            })
        ]);
    }

    /**
     * Show sale return transaction details (AJAX)
     */
    public function showSaleReturn($customerId, $id)
    {
        $saleReturn = SaleReturnTransaction::with(['items.item', 'customer', 'salesman'])->findOrFail($id);

        return response()->json([
            'customer_name' => $saleReturn->customer->name ?? 'N/A',
            'sr_no' => $saleReturn->sr_no,
            'return_date' => $saleReturn->return_date->format('d-m-Y'),
            'original_invoice_no' => $saleReturn->original_invoice_no,
            'net_amount' => $saleReturn->net_amount,
            'items' => $saleReturn->items->map(function($item) {
                return [
                    'item_name' => $item->item->name ?? 'N/A',
                    'pack' => $item->packing ?? '',
                    'batch' => $item->batch_no ?? '',
                    'expiry' => $item->expiry_date ?? '',
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty ?? 0,
                    'rate' => $item->sale_rate,
                    'discount' => $item->discount_percent ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'amount' => $item->amount,
                ];
            })
        ]);
    }

    /**
     * Show breakage/expiry transaction details (AJAX)
     */
    public function showBreakageExpiry($customerId, $id)
    {
        $be = BreakageExpiryTransaction::with(['items.item', 'customer', 'salesman'])->findOrFail($id);

        return response()->json([
            'customer_name' => $be->customer->name ?? 'N/A',
            'sr_no' => $be->sr_no,
            'transaction_date' => $be->transaction_date->format('d-m-Y'),
            'net_amount' => $be->net_amount,
            'items' => $be->items->map(function($item) {
                return [
                    'item_name' => $item->item->name ?? 'N/A',
                    'pack' => $item->packing ?? '',
                    'batch' => $item->batch_no ?? '',
                    'expiry' => $item->expiry ?? '',
                    'qty' => $item->qty,
                    'rate' => $item->s_rate ?? 0,
                    'amount' => $item->amount,
                ];
            })
        ]);
    }

    /**
     * View customer expiry ledger - Shows Breakage/Expiry transactions only
     */
    public function expiryLedger(Customer $customer, Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $search = $request->get('search');

        // Fetch breakage/expiry transactions for the customer
        $transactions = BreakageExpiryTransaction::where('customer_id', $customer->id)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sr_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('sr_no', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.customers.expiry-ledger', compact(
                'customer',
                'transactions'
            ));
        }

        return view('admin.customers.expiry-ledger', compact(
            'customer',
            'transactions'
        ));
    }

    /**
     * Store expiry ledger entry
     */
    public function storeExpiryLedger(Customer $customer, Request $request)
    {
        // This method can be implemented if needed
        return redirect()->back()->with('success', 'Entry stored successfully');
    }

    /**
     * Delete expiry ledger entry
     */
    public function destroyExpiryLedger(Customer $customer, $ledger)
    {
        $transaction = BreakageExpiryTransaction::findOrFail($ledger);
        $transaction->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);
        }

        return redirect()->back()->with('success', 'Transaction deleted successfully');
    }

    /**
     * View customer bills - Shows all sale and return transactions
     */
    public function bills(Customer $customer, Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $search = $request->get('search');
        $status = $request->get('status'); // 'sale', 'return', or empty for all
        $series = $request->get('series');
        
        // Get current organization ID
        $orgId = auth()->user()->organization_id ?? 1;

        // Fetch sale transactions - use withoutGlobalScopes to avoid binding issues in union
        $salesQuery = SaleTransaction::withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->where('sale_transactions.organization_id', $orgId)
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->when($series, function($query) use ($series) {
                $query->where('series', $series);
            })
            ->select([
                'id',
                'invoice_no as bill_no',
                'series',
                'sale_date as date',
                'net_amount',
                DB::raw("'sale' as type")
            ]);

        // Fetch return transactions - use withoutGlobalScopes to avoid binding issues in union
        $returnsQuery = SaleReturnTransaction::withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->where('sale_return_transactions.organization_id', $orgId)
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sr_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->when($series, function($query) use ($series) {
                $query->where('series', $series);
            })
            ->select([
                'id',
                'sr_no as bill_no',
                'series',
                'return_date as date',
                'net_amount',
                DB::raw("'return' as type")
            ]);

        // Calculate totals before filtering by status
        $totalSaleAmount = SaleTransaction::where('customer_id', $customer->id)
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->when($series, function($query) use ($series) {
                $query->where('series', $series);
            })
            ->sum('net_amount');

        $totalReturnAmount = SaleReturnTransaction::where('customer_id', $customer->id)
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sr_no', 'LIKE', "%{$search}%")
                      ->orWhere('series', 'LIKE', "%{$search}%");
                });
            })
            ->when($series, function($query) use ($series) {
                $query->where('series', $series);
            })
            ->sum('net_amount');

        $netTotal = $totalSaleAmount - $totalReturnAmount;

        // Combine based on status filter
        if ($status === 'sale') {
            $combinedQuery = $salesQuery;
        } elseif ($status === 'return') {
            $combinedQuery = $returnsQuery;
        } else {
            $combinedQuery = $salesQuery->unionAll($returnsQuery);
        }

        // Get paginated results
        $bills = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery->getQuery())
            ->orderBy('date', 'desc')
            ->orderBy('bill_no', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.customers.bills', compact(
                'customer',
                'bills',
                'fromDate',
                'toDate',
                'totalSaleAmount',
                'totalReturnAmount',
                'netTotal'
            ));
        }

        return view('admin.customers.bills', compact(
            'customer',
            'bills',
            'fromDate',
            'toDate',
            'totalSaleAmount',
            'totalReturnAmount',
            'netTotal'
        ));
    }
}
