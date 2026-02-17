@extends('layouts.admin')

@section('title', 'Purchase Return Br.Expiry Adjustment - Modification')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .readonly-field { background-color: #e9ecef !important; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .claim-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    
    .hsn-table { font-size: 11px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .hsn-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 4px 3px; border: 1px solid #5a3a1a; }
    .hsn-table td { padding: 3px; border: 1px solid #ccc; background: #fffacd; }
    .hsn-table input { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; }
    .hsn-table .row-selected td { background: #cce5ff !important; }
    
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    
    .modal-backdrop-custom { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 700px; background: #fff; border-radius: 6px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1055; }
    .custom-modal.show { display: block; }
    .modal-header-custom { padding: 10px 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .invoice-row:hover { background: #e3f2fd !important; cursor: pointer; }
    
    .adjustment-section { background: #e8f5e9; border: 1px solid #a5d6a7; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .adjustment-table { font-size: 11px; }
    .adjustment-table th { background: #6f42c1; color: #fff; }
    .btn-purple { background: #6f42c1; color: #fff; border: none; }
    .btn-purple:hover { background: #5a32a3; color: #fff; }
    .adjust-input { font-size: 11px; padding: 2px 4px; height: 24px; }
    .adjust-input.is-invalid { border-color: #dc3545; background: #fff0f0; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-pencil-square me-1"></i> Purchase Return Br.Expiry Adjustment - Modification</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-purple btn-sm py-0" onclick="openAdjustmentModal()" id="adjustBtn" style="display:none;">
                <i class="bi bi-credit-card me-1"></i> View Adjustment
            </button>
            <button type="button" class="btn btn-info btn-sm py-0" onclick="showInvoiceModal()">
                <i class="bi bi-folder-open me-1"></i> Load Invoice
            </button>
            <a href="{{ route('admin.breakage-supplier.received-transaction') }}" class="btn btn-success btn-sm py-0">
                <i class="bi bi-plus-circle me-1"></i> New
            </a>
        </div>
    </div>

    <form id="modifyForm" autocomplete="off">
        @csrf
        <input type="hidden" id="transaction_id" name="transaction_id" value="">
        <input type="hidden" id="supplier_id" name="supplier_id" value="">
        <input type="hidden" id="claim_transaction_id" name="claim_transaction_id" value="">
        
        <!-- Header Section -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group">
                        <label style="width:35px;">Date</label>
                        <input type="date" id="transaction_date" name="transaction_date" class="form-control">
                    </div>
                    <div class="field-group">
                        <label style="width:35px;">Trn.No.</label>
                        <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" readonly style="width:80px;">
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="inner-card">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width:55px;">Supplier</label>
                                    <input type="text" id="supplier_name" name="supplier_name" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width:70px;">Party Trn No.</label>
                                    <input type="text" id="party_trn_no" name="party_trn_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width:55px;">Party Date</label>
                                    <input type="date" id="party_date" name="party_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Section -->
        <div class="claim-section">
            <div class="inner-card">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:65px;">O/S.Amount</label>
                            <input type="number" id="os_amount" name="os_amount" class="form-control readonly-field text-end" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:70px;">Claim [Y/N]</label>
                            <input type="text" id="claim_flag" name="claim_flag" class="form-control text-center" value="Y" maxlength="1" style="width:30px;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="received_as_debit_note" name="received_as_debit_note">
                            <label class="form-check-label" for="received_as_debit_note">Received as Debit Note</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:75px;">Claim Amount</label>
                            <input type="number" id="claim_amount" name="claim_amount" class="form-control text-end" value="0.00" step="0.01" onchange="calculateTotals()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="field-group">
                            <label style="width:55px; color:red;">Claim Trn.</label>
                            <input type="text" id="claim_trn_no" name="claim_trn_no" class="form-control readonly-field" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HSN Table Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <div class="d-flex justify-content-between mb-2">
                <button type="button" class="btn btn-info btn-sm" onclick="showHsnModal()">
                    <i class="bi bi-plus-circle me-1"></i> Add HSN Code
                </button>
                <div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedRow()">Delete Row</button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="clearAllRows()">Clear All</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="hsn-table" id="hsnTable">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width:120px;">HSN Code</th>
                            <th style="width:100px;">Amount</th>
                            <th style="width:80px;">GST%</th>
                            <th style="width:80px;">IGST %</th>
                            <th style="width:100px;">GST Amount</th>
                            <th style="width:60px;">Qty.</th>
                        </tr>
                    </thead>
                    <tbody id="hsnTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Adjustment Section (Hidden - shown only when adjustments exist) -->
        <div id="adjustmentSection" style="display:none;"></div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <label>Gross Amt.</label>
                    <input type="number" id="gross_amt" name="gross_amt" class="form-control readonly-field text-end" value="0.00" readonly>
                </div>
                <div class="col-md-2">
                    <label>Total GST</label>
                    <input type="number" id="total_gst" name="total_gst" class="form-control readonly-field text-end" value="0.00" readonly>
                </div>
                <div class="col-md-2">
                    <label>Net Amt.</label>
                    <input type="number" id="net_amt" name="net_amt" class="form-control readonly-field text-end fw-bold" value="0.00" readonly>
                </div>
                <div class="col-md-2">
                    <label>Round Off</label>
                    <input type="number" id="round_off" name="round_off" class="form-control text-end" value="0.00" step="0.01" onchange="calculateTotals()">
                </div>
                <div class="col-md-2">
                    <label>Amount</label>
                    <input type="number" id="final_amount" name="final_amount" class="form-control readonly-field text-end fw-bold text-success" value="0.00" readonly>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-8">
                    <label>Remarks</label>
                    <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Enter remarks...">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-primary btn-sm" onclick="updateTransaction()" id="updateBtn" disabled>
                <i class="bi bi-save me-1"></i> Update
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteTransaction()" id="deleteBtn" disabled>
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelTransaction()">
                <i class="bi bi-x-lg me-1"></i> Close
            </button>
        </div>
    </form>
</div>

<!-- Load Invoice Modal -->
<div class="modal-backdrop-custom" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
<div class="custom-modal" id="invoiceModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-folder-open me-1"></i> Load Invoice</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeInvoiceModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="invoiceSearchInput" class="form-control form-control-sm mb-2" placeholder="Search..." onkeyup="filterInvoices()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Trn No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody id="invoicesListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
    </div>
</div>

<!-- HSN Code Modal -->
<div class="modal-backdrop-custom" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="custom-modal" id="hsnModal">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
        <h6 class="mb-0"><i class="bi bi-grid me-1"></i> Select HSN Code</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeHsnModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="hsnSearchInput" class="form-control form-control-sm mb-2" placeholder="Search HSN..." onkeyup="filterHsnCodes()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-success sticky-top">
                    <tr>
                        <th>HSN Code</th>
                        <th>Name</th>
                        <th class="text-end">CGST%</th>
                        <th class="text-end">SGST%</th>
                        <th class="text-end">Total GST%</th>
                    </tr>
                </thead>
                <tbody id="hsnListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeHsnModal()">Close</button>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="modal-backdrop-custom" id="adjustBackdrop"></div>
<div class="custom-modal" id="adjustModal" style="max-width: 850px;">
    <div class="modal-header-custom" style="background: #6f42c1;">
        <h6 class="mb-0"><i class="bi bi-credit-card me-1"></i> Purchase Adjustment</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeAdjustModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>SR.</th>
                        <th>TRANS NO.</th>
                        <th>DATE</th>
                        <th class="text-end">BILL AMT.</th>
                        <th class="text-end">BALANCE</th>
                        <th class="text-end">ADJUST AMT.</th>
                    </tr>
                </thead>
                <tbody id="adjustTableBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom" style="background: #f0f0f0;">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <span class="text-muted small">ESC to close</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span>Total: <strong class="text-success" id="adjustTotalDisplay">₹0.00</strong></span>
                <span>Adjusted: <strong class="text-primary" id="adjustedDisplay">₹0.00</strong></span>
                <span>Balance: <strong class="text-danger" id="adjustBalanceDisplay">₹0.00</strong></span>
            </div>
            <div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustModal()">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allInvoices = [];
let allHsnCodes = [];
let hsnRowIndex = 0;
let selectedHsnRowIndex = null;
let currentTransactionId = null;
let currentSupplierId = null;
let currentAdjustments = [];
let currentFinalAmount = 0;

document.addEventListener('DOMContentLoaded', function() {
    loadInvoices();
    loadHsnCodes();
    
    // Global ESC key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeInvoiceModal(); closeHsnModal(); closeAdjustModal(); }
    });
    
    // ==========================================
    // PHASE 1: AUTO-FOCUS DATE FIELD ON PAGE LOAD
    // ==========================================
    setTimeout(function() {
        const dateField = document.getElementById('transaction_date');
        if (dateField) {
            dateField.focus();
            dateField.select();
        }
    }, 100);
    
    // ==========================================
    // PHASE 2: ENTER KEY SEQUENTIAL NAVIGATION
    // Date → Party Trn No → Party Date → Claim [Y/N] → Received as Debit Note → Claim Amount → Load Invoice
    // ==========================================
    const enterSequence = [
        'transaction_date',
        'party_trn_no',
        'party_date',
        'claim_flag',
        'received_as_debit_note',
        'claim_amount'
    ];
    
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (e.ctrlKey || e.altKey) return;
        
        // Skip if any modal is open
        const invoiceModal = document.getElementById('invoiceModal');
        const hsnModal = document.getElementById('hsnModal');
        const adjustModal = document.getElementById('adjustModal');
        if (invoiceModal.classList.contains('show') || 
            hsnModal.classList.contains('show') || 
            adjustModal.classList.contains('show')) return;
        
        const activeEl = document.activeElement;
        if (!activeEl || !activeEl.id) return;
        
        // Remarks → Trigger Update (before sequence check since remarks isn't in sequence)
        if (activeEl.id === 'remarks' && !e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const updateBtn = document.getElementById('updateBtn');
            if (updateBtn && !updateBtn.disabled) {
                updateBtn.click();
            }
            return;
        }
        
        const currentIdx = enterSequence.indexOf(activeEl.id);
        if (currentIdx === -1) return;
        
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Shift+Enter → move to PREVIOUS field
        if (e.shiftKey) {
            if (currentIdx > 0) {
                const prevField = document.getElementById(enterSequence[currentIdx - 1]);
                if (prevField) {
                    prevField.focus();
                    prevField.select();
                }
            }
            return;
        }
        
        // PHASE 3: Claim Amount → Auto Load Invoice
        if (activeEl.id === 'claim_amount') {
            showInvoiceModal();
            return;
        }
        
        // Move to next field in sequence
        if (currentIdx < enterSequence.length - 1) {
            const nextField = document.getElementById(enterSequence[currentIdx + 1]);
            if (nextField) {
                nextField.focus();
                nextField.select();
            }
        }
    }, true); // Capture phase
    
    // ==========================================
    // CTRL+ENTER → JUMP TO ROUND OFF FIELD
    // ==========================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const roundOffField = document.getElementById('round_off');
            if (roundOffField) {
                roundOffField.focus();
                roundOffField.select();
            }
        }
    }, true);
    
    // ==========================================
    // CTRL+S → UPDATE TRANSACTION
    // ==========================================
    let isSaving = false;
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (isSaving) return;
            isSaving = true;
            
            if (typeof updateTransaction === 'function') {
                updateTransaction();
            }
            
            setTimeout(function() { isSaving = false; }, 1000);
        }
    }, true);
    
    // ==========================================
    // PHASE 4: LOAD INVOICE MODAL KEYBOARD CONTROL
    // Arrow navigation, Enter selection, highlighting
    // ==========================================
    let invoiceSelectedIndex = -1;
    
    // Must use capture + stopImmediatePropagation to beat transaction-shortcuts.blade.php
    document.addEventListener('keydown', function(e) {
        const invoiceModal = document.getElementById('invoiceModal');
        if (!invoiceModal || !invoiceModal.classList.contains('show')) return;
        
        const tbody = document.getElementById('invoicesListBody');
        const rows = Array.from(tbody.querySelectorAll('tr.invoice-row'));
        
        // Arrow Down → next row
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Blur search input so arrows don't move text cursor
            const searchInput = document.getElementById('invoiceSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (invoiceSelectedIndex < rows.length - 1) {
                invoiceSelectedIndex++;
            } else if (invoiceSelectedIndex === -1) {
                invoiceSelectedIndex = 0; // Auto-select first row
            }
            highlightInvoiceRow(rows, invoiceSelectedIndex);
            return;
        }
        
        // Arrow Up → previous row
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Blur search input
            const searchInput = document.getElementById('invoiceSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (invoiceSelectedIndex > 0) {
                invoiceSelectedIndex--;
            } else if (invoiceSelectedIndex === -1 && rows.length > 0) {
                invoiceSelectedIndex = 0; // Auto-select first row
            }
            highlightInvoiceRow(rows, invoiceSelectedIndex);
            return;
        }
        
        // Enter → select highlighted row
        if (e.key === 'Enter') {
            // If search input is focused, let Enter re-filter, don't select row
            const searchInput = document.getElementById('invoiceSearchInput');
            if (document.activeElement === searchInput && invoiceSelectedIndex === -1) {
                return; // Let search work normally
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (!rows.length) return;
            
            // If no row selected yet, select first one
            if (invoiceSelectedIndex === -1 && rows.length > 0) {
                invoiceSelectedIndex = 0;
                highlightInvoiceRow(rows, invoiceSelectedIndex);
                return;
            }
            
            if (invoiceSelectedIndex >= 0 && invoiceSelectedIndex < rows.length) {
                rows[invoiceSelectedIndex].click();
            }
            return;
        }
        
        // Any letter/number key → refocus search input for typing
        if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
            const searchInput = document.getElementById('invoiceSearchInput');
            if (searchInput && document.activeElement !== searchInput) {
                searchInput.focus();
                // Reset row selection when user types
                invoiceSelectedIndex = -1;
                highlightInvoiceRow(rows, -1);
            }
        }
    }, true); // Capture phase
    
    function highlightInvoiceRow(rows, index) {
        // Remove all highlights
        rows.forEach(row => {
            row.style.backgroundColor = '';
            row.style.fontWeight = '';
            row.classList.remove('table-active');
        });
        // Add highlight to selected row
        if (index >= 0 && index < rows.length) {
            rows[index].style.backgroundColor = '#cce5ff';
            rows[index].style.fontWeight = 'bold';
            rows[index].classList.add('table-active');
            rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    
    // Reset invoice selection when modal opens/closes
    const invoiceModalEl = document.getElementById('invoiceModal');
    if (invoiceModalEl) {
        const invoiceObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (invoiceModalEl.classList.contains('show')) {
                        // Modal opened - reset selection and focus search
                        invoiceSelectedIndex = -1;
                        setTimeout(function() {
                            const searchInput = document.getElementById('invoiceSearchInput');
                            if (searchInput) searchInput.focus();
                        }, 150);
                    } else {
                        // Modal closed - reset selection
                        invoiceSelectedIndex = -1;
                    }
                }
            });
        });
        invoiceObserver.observe(invoiceModalEl, { attributes: true, attributeFilter: ['class'] });
    }
    
    // ==========================================
    // PURCHASE ADJUSTMENT MODAL KEYBOARD CONTROL
    // Left/Right for Save/Cancel, Up/Down for inputs
    // ==========================================
    const adjustModalEl = document.getElementById('adjustModal');
    if (adjustModalEl) {
        // Document-level: Left/Right Arrow + Enter on buttons
        document.addEventListener('keydown', function(e) {
            if (!adjustModalEl.classList.contains('show')) return;
            
            // Skip if Invoice modal is also open
            const invoiceModal = document.getElementById('invoiceModal');
            if (invoiceModal && invoiceModal.classList.contains('show')) return;
            
            const saveBtn = adjustModalEl.querySelector('button.btn-success');
            const cancelBtn = adjustModalEl.querySelector('button.btn-secondary');
            
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (saveBtn) saveBtn.focus();
                return;
            }
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (cancelBtn) cancelBtn.focus();
                return;
            }
            if (e.target.tagName === 'BUTTON' && adjustModalEl.contains(e.target) && e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                e.target.click();
                return;
            }
        }, true);
        
        // Modal-level: Up/Down/Enter for adjust-inputs
        adjustModalEl.addEventListener('keydown', function(e) {
            const isAdjustInput = e.target.classList.contains('adjust-input');
            if (!isAdjustInput) return;
            
            const inputs = Array.from(document.querySelectorAll('.adjust-input'));
            const currentIndex = inputs.indexOf(e.target);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                    inputs[currentIndex + 1].select();
                }
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex > 0) {
                    inputs[currentIndex - 1].focus();
                    inputs[currentIndex - 1].select();
                }
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                    inputs[currentIndex + 1].select();
                } else {
                    const closeBtn = adjustModalEl.querySelector('button.btn-secondary');
                    if (closeBtn) closeBtn.focus();
                }
                return;
            }
        });
        
        // Auto-focus when modal opens
        const adjustObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (adjustModalEl.classList.contains('show')) {
                        setTimeout(function() {
                            const firstInput = document.querySelector('.adjust-input');
                            if (firstInput) {
                                firstInput.focus();
                                firstInput.select();
                            } else {
                                const closeBtn = adjustModalEl.querySelector('button.btn-secondary');
                                if (closeBtn) closeBtn.focus();
                            }
                        }, 150);
                    }
                }
            });
        });
        adjustObserver.observe(adjustModalEl, { attributes: true, attributeFilter: ['class'] });
    }
    
    // ==========================================
    // HSN TABLE ENTER KEY NAVIGATION
    // Amount → Qty → next row Amount → ... → Round Off
    // ==========================================
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (e.ctrlKey || e.altKey) return;
        
        const activeEl = document.activeElement;
        if (!activeEl) return;
        
        // Only handle editable HSN table inputs
        if (!activeEl.classList.contains('hsn-editable')) return;
        if (!activeEl.closest('#hsnTableBody')) return;
        
        // Skip if any modal is open
        const invoiceModal = document.getElementById('invoiceModal');
        const hsnModal = document.getElementById('hsnModal');
        const adjustModal = document.getElementById('adjustModal');
        if ((invoiceModal && invoiceModal.classList.contains('show')) || 
            (hsnModal && hsnModal.classList.contains('show')) || 
            (adjustModal && adjustModal.classList.contains('show'))) return;
        
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Get all editable inputs in HSN table (Amount, Qty for each row)
        const allEditable = Array.from(document.querySelectorAll('#hsnTableBody .hsn-editable'));
        const currentIndex = allEditable.indexOf(activeEl);
        
        // Shift+Enter → move to PREVIOUS editable field
        if (e.shiftKey) {
            if (currentIndex > 0) {
                allEditable[currentIndex - 1].focus();
                allEditable[currentIndex - 1].select();
            } else {
                // First Amount field → go back to Claim Amount
                const claimAmount = document.getElementById('claim_amount');
                if (claimAmount) {
                    claimAmount.focus();
                    claimAmount.select();
                }
            }
            return;
        }
        
        // Enter → move to NEXT editable field
        if (currentIndex >= 0 && currentIndex < allEditable.length - 1) {
            allEditable[currentIndex + 1].focus();
            allEditable[currentIndex + 1].select();
        } else {
            // Last Qty field → directly open "Add HSN Code" modal
            showHsnModal();
        }
    }, true); // Capture phase
    
    // ==========================================
    // SELECT HSN CODE MODAL KEYBOARD CONTROL
    // Arrow navigation, Enter selection, highlighting
    // ==========================================
    let hsnSelectedIndex = -1;
    
    document.addEventListener('keydown', function(e) {
        const hsnModal = document.getElementById('hsnModal');
        if (!hsnModal || !hsnModal.classList.contains('show')) return;
        
        const tbody = document.getElementById('hsnListBody');
        const rows = Array.from(tbody.querySelectorAll('tr.invoice-row'));
        
        // Arrow Down → next row
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Blur search input so arrows don't move text cursor
            const searchInput = document.getElementById('hsnSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (hsnSelectedIndex < rows.length - 1) {
                hsnSelectedIndex++;
            } else if (hsnSelectedIndex === -1) {
                hsnSelectedIndex = 0;
            }
            highlightHsnRow(rows, hsnSelectedIndex);
            return;
        }
        
        // Arrow Up → previous row
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const searchInput = document.getElementById('hsnSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (hsnSelectedIndex > 0) {
                hsnSelectedIndex--;
            } else if (hsnSelectedIndex === -1 && rows.length > 0) {
                hsnSelectedIndex = 0;
            }
            highlightHsnRow(rows, hsnSelectedIndex);
            return;
        }
        
        // Enter → select highlighted row
        if (e.key === 'Enter') {
            const searchInput = document.getElementById('hsnSearchInput');
            if (document.activeElement === searchInput && hsnSelectedIndex === -1) {
                return; // Let search work normally
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (!rows.length) return;
            
            if (hsnSelectedIndex === -1 && rows.length > 0) {
                hsnSelectedIndex = 0;
                highlightHsnRow(rows, hsnSelectedIndex);
                return;
            }
            
            if (hsnSelectedIndex >= 0 && hsnSelectedIndex < rows.length) {
                rows[hsnSelectedIndex].click();
            }
            return;
        }
        
        // Any letter/number key → refocus search input for typing
        if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
            const searchInput = document.getElementById('hsnSearchInput');
            if (searchInput && document.activeElement !== searchInput) {
                searchInput.focus();
                hsnSelectedIndex = -1;
                highlightHsnRow(rows, -1);
            }
        }
    }, true);
    
    function highlightHsnRow(rows, index) {
        rows.forEach(row => {
            row.style.backgroundColor = '';
            row.style.fontWeight = '';
            row.classList.remove('table-active');
        });
        if (index >= 0 && index < rows.length) {
            rows[index].style.backgroundColor = '#c3e6cb';
            rows[index].style.fontWeight = 'bold';
            rows[index].classList.add('table-active');
            rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    
    // Reset HSN selection when modal opens/closes
    const hsnModalEl = document.getElementById('hsnModal');
    if (hsnModalEl) {
        const hsnObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (hsnModalEl.classList.contains('show')) {
                        hsnSelectedIndex = -1;
                        setTimeout(function() {
                            const searchInput = document.getElementById('hsnSearchInput');
                            if (searchInput) searchInput.focus();
                        }, 150);
                    } else {
                        hsnSelectedIndex = -1;
                    }
                }
            });
        });
        hsnObserver.observe(hsnModalEl, { attributes: true, attributeFilter: ['class'] });
    }
});

