@extends('layouts.admin')

@section('title', 'Credit Note Modification')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Credit Note Modification</h4>
        <div class="text-muted small">Edit existing credit note</div>
    </div>
    <div>
        <a href="{{ route('admin.credit-note.invoices') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
        <a href="{{ route('admin.credit-note.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New
        </a>
    </div>
</div>

<!-- Search Section -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="searchCreditNoteNo" class="form-label">Credit Note No.</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchCreditNoteNo" placeholder="Enter Credit Note No." 
                           value="{{ $preloadCreditNoteNo ?? '' }}">
                    <button class="btn btn-primary" type="button" onclick="searchCreditNote()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-info w-100" type="button" id="browseBtn" onclick="openCreditNotesModal()">
                    <i class="bi bi-list-ul me-1"></i> Browse
                </button>
            </div>
        </div>
    </div>
</div>

<form id="creditNoteForm" autocomplete="off">
    @csrf
    
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Credit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="creditNoteDate" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="creditNoteDate" name="credit_note_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly tabindex="-1">
                </div>
                <div class="col-md-2">
                    <label for="creditNoteNo" class="form-label">Credit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="creditNoteNo" readonly tabindex="-1">
                </div>
                <div class="col-md-3">
                    <label for="reason" class="form-label">Reason</label>
                    <select class="form-select no-select2" id="reason" name="reason">
                        <option value="">Select Reason</option>
                        <option value="Rate Diff.">Rate Diff.</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Party Details Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-people me-2"></i> Party Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Credit Section (Left) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-primary mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Credit (Party)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="credit_party_type" id="partySupplier" value="S" checked>
                                    <label class="btn btn-outline-primary" for="partySupplier">Supplier</label>
                                    <input type="radio" class="btn-check" name="credit_party_type" id="partyCustomer" value="C">
                                    <label class="btn btn-outline-primary" for="partyCustomer">Customer</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="partySelect" class="form-label">Party Name <span class="text-danger">*</span></label>
                                <div class="position-relative party-search-container">
                                    <!-- Visual Input for searching -->
                                    <input type="text" class="form-control" id="partySearchInput" placeholder="Type to search..." autocomplete="off">
                                    <!-- Hidden Input for actual value submission -->
                                    <input type="hidden" id="partySelect" name="credit_party_id">
                                    <!-- Results Dropdown -->
                                    <div id="partySearchResults" class="list-group position-absolute w-100 shadow-sm start-0" style="display:none; z-index: 1050; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #ddd;"></div>
                                </div>
                                <small class="text-muted">Start typing to search for suppliers</small>
                            </div>
                            <div class="col-md-6">
                                <label for="salesmanSelect" class="form-label">Sales Man</label>
                                <select class="form-select no-select2" id="salesmanSelect" name="salesman_id">
                                    <option value="">Select Salesman</option>
                                    @foreach($salesmen as $salesman)
                                        <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #fff3cd;">
                        <h6 class="text-warning mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Account)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Account Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountPurchase" value="P" checked>
                                    <label class="btn btn-outline-warning" for="accountPurchase">Purchase</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountSale" value="S">
                                    <label class="btn btn-outline-warning" for="accountSale">Sale</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountGeneral" value="G">
                                    <label class="btn btn-outline-warning" for="accountGeneral">General</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control" id="accountNo" name="debit_account_no">
                            </div>
                            <div class="col-md-6">
                                <label for="invRefNo" class="form-label">Inv. Ref. No.</label>
                                <input type="text" class="form-control" id="invRefNo" name="inv_ref_no">
                            </div>
                            <div class="col-md-6">
                                <label for="invoiceDate" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoiceDate" name="invoice_date">
                            </div>
                            <div class="col-md-6">
                                <label for="gstVno" class="form-label">GST Vno.</label>
                                <input type="text" class="form-control" id="gstVno" name="gst_vno">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Party Transaction Details -->
                <div class="col-md-12">
                    <div class="row g-2 mt-2">
                        <div class="col-md-3">
                            <label for="partyTrnNo" class="form-label">Party Trn. No.</label>
                            <input type="text" class="form-control" id="partyTrnNo" name="party_trn_no">
                        </div>
                        <div class="col-md-3">
                            <label for="partyTrnDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="partyTrnDate" name="party_trn_date">
                        </div>
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HSN Code Table -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i> HSN Details</h6>
            <button type="button" class="btn btn-light btn-sm" onclick="addHsnRow()">
                <i class="bi bi-plus-circle me-1"></i> Add Row
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="hsnTable" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">HSN Code</th>
                            <th style="width: 120px;">Amount</th>
                            <th style="width: 80px;">GST%</th>
                            <th style="width: 80px;">CGST%</th>
                            <th style="width: 100px;">CGST Amt</th>
                            <th style="width: 80px;">SGST%</th>
                            <th style="width: 100px;">SGST Amt</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hsnTableBody">
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calculation Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i> Narration</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter narration..."></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i> Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Gross Amount</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="grossAmount" name="gross_amount" value="0.00" readonly tabindex="-1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total GST</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="totalGst" name="total_gst" value="0.00" readonly tabindex="-1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Net Amount</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="netAmount" name="net_amount" value="0.00" readonly tabindex="-1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">TCS</label>
                            <input type="number" class="form-control form-control-sm" id="tcsAmount" name="tcs_amount" step="0.01" value="0.00" onchange="calculateTotals()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Round Off</label>
                            <input type="number" class="form-control form-control-sm" id="roundOff" name="round_off" step="0.01" value="0.00" onchange="calculateTotals()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-success">CN Amount</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-success readonly-field" id="cnAmount" name="cn_amount" value="0.00" readonly tabindex="-1" style="font-size: 16px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card shadow-sm border-0">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.credit-note.invoices') }}'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteCreditNoteBtn" style="display: none;" onclick="deleteCreditNote()">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-info me-2" id="adjustmentBtn" onclick="openAdjustmentModal()" style="display: none;">
                        <i class="bi bi-sliders me-1"></i> Adjustments
                    </button>
                    <button type="button" class="btn btn-primary" id="updateBtn" onclick="updateCreditNote()" disabled>
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Hidden field for credit note ID -->
<input type="hidden" id="creditNoteId" value="">

<!-- Browse Credit Notes Modal - Custom -->
<div class="custom-modal-backdrop" id="creditNotesModalBackdrop" onclick="closeCreditNotesModal()"></div>
<div class="custom-modal" id="creditNotesModal">
    <div class="custom-modal-header">
        <h5><i class="bi bi-list-ul me-2"></i> Select Credit Note</h5>
        <button type="button" class="custom-modal-close" onclick="closeCreditNotesModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <!-- Search Box -->
        <div class="mb-3">
            <input type="text" class="form-control" id="modalSearchInput" placeholder="Search by CN No., Party Name..." oninput="searchCreditNotesInModal()">
        </div>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>CN No.</th>
                        <th>Date</th>
                        <th>Party</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="creditNotesModalBody">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Adjustment Modal - Custom -->
