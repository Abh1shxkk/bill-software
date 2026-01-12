<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tables that need organization_id for multi-tenancy
     */
    protected array $tenantTables = [
        // Master tables
        'companies',
        'customers',
        'suppliers',
        'items',
        'batches',
        'hsn_codes',
        'locations',
        'salesmen',
        'areas',
        'routes',
        'transport_masters',
        'item_categories',
        
        // Sale transactions
        'sale_transactions',
        'sale_transaction_items',
        'sale_return_transactions',
        'sale_return_transaction_items',
        'sale_challan_transactions',
        'sale_challan_transaction_items',
        'sale_return_replacement_transactions',
        'sale_return_replacement_items',
        
        // Purchase transactions
        'purchase_transactions',
        'purchase_transaction_items',
        'purchase_return_transactions',
        'purchase_return_transaction_items',
        'purchase_challan_transactions',
        'purchase_challan_transaction_items',
        
        // Financial
        'customer_receipts',
        'customer_receipt_items',
        'customer_receipt_adjustments',
        'supplier_payments',
        'supplier_payment_items',
        'supplier_payment_adjustments',
        'credit_notes',
        'credit_note_items',
        'debit_notes',
        'debit_note_items',
        'vouchers',
        'voucher_items',
        'purchase_vouchers',
        'purchase_voucher_items',
        'purchase_voucher_accounts',
        'income_vouchers',
        'income_voucher_items',
        'income_voucher_accounts',
        'multi_vouchers',
        'multi_voucher_entries',
        'bank_transactions',
        'cheque_returns',
        'deposit_slips',
        
        // Inventory
        'stock_ledgers',
        'stock_adjustments',
        'stock_adjustment_items',
        'stock_transfer_outgoing_transactions',
        'stock_transfer_outgoing_transaction_items',
        'stock_transfer_outgoing_return_transactions',
        'stock_transfer_outgoing_return_transaction_items',
        'stock_transfer_incoming_transactions',
        'stock_transfer_incoming_transaction_items',
        'stock_transfer_incoming_return_transactions',
        'stock_transfer_incoming_return_transaction_items',
        
        // Breakage & Expiry
        'breakage_expiry_transactions',
        'breakage_expiry_transaction_items',
        'breakage_expiry_adjustments',
        'breakage_supplier_issued_transactions',
        'breakage_supplier_issued_transaction_items',
        'breakage_supplier_received_transactions',
        'breakage_supplier_received_transaction_items',
        'breakage_supplier_received_adjustments',
        'breakage_supplier_unused_dump_transactions',
        'breakage_supplier_unused_dump_transaction_items',
        'godown_breakage_expiry_transactions',
        'godown_breakage_expiry_transaction_items',
        'claim_to_supplier_transactions',
        'claim_to_supplier_transaction_items',
        
        // Samples
        'sample_received_transactions',
        'sample_received_transaction_items',
        'sample_issued_transactions',
        'sample_issued_transaction_items',
        
        // Replacement
        'replacement_note_transactions',
        'replacement_note_transaction_items',
        'replacement_received_transactions',
        'replacement_received_transaction_items',
        'replacement_received_adjustments',
        
        // Ledgers
        'customer_ledgers',
        'customer_dues',
        'customer_discounts',
        'customer_challans',
        'customer_prescriptions',
        'customer_special_rates',
        'sale_ledgers',
        'purchase_ledgers',
        'expiry_ledgers',
        'general_ledgers',
        
        // Quotations & Orders
        'quotations',
        'quotation_items',
        'pending_orders',
        'pending_order_items',
        
        // Misc  
        'invoices',
        'invoice_items',
        'general_notebooks',
        'general_reminders',
        'personal_directories',
        'page_settings',
        'hotkeys',
        
        // Adjustments
        'sale_return_adjustments',
        'purchase_return_adjustments',
        'debit_note_adjustments',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add organization_id to users table (special handling since pk is user_id)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'organization_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('user_id');
                $table->boolean('is_organization_owner')->default(false)->after('organization_id');
                $table->index('organization_id', 'users_org_idx');
            });
        }

        // Update users table role column to include super_admin
        if (Schema::hasTable('users')) {
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'manager', 'staff', 'readonly', 'user') DEFAULT 'staff'");
            } catch (\Exception $e) {
                // Column might already have this definition
            }
        }

        // Add organization_id to all tenant tables
        foreach ($this->tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Find the first column to add after
                    $columns = Schema::getColumnListing($tableName);
                    $firstCol = $columns[0] ?? 'id';
                    
                    $table->unsignedBigInteger('organization_id')->nullable()->after($firstCol);
                    $table->index('organization_id', $tableName . '_org_idx');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove from users first
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'organization_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropIndex('users_org_idx');
                    $table->dropColumn(['organization_id', 'is_organization_owner']);
                });
            }
            
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Remove from all tenant tables
        foreach ($this->tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropIndex($tableName . '_org_idx');
                    $table->dropColumn('organization_id');
                });
            }
        }
    }
};