// Format date for HTML date input (YYYY-MM-DD)
function formatDateForInput(dateStr) {
    if (!dateStr) return '';
    // Handle various date formats
    if (dateStr.includes('T')) {
        return dateStr.split('T')[0];
    }
    if (dateStr.includes(' ')) {
        return dateStr.split(' ')[0];
    }
    return dateStr;
}

function loadInvoices() {
    fetch('{{ route("admin.breakage-supplier.get-received-past-invoices") }}')
        .then(r => r.json())
        .then(data => {
            allInvoices = data.success ? data.invoices : (Array.isArray(data) ? data : []);
            renderInvoicesList(allInvoices);
        })
        .catch(e => console.error('Error:', e));
}

function showInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.add('show');
    document.getElementById('invoiceModal').classList.add('show');
    document.getElementById('invoiceSearchInput').value = '';
    renderInvoicesList(allInvoices);
}

function closeInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.remove('show');
    document.getElementById('invoiceModal').classList.remove('show');
}

function filterInvoices() {
    const search = document.getElementById('invoiceSearchInput').value.toLowerCase();
    const filtered = allInvoices.filter(inv => 
        String(inv.trn_no || '').toLowerCase().includes(search) ||
        (inv.supplier_name || '').toLowerCase().includes(search)
    );
    renderInvoicesList(filtered);
}