<div class="custom-modal-backdrop" id="adjustmentModalBackdrop" onclick="closeAdjustmentModal()"></div>
<div class="custom-modal" id="adjustmentModal" style="width: 800px;">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #17a2b8, #138496);">
        <h5><i class="bi bi-sliders me-2"></i> Credit Note Adjustment</h5>
        <button type="button" class="custom-modal-close" onclick="closeAdjustmentModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 60px;">SR.NO.</th>
                        <th>TRANS NO.</th>
                        <th>DATE</th>
                        <th class="text-end">BILL AMT.</th>
                        <th style="width: 120px;">ADJUSTED</th>
                        <th class="text-end">BALANCE</th>
                    </tr>
                </thead>
                <tbody id="adjustmentModalBody">
                    <tr><td colspan="6" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">Press ESC to close</span>
                </div>
                <div class="col-md-6 text-end">
                    <strong class="text-primary" style="font-size: 16px;">
                        NET AMOUNT TO ADJUST: ₹ <span id="adjustmentBalance">0.00</span>
                    </strong>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Auto Adjust</span>
                        <input type="number" class="form-control" id="autoAdjustAmount" step="0.01" placeholder="Amount">
                        <button type="button" class="btn btn-info" onclick="autoDistributeAmount()">
                            <i class="bi bi-magic"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-modal-footer" style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #e9ecef; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">Cancel</button>
        <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustments()">
            <i class="bi bi-check-circle me-1"></i> Save Adjustments
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
let hsnRowCount = 0;
let currentPartyType = 'S'; // S = Supplier, C = Customer
let searchTimeout = null;
let currentFocusIndex = -1; // For search results navigation

// ============================================================
// FIELD NAVIGATION ORDER for Enter Key
// ============================================================
const FIELD_ORDER = [
    'searchCreditNoteNo',
    'browseBtn',        // Browse button
    'creditNoteDate',
    'reason',           // Native <select>
    'partySupplier',    // Radio Group Start (Supplier/Customer)
    'partySearchInput', // Custom Party Search Input
    'salesmanSelect',   // Native <select>
    'accountPurchase',  // Radio Group Start (Purchase/Sale/General)
    'accountNo',
    'invRefNo',
    'invoiceDate',
    'gstVno',
    'partyTrnNo',
    'partyTrnDate',
    'amount',
    'narration',
    'tcsAmount',
    'roundOff',
    'updateBtn'         // Final → Update button
];

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // Initialize Custom Party Search
    initPartySearch();
    
    // PREVENT form submission on Enter key globally
    document.getElementById('creditNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Initialize Keyboard Navigation
    initKeyboardNavigation();
    
    // Initialize Browse Modal Keyboard Navigation
    initBrowseModalKeyboard();
    
    // Initialize Adjustment Modal Keyboard Navigation
    initAdjustmentModalKeyboard();
    
    document.getElementById('creditNoteDate').addEventListener('change', updateDayName);
    
    document.querySelectorAll('input[name="credit_party_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentPartyType = this.value;
            updatePartyDropdown();
        });
    });
    
    // Auto-focus search field on page load
    setTimeout(function() {
        var searchField = document.getElementById('searchCreditNoteNo');
        if (searchField) {
            searchField.focus();
            searchField.select();
        }
    }, 100);
    
    // Auto-load if preload credit note no is provided
    @if($preloadCreditNoteNo)
        searchCreditNote();
    @endif
});

