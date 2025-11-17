<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDue;
use App\Models\SaleTransaction;
use App\Models\SaleReturnTransaction;
use App\Models\BreakageExpiryTransaction;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerDueController extends Controller
{
    use CrudNotificationTrait;

    /**
     * View customer dues
     */
    public function index(Customer $customer)
    {
        $search = request('search');
        $type = request('type');
        $fromDate = request('from_date');
        $toDate = request('to_date');

        // Build queries for each transaction type
        $saleQuery = SaleTransaction::where('customer_id', $customer->id)
            ->select([
                'id',
                'series',
                'invoice_no as trans_no',
                'sale_date as transaction_date',
                'due_date',
                'net_amount',
                \DB::raw("'sale' as transaction_type")
            ]);

        $saleReturnQuery = SaleReturnTransaction::where('customer_id', $customer->id)
            ->select([
                'id',
                'series',
                'sr_no as trans_no',
                'return_date as transaction_date',
                \DB::raw('return_date as due_date'),
                'net_amount',
                \DB::raw("'sale_return' as transaction_type")
            ]);

        $breakageExpiryQuery = BreakageExpiryTransaction::where('customer_id', $customer->id)
            ->select([
                'id',
                'series',
                'sr_no as trans_no',
                'transaction_date',
                'end_date as due_date',
                'net_amount',
                \DB::raw("'breakage_expiry' as transaction_type")
            ]);

        // Apply search filter
        if ($search) {
            $saleQuery->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('series', 'like', "%{$search}%");
            });
            $saleReturnQuery->where(function($q) use ($search) {
                $q->where('sr_no', 'like', "%{$search}%")
                  ->orWhere('series', 'like', "%{$search}%");
            });
            $breakageExpiryQuery->where(function($q) use ($search) {
                $q->where('sr_no', 'like', "%{$search}%")
                  ->orWhere('series', 'like', "%{$search}%");
            });
        }

        // Apply date filters
        if ($fromDate) {
            $saleQuery->whereDate('sale_date', '>=', $fromDate);
            $saleReturnQuery->whereDate('return_date', '>=', $fromDate);
            $breakageExpiryQuery->whereDate('transaction_date', '>=', $fromDate);
        }
        if ($toDate) {
            $saleQuery->whereDate('sale_date', '<=', $toDate);
            $saleReturnQuery->whereDate('return_date', '<=', $toDate);
            $breakageExpiryQuery->whereDate('transaction_date', '<=', $toDate);
        }

        // Apply type filter and union queries
        if ($type === 'sale') {
            $combinedQuery = $saleQuery;
        } elseif ($type === 'sale_return') {
            $combinedQuery = $saleReturnQuery;
        } elseif ($type === 'breakage_expiry') {
            $combinedQuery = $breakageExpiryQuery;
        } else {
            // Union all three queries
            $combinedQuery = $saleQuery
                ->union($saleReturnQuery)
                ->union($breakageExpiryQuery);
        }

        // Get all results for totals calculation
        $allTransactions = \DB::table(\DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery->getQuery())
            ->get();

        // Calculate totals
        $totalDebit = $allTransactions->where('transaction_type', 'sale')->sum('net_amount');
        $totalCredit = $allTransactions->whereIn('transaction_type', ['sale_return', 'breakage_expiry'])->sum('net_amount');

        // Paginate results
        $perPage = 10;
        $page = request('page', 1);
        
        // Re-run the query for pagination with ordering
        if ($type === 'sale') {
            $paginatedQuery = SaleTransaction::where('customer_id', $customer->id)
                ->select([
                    'id',
                    'series',
                    'invoice_no as trans_no',
                    'sale_date as transaction_date',
                    'due_date',
                    'net_amount',
                    \DB::raw("'sale' as transaction_type")
                ]);
            if ($search) {
                $paginatedQuery->where(function($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('series', 'like', "%{$search}%");
                });
            }
            if ($fromDate) $paginatedQuery->whereDate('sale_date', '>=', $fromDate);
            if ($toDate) $paginatedQuery->whereDate('sale_date', '<=', $toDate);
            $transactions = $paginatedQuery->orderBy('sale_date', 'desc')->paginate($perPage)->withQueryString();
        } elseif ($type === 'sale_return') {
            $paginatedQuery = SaleReturnTransaction::where('customer_id', $customer->id)
                ->select([
                    'id',
                    'series',
                    'sr_no as trans_no',
                    'return_date as transaction_date',
                    \DB::raw('return_date as due_date'),
                    'net_amount',
                    \DB::raw("'sale_return' as transaction_type")
                ]);
            if ($search) {
                $paginatedQuery->where(function($q) use ($search) {
                    $q->where('sr_no', 'like', "%{$search}%")
                      ->orWhere('series', 'like', "%{$search}%");
                });
            }
            if ($fromDate) $paginatedQuery->whereDate('return_date', '>=', $fromDate);
            if ($toDate) $paginatedQuery->whereDate('return_date', '<=', $toDate);
            $transactions = $paginatedQuery->orderBy('return_date', 'desc')->paginate($perPage)->withQueryString();
        } elseif ($type === 'breakage_expiry') {
            $paginatedQuery = BreakageExpiryTransaction::where('customer_id', $customer->id)
                ->select([
                    'id',
                    'series',
                    'sr_no as trans_no',
                    'transaction_date',
                    'end_date as due_date',
                    'net_amount',
                    \DB::raw("'breakage_expiry' as transaction_type")
                ]);
            if ($search) {
                $paginatedQuery->where(function($q) use ($search) {
                    $q->where('sr_no', 'like', "%{$search}%")
                      ->orWhere('series', 'like', "%{$search}%");
                });
            }
            if ($fromDate) $paginatedQuery->whereDate('transaction_date', '>=', $fromDate);
            if ($toDate) $paginatedQuery->whereDate('transaction_date', '<=', $toDate);
            $transactions = $paginatedQuery->orderBy('transaction_date', 'desc')->paginate($perPage)->withQueryString();
        } else {
            // Manual pagination for union query
            $sortedTransactions = $allTransactions->sortByDesc('transaction_date')->values();
            $total = $sortedTransactions->count();
            $items = $sortedTransactions->forPage($page, $perPage);
            
            $transactions = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('admin.customers.dues', compact(
            'customer',
            'transactions',
            'totalDebit',
            'totalCredit'
        ));
    }

    /**
     * Store due entry
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'series' => 'nullable|string|max:50',
            'trans_no' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            'trans_amount' => 'required|numeric|min:0',
            'update_ledger' => 'nullable|boolean',
        ]);

        // Build trans_no with series if provided
        $transNo = $validated['trans_no'];
        if (!empty($validated['series'])) {
            $transNo = $validated['series'] . '/' . $validated['trans_no'];
        }

        // Calculate due date (30 days from invoice date by default)
        $invoiceDate = \Carbon\Carbon::parse($validated['invoice_date']);
        $dueDate = $invoiceDate->copy()->addDays(30);

        // Prepare data for storage
        $dueData = [
            'customer_id' => $customer->id,
            'trans_no' => $transNo,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $dueDate->toDateString(),
            'trans_amount' => $validated['trans_amount'],
            'debit' => $validated['trans_amount'],  // Debit = Trans Amount
            'credit' => 0,  // Credit = 0 by default
            'days_from_invoice' => $invoiceDate->diffInDays(now()),
            'days_from_due' => $dueDate->diffInDays(now()),
            'hold' => false,
        ];

        // Create the due entry
        $due = CustomerDue::create($dueData);

        return redirect()->route('admin.customers.dues', $customer)
            ->with('success', 'Due entry created successfully. Total: ₹' . number_format($validated['trans_amount'], 2));
    }

    /**
     * View expiry due list
     */
    public function expiryList(Customer $customer)
    {
        // For now, using dummy data - will be calculated from database later
        return view('admin.customers.dues-expiry', compact('customer'));
    }

    /**
     * Delete due entry
     */
    public function destroy(Customer $customer, CustomerDue $due)
    {
        $dueName = $due->name ?? 'Item';
            $due->delete();
            $this->notifyDeleted($dueName);
            return back();
    }
}