function renderInvoicesList(invoices) {
    const tbody = document.getElementById('invoicesListBody');
    if (!invoices || !invoices.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No invoices found</td></tr>';
        return;
    }
    tbody.innerHTML = invoices.map(inv => `
        <tr class="invoice-row" onclick="selectInvoice(${inv.id})" style="cursor:pointer;">
            <td><strong>${inv.trn_no || inv.id}</strong></td>
            <td>${inv.transaction_date || ''}</td>
            <td>${inv.supplier_name || ''}</td>
            <td class="text-end">₹${parseFloat(inv.final_amount || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectInvoice(id) {
    closeInvoiceModal();
    fetch(`{{ url('admin/breakage-supplier/received-details') }}/${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) populateForm(data.transaction);
            else alert('Error: ' + (data.message || 'Unknown error'));
        })
        .catch(e => alert('Error loading invoice'));
}

function populateForm(data) {
    currentTransactionId = data.id;
    currentSupplierId = data.supplier_id;
    currentAdjustments = data.adjustments || [];
    currentFinalAmount = parseFloat(data.final_amount || 0);
    
    document.getElementById('transaction_id').value = data.id;
    document.getElementById('trn_no').value = data.trn_no || data.id;
    document.getElementById('transaction_date').value = formatDateForInput(data.transaction_date);
    document.getElementById('supplier_id').value = data.supplier_id || '';
    document.getElementById('supplier_name').value = data.supplier_name || '';
    document.getElementById('party_trn_no').value = data.party_trn_no || '';
    document.getElementById('party_date').value = formatDateForInput(data.party_date);
    document.getElementById('claim_transaction_id').value = data.claim_transaction_id || '';
    document.getElementById('claim_trn_no').value = data.claim_trn_no || '';
    document.getElementById('os_amount').value = parseFloat(data.os_amount || 0).toFixed(2);
    document.getElementById('claim_flag').value = data.claim_flag || 'Y';
    document.getElementById('received_as_debit_note').checked = !!data.received_as_debit_note;
    document.getElementById('claim_amount').value = parseFloat(data.claim_amount || 0).toFixed(2);
    document.getElementById('gross_amt').value = parseFloat(data.gross_amt || 0).toFixed(2);
    document.getElementById('total_gst').value = parseFloat(data.total_gst || 0).toFixed(2);
    document.getElementById('net_amt').value = parseFloat(data.net_amt || 0).toFixed(2);
    document.getElementById('round_off').value = parseFloat(data.round_off || 0).toFixed(2);
    document.getElementById('final_amount').value = parseFloat(data.final_amount || 0).toFixed(2);
    document.getElementById('remarks').value = data.remarks || '';
    
    // Load HSN items
    clearAllRows();
    if (data.items && data.items.length > 0) {
        data.items.forEach(item => addHSNRowWithData(item));
    }
    
    // Load adjustments
    renderAdjustmentSection(data.adjustments || []);
    
    document.getElementById('updateBtn').disabled = false;
    document.getElementById('deleteBtn').disabled = false;
    
    // Auto-focus first Amount field in HSN table after invoice loads
    setTimeout(function() {
        const firstAmountInput = document.querySelector('#hsnTableBody input[name*="[amount]"]');
        if (firstAmountInput) {
            firstAmountInput.focus();
            firstAmountInput.select();
        }
    }, 200);
}