// ============================================================
// KEYBOARD NAVIGATION SYSTEM
// ============================================================
function initKeyboardNavigation() {

    // ==============================================
    // Escape Key → Close any open modal
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close Browse Credit Notes Modal
            var creditNotesModal = document.getElementById('creditNotesModal');
            if (creditNotesModal && creditNotesModal.classList.contains('show')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeCreditNotesModal();
                // Focus Browse button after closing
                setTimeout(function() {
                    var browseBtn = document.getElementById('browseBtn');
                    if (browseBtn) browseBtn.focus();
                }, 50);
                return false;
            }
            
            // Close Adjustment Modal
            var adjustmentModal = document.getElementById('adjustmentModal');
            if (adjustmentModal && adjustmentModal.classList.contains('show')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeAdjustmentModal();
                return false;
            }
        }
    }, true);

    // ==============================================
    // Ctrl+S → Update Credit Note
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var updateBtn = document.getElementById('updateBtn');
            if (updateBtn && !updateBtn.disabled && typeof updateCreditNote === 'function') {
                updateCreditNote();
            }
        }
    }, true);

    // ==============================================
    // DOCUMENT-LEVEL CAPTURE: Main Enter Key Handler
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        
        const activeEl = document.activeElement;
        if (!activeEl) return;
        
        // Skip if any modal is open
        var creditNotesModal = document.getElementById('creditNotesModal');
        var adjustmentModal = document.getElementById('adjustmentModal');
        if ((creditNotesModal && creditNotesModal.classList.contains('show')) ||
            (adjustmentModal && adjustmentModal.classList.contains('show'))) {
            return; // Let modal handlers deal with it
        }
        
        // ---- Handle Ctrl+Enter → jump to TCS Amount ----
        if (e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var tcsField = document.getElementById('tcsAmount');
            if (tcsField) {
                tcsField.focus();
                tcsField.select();
            }
            return false;
        }
        
        // ============================================
        // SHIFT+ENTER → BACKWARD NAVIGATION (ALL FIELDS)
        // ============================================
        if (e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Browse button → back to Search Credit Note No.
            if (activeEl.id === 'browseBtn') {
                var searchField = document.getElementById('searchCreditNoteNo');
                if (searchField) { searchField.focus(); searchField.select(); }
                return false;
            }
            
            // Credit Note Date → back to Browse button
            if (activeEl.id === 'creditNoteDate') {
                var browseBtn = document.getElementById('browseBtn');
                if (browseBtn) { browseBtn.focus(); }
                return false;
            }
            
            // Reason → back to Date
            if (activeEl.id === 'reason') {
                var dateField = document.getElementById('creditNoteDate');
                if (dateField) { dateField.focus(); }
                return false;
            }
            
            // Party Type radios → back to Reason
            if (activeEl.id === 'partySupplier' || activeEl.id === 'partyCustomer') {
                var reason = document.getElementById('reason');
                if (reason) {
                    reason.focus();
                    try {
                        reason.showPicker();
                    } catch(err) {
                        var event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
                        reason.dispatchEvent(event);
                    }
                }
                return false;
            }
            
            // Party Search Input → back to Party Type radio
            if (activeEl.id === 'partySearchInput') {
                var checkedRadio = document.querySelector('input[name="credit_party_type"]:checked');
                if (checkedRadio) { checkedRadio.focus(); }
                return false;
            }
            
            // Salesman → back to Party Search Input
            if (activeEl.id === 'salesmanSelect') {
                var partySearch = document.getElementById('partySearchInput');
                if (partySearch) {
                    partySearch.focus();
                    partySearch.select();
                }
                return false;
            }
            
            // Account Type radios → back to Salesman
            if (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral') {
                var salesman = document.getElementById('salesmanSelect');
                if (salesman) { salesman.focus(); }
                return false;
            }
            
            // Account No → back to Account Type radio
            if (activeEl.id === 'accountNo') {
                var checkedRadio = document.querySelector('input[name="debit_account_type"]:checked');
                if (checkedRadio) { checkedRadio.focus(); }
                return false;
            }
            
            // Inv Ref No → back to Account No
            if (activeEl.id === 'invRefNo') {
                var accountNo = document.getElementById('accountNo');
                if (accountNo) { accountNo.focus(); accountNo.select(); }
                return false;
            }
            
            // Invoice Date → back to Inv Ref No
            if (activeEl.id === 'invoiceDate') {
                var invRefNo = document.getElementById('invRefNo');
                if (invRefNo) { invRefNo.focus(); invRefNo.select(); }
                return false;
            }
            
            // GST Vno → back to Invoice Date
            if (activeEl.id === 'gstVno') {
                var invoiceDate = document.getElementById('invoiceDate');
                if (invoiceDate) { invoiceDate.focus(); }
                return false;
            }
            
            // Party Trn No → back to GST Vno
            if (activeEl.id === 'partyTrnNo') {
                var gstVno = document.getElementById('gstVno');
                if (gstVno) { gstVno.focus(); gstVno.select(); }
                return false;
            }
            
            // Party Trn Date → back to Party Trn No
            if (activeEl.id === 'partyTrnDate') {
                var partyTrnNo = document.getElementById('partyTrnNo');
                if (partyTrnNo) { partyTrnNo.focus(); partyTrnNo.select(); }
                return false;
            }
            
            // Amount → back to Party Trn Date
            if (activeEl.id === 'amount') {
                var partyTrnDate = document.getElementById('partyTrnDate');
                if (partyTrnDate) { partyTrnDate.focus(); }
                return false;
            }
            
            // Narration → back to Amount
            if (activeEl.id === 'narration') {
                var amount = document.getElementById('amount');
                if (amount) { amount.focus(); amount.select(); }
                return false;
            }
            
            // TCS Amount → back to Narration
            if (activeEl.id === 'tcsAmount') {
                var narration = document.getElementById('narration');
                if (narration) { narration.focus(); }
                return false;
            }
            
            // Round Off → back to TCS Amount
            if (activeEl.id === 'roundOff') {
                var tcsAmount = document.getElementById('tcsAmount');
                if (tcsAmount) { tcsAmount.focus(); tcsAmount.select(); }
                return false;
            }
            
            // Update button → back to Round Off
            if (activeEl.id === 'updateBtn') {
                var roundOff = document.getElementById('roundOff');
                if (roundOff) { roundOff.focus(); roundOff.select(); }
                return false;
            }
            
            // HSN amount → back to previous HSN row or Amount field
            if (activeEl.classList.contains('hsn-amount')) {
                var allHsnAmounts = Array.from(document.querySelectorAll('#hsnTableBody .hsn-amount'));
                var currentIdx = allHsnAmounts.indexOf(activeEl);
                
                if (currentIdx > 0) {
                    allHsnAmounts[currentIdx - 1].focus();
                    allHsnAmounts[currentIdx - 1].select();
                } else {
                    var amount = document.getElementById('amount');
                    if (amount) { amount.focus(); amount.select(); }
                }
                return false;
            }
            
            // General input fields → navigate backward using FIELD_ORDER
            if (activeEl.tagName === 'INPUT' || activeEl.tagName === 'SELECT' || activeEl.tagName === 'TEXTAREA') {
                navigateField(activeEl, -1);
                return false;
            }
            
            return false;
        }
        
        // ============================================
        // ENTER → FORWARD NAVIGATION
        // ============================================
        
        // ---- Handle Browse button Enter → open Browse modal ----
        if (activeEl.id === 'browseBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            openCreditNotesModal();
            return false;
        }
        
        // Skip other buttons and links - let them work normally
        if (activeEl.tagName === 'BUTTON' || activeEl.tagName === 'A') return;
        
        // ---- Handle Party Search Input Enter → select highlighted party ----
        if (activeEl.id === 'partySearchInput') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var partyResults = document.getElementById('partySearchResults');
            
            // If dropdown is visible, select highlighted item
            if (partyResults && partyResults.style.display === 'block') {
                var activeItem = partyResults.querySelector('.list-group-item.active');
                
                if (activeItem) {
                    // Click the highlighted item to select the party
                    activeItem.click();
                } else {
                    // No item highlighted → highlight the first one
                    var firstItem = partyResults.querySelector('.list-group-item');
                    if (firstItem) {
                        currentFocusIndex = 0;
                        var allItems = partyResults.querySelectorAll('.list-group-item');
                        highlightItem(allItems, 0);
                    }
                }
            } else {
                // Dropdown not visible → if party already selected, move to Salesman
                var hiddenInput = document.getElementById('partySelect');
                if (hiddenInput && hiddenInput.value) {
                    setTimeout(function() {
                        var salesmanSelect = document.getElementById('salesmanSelect');
                        if (salesmanSelect) {
                            salesmanSelect.focus();
                            try {
                                salesmanSelect.showPicker();
                            } catch(err) {
                                var event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
                                salesmanSelect.dispatchEvent(event);
                            }
                        }
                    }, 50);
                }
            }
            return false;
        }
        
        // ---- Handle Search Credit Note No. Enter → trigger search, then move to Browse ----
        if (activeEl.id === 'searchCreditNoteNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (activeEl.value.trim()) {
                searchCreditNote();
            }
            
            // Move cursor to Browse button
            setTimeout(function() {
                var browseBtn = document.getElementById('browseBtn');
                if (browseBtn) {
                    browseBtn.focus();
                }
            }, 50);
            return false;
        }
        
        // ---- Handle Date field Enter → jump to Reason ----
        if (activeEl.id === 'creditNoteDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            activeEl.blur();
            
            setTimeout(function() {
                var reasonSelect = document.getElementById('reason');
                if (reasonSelect) {
                    reasonSelect.focus();
                    // Auto-open the select dropdown
                    try {
                        reasonSelect.showPicker();
                    } catch(err) {
                        var event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
                        reasonSelect.dispatchEvent(event);
                    }
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle Reason select Enter → jump to checked Party Type radio and trigger it ----
        if (activeEl.id === 'reason') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="credit_party_type"]:checked');
                if (checkedRadio) {
                    checkedRadio.focus();
                    checkedRadio.click();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Supplier/Customer radio Enter → jump to Party Name search ----
        if (activeEl.id === 'partySupplier' || activeEl.id === 'partyCustomer') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var partySearch = document.getElementById('partySearchInput');
                if (partySearch) {
                    partySearch.focus();
                    partySearch.select();
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle Salesman select Enter → jump to Account Type radio ----
        if (activeEl.id === 'salesmanSelect') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="debit_account_type"]:checked');
                if (checkedRadio) {
                    checkedRadio.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Account Type radio Enter → jump to Account No ----
        if (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var accountNo = document.getElementById('accountNo');
                if (accountNo) {
                    accountNo.focus();
                    accountNo.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Account No Enter → jump to Inv. Ref. No. ----
        if (activeEl.id === 'accountNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var invRefNo = document.getElementById('invRefNo');
                if (invRefNo) {
                    invRefNo.focus();
                    invRefNo.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Inv. Ref. No. Enter → jump to Invoice Date ----
        if (activeEl.id === 'invRefNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var invoiceDate = document.getElementById('invoiceDate');
                if (invoiceDate) {
                    invoiceDate.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Invoice Date Enter → jump to GST Vno. ----
        if (activeEl.id === 'invoiceDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var gstVno = document.getElementById('gstVno');
                if (gstVno) {
                    gstVno.focus();
                    gstVno.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle GST Vno. Enter → jump to Party Trn. No. ----
        if (activeEl.id === 'gstVno') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var partyTrnNo = document.getElementById('partyTrnNo');
                if (partyTrnNo) {
                    partyTrnNo.focus();
                    partyTrnNo.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Party Trn. No. Enter → jump to Party Trn Date ----
        if (activeEl.id === 'partyTrnNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var partyTrnDate = document.getElementById('partyTrnDate');
                if (partyTrnDate) {
                    partyTrnDate.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Party Trn Date Enter → jump to Amount ----
        if (activeEl.id === 'partyTrnDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var amount = document.getElementById('amount');
                if (amount) {
                    amount.focus();
                    amount.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Amount field Enter → trigger Add Row ----
        if (activeEl.id === 'amount') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Trigger Add Row
            setTimeout(function() {
                addHsnRow();
            }, 30);
            
            return false;
        }
        
        // ---- Handle Narration → allow normal textarea behavior ----
        if (activeEl.id === 'narration') {
            return; // Allow normal textarea Enter (newline)
        }
        
        // ---- Handle TCS Amount Enter → jump to Round Off ----
        if (activeEl.id === 'tcsAmount') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var roundOff = document.getElementById('roundOff');
                if (roundOff) {
                    roundOff.focus();
                    roundOff.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Round Off Enter → jump to Update button ----
        if (activeEl.id === 'roundOff') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var updateBtn = document.getElementById('updateBtn');
                if (updateBtn && !updateBtn.disabled) {
                    updateBtn.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle HSN row HSN Code field Enter → jump to Amount in same row ----
        if (activeEl.name && activeEl.name.match(/items\[\d+\]\[hsn_code\]/)) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var row = activeEl.closest('tr');
            if (row) {
                var amountField = row.querySelector('.hsn-amount');
                if (amountField) {
                    amountField.focus();
                    amountField.select();
                }
            }
            return false;
        }
        
        // ---- Handle HSN row Amount field Enter → jump to GST% in same row ----
        if (activeEl.classList.contains('hsn-amount')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var row = activeEl.closest('tr');
            if (row) {
                var gstField = row.querySelector('.hsn-gst');
                if (gstField) {
                    gstField.focus();
                    gstField.select();
                }
            }
            return false;
        }
        
        // ---- Handle HSN row GST% field Enter → trigger Add Row ----
        if (activeEl.classList.contains('hsn-gst')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Trigger GST calculation for current row first
            var row = activeEl.closest('tr');
            if (row) {
                var rowIndex = row.getAttribute('data-row');
                if (rowIndex !== null) {
                    calculateGst(parseInt(rowIndex));
                }
            }
            
            // Then add a new row
            setTimeout(function() {
                addHsnRow();
            }, 50);
            
            return false;
        }
        
        // ---- General: Handle regular text/number inputs with Enter ----
        if (activeEl.tagName === 'INPUT' && (activeEl.type === 'text' || activeEl.type === 'number' || activeEl.type === 'date')) {
            e.preventDefault();
            e.stopPropagation();
            
            navigateField(activeEl, 1);
            return false;
        }
        
    }, true); // CAPTURE PHASE
    
    
    // ==============================================
    // Auto-advance on Reason Select change
    // ==============================================
    var reasonSelect = document.getElementById('reason');
    if (reasonSelect) {
        reasonSelect.addEventListener('change', function() {
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="credit_party_type"]:checked');
                if (checkedRadio) {
                    checkedRadio.focus();
                }
            }, 30);
        });
    }
    
    // ==============================================
    // Auto-advance on Salesman Select change
    // ==============================================
    var salesmanSelect = document.getElementById('salesmanSelect');
    if (salesmanSelect) {
        salesmanSelect.addEventListener('change', function() {
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="debit_account_type"]:checked');
                if (checkedRadio) {
                    checkedRadio.focus();
                }
            }, 30);
        });
    }
}

// ============================================================
// FIELD NAVIGATION HELPER
// ============================================================
function navigateField(currentElement, direction) {
    var currentIndex = -1;
    var currentId = currentElement.id;

    // Map radio buttons to their group representative in FIELD_ORDER
    if (currentElement.type === 'radio') {
        if (currentElement.name === 'credit_party_type') {
            currentIndex = FIELD_ORDER.indexOf('partySupplier');
        } else if (currentElement.name === 'debit_account_type') {
            currentIndex = FIELD_ORDER.indexOf('accountPurchase');
        }
    } else {
        currentIndex = FIELD_ORDER.indexOf(currentId);
    }

    if (currentIndex === -1) return;

    var nextIndex = currentIndex + direction;

    if (nextIndex < 0) return;
    if (nextIndex >= FIELD_ORDER.length) {
        var updateBtn = document.getElementById('updateBtn');
        if (updateBtn && !updateBtn.disabled) {
            updateBtn.focus();
        }
        return;
    }

    var nextId = FIELD_ORDER[nextIndex];
    var nextElement = document.getElementById(nextId);

    // Handle radio groups: focus the checked radio
    if (nextId === 'partySupplier' || nextId === 'accountPurchase') {
        var groupName = (nextId === 'partySupplier') ? 'credit_party_type' : 'debit_account_type';
        var checkedRadio = document.querySelector('input[name="' + groupName + '"]:checked');
        if (checkedRadio) nextElement = checkedRadio;
    }
    
    // Handle custom party search input
    if (nextId === 'partySearchInput') {
        var partySearch = document.getElementById('partySearchInput');
        if (partySearch) {
            partySearch.focus();
            partySearch.select();
        }
        return;
    }
    
    // Handle Update button
    if (nextId === 'updateBtn') {
        var updateBtn = document.getElementById('updateBtn');
        if (updateBtn && !updateBtn.disabled) {
            updateBtn.focus();
        }
        return;
    }

    if (!nextElement) {
        // Skip to next field if element not found
        navigateField({ id: nextId, type: 'text', name: '' }, direction);
        return;
    }

    // Skip disabled, hidden, or readonly fields
    if (nextElement.disabled || nextElement.offsetParent === null) {
        navigateField({ id: nextId, type: nextElement.type, name: nextElement.name }, direction);
        return;
    }

    if (nextElement.readOnly && nextElement.tagName !== 'SELECT' && nextElement.tagName !== 'TEXTAREA') {
        navigateField({ id: nextId, type: nextElement.type, name: nextElement.name }, direction);
        return;
    }

    nextElement.focus();

    // Select text for input fields
    if (nextElement.tagName === 'INPUT' && nextElement.select) {
        nextElement.select();
    }
    
    // Auto-open native selects
    if (nextElement.tagName === 'SELECT') {
        try {
            nextElement.showPicker();
        } catch(err) {
            var event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
            nextElement.dispatchEvent(event);
        }
    }
}

// ============================================================
// BROWSE CREDIT NOTES MODAL - KEYBOARD NAVIGATION
// ============================================================
function initBrowseModalKeyboard() {
    var browseSelectedIndex = -1;
    
    document.addEventListener('keydown', function(e) {
        var creditNotesModal = document.getElementById('creditNotesModal');
        if (!creditNotesModal || !creditNotesModal.classList.contains('show')) return;
        
        var tbody = document.getElementById('creditNotesModalBody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        // Filter out non-data rows (loading, empty messages)
        rows = rows.filter(function(row) {
            return row.querySelector('button') !== null;
        });
        
        // Arrow Down → next row
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Blur search input
            var searchInput = document.getElementById('modalSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (browseSelectedIndex < rows.length - 1) {
                browseSelectedIndex++;
            } else if (browseSelectedIndex === -1) {
                browseSelectedIndex = 0;
            }
            highlightBrowseRow(rows, browseSelectedIndex);
            return;
        }
        
        // Arrow Up → previous row
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var searchInput = document.getElementById('modalSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (browseSelectedIndex > 0) {
                browseSelectedIndex--;
            } else if (browseSelectedIndex === -1 && rows.length > 0) {
                browseSelectedIndex = 0;
            }
            highlightBrowseRow(rows, browseSelectedIndex);
            return;
        }
        
        // Enter → select highlighted row
        if (e.key === 'Enter') {
            var searchInput = document.getElementById('modalSearchInput');
            if (document.activeElement === searchInput && browseSelectedIndex === -1) {
                return; // Let search work normally
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (!rows.length) return;
            
            if (browseSelectedIndex === -1 && rows.length > 0) {
                browseSelectedIndex = 0;
                highlightBrowseRow(rows, browseSelectedIndex);
                return;
            }
            
            if (browseSelectedIndex >= 0 && browseSelectedIndex < rows.length) {
                var selectBtn = rows[browseSelectedIndex].querySelector('button');
                if (selectBtn) selectBtn.click();
            }
            return;
        }
        
        // Escape → close modal
        if (e.key === 'Escape') {
            e.preventDefault();
            closeCreditNotesModal();
            browseSelectedIndex = -1;
            return;
        }
        
        // Any letter/number key → refocus search input
        if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
            var searchInput = document.getElementById('modalSearchInput');
            if (searchInput && document.activeElement !== searchInput) {
                searchInput.focus();
                browseSelectedIndex = -1;
                highlightBrowseRow(rows, -1);
            }
        }
    }, true);
    
    function highlightBrowseRow(rows, index) {
        rows.forEach(function(row) {
            row.style.backgroundColor = '';
            row.style.fontWeight = '';
            row.classList.remove('table-active');
        });
        if (index >= 0 && index < rows.length) {
            rows[index].style.backgroundColor = '#cce5ff';
            rows[index].style.fontWeight = 'bold';
            rows[index].classList.add('table-active');
            rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    
    // Reset selection when modal opens
    var creditNotesModalEl = document.getElementById('creditNotesModal');
    if (creditNotesModalEl) {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (creditNotesModalEl.classList.contains('show')) {
                        browseSelectedIndex = -1;
                        setTimeout(function() {
                            var searchInput = document.getElementById('modalSearchInput');
                            if (searchInput) searchInput.focus();
                        }, 150);
                    } else {
                        browseSelectedIndex = -1;
                    }
                }
            });
        });
        observer.observe(creditNotesModalEl, { attributes: true, attributeFilter: ['class'] });
    }
}

// ============================================================
// ADJUSTMENT MODAL - KEYBOARD NAVIGATION
// ============================================================
function initAdjustmentModalKeyboard() {
    var adjustmentModal = document.getElementById('adjustmentModal');
    if (!adjustmentModal) return;
    
    document.addEventListener('keydown', function(e) {
        if (!adjustmentModal.classList.contains('show')) return;
        
        var activeEl = document.activeElement;
        
        // Escape → close
        if (e.key === 'Escape') {
            e.preventDefault();
            closeAdjustmentModal();
            return;
        }
        
        // Handle adjustment inputs navigation
        if (activeEl && activeEl.classList.contains('adjustment-input')) {
            var inputs = Array.from(document.querySelectorAll('.adjustment-input'));
            var currentIdx = inputs.indexOf(activeEl);
            
            if (e.key === 'ArrowDown' || (e.key === 'Enter' && !e.shiftKey)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (currentIdx >= 0 && currentIdx < inputs.length - 1) {
                    inputs[currentIdx + 1].focus();
                    inputs[currentIdx + 1].select();
                } else {
                    // Last input → focus Save button
                    var saveBtn = adjustmentModal.querySelector('.btn-success');
                    if (saveBtn) saveBtn.focus();
                }
                return;
            }
            
            if (e.key === 'ArrowUp' || (e.key === 'Enter' && e.shiftKey)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (currentIdx > 0) {
                    inputs[currentIdx - 1].focus();
                    inputs[currentIdx - 1].select();
                }
                return;
            }
        }
        
        // Handle button Enter
        if (activeEl && activeEl.tagName === 'BUTTON' && adjustmentModal.contains(activeEl)) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                activeEl.click();
                return;
            }
        }
    }, true);
    
    // Auto-focus first input when modal opens
    var adjustObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                if (adjustmentModal.classList.contains('show')) {
                    setTimeout(function() {
                        var firstInput = document.querySelector('.adjustment-input');
                        if (firstInput) {
                            firstInput.focus();
                            firstInput.select();
                        }
                    }, 300);
                }
            }
        });
    });
    adjustObserver.observe(adjustmentModal, { attributes: true, attributeFilter: ['class'] });
}

