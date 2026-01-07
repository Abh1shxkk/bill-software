<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Models\CashBankBook;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SaleTransaction;
use App\Models\PurchaseTransaction;
use App\Models\CustomerLedger;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    /**
     * Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        $reportData = collect();
        $totals = [
            'opening_debit' => 0,
            'opening_credit' => 0,
            'closing_debit' => 0,
            'closing_credit' => 0,
        ];

        // Default dates
        $asOnDate = $request->as_on_date ?? date('Y-m-d');
        $fromDate = $request->from_date ?? date('Y-04-01'); // Financial year start
        $trialHead = $request->trial_head ?? 'All';
        $showOpening = strtoupper($request->show_opening ?? 'Y') === 'Y';

        if ($request->has('view') || $request->has('print')) {
            // Get General Ledger accounts
            $query = GeneralLedger::query();
            
            if ($trialHead !== 'All') {
                $query->where('under', $trialHead);
            }

            $ledgers = $query->orderBy('account_code')->get();

            foreach ($ledgers as $ledger) {
                $openingBalance = (float) ($ledger->opening_balance ?? 0);
                $balanceType = $ledger->balance_type ?? 'D';
                
                // Calculate opening balance (debit/credit)
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                // Calculate transactions during the period
                $transactions = $this->getLedgerTransactions($ledger->id, $fromDate, $asOnDate);
                
                // Calculate closing balance
                $closingBalance = $openingBalance + $transactions['debit'] - $transactions['credit'];
                if ($balanceType === 'C') {
                    $closingBalance = $openingBalance - $transactions['debit'] + $transactions['credit'];
                }

                $closingDebit = $closingBalance > 0 ? abs($closingBalance) : 0;
                $closingCredit = $closingBalance < 0 ? abs($closingBalance) : 0;

                // Only include if there's any balance
                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $ledger->account_code,
                        'name' => $ledger->account_name,
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Add Cash/Bank Books
            $cashBankBooks = CashBankBook::orderBy('name')->get();
            foreach ($cashBankBooks as $book) {
                $openingBalance = (float) ($book->opening_balance ?? 0);
                $balanceType = $book->opening_balance_type ?? 'D';
                
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                // For now, closing = opening (transactions would be calculated from vouchers)
                $closingDebit = $openingDebit;
                $closingCredit = $openingCredit;

                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $book->alter_code ?? 'CB' . $book->id,
                        'name' => $book->name,
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Add Sundry Debtors (Customers)
            $customers = Customer::where('is_deleted', 0)
                ->where(function($q) {
                    $q->where('opening_balance', '!=', 0)
                      ->orWhereHas('ledgers');
                })
                ->withSum('ledgers as ledger_balance', 'amount')
                ->get();

            foreach ($customers as $customer) {
                $openingBalance = (float) ($customer->opening_balance ?? 0);
                $balanceType = $customer->opening_balance_type ?? 'D';
                $ledgerBalance = (float) ($customer->ledger_balance ?? 0);
                
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                $closingBalance = $openingBalance + $ledgerBalance;
                $closingDebit = $closingBalance > 0 ? $closingBalance : 0;
                $closingCredit = $closingBalance < 0 ? abs($closingBalance) : 0;

                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $customer->code ?? 'C' . $customer->id,
                        'name' => $customer->name . ' (Debtor)',
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Add Sundry Creditors (Suppliers)
            $suppliers = Supplier::where('is_deleted', 0)
                ->where('opening_balance', '!=', 0)
                ->get();

            foreach ($suppliers as $supplier) {
                $openingBalance = (float) ($supplier->opening_balance ?? 0);
                $balanceType = $supplier->opening_balance_type ?? 'C';
                
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                // Closing = Opening for now
                $closingDebit = $openingDebit;
                $closingCredit = $openingCredit;

                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $supplier->code ?? 'S' . $supplier->supplier_id,
                        'name' => $supplier->name . ' (Creditor)',
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Sort by code
            $reportData = $reportData->sortBy('code')->values();

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.trial-balance-print', compact(
                    'reportData', 'totals', 'asOnDate', 'fromDate', 'showOpening'
                ));
            }
        }

        return view('admin.reports.financial-reports.trial-balance', compact(
            'reportData', 'totals', 'asOnDate', 'fromDate', 'trialHead', 'showOpening'
        ));
    }

    /**
     * Get trial head options - Fixed list
     */
    private function getTrialHeadOptions()
    {
        return ['All', 'Assets', 'Liabilities', 'Purchases', 'Sales', 'Expenses', 'Income'];
    }

    /**
     * Get ledger transactions for a period
     */
    private function getLedgerTransactions($ledgerId, $fromDate, $toDate)
    {
        // This would query voucher entries for the ledger
        // For now, return zeros - implement based on your voucher structure
        return [
            'debit' => 0,
            'credit' => 0,
        ];
    }

    /**
     * Balance Sheet Report
     */
    public function balanceSheet(Request $request)
    {
        $liabilities = collect();
        $assets = collect();
        $totalLiabilities = 0;
        $totalAssets = 0;

        // Default dates
        $asOnDate = $request->as_on_date ?? date('Y-m-d');
        $fromDate = $request->from_date ?? date('Y-04-01'); // Financial year start

        if ($request->has('view') || $request->has('print')) {
            // LIABILITIES SIDE
            // Capital Account
            $capitalAccounts = GeneralLedger::where('under', 'Capital Account')
                ->orWhere('under', 'Capital')
                ->get();
            foreach ($capitalAccounts as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($account->balance_type === 'D') $balance = -$balance;
                if ($balance != 0) {
                    $liabilities->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalLiabilities += abs($balance);
                }
            }

            // Loans (Secured/Unsecured)
            $loanAccounts = GeneralLedger::where('under', 'like', '%Loan%')
                ->orWhere('under', 'Secured Loans')
                ->orWhere('under', 'Unsecured Loans')
                ->get();
            foreach ($loanAccounts as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($account->balance_type === 'D') $balance = -$balance;
                if ($balance != 0) {
                    $liabilities->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalLiabilities += abs($balance);
                }
            }

            // Sundry Creditors (Suppliers)
            $sundryCreditors = Supplier::where('is_deleted', 0)
                ->where('opening_balance', '>', 0)
                ->get();
            $creditorTotal = 0;
            foreach ($sundryCreditors as $supplier) {
                $balance = (float) ($supplier->opening_balance ?? 0);
                $creditorTotal += $balance;
            }
            if ($creditorTotal > 0) {
                $liabilities->push(['name' => 'Sundry Creditors', 'amount' => $creditorTotal]);
                $totalLiabilities += $creditorTotal;
            }

            // Current Liabilities
            $currentLiabilities = GeneralLedger::where('under', 'Current Liabilities')
                ->orWhere('under', 'Duties & Taxes')
                ->get();
            foreach ($currentLiabilities as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($account->balance_type === 'D') $balance = -$balance;
                if ($balance != 0) {
                    $liabilities->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalLiabilities += abs($balance);
                }
            }

            // ASSETS SIDE
            // Fixed Assets
            $fixedAssets = GeneralLedger::where('under', 'Fixed Assets')
                ->orWhere('under', 'like', '%Asset%')
                ->get();
            foreach ($fixedAssets as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($account->balance_type === 'C') $balance = -$balance;
                if ($balance != 0) {
                    $assets->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalAssets += abs($balance);
                }
            }

            // Cash & Bank
            $cashBankBooks = CashBankBook::orderBy('name')->get();
            foreach ($cashBankBooks as $book) {
                $balance = (float) ($book->opening_balance ?? 0);
                if ($book->opening_balance_type === 'C') $balance = -$balance;
                if ($balance != 0) {
                    $assets->push(['name' => $book->name, 'amount' => abs($balance)]);
                    $totalAssets += abs($balance);
                }
            }

            // Sundry Debtors (Customers)
            $sundryDebtors = Customer::where('is_deleted', 0)
                ->where(function($q) {
                    $q->where('opening_balance', '>', 0)
                      ->orWhereHas('ledgers');
                })
                ->withSum('ledgers as ledger_balance', 'amount')
                ->get();
            $debtorTotal = 0;
            foreach ($sundryDebtors as $customer) {
                $balance = (float) ($customer->opening_balance ?? 0) + (float) ($customer->ledger_balance ?? 0);
                if ($balance > 0) $debtorTotal += $balance;
            }
            if ($debtorTotal > 0) {
                $assets->push(['name' => 'Sundry Debtors', 'amount' => $debtorTotal]);
                $totalAssets += $debtorTotal;
            }

            // Stock/Inventory (if applicable)
            $stockAccounts = GeneralLedger::where('under', 'Stock-in-Hand')
                ->orWhere('under', 'Inventory')
                ->get();
            foreach ($stockAccounts as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($balance != 0) {
                    $assets->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalAssets += abs($balance);
                }
            }

            // Investments
            $investments = GeneralLedger::where('under', 'Investments')->get();
            foreach ($investments as $account) {
                $balance = (float) ($account->opening_balance ?? 0);
                if ($balance != 0) {
                    $assets->push(['name' => $account->account_name, 'amount' => abs($balance)]);
                    $totalAssets += abs($balance);
                }
            }

            // Calculate difference (Profit/Loss)
            $difference = $totalAssets - $totalLiabilities;
            if ($difference > 0) {
                // Profit - add to liabilities
                $liabilities->push(['name' => 'Profit & Loss A/c', 'amount' => $difference]);
                $totalLiabilities += $difference;
            } elseif ($difference < 0) {
                // Loss - add to assets
                $assets->push(['name' => 'Profit & Loss A/c', 'amount' => abs($difference)]);
                $totalAssets += abs($difference);
            }

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.balance-sheet-print', compact(
                    'liabilities', 'assets', 'totalLiabilities', 'totalAssets', 'asOnDate', 'fromDate'
                ));
            }
        }

        return view('admin.reports.financial-reports.balance-sheet', compact(
            'liabilities', 'assets', 'totalLiabilities', 'totalAssets', 'asOnDate', 'fromDate'
        ));
    }

    /**
     * Day Book Report
     */
    public function dayBook(Request $request)
    {
        $reportData = collect();
        $totals = [
            'debit' => 0,
            'credit' => 0,
        ];

        // Default dates
        $fromDate = $request->from_date ?? date('Y-m-d');
        $toDate = $request->to_date ?? date('Y-m-d');
        $entryType = strtoupper($request->entry_type ?? 'S'); // S = Single, D = Double
        $voucherType = strtoupper($request->voucher_type ?? ''); // J = Journal, P = Payment, R = Receipt

        if ($request->has('view') || $request->has('print')) {
            // Get vouchers within date range
            $query = Voucher::with('items')
                ->whereBetween('voucher_date', [$fromDate, $toDate])
                ->orderBy('voucher_date')
                ->orderBy('voucher_no');

            // Filter by voucher type if specified
            if ($voucherType) {
                $typeMap = [
                    'J' => 'journal',
                    'P' => 'payment',
                    'R' => 'receipt',
                ];
                if (isset($typeMap[$voucherType])) {
                    $query->where('voucher_type', $typeMap[$voucherType]);
                }
            }

            $vouchers = $query->get();

            foreach ($vouchers as $voucher) {
                if ($entryType === 'S') {
                    // Single entry - show each item separately
                    foreach ($voucher->items as $item) {
                        $reportData->push([
                            'voucher_no' => $voucher->voucher_no,
                            'date' => $voucher->voucher_date,
                            'account_name' => $item->account_name,
                            'debit' => (float) ($item->debit_amount ?? 0),
                            'credit' => (float) ($item->credit_amount ?? 0),
                            'narration' => $item->item_narration ?? $voucher->narration,
                        ]);

                        $totals['debit'] += (float) ($item->debit_amount ?? 0);
                        $totals['credit'] += (float) ($item->credit_amount ?? 0);
                    }
                } else {
                    // Double entry - show voucher totals
                    $reportData->push([
                        'voucher_no' => $voucher->voucher_no,
                        'date' => $voucher->voucher_date,
                        'account_name' => $this->getVoucherAccountSummary($voucher),
                        'debit' => (float) ($voucher->total_debit ?? 0),
                        'credit' => (float) ($voucher->total_credit ?? 0),
                        'narration' => $voucher->narration,
                    ]);

                    $totals['debit'] += (float) ($voucher->total_debit ?? 0);
                    $totals['credit'] += (float) ($voucher->total_credit ?? 0);
                }
            }

            // Also include Customer Receipts
            $customerReceipts = \App\Models\CustomerReceipt::whereBetween('receipt_date', [$fromDate, $toDate])
                ->orderBy('receipt_date')
                ->get();

            foreach ($customerReceipts as $receipt) {
                if (!$voucherType || $voucherType === 'R') {
                    $amount = (float) ($receipt->total_cash ?? 0) + (float) ($receipt->total_cheque ?? 0);
                    $reportData->push([
                        'voucher_no' => 'CR-' . $receipt->trn_no,
                        'date' => $receipt->receipt_date,
                        'account_name' => $receipt->ledger ?? 'Customer Receipt',
                        'debit' => $amount,
                        'credit' => 0,
                        'narration' => $receipt->remarks ?? 'Receipt from customer',
                    ]);
                    $totals['debit'] += $amount;
                }
            }

            // Also include Supplier Payments
            $supplierPayments = \App\Models\SupplierPayment::whereBetween('payment_date', [$fromDate, $toDate])
                ->orderBy('payment_date')
                ->get();

            foreach ($supplierPayments as $payment) {
                if (!$voucherType || $voucherType === 'P') {
                    $amount = (float) ($payment->total_cash ?? 0) + (float) ($payment->total_cheque ?? 0);
                    $reportData->push([
                        'voucher_no' => 'SP-' . $payment->trn_no,
                        'date' => $payment->payment_date,
                        'account_name' => $payment->ledger ?? 'Supplier Payment',
                        'debit' => 0,
                        'credit' => $amount,
                        'narration' => $payment->remarks ?? 'Payment to supplier',
                    ]);
                    $totals['credit'] += $amount;
                }
            }

            // Sort by date and voucher number
            $reportData = $reportData->sortBy([
                ['date', 'asc'],
                ['voucher_no', 'asc'],
            ])->values();

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.day-book-print', compact(
                    'reportData', 'totals', 'fromDate', 'toDate', 'entryType', 'voucherType'
                ));
            }
        }

        return view('admin.reports.financial-reports.day-book', compact(
            'reportData', 'totals', 'fromDate', 'toDate', 'entryType', 'voucherType'
        ));
    }

    /**
     * Get voucher account summary for double entry display
     */
    private function getVoucherAccountSummary($voucher)
    {
        $accounts = $voucher->items->pluck('account_name')->filter()->unique()->take(2)->implode(' / ');
        if ($voucher->items->count() > 2) {
            $accounts .= ' ...';
        }
        return $accounts ?: 'Multiple Accounts';
    }

    /**
     * Sundry Creditors Report (Suppliers)
     */
    public function sundryCreditors(Request $request)
    {
        $reportData = collect();
        $totals = [
            'opening_debit' => 0,
            'opening_credit' => 0,
            'closing_debit' => 0,
            'closing_credit' => 0,
        ];

        // Default dates
        $asOnDate = $request->as_on_date ?? date('Y-m-d');
        $fromDate = $request->from_date ?? date('Y-04-01'); // Financial year start
        $showOpening = strtoupper($request->show_opening ?? 'Y') === 'Y';
        $trialHead = $request->trial_head ?? 'All';

        if ($request->has('view') || $request->has('print')) {
            // Get all suppliers (Sundry Creditors)
            $suppliers = Supplier::where('is_deleted', 0)
                ->orderBy('name')
                ->get();

            foreach ($suppliers as $supplier) {
                $openingBalance = (float) ($supplier->opening_balance ?? 0);
                $balanceType = $supplier->opening_balance_type ?? 'C'; // Creditors typically have Credit balance
                
                // Calculate opening balance (debit/credit)
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                // Calculate transactions during the period (purchases - payments - returns)
                $purchases = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
                    ->whereBetween('bill_date', [$fromDate, $asOnDate])
                    ->sum('net_amount');

                $payments = \App\Models\SupplierPayment::where('ledger', 'like', '%' . $supplier->name . '%')
                    ->whereBetween('payment_date', [$fromDate, $asOnDate])
                    ->selectRaw('SUM(COALESCE(total_cash, 0) + COALESCE(total_cheque, 0)) as total')
                    ->value('total') ?? 0;

                $returns = \App\Models\PurchaseReturnTransaction::where('supplier_id', $supplier->supplier_id)
                    ->whereBetween('return_date', [$fromDate, $asOnDate])
                    ->sum('net_amount');

                // Calculate closing balance
                // For creditors: Opening Credit + Purchases - Payments - Returns
                $closingBalance = $openingCredit - $openingDebit + $purchases - $payments - $returns;
                
                $closingDebit = $closingBalance < 0 ? abs($closingBalance) : 0;
                $closingCredit = $closingBalance > 0 ? $closingBalance : 0;

                // Only include if there's any balance
                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $supplier->code ?? 'S' . $supplier->supplier_id,
                        'name' => $supplier->name,
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Sort by code
            $reportData = $reportData->sortBy('code')->values();

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.sundry-creditors-print', compact(
                    'reportData', 'totals', 'asOnDate', 'fromDate', 'showOpening'
                ));
            }
        }

        return view('admin.reports.financial-reports.sundry-creditors', compact(
            'reportData', 'totals', 'asOnDate', 'fromDate', 'showOpening', 'trialHead'
        ));
    }

    /**
     * Sundry Debtors Report (Customers)
     */
    public function sundryDebtors(Request $request)
    {
        $reportData = collect();
        $totals = [
            'opening_debit' => 0,
            'opening_credit' => 0,
            'closing_debit' => 0,
            'closing_credit' => 0,
        ];

        // Default dates
        $asOnDate = $request->as_on_date ?? date('Y-m-d');
        $fromDate = $request->from_date ?? date('Y-04-01'); // Financial year start
        $showOpening = strtoupper($request->show_opening ?? 'Y') === 'Y';
        $trialHead = $request->trial_head ?? 'All';

        if ($request->has('view') || $request->has('print')) {
            // Get all customers (Sundry Debtors)
            $customers = Customer::where('is_deleted', 0)
                ->orderBy('name')
                ->get();

            foreach ($customers as $customer) {
                $openingBalance = (float) ($customer->opening_balance ?? 0);
                $balanceType = $customer->opening_balance_type ?? 'D'; // Debtors typically have Debit balance
                
                // Calculate opening balance (debit/credit)
                $openingDebit = $balanceType === 'D' ? $openingBalance : 0;
                $openingCredit = $balanceType === 'C' ? $openingBalance : 0;

                // Calculate transactions during the period (sales - receipts - returns)
                $sales = SaleTransaction::where('customer_id', $customer->id)
                    ->whereBetween('sale_date', [$fromDate, $asOnDate])
                    ->sum('net_amount');

                $receipts = \App\Models\CustomerReceipt::where('ledger', 'like', '%' . $customer->name . '%')
                    ->whereBetween('receipt_date', [$fromDate, $asOnDate])
                    ->selectRaw('SUM(COALESCE(total_cash, 0) + COALESCE(total_cheque, 0)) as total')
                    ->value('total') ?? 0;

                $returns = \App\Models\SaleReturnTransaction::where('customer_id', $customer->id)
                    ->whereBetween('return_date', [$fromDate, $asOnDate])
                    ->sum('net_amount');

                // Calculate closing balance
                // For debtors: Opening Debit + Sales - Receipts - Returns
                $closingBalance = $openingDebit - $openingCredit + $sales - $receipts - $returns;
                
                $closingDebit = $closingBalance > 0 ? $closingBalance : 0;
                $closingCredit = $closingBalance < 0 ? abs($closingBalance) : 0;

                // Only include if there's any balance
                if ($openingDebit > 0 || $openingCredit > 0 || $closingDebit > 0 || $closingCredit > 0) {
                    $reportData->push([
                        'code' => $customer->code ?? 'C' . $customer->id,
                        'name' => $customer->name,
                        'opening_debit' => $openingDebit,
                        'opening_credit' => $openingCredit,
                        'closing_debit' => $closingDebit,
                        'closing_credit' => $closingCredit,
                    ]);

                    $totals['opening_debit'] += $openingDebit;
                    $totals['opening_credit'] += $openingCredit;
                    $totals['closing_debit'] += $closingDebit;
                    $totals['closing_credit'] += $closingCredit;
                }
            }

            // Sort by code
            $reportData = $reportData->sortBy('code')->values();

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.sundry-debtors-print', compact(
                    'reportData', 'totals', 'asOnDate', 'fromDate', 'showOpening'
                ));
            }
        }

        return view('admin.reports.financial-reports.sundry-debtors', compact(
            'reportData', 'totals', 'asOnDate', 'fromDate', 'showOpening', 'trialHead'
        ));
    }

    /**
     * Voucher Printing Report
     */
    public function voucherPrinting(Request $request)
    {
        $reportData = collect();

        // Default dates
        $fromDate = $request->from_date ?? date('Y-m-d');
        $toDate = $request->to_date ?? date('Y-m-d');
        $voucherType = $request->voucher_type ?? '00'; // 00 = All

        // Voucher type options
        $voucherTypes = [
            '00' => 'All',
            'receipt' => 'Receipt Voucher',
            'payment' => 'Payment Voucher',
            'contra' => 'Contra Voucher',
            'journal' => 'Journal Voucher',
        ];

        if ($request->has('print')) {
            // Get vouchers within date range
            $query = Voucher::with('items')
                ->whereBetween('voucher_date', [$fromDate, $toDate])
                ->orderBy('voucher_date')
                ->orderBy('voucher_no');

            // Filter by voucher type if specified
            if ($voucherType && $voucherType !== '00') {
                $query->where('voucher_type', $voucherType);
            }

            $reportData = $query->get();

            return view('admin.reports.financial-reports.voucher-printing-print', compact(
                'reportData', 'fromDate', 'toDate', 'voucherType', 'voucherTypes'
            ));
        }

        return view('admin.reports.financial-reports.voucher-printing', compact(
            'reportData', 'fromDate', 'toDate', 'voucherType', 'voucherTypes'
        ));
    }

    /**
     * Ledger Printing Report
     */
    public function ledgerPrinting(Request $request)
    {
        $reportData = collect();
        $selectedItems = collect();

        // Default dates
        $fromDate = $request->from_date ?? date('Y-04-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        
        // Filters
        $ledgerType = strtoupper($request->ledger_type ?? 'C'); // C = Customer, S = Supplier
        $salesmanCode = $request->salesman ?? '00';
        $areaCode = $request->area ?? '00';
        $routeCode = $request->route ?? '00';
        $nameFrom = $request->name_from ?? '';
        $nameTo = $request->name_to ?? '';
        $showAccountName = strtoupper($request->show_account_name ?? 'N') === 'Y';
        $tinOption = $request->tin_option ?? '3'; // 1=With TIN, 2=Without TIN, 3=All
        $viewMode = $request->view_mode ?? 'selective'; // all or selective

        // Get salesmen, areas, routes for dropdowns
        $salesmen = \App\Models\SalesMan::orderBy('name')->get();
        $areas = \App\Models\Area::orderBy('name')->get();
        $routes = \App\Models\Route::orderBy('name')->get();

        if ($request->has('view') || $request->has('print')) {
            // Build query based on ledger type
            if ($ledgerType === 'C') {
                $query = Customer::where('is_deleted', 0);
                
                // Apply filters
                if ($salesmanCode && $salesmanCode !== '00') {
                    $query->where('sales_man_code', $salesmanCode);
                }
                if ($areaCode && $areaCode !== '00') {
                    $query->where('area_code', $areaCode);
                }
                if ($routeCode && $routeCode !== '00') {
                    $query->where('route_code', $routeCode);
                }
                if ($nameFrom) {
                    $query->where('name', '>=', $nameFrom);
                }
                if ($nameTo) {
                    $query->where('name', '<=', $nameTo . 'zzz');
                }
                // TIN filter
                if ($tinOption === '1') {
                    $query->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($tinOption === '2') {
                    $query->where(function($q) {
                        $q->whereNull('gst_number')->orWhere('gst_number', '');
                    });
                }
                
                $selectedItems = $query->orderBy('name')->get()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'code' => $item->code ?? 'C' . $item->id,
                        'name' => $item->name,
                        'type' => 'customer',
                    ];
                });
            } else {
                $query = Supplier::where('is_deleted', 0);
                
                // Apply filters
                if ($nameFrom) {
                    $query->where('name', '>=', $nameFrom);
                }
                if ($nameTo) {
                    $query->where('name', '<=', $nameTo . 'zzz');
                }
                // TIN filter
                if ($tinOption === '1') {
                    $query->whereNotNull('gst_number')->where('gst_number', '!=', '');
                } elseif ($tinOption === '2') {
                    $query->where(function($q) {
                        $q->whereNull('gst_number')->orWhere('gst_number', '');
                    });
                }
                
                $selectedItems = $query->orderBy('name')->get()->map(function($item) {
                    return [
                        'id' => $item->supplier_id,
                        'code' => $item->code ?? 'S' . $item->supplier_id,
                        'name' => $item->name,
                        'type' => 'supplier',
                    ];
                });
            }

            if ($request->has('print')) {
                // Get detailed ledger data for printing
                $reportData = $this->getLedgerPrintData($selectedItems, $fromDate, $toDate, $ledgerType);
                
                return view('admin.reports.financial-reports.ledger-printing-print', compact(
                    'reportData', 'fromDate', 'toDate', 'ledgerType', 'showAccountName'
                ));
            }
        }

        return view('admin.reports.financial-reports.ledger-printing', compact(
            'selectedItems', 'fromDate', 'toDate', 'ledgerType', 'salesmanCode', 'areaCode', 
            'routeCode', 'nameFrom', 'nameTo', 'showAccountName', 'tinOption', 'viewMode',
            'salesmen', 'areas', 'routes'
        ));
    }

    /**
     * Get ledger print data for customers/suppliers
     */
    private function getLedgerPrintData($items, $fromDate, $toDate, $ledgerType)
    {
        $data = collect();

        foreach ($items as $item) {
            $ledgerEntries = collect();
            $openingBalance = 0;

            if ($ledgerType === 'C') {
                $customer = Customer::find($item['id']);
                if ($customer) {
                    $openingBalance = (float) ($customer->opening_balance ?? 0);
                    
                    // Get ledger entries using ledgers() relationship
                    $ledgerEntries = $customer->ledgers()
                        ->whereBetween('transaction_date', [$fromDate, $toDate])
                        ->orderBy('transaction_date')
                        ->get()
                        ->map(function($entry) {
                            $amount = (float) ($entry->amount ?? 0);
                            return [
                                'date' => $entry->transaction_date,
                                'particulars' => $entry->remarks ?? $entry->transaction_type ?? 'Transaction',
                                'voucher_no' => $entry->trans_no ?? '',
                                'debit' => $amount > 0 ? $amount : 0,
                                'credit' => $amount < 0 ? abs($amount) : 0,
                            ];
                        });
                }
            } else {
                $supplier = Supplier::find($item['id']);
                if ($supplier) {
                    $openingBalance = (float) ($supplier->opening_balance ?? 0);
                    
                    // Get supplier ledger entries (purchases, payments, returns)
                    $purchases = PurchaseTransaction::where('supplier_id', $item['id'])
                        ->whereBetween('bill_date', [$fromDate, $toDate])
                        ->get()
                        ->map(function($p) {
                            return [
                                'date' => $p->bill_date,
                                'particulars' => 'Purchase - Bill#' . $p->bill_no,
                                'voucher_no' => $p->bill_no,
                                'debit' => 0,
                                'credit' => (float) $p->net_amount,
                            ];
                        });

                    $payments = \App\Models\SupplierPayment::where('ledger', 'like', '%' . $supplier->name . '%')
                        ->whereBetween('payment_date', [$fromDate, $toDate])
                        ->get()
                        ->map(function($p) {
                            return [
                                'date' => $p->payment_date,
                                'particulars' => 'Payment - ' . $p->trn_no,
                                'voucher_no' => $p->trn_no,
                                'debit' => (float) (($p->total_cash ?? 0) + ($p->total_cheque ?? 0)),
                                'credit' => 0,
                            ];
                        });

                    $ledgerEntries = $purchases->concat($payments)->sortBy('date')->values();
                }
            }

            // Calculate running balance
            $runningBalance = $openingBalance;
            $entries = $ledgerEntries->map(function($entry) use (&$runningBalance) {
                $runningBalance = $runningBalance + $entry['debit'] - $entry['credit'];
                $entry['balance'] = $runningBalance;
                return $entry;
            });

            $data->push([
                'code' => $item['code'],
                'name' => $item['name'],
                'opening_balance' => $openingBalance,
                'entries' => $entries,
                'closing_balance' => $runningBalance,
                'total_debit' => $entries->sum('debit'),
                'total_credit' => $entries->sum('credit'),
            ]);
        }

        return $data;
    }

    /**
     * Ledger Summary Report
     */
    public function ledgerSummary(Request $request)
    {
        $reportData = collect();
        $totals = [
            'opening' => 0,
            'opening_type' => 'Dr',
            'debit' => 0,
            'credit' => 0,
            'closing' => 0,
            'closing_type' => 'Dr',
        ];

        // Default dates
        $fromDate = $request->from_date ?? date('Y-m-d');
        $toDate = $request->to_date ?? date('Y-m-d');
        
        // Filters
        $ledgerType = strtoupper($request->ledger_type ?? 'G'); // C = Customer, S = Supplier, G = General
        $groupHead = $request->group_head ?? 'All';
        $flag = $request->flag ?? '';

        // Get group head options for General Ledger
        $groupHeads = ['All', 'Assets', 'Liabilities', 'Purchases', 'Sales', 'Expenses', 'Income'];

        if ($request->has('view') || $request->has('print')) {
            if ($ledgerType === 'C') {
                // Customer Ledger Summary
                $customers = Customer::where('is_deleted', 0)
                    ->orderBy('name')
                    ->get();

                foreach ($customers as $customer) {
                    $openingBalance = (float) ($customer->opening_balance ?? 0);
                    $balanceType = $customer->balance_type ?? 'D';
                    
                    // Get transactions
                    $ledgerSum = $customer->ledgers()
                        ->whereBetween('transaction_date', [$fromDate, $toDate])
                        ->selectRaw('SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_debit, SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_credit')
                        ->first();

                    $debit = (float) ($ledgerSum->total_debit ?? 0);
                    $credit = (float) ($ledgerSum->total_credit ?? 0);
                    
                    // Calculate closing
                    $openingDr = $balanceType === 'D' ? $openingBalance : 0;
                    $openingCr = $balanceType === 'C' ? $openingBalance : 0;
                    $closingBalance = ($openingDr - $openingCr) + $debit - $credit;
                    
                    if ($openingBalance > 0 || $debit > 0 || $credit > 0 || $closingBalance != 0) {
                        $reportData->push([
                            'code' => $customer->code ?? 'C' . $customer->id,
                            'name' => $customer->name,
                            'opening' => abs($openingBalance),
                            'opening_type' => $balanceType === 'D' ? 'Dr' : 'Cr',
                            'debit' => $debit,
                            'credit' => $credit,
                            'closing' => abs($closingBalance),
                            'closing_type' => $closingBalance >= 0 ? 'Dr' : 'Cr',
                        ]);

                        $totals['opening'] += $openingDr - $openingCr;
                        $totals['debit'] += $debit;
                        $totals['credit'] += $credit;
                        $totals['closing'] += $closingBalance;
                    }
                }
            } elseif ($ledgerType === 'S') {
                // Supplier Ledger Summary
                $suppliers = Supplier::where('is_deleted', 0)
                    ->orderBy('name')
                    ->get();

                foreach ($suppliers as $supplier) {
                    $openingBalance = (float) ($supplier->opening_balance ?? 0);
                    $balanceType = $supplier->opening_balance_type ?? 'C';
                    
                    // Get purchases
                    $purchases = PurchaseTransaction::where('supplier_id', $supplier->supplier_id)
                        ->whereBetween('bill_date', [$fromDate, $toDate])
                        ->sum('net_amount');

                    // Get payments - SupplierPayment uses 'ledger' column with supplier name
                    $payments = \App\Models\SupplierPayment::where('ledger', 'like', '%' . $supplier->name . '%')
                        ->whereBetween('payment_date', [$fromDate, $toDate])
                        ->selectRaw('SUM(COALESCE(total_cash, 0) + COALESCE(total_cheque, 0)) as total')
                        ->value('total') ?? 0;

                    $debit = (float) $payments; // Payments reduce liability
                    $credit = (float) $purchases; // Purchases increase liability
                    
                    $openingDr = $balanceType === 'D' ? $openingBalance : 0;
                    $openingCr = $balanceType === 'C' ? $openingBalance : 0;
                    $closingBalance = ($openingCr - $openingDr) + $credit - $debit;
                    
                    if ($openingBalance > 0 || $debit > 0 || $credit > 0 || $closingBalance != 0) {
                        $reportData->push([
                            'code' => $supplier->code ?? 'S' . $supplier->supplier_id,
                            'name' => $supplier->name,
                            'opening' => abs($openingBalance),
                            'opening_type' => $balanceType === 'C' ? 'Cr' : 'Dr',
                            'debit' => $debit,
                            'credit' => $credit,
                            'closing' => abs($closingBalance),
                            'closing_type' => $closingBalance >= 0 ? 'Cr' : 'Dr',
                        ]);

                        $totals['opening'] += $openingCr - $openingDr;
                        $totals['debit'] += $debit;
                        $totals['credit'] += $credit;
                        $totals['closing'] += $closingBalance;
                    }
                }
            } else {
                // General Ledger Summary
                $query = GeneralLedger::query();
                if ($groupHead !== 'All') {
                    $query->where('under', $groupHead);
                }
                $ledgers = $query->orderBy('account_name')->get();

                foreach ($ledgers as $ledger) {
                    $openingBalance = (float) ($ledger->opening_balance ?? 0);
                    $balanceType = $ledger->balance_type ?? 'D';
                    
                    // For now, no transactions - just opening = closing
                    $debit = 0;
                    $credit = 0;
                    $closingBalance = $openingBalance;
                    
                    if ($openingBalance > 0) {
                        $reportData->push([
                            'code' => $ledger->account_code ?? 'G' . $ledger->id,
                            'name' => $ledger->account_name,
                            'opening' => abs($openingBalance),
                            'opening_type' => $balanceType === 'D' ? 'Dr' : 'Cr',
                            'debit' => $debit,
                            'credit' => $credit,
                            'closing' => abs($closingBalance),
                            'closing_type' => $balanceType === 'D' ? 'Dr' : 'Cr',
                        ]);

                        $openingDr = $balanceType === 'D' ? $openingBalance : 0;
                        $openingCr = $balanceType === 'C' ? $openingBalance : 0;
                        $totals['opening'] += $openingDr - $openingCr;
                        $totals['debit'] += $debit;
                        $totals['credit'] += $credit;
                        $totals['closing'] += $closingBalance * ($balanceType === 'D' ? 1 : -1);
                    }
                }
            }

            // Set total types
            $totals['opening_type'] = $totals['opening'] >= 0 ? 'Dr' : 'Cr';
            $totals['opening'] = abs($totals['opening']);
            $totals['closing_type'] = $totals['closing'] >= 0 ? 'Dr' : 'Cr';
            $totals['closing'] = abs($totals['closing']);

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.ledger-summary-print', compact(
                    'reportData', 'totals', 'fromDate', 'toDate', 'ledgerType', 'groupHead'
                ));
            }
        }

        return view('admin.reports.financial-reports.ledger-summary', compact(
            'reportData', 'totals', 'fromDate', 'toDate', 'ledgerType', 'groupHead', 'groupHeads', 'flag'
        ));
    }

    /**
     * Cash Deposite/Withdrawn Report
     */
    public function cashDepositeWithdrawn(Request $request)
    {
        $reportData = collect();
        $totals = [
            'amount' => 0,
        ];

        // Default dates
        $fromDate = $request->from_date ?? date('Y-m-d');
        $toDate = $request->to_date ?? date('Y-m-d');
        
        // Filters
        $transactionType = strtoupper($request->transaction_type ?? 'D'); // D = Deposited, W = Withdrawn
        $bankCode = $request->bank ?? '';

        // Get banks for dropdown (all cash/bank books)
        $banks = CashBankBook::orderBy('name')->get();

        if ($request->has('view') || $request->has('print')) {
            $query = \App\Models\BankTransaction::whereBetween('transaction_date', [$fromDate, $toDate])
                ->orderBy('transaction_date')
                ->orderBy('transaction_no');

            // Filter by transaction type
            if ($transactionType) {
                $query->where('transaction_type', $transactionType);
            }

            // Filter by bank
            if ($bankCode && $bankCode !== '00' && $bankCode !== '') {
                $query->where('bank_id', $bankCode);
            }

            $reportData = $query->get()->map(function($item) {
                return [
                    'date' => $item->transaction_date,
                    'transaction_no' => $item->transaction_no,
                    'bank_name' => $item->bank_name ?? ($item->bank ? $item->bank->name : ''),
                    'cheque_no' => $item->cheque_no,
                    'amount' => (float) $item->amount,
                    'narration' => $item->narration,
                    'type' => $item->transaction_type === 'D' ? 'Deposit' : 'Withdrawal',
                ];
            });

            $totals['amount'] = $reportData->sum('amount');

            if ($request->has('print')) {
                return view('admin.reports.financial-reports.cash-deposite-withdrawn-print', compact(
                    'reportData', 'totals', 'fromDate', 'toDate', 'transactionType'
                ));
            }
        }

        return view('admin.reports.financial-reports.cash-deposite-withdrawn', compact(
            'reportData', 'totals', 'fromDate', 'toDate', 'transactionType', 'bankCode', 'banks'
        ));
    }
}