function renderAdjustmentSection(adjustments) {
    // Just store adjustments, don't render section - show via modal only
    if (adjustments && adjustments.length > 0) {
        document.getElementById('adjustBtn').style.display = 'inline-block';
    } else {
        document.getElementById('adjustBtn').style.display = 'none';
    }
}

// Adjustment Modal Functions
function openAdjustmentModal() {
    if (!currentSupplierId) {
        alert('Please load a transaction first');
        return;
    }
    
    const url = `{{ url('admin/breakage-supplier/supplier-purchases') }}/${currentSupplierId}`;
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderAdjustTable(data.purchases, currentFinalAmount, currentAdjustments);
                document.getElementById('adjustBackdrop').classList.add('show');
                document.getElementById('adjustModal').classList.add('show');
            } else {
                alert('No purchases found for this supplier');
            }
        })
        .catch(e => {
            console.error('Error:', e);
            alert('Error loading purchases');
        });
}

function renderAdjustTable(purchases, totalAmount, existingAdjustments) {
    const tbody = document.getElementById('adjustTableBody');
    
    // Create a map of existing adjustments for quick lookup
    const adjMap = {};
    existingAdjustments.forEach(adj => {
        adjMap[adj.purchase_transaction_id] = parseFloat(adj.adjusted_amount || 0);
    });
    
    if (!purchases.length && existingAdjustments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No purchases found for this supplier</td></tr>';
    } else {
        // Merge purchases with existing adjustments
        // Add adjusted purchases that might have 0 balance now
        const allPurchaseIds = new Set([
            ...purchases.map(p => p.id),
            ...existingAdjustments.map(a => a.purchase_transaction_id)
        ]);
        
        tbody.innerHTML = purchases.map((p, i) => {
            const existingAdj = adjMap[p.id] || 0;
            const displayBalance = parseFloat(p.balance_amount || 0) + existingAdj; // Add back the existing adjustment to show original available
            
            return `
            <tr data-purchase-id="${p.id}" data-original-balance="${displayBalance}">
                <td class="text-center">${i + 1}</td>
                <td><strong>${p.purchase_no || ''}</strong></td>
                <td>${p.purchase_date || ''}</td>
                <td class="text-end">₹${parseFloat(p.total_amount || 0).toFixed(2)}</td>
                <td class="text-end text-primary fw-bold balance-cell" data-original="${displayBalance}">${(displayBalance - existingAdj).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end adjust-input" 
                           data-purchase-id="${p.id}" data-max="${displayBalance}"
                           step="0.01" min="0" value="${existingAdj.toFixed(2)}" oninput="updateRowBalance(this)" onchange="updateRowBalance(this)">
                </td>
            </tr>
        `}).join('');
    }
    
    document.getElementById('adjustTotalDisplay').textContent = '₹' + totalAmount.toFixed(2);
    updateAdjustTotals();
}