// --- Custom Party Search Logic ---
function initPartySearch() {
    const searchInput = document.getElementById('partySearchInput');
    const hiddenInput = document.getElementById('partySelect');
    const resultsContainer = document.getElementById('partySearchResults');

    // Input Handler
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        hiddenInput.value = ''; // Clear ID while typing
        
        if (query.length === 0) {
            resultsContainer.style.display = 'none';
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchParties(query);
        }, 300);
    });

    // Focus Handler - trigger search on focus
    searchInput.addEventListener('focus', function() {
        fetchParties(this.value.trim());
    });

    // Handle clicks outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    // Keyboard navigation within search results
    searchInput.addEventListener('keydown', function(e) {
        if (resultsContainer.style.display === 'block') {
            const items = resultsContainer.querySelectorAll('.list-group-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentFocusIndex++;
                if (currentFocusIndex >= items.length) currentFocusIndex = 0;
                highlightItem(items, currentFocusIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocusIndex--;
                if (currentFocusIndex < 0) currentFocusIndex = items.length - 1;
                highlightItem(items, currentFocusIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (currentFocusIndex > -1 && items[currentFocusIndex]) {
                    items[currentFocusIndex].click();
                }
            }
        }
    });
}

function fetchParties(query) {
    const searchUrl = '{{ route("admin.credit-note.search-parties") }}';
    const resultsContainer = document.getElementById('partySearchResults');
    
    resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="bi bi-hourglass-split"></i> Searching...</div>';
    resultsContainer.style.display = 'block';

    fetch(`${searchUrl}?q=${encodeURIComponent(query)}&party_type=${currentPartyType}&page=1`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        renderPartyResults(data.results || []);
    })
    .catch(error => {
        console.error('Error fetching parties:', error);
        resultsContainer.innerHTML = '<div class="p-2 text-danger">Error loading results</div>';
    });
}

