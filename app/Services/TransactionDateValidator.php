<?php

namespace App\Services;

use Carbon\Carbon;
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
        'sale_return_replacement' => ['table' => 'sale_return_replacement_transactions', 'date_column' => 'transaction_date'],
        
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
        'stock_transfer_outgoing_return' => ['table' => 'stock_transfer_outgoing_return_transactions', 'date_column' => 'return_date'],
        'stock_transfer_incoming' => ['table' => 'stock_transfer_incoming_transactions', 'date_column' => 'transaction_date'],
        'stock_transfer_incoming_return' => ['table' => 'stock_transfer_incoming_return_transactions', 'date_column' => 'return_date'],
        
        // Samples
        'sample_issued' => ['table' => 'sample_issued_transactions', 'date_column' => 'transaction_date'],
        'sample_received' => ['table' => 'sample_received_transactions', 'date_column' => 'transaction_date'],
        
        // Others
        'quotation' => ['table' => 'quotations', 'date_column' => 'quotation_date'],
        'pending_order' => ['table' => 'pending_order_items', 'date_column' => 'created_at'],
        'claim_to_supplier' => ['table' => 'claim_to_supplier_transactions', 'date_column' => 'claim_date'],
        
        // HSN Vouchers
        'sale_voucher' => ['table' => 'sale_transactions', 'date_column' => 'sale_date', 'where' => ['voucher_type' => 'voucher']],
        'sale_return_voucher' => ['table' => 'sale_return_transactions', 'date_column' => 'return_date', 'where' => ['voucher_type' => 'voucher']],
        'purchase_return_voucher' => ['table' => 'purchase_return_transactions', 'date_column' => 'return_date', 'where' => ['voucher_type' => 'voucher']],
    ];

    /**
     * Validate transaction date
     * 
     * Rules:
     * 1. Cannot be before the last transaction date (no backdating)
     * 2. Cannot be more than 1 day in the future (today + 1 max)
     * 
     * @param string $transactionType
     * @param string $date (Y-m-d format)
     * @param int|null $excludeId - Exclude this ID when checking (for updates)
     * @return array ['valid' => bool, 'message' => string, 'last_date' => string|null]
     */
    public static function validate(string $transactionType, string $date, ?int $excludeId = null): array
    {
        $config = self::$transactionConfig[$transactionType] ?? null;
        
        if (!$config) {
            return [
                'valid' => false,
                'message' => "Unknown transaction type: {$transactionType}",
                'last_date' => null
            ];
        }

        $inputDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::today();
        $maxFutureDate = $today->copy()->addDay(); // Tomorrow

        // Rule 2: Check future date limit (max 1 day ahead)
        if ($inputDate->gt($maxFutureDate)) {
            return [
                'valid' => false,
                'message' => "Date cannot be more than 1 day in the future. Maximum allowed date is " . $maxFutureDate->format('d-m-Y'),
                'last_date' => null,
                'max_date' => $maxFutureDate->format('Y-m-d')
            ];
        }

        // Rule 1: Check backdating (cannot be before last transaction)
        $query = DB::table($config['table']);
        
        // Apply additional where conditions if specified
        if (isset($config['where'])) {
            foreach ($config['where'] as $column => $value) {
                $query->where($column, $value);
            }
        }
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lastTransaction = $query->orderBy($config['date_column'], 'desc')->first();

        if ($lastTransaction) {
            $lastDate = Carbon::parse($lastTransaction->{$config['date_column']})->startOfDay();
            
            if ($inputDate->lt($lastDate)) {
                return [
                    'valid' => false,
                    'message' => "Date cannot be before the last transaction date (" . $lastDate->format('d-m-Y') . "). Backdating is not allowed.",
                    'last_date' => $lastDate->format('Y-m-d'),
                    'last_date_formatted' => $lastDate->format('d-m-Y')
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'Date is valid',
            'last_date' => $lastTransaction ? Carbon::parse($lastTransaction->{$config['date_column']})->format('Y-m-d') : null
        ];
    }

    /**
     * Get the last transaction date for a type
     */
    public static function getLastTransactionDate(string $transactionType): ?string
    {
        $config = self::$transactionConfig[$transactionType] ?? null;
        
        if (!$config) {
            return null;
        }

        $query = DB::table($config['table']);
        
        // Apply additional where conditions if specified
        if (isset($config['where'])) {
            foreach ($config['where'] as $column => $value) {
                $query->where($column, $value);
            }
        }

        $lastTransaction = $query->orderBy($config['date_column'], 'desc')->first();

        if ($lastTransaction) {
            return Carbon::parse($lastTransaction->{$config['date_column']})->format('Y-m-d');
        }

        return null;
    }

    /**
     * Get min and max allowed dates for a transaction type
     */
    public static function getAllowedDateRange(string $transactionType): array
    {
        $lastDate = self::getLastTransactionDate($transactionType);
        $today = Carbon::today();
        $maxDate = $today->copy()->addDay();

        return [
            'min_date' => $lastDate ?? $today->format('Y-m-d'),
            'max_date' => $maxDate->format('Y-m-d'),
            'min_date_formatted' => $lastDate ? Carbon::parse($lastDate)->format('d-m-Y') : $today->format('d-m-Y'),
            'max_date_formatted' => $maxDate->format('d-m-Y')
        ];
    }

    /**
     * Get all supported transaction types
     */
    public static function getSupportedTypes(): array
    {
        return array_keys(self::$transactionConfig);
    }
}