function updateRowBalance(input) {
    const row = input.closest('tr');
    const balanceCell = row.querySelector('.balance-cell');
    const originalBalance = parseFloat(input.dataset.max) || 0;
    const adjustAmount = parseFloat(input.value) || 0;
    
    if (adjustAmount > originalBalance) {
        input.value = originalBalance;
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
    }
    
    const newBalance = originalBalance - Math.min(adjustAmount, originalBalance);
    balanceCell.textContent = newBalance.toFixed(2);
    
    updateAdjustTotals();
}

function updateAdjustTotals() {
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjust-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const max = parseFloat(input.dataset.max) || 0;
        totalAdjusted += Math.min(amount, max);
    });
    
    const balance = currentFinalAmount - totalAdjusted;
    
    document.getElementById('adjustedDisplay').textContent = '₹' + totalAdjusted.toFixed(2);
    document.getElementById('adjustBalanceDisplay').textContent = '₹' + balance.toFixed(2);
}

function closeAdjustModal() {
    document.getElementById('adjustBackdrop').classList.remove('show');
    document.getElementById('adjustModal').classList.remove('show');
}

// HSN Functions
function loadHsnCodes() {
    fetch('{{ route("admin.sale-voucher.hsn-codes") }}')
        .then(r => r.json())
        .then(data => { if (data.success) allHsnCodes = data.hsn_codes; })
        .catch(e => console.error('Error:', e));
}

function showHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
    renderHsnList(allHsnCodes);
}

function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
}

function filterHsnCodes() {
    const search = document.getElementById('hsnSearchInput').value.toLowerCase();
    renderHsnList(allHsnCodes.filter(h => 
        (h.hsn_code || '').toLowerCase().includes(search) || (h.name || '').toLowerCase().includes(search)
    ));
}

function renderHsnList(codes) {
    const tbody = document.getElementById('hsnListBody');
    if (!codes || !codes.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No HSN codes</td></tr>';
        return;
    }
    tbody.innerHTML = codes.map(h => `
        <tr class="invoice-row" onclick="selectHsnCode(${h.id})" style="cursor:pointer;">
            <td><strong>${h.hsn_code || ''}</strong></td>
            <td>${h.name || ''}</td>
            <td class="text-end">${parseFloat(h.cgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.sgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.total_gst_percent || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectHsnCode(id) {
    const hsn = allHsnCodes.find(h => h.id === id);
    if (!hsn) return;
    closeHsnModal();
    addHSNRowWithData({ hsn_code: hsn.hsn_code, amount: 0, gst_percent: hsn.total_gst_percent || 0, igst_percent: 0, qty: 1 });
}

function addHSNRowWithData(data) {
    const tbody = document.getElementById('hsnTableBody');
    const idx = hsnRowIndex++;
    const gstAmt = (parseFloat(data.amount || 0) * parseFloat(data.gst_percent || 0) / 100).toFixed(2);
    
    const tr = document.createElement('tr');
    tr.id = `hsn_row_${idx}`;
    tr.onclick = function() { selectHsnRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="hsn[${idx}][hsn_code]" class="form-control readonly-field" value="${data.hsn_code || ''}" readonly></td>
        <td><input type="number" name="hsn[${idx}][amount]" class="form-control text-end hsn-editable" value="${parseFloat(data.amount || 0).toFixed(2)}" onchange="calculateRowGST(${idx}); calculateTotals();"></td>
        <td><input type="number" name="hsn[${idx}][gst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.gst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][igst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.igst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][gst_amount]" class="form-control text-end readonly-field" value="${data.gst_amount || gstAmt}" readonly></td>
        <td><input type="number" name="hsn[${idx}][qty]" class="form-control text-end hsn-editable" value="${parseInt(data.qty || 1)}"></td>
    `;
    tbody.appendChild(tr);
    selectHsnRow(idx);
    
    // Auto-focus the Amount field of the new row
    setTimeout(function() {
        const amountInput = tr.querySelector('input[name*="[amount]"]');
        if (amountInput) {
            amountInput.focus();
            amountInput.select();
        }
    }, 100);
}

function selectHsnRow(idx) {
    document.querySelectorAll('#hsnTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`hsn_row_${idx}`);
    if (row) { row.classList.add('row-selected'); selectedHsnRowIndex = idx; }
}

function calculateRowGST(idx) {
    const row = document.getElementById(`hsn_row_${idx}`);
    if (!row) return;
    const amt = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
    const gst = parseFloat(row.querySelector('input[name*="[gst_percent]"]').value) || 0;
    row.querySelector('input[name*="[gst_amount]"]').value = (amt * gst / 100).toFixed(2);
}

function deleteSelectedRow() {
    if (selectedHsnRowIndex !== null) {
        document.getElementById(`hsn_row_${selectedHsnRowIndex}`)?.remove();
        selectedHsnRowIndex = null;
        calculateTotals();
    }
}

function clearAllRows() {
    document.getElementById('hsnTableBody').innerHTML = '';
    hsnRowIndex = 0;
    selectedHsnRowIndex = null;
}

function calculateTotals() {
    let gross = 0, gst = 0;
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        gross += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        gst += parseFloat(row.querySelector('input[name*="[gst_amount]"]')?.value) || 0;
    });
    if (document.querySelectorAll('#hsnTableBody tr').length === 0) {
        gross = parseFloat(document.getElementById('claim_amount').value) || 0;
    }
    const net = gross + gst;
    const roundOff = parseFloat(document.getElementById('round_off').value) || 0;
    document.getElementById('gross_amt').value = gross.toFixed(2);
    document.getElementById('total_gst').value = gst.toFixed(2);
    document.getElementById('net_amt').value = net.toFixed(2);
    document.getElementById('final_amount').value = (net + roundOff).toFixed(2);
    currentFinalAmount = net + roundOff;
}

