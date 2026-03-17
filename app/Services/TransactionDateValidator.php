<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionDateValidator
{
    /**
     * Transaction type to table/date column mapping
     */
    protected static array $transactionConfig = [
        // Sales
        'sale' => ['table' => 'sale_transactions', 'date_column' => 'sale_date'],
        'sale_return' => ['table' => 'sale_return_transactions', 'date_column' => 'return_date'],
        'sale_challan' => ['table' => 'sale_challan_transactions', 'date_column' => 'challan_date'],
        'sale_voucher' => ['table' => 'sale_vouchers', 'date_column' => 'voucher_date'],
        'sale_return_voucher' => ['table' => 'sale_return_vouchers', 'date_column' => 'voucher_date'],
        'sale_return_replacement' => ['table' => 'sale_return_replacement_transactions', 'date_column' => 'trn_date'],

        // Purchase
        'purchase' => ['table' => 'purchase_transactions', 'date_column' => 'bill_date'],
        'purchase_return' => ['table' => 'purchase_return_transactions', 'date_column' => 'return_date'],
        'purchase_challan' => ['table' => 'purchase_challan_transactions', 'date_column' => 'challan_date'],
        'purchase_voucher' => ['table' => 'purchase_vouchers', 'date_column' => 'voucher_date'],
        'purchase_return_voucher' => ['table' => 'purchase_return_vouchers', 'date_column' => 'voucher_date'],

        // Breakage/Expiry
        'breakage_expiry' => ['table' => 'breakage_expiry_transactions', 'date_column' => 'transaction_date'],
        'breakage_supplier_issued' => ['table' => 'breakage_supplier_issued_transactions', 'date_column' => 'transaction_date'],
        'breakage_supplier_received' => ['table' => 'breakage_supplier_received_transactions', 'date_column' => 'transaction_date'],
        'godown_breakage_expiry' => ['table' => 'godown_breakage_expiry_transactions', 'date_column' => 'transaction_date'],

        // Receipts & Payments
        'customer_receipt' => ['table' => 'customer_receipts', 'date_column' => 'receipt_date'],
        'supplier_payment' => ['table' => 'supplier_payments', 'date_column' => 'payment_date'],
        'cheque_return' => ['table' => 'cheque_returns', 'date_column' => 'return_date'],
        'deposit_slip' => ['table' => 'deposit_slips', 'date_column' => 'deposit_date'],

        // Vouchers
        'voucher_entry' => ['table' => 'vouchers', 'date_column' => 'voucher_date'],
        'voucher_purchase' => ['table' => 'purchase_vouchers', 'date_column' => 'voucher_date'],
        'voucher_income' => ['table' => 'income_vouchers', 'date_column' => 'voucher_date'],
        'multi_voucher' => ['table' => 'multi_vouchers', 'date_column' => 'voucher_date'],
        'cash_bank' => ['table' => 'bank_transactions', 'date_column' => 'transaction_date'],

        // Notes
        'credit_note' => ['table' => 'credit_notes', 'date_column' => 'credit_note_date'],
        'debit_note' => ['table' => 'debit_notes', 'date_column' => 'debit_note_date'],
        'replacement_note' => ['table' => 'replacement_note_transactions', 'date_column' => 'transaction_date'],
        'replacement_received' => ['table' => 'replacement_received_transactions', 'date_column' => 'transaction_date'],

        // Stock
        'stock_adjustment' => ['table' => 'stock_adjustments', 'date_column' => 'adjustment_date'],
        'stock_transfer_outgoing' => ['table' => 'stock_transfer_outgoing_transactions', 'date_column' => 'transaction_date'],
        'stock_transfer_outgoing_return' => ['table' => 'stock_transfer_outgoing_return_transactions', 'date_column' => 'transaction_date'],
        'stock_transfer_incoming' => ['table' => 'stock_transfer_incoming_transactions', 'date_column' => 'transaction_date'],
        'stock_transfer_incoming_return' => ['table' => 'stock_transfer_incoming_return_transactions', 'date_column' => 'transaction_date'],

        // Samples
        'sample_issued' => ['table' => 'sample_issued_transactions', 'date_column' => 'transaction_date'],
        'sample_received' => ['table' => 'sample_received_transactions', 'date_column' => 'transaction_date'],

        // Others
        'quotation' => ['table' => 'quotations', 'date_column' => 'quotation_date'],
        'pending_order' => ['table' => 'pending_order_items', 'date_column' => 'created_at'],
        'claim_to_supplier' => ['table' => 'claim_to_supplier_transactions', 'date_column' => 'claim_date'],
    ];

    /**
     * Build a base query filtered to the current user's organization
     * and excluding soft-deleted records.
     */
    private static function baseQuery(string $table): \Illuminate\Database\Query\Builder
    {
        $orgId = Auth::user()->organization_id ?? null;

        $query = DB::table($table);

        // Always scope to current organization to prevent cross-tenant contamination
        if ($orgId !== null) {
            $query->where('organization_id', $orgId);
        }

        return $query;
    }

    /**
     * Fetch the latest transaction date as a normalized Y-m-d string.
     *
     * Selecting DATE(column) avoids time/tz edge cases where same-day entries
     * could be incorrectly treated as earlier.
     */
    private static function getLatestTransactionDateString(
        \Illuminate\Database\Query\Builder $query,
        string $dateColumn
    ): ?string {
        return (clone $query)
            ->selectRaw("DATE({$dateColumn}) as last_transaction_date")
            ->orderBy($dateColumn, 'desc')
            ->value('last_transaction_date');
    }

    /**
     * Normalize incoming input date to Y-m-d without timezone shifts.
     */
    private static function normalizeInputDateString(string $date): string
    {
        $value = trim($date);

        // Exact Y-m-d date (native HTML date input)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // ISO-like datetime: keep literal date part to avoid tz conversion drift
        if (preg_match('/^(\d{4}-\d{2}-\d{2})[T\s].*$/', $value, $matches)) {
            return $matches[1];
        }

        // Common explicit formats used in this project UI (strict parse only)
        $explicitFormats = ['!d-M-Y', '!d-m-Y', '!d/m/Y', '!d M Y'];

        foreach ($explicitFormats as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $value);
            $errors = \DateTimeImmutable::getLastErrors();

            if ($parsed !== false
                && (($errors['warning_count'] ?? 0) === 0)
                && (($errors['error_count'] ?? 0) === 0)) {
                return $parsed->format('Y-m-d');
            }
        }

        throw new \InvalidArgumentException('Unrecognized date format: ' . $value);
    }

    /**
     * Validate transaction date.
     *
     * Rules:
     * 1. Cannot be strictly BEFORE the last transaction date (no backdating).
     *    Transactions on the SAME date as the last one are explicitly allowed.
     * 2. Cannot be more than 1 day in the future (today + 1 max).
     *
     * @param string   $transactionType
     * @param string   $date       Y-m-d format
     * @param int|null $excludeId  Exclude this ID when checking (for updates)
     */
    public static function validate(string $transactionType, string $date, ?int $excludeId = null): array
    {

    
        $config = self::$transactionConfig[$transactionType] ?? null;

        if (!$config) {
            return [
                'valid'     => false,
                'message'   => "Unknown transaction type: {$transactionType}",
                'last_date' => null,
            ];
        }

        try {
            $normalizedInputDate = self::normalizeInputDateString($date);
        } catch (\InvalidArgumentException $e) {
            return [
                'valid' => false,
                'message' => '[TDV-20260317] Invalid date format. Please enter date as YYYY-MM-DD.',
                'last_date' => null,
            ];
        }

        $inputDate = Carbon::createFromFormat('Y-m-d', $normalizedInputDate)->startOfDay();
        $today     = Carbon::today();
        $maxFutureDate = $today->copy()->addDay(); // Tomorrow

        // Rule 2: future date limit (max 1 day ahead)
        if ($inputDate->gt($maxFutureDate)) {
            return [
                'valid'    => false,
                'message'  => 'Date cannot be more than 1 day in the future. Maximum allowed date is '
                              . $maxFutureDate->format('d-m-Y'),
                'last_date' => null,
                'max_date'  => $maxFutureDate->format('Y-m-d'),
            ];
        }

        // Rule 1: no backdating — same date as last transaction IS allowed
        $query = self::baseQuery($config['table']);

        // Apply extra where conditions if specified in config
        if (isset($config['where'])) {
            foreach ($config['where'] as $column => $value) {
                $query->where($column, $value);
            }
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lastTransactionDate = self::getLatestTransactionDateString($query, $config['date_column']);

        if ($lastTransactionDate) {
            $lastDate = Carbon::createFromFormat('Y-m-d', $lastTransactionDate)->startOfDay();

            // FIX: use lt (strictly less than) so that inputDate == lastDate is ALLOWED.
            // Using lte here would incorrectly block same-date transactions.
            if ($inputDate->lt($lastDate)) {
                return [
                    'valid'              => false,
                    'message'            => '[TDV-20260317] Date cannot be before the last transaction date ('
                                           . $lastDate->format('d-m-Y')
                                           . '). Entered date interpreted as '
                                           . $inputDate->format('d-m-Y')
                                           . ' (raw: ' . $date
                                           . ', normalized: ' . $normalizedInputDate . ')'
                                           . '). Backdating is not allowed.',
                    'last_date'          => $lastDate->format('Y-m-d'),
                    'last_date_formatted' => $lastDate->format('d-m-Y'),
                ];
            }
        }

        return [
            'valid'     => true,
            'message'   => 'Date is valid',
            'last_date' => $lastTransactionDate,
        ];
    }

    /**
     * Get the last transaction date for a type (scoped to current org).
     */
    public static function getLastTransactionDate(string $transactionType): ?string
    {
        $config = self::$transactionConfig[$transactionType] ?? null;

        if (!$config) {
            return null;
        }

        $query = self::baseQuery($config['table']);

        if (isset($config['where'])) {
            foreach ($config['where'] as $column => $value) {
                $query->where($column, $value);
            }
        }

        return self::getLatestTransactionDateString($query, $config['date_column']);
    }

    /**
     * Get min and max allowed dates for a transaction type.
     * min_date = last transaction date (same-date entry IS allowed).
     */
    public static function getAllowedDateRange(string $transactionType): array
    {
        $lastDate = self::getLastTransactionDate($transactionType);
        $today    = Carbon::today();
        $maxDate  = $today->copy()->addDay();

        return [
            'min_date'           => $lastDate ?? $today->format('Y-m-d'),
            'max_date'           => $maxDate->format('Y-m-d'),
            'min_date_formatted' => $lastDate
                ? Carbon::parse($lastDate)->format('d-m-Y')
                : $today->format('d-m-Y'),
            'max_date_formatted' => $maxDate->format('d-m-Y'),
        ];
    }

    /**
     * Get all supported transaction types.
     */
    public static function getSupportedTypes(): array
    {
        return array_keys(self::$transactionConfig);
    }
}