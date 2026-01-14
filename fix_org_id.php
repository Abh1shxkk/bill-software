<?php
/**
 * Fix Organization ID for All Tables
 * Run this script from browser: http://localhost/bill-software1/fix_org_id.php
 */

// Simple output
header('Content-Type: text/html; charset=utf-8');
echo "<html><head><title>Fix Organization ID</title></head><body>";
echo "<h1>Fixing Organization ID for All Tables</h1>";
echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'companies', 'customers', 'suppliers', 'items', 'batches', 'hsn_codes', 
    'locations', 'salesmen', 'areas', 'routes', 'transport_masters', 'item_categories',
    'sale_transactions', 'sale_transaction_items',
    'sale_return_transactions', 'sale_return_transaction_items',
    'sale_challan_transactions', 'sale_challan_transaction_items',
    'sale_return_replacement_transactions', 'sale_return_replacement_items',
    'purchase_transactions', 'purchase_transaction_items',
    'purchase_return_transactions', 'purchase_return_transaction_items',
    'purchase_challan_transactions', 'purchase_challan_transaction_items',
    'customer_receipts', 'customer_receipt_items', 'customer_receipt_adjustments',
    'supplier_payments', 'supplier_payment_items', 'supplier_payment_adjustments',
    'credit_notes', 'credit_note_items', 'debit_notes', 'debit_note_items',
    'vouchers', 'voucher_items',
    'purchase_vouchers', 'purchase_voucher_items', 'purchase_voucher_accounts',
    'income_vouchers', 'income_voucher_items', 'income_voucher_accounts',
    'multi_vouchers', 'multi_voucher_entries',
    'bank_transactions', 'cheque_returns', 'deposit_slips',
    'stock_ledgers', 'stock_adjustments', 'stock_adjustment_items',
    'stock_transfer_outgoing_transactions', 'stock_transfer_outgoing_transaction_items',
    'stock_transfer_outgoing_return_transactions', 'stock_transfer_outgoing_return_transaction_items',
    'stock_transfer_incoming_transactions', 'stock_transfer_incoming_transaction_items',
    'stock_transfer_incoming_return_transactions', 'stock_transfer_incoming_return_transaction_items',
    'breakage_expiry_transactions', 'breakage_expiry_transaction_items', 'breakage_expiry_adjustments',
    'breakage_supplier_issued_transactions', 'breakage_supplier_issued_transaction_items',
    'breakage_supplier_received_transactions', 'breakage_supplier_received_transaction_items',
    'breakage_supplier_received_adjustments',
    'breakage_supplier_unused_dump_transactions', 'breakage_supplier_unused_dump_transaction_items',
    'godown_breakage_expiry_transactions', 'godown_breakage_expiry_transaction_items',
    'claim_to_supplier_transactions', 'claim_to_supplier_transaction_items',
    'sample_received_transactions', 'sample_received_transaction_items',
    'sample_issued_transactions', 'sample_issued_transaction_items',
    'replacement_note_transactions', 'replacement_note_transaction_items',
    'replacement_received_transactions', 'replacement_received_transaction_items',
    'replacement_received_adjustments',
    'customer_ledgers', 'customer_dues', 'customer_discounts', 'customer_challans',
    'customer_prescriptions', 'customer_special_rates',
    'sale_ledgers', 'purchase_ledgers', 'expiry_ledgers', 'general_ledgers',
    'quotations', 'quotation_items', 'pending_orders', 'pending_order_items',
    'invoices', 'invoice_items', 'general_notebooks', 'general_reminders',
    'personal_directories', 'page_settings', 'hotkeys',
    'sale_return_adjustments', 'purchase_return_adjustments', 'debit_note_adjustments',
];

$totalUpdated = 0;
$updatedTables = [];

foreach ($tables as $table) {
    try {
        if (Schema::hasTable($table) && Schema::hasColumn($table, 'organization_id')) {
            $count = DB::table($table)
                ->whereNull('organization_id')
                ->update(['organization_id' => 1]);
            
            if ($count > 0) {
                echo "✓ Updated <strong>$count</strong> records in '<strong>$table</strong>'\n";
                $totalUpdated += $count;
                $updatedTables[] = "$table ($count)";
            }
        }
    } catch (Exception $e) {
        echo "✗ Error in '$table': " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "<strong style='color: green; font-size: 18px;'>COMPLETE!</strong>\n";
echo "Total records updated: <strong>$totalUpdated</strong>\n";
echo "All records now have organization_id = 1\n";
echo "</pre>";

if ($totalUpdated > 0) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
    echo "<strong>✓ Success!</strong> $totalUpdated records updated across " . count($updatedTables) . " tables.";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
    echo "<strong>ℹ No updates needed.</strong> All records already have organization_id set.";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'>You can now delete this file for security.</p>";
echo "</body></html>";
