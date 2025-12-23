<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            // Masters
            ['name' => 'companies', 'display_name' => 'Companies', 'group' => 'Masters', 'icon' => 'bi-buildings', 'sort_order' => 1],
            ['name' => 'customers', 'display_name' => 'Customers', 'group' => 'Masters', 'icon' => 'bi-people', 'sort_order' => 2],
            ['name' => 'items', 'display_name' => 'Items', 'group' => 'Masters', 'icon' => 'bi-box-seam', 'sort_order' => 3],
            ['name' => 'suppliers', 'display_name' => 'Suppliers', 'group' => 'Masters', 'icon' => 'bi-truck', 'sort_order' => 4],
            ['name' => 'hsn-codes', 'display_name' => 'HSN Master', 'group' => 'Masters', 'icon' => 'bi-upc-scan', 'sort_order' => 5],
            ['name' => 'batches', 'display_name' => 'Batches', 'group' => 'Masters', 'icon' => 'bi-collection', 'sort_order' => 6],
            
            // Ledgers
            ['name' => 'all-ledger', 'display_name' => 'All Ledger', 'group' => 'Ledgers', 'icon' => 'bi-journal-check', 'sort_order' => 10],
            ['name' => 'general-ledger', 'display_name' => 'General Ledger', 'group' => 'Ledgers', 'icon' => 'bi-journal-text', 'sort_order' => 11],
            ['name' => 'cash-bank-books', 'display_name' => 'Cash / Bank Books', 'group' => 'Ledgers', 'icon' => 'bi-cash-stack', 'sort_order' => 12],
            ['name' => 'sale-ledger', 'display_name' => 'Sale Ledger', 'group' => 'Ledgers', 'icon' => 'bi-cart-check', 'sort_order' => 13],
            ['name' => 'purchase-ledger', 'display_name' => 'Purchase Ledger', 'group' => 'Ledgers', 'icon' => 'bi-bag-check', 'sort_order' => 14],
            
            // Sales Transactions
            ['name' => 'sale', 'display_name' => 'Sale Transaction', 'group' => 'Sales', 'icon' => 'bi-cart-plus', 'sort_order' => 20],
            ['name' => 'sale-challan', 'display_name' => 'Sale Challan', 'group' => 'Sales', 'icon' => 'bi-receipt', 'sort_order' => 21],
            ['name' => 'sale-return', 'display_name' => 'Sale Return', 'group' => 'Sales', 'icon' => 'bi-cart-dash', 'sort_order' => 22],
            ['name' => 'quotation', 'display_name' => 'Quotation', 'group' => 'Sales', 'icon' => 'bi-file-text', 'sort_order' => 23],
            ['name' => 'customer-receipt', 'display_name' => 'Customer Receipt', 'group' => 'Sales', 'icon' => 'bi-cash-coin', 'sort_order' => 24],
            
            // Purchase Transactions
            ['name' => 'purchase', 'display_name' => 'Purchase Transaction', 'group' => 'Purchase', 'icon' => 'bi-bag-plus', 'sort_order' => 30],
            ['name' => 'purchase-challan', 'display_name' => 'Purchase Challan', 'group' => 'Purchase', 'icon' => 'bi-receipt-cutoff', 'sort_order' => 31],
            ['name' => 'purchase-return', 'display_name' => 'Purchase Return', 'group' => 'Purchase', 'icon' => 'bi-bag-dash', 'sort_order' => 32],
            ['name' => 'supplier-payment', 'display_name' => 'Supplier Payment', 'group' => 'Purchase', 'icon' => 'bi-wallet2', 'sort_order' => 33],
            
            // Stock Management
            ['name' => 'stock-adjustment', 'display_name' => 'Stock Adjustment', 'group' => 'Stock', 'icon' => 'bi-sliders', 'sort_order' => 40],
            ['name' => 'stock-transfer-outgoing', 'display_name' => 'Stock Transfer Outgoing', 'group' => 'Stock', 'icon' => 'bi-box-arrow-right', 'sort_order' => 41],
            ['name' => 'stock-transfer-incoming', 'display_name' => 'Stock Transfer Incoming', 'group' => 'Stock', 'icon' => 'bi-box-arrow-in-left', 'sort_order' => 42],
            ['name' => 'breakage-expiry', 'display_name' => 'Breakage/Expiry', 'group' => 'Stock', 'icon' => 'bi-x-circle', 'sort_order' => 43],
            ['name' => 'godown-breakage-expiry', 'display_name' => 'Godown Breakage/Expiry', 'group' => 'Stock', 'icon' => 'bi-house-x', 'sort_order' => 44],
            
            // Notes & Claims
            ['name' => 'credit-note', 'display_name' => 'Credit Note', 'group' => 'Notes', 'icon' => 'bi-file-earmark-plus', 'sort_order' => 50],
            ['name' => 'debit-note', 'display_name' => 'Debit Note', 'group' => 'Notes', 'icon' => 'bi-file-earmark-minus', 'sort_order' => 51],
            ['name' => 'claim-to-supplier', 'display_name' => 'Claim to Supplier', 'group' => 'Notes', 'icon' => 'bi-file-earmark-arrow-up', 'sort_order' => 52],
            ['name' => 'replacement-note', 'display_name' => 'Replacement Note', 'group' => 'Notes', 'icon' => 'bi-arrow-repeat', 'sort_order' => 53],
            
            // Samples
            ['name' => 'sample-issued', 'display_name' => 'Sample Issued', 'group' => 'Samples', 'icon' => 'bi-box-arrow-up', 'sort_order' => 60],
            ['name' => 'sample-received', 'display_name' => 'Sample Received', 'group' => 'Samples', 'icon' => 'bi-box-arrow-in-down', 'sort_order' => 61],
            
            // Vouchers
            ['name' => 'voucher-entry', 'display_name' => 'Voucher Entry', 'group' => 'Vouchers', 'icon' => 'bi-journal-plus', 'sort_order' => 70],
            ['name' => 'multi-voucher', 'display_name' => 'Multi Voucher', 'group' => 'Vouchers', 'icon' => 'bi-journals', 'sort_order' => 71],
            ['name' => 'bank-transaction', 'display_name' => 'Bank Transaction', 'group' => 'Vouchers', 'icon' => 'bi-bank', 'sort_order' => 72],
            ['name' => 'cheque-return', 'display_name' => 'Cheque Return', 'group' => 'Vouchers', 'icon' => 'bi-x-square', 'sort_order' => 73],
            ['name' => 'deposit-slip', 'display_name' => 'Deposit Slip', 'group' => 'Vouchers', 'icon' => 'bi-file-earmark-check', 'sort_order' => 74],
            
            // HR/Management
            ['name' => 'sales-men', 'display_name' => 'Sales Man', 'group' => 'HR', 'icon' => 'bi-person-badge', 'sort_order' => 80],
            ['name' => 'areas', 'display_name' => 'Area', 'group' => 'HR', 'icon' => 'bi-geo-alt', 'sort_order' => 81],
            ['name' => 'routes', 'display_name' => 'Route', 'group' => 'HR', 'icon' => 'bi-signpost', 'sort_order' => 82],
            ['name' => 'states', 'display_name' => 'State', 'group' => 'HR', 'icon' => 'bi-map', 'sort_order' => 83],
            ['name' => 'area-managers', 'display_name' => 'Area Manager', 'group' => 'HR', 'icon' => 'bi-person-workspace', 'sort_order' => 84],
            ['name' => 'regional-managers', 'display_name' => 'Regional Manager', 'group' => 'HR', 'icon' => 'bi-people-fill', 'sort_order' => 85],
            ['name' => 'marketing-managers', 'display_name' => 'Marketing Manager', 'group' => 'HR', 'icon' => 'bi-megaphone', 'sort_order' => 86],
            ['name' => 'general-managers', 'display_name' => 'General Manager', 'group' => 'HR', 'icon' => 'bi-person-badge', 'sort_order' => 87],
            ['name' => 'divisional-managers', 'display_name' => 'Divisional Manager', 'group' => 'HR', 'icon' => 'bi-diagram-3', 'sort_order' => 88],
            ['name' => 'country-managers', 'display_name' => 'Country Manager', 'group' => 'HR', 'icon' => 'bi-globe', 'sort_order' => 89],
            
            // Utilities
            ['name' => 'personal-directory', 'display_name' => 'Personal Directory', 'group' => 'Utilities', 'icon' => 'bi-person-lines-fill', 'sort_order' => 90],
            ['name' => 'general-reminders', 'display_name' => 'General Reminders', 'group' => 'Utilities', 'icon' => 'bi-bell', 'sort_order' => 91],
            ['name' => 'general-notebook', 'display_name' => 'General NoteBook', 'group' => 'Utilities', 'icon' => 'bi-journal-text', 'sort_order' => 92],
            ['name' => 'item-category', 'display_name' => 'Item Category', 'group' => 'Utilities', 'icon' => 'bi-tag', 'sort_order' => 93],
            ['name' => 'transport-master', 'display_name' => 'Transport Master', 'group' => 'Utilities', 'icon' => 'bi-truck', 'sort_order' => 94],
            
            // Reports
            ['name' => 'reports-sales', 'display_name' => 'Sales Reports', 'group' => 'Reports', 'icon' => 'bi-graph-up', 'sort_order' => 100],
            ['name' => 'reports-purchase', 'display_name' => 'Purchase Reports', 'group' => 'Reports', 'icon' => 'bi-graph-down', 'sort_order' => 101],
            
            // User Management (Admin Only)
            ['name' => 'user-management', 'display_name' => 'User Management', 'group' => 'Admin', 'icon' => 'bi-people-fill', 'sort_order' => 110],
        ];

        $now = now();
        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        DB::table('permissions')->insert($permissions);
    }

    public function down(): void
    {
        DB::table('permissions')->truncate();
    }
};
