/**
 * Transaction Date Validator
 * 
 * Validates transaction dates to prevent:
 * 1. Backdating (date before last transaction)
 * 2. Future dating beyond 1 day
 * 
 * Usage:
 * 1. Include this script in your blade file
 * 2. Call TransactionDateValidator.init('sale', '#date_field', excludeId)
 * 3. Or use TransactionDateValidator.validate('sale', '2026-01-08') for manual validation
 */

const TransactionDateValidator = {
    // Cache for date ranges to avoid repeated API calls
    dateRangeCache: {},
    
    // Store last valid date for each field
    lastValidDates: {},
    
    /**
     * Initialize date validation on a date input field
     * @param {string} transactionType - Type of transaction (sale, purchase, etc.)
     * @param {string} dateSelector - jQuery selector for date input
     * @param {number|null} excludeId - ID to exclude (for updates)
     */
    init: function(transactionType, dateSelector, excludeId = null) {
        const self = this;
        const $dateInput = $(dateSelector);
        
        if (!$dateInput.length) {
            console.warn('TransactionDateValidator: Date input not found:', dateSelector);
            return;
        }
        
        // Store config on the element
        $dateInput.data('txn-type', transactionType);
        $dateInput.data('exclude-id', excludeId);
        
        // Store initial valid date
        const fieldId = $dateInput.attr('id') || dateSelector;
        self.lastValidDates[fieldId] = $dateInput.val();
        
        // Fetch and set date constraints
        self.fetchDateRange(transactionType).then(function(range) {
            if (range) {
                $dateInput.attr('min', range.min_date);
                $dateInput.attr('max', range.max_date);
                
                // Set default to today if empty
                if (!$dateInput.val()) {
                    const today = new Date().toISOString().split('T')[0];
                    $dateInput.val(today);
                    self.lastValidDates[fieldId] = today;
                }
            }
        });
        
        // Validate on change - INSTANT validation
        $dateInput.on('change', function() {
            self.validateFieldInstant($(this));
        });
    },
    
    /**
     * Instant validation on date change - revert if invalid
     */
    validateFieldInstant: function($field) {
        const self = this;
        const transactionType = $field.data('txn-type');
        const excludeId = $field.data('exclude-id');
        const date = $field.val();
        const fieldId = $field.attr('id') || $field.attr('name');
        
        if (!date || !transactionType) return;
        
        self.validate(transactionType, date, excludeId).then(function(result) {
            if (!result.valid) {
                // Show error immediately
                self.showAlert(result.message);
                
                // Always revert to TODAY's date when invalid
                const today = new Date().toISOString().split('T')[0];
                $field.val(today);
                self.lastValidDates[fieldId] = today;
                
                // Trigger change event for day name update etc.
                $field.trigger('input');
                
                // Add visual feedback
                $field.addClass('is-invalid');
                setTimeout(function() {
                    $field.removeClass('is-invalid');
                }, 2000);
            } else {
                // Store as last valid date
                self.lastValidDates[fieldId] = date;
                self.clearError($field);
            }
        });
    },
    
    /**
     * Fetch allowed date range for a transaction type
     */
    fetchDateRange: function(transactionType) {
        const self = this;
        
        // Return cached if available
        if (self.dateRangeCache[transactionType]) {
            return Promise.resolve(self.dateRangeCache[transactionType]);
        }
        
        return $.ajax({
            url: '/admin/api/transaction-date-range/' + transactionType,
            method: 'GET',
            dataType: 'json'
        }).then(function(response) {
            if (response.success) {
                self.dateRangeCache[transactionType] = response;
                return response;
            }
            return null;
        }).catch(function(error) {
            console.error('Error fetching date range:', error);
            return null;
        });
    },
    
    /**
     * Validate a date field (legacy - kept for compatibility)
     */
    validateField: function($field) {
        this.validateFieldInstant($field);
    },
    
    /**
     * Validate a transaction date via API
     * @param {string} transactionType
     * @param {string} date
     * @param {number|null} excludeId
     * @returns {Promise<{valid: boolean, message: string}>}
     */
    validate: function(transactionType, date, excludeId = null) {
        return $.ajax({
            url: '/admin/api/validate-transaction-date',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                transaction_type: transactionType,
                date: date,
                exclude_id: excludeId
            },
            dataType: 'json'
        }).then(function(response) {
            return response;
        }).catch(function(xhr) {
            if (xhr.responseJSON) {
                return xhr.responseJSON;
            }
            return { valid: false, message: 'Validation failed' };
        });
    },
    
    /**
     * Validate before form submission
     * @param {string} transactionType
     * @param {string} date
     * @param {number|null} excludeId
     * @returns {Promise<boolean>}
     */
    validateBeforeSubmit: async function(transactionType, date, excludeId = null) {
        const result = await this.validate(transactionType, date, excludeId);
        
        if (!result.valid) {
            this.showAlert(result.message);
            return false;
        }
        
        return true;
    },
    
    /**
     * Show error message near the field
     */
    showError: function($field, message) {
        this.clearError($field);
        
        $field.addClass('is-invalid');
        $field.after('<div class="invalid-feedback txn-date-error">' + message + '</div>');
        
        // Also show alert
        this.showAlert(message);
    },
    
    /**
     * Clear error message
     */
    clearError: function($field) {
        $field.removeClass('is-invalid');
        $field.siblings('.txn-date-error').remove();
    },
    
    /**
     * Show alert/toast message
     */
    showAlert: function(message) {
        // Try SweetAlert2 first
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date',
                text: message,
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Try Toastr
        if (typeof toastr !== 'undefined') {
            toastr.warning(message, 'Invalid Date');
            return;
        }
        
        // Fallback to alert
        alert('Invalid Date: ' + message);
    },
    
    /**
     * Get formatted date range info
     */
    getDateRangeInfo: function(transactionType) {
        return this.fetchDateRange(transactionType).then(function(range) {
            if (range) {
                return 'Allowed dates: ' + range.min_date_formatted + ' to ' + range.max_date_formatted;
            }
            return '';
        });
    }
};

