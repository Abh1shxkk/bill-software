{{-- Keyboard Shortcuts Configuration - Fully Database Driven --}}
@php
    use App\Models\Hotkey;
    
    $dbShortcuts = [];
    $categoryHotkeys = [];
    $hasDbHotkeys = false;
    
    try {
        if (class_exists(Hotkey::class) && \Schema::hasTable('hotkeys')) {
            $hotkeys = Hotkey::where('is_active', true)
                ->where('scope', 'global')
                ->orderBy('category')
                ->orderBy('module_name')
                ->get();
            
            if ($hotkeys->count() > 0) {
                $hasDbHotkeys = true;
                
                foreach ($hotkeys as $hotkey) {
                    // Build the shortcut data
                    $shortcutData = [
                        'description' => $hotkey->module_name,
                        'category' => $hotkey->category
                    ];
                    
                    // Handle special action hotkeys
                    if (str_starts_with($hotkey->route_name, '#')) {
                        $shortcutData['action'] = str_replace('#', '', $hotkey->route_name);
                    } elseif (\Route::has($hotkey->route_name)) {
                        $shortcutData['url'] = route($hotkey->route_name);
                    } else {
                        continue; // Skip if route doesn't exist
                    }
                    
                    $dbShortcuts[$hotkey->key_combination] = $shortcutData;
                    
                    // Group by category for help panel
                    if (!isset($categoryHotkeys[$hotkey->category])) {
                        $categoryHotkeys[$hotkey->category] = [];
                    }
                    $categoryHotkeys[$hotkey->category][] = [
                        'key' => $hotkey->key_combination,
                        'name' => $hotkey->module_name
                    ];
                }
            }
        }
    } catch (\Exception $e) {
        // Silently fall back to static config
        $hasDbHotkeys = false;
    }
    
    // Category display names and icons
    $categoryConfig = [
        'masters' => ['name' => 'Masters', 'icon' => 'bi-database', 'color' => 'text-warning'],
        'transactions' => ['name' => 'Transactions', 'icon' => 'bi-cart', 'color' => 'text-success'],
        'receipts' => ['name' => 'Receipts/Payments', 'icon' => 'bi-cash', 'color' => 'text-danger'],
        'notes' => ['name' => 'Notes & Vouchers', 'icon' => 'bi-journal', 'color' => 'text-purple'],
        'stock' => ['name' => 'Stock & Transfer', 'icon' => 'bi-box', 'color' => 'text-cyan'],
        'ledgers' => ['name' => 'Ledgers', 'icon' => 'bi-book', 'color' => 'text-pink'],
        'managers' => ['name' => 'Managers', 'icon' => 'bi-people', 'color' => 'text-teal'],
        'utilities' => ['name' => 'Utilities', 'icon' => 'bi-gear', 'color' => 'text-secondary'],
        'breakage' => ['name' => 'Breakage & Samples', 'icon' => 'bi-exclamation-triangle', 'color' => 'text-orange'],
    ];