function updateTransaction() {
    if (!currentTransactionId) return alert('Please load an invoice first');
    
    const hsnItems = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        hsnItems.push({
            hsn_code: row.querySelector('input[name*="[hsn_code]"]')?.value || '',
            amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0,
            gst_percent: parseFloat(row.querySelector('input[name*="[gst_percent]"]')?.value) || 0,
            igst_percent: parseFloat(row.querySelector('input[name*="[igst_percent]"]')?.value) || 0,
            gst_amount: parseFloat(row.querySelector('input[name*="[gst_amount]"]')?.value) || 0,
            qty: parseInt(row.querySelector('input[name*="[qty]"]')?.value) || 0
        });
    });
    
    fetch(`{{ url('admin/breakage-supplier/update-received') }}/${currentTransactionId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            transaction_date: document.getElementById('transaction_date').value,
            party_trn_no: document.getElementById('party_trn_no').value,
            party_date: document.getElementById('party_date').value,
            claim_flag: document.getElementById('claim_flag').value,
            received_as_debit_note: document.getElementById('received_as_debit_note').checked,
            claim_amount: parseFloat(document.getElementById('claim_amount').value) || 0,
            gross_amt: parseFloat(document.getElementById('gross_amt').value) || 0,
            total_gst: parseFloat(document.getElementById('total_gst').value) || 0,
            net_amt: parseFloat(document.getElementById('net_amt').value) || 0,
            round_off: parseFloat(document.getElementById('round_off').value) || 0,
            final_amount: parseFloat(document.getElementById('final_amount').value) || 0,
            remarks: document.getElementById('remarks').value,
            hsn_items: hsnItems
        })
    })
    .then(r => r.json())
    .then(res => { alert(res.success ? 'Updated successfully!' : 'Error: ' + res.message); if(res.success) loadInvoices(); })
    .catch(e => alert('Error updating'));
}

function deleteTransaction() {
    if (!currentTransactionId) return alert('Please load an invoice first');
    if (!confirm('Delete this transaction?')) return;
    
    fetch(`{{ url('admin/breakage-supplier/delete-received') }}/${currentTransactionId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(res => {
        alert(res.success ? 'Deleted!' : 'Error: ' + res.message);
        if(res.success) { resetForm(); loadInvoices(); }
    })
    .catch(e => alert('Error deleting'));
}

function resetForm() {
    currentTransactionId = null;
    currentSupplierId = null;
    currentAdjustments = [];
    currentFinalAmount = 0;
    document.getElementById('modifyForm').reset();
    clearAllRows();
    document.getElementById('adjustmentSection').style.display = 'none';
    document.getElementById('updateBtn').disabled = true;
    document.getElementById('deleteBtn').disabled = true;
}

function cancelTransaction() {
    window.location.href = '{{ route("admin.breakage-supplier.received-transaction") }}';
}
</script>
@endpush
