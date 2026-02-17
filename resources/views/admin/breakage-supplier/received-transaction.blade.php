@extends('layouts.admin')

@section('title', 'Purchase Return Br.Expiry Adjustment')
@section('disable_select2', '1')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .readonly-field { background-color: #e9ecef !important; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    
    /* Keyboard Navigation Focus Styles */
    input:focus,
    select:focus,
    textarea:focus,
    button:focus {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        outline: none !important;
    }
    
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        outline: none !important;
    }
    
    /* Custom Dropdown Styles */
    .custom-dropdown-wrapper {
        position: relative;
    }
    
    .custom-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    
    .custom-dropdown-menu .dropdown-item {
        padding: 6px 10px;
        cursor: pointer;
        font-size: 11px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.15s ease;
    }
    
    .custom-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5ff;
    }
    
    .custom-dropdown-menu .dropdown-item.active {
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
    }
    
    /* Claim Section */
    .claim-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    
    /* HSN Table Styles - Brown Header (matching issued-transaction) */
    .hsn-table { font-size: 11px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .hsn-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 4px 3px; border: 1px solid #5a3a1a; white-space: nowrap; font-size: 11px; }
    .hsn-table td { padding: 3px; border: 1px solid #ccc; background: #fffacd; }
    .hsn-table input { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; }
    .hsn-table .row-selected td { background: #cce5ff !important; }
    
    /* Summary Section - Pink */
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .summary-section label { font-weight: bold; font-size: 11px; }
    .summary-section input { height: 24px; font-size: 11px; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .action-buttons .btn { min-width: 80px; }
    
    /* Modal Styles */
    .modal-backdrop-custom { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 700px; background: #fff; border-radius: 6px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1055; }
    .custom-modal.show { display: block; }
    .modal-header-custom { padding: 10px 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .claim-row:hover { background: #e3f2fd !important; cursor: pointer; }
    .claim-row.selected { background: #007bff !important; color: #fff !important; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-arrow-return-left me-1"></i> Purchase Return Br.Expiry Adjustment</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-info btn-sm py-0" onclick="showClaimModal()">
                <i class="bi bi-file-earmark-text me-1"></i> Load Claim
            </button>
            <a href="{{ route('admin.breakage-supplier.received-modification') }}" class="btn btn-outline-secondary btn-sm py-0">
                <i class="bi bi-list"></i> Modification
            </a>
        </div>
    </div>

    <form id="receivedForm" autocomplete="off">
        @csrf
        <input type="hidden" id="claim_transaction_id" name="claim_transaction_id" value="">
        
        <!-- Header Section Row 1 -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group">
                        <label style="width:35px;">Date</label>
                        <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field-group">
                        <label style="width:35px;">Trn.No.</label>
                        <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $trnNo ?? '' }}" readonly style="width:80px;">
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="inner-card">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width:55px;">Supplier</label>
                                    <div class="custom-dropdown-wrapper" style="width: 100%; position: relative;">
                                        <input type="text" class="form-control"
                                               id="supplierSearchInput"
                                               placeholder="Type to search supplier..."
                                               autocomplete="off">
                                        <select id="supplier_id" name="supplier_id" class="form-control" style="display: none;">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers ?? [] as $s)
                                            <option value="{{ $s->supplier_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="supplier_name" name="supplier_name" value="">
                                        
                                        <div id="supplierDropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                                            <div class="dropdown-header" style="padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-weight: 600; font-size: 11px;">
                                                Select Supplier
                                            </div>
                                            <div id="supplierList" class="dropdown-list">
                                                @foreach($suppliers ?? [] as $s)
                                                    <div class="dropdown-item"
                                                         data-id="{{ $s->supplier_id }}"
                                                         data-name="{{ $s->name }}"
                                                         style="padding: 6px 10px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #f0f0f0;">
                                                        {{ $s->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
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

        <!-- Claim Section Row 2 -->
        <div class="claim-section">
            <div class="inner-card">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:65px;">O/S.Amount</label>
                            <input type="number" id="os_amount" name="os_amount" class="form-control readonly-field text-end" value="0.00" readonly step="0.01">
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
                            <input type="number" id="claim_amount" name="claim_amount" class="form-control text-end" value="0.00" step="0.01" onchange="calculateFromClaimAmount()">
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
                <button type="button" class="btn btn-info btn-sm" id="btn_add_hsn" onclick="showHsnModal()">
                    <i class="bi bi-plus-circle me-1"></i> Add HSN Code
                </button>
                <div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedRow()">Delete Row</button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="clearAllRows()">Clear All</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="hsn-table" id="hsnTable" style="table-layout: fixed; width: 100%;">
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
                    <tbody id="hsnTableBody">
                        <!-- HSN rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Gross Amt.</label>
                        <input type="number" id="gross_amt" name="gross_amt" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-2">
                    <div class="field-group">
                        <input type="number" id="total_gst_amt" name="total_gst_amt" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Total GST</label>
                        <input type="number" id="total_gst" name="total_gst" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Net Amt.</label>
                        <input type="number" id="net_amt" name="net_amt" class="form-control readonly-field text-end fw-bold" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Round Off</label>
                        <input type="number" id="round_off" name="round_off" class="form-control text-end" value="0.00" step="0.01">
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Amount</label>
                        <input type="number" id="final_amount" name="final_amount" class="form-control readonly-field text-end fw-bold text-success" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-8">
                    <div class="field-group">
                        <label>Remarks</label>
                        <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Enter remarks...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-success btn-sm" onclick="saveTransaction()">
                <i class="bi bi-check-lg me-1"></i> Ok
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelTransaction()">
                <i class="bi bi-x-lg me-1"></i> Close
            </button>
        </div>
    </form>
</div>

<!-- Load Claim Modal -->
<div class="modal-backdrop-custom" id="claimModalBackdrop" onclick="closeClaimModal()"></div>
<div class="custom-modal" id="claimModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-1"></i> Load Claim to Supplier Transaction</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeClaimModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="claimSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by Claim No or Supplier..." onkeyup="filterClaims()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Claim No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody id="claimsListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeClaimModal()">Close</button>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="modal-backdrop-custom" id="adjustBackdrop"></div>
<div class="custom-modal" id="adjustModal" style="max-width: 800px;">
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
                <button type="button" class="btn btn-success btn-sm" onclick="saveWithAdjustments()">
                    <i class="bi bi-check-circle me-1"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- HSN Code Selection Modal -->
<div class="modal-backdrop-custom" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="custom-modal" id="hsnModal">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
        <h6 class="mb-0"><i class="bi bi-grid me-1"></i> Select HSN Code</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeHsnModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="hsnSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by HSN Code or Name...">
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

@endsection

@push('scripts')
<script>
let allClaims = [];
let allHsnCodes = [];
let hsnRowIndex = 0;
let selectedHsnRowIndex = null;
let pendingTransactionData = null;
let hsnModalSelectedIndex = -1; // For arrow key navigation in HSN modal

document.addEventListener('DOMContentLoaded', function() {
    // Disable Select2 on this page
    if (window.$ && $.fn.select2) {
        document.querySelectorAll('select').forEach(function(selectElement) {
            selectElement.classList.add('no-select2');
            const $el = $(selectElement);
            if ($el.data('select2')) {
                $el.select2('destroy');
                $el.removeClass('select2-hidden-accessible');
                $el.next('.select2-container').remove();
            }
        });
    }
    
    loadClaims();
    loadHsnCodes();
    
    // Initialize keyboard navigation
    initHeaderKeyboardNavigation();
    initHsnTableKeyboardNavigation();
    initModalKeyboardNavigation();
    
    // Global ESC key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { 
            closeClaimModal(); 
            closeAdjustModal(); 
            closeHsnModal(); 
        }
    });
    
    // ==========================================
    // CTRL+ENTER → JUMP TO ROUND OFF FIELD
    // ==========================================
    // Global shortcut: Ctrl+Enter from ANY field moves focus to Round Off
    // Uses capture phase to intercept before all other Enter handlers
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            // Prevent all default behaviors
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Focus the Round Off field directly
            const roundOffField = document.getElementById('round_off');
            if (roundOffField) {
                roundOffField.focus();
                roundOffField.select();
            }
        }
    }, true); // Capture phase - fires before ALL other keydown handlers
    
    // ==========================================
    // CTRL+S → SAVE TRANSACTION
    // ==========================================
    // Global shortcut: Ctrl+S triggers saveTransaction()
    // Prevents browser "Save Page" dialog and duplicate submissions
    let isSaving = false;
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            // Prevent browser Save dialog
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Guard against duplicate submissions
            if (isSaving) return;
            isSaving = true;
            
            // Trigger save exactly as manual button click
            if (typeof saveTransaction === 'function') {
                saveTransaction();
            }
            
            // Reset guard after a short delay
            setTimeout(function() {
                isSaving = false;
            }, 1000);
        }
    }, true); // Capture phase
    
    // Set initial focus to Date field
    setTimeout(() => {
        const dateField = document.getElementById('transaction_date');
        if (dateField) {
            dateField.focus();
            dateField.select();
        }
    }, 100);
    
    // ==========================================
    // CLAIM AMOUNT ENTER KEY → ADD HSN MODAL
    // ==========================================
    // Dedicated Enter key handler for Claim Amount field
    // Prevents form submission and opens HSN modal instantly
    const claimAmountField = document.getElementById('claim_amount');
    const addHsnButton = document.getElementById('btn_add_hsn');
    const hsnModalElement = document.getElementById('hsnModal');
    const hsnModalBackdrop = document.getElementById('hsnModalBackdrop');
    
    if (claimAmountField && addHsnButton && hsnModalElement && hsnModalBackdrop) {
        // Handler for Claim Amount field
        claimAmountField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                // Prevent all default behaviors
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Check if Shift key is pressed
                if (e.shiftKey) {
                    // Shift+Enter: Navigate to button (for manual trigger)
                    addHsnButton.focus();
                    return;
                }
                
                // Enter alone: Directly open modal
                setTimeout(function() {
                    // Check if modal is already open to prevent duplicates
                    if (hsnModalElement.classList.contains('show')) {
                        return;
                    }
                    
                    // Open the modal using the existing function
                    showHsnModal();
                }, 50);
            }
        });
        
        // Handler for Add HSN Code button itself
        // When button is focused and Enter is pressed, open modal
        addHsnButton.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                // Check if modal is already open
                if (hsnModalElement.classList.contains('show')) {
                    return;
                }
                
                // Trigger the button's click handler
                showHsnModal();
            }
        });
        
        // ==========================================
        // AUTO-OPEN MODAL ON BUTTON FOCUS (TRIGGER)
        // ==========================================
        // When button receives focus (cursor trigger), automatically open modal
        let buttonFocusTimeout = null;
        addHsnButton.addEventListener('focus', function(e) {
            // Clear any existing timeout
            if (buttonFocusTimeout) {
                clearTimeout(buttonFocusTimeout);
            }
            
            // Small delay to ensure focus is stable
            buttonFocusTimeout = setTimeout(function() {
                // Check if modal is already open
                if (!hsnModalElement.classList.contains('show')) {
                    // Auto-open modal when button gets focus
                    showHsnModal();
                }
            }, 100);
        });
        
        // Clear timeout if button loses focus before modal opens
        addHsnButton.addEventListener('blur', function(e) {
            if (buttonFocusTimeout) {
                clearTimeout(buttonFocusTimeout);
                buttonFocusTimeout = null;
            }
        });
        
        // Auto-focus first input when modal opens
        // Using MutationObserver to detect when modal becomes visible
        const modalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (hsnModalElement.classList.contains('show')) {
                        // Modal just opened, focus the first input
                        setTimeout(function() {
                            const firstInput = document.getElementById('hsnSearchInput');
                            if (firstInput) {
                                firstInput.focus();
                                if (typeof firstInput.select === 'function') {
                                    firstInput.select();
                                }
                            }
                        }, 50);
                    }
                }
            });
        });
        
        // Start observing the modal for class changes
        modalObserver.observe(hsnModalElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});

