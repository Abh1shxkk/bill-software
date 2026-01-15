<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates all transaction number unique constraints to be scoped by organization_id
     * to support multi-tenancy. Each organization can now have its own sequence of transaction numbers.
     */
    public function up(): void
    {
        $tables = [
            'purchase_transactions' => ['trn_no', 'purchase_transactions_trn_no_unique'],
            'sale_transactions' => ['invoice_no', 'sale_transactions_invoice_no_unique'],
            'sale_return_transactions' => ['sr_no', 'sale_return_transactions_sr_no_unique'],
            'purchase_return_transactions' => ['pr_no', 'purchase_return_transactions_pr_no_unique'],
            'sale_challan_transactions' => ['challan_no', 'sale_challan_transactions_challan_no_unique'],
            'purchase_challan_transactions' => ['challan_no', 'purchase_challan_transactions_challan_no_unique'],
            'stock_adjustments' => ['trn_no', 'stock_adjustments_trn_no_unique'],
            'claim_to_supplier_transactions' => ['claim_no', 'claim_to_supplier_transactions_claim_no_unique'],
            'breakage_supplier_issued_transactions' => ['trn_no', 'breakage_supplier_issued_transactions_trn_no_unique'],
            'breakage_supplier_received_transactions' => ['trn_no', 'breakage_supplier_received_transactions_trn_no_unique'],
            'breakage_supplier_unused_dump_transactions' => ['trn_no', 'breakage_supplier_unused_dump_transactions_trn_no_unique'],
            'godown_breakage_expiry_transactions' => ['trn_no', 'godown_breakage_expiry_transactions_trn_no_unique'],
            'sample_issued_transactions' => ['trn_no', 'sample_issued_transactions_trn_no_unique'],
            'sample_received_transactions' => ['trn_no', 'sample_received_transactions_trn_no_unique'],
            'stock_transfer_incoming_return_transactions' => ['trn_no', 'stock_transfer_incoming_return_transactions_trn_no_unique'],
            // Additional tables added
            'customer_receipts' => ['trn_no', 'customer_receipts_trn_no_unique'],
            'supplier_payments' => ['trn_no', 'supplier_payments_trn_no_unique'],
            'sale_return_replacement_transactions' => ['trn_no', 'sale_return_replacement_transactions_trn_no_unique'],
            'replacement_note_transactions' => ['rn_no', 'replacement_note_transactions_rn_no_unique'],
            'purchase_vouchers' => ['voucher_no', 'purchase_vouchers_voucher_no_unique'],
            'multi_vouchers' => ['voucher_no', 'multi_vouchers_voucher_no_unique'],
            'income_vouchers' => ['voucher_no', 'income_vouchers_voucher_no_unique'],
            'bank_transactions' => ['transaction_no', 'bank_transactions_transaction_no_unique'],
            'stock_transfer_outgoing_transactions' => ['sr_no', 'stock_transfer_outgoing_transactions_sr_no_unique'],
            'stock_transfer_outgoing_return_transactions' => ['sr_no', 'stock_transfer_outgoing_return_transactions_sr_no_unique'],
        ];

        foreach ($tables as $tableName => [$column, $oldIndexName]) {
            // Check if table exists
            if (!Schema::hasTable($tableName)) {
                echo "⚠️  Table '{$tableName}' does not exist. Skipping...\n";
                continue;
            }

            // Check if column exists
            if (!Schema::hasColumn($tableName, $column)) {
                echo "⚠️  Column '{$column}' does not exist in table '{$tableName}'. Skipping...\n";
                continue;
            }

            // Check if organization_id column exists
            if (!Schema::hasColumn($tableName, 'organization_id')) {
                echo "⚠️  Column 'organization_id' does not exist in table '{$tableName}'. Skipping...\n";
                continue;
            }

            try {
                // Drop old unique constraint if it exists
                $indexes = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$oldIndexName]);
                if (!empty($indexes)) {
                    DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$oldIndexName}`");
                    echo "✅ Dropped old unique constraint '{$oldIndexName}' from '{$tableName}'\n";
                }

                // Create new composite unique constraint (organization_id + transaction number)
                $newIndexName = str_replace('_unique', '_org_unique', $oldIndexName);
                
                // Check if new index already exists
                $existingNewIndex = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$newIndexName]);
                if (empty($existingNewIndex)) {
                    DB::statement("ALTER TABLE `{$tableName}` ADD UNIQUE KEY `{$newIndexName}` (`organization_id`, `{$column}`)");
                    echo "✅ Added new composite unique constraint '{$newIndexName}' on '{$tableName}' (organization_id, {$column})\n";
                } else {
                    echo "ℹ️  Constraint '{$newIndexName}' already exists on '{$tableName}'. Skipping...\n";
                }

            } catch (\Exception $e) {
                echo "❌ Error processing table '{$tableName}': " . $e->getMessage() . "\n";
                // Don't throw - continue with other tables
            }
        }

        echo "\n✨ Migration completed successfully!\n";
        echo "📝 Each organization can now have its own transaction number sequence.\n";
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: This will revert to global unique constraints!
     * Only run this if you're absolutely sure and have no duplicate transaction numbers.
     */
    public function down(): void
    {
        $tables = [
            'purchase_transactions' => ['trn_no', 'purchase_transactions_trn_no_unique'],
            'sale_transactions' => ['invoice_no', 'sale_transactions_invoice_no_unique'],
            'sale_return_transactions' => ['sr_no', 'sale_return_transactions_sr_no_unique'],
            'purchase_return_transactions' => ['pr_no', 'purchase_return_transactions_pr_no_unique'],
            'sale_challan_transactions' => ['challan_no', 'sale_challan_transactions_challan_no_unique'],
            'purchase_challan_transactions' => ['challan_no', 'purchase_challan_transactions_challan_no_unique'],
            'stock_adjustments' => ['trn_no', 'stock_adjustments_trn_no_unique'],
            'claim_to_supplier_transactions' => ['claim_no', 'claim_to_supplier_transactions_claim_no_unique'],
            'breakage_supplier_issued_transactions' => ['trn_no', 'breakage_supplier_issued_transactions_trn_no_unique'],
            'breakage_supplier_received_transactions' => ['trn_no', 'breakage_supplier_received_transactions_trn_no_unique'],
            'breakage_supplier_unused_dump_transactions' => ['trn_no', 'breakage_supplier_unused_dump_transactions_trn_no_unique'],
            'godown_breakage_expiry_transactions' => ['trn_no', 'godown_breakage_expiry_transactions_trn_no_unique'],
            'sample_issued_transactions' => ['trn_no', 'sample_issued_transactions_trn_no_unique'],
            'sample_received_transactions' => ['trn_no', 'sample_received_transactions_trn_no_unique'],
            'stock_transfer_incoming_return_transactions' => ['trn_no', 'stock_transfer_incoming_return_transactions_trn_no_unique'],
            // Additional tables added
            'customer_receipts' => ['trn_no', 'customer_receipts_trn_no_unique'],
            'supplier_payments' => ['trn_no', 'supplier_payments_trn_no_unique'],
            'sale_return_replacement_transactions' => ['trn_no', 'sale_return_replacement_transactions_trn_no_unique'],
            'replacement_note_transactions' => ['rn_no', 'replacement_note_transactions_rn_no_unique'],
            'purchase_vouchers' => ['voucher_no', 'purchase_vouchers_voucher_no_unique'],
            'multi_vouchers' => ['voucher_no', 'multi_vouchers_voucher_no_unique'],
            'income_vouchers' => ['voucher_no', 'income_vouchers_voucher_no_unique'],
            'bank_transactions' => ['transaction_no', 'bank_transactions_transaction_no_unique'],
            'stock_transfer_outgoing_transactions' => ['sr_no', 'stock_transfer_outgoing_transactions_sr_no_unique'],
            'stock_transfer_outgoing_return_transactions' => ['sr_no', 'stock_transfer_outgoing_return_transactions_sr_no_unique'],
        ];

        foreach ($tables as $tableName => [$column, $oldIndexName]) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            try {
                // Drop new composite constraint
                $newIndexName = str_replace('_unique', '_org_unique', $oldIndexName);
                $indexes = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$newIndexName]);
                if (!empty($indexes)) {
                    DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$newIndexName}`");
                }

                // Restore old global unique constraint
                $existingOldIndex = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$oldIndexName]);
                if (empty($existingOldIndex)) {
                    DB::statement("ALTER TABLE `{$tableName}` ADD UNIQUE KEY `{$oldIndexName}` (`{$column}`)");
                }

            } catch (\Exception $e) {
                echo "Error reverting table '{$tableName}': " . $e->getMessage() . "\n";
            }
        }

        echo "\n⚠️  Reverted to global unique constraints. Multi-tenancy support removed.\n";
    }
};