@endphp
<script>
    window.KEYBOARD_SHORTCUTS_CONFIG = {
        // Dashboard URL for checking if on dashboard
        dashboardUrl: "{{ route('admin.dashboard') }}",
        
        // Flag to indicate if using database hotkeys
        isDynamic: {{ $hasDbHotkeys ? 'true' : 'false' }},
        
        // Category configuration for help panel
        categories: @json($categoryConfig),
        
        // Hotkeys grouped by category for help panel
        categoryHotkeys: @json($categoryHotkeys),
        
        // All shortcuts
        @if($hasDbHotkeys)
        shortcuts: @json($dbShortcuts)
        @else
        // Fallback to static shortcuts if database not available
        shortcuts: {
            // =============== MASTERS ===============
            'ctrl+f12': {
                url: "{{ route('admin.items.index') }}",
                description: 'Item Master',
                category: 'masters'
            },
            'ctrl+f11': {
                url: "{{ route('admin.customers.index') }}",
                description: 'Customer Master',
                category: 'masters'
            },
            'ctrl+f9': {
                url: "{{ route('admin.suppliers.index') }}",
                description: 'Supplier Master',
                category: 'masters'
            },
            'ctrl+shift+c': {
                url: "{{ route('admin.companies.index') }}",
                description: 'Companies',
                category: 'masters'
            },
            'ctrl+h': {
                url: "{{ route('admin.hsn-codes.index') }}",
                description: 'HSN Codes',
                category: 'masters'
            },
            'ctrl+shift+s': {
                url: "{{ route('admin.sales-men.index') }}",
                description: 'Salesmen',
                category: 'masters'
            },
            'alt+a': {
                url: "{{ route('admin.areas.index') }}",
                description: 'Areas',
                category: 'masters'
            },
            'alt+r': {
                url: "{{ route('admin.routes.index') }}",
                description: 'Routes',
                category: 'masters'
            },
            'alt+t': {
                url: "{{ route('admin.states.index') }}",
                description: 'States',
                category: 'masters'
            },
            'alt+c': {
                url: "{{ route('admin.item-category.index') }}",
                description: 'Item Categories',
                category: 'masters'
            },
            'alt+x': {
                url: "{{ route('admin.transport-master.index') }}",
                description: 'Transport Master',
                category: 'masters'
            },

            // =============== TRANSACTIONS ===============
            'ctrl+f1': {
                url: "{{ route('admin.sale.transaction') }}",
                description: 'Sale - Transaction',
                category: 'transactions'
            },
            'shift+ctrl+f1': {
                url: "{{ route('admin.sale.modification') }}",
                description: 'Sale - Modification',
                category: 'transactions'
            },
            'ctrl+f2': {
                url: "{{ route('admin.purchase.transaction') }}",
                description: 'Purchase - Transaction',
                category: 'transactions'
            },
            'shift+ctrl+f2': {
                url: "{{ route('admin.purchase.modification') }}",
                description: 'Purchase - Modification',
                category: 'transactions'
            },
            'ctrl+f3': {
                url: "{{ route('admin.sale-return.transaction') }}",
                description: 'Sale Return - Transaction',
                category: 'transactions'
            },
            'shift+ctrl+f3': {
                url: "{{ route('admin.sale-return.modification') }}",
                description: 'Sale Return - Modification',
                category: 'transactions'
            },
            'ctrl+f8': {
                url: "{{ route('admin.purchase-return.transaction') }}",
                description: 'Purchase Return - Transaction',
                category: 'transactions'
            },
            'shift+ctrl+f8': {
                url: "{{ route('admin.purchase-return.modification') }}",
                description: 'Purchase Return - Modification',
                category: 'transactions'
            },
            'ctrl+q': {
                url: "{{ route('admin.sale-challan.transaction') }}",
                description: 'Sale Challan',
                category: 'transactions'
            },
            'ctrl+shift+q': {
                url: "{{ route('admin.quotation.transaction') }}",
                description: 'Quotation',
                category: 'transactions'
            },
            'ctrl+shift+p': {
                url: "{{ route('admin.purchase-challan.transaction') }}",
                description: 'Purchase Challan',
                category: 'transactions'
            },

            // =============== RECEIPTS ===============
            'ctrl+f5': {
                url: "{{ route('admin.customer-receipt.transaction') }}",
                description: 'Customer Receipt - Transaction',
                category: 'receipts'
            },
            'shift+ctrl+f5': {
                url: "{{ route('admin.customer-receipt.modification') }}",
                description: 'Customer Receipt - Modification',
                category: 'receipts'
            },
            'ctrl+f7': {
                url: "{{ route('admin.supplier-payment.transaction') }}",
                description: 'Supplier Payment - Transaction',
                category: 'receipts'
            },
            'shift+ctrl+f7': {
                url: "{{ route('admin.supplier-payment.modification') }}",
                description: 'Supplier Payment - Modification',
                category: 'receipts'
            },
            'alt+q': {
                url: "{{ route('admin.cheque-return.index') }}",
                description: 'Cheque Return',
                category: 'receipts'
            },
            'alt+d': {
                url: "{{ route('admin.deposit-slip.index') }}",
                description: 'Deposit Slip',
                category: 'receipts'
            },

            // =============== NOTES ===============
            'ctrl+f6': {
                url: "{{ route('admin.credit-note.transaction') }}",
                description: 'Credit Note',
                category: 'notes'
            },
            'ctrl+f10': {
                url: "{{ route('admin.debit-note.transaction') }}",
                description: 'Debit Note',
                category: 'notes'
            },
            'alt+n': {
                url: "{{ route('admin.replacement-note.transaction') }}",
                description: 'Replacement Note',
                category: 'notes'
            },
            'alt+y': {
                url: "{{ route('admin.replacement-received.transaction') }}",
                description: 'Replacement Received',
                category: 'notes'
            },
            'ctrl+shift+v': {
                url: "{{ route('admin.voucher-entry.transaction') }}",
                description: 'Voucher Entry',
                category: 'notes'
            },
            'ctrl+m': {
                url: "{{ route('admin.multi-voucher.transaction') }}",
                description: 'Multi Voucher',
                category: 'notes'
            },
            'alt+v': {
                url: "{{ route('admin.sale-voucher.transaction') }}",
                description: 'Sale Voucher',
                category: 'notes'
            },
            'alt+f': {
                url: "{{ route('admin.purchase-voucher.transaction') }}",
                description: 'Purchase Voucher',
                category: 'notes'
            },
            'alt+u': {
                url: "{{ route('admin.voucher-purchase.transaction') }}",
                description: 'Voucher Purchase',
                category: 'notes'
            },
            'alt+i': {
                url: "{{ route('admin.voucher-income.transaction') }}",
                description: 'Voucher Income',
                category: 'notes'
            },

            // =============== STOCK ===============
            'ctrl+f4': {
                url: "{{ route('admin.stock-adjustment.transaction') }}",
                description: 'Stock Adjustment',
                category: 'stock'
            },
            'ctrl+shift+o': {
                url: "{{ route('admin.stock-transfer-outgoing.transaction') }}",
                description: 'Stock Transfer Outgoing',
                category: 'stock'
            },
            'alt+o': {
                url: "{{ route('admin.stock-transfer-outgoing-return.transaction') }}",
                description: 'Stock Transfer Outgoing Return',
                category: 'stock'
            },
            'alt+shift+t': {
                url: "{{ route('admin.stock-transfer-incoming.transaction') }}",
                description: 'Stock Transfer Incoming',
                category: 'stock'
            },
            'alt+j': {
                url: "{{ route('admin.stock-transfer-incoming-return.transaction') }}",
                description: 'Stock Transfer Incoming Return',
                category: 'stock'
            },

            // =============== BREAKAGE ===============
            'ctrl+e': {
                url: "{{ route('admin.breakage-expiry.transaction') }}",
                description: 'Breakage/Expiry from Customer',
                category: 'breakage'
            },
            'alt+g': {
                url: "{{ route('admin.godown-breakage-expiry.create') }}",
                description: 'Godown Breakage/Expiry',
                category: 'breakage'
            },
            'alt+shift+i': {
                url: "{{ route('admin.sample-issued.create') }}",
                description: 'Sample Issued',
                category: 'breakage'
            },
            'alt+shift+r': {
                url: "{{ route('admin.sample-received.create') }}",
                description: 'Sample Received',
                category: 'breakage'
            },
            'alt+w': {
                url: "{{ route('admin.claim-to-supplier.transaction') }}",
                description: 'Claim to Supplier',
                category: 'breakage'
            },
            'alt+7': {
                url: "{{ route('admin.breakage-supplier.issued-transaction') }}",
                description: 'Breakage Supplier - Issued',
                category: 'breakage'
            },
            'alt+8': {
                url: "{{ route('admin.breakage-supplier.received-transaction') }}",
                description: 'Breakage Supplier - Received',
                category: 'breakage'
            },
            'alt+9': {
                url: "{{ route('admin.breakage-supplier.unused-dump-transaction') }}",
                description: 'Breakage Supplier - Dump',
                category: 'breakage'
            },

            // =============== LEDGERS ===============
            'shift+insert': {
                url: "{{ route('admin.general-ledger.index') }}",
                description: 'General Ledger',
                category: 'ledgers'
            },
            'ctrl+insert': {
                url: "{{ route('admin.cash-bank-books.index') }}",
                description: 'Cash/Bank Books',
                category: 'ledgers'
            },
            'ctrl+shift+a': {
                url: "{{ route('admin.all-ledger.index') }}",
                description: 'All A/C Ledgers',
                category: 'ledgers'
            },
            'alt+l': {
                url: "{{ route('admin.sale-ledger.index') }}",
                description: 'Sale Ledger',
                category: 'ledgers'
            },
            'alt+k': {
                url: "{{ route('admin.purchase-ledger.index') }}",
                description: 'Purchase Ledger',
                category: 'ledgers'
            },

            // =============== MANAGERS ===============
            'alt+1': {
                url: "{{ route('admin.area-managers.index') }}",
                description: 'Area Managers',
                category: 'managers'
            },
            'alt+2': {
                url: "{{ route('admin.regional-managers.index') }}",
                description: 'Regional Managers',
                category: 'managers'
            },
            'alt+3': {
                url: "{{ route('admin.marketing-managers.index') }}",
                description: 'Marketing Managers',
                category: 'managers'
            },
            'alt+4': {
                url: "{{ route('admin.general-managers.index') }}",
                description: 'General Managers',
                category: 'managers'
            },
            'alt+5': {
                url: "{{ route('admin.divisional-managers.index') }}",
                description: 'Divisional Managers',
                category: 'managers'
            },
            'alt+6': {
                url: "{{ route('admin.country-managers.index') }}",
                description: 'Country Managers',
                category: 'managers'
            },

            // =============== UTILITIES ===============
            'ctrl+d': {
                url: "{{ route('admin.dashboard') }}",
                description: 'Dashboard',
                category: 'utilities'
            },
            'ctrl+n': {
                url: "{{ route('admin.general-notebook.index') }}",
                description: 'General Notebook',
                category: 'utilities'
            },
            'alt+backspace': {
                url: "{{ route('admin.personal-directory.index') }}",
                description: 'Personal Directory',
                category: 'utilities'
            },
            'shift+delete': {
                url: "{{ route('admin.general-reminders.index') }}",
                description: 'General Reminders',
                category: 'utilities'
            },
            'alt+m': {
                url: "{{ route('admin.pending-order-item.transaction') }}",
                description: 'Pending Order Items',
                category: 'utilities'
            },
            'alt+s': {
                url: "{{ route('admin.reports.sales') }}",
                description: 'Sales Report',
                category: 'utilities'
            },
            'alt+p': {
                url: "{{ route('admin.reports.purchase') }}",
                description: 'Purchase Report',
                category: 'utilities'
            },
            'ctrl+shift+k': {
                action: 'calculator',
                description: 'Open Calculator',
                category: 'utilities'
            }
        }
        @endif
    };
</script>