// ==========================================
// KEYBOARD NAVIGATION FUNCTIONS
// ==========================================

let supplierDropdownIndex = -1; 

function initHeaderKeyboardNavigation() {
    const headerFields = [
        { id: 'transaction_date', next: 'supplierSearchInput' },
        { id: 'supplierSearchInput', next: 'party_trn_no' },
        { id: 'party_trn_no', next: 'party_date' },
        { id: 'party_date', next: 'os_amount' },
        { id: 'os_amount', next: 'claim_flag' },
        { id: 'claim_flag', next: 'received_as_debit_note' },
        { id: 'received_as_debit_note', next: 'claim_amount' },
        { id: 'claim_amount', next: 'btn_add_hsn' }, // Navigate to Add HSN button
        { id: 'btn_add_hsn', next: 'round_off' }, // From button to round_off
        { id: 'round_off', next: 'remarks' }
    ];

    headerFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element) return;
        
        element.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            
            // For supplier input, let its own handler manage Enter (selection)
            if (field.id === 'supplierSearchInput') return;
            
            e.preventDefault();
            
            // Special handling for Claim Amount -> Navigate to Add HSN button
            // Note: Dedicated handler exists in DOMContentLoaded for Enter key
            if (field.id === 'claim_amount') {
                if (field.next) {
                    const nextElement = document.getElementById(field.next);
                    if (nextElement) {
                        nextElement.focus();
                    }
                }
                return;
            }
            
            // Special handling for Add HSN button -> Open modal
            // Note: Dedicated handler exists in DOMContentLoaded for Enter key
            if (field.id === 'btn_add_hsn') {
                return; // Let the dedicated handler manage modal opening
            }
            
            if (field.next) {
                const nextElement = document.getElementById(field.next);
                if (nextElement) {
                    nextElement.focus();
                    if (nextElement.tagName === 'INPUT' && nextElement.type !== 'checkbox') {
                        nextElement.select();
                    }
                }
            }
        });
    });
    
    // Initialize supplier dropdown
    initSupplierDropdown();
}