// Auto-initialize for elements with data-txn-date-type attribute
$(document).ready(function() {
    $('[data-txn-date-type]').each(function() {
        const $el = $(this);
        const type = $el.data('txn-date-type');
        const excludeId = $el.data('txn-exclude-id') || null;
        TransactionDateValidator.init(type, $el, excludeId);
    });
    
    // Auto-detect transaction type from URL and initialize common date fields
    TransactionDateValidator.autoInit();
});

/**
 * Auto-initialize based on URL pattern detection
 * Maps URL patterns to transaction types and date field IDs
 */
TransactionDateValidator.autoInit = function() {
    const self = this;
    const path = window.location.pathname.toLowerCase();
    
    // URL pattern to transaction type and date field mapping
    const urlMappings = [
        // Sales
        { pattern: /\/sale\/transaction/, type: 'sale', dateFields: ['#saleDate', '#sale_date', 'input[name="date"]'] },
        { pattern: /\/sale\/modification/, type: 'sale', dateFields: ['#saleDate', '#sale_date', 'input[name="date"]'] },
        { pattern: /\/sale-return\/transaction/, type: 'sale_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/sale-return\/modification/, type: 'sale_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/sale-challan\/transaction/, type: 'sale_challan', dateFields: ['#challanDate', '#challan_date', 'input[name="challan_date"]'] },
        { pattern: /\/sale-challan\/modification/, type: 'sale_challan', dateFields: ['#challanDate', '#challan_date', 'input[name="challan_date"]'] },
        { pattern: /\/sale-voucher\/transaction/, type: 'sale_voucher', dateFields: ['#saleDate', 'input[name="sale_date"]'] },
        { pattern: /\/sale-voucher\/modification/, type: 'sale_voucher', dateFields: ['#saleDate', 'input[name="sale_date"]'] },
        { pattern: /\/sale-return-voucher\/transaction/, type: 'sale_return_voucher', dateFields: ['#returnDate', 'input[name="return_date"]'] },
        { pattern: /\/sale-return-voucher\/modification/, type: 'sale_return_voucher', dateFields: ['#returnDate', 'input[name="return_date"]'] },
        { pattern: /\/sale-return-replacement\/transaction/, type: 'sale_return_replacement', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        
        // Purchase
        { pattern: /\/purchase\/transaction/, type: 'purchase', dateFields: ['#billDate', '#bill_date', 'input[name="bill_date"]'] },
        { pattern: /\/purchase\/modification/, type: 'purchase', dateFields: ['#billDate', '#bill_date', 'input[name="bill_date"]'] },
        { pattern: /\/purchase-return\/transaction/, type: 'purchase_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/purchase-return\/modification/, type: 'purchase_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/purchase-challan\/transaction/, type: 'purchase_challan', dateFields: ['#challanDate', '#challan_date', 'input[name="challan_date"]'] },
        { pattern: /\/purchase-challan\/modification/, type: 'purchase_challan', dateFields: ['#challanDate', '#challan_date', 'input[name="challan_date"]'] },
        { pattern: /\/purchase-return-voucher\/transaction/, type: 'purchase_return_voucher', dateFields: ['#returnDate', 'input[name="return_date"]'] },
        { pattern: /\/purchase-return-voucher\/modification/, type: 'purchase_return_voucher', dateFields: ['#returnDate', 'input[name="return_date"]'] },
        
        // Breakage/Expiry
        { pattern: /\/breakage-expiry\/transaction/, type: 'breakage_expiry', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/breakage-expiry\/modification/, type: 'breakage_expiry', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/breakage-supplier/, type: 'breakage_supplier_issued', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/godown-breakage-expiry\/transaction/, type: 'godown_breakage_expiry', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/godown-breakage-expiry\/modification/, type: 'godown_breakage_expiry', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        
        // Receipts & Payments
        { pattern: /\/customer-receipt\/transaction/, type: 'customer_receipt', dateFields: ['#receiptDate', '#receipt_date', 'input[name="receipt_date"]'] },
        { pattern: /\/customer-receipt\/modification/, type: 'customer_receipt', dateFields: ['#receiptDate', '#receipt_date', 'input[name="receipt_date"]'] },
        { pattern: /\/supplier-payment\/transaction/, type: 'supplier_payment', dateFields: ['#paymentDate', '#payment_date', 'input[name="payment_date"]'] },
        { pattern: /\/supplier-payment\/modification/, type: 'supplier_payment', dateFields: ['#paymentDate', '#payment_date', 'input[name="payment_date"]'] },
        { pattern: /\/cheque-return\/transaction/, type: 'cheque_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/cheque-return\/modification/, type: 'cheque_return', dateFields: ['#returnDate', '#return_date', 'input[name="return_date"]'] },
        { pattern: /\/deposit-slip\/transaction/, type: 'deposit_slip', dateFields: ['#depositDate', '#deposit_date', 'input[name="deposit_date"]'] },
        { pattern: /\/deposit-slip\/modification/, type: 'deposit_slip', dateFields: ['#depositDate', '#deposit_date', 'input[name="deposit_date"]'] },
        
        // Vouchers
        { pattern: /\/voucher-entry\/transaction/, type: 'voucher_entry', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/voucher-entry\/modification/, type: 'voucher_entry', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/voucher-purchase\/transaction/, type: 'voucher_purchase', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/voucher-purchase\/modification/, type: 'voucher_purchase', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/voucher-income\/transaction/, type: 'voucher_income', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/voucher-income\/modification/, type: 'voucher_income', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/multi-voucher\/transaction/, type: 'multi_voucher', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/multi-voucher\/modification/, type: 'multi_voucher', dateFields: ['#voucherDate', '#voucher_date', 'input[name="voucher_date"]'] },
        { pattern: /\/bank-transaction\/transaction/, type: 'cash_bank', dateFields: ['#transactionDate', '#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/bank-transaction\/modification/, type: 'cash_bank', dateFields: ['#transactionDate', '#transaction_date', 'input[name="transaction_date"]'] },
        
        // Notes
        { pattern: /\/credit-note\/transaction/, type: 'credit_note', dateFields: ['#noteDate', '#note_date', 'input[name="note_date"]'] },
        { pattern: /\/credit-note\/modification/, type: 'credit_note', dateFields: ['#noteDate', '#note_date', 'input[name="note_date"]'] },
        { pattern: /\/debit-note\/transaction/, type: 'debit_note', dateFields: ['#noteDate', '#note_date', 'input[name="note_date"]'] },
        { pattern: /\/debit-note\/modification/, type: 'debit_note', dateFields: ['#noteDate', '#note_date', 'input[name="note_date"]'] },
        { pattern: /\/replacement-note\/transaction/, type: 'replacement_note', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/replacement-note\/modification/, type: 'replacement_note', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/replacement-received\/transaction/, type: 'replacement_received', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/replacement-received\/modification/, type: 'replacement_received', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        
        // Stock
        { pattern: /\/stock-adjustment\/transaction/, type: 'stock_adjustment', dateFields: ['#adjustmentDate', '#adjustment_date', 'input[name="adjustment_date"]'] },
        { pattern: /\/stock-adjustment\/modification/, type: 'stock_adjustment', dateFields: ['#adjustmentDate', '#adjustment_date', 'input[name="adjustment_date"]'] },
        { pattern: /\/stock-transfer-outgoing\/transaction/, type: 'stock_transfer_outgoing', dateFields: ['#transaction_date', 'input[name="transaction_date"]', 'input[name="transfer_date"]'] },
        { pattern: /\/stock-transfer-outgoing\/modification/, type: 'stock_transfer_outgoing', dateFields: ['#transaction_date', 'input[name="transaction_date"]', 'input[name="transfer_date"]'] },
        { pattern: /\/stock-transfer-outgoing-return\/transaction/, type: 'stock_transfer_outgoing_return', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/stock-transfer-outgoing-return\/modification/, type: 'stock_transfer_outgoing_return', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/stock-transfer-incoming\/transaction/, type: 'stock_transfer_incoming', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/stock-transfer-incoming\/modification/, type: 'stock_transfer_incoming', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/stock-transfer-incoming-return\/transaction/, type: 'stock_transfer_incoming_return', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/stock-transfer-incoming-return\/modification/, type: 'stock_transfer_incoming_return', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        
        // Samples
        { pattern: /\/sample-issued\/transaction/, type: 'sample_issued', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/sample-issued\/modification/, type: 'sample_issued', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/sample-received\/transaction/, type: 'sample_received', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        { pattern: /\/sample-received\/modification/, type: 'sample_received', dateFields: ['#transaction_date', 'input[name="transaction_date"]'] },
        
        // Others
        { pattern: /\/quotation\/transaction/, type: 'quotation', dateFields: ['#quotationDate', '#quotation_date', 'input[name="quotation_date"]'] },
        { pattern: /\/quotation\/modification/, type: 'quotation', dateFields: ['#quotationDate', '#quotation_date', 'input[name="quotation_date"]'] },
        { pattern: /\/claim-to-supplier\/transaction/, type: 'claim_to_supplier', dateFields: ['#claimDate', '#claim_date', 'input[name="claim_date"]'] },
        { pattern: /\/claim-to-supplier\/modification/, type: 'claim_to_supplier', dateFields: ['#claimDate', '#claim_date', 'input[name="claim_date"]'] },
    ];
    
    // Find matching URL pattern
    for (const mapping of urlMappings) {
        if (mapping.pattern.test(path)) {
            console.log('TransactionDateValidator: Auto-detected type:', mapping.type, 'for path:', path);
            
            // Try each date field selector until one is found
            for (const selector of mapping.dateFields) {
                const $field = $(selector);
                if ($field.length && !$field.data('txn-type')) {
                    console.log('TransactionDateValidator: Initializing field:', selector);
                    self.init(mapping.type, selector);
                    break; // Only initialize the first found field
                }
            }
            break; // Only match first URL pattern
        }
    }
};