function renderPartyResults(results) {
    const resultsContainer = document.getElementById('partySearchResults');
    resultsContainer.innerHTML = '';
    currentFocusIndex = -1;

    if (results.length === 0) {
        resultsContainer.innerHTML = '<div class="p-2 text-muted">No results found</div>';
        return;
    }

    results.forEach((party, index) => {
        const item = document.createElement('a');
        item.href = '#';
        item.className = 'list-group-item list-group-item-action';
        item.textContent = party.text || party.name;
        item.dataset.id = party.id;
        item.dataset.name = party.name;

        item.addEventListener('click', function(e) {
            e.preventDefault();
            selectParty(party);
        });

        resultsContainer.appendChild(item);
    });
}

function selectParty(party) {
    document.getElementById('partySearchInput').value = party.text || party.name;
    document.getElementById('partySelect').value = party.id;
    document.getElementById('partySearchResults').style.display = 'none';
    window.selectedPartyName = party.text || party.name;
    
    // After selecting party, move cursor to Salesman field and open dropdown
    setTimeout(function() {
        var salesmanSelect = document.getElementById('salesmanSelect');
        if (salesmanSelect) {
            salesmanSelect.focus();
            try {
                salesmanSelect.showPicker();
            } catch(err) {
                var event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
                salesmanSelect.dispatchEvent(event);
            }
        }
    }, 50);
}