/**
 * Initialize Supplier Dropdown with Professional ERP-style Keyboard Navigation
 * Replicated from Sales Return Customer Dropdown Reference
 */
function initSupplierDropdown() {
    const searchInput = document.getElementById('supplierSearchInput');
    const selectElement = document.getElementById('supplier_id');
    const dropdown = document.getElementById('supplierDropdown');
    const listContainer = document.getElementById('supplierList');
    const supplierNameInput = document.getElementById('supplier_name');

    if (!searchInput || !selectElement || !dropdown || !listContainer) return;

    let supplierActiveIndex = -1;

    function isSelectableItem(item) {
        return !!(item && item.getAttribute('data-id'));
    }

    function getVisibleItems() {
        return Array.from(listContainer.querySelectorAll('.dropdown-item:not([style*="display: none"])'))
            .filter(isSelectableItem);
    }

    function setActiveItem(index) {
        const items = getVisibleItems();
        items.forEach(item => item.classList.remove('active'));
        
        if (index < 0 || index >= items.length) {
            supplierActiveIndex = -1;
            return;
        }
        
        supplierActiveIndex = index;
        const activeItem = items[index];
        activeItem.classList.add('active');
        activeItem.scrollIntoView({ block: 'nearest' });
    }

    function filterSuppliers(searchTerm) {
        const items = listContainer.querySelectorAll('.dropdown-item');
        let visibleCount = 0;
        const normalized = (searchTerm || '').toLowerCase();
        
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const text = item.textContent.toLowerCase();
            
            if (name.includes(normalized) || text.includes(normalized)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        setActiveItem(-1);

        if (visibleCount === 0 && !document.getElementById('supplierNoResults')) {
            const noResults = document.createElement('div');
            noResults.id = 'supplierNoResults';
            noResults.style.padding = '12px';
            noResults.style.textAlign = 'center';
            noResults.style.color = '#999';
            noResults.textContent = 'No suppliers found';
            listContainer.appendChild(noResults);
        } else if (visibleCount > 0) {
            const noResults = document.getElementById('supplierNoResults');
            if (noResults) noResults.remove();
        }
    }

    function selectSupplierItem(item, shouldMoveNext = false) {
        if (!item || !isSelectableItem(item)) return false;
        
        const id = item.getAttribute('data-id');
        const name = item.getAttribute('data-name') || '';
        
        searchInput.value = name;
        selectElement.value = id;
        if (supplierNameInput) supplierNameInput.value = name;
        
        dropdown.style.display = 'none';
        
        // Trigger change event
        selectElement.dispatchEvent(new Event('change', { bubbles: true }));

        if (shouldMoveNext) {
            const nextField = document.getElementById('party_trn_no');
            if (nextField) {
                nextField.focus();
                if (typeof nextField.select === 'function') {
                    nextField.select();
                }
            }
        }
        return true;
    }

    // Input Event Listeners
    searchInput.addEventListener('focus', function() {
        dropdown.style.display = 'block';
        filterSuppliers(this.value || '');
    });

    searchInput.addEventListener('input', function() {
        filterSuppliers(this.value);
        dropdown.style.display = 'block';
    });

    // Click selection
    listContainer.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;
        selectSupplierItem(item, true);
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Keyboard Navigation (Input Scope)
    searchInput.addEventListener('keydown', function(e) {
        const isDropdownOpen = dropdown.style.display === 'block';
        
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const visibleItems = getVisibleItems();
            let selected = false;
            
            if (supplierActiveIndex >= 0 && visibleItems[supplierActiveIndex]) {
                selected = selectSupplierItem(visibleItems[supplierActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                // Default to first visible item if none selected
                selected = selectSupplierItem(visibleItems[0], true);
            }
            
            if (!selected) {
                dropdown.style.display = 'block';
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            
            if (!isDropdownOpen) {
                dropdown.style.display = 'block';
            }
            
            const items = getVisibleItems();
            if (!items.length) return;
            
            const nextIndex = supplierActiveIndex < 0 ? 0 : Math.min(supplierActiveIndex + 1, items.length - 1);
            setActiveItem(nextIndex);
            
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            
            if (!isDropdownOpen) {
                dropdown.style.display = 'block';
            }
            
            const items = getVisibleItems();
            if (!items.length) return;
            
            const prevIndex = supplierActiveIndex <= 0 ? 0 : supplierActiveIndex - 1;
            setActiveItem(prevIndex);
            
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        } else if (e.key === 'Tab') {
            dropdown.style.display = 'none';
        }
    });

    // Global capture to ensure dropdown selection works even if focus shifts slightly
    window.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        const isSupplierFocus = activeEl === searchInput || dropdown.contains(activeEl);
        const isDropdownOpen = dropdown.style.display === 'block';

        if (!isSupplierFocus || !isDropdownOpen) return;

        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const visibleItems = getVisibleItems();
            let selected = false;
            
            if (supplierActiveIndex >= 0 && visibleItems[supplierActiveIndex]) {
                selected = selectSupplierItem(visibleItems[supplierActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                selected = selectSupplierItem(visibleItems[0], true);
            }
            
            if (!selected) {
                dropdown.style.display = 'block';
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            
            const items = getVisibleItems();
            if (!items.length) return;
            
            const nextIndex = supplierActiveIndex < 0 ? 0 : Math.min(supplierActiveIndex + 1, items.length - 1);
            setActiveItem(nextIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            
            const items = getVisibleItems();
            if (!items.length) return;
            
            const prevIndex = supplierActiveIndex <= 0 ? 0 : supplierActiveIndex - 1;
            setActiveItem(prevIndex);
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    }, true);
}

// Update supplier name from hidden select (for backward compatibility)
function updateSupplierName() {
    const select = document.getElementById('supplier_id');
    const selectedOption = select.options[select.selectedIndex];
    const supplierNameInput = document.getElementById('supplier_name');
    if (supplierNameInput) {
        supplierNameInput.value = selectedOption ? selectedOption.dataset.name || '' : '';
    }
}

function initHsnTableKeyboardNavigation() {
    const hsnTableBody = document.getElementById('hsnTableBody');
    if (!hsnTableBody) return;
    
    hsnTableBody.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        
        const target = e.target;
        if (!target || target.tagName !== 'INPUT') return;
        
        const name = target.getAttribute('name') || '';
        
        // Amount field: Enter -> Qty
        if (name.includes('[amount]')) {
            e.preventDefault();
            const row = target.closest('tr');
            const qtyInput = row?.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
            return;
        }
        
        // Qty field: Enter -> Focus Add HSN button (which auto-triggers modal)
        if (name.includes('[qty]')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Move focus to Add HSN Code button first
            const addHsnBtn = document.getElementById('btn_add_hsn');
            if (addHsnBtn) {
                addHsnBtn.focus();
                // The existing focus handler on btn_add_hsn will auto-trigger showHsnModal()
            }
            return;
        }
    });
}
function initModalKeyboardNavigation() {
    // Claim Modal keyboard navigation
    const claimSearchInput = document.getElementById('claimSearchInput');
    if (claimSearchInput) {
        claimSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Select first visible claim
                const firstRow = document.querySelector('#claimsListBody tr.claim-row');
                if (firstRow) {
                    firstRow.click();
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeClaimModal();
            }
        });
    }
    
    // ==========================================
    // HSN MODAL KEYBOARD NAVIGATION
    // ==========================================
    const hsnSearchInput = document.getElementById('hsnSearchInput');
    const hsnListBody = document.getElementById('hsnListBody');
    const hsnModal = document.getElementById('hsnModal');
    
    if (hsnSearchInput && hsnListBody && hsnModal) {
        // Search input handler - filter on typing (but not on arrow keys)
        hsnSearchInput.addEventListener('input', function(e) {
            filterHsnCodes();
        });
        
        // Prevent arrow keys from triggering input event
        hsnSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                // Don't let these keys trigger input event
                // They will be handled by modal keydown handler
                return;
            }
        });
        
        // Global keydown handler for the entire modal
        hsnModal.addEventListener('keydown', function(e) {
            const rows = Array.from(hsnListBody.querySelectorAll('tr'));
            const visibleRows = rows.filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Arrow Down - Current Index:', hsnModalSelectedIndex, 'Total Rows:', visibleRows.length);
                
                if (visibleRows.length > 0) {
                    // Move to next row
                    if (hsnModalSelectedIndex < 0) {
                        hsnModalSelectedIndex = 0;
                    } else if (hsnModalSelectedIndex < visibleRows.length - 1) {
                        hsnModalSelectedIndex++;
                    }
                    console.log('New Index:', hsnModalSelectedIndex);
                    highlightHsnRow(visibleRows, hsnModalSelectedIndex);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Arrow Up - Current Index:', hsnModalSelectedIndex, 'Total Rows:', visibleRows.length);
                
                if (visibleRows.length > 0) {
                    // Move to previous row
                    if (hsnModalSelectedIndex < 0) {
                        hsnModalSelectedIndex = visibleRows.length - 1;
                    } else if (hsnModalSelectedIndex > 0) {
                        hsnModalSelectedIndex--;
                    }
                    console.log('New Index:', hsnModalSelectedIndex);
                    highlightHsnRow(visibleRows, hsnModalSelectedIndex);
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                // Select highlighted row
                if (hsnModalSelectedIndex >= 0 && visibleRows[hsnModalSelectedIndex]) {
                    visibleRows[hsnModalSelectedIndex].click();
                } else if (visibleRows.length > 0) {
                    // Fallback: select first visible row
                    visibleRows[0].click();
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                e.stopPropagation();
                closeHsnModal();
            }
        });
        
        // Click handler for rows
        hsnListBody.addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (row) {
                const rows = Array.from(hsnListBody.querySelectorAll('tr'));
                const visibleRows = rows.filter(r => r.style.display !== 'none' && !r.querySelector('td[colspan]'));
                hsnModalSelectedIndex = visibleRows.indexOf(row);
            }
        });
        
        // Helper function to highlight selected row
        function highlightHsnRow(visibleRows, index) {
            // Remove previous highlights
            removeHsnHighlight(visibleRows);
            
            // Add highlight to selected row
            if (index >= 0 && index < visibleRows.length) {
                const selectedRow = visibleRows[index];
                selectedRow.classList.add('table-active');
                selectedRow.style.backgroundColor = '#cce5ff';
                selectedRow.style.fontWeight = 'bold';
                
                // Scroll into view
                selectedRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }
        
        // Helper function to remove highlights
        function removeHsnHighlight(visibleRows) {
            visibleRows.forEach(row => {
                row.classList.remove('table-active');
                row.style.backgroundColor = '';
                row.style.fontWeight = '';
            });
        }
        
        // Reset selection when modal closes
        const modalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (!hsnModal.classList.contains('show')) {
                        hsnModalSelectedIndex = -1;
                        const rows = Array.from(hsnListBody.querySelectorAll('tr'));
                        removeHsnHighlight(rows);
                    }
                }
            });
        });
        modalObserver.observe(hsnModal, { attributes: true });
    }
    
    // Adjustment Modal keyboard navigation
    const adjustModal = document.getElementById('adjustModal');
    if (adjustModal) {
        // Helper: highlight the row of the focused adjust-input
        function highlightAdjustRow(input) {
            // Remove highlight from all rows
            document.querySelectorAll('#adjustTableBody tr').forEach(tr => {
                tr.style.backgroundColor = '';
                tr.style.fontWeight = '';
            });
            // Highlight current row
            if (input) {
                const row = input.closest('tr');
                if (row) {
                    row.style.backgroundColor = '#cce5ff';
                    row.style.fontWeight = 'bold';
                    row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            }
        }
        
        // Auto-highlight on focus
        adjustModal.addEventListener('focusin', function(e) {
            if (e.target.classList.contains('adjust-input')) {
                highlightAdjustRow(e.target);
            }
        });
        
        // ==========================================
        // DOCUMENT-LEVEL: Left/Right Arrow + Enter on buttons
        // Works even when no element inside modal has focus
        // ==========================================
        document.addEventListener('keydown', function(e) {
            // Only when Purchase Adjustment modal is visible
            if (!adjustModal.classList.contains('show')) return;
            
            const saveBtn = adjustModal.querySelector('button.btn-success');
            const cancelBtn = adjustModal.querySelector('button.btn-secondary');
            
            // Right Arrow → focus Save button
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (saveBtn) saveBtn.focus();
                return;
            }
            
            // Left Arrow → focus Cancel button
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (cancelBtn) cancelBtn.focus();
                return;
            }
            
            // Enter on button → trigger click
            if (e.target.tagName === 'BUTTON' && adjustModal.contains(e.target) && e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                e.target.click();
                return;
            }
        }, true); // Capture phase - fires before all other handlers
        
        // ==========================================
        // MODAL-LEVEL: Up/Down/Enter for adjust-inputs
        // ==========================================
        adjustModal.addEventListener('keydown', function(e) {
            const isAdjustInput = e.target.classList.contains('adjust-input');
            if (!isAdjustInput) return;
            
            const inputs = Array.from(document.querySelectorAll('.adjust-input'));
            const currentIndex = inputs.indexOf(e.target);
            
            // Arrow Down → next adjust-input
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                    inputs[currentIndex + 1].select();
                }
                return;
            }
            
            // Arrow Up → previous adjust-input
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex > 0) {
                    inputs[currentIndex - 1].focus();
                    inputs[currentIndex - 1].select();
                }
                return;
            }
            
            // Enter → next adjust-input or Save button
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                    inputs[currentIndex + 1].select();
                } else {
                    // Last input - focus on Save button
                    const saveBtn = adjustModal.querySelector('button.btn-success');
                    if (saveBtn) saveBtn.focus();
                }
                return;
            }
        });
        
        // Auto-focus when modal opens (adjust-input OR Save button fallback)
        const adjustObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (adjustModal.classList.contains('show')) {
                        setTimeout(function() {
                            const firstInput = document.querySelector('.adjust-input');
                            if (firstInput) {
                                firstInput.focus();
                                firstInput.select();
                            } else {
                                // No inputs (no purchases) → focus Save button
                                const saveBtn = adjustModal.querySelector('button.btn-success');
                                if (saveBtn) saveBtn.focus();
                            }
                        }, 150);
                    } else {
                        // Clear highlights when modal closes
                        document.querySelectorAll('#adjustTableBody tr').forEach(tr => {
                            tr.style.backgroundColor = '';
                            tr.style.fontWeight = '';
                        });
                    }
                }
            });
        });
        adjustObserver.observe(adjustModal, { attributes: true, attributeFilter: ['class'] });
    }
}

