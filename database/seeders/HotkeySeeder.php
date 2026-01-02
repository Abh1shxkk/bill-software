<?php

namespace Database\Seeders;

use App\Models\Hotkey;
use Illuminate\Database\Seeder;

class HotkeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotkeys = [
            // =============== MASTERS ===============
            ['key_combination' => 'ctrl+f12', 'module_name' => 'Items', 'route_name' => 'admin.items.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Item Master'],
            ['key_combination' => 'ctrl+f11', 'module_name' => 'Customers', 'route_name' => 'admin.customers.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Customer Master'],
            ['key_combination' => 'ctrl+f9', 'module_name' => 'Suppliers', 'route_name' => 'admin.suppliers.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Supplier Master'],
            ['key_combination' => 'ctrl+shift+c', 'module_name' => 'Companies', 'route_name' => 'admin.companies.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Companies'],
            ['key_combination' => 'ctrl+shift+s', 'module_name' => 'Salesmen', 'route_name' => 'admin.sales-men.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Salesmen'],
            ['key_combination' => 'ctrl+h', 'module_name' => 'HSN Codes', 'route_name' => 'admin.hsn-codes.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open HSN Codes'],
            ['key_combination' => 'alt+a', 'module_name' => 'Areas', 'route_name' => 'admin.areas.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Areas'],
            ['key_combination' => 'alt+r', 'module_name' => 'Routes', 'route_name' => 'admin.routes.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Routes'],
            ['key_combination' => 'alt+t', 'module_name' => 'States', 'route_name' => 'admin.states.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open States'],
            ['key_combination' => 'alt+c', 'module_name' => 'Item Categories', 'route_name' => 'admin.item-category.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Item Categories'],
            ['key_combination' => 'alt+x', 'module_name' => 'Transport Master', 'route_name' => 'admin.transport-master.index', 'category' => 'masters', 'scope' => 'global', 'description' => 'Open Transport Master'],

            // =============== TRANSACTIONS ===============
            ['key_combination' => 'ctrl+f1', 'module_name' => 'Sale Transaction', 'route_name' => 'admin.sale.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Sale Transaction'],
            ['key_combination' => 'shift+ctrl+f1', 'module_name' => 'Sale Modification', 'route_name' => 'admin.sale.modification', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Sale Modification'],
            ['key_combination' => 'ctrl+f2', 'module_name' => 'Purchase Transaction', 'route_name' => 'admin.purchase.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Purchase Transaction'],
            ['key_combination' => 'shift+ctrl+f2', 'module_name' => 'Purchase Modification', 'route_name' => 'admin.purchase.modification', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Purchase Modification'],
            ['key_combination' => 'ctrl+f3', 'module_name' => 'Sale Return', 'route_name' => 'admin.sale-return.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Sale Return Transaction'],
            ['key_combination' => 'shift+ctrl+f3', 'module_name' => 'Sale Return Modification', 'route_name' => 'admin.sale-return.modification', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Sale Return Modification'],
            ['key_combination' => 'ctrl+f8', 'module_name' => 'Purchase Return', 'route_name' => 'admin.purchase-return.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Purchase Return Transaction'],
            ['key_combination' => 'shift+ctrl+f8', 'module_name' => 'Purchase Return Modification', 'route_name' => 'admin.purchase-return.modification', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Purchase Return Modification'],
            ['key_combination' => 'ctrl+e', 'module_name' => 'Breakage/Expiry', 'route_name' => 'admin.breakage-expiry.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Breakage/Expiry from Customer'],
            ['key_combination' => 'ctrl+q', 'module_name' => 'Sale Challan', 'route_name' => 'admin.sale-challan.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Sale Challan'],
            ['key_combination' => 'ctrl+shift+q', 'module_name' => 'Quotation', 'route_name' => 'admin.quotation.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Quotation'],
            ['key_combination' => 'ctrl+shift+p', 'module_name' => 'Purchase Challan', 'route_name' => 'admin.purchase-challan.transaction', 'category' => 'transactions', 'scope' => 'global', 'description' => 'Open Purchase Challan'],

            // =============== RECEIPTS & PAYMENTS ===============
            ['key_combination' => 'ctrl+f5', 'module_name' => 'Customer Receipt', 'route_name' => 'admin.customer-receipt.transaction', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Customer Receipt Transaction'],
            ['key_combination' => 'shift+ctrl+f5', 'module_name' => 'Customer Receipt Modification', 'route_name' => 'admin.customer-receipt.modification', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Customer Receipt Modification'],
            ['key_combination' => 'ctrl+f7', 'module_name' => 'Supplier Payment', 'route_name' => 'admin.supplier-payment.transaction', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Supplier Payment Transaction'],
            ['key_combination' => 'shift+ctrl+f7', 'module_name' => 'Supplier Payment Modification', 'route_name' => 'admin.supplier-payment.modification', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Supplier Payment Modification'],
            ['key_combination' => 'alt+q', 'module_name' => 'Cheque Return', 'route_name' => 'admin.cheque-return.index', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Cheque Return'],
            ['key_combination' => 'alt+d', 'module_name' => 'Deposit Slip', 'route_name' => 'admin.deposit-slip.index', 'category' => 'receipts', 'scope' => 'global', 'description' => 'Open Deposit Slip'],

            // =============== NOTES & VOUCHERS ===============
            ['key_combination' => 'ctrl+f6', 'module_name' => 'Credit Note', 'route_name' => 'admin.credit-note.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Credit Note'],
            ['key_combination' => 'ctrl+f10', 'module_name' => 'Debit Note', 'route_name' => 'admin.debit-note.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Debit Note'],
            ['key_combination' => 'alt+n', 'module_name' => 'Replacement Note', 'route_name' => 'admin.replacement-note.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Replacement Note'],
            ['key_combination' => 'alt+y', 'module_name' => 'Replacement Received', 'route_name' => 'admin.replacement-received.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Replacement Received'],
            ['key_combination' => 'ctrl+shift+v', 'module_name' => 'Voucher Entry', 'route_name' => 'admin.voucher-entry.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Voucher Entry'],
            ['key_combination' => 'ctrl+m', 'module_name' => 'Multi Voucher', 'route_name' => 'admin.multi-voucher.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Multi Voucher'],
            ['key_combination' => 'alt+u', 'module_name' => 'Voucher Purchase', 'route_name' => 'admin.voucher-purchase.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Voucher Purchase (Input GST)'],
            ['key_combination' => 'alt+i', 'module_name' => 'Voucher Income', 'route_name' => 'admin.voucher-income.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Voucher Income (Output GST)'],
            ['key_combination' => 'alt+v', 'module_name' => 'Sale Voucher', 'route_name' => 'admin.sale-voucher.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Sale Voucher (HSN)'],
            ['key_combination' => 'alt+f', 'module_name' => 'Purchase Voucher', 'route_name' => 'admin.purchase-voucher.transaction', 'category' => 'notes', 'scope' => 'global', 'description' => 'Open Purchase Voucher (HSN)'],

            // =============== STOCK & TRANSFER ===============
            ['key_combination' => 'ctrl+f4', 'module_name' => 'Stock Adjustment', 'route_name' => 'admin.stock-adjustment.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Stock Adjustment'],
            ['key_combination' => 'ctrl+shift+o', 'module_name' => 'Stock Transfer Outgoing', 'route_name' => 'admin.stock-transfer-outgoing.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Stock Transfer Outgoing'],
            ['key_combination' => 'alt+o', 'module_name' => 'ST Outgoing Return', 'route_name' => 'admin.stock-transfer-outgoing-return.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Stock Transfer Outgoing Return'],
            ['key_combination' => 'alt+shift+t', 'module_name' => 'Stock Transfer Incoming', 'route_name' => 'admin.stock-transfer-incoming.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Stock Transfer Incoming'],
            ['key_combination' => 'alt+j', 'module_name' => 'ST Incoming Return', 'route_name' => 'admin.stock-transfer-incoming-return.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Stock Transfer Incoming Return'],
            ['key_combination' => 'alt+g', 'module_name' => 'Godown Breakage/Expiry', 'route_name' => 'admin.godown-breakage-expiry.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Godown Breakage/Expiry'],
            ['key_combination' => 'alt+shift+i', 'module_name' => 'Sample Issued', 'route_name' => 'admin.sample-issued.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Sample Issued'],
            ['key_combination' => 'alt+shift+r', 'module_name' => 'Sample Received', 'route_name' => 'admin.sample-received.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Sample Received'],
            ['key_combination' => 'alt+w', 'module_name' => 'Claim to Supplier', 'route_name' => 'admin.claim-to-supplier.transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Claim to Supplier'],
            ['key_combination' => 'alt+7', 'module_name' => 'Breakage Supplier Issued', 'route_name' => 'admin.breakage-supplier.issued-transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Breakage Supplier Issued'],
            ['key_combination' => 'alt+8', 'module_name' => 'Breakage Supplier Received', 'route_name' => 'admin.breakage-supplier.received-transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Breakage Supplier Received'],
            ['key_combination' => 'alt+9', 'module_name' => 'Breakage Supplier Dump', 'route_name' => 'admin.breakage-supplier.dump-transaction', 'category' => 'stock', 'scope' => 'global', 'description' => 'Open Breakage Supplier Dump'],

            // =============== LEDGERS ===============
            ['key_combination' => 'shift+insert', 'module_name' => 'General Ledger', 'route_name' => 'admin.general-ledger.index', 'category' => 'ledgers', 'scope' => 'global', 'description' => 'Open General Ledger'],
            ['key_combination' => 'ctrl+insert', 'module_name' => 'Cash/Bank Books', 'route_name' => 'admin.cash-bank-books.index', 'category' => 'ledgers', 'scope' => 'global', 'description' => 'Open Cash/Bank Books'],
            ['key_combination' => 'ctrl+shift+a', 'module_name' => 'All Ledgers', 'route_name' => 'admin.all-ledger.index', 'category' => 'ledgers', 'scope' => 'global', 'description' => 'Open All A/C Ledgers'],
            ['key_combination' => 'alt+l', 'module_name' => 'Sale Ledger', 'route_name' => 'admin.sale-ledger.index', 'category' => 'ledgers', 'scope' => 'global', 'description' => 'Open Sale Ledger'],
            ['key_combination' => 'alt+k', 'module_name' => 'Purchase Ledger', 'route_name' => 'admin.purchase-ledger.index', 'category' => 'ledgers', 'scope' => 'global', 'description' => 'Open Purchase Ledger'],

            // =============== MANAGERS ===============
            ['key_combination' => 'alt+1', 'module_name' => 'Area Managers', 'route_name' => 'admin.area-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open Area Managers'],
            ['key_combination' => 'alt+2', 'module_name' => 'Regional Managers', 'route_name' => 'admin.regional-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open Regional Managers'],
            ['key_combination' => 'alt+3', 'module_name' => 'Marketing Managers', 'route_name' => 'admin.marketing-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open Marketing Managers'],
            ['key_combination' => 'alt+4', 'module_name' => 'General Managers', 'route_name' => 'admin.general-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open General Managers'],
            ['key_combination' => 'alt+5', 'module_name' => 'Divisional Managers', 'route_name' => 'admin.divisional-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open Divisional Managers'],
            ['key_combination' => 'alt+6', 'module_name' => 'Country Managers', 'route_name' => 'admin.country-managers.index', 'category' => 'managers', 'scope' => 'global', 'description' => 'Open Country Managers'],

            // =============== UTILITIES ===============
            ['key_combination' => 'ctrl+d', 'module_name' => 'Dashboard', 'route_name' => 'admin.dashboard', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Go to Dashboard'],
            ['key_combination' => 'ctrl+n', 'module_name' => 'General Notebook', 'route_name' => 'admin.general-notebook.index', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open General Notebook'],
            ['key_combination' => 'alt+backspace', 'module_name' => 'Personal Directory', 'route_name' => 'admin.personal-directory.index', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open Personal Directory'],
            ['key_combination' => 'shift+delete', 'module_name' => 'General Reminders', 'route_name' => 'admin.general-reminders.index', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open General Reminders'],
            ['key_combination' => 'alt+m', 'module_name' => 'Pending Orders', 'route_name' => 'admin.pending-orders.index', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open Pending Orders'],
            ['key_combination' => 'alt+s', 'module_name' => 'Sales Report', 'route_name' => 'admin.reports.sale', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open Sales Report'],
            ['key_combination' => 'alt+p', 'module_name' => 'Purchase Report', 'route_name' => 'admin.reports.purchase', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open Purchase Report'],
            ['key_combination' => 'ctrl+shift+k', 'module_name' => 'Calculator', 'route_name' => '#calculator', 'category' => 'utilities', 'scope' => 'global', 'description' => 'Open Calculator'],

            // =============== INDEX PAGE SHORTCUTS ===============
            ['key_combination' => 'f9', 'module_name' => 'Add New (Index)', 'route_name' => '#add-new', 'category' => 'index', 'scope' => 'index', 'description' => 'Add new record from index page'],
            ['key_combination' => 'f3', 'module_name' => 'Edit Selected (Index)', 'route_name' => '#edit', 'category' => 'index', 'scope' => 'index', 'description' => 'Edit selected record from index page'],
            ['key_combination' => 'delete', 'module_name' => 'Delete Selected (Index)', 'route_name' => '#delete', 'category' => 'index', 'scope' => 'index', 'description' => 'Delete selected record from index page'],
            ['key_combination' => 'arrowup', 'module_name' => 'Previous Row (Index)', 'route_name' => '#prev-row', 'category' => 'index', 'scope' => 'index', 'description' => 'Select previous row in table'],
            ['key_combination' => 'arrowdown', 'module_name' => 'Next Row (Index)', 'route_name' => '#next-row', 'category' => 'index', 'scope' => 'index', 'description' => 'Select next row in table'],
            ['key_combination' => 'end', 'module_name' => 'Save/Submit Form', 'route_name' => '#save', 'category' => 'index', 'scope' => 'index', 'description' => 'Save/Submit form (create/edit/transaction pages)'],
            ['key_combination' => 'enter', 'module_name' => 'Next Field', 'route_name' => '#next-field', 'category' => 'index', 'scope' => 'index', 'description' => 'Move to next form field'],
            ['key_combination' => 'shift+enter', 'module_name' => 'Previous Field', 'route_name' => '#prev-field', 'category' => 'index', 'scope' => 'index', 'description' => 'Move to previous form field'],
            ['key_combination' => 'escape', 'module_name' => 'Go Back', 'route_name' => '#back', 'category' => 'index', 'scope' => 'index', 'description' => 'Go back to previous page'],
            ['key_combination' => 'f1', 'module_name' => 'Shortcuts Help', 'route_name' => '#help', 'category' => 'index', 'scope' => 'index', 'description' => 'Show keyboard shortcuts help panel'],
        ];

        foreach ($hotkeys as $hotkey) {
            Hotkey::updateOrCreate(
                ['key_combination' => $hotkey['key_combination']],
                array_merge($hotkey, ['is_system' => true, 'is_active' => true])
            );
        }
    }
}