function highlightItem(items, index) {
    items.forEach(item => item.classList.remove('active'));
    if (items[index]) {
        items[index].classList.add('active');
        items[index].scrollIntoView({ block: 'nearest' });
    }
}

function updateDayName() {
    const dateInput = document.getElementById('creditNoteDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

function updatePartyDropdown() {
    const searchInput = document.getElementById('partySearchInput');
    const hiddenInput = document.getElementById('partySelect');
    
    searchInput.value = '';
    hiddenInput.value = '';
    document.getElementById('partySearchResults').style.display = 'none';
    
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    
    const helpText = document.querySelector('.party-search-container + small');
    if (helpText) {
        helpText.textContent = partyType === 'S' ? 'Start typing to search for suppliers' : 'Start typing to search for customers';
    }
    searchInput.placeholder = partyType === 'S' ? 'Search supplier...' : 'Search customer...';
}

function searchCreditNote() {
    const creditNoteNo = document.getElementById('searchCreditNoteNo').value.trim();
    if (!creditNoteNo) {
        alert('Please enter Credit Note No.');
        return;
    }
    
    fetch(`{{ url('admin/credit-note/fetch') }}/${encodeURIComponent(creditNoteNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.credit_note) {
                populateCreditNoteData(data.credit_note);
            } else {
                alert(data.message || 'Credit note not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching credit note');
        });
}

function populateCreditNoteData(cn) {
    console.log('Populating credit note data:', cn);
    
    // Helper function to format date for input[type=date]
    function formatDateForInput(dateStr) {
        if (!dateStr) return '';
        // Handle ISO format (2025-12-03T18:30:00.000000Z)
        if (dateStr.includes('T')) {
            return dateStr.split('T')[0];
        }
        // Handle format like "2025-12-03"
        if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return dateStr;
        }
        // Try to parse and format
        try {
            const date = new Date(dateStr);
            return date.toISOString().split('T')[0];
        } catch (e) {
            console.error('Date parsing error:', e, dateStr);
            return '';
        }
    }
    
    document.getElementById('creditNoteId').value = cn.id;
    document.getElementById('creditNoteNo').value = cn.credit_note_no;
    
    const formattedDate = formatDateForInput(cn.credit_note_date);
    console.log('Credit note date:', cn.credit_note_date, '-> formatted:', formattedDate);
    document.getElementById('creditNoteDate').value = formattedDate;
    updateDayName();
    
    // Set party type
    currentPartyType = cn.credit_party_type || 'S';
    console.log('Party type:', currentPartyType, 'Party ID:', cn.credit_party_id, 'Party Name:', cn.credit_party_name);
    
    if (cn.credit_party_type === 'C') {
        document.getElementById('partyCustomer').checked = true;
    } else {
        document.getElementById('partySupplier').checked = true;
    }
    
    // Pre-select the party in custom search input
    if (cn.credit_party_id && cn.credit_party_name) {
        document.getElementById('partySearchInput').value = cn.credit_party_name;
        document.getElementById('partySelect').value = cn.credit_party_id;
        window.selectedPartyName = cn.credit_party_name;
        console.log('Pre-selected party:', cn.credit_party_name, cn.credit_party_id);
    }
    
    // Set salesman and reason
    if (cn.salesman_id) {
        document.getElementById('salesmanSelect').value = cn.salesman_id;
        console.log('Pre-selected salesman:', cn.salesman_id);
    }
    if (cn.reason) {
        document.getElementById('reason').value = cn.reason;
        console.log('Pre-selected reason:', cn.reason);
    }
    
    // Debit account
    if (cn.debit_account_type === 'S') {
        document.getElementById('accountSale').checked = true;
    } else if (cn.debit_account_type === 'G') {
        document.getElementById('accountGeneral').checked = true;
    } else {
        document.getElementById('accountPurchase').checked = true;
    }
    
    document.getElementById('accountNo').value = cn.debit_account_no || '';
    document.getElementById('invRefNo').value = cn.inv_ref_no || '';
    document.getElementById('invoiceDate').value = formatDateForInput(cn.invoice_date);
    document.getElementById('gstVno').value = cn.gst_vno || '';
    document.getElementById('partyTrnNo').value = cn.party_trn_no || '';
    document.getElementById('partyTrnDate').value = formatDateForInput(cn.party_trn_date);
    document.getElementById('amount').value = cn.amount || 0;
    
    // Summary
    document.getElementById('grossAmount').value = cn.gross_amount || 0;
    document.getElementById('totalGst').value = cn.total_gst || 0;
    document.getElementById('netAmount').value = cn.net_amount || 0;
    document.getElementById('tcsAmount').value = cn.tcs_amount || 0;
    document.getElementById('roundOff').value = cn.round_off || 0;
    document.getElementById('cnAmount').value = cn.cn_amount || 0;
    document.getElementById('narration').value = cn.narration || '';
    
    // Load items
    const tbody = document.getElementById('hsnTableBody');
    tbody.innerHTML = '';
    hsnRowCount = 0;
    
    console.log('Credit note items:', cn.items);
    
    if (cn.items && cn.items.length > 0) {
        cn.items.forEach((item, index) => {
            addHsnRowWithData(item);
        });
    } else {
        addHsnRow();
    }
    
    document.getElementById('updateBtn').disabled = false;
    document.getElementById('deleteCreditNoteBtn').style.display = 'inline-block';
    document.getElementById('adjustmentBtn').style.display = 'inline-block';
    
    console.log('Credit note data populated successfully');
}

function addHsnRow() {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${hsnRowCount}][hsn_code]"></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="0.00" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    hsnRowCount++;
    
    // Auto-focus the first field (HSN Code) of the newly added row
    setTimeout(function() {
        var firstInput = row.querySelector('input[type="text"]');
        if (firstInput) {
            firstInput.focus();
            firstInput.select();
        }
    }, 50);
}

function addHsnRowWithData(item) {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${hsnRowCount}][hsn_code]" value="${item.hsn_code || ''}"></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="${item.amount || 0}" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="${item.gst_percent || 0}" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="${item.cgst_percent || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="${item.cgst_amount || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="${item.sgst_percent || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="${item.sgst_amount || 0}" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    hsnRowCount++;
}

function deleteHsnRow(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function calculateGst(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPercent = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    
    const cgstPercent = gstPercent / 2;
    const sgstPercent = gstPercent / 2;
    const cgstAmount = (amount * cgstPercent) / 100;
    const sgstAmount = (amount * sgstPercent) / 100;
    
    row.querySelector('.hsn-cgst-percent').value = cgstPercent.toFixed(2);
    row.querySelector('.hsn-cgst-amount').value = cgstAmount.toFixed(2);
    row.querySelector('.hsn-sgst-percent').value = sgstPercent.toFixed(2);
    row.querySelector('.hsn-sgst-amount').value = sgstAmount.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let grossAmount = 0;
    let totalGst = 0;
    
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        const cgstAmount = parseFloat(row.querySelector('.hsn-cgst-amount')?.value) || 0;
        const sgstAmount = parseFloat(row.querySelector('.hsn-sgst-amount')?.value) || 0;
        
        grossAmount += amount;
        totalGst += cgstAmount + sgstAmount;
    });
    
    const tcsAmount = parseFloat(document.getElementById('tcsAmount').value) || 0;
    const roundOff = parseFloat(document.getElementById('roundOff').value) || 0;
    
    const netAmount = grossAmount + totalGst;
    const cnAmount = netAmount + tcsAmount + roundOff;
    
    document.getElementById('grossAmount').value = grossAmount.toFixed(2);
    document.getElementById('totalGst').value = totalGst.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('cnAmount').value = cnAmount.toFixed(2);
}

function updateCreditNote() {
    const creditNoteId = document.getElementById('creditNoteId').value;
    if (!creditNoteId) {
        alert('Please load a credit note first');
        return;
    }
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('input[name*="hsn_code"]')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        
        if (hsnCode || parseFloat(amount) > 0) {
            items.push({
                hsn_code: hsnCode,
                amount: amount,
                gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-percent')?.value || 0,
                cgst_amount: row.querySelector('.hsn-cgst-amount')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-percent')?.value || 0,
                sgst_amount: row.querySelector('.hsn-sgst-amount')?.value || 0,
            });
        }
    });
    
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    // Get party name from custom search input
    const partyName = document.getElementById('partySearchInput').value || window.selectedPartyName || '';
    
    const data = {
        header: {
            credit_note_date: document.getElementById('creditNoteDate').value,
            day_name: document.getElementById('dayName').value,
            credit_party_type: partyType,
            credit_party_id: document.getElementById('partySelect').value,
            credit_party_name: partyName,
            debit_account_type: document.querySelector('input[name="debit_account_type"]:checked').value,
            debit_account_no: document.getElementById('accountNo').value,
            inv_ref_no: document.getElementById('invRefNo').value,
            invoice_date: document.getElementById('invoiceDate').value || null,
            gst_vno: document.getElementById('gstVno').value,
            party_trn_no: document.getElementById('partyTrnNo').value,
            party_trn_date: document.getElementById('partyTrnDate').value || null,
            amount: document.getElementById('amount').value,
            salesman_id: document.getElementById('salesmanSelect').value || null,
            reason: document.getElementById('reason').value,
            gross_amount: document.getElementById('grossAmount').value,
            total_gst: document.getElementById('totalGst').value,
            net_amount: document.getElementById('netAmount').value,
            tcs_amount: document.getElementById('tcsAmount').value,
            round_off: document.getElementById('roundOff').value,
            cn_amount: document.getElementById('cnAmount').value,
            narration: document.getElementById('narration').value,
        },
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/credit-note') }}/${creditNoteId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.href = '{{ route("admin.credit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating credit note');
    });
}

function deleteCreditNote() {
    const creditNoteId = document.getElementById('creditNoteId').value;
    if (!creditNoteId) return;
    
    if (!confirm('Are you sure you want to delete this credit note?')) return;
    
    fetch(`{{ url('admin/credit-note') }}/${creditNoteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.href = '{{ route("admin.credit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting credit note');
    });
}

let allCreditNotes = [];

function openCreditNotesModal() {
    document.getElementById('creditNotesModalBackdrop').classList.add('show');
    document.getElementById('creditNotesModal').classList.add('show');
    
    document.getElementById('modalSearchInput').value = '';
    document.getElementById('creditNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    
    // Fetch all credit notes
    fetch('{{ route("admin.credit-note.invoices") }}?all=1', {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            allCreditNotes = data.data;
        } else if (Array.isArray(data)) {
            allCreditNotes = data;
        } else {
            allCreditNotes = [];
        }
        renderCreditNotesInModal(allCreditNotes);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('creditNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
    });
}

function closeCreditNotesModal() {
    document.getElementById('creditNotesModalBackdrop').classList.remove('show');
    document.getElementById('creditNotesModal').classList.remove('show');
}

function renderCreditNotesInModal(creditNotes) {
    const tbody = document.getElementById('creditNotesModalBody');
    
    if (!creditNotes || creditNotes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No credit notes found</td></tr>';
        return;
    }
    
    tbody.innerHTML = creditNotes.map(cn => `
        <tr>
            <td><strong>${cn.credit_note_no || ''}</strong></td>
            <td>${cn.credit_note_date ? new Date(cn.credit_note_date).toLocaleDateString('en-IN') : ''}</td>
            <td>${cn.credit_party_name || ''}</td>
            <td class="text-end">₹ ${parseFloat(cn.net_amount || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick="selectCreditNoteFromModal('${cn.credit_note_no}')">
                    <i class="bi bi-check"></i> Select
                </button>
            </td>
        </tr>
    `).join('');
}

function searchCreditNotesInModal() {
    const searchTerm = document.getElementById('modalSearchInput').value.toLowerCase().trim();
    
    if (!searchTerm) {
        renderCreditNotesInModal(allCreditNotes);
        return;
    }
    
    const filtered = allCreditNotes.filter(cn => {
        const cnNo = (cn.credit_note_no || '').toString().toLowerCase();
        const partyName = (cn.credit_party_name || '').toLowerCase();
        return cnNo.includes(searchTerm) || partyName.includes(searchTerm);
    });
    
    renderCreditNotesInModal(filtered);
}

function selectCreditNoteFromModal(creditNoteNo) {
    // Close modal
    closeCreditNotesModal();
    
    // Set the credit note number and search
    document.getElementById('searchCreditNoteNo').value = creditNoteNo;
    searchCreditNote();
    
    // After selection, move cursor to Date field
    setTimeout(function() {
        var dateField = document.getElementById('creditNoteDate');
        if (dateField) {
            dateField.focus();
        }
    }, 300);
}

// ============ ADJUSTMENT MODAL FUNCTIONS ============
let allInvoices = [];
let existingAdjustments = [];
let cnAmount = 0;

function openAdjustmentModal() {
    const creditNoteId = document.getElementById('creditNoteId').value;
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    
    if (!creditNoteId || !partyId) {
        alert('Please load a credit note first');
        return;
    }
    
    cnAmount = parseFloat(document.getElementById('cnAmount').value) || 0;
    
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
    document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="6" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    document.getElementById('autoAdjustAmount').value = cnAmount.toFixed(2);
    
    // Fetch invoices and existing adjustments
    Promise.all([
        fetch('{{ route("admin.credit-note.party-invoices") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ party_id: partyId, party_type: partyType })
        }).then(r => r.json()),
        fetch(`{{ url('admin/credit-note') }}/${creditNoteId}/adjustments`, {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json())
    ])
    .then(([invoicesData, adjustmentsData]) => {
        if (invoicesData.success) {
            allInvoices = invoicesData.invoices || [];
        } else {
            allInvoices = [];
        }
        
        if (adjustmentsData.success) {
            existingAdjustments = adjustmentsData.adjustments || [];
        } else {
            existingAdjustments = [];
        }
        
        renderAdjustmentTable();
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
    });
    
    // ESC key listener
    document.addEventListener('keydown', handleAdjustmentEsc);
}

function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') {
        closeAdjustmentModal();
    }
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
    document.removeEventListener('keydown', handleAdjustmentEsc);
}

