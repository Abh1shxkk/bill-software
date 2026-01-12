<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // List of all tables that may need organization_id
        $tables = [
            'cash_bank_books',
            'general_ledgers',
            'sundry_debtors',
            'sundry_creditors',
            'routes',
            'areas',
            'transports',
            'godowns',
            'racks',
            'salesman_targets',
            'payment_terms',
            'tax_categories',
            'ledger_groups',
            'cost_centers',
            'branches',
            'divisions',
            'schemes',
            'scheme_details',
            'price_lists',
            'price_list_items',
            'voucher_series',
            'document_numbering',
            'bank_accounts',
            'cheque_books',
            'contra_entries',
            'debit_notes',
            'credit_notes',
            'journal_vouchers',
            'opening_stock',
            'stock_adjustments',
            'damage_entries',
            'expiry_entries',
            'rate_contracts',
            'quotations',
            'purchase_orders',
            'sale_orders',
            'delivery_challans',
            'goods_receipts',
            'indent_requests',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'organization_id')) {
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->unsignedBigInteger('organization_id')->nullable()->after('id');
                        $t->index('organization_id');
                    });
                    
                    // Set existing records to org 1
                    DB::table($table)->whereNull('organization_id')->update(['organization_id' => 1]);
                    
                    echo "Added organization_id to {$table}\n";
                } catch (\Exception $e) {
                    echo "Skipped {$table}: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    public function down(): void
    {
        // Rollback not needed for this migration
    }
};