// Update supplier name from dropdown
function updateSupplierName() {
    const select = document.getElementById('supplier_id');
    const selectedOption = select.options[select.selectedIndex];
    document.getElementById('supplier_name').value = selectedOption ? selectedOption.dataset.name || '' : '';
}

// Load Claim to Supplier Transactions
function loadClaims() {
    fetch('{{ route("admin.claim-to-supplier.past-claims") }}')
        .then(r => r.json())
        .then(data => {
            console.log('Claims API Response:', data);
            // API returns {success: true, transactions: [...]}
            if (data.success && data.transactions) {
                allClaims = data.transactions;
            } else {
                allClaims = [];
            }
            renderClaimsList(allClaims);
        })
        .catch(e => console.error('Error loading claims:', e));
}

function showClaimModal() {
    document.getElementById('claimModalBackdrop').classList.add('show');
    document.getElementById('claimModal').classList.add('show');
    document.getElementById('claimSearchInput').value = '';
    renderClaimsList(allClaims);
    setTimeout(() => {
        const searchInput = document.getElementById('claimSearchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }, 100);
}

function closeClaimModal() {
    document.getElementById('claimModalBackdrop').classList.remove('show');
    document.getElementById('claimModal').classList.remove('show');
}

function filterClaims() {
    const search = document.getElementById('claimSearchInput').value.toLowerCase();
    const filtered = allClaims.filter(c => 
        (c.claim_no && c.claim_no.toLowerCase().includes(search)) ||
        (c.supplier_name && c.supplier_name.toLowerCase().includes(search))
    );
    renderClaimsList(filtered);
}

function renderClaimsList(claims) {
    const tbody = document.getElementById('claimsListBody');
    if (!claims || !claims.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No claims found</td></tr>';
        return;
    }
    tbody.innerHTML = claims.map(c => `
        <tr class="claim-row" onclick="selectClaim(${c.id})">
            <td><strong>${c.claim_no || ''}</strong></td>
            <td>${c.claim_date || ''}</td>
            <td>${c.supplier_name || ''}</td>
            <td class="text-end">₹${parseFloat(c.net_amount || c.amount || 0).toFixed(2)}</td>
            <td class="text-end text-primary">₹${parseFloat(c.balance_amount || c.net_amount || c.amount || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectClaim(claimId) {
    const claim = allClaims.find(c => c.id === claimId);
    if (!claim) return;
    
    console.log('selectClaim - claim:', claim);
    
    closeClaimModal();
    
    // Populate form fields
    document.getElementById('claim_transaction_id').value = claim.id;
    
    // Set supplier using the custom dropdown
    const supplierSearchInput = document.getElementById('supplierSearchInput');
    const supplierSelect = document.getElementById('supplier_id');
    const supplierNameInput = document.getElementById('supplier_name');
    const supplierIdStr = String(claim.supplier_id || '');
    
    console.log('Setting supplier_id to:', supplierIdStr);
    
    // Find and select the matching option
    let found = false;
    for (let i = 0; i < supplierSelect.options.length; i++) {
        if (String(supplierSelect.options[i].value) === supplierIdStr) {
            supplierSelect.selectedIndex = i;
            found = true;
            break;
        }
    }
    
    // Update the search input and hidden field
    if (found) {
        supplierSearchInput.value = claim.supplier_name || '';
        supplierNameInput.value = claim.supplier_name || '';
    }
    
    console.log('Dropdown value after set:', supplierSelect.value, 'Found:', found);
    
    document.getElementById('claim_trn_no').value = claim.claim_no || '';
    
    const netAmount = parseFloat(claim.net_amount || claim.amount || 0);
    document.getElementById('os_amount').value = netAmount.toFixed(2);
    document.getElementById('claim_amount').value = netAmount.toFixed(2);
    
    // Load HSN data from claim items if available
    if (claim.items && claim.items.length > 0) {
        loadHSNFromClaim(claim.items);
    }
    
    calculateFromClaimAmount();
}

function loadHSNFromClaim(items) {
    // Group items by HSN code
    const hsnGroups = {};
    items.forEach(item => {
        const hsn = item.hsn_code || 'NO-HSN';
        if (!hsnGroups[hsn]) {
            hsnGroups[hsn] = {
                hsn_code: hsn,
                amount: 0,
                gst_percent: (parseFloat(item.cgst_percent || 0) + parseFloat(item.sgst_percent || 0)),
                igst_percent: 0,
                qty: 0
            };
        }
        hsnGroups[hsn].amount += parseFloat(item.ft_amount || 0);
        hsnGroups[hsn].qty += parseFloat(item.qty || 0);
    });
    
    // Clear and populate HSN table
    clearAllRows();
    Object.values(hsnGroups).forEach(hsn => {
        addHSNRowWithData(hsn);
    });
    
    calculateTotals();
}

// HSN Table Functions
function addHSNRowWithData(data) {
    const tbody = document.getElementById('hsnTableBody');
    const idx = hsnRowIndex++;
    
    const gstAmount = (parseFloat(data.amount) * parseFloat(data.gst_percent) / 100).toFixed(2);
    
    const tr = document.createElement('tr');
    tr.id = `hsn_row_${idx}`;
    tr.onclick = function() { selectHsnRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="hsn[${idx}][hsn_code]" class="form-control readonly-field" value="${data.hsn_code || ''}" readonly></td>
        <td><input type="number" name="hsn[${idx}][amount]" class="form-control text-end" value="${parseFloat(data.amount || 0).toFixed(2)}" step="0.01" onchange="calculateRowGST(${idx}); validateAmount();"></td>
        <td><input type="number" name="hsn[${idx}][gst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.gst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][igst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.igst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][gst_amount]" class="form-control text-end readonly-field" value="${gstAmount}" readonly></td>
        <td><input type="number" name="hsn[${idx}][qty]" class="form-control text-end" value="${parseInt(data.qty || 1)}" step="1"></td>
    `;
    tbody.appendChild(tr);
    selectHsnRow(idx);
    calculateTotals();
    validateAmount();
    
    // Focus on amount field of newly added row
    setTimeout(() => {
        const amountInput = tr.querySelector('input[name*="[amount]"]');
        if (amountInput && !amountInput.readOnly) {
            amountInput.focus();
            amountInput.select();
        }
    }, 100);
}

function selectHsnRow(idx) {
    document.querySelectorAll('#hsnTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`hsn_row_${idx}`);
    if (row) {
        row.classList.add('row-selected');
        selectedHsnRowIndex = idx;
    }
}

function calculateRowGST(idx) {
    const row = document.getElementById(`hsn_row_${idx}`);
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
    const gstPercent = parseFloat(row.querySelector('input[name*="[gst_percent]"]').value) || 0;
    const igstPercent = parseFloat(row.querySelector('input[name*="[igst_percent]"]').value) || 0;
    
    const gstAmount = amount * (gstPercent + igstPercent) / 100;
    row.querySelector('input[name*="[gst_amount]"]').value = gstAmount.toFixed(2);
    
    calculateTotals();
}

function deleteSelectedRow() {
    if (selectedHsnRowIndex !== null) {
        document.getElementById(`hsn_row_${selectedHsnRowIndex}`)?.remove();
        selectedHsnRowIndex = null;
        calculateTotals();
    } else {
        alert('Please select a row first');
    }
}

function clearAllRows() {
    document.getElementById('hsnTableBody').innerHTML = '';
    hsnRowIndex = 0;
    selectedHsnRowIndex = null;
    calculateTotals();
}

// HSN Modal Functions
function loadHsnCodes() {
    fetch('{{ route("admin.sale-voucher.hsn-codes") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.hsn_codes) {
                allHsnCodes = data.hsn_codes;
            }
        })
        .catch(e => console.error('Error loading HSN codes:', e));
}

function showHsnModal() {
    const backdrop = document.getElementById('hsnModalBackdrop');
    const modal = document.getElementById('hsnModal');
    
    // Prevent duplicate modal instances
    if (modal.classList.contains('show')) {
        return; // Modal already open, do nothing
    }
    
    backdrop.classList.add('show');
    modal.classList.add('show');
    document.getElementById('hsnSearchInput').value = '';
    renderHsnList(allHsnCodes);
    
    // Immediate focus and highlight first row
    setTimeout(() => {
        const searchInput = document.getElementById('hsnSearchInput');
        if (searchInput) {
            searchInput.focus();
            if (searchInput.select) searchInput.select();
        }
        
        // Auto-select first row by default
        const hsnListBody = document.getElementById('hsnListBody');
        if (hsnListBody) {
            const rows = Array.from(hsnListBody.querySelectorAll('tr'));
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            
            if (visibleRows.length > 0) {
                // Set first row as selected
                hsnModalSelectedIndex = 0;
                
                // Highlight first row
                const firstRow = visibleRows[0];
                firstRow.classList.add('table-active');
                firstRow.style.backgroundColor = '#cce5ff';
                firstRow.style.fontWeight = 'bold';
            }
        }
    }, 100); // Slightly increased delay to ensure DOM is ready
}

function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
}

function filterHsnCodes() {
    const search = document.getElementById('hsnSearchInput').value.toLowerCase();
    const filtered = allHsnCodes.filter(h => 
        (h.hsn_code && h.hsn_code.toLowerCase().includes(search)) ||
        (h.name && h.name.toLowerCase().includes(search))
    );
    renderHsnList(filtered);
    
    // Auto-select first row after filtering
    setTimeout(() => {
        const hsnListBody = document.getElementById('hsnListBody');
        if (hsnListBody) {
            const rows = Array.from(hsnListBody.querySelectorAll('tr'));
            const visibleRows = rows.filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            
            // Remove all highlights first
            rows.forEach(row => {
                row.classList.remove('table-active');
                row.style.backgroundColor = '';
                row.style.fontWeight = '';
            });
            
            if (visibleRows.length > 0) {
                // Set first row as selected
                hsnModalSelectedIndex = 0;
                
                // Highlight first row
                const firstRow = visibleRows[0];
                firstRow.classList.add('table-active');
                firstRow.style.backgroundColor = '#cce5ff';
                firstRow.style.fontWeight = 'bold';
            } else {
                hsnModalSelectedIndex = -1;
            }
        }
    }, 50);
}

function renderHsnList(hsnCodes) {
    const tbody = document.getElementById('hsnListBody');
    if (!hsnCodes || !hsnCodes.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No HSN codes found</td></tr>';
        return;
    }
    tbody.innerHTML = hsnCodes.map(h => `
        <tr class="claim-row" onclick="selectHsnCode(${h.id})" style="cursor:pointer;">
            <td><strong>${h.hsn_code || ''}</strong></td>
            <td>${h.name || ''}</td>
            <td class="text-end">${parseFloat(h.cgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.sgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.total_gst_percent || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectHsnCode(hsnId) {
    const hsn = allHsnCodes.find(h => h.id === hsnId);
    if (!hsn) return;
    
    closeHsnModal();
    
    // Add HSN row with selected code
    addHSNRowWithData({
        hsn_code: hsn.hsn_code,
        amount: 0,
        gst_percent: parseFloat(hsn.total_gst_percent || 0),
        igst_percent: 0,
        qty: 1
    });
}

// Validation Function
function validateAmount() {
    const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
    const claimAmount = parseFloat(document.getElementById('claim_amount').value) || 0;
    const finalAmount = parseFloat(document.getElementById('final_amount').value) || 0;
    
    // Calculate reference amount (O/S Amount)
    const referenceAmount = osAmount;
    
    // Check if final amount exceeds O/S Amount
    if (referenceAmount > 0 && finalAmount > referenceAmount) {
        document.getElementById('final_amount').style.color = 'red';
        return false;
    } else {
        document.getElementById('final_amount').style.color = '#28a745';
        return true;
    }
}

function calculateFromClaimAmount() {
    calculateTotals();
    validateAmount();
}

function calculateTotals() {
    let grossAmt = 0;
    let totalGst = 0;
    
    const hsnRows = document.querySelectorAll('#hsnTableBody tr');
    
    // If HSN codes are added, calculate from HSN table
    if (hsnRows.length > 0) {
        hsnRows.forEach(row => {
            const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
            const gstAmount = parseFloat(row.querySelector('input[name*="[gst_amount]"]')?.value) || 0;
            grossAmt += amount;
            totalGst += gstAmount;
        });
    } else {
        // No HSN - use Claim Amount, if no Claim Amount use O/S Amount
        const claimAmount = parseFloat(document.getElementById('claim_amount').value) || 0;
        const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
        grossAmt = claimAmount > 0 ? claimAmount : osAmount;
    }
    
    const netAmt = grossAmt + totalGst;
    const roundOff = parseFloat(document.getElementById('round_off').value) || 0;
    const finalAmount = netAmt + roundOff;
    
    document.getElementById('gross_amt').value = grossAmt.toFixed(2);
    document.getElementById('total_gst').value = totalGst.toFixed(2);
    document.getElementById('total_gst_amt').value = totalGst.toFixed(2);
    document.getElementById('net_amt').value = netAmt.toFixed(2);
    document.getElementById('final_amount').value = finalAmount.toFixed(2);
    
    validateAmount();
}

// Save Transaction
function saveTransaction() {
    const supplierId = document.getElementById('supplier_id').value;
    if (!supplierId) {
        alert('Please select a supplier or load a claim first');
        return;
    }
    
    const finalAmount = parseFloat(document.getElementById('final_amount').value) || 0;
    if (finalAmount <= 0) {
        alert('Amount must be greater than 0');
        return;
    }
    
    // Validate: Final amount should not exceed O/S Amount
    const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
    if (osAmount > 0 && finalAmount > osAmount) {
        alert('Final Amount (₹' + finalAmount.toFixed(2) + ') cannot exceed O/S Amount (₹' + osAmount.toFixed(2) + ')');
        return;
    }
    
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
    
    pendingTransactionData = {
        claim_transaction_id: document.getElementById('claim_transaction_id').value,
        transaction_date: document.getElementById('transaction_date').value,
        supplier_id: supplierId,
        supplier_name: document.getElementById('supplier_name').value,
        party_trn_no: document.getElementById('party_trn_no').value,
        party_date: document.getElementById('party_date').value,
        claim_flag: document.getElementById('claim_flag').value,
        received_as_debit_note: document.getElementById('received_as_debit_note').checked,
        claim_amount: parseFloat(document.getElementById('claim_amount').value) || 0,
        gross_amt: parseFloat(document.getElementById('gross_amt').value) || 0,
        total_gst: parseFloat(document.getElementById('total_gst').value) || 0,
        net_amt: parseFloat(document.getElementById('net_amt').value) || 0,
        round_off: parseFloat(document.getElementById('round_off').value) || 0,
        final_amount: finalAmount,
        remarks: document.getElementById('remarks').value,
        hsn_items: hsnItems
    };
    
    showAdjustModal();
}

// Adjustment Modal Functions
function showAdjustModal() {
    const supplierId = pendingTransactionData.supplier_id;
    const totalAmount = pendingTransactionData.final_amount;
    
    console.log('showAdjustModal - supplierId:', supplierId, 'totalAmount:', totalAmount);
    
    const url = `{{ url('admin/breakage-supplier/supplier-purchases') }}/${supplierId}`;
    console.log('Fetching URL:', url);
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                renderAdjustTable(data.purchases, totalAmount);
                document.getElementById('adjustBackdrop').classList.add('show');
                document.getElementById('adjustModal').classList.add('show');
            } else {
                console.error('API returned success:false', data.message);
                submitTransaction([]);
            }
        })
        .catch(e => {
            console.error('Error:', e);
            submitTransaction([]);
        });
}

function renderAdjustTable(purchases, totalAmount) {
    const tbody = document.getElementById('adjustTableBody');
    
    if (!purchases.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No purchases found for this supplier</td></tr>';
    } else {
        tbody.innerHTML = purchases.map((p, i) => `
            <tr data-purchase-id="${p.id}" data-original-balance="${parseFloat(p.balance_amount || 0)}">
                <td class="text-center">${i + 1}</td>
                <td><strong>${p.purchase_no || ''}</strong></td>
                <td>${p.purchase_date || ''}</td>
                <td class="text-end">₹${parseFloat(p.total_amount || 0).toFixed(2)}</td>
                <td class="text-end text-primary fw-bold balance-cell" data-original="${parseFloat(p.balance_amount || 0)}">${parseFloat(p.balance_amount || 0).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end adjust-input" 
                           data-purchase-id="${p.id}" data-max="${parseFloat(p.balance_amount || 0)}"
                           step="0.01" min="0" value="0" oninput="updateRowBalance(this)" onchange="updateRowBalance(this)">
                </td>
            </tr>
        `).join('');
    }
    
    document.getElementById('adjustTotalDisplay').textContent = '₹' + totalAmount.toFixed(2);
    document.getElementById('adjustedDisplay').textContent = '₹0.00';
    document.getElementById('adjustBalanceDisplay').textContent = '₹' + totalAmount.toFixed(2);
}

// Update row balance instantly when adjustment amount changes
function updateRowBalance(input) {
    const row = input.closest('tr');
    const balanceCell = row.querySelector('.balance-cell');
    const originalBalance = parseFloat(input.dataset.max) || 0;
    const adjustAmount = parseFloat(input.value) || 0;
    
    // Validate amount doesn't exceed original balance
    if (adjustAmount > originalBalance) {
        input.value = originalBalance;
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
    }
    
    // Update balance cell instantly
    const newBalance = originalBalance - Math.min(adjustAmount, originalBalance);
    balanceCell.textContent = newBalance.toFixed(2);
    
    // Update totals
    updateAdjustTotals();
}

function updateAdjustTotals() {
    const totalAmount = pendingTransactionData.final_amount;
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjust-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const max = parseFloat(input.dataset.max) || 0;
        if (amount > max) {
            input.value = max;
        }
        totalAdjusted += Math.min(amount, max);
    });
    
    document.getElementById('adjustedDisplay').textContent = '₹' + totalAdjusted.toFixed(2);
    document.getElementById('adjustBalanceDisplay').textContent = '₹' + (totalAmount - totalAdjusted).toFixed(2);
}

function closeAdjustModal() {
    document.getElementById('adjustBackdrop').classList.remove('show');
    document.getElementById('adjustModal').classList.remove('show');
}

function saveWithAdjustments() {
    const adjustments = [];
    document.querySelectorAll('.adjust-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        if (amount > 0) {
            adjustments.push({
                purchase_id: input.dataset.purchaseId,
                amount: amount
            });
        }
    });
    
    closeAdjustModal();
    submitTransaction(adjustments);
}

function submitTransaction(adjustments) {
    pendingTransactionData.adjustments = adjustments;
    
    fetch('{{ route("admin.breakage-supplier.store-received") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(pendingTransactionData)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Transaction saved successfully! Trn No: ' + result.trn_no);
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to save'));
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Discard changes?')) {
        window.location.href = '{{ route("admin.breakage-supplier.received-modification") }}';
    }
}

document.getElementById('round_off')?.addEventListener('change', calculateTotals);
</script>
@endpush