function renderAdjustmentTable() {
    const tbody = document.getElementById('adjustmentModalBody');
    
    // Merge invoices with existing adjustments
    const mergedInvoices = [];
    
    // First add invoices that have existing adjustments
    existingAdjustments.forEach(adj => {
        mergedInvoices.push({
            id: adj.invoice_id,
            trans_no: adj.trans_no || adj.invoice_no || '',
            date: adj.date || '',
            bill_amount: parseFloat(adj.bill_amount || 0),
            balance: parseFloat(adj.balance || 0) + parseFloat(adj.adjusted_amount || 0), // Add back the adjusted amount
            adjusted_amount: parseFloat(adj.adjusted_amount || 0),
            is_existing: true
        });
    });
    
    // Then add remaining invoices
    allInvoices.forEach(inv => {
        const existingAdj = existingAdjustments.find(a => a.invoice_id == inv.id);
        if (!existingAdj) {
            mergedInvoices.push({
                id: inv.id,
                trans_no: inv.trans_no || '',
                date: inv.date || '',
                bill_amount: parseFloat(inv.bill_amount || 0),
                balance: parseFloat(inv.balance || 0),
                adjusted_amount: 0,
                is_existing: false
            });
        }
    });
    
    if (mergedInvoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No invoices found</td></tr>';
        updateAdjustmentBalance();
        return;
    }
    
    tbody.innerHTML = mergedInvoices.map((inv, index) => `
        <tr class="${inv.is_existing ? 'table-success' : ''}">
            <td class="text-center">${index + 1}</td>
            <td><strong>${inv.trans_no}</strong></td>
            <td>${inv.date}</td>
            <td class="text-end text-primary fw-bold">₹ ${inv.bill_amount.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm adjustment-input" 
                       id="adj_${inv.id}" 
                       data-invoice-id="${inv.id}"
                       data-balance="${inv.balance}"
                       data-original-adjusted="${inv.adjusted_amount}"
                       value="${inv.adjusted_amount.toFixed(2)}" 
                       min="0" 
                       max="${inv.balance}"
                       step="0.01"
                       onchange="updateAdjustmentBalance()"
                       oninput="updateAdjustmentBalance()"
                       style="width: 100px; text-align: right;">
            </td>
            <td class="text-end" id="balance_${inv.id}">
                <span class="text-success">₹ ${(inv.balance - inv.adjusted_amount).toFixed(2)}</span>
            </td>
        </tr>
    `).join('');
    
    updateAdjustmentBalance();
}

function updateAdjustmentBalance() {
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        const balance = parseFloat(input.dataset.balance) || 0;
        const invoiceId = input.dataset.invoiceId;
        
        // Validate max
        if (value > balance) {
            input.value = balance.toFixed(2);
        }
        
        totalAdjusted += parseFloat(input.value) || 0;
        
        // Update balance display
        const balanceEl = document.getElementById(`balance_${invoiceId}`);
        if (balanceEl) {
            const remaining = balance - (parseFloat(input.value) || 0);
            balanceEl.innerHTML = `<span class="text-success">₹ ${remaining.toFixed(2)}</span>`;
        }
    });
    
    const remaining = cnAmount - totalAdjusted;
    document.getElementById('adjustmentBalance').textContent = remaining.toFixed(2);
    
    // Change color based on remaining
    const balanceSpan = document.getElementById('adjustmentBalance');
    if (remaining < 0) {
        balanceSpan.classList.remove('text-primary', 'text-success');
        balanceSpan.classList.add('text-danger');
    } else if (remaining === 0) {
        balanceSpan.classList.remove('text-primary', 'text-danger');
        balanceSpan.classList.add('text-success');
    } else {
        balanceSpan.classList.remove('text-danger', 'text-success');
        balanceSpan.classList.add('text-primary');
    }
}

function autoDistributeAmount() {
    let amountToDistribute = parseFloat(document.getElementById('autoAdjustAmount').value) || 0;
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        if (amountToDistribute <= 0) {
            input.value = '0.00';
            return;
        }
        
        const balance = parseFloat(input.dataset.balance) || 0;
        const toAdjust = Math.min(amountToDistribute, balance);
        
        input.value = toAdjust.toFixed(2);
        amountToDistribute -= toAdjust;
    });
    
    updateAdjustmentBalance();
}

function saveAdjustments() {
    const creditNoteId = document.getElementById('creditNoteId').value;
    const adjustments = [];
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        const adjustedAmount = parseFloat(input.value) || 0;
        if (adjustedAmount > 0) {
            adjustments.push({
                invoice_id: input.dataset.invoiceId,
                adjusted_amount: adjustedAmount
            });
        }
    });
    
    // Save adjustments
    fetch(`{{ url('admin/credit-note') }}/${creditNoteId}/save-adjustments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ adjustments: adjustments })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message || 'Adjustments saved successfully');
            closeAdjustmentModal();
        } else {
            alert('Error: ' + (result.message || 'Failed to save adjustments'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving adjustments');
    });
}
</script>

<style>
.readonly-field {
    background-color: #e9ecef !important;
    cursor: not-allowed;
}

/* Custom Modal Styles */
.custom-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
}

.custom-modal-backdrop.show {
    display: block;
}

.custom-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 700px;
    max-width: 95%;
    max-height: 90vh;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 1060;
    overflow: hidden;
}

.custom-modal.show {
    display: block;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    color: white;
}

.custom-modal-header h5 {
    margin: 0;
    font-size: 1.1rem;
}

.custom-modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.custom-modal-close:hover {
    opacity: 0.8;
}

.custom-modal-body {
    padding: 20px;
    max-height: calc(90vh - 60px);
    overflow-y: auto;
}
</style>
@endpush
