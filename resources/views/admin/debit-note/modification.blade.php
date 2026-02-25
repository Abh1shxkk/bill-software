@extends('layouts.admin')

@section('title', 'Debit Note Modification')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Debit Note Modification</h4>
        <div class="text-muted small">Edit existing debit note</div>
    </div>
    <div>
        <a href="{{ route('admin.debit-note.invoices') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
        <a href="{{ route('admin.debit-note.transaction') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New
        </a>
    </div>
</div>

<!-- Search Section -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="searchDebitNoteNo" class="form-label">Debit Note No.</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchDebitNoteNo" placeholder="Enter Debit Note No." 
                           value="{{ $preloadDebitNoteNo ?? '' }}">
                    <button class="btn btn-danger" type="button" onclick="searchDebitNote()" id="searchBtn">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-info w-100" type="button" id="browseBtn" onclick="openDebitNotesModal()">
                    <i class="bi bi-list-ul me-1"></i> Browse
                </button>
            </div>
        </div>
    </div>
</div>

<form id="debitNoteForm" autocomplete="off">
    @csrf
    
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light py-2 border-bottom">
            <h6 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i> Debit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="debitNoteDate" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="debitNoteDate" name="debit_note_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly tabindex="-1">
                </div>
                <div class="col-md-2">
                    <label for="debitNoteNo" class="form-label">Debit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="debitNoteNo" readonly tabindex="-1">
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
        <div class="card-header bg-light py-2 border-bottom">
            <h6 class="mb-0 text-dark"><i class="bi bi-people me-2"></i> Party Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Debit Section (Left) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-dark mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Party)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="debit_party_type" id="partySupplier" value="S" checked>
                                    <label class="btn btn-outline-secondary" for="partySupplier">Supplier</label>
                                    <input type="radio" class="btn-check" name="debit_party_type" id="partyCustomer" value="C">
                                    <label class="btn btn-outline-secondary" for="partyCustomer">Customer</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="partySearchInput" class="form-label">Party Name <span class="text-danger">*</span></label>
                                <div class="party-search-container" style="position: relative;">
                                    <input type="text" class="form-control" id="partySearchInput" placeholder="Search supplier..." autocomplete="off">
                                    <input type="hidden" id="partySelect" name="debit_party_id">
                                    <div id="partySearchResults" class="list-group" style="display:none; position:absolute; z-index:1050; width:100%; max-height:250px; overflow-y:auto; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 0 0 8px 8px;"></div>
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

                <!-- Credit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-dark mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Credit (Account)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Account Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountPurchase" value="P" checked>
                                    <label class="btn btn-outline-secondary" for="accountPurchase">Purchase</label>
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountSale" value="S">
                                    <label class="btn btn-outline-secondary" for="accountSale">Sale</label>
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountGeneral" value="G">
                                    <label class="btn btn-outline-secondary" for="accountGeneral">General</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control" id="accountNo" name="credit_account_no">
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
        <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-dark"><i class="bi bi-table me-2"></i> HSN Details</h6>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="insertHsnBtn" onclick="openHsnModal()">
                <i class="bi bi-plus-circle me-1"></i> Insert
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
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 text-dark"><i class="bi bi-chat-text me-2"></i> Narration</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter narration..."></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 text-dark"><i class="bi bi-calculator me-2"></i> Summary</h6>
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
                            <label class="form-label small fw-bold text-danger">DN Amount</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-danger readonly-field" id="dnAmount" name="dn_amount" value="0.00" readonly tabindex="-1" style="font-size: 16px;">
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
                    <button type="button" class="btn btn-secondary" id="cancelBtn" onclick="window.location.href='{{ route('admin.debit-note.invoices') }}'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteDebitNoteBtn" style="display: none;" onclick="deleteDebitNote()">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="updateBtn" onclick="showSaveOptionsModal()" disabled>
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Hidden field for debit note ID -->
<input type="hidden" id="debitNoteId" value="">

<!-- HSN Code Selection Modal - Right Sliding -->
<div id="hsnCodeModal" class="hsn-modal">
    <div class="hsn-modal-content">
        <div class="hsn-modal-header">
            <h5 class="hsn-modal-title">
                <i class="bi bi-upc-scan me-2"></i>Select HSN Code
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeHsnModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="hsn-modal-body" id="hsnModalBody">
            <!-- Search Box -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="hsn_modal_search" 
                           placeholder="Search HSN code or description..." autocomplete="off">
                </div>
            </div>
            
            <!-- Loading Spinner -->
            <div id="hsn_modal_loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading HSN codes...</div>
            </div>
            
            <!-- HSN Codes List -->
            <div id="hsn_modal_table_container" style="display: none;">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th class="fw-semibold text-dark">HSN Code</th>
                            <th class="fw-semibold text-dark">Description</th>
                            <th class="fw-semibold text-dark text-center">GST%</th>
                            <th class="fw-semibold text-dark text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hsn_codes_list">
                        <!-- HSN codes will be populated here -->
                    </tbody>
                </table>
            </div>
            <div id="hsn_modal_no_results" class="text-center py-5 text-muted" style="display: none;">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-0">No HSN codes found</p>
            </div>
        </div>
    </div>
</div>
<div id="hsnModalBackdrop" class="hsn-modal-backdrop" onclick="closeHsnModal()"></div>
<!-- Browse Debit Notes Modal - Custom -->
<div class="custom-modal-backdrop" id="debitNotesModalBackdrop" onclick="closeDebitNotesModal()"></div>
<div class="custom-modal" id="debitNotesModal">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <h5><i class="bi bi-list-ul me-2"></i> Select Debit Note</h5>
        <button type="button" class="custom-modal-close" onclick="closeDebitNotesModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <!-- Search Box -->
        <div class="mb-3">
            <input type="text" class="form-control" id="modalSearchInput" placeholder="Search by DN No., Party Name..." oninput="searchDebitNotesInModal()">
        </div>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>DN No.</th>
                        <th>Date</th>
                        <th>Party</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="debitNotesModalBody">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Save Options Modal -->
<div class="custom-modal-backdrop" id="saveOptionsModalBackdrop" onclick="closeSaveOptionsModal()"></div>
<div class="custom-modal" id="saveOptionsModal" style="width: 400px;">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #6c757d, #495057);">
        <h5><i class="bi bi-save me-2"></i> Save Options</h5>
        <button type="button" class="custom-modal-close" onclick="closeSaveOptionsModal()">&times;</button>
    </div>
    <div class="custom-modal-body text-center py-4">
        <p class="mb-4">How would you like to save this debit note?</p>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg" onclick="saveWithoutAdjustment()" id="saveWithoutAdjustmentBtn">
                <i class="bi bi-save me-2"></i> Save Without Adjustment
            </button>
            <button type="button" class="btn btn-success btn-lg" onclick="saveWithAdjustment()" id="saveWithAdjustmentBtn">
                <i class="bi bi-sliders me-2"></i> Save With Adjustment
            </button>
        </div>
    </div>
</div>

<!-- Adjustment Modal - Custom -->
<div class="custom-modal-backdrop" id="adjustmentModalBackdrop" onclick="closeAdjustmentModal()"></div>
<div class="custom-modal" id="adjustmentModal" style="width: 850px;">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <h5><i class="bi bi-sliders me-2"></i> Debit Note Adjustment</h5>
        <button type="button" class="custom-modal-close" onclick="closeAdjustmentModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 50px;">SR.</th>
                        <th style="width: 80px;">TYPE</th>
                        <th>TRANS NO.</th>
                        <th>DATE</th>
                        <th class="text-end">BALANCE AMT.</th>
                        <th style="width: 120px;">ADJUST</th>
                        <th class="text-end">REMAINING</th>
                    </tr>
                </thead>
                <tbody id="adjustmentModalBody">
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">Press ESC to close</span>
                </div>
                <div class="col-md-6 text-end">
                    <strong class="text-danger" style="font-size: 16px;">
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
// ============================================================
// MASTER KEYBOARD EVENT INTERCEPTOR
// Captures key events before any other library (like grid navigation) can bleed
// ============================================================
window.addEventListener('keydown', function(e) {
    if (typeof handleSaveOptionsKeys === 'function') {
        handleSaveOptionsKeys(e);
    }
    if (typeof handleAdjustmentKeys === 'function') {
        handleAdjustmentKeys(e);
    }
}, true);

let hsnRowCount = 0;
let hsnCodesData = [];
let hsnModalActiveIndex = -1;
let currentDebitNoteId = null;
let currentPartyType = 'S'; // S = Supplier, C = Customer
let searchTimeout = null;
let currentFocusIndex = -1;

const FIELD_ORDER = [
    'searchDebitNoteNo',
    'searchBtn',
    'browseBtn',
    'debitNoteDate',
    'reason',
    'partySupplier',
    'partySearchInput',
    'salesmanSelect',
    'accountPurchase',
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
    'cancelBtn',
    'deleteDebitNoteBtn',
    'updateBtn'
];

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // Initialize Custom Party Search
    initPartySearch();
    
    // PREVENT form submission on Enter key globally
    document.getElementById('debitNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Initialize Keyboard Navigation
    initKeyboardNavigation();
    
    // Initialize Browse Modal Keyboard Navigation
    initBrowseModalKeyboard();
    
    document.getElementById('debitNoteDate').addEventListener('change', updateDayName);
    
    document.querySelectorAll('input[name="debit_party_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentPartyType = this.value;
            updatePartyDropdown();
        });
    });
    
    // Auto-focus search field on page load
    setTimeout(function() {
        var searchField = document.getElementById('searchDebitNoteNo');
        if (searchField) { searchField.focus(); searchField.select(); }
    }, 100);
    
    @if($preloadDebitNoteNo)
        searchDebitNote();
    @endif
    
    // Load HSN codes immediately for the modal
    loadHsnCodes();
    
    // Setup HSN search listener
    const hsnSearchInput = document.getElementById('hsn_modal_search');
    if (hsnSearchInput) {
        let hsnSearchTimeout;
        hsnSearchInput.addEventListener('input', function() {
            clearTimeout(hsnSearchTimeout);
            hsnSearchTimeout = setTimeout(filterHsnCodes, 300);
        });
    }
});

// Custom Party Search
function initPartySearch() {
    const searchInput = document.getElementById('partySearchInput');
    const hiddenInput = document.getElementById('partySelect');
    const resultsContainer = document.getElementById('partySearchResults');

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        hiddenInput.value = '';
        var query = e.target.value.trim();
        searchTimeout = setTimeout(function() { fetchParties(query); }, 250);
    });

    searchInput.addEventListener('focus', function() {
        var query = this.value.trim();
        fetchParties(query);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    searchInput.addEventListener('keydown', function(e) {
        if (resultsContainer.style.display === 'block') {
            var items = resultsContainer.querySelectorAll('.list-group-item');
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
            }
        }
    });
}

function fetchParties(query) {
    var resultsContainer = document.getElementById('partySearchResults');
    resultsContainer.innerHTML = '<div class="list-group-item text-muted">Searching...</div>';
    resultsContainer.style.display = 'block';

    fetch('{{ route("admin.debit-note.search-parties") }}?q=' + encodeURIComponent(query) + '&party_type=' + currentPartyType)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.results && data.results.length > 0) {
                renderPartyResults(data.results);
            } else {
                resultsContainer.innerHTML = '<div class="list-group-item text-muted">No results found</div>';
            }
        })
        .catch(function() {
            resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error loading results</div>';
        });
}

function renderPartyResults(results) {
    var container = document.getElementById('partySearchResults');
    currentFocusIndex = -1;
    container.innerHTML = results.map(function(item) {
        return '<a href="#" class="list-group-item list-group-item-action" data-id="' + item.id + '" data-text="' + (item.text || item.name) + '">' + (item.text || item.name) + '</a>';
    }).join('');

    container.querySelectorAll('.list-group-item').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            selectParty({ id: this.dataset.id, text: this.dataset.text });
        });
    });
}

function selectParty(party) {
    document.getElementById('partySearchInput').value = party.text || party.name;
    document.getElementById('partySelect').value = party.id;
    document.getElementById('partySearchResults').style.display = 'none';
    window.selectedPartyName = party.text || party.name;
    
    setTimeout(function() {
        var salesmanSelect = document.getElementById('salesmanSelect');
        if (salesmanSelect) {
            salesmanSelect.focus();
            try { salesmanSelect.showPicker(); } catch(err) {
                salesmanSelect.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
            }
        }
    }, 50);
}

function highlightItem(items, index) {
    items.forEach(function(el) { el.classList.remove('active'); });
    if (items[index]) {
        items[index].classList.add('active');
        items[index].scrollIntoView({ block: 'nearest' });
    }
}

function updatePartyDropdown() {
    var searchInput = document.getElementById('partySearchInput');
    var hiddenInput = document.getElementById('partySelect');
    searchInput.value = '';
    hiddenInput.value = '';
    document.getElementById('partySearchResults').style.display = 'none';
    
    var helpText = document.querySelector('.party-search-container + small');
    if (helpText) {
        helpText.textContent = currentPartyType === 'S' ? 'Start typing to search for suppliers' : 'Start typing to search for customers';
    }
    searchInput.placeholder = currentPartyType === 'S' ? 'Search supplier...' : 'Search customer...';
}

function updateDayName() {
    const dateInput = document.getElementById('debitNoteDate');
    if (dateInput && dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

function searchDebitNote() {
    const debitNoteNo = document.getElementById('searchDebitNoteNo').value.trim();
    if (!debitNoteNo) {
        alert('Please enter Debit Note No.');
        return;
    }
    
    fetch(`{{ url('admin/debit-note/fetch') }}/${encodeURIComponent(debitNoteNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.debit_note) {
                populateDebitNoteData(data.debit_note);
            } else {
                alert(data.message || 'Debit note not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching debit note');
        });
}

function populateDebitNoteData(dn) {
    console.log('Populating debit note data:', dn);
    
    document.getElementById('debitNoteId').value = dn.id;
    document.getElementById('debitNoteNo').value = dn.debit_note_no;
    document.getElementById('debitNoteDate').value = dn.debit_note_date ? dn.debit_note_date.split('T')[0] : '';
    updateDayName();
    
    // Set party type
    currentPartyType = dn.debit_party_type || 'S';
    if (dn.debit_party_type === 'C') {
        document.getElementById('partyCustomer').checked = true;
    } else {
        document.getElementById('partySupplier').checked = true;
    }
    
    // Initialize party search with the current party type
    updatePartyDropdown();
    
    // Pre-select the party in custom search input
    if (dn.debit_party_id && dn.debit_party_name) {
        document.getElementById('partySearchInput').value = dn.debit_party_name;
        document.getElementById('partySelect').value = dn.debit_party_id;
        window.selectedPartyName = dn.debit_party_name;
    }
    
    // Set salesman and reason
    if (dn.salesman_id) {
        document.getElementById('salesmanSelect').value = dn.salesman_id;
    }
    if (dn.reason) {
        document.getElementById('reason').value = dn.reason;
    }
    
    if (dn.credit_account_type === 'S') {
        document.getElementById('accountSale').checked = true;
    } else if (dn.credit_account_type === 'G') {
        document.getElementById('accountGeneral').checked = true;
    } else {
        document.getElementById('accountPurchase').checked = true;
    }
    
    document.getElementById('accountNo').value = dn.credit_account_no || '';
    document.getElementById('invRefNo').value = dn.inv_ref_no || '';
    document.getElementById('invoiceDate').value = dn.invoice_date ? dn.invoice_date.split('T')[0] : '';
    document.getElementById('gstVno').value = dn.gst_vno || '';
    document.getElementById('partyTrnNo').value = dn.party_trn_no || '';
    document.getElementById('partyTrnDate').value = dn.party_trn_date ? dn.party_trn_date.split('T')[0] : '';
    document.getElementById('amount').value = dn.amount || 0;
    
    document.getElementById('grossAmount').value = dn.gross_amount || 0;
    document.getElementById('totalGst').value = dn.total_gst || 0;
    document.getElementById('netAmount').value = dn.net_amount || 0;
    document.getElementById('tcsAmount').value = dn.tcs_amount || 0;
    document.getElementById('roundOff').value = dn.round_off || 0;
    document.getElementById('dnAmount').value = dn.dn_amount || 0;
    document.getElementById('narration').value = dn.narration || '';
    
    const tbody = document.getElementById('hsnTableBody');
    tbody.innerHTML = '';
    hsnRowCount = 0;
    
    if (dn.items && dn.items.length > 0) {
        dn.items.forEach((item, index) => {
            addHsnRowWithData(item);
        });
    } else {
        addHsnRow();
    }
    
    document.getElementById('updateBtn').disabled = false;
    document.getElementById('deleteDebitNoteBtn').style.display = 'inline-block';
    
    // Store current debit note ID for adjustments
    currentDebitNoteId = dn.id;
}

// ============================================================
// KEYBOARD NAVIGATION SYSTEM
// ============================================================
function initKeyboardNavigation() {

    // ==============================================
    // Escape Key → Close any open modal
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close Browse Debit Notes Modal
            var debitNotesModal = document.getElementById('debitNotesModal');
            if (debitNotesModal && debitNotesModal.classList.contains('show')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeDebitNotesModal();
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
            
            // Close Save Options Modal
            var saveOptionsModal = document.getElementById('saveOptionsModal');
            if (saveOptionsModal && saveOptionsModal.classList.contains('show')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeSaveOptionsModal();
                return false;
            }
        }
    }, true);

    // ==============================================
    // Ctrl+S → Update Debit Note
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // If Adjustment Modal is open, save adjustments
            var adjustmentModal = document.getElementById('adjustmentModal');
            if (adjustmentModal && adjustmentModal.classList.contains('show')) {
                if (typeof saveAdjustments === 'function') {
                    saveAdjustments();
                }
                return false;
            }
            
            // Otherwise open normal Save Options if update button is enabled
            var updateBtn = document.getElementById('updateBtn');
            if (updateBtn && !updateBtn.disabled && typeof showSaveOptionsModal === 'function') {
                showSaveOptionsModal();
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
        var debitNotesModal = document.getElementById('debitNotesModal');
        var adjustmentModal = document.getElementById('adjustmentModal');
        var saveOptionsModal = document.getElementById('saveOptionsModal');
        var hsnCodeModal = document.getElementById('hsnCodeModal');
        if ((debitNotesModal && debitNotesModal.classList.contains('show')) ||
            (adjustmentModal && adjustmentModal.classList.contains('show')) ||
            (saveOptionsModal && saveOptionsModal.classList.contains('show')) ||
            (hsnCodeModal && hsnCodeModal.classList.contains('show'))) {
            return; // Let modal handlers deal with it
        }
        
        // ============================================
        // SHIFT+ENTER → BACKWARD NAVIGATION
        // ============================================
        if (e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Browse button → back to Search Debit Note No.
            if (activeEl.id === 'browseBtn') {
                var searchField = document.getElementById('searchDebitNoteNo');
                if (searchField) { searchField.focus(); searchField.select(); }
                return false;
            }
            
            // Debit Note Date → back to Browse button
            if (activeEl.id === 'debitNoteDate') {
                var browseBtn = document.getElementById('browseBtn');
                if (browseBtn) { browseBtn.focus(); }
                return false;
            }
            
            // Reason → back to Date
            if (activeEl.id === 'reason') {
                var dateField = document.getElementById('debitNoteDate');
                if (dateField) { 
                    dateField.focus(); 
                }
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
                var checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
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
                if (salesman) { 
                    salesman.focus(); 
                    try { salesman.showPicker(); } catch(err) {} 
                }
                return false;
            }
            
            // Account No → back to Account Type radio
            if (activeEl.id === 'accountNo') {
                var checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
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
            
            // Cancel button → back to Round Off
            if (activeEl.id === 'cancelBtn') {
                var roundOff = document.getElementById('roundOff');
                if (roundOff) { roundOff.focus(); roundOff.select(); }
                return false;
            }

            // Delete button → back to Cancel Button
            if (activeEl.id === 'deleteDebitNoteBtn') {
                var cancelBtn = document.getElementById('cancelBtn');
                if (cancelBtn) { cancelBtn.focus(); }
                return false;
            }
            
            // Update button → back to Delete or Cancel
            if (activeEl.id === 'updateBtn') {
                var deleteBtn = document.getElementById('deleteDebitNoteBtn');
                if (deleteBtn && deleteBtn.style.display !== 'none') {
                    deleteBtn.focus();
                } else {
                    var cancelBtn = document.getElementById('cancelBtn');
                    if (cancelBtn) cancelBtn.focus();
                }
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

        // ---- Handle Ctrl+Enter → jump to TCS Amount ----
        if (e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            setTimeout(function() {
                var tcs = document.getElementById('tcsAmount');
                if (tcs) {
                    tcs.focus();
                    tcs.select();
                }
            }, 30);
            return false;
        }

        // ---- Handle Browse Button Enter ----
        if (activeEl.id === 'browseBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            openDebitNotesModal();
            return false;
        }

        // ---- Handle Search Button Enter ----
        if (activeEl.id === 'searchBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Trigger the click natively
            activeEl.click();
            
            // Move focus to Browse button
            setTimeout(function() {
                var browseBtn = document.getElementById('browseBtn');
                if (browseBtn) {
                    browseBtn.focus();
                }
            }, 50);
            
            return false;
        }
        
        // Skip other buttons and links - let them work normally
        if (activeEl.tagName === 'BUTTON' || activeEl.tagName === 'A') return;
        
        // ---- Handle Date field Enter → jump to Reason ----
        if (activeEl.id === 'debitNoteDate') {
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
                var checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
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

        // ---- Handle Search Debit Note No. Enter → move focus to Search button ----
        if (activeEl.id === 'searchDebitNoteNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var searchBtn = document.getElementById('searchBtn');
            if (searchBtn) {
                searchBtn.focus();
            }
            
            return false;
        }
        
        // ---- Handle Salesman select Enter → jump to Account Type radio ----
        if (activeEl.id === 'salesmanSelect') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
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
                    try { invoiceDate.showPicker(); } catch(e) {}
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
                     try { partyTrnDate.showPicker(); } catch(e) {}
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

        // ---- Handle Amount field Enter → trigger Insert button (open modal) ----
        if (activeEl.id === 'amount') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                if (typeof openHsnModal === 'function') {
                    openHsnModal();
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
        
        // ---- Handle HSN row Amount field Enter → trigger Insert button (open modal) ----
        if (activeEl.classList.contains('hsn-amount')) {
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
            
            setTimeout(function() {
                if (typeof openHsnModal === 'function') {
                    openHsnModal();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle HSN row GST% field Enter (fallback since readonly) ----
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
            
            setTimeout(function() {
                if (typeof openHsnModal === 'function') {
                    openHsnModal();
                }
            }, 50);
            
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
        
        // ---- Handle Round Off Enter → jump to Cancel button ----
        if (activeEl.id === 'roundOff') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var cancelBtn = document.getElementById('cancelBtn');
                if (cancelBtn) {
                    cancelBtn.focus();
                }
            }, 30);
            
            return false;
        }

        // ---- Handle Cancel Button Enter → jump to Delete or Update ----
        if (activeEl.id === 'cancelBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var deleteBtn = document.getElementById('deleteDebitNoteBtn');
                if (deleteBtn && deleteBtn.style.display !== 'none') {
                    deleteBtn.focus();
                } else {
                    var updateBtn = document.getElementById('updateBtn');
                    if (updateBtn && !updateBtn.disabled) updateBtn.focus();
                }
            }, 30);
            
            return false;
        }

        // ---- Handle Delete Button Enter → jump to Update ----
        if (activeEl.id === 'deleteDebitNoteBtn') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                var updateBtn = document.getElementById('updateBtn');
                if (updateBtn && !updateBtn.disabled) updateBtn.focus();
            }, 30);
            
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
    
    // (Shift+Enter code merged with main handler)
    
    // ==============================================
    // Auto-advance on Reason Select change
    // ==============================================
    var reasonSelect = document.getElementById('reason');
    if (reasonSelect) {
        reasonSelect.addEventListener('change', function() {
            setTimeout(function() {
                var checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
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
                var checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
                if (checkedRadio) {
                    checkedRadio.focus();
                }
            }, 30);
        });
    }
}

function navigateField(currentEl, direction) {
    var currentIndex = -1;
    var currentId = currentEl.id;

    // Map radio buttons to their group representative in FIELD_ORDER
    if (currentEl.type === 'radio') {
        if (currentEl.name === 'debit_party_type') {
            currentIndex = FIELD_ORDER.indexOf('partySupplier');
        } else if (currentEl.name === 'credit_account_type') {
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
        var groupName = (nextId === 'partySupplier') ? 'debit_party_type' : 'credit_account_type';
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
// BROWSE DEBIT NOTES MODAL - KEYBOARD NAVIGATION
// ============================================================
function initBrowseModalKeyboard() {
    var browseSelectedIndex = -1;
    
    document.addEventListener('keydown', function(e) {
        var debitNotesModal = document.getElementById('debitNotesModal');
        if (!debitNotesModal || !debitNotesModal.classList.contains('show')) return;
        
        var tbody = document.getElementById('debitNotesModalBody');
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
            closeDebitNotesModal();
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
            rows[index].style.backgroundColor = '#ffcccc';
            rows[index].style.fontWeight = 'bold';
            rows[index].classList.add('table-active');
            rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    
    // Reset selection when modal opens
    var debitNotesModalEl = document.getElementById('debitNotesModal');
    if (debitNotesModalEl) {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (debitNotesModalEl.classList.contains('show')) {
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
        observer.observe(debitNotesModalEl, { attributes: true, attributeFilter: ['class'] });
    }
}
function loadHsnCodes() {
    fetch('{{ route("admin.hsn-codes.index") }}?all=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        hsnCodesData = data;
        renderHsnCodes(hsnCodesData);
        document.getElementById('hsn_modal_loading').style.display = 'none';
        document.getElementById('hsn_modal_table_container').style.display = 'block';
        
        if (hsnCodesData.length === 0) {
            document.getElementById('hsn_modal_no_results').style.display = 'block';
            document.getElementById('hsn_modal_table_container').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error loading HSN codes:', error);
        document.getElementById('hsn_modal_loading').innerHTML = '<p class="text-danger">Error loading HSN codes</p>';
    });
}

function renderHsnCodes(codes) {
    const tbody = document.getElementById('hsn_codes_list');
    tbody.innerHTML = '';
    hsnModalActiveIndex = -1;
    
    if (codes.length === 0) return;
    
    codes.forEach((code, index) => {
        const gstPercent = parseFloat(code.cgst_percent || 0) + parseFloat(code.sgst_percent || 0);
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.setAttribute('data-hsn-code', code.hsn_code);
        tr.setAttribute('data-cgst', code.cgst_percent || 0);
        tr.setAttribute('data-sgst', code.sgst_percent || 0);
        tr.innerHTML = `
            <td><strong>${code.hsn_code}</strong></td>
            <td class="small">${code.description || '-'}</td>
            <td class="text-center"><span class="badge bg-success">${gstPercent.toFixed(2)}%</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-primary" onclick="selectHsnCode('${code.hsn_code}', ${code.cgst_percent || 0}, ${code.sgst_percent || 0})">
                    <i class="bi bi-plus-circle"></i> Select
                </button>
            </td>
        `;
        // Click on row to select
        tr.addEventListener('click', function() {
            selectHsnCode(code.hsn_code, code.cgst_percent || 0, code.sgst_percent || 0);
        });
        tbody.appendChild(tr);
    });
}

function highlightHsnRow(index) {
    var rows = document.querySelectorAll('#hsn_codes_list tr');
    rows.forEach(function(row) {
        row.style.backgroundColor = '';
        row.classList.remove('table-primary');
    });
    if (index >= 0 && index < rows.length) {
        rows[index].classList.add('table-primary');
        rows[index].style.backgroundColor = '#cfe2ff';
        rows[index].scrollIntoView({ block: 'nearest' });
    }
}

function filterHsnCodes() {
    const searchTerm = document.getElementById('hsn_modal_search').value.toLowerCase();
    const filtered = hsnCodesData.filter(code => 
        code.hsn_code.toLowerCase().includes(searchTerm) || 
        (code.description && code.description.toLowerCase().includes(searchTerm))
    );
    renderHsnCodes(filtered);
}

function openHsnModal() {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    document.getElementById('hsn_modal_search').value = '';
    renderHsnCodes(hsnCodesData);
    hsnModalActiveIndex = -1;
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
        document.getElementById('hsn_modal_search').focus();
        // Auto-highlight first row
        var rows = document.querySelectorAll('#hsn_codes_list tr');
        if (rows.length > 0) { hsnModalActiveIndex = 0; highlightHsnRow(0); }
    }, 10);
}

// HSN Modal keyboard navigation — window CAPTURE phase (fires before all other handlers)
window.addEventListener('keydown', function(e) {
    var modal = document.getElementById('hsnCodeModal');
    if (!modal || !modal.classList.contains('show')) return;

    var MANAGED = ['ArrowDown','ArrowUp','Enter','Escape'];
    if (!MANAGED.includes(e.key)) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var rows = Array.from(document.querySelectorAll('#hsn_codes_list tr'));

    if (e.key === 'Escape') { closeHsnModal(); return; }
    if (rows.length === 0) return;

    if (e.key === 'ArrowDown') {
        if (hsnModalActiveIndex < rows.length - 1) hsnModalActiveIndex++;
        else hsnModalActiveIndex = 0;
        highlightHsnRow(hsnModalActiveIndex);
        return;
    }
    if (e.key === 'ArrowUp') {
        if (hsnModalActiveIndex > 0) hsnModalActiveIndex--;
        else hsnModalActiveIndex = rows.length - 1;
        highlightHsnRow(hsnModalActiveIndex);
        return;
    }
    if (e.key === 'Enter') {
        var idx = hsnModalActiveIndex >= 0 ? hsnModalActiveIndex : 0;
        var selectedRow = rows[idx];
        if (selectedRow) {
            var hsnCode = selectedRow.getAttribute('data-hsn-code');
            var cgst = parseFloat(selectedRow.getAttribute('data-cgst') || 0);
            var sgst = parseFloat(selectedRow.getAttribute('data-sgst') || 0);
            selectHsnCode(hsnCode, cgst, sgst);
        }
        return;
    }
}, true); // capture phase

function closeHsnModal() {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

function selectHsnCode(hsnCode, cgstPercent, sgstPercent) {
    const gstPercent = parseFloat(cgstPercent) + parseFloat(sgstPercent);
    addHsnRowWithData({
        hsn_code: hsnCode,
        gst_percent: gstPercent,
        cgst_percent: cgstPercent,
        sgst_percent: sgstPercent,
        amount: 0.00
    }, true);
    closeHsnModal();
}

function addHsnRow() {
    addHsnRowWithData({});
}

function addHsnRowWithData(item, isNewFromModal = false) {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm hsn-code readonly-field" name="items[${hsnRowCount}][hsn_code]" value="${item.hsn_code || ''}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="${item.amount || 0}" onchange="calculateGst(${hsnRowCount})" onkeyup="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst readonly-field" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="${item.gst_percent || 0}" readonly></td>
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
    
    if (isNewFromModal) {
        setTimeout(function() {
            var input = row.querySelector('.hsn-amount');
            if (input) {
                input.focus();
                input.select();
            }
        }, 50);
    }
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
    const dnAmount = netAmount + tcsAmount + roundOff;
    
    document.getElementById('grossAmount').value = grossAmount.toFixed(2);
    document.getElementById('totalGst').value = totalGst.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('dnAmount').value = dnAmount.toFixed(2);
}

function updateDebitNote() {
    const debitNoteId = document.getElementById('debitNoteId')?.value;
    if (!debitNoteId) {
        alert('Please load a debit note first');
        return;
    }
    
    // Helper function to safely get element value
    const getVal = (id) => document.getElementById(id)?.value || '';
    const getChecked = (name) => document.querySelector(`input[name="${name}"]:checked`)?.value || '';
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value || row.querySelector('input[name*="hsn_code"]')?.value;
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
    
    // Get party name from custom search input
    const partyName = document.getElementById('partySearchInput').value;
    
    const data = {
        header: {
            debit_note_date: getVal('debitNoteDate'),
            day_name: getVal('dayName'),
            debit_party_type: getChecked('debit_party_type'),
            debit_party_id: getVal('partySelect'),
            debit_party_name: partyName,
            credit_account_type: getChecked('credit_account_type'),
            credit_account_no: getVal('accountNo'),
            inv_ref_no: getVal('invRefNo'),
            invoice_date: getVal('invoiceDate') || null,
            gst_vno: getVal('gstVno'),
            party_trn_no: getVal('partyTrnNo'),
            party_trn_date: getVal('partyTrnDate') || null,
            amount: getVal('amount'),
            salesman_id: getVal('salesmanSelect') || null,
            reason: getVal('reason'),
            gross_amount: getVal('grossAmount'),
            total_gst: getVal('totalGst'),
            net_amount: getVal('netAmount'),
            tcs_amount: getVal('tcsAmount'),
            round_off: getVal('roundOff'),
            dn_amount: getVal('dnAmount'),
            narration: getVal('narration'),
        },
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
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
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating debit note');
    });
}

function deleteDebitNote() {
    const debitNoteId = document.getElementById('debitNoteId').value;
    if (!debitNoteId) return;
    
    if (!confirm('Are you sure you want to delete this debit note?')) return;
    
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
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
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting debit note');
    });
}

let allDebitNotes = [];

function openDebitNotesModal() {
    document.getElementById('debitNotesModalBackdrop').classList.add('show');
    document.getElementById('debitNotesModal').classList.add('show');
    
    document.getElementById('modalSearchInput').value = '';
    document.getElementById('debitNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    
    // Auto-focus the search input inside the modal
    setTimeout(function() {
        var modalInput = document.getElementById('modalSearchInput');
        if (modalInput) {
            modalInput.focus();
        }
    }, 100);
    
    // Fetch all debit notes
    fetch('{{ route("admin.debit-note.invoices") }}?all=1', {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            allDebitNotes = data.data;
        } else if (Array.isArray(data)) {
            allDebitNotes = data;
        } else {
            allDebitNotes = [];
        }
        renderDebitNotesInModal(allDebitNotes);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('debitNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
    });
}

function closeDebitNotesModal() {
    document.getElementById('debitNotesModalBackdrop').classList.remove('show');
    document.getElementById('debitNotesModal').classList.remove('show');
}

function renderDebitNotesInModal(debitNotes) {
    const tbody = document.getElementById('debitNotesModalBody');
    
    if (!debitNotes || debitNotes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No debit notes found</td></tr>';
        return;
    }
    
    tbody.innerHTML = debitNotes.map(dn => `
        <tr>
            <td><strong>${dn.debit_note_no || ''}</strong></td>
            <td>${dn.debit_note_date ? new Date(dn.debit_note_date).toLocaleDateString('en-IN') : ''}</td>
            <td>${dn.debit_party_name || ''}</td>
            <td class="text-end">₹ ${parseFloat(dn.net_amount || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="selectDebitNoteFromModal('${dn.debit_note_no}')">
                    <i class="bi bi-check"></i> Select
                </button>
            </td>
        </tr>
    `).join('');
}

function searchDebitNotesInModal() {
    const searchTerm = document.getElementById('modalSearchInput').value.toLowerCase().trim();
    
    if (!searchTerm) {
        renderDebitNotesInModal(allDebitNotes);
        return;
    }
    
    const filtered = allDebitNotes.filter(dn => {
        const dnNo = (dn.debit_note_no || '').toString().toLowerCase();
        const partyName = (dn.debit_party_name || '').toLowerCase();
        return dnNo.includes(searchTerm) || partyName.includes(searchTerm);
    });
    
    renderDebitNotesInModal(filtered);
}

function selectDebitNoteFromModal(debitNoteNo) {
    // Close modal
    closeDebitNotesModal();
    
    // Set the debit note number and search
    document.getElementById('searchDebitNoteNo').value = debitNoteNo;
    searchDebitNote();
    
    // Auto-focus Date field after the selection is processed
    setTimeout(function() {
        var dateField = document.getElementById('debitNoteDate');
        if (dateField) {
            dateField.focus();
        }
    }, 200);
}

// ============ ADJUSTMENT MODAL FUNCTIONS ============
let allInvoices = [];
let existingAdjustments = [];
let dnAmount = 0;

function openAdjustmentModal() {
    const debitNoteId = document.getElementById('debitNoteId').value;
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    
    if (!debitNoteId || !partyId) {
        alert('Please load a debit note first');
        return;
    }
    
    if (partyType !== 'S') {
        alert('Adjustment is only available for Supplier party type');
        return;
    }
    
    dnAmount = parseFloat(document.getElementById('dnAmount').value) || 0;
    
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
    document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="7" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    document.getElementById('autoAdjustAmount').value = dnAmount.toFixed(2);
    
    // Fetch invoices and existing adjustments
    Promise.all([
        fetch(`{{ url('admin/debit-note/supplier') }}/${partyId}/purchase-invoices`).then(r => r.json()),
        fetch(`{{ url('admin/debit-note/supplier') }}/${partyId}/credit-notes`).then(r => r.json()),
        fetch(`{{ url('admin/debit-note') }}/${debitNoteId}/adjustments`).then(r => r.json())
    ])
    .then(([purchaseData, creditNoteData, adjustmentsData]) => {
        allInvoices = [];
        
        // Add purchase invoices
        if (purchaseData.success && purchaseData.invoices) {
            purchaseData.invoices.forEach(inv => {
                allInvoices.push({
                    id: inv.id,
                    type: 'PURCHASE',
                    trans_no: inv.bill_no || inv.trans_no || '',
                    date: inv.bill_date_formatted || '',
                    bill_amount: parseFloat(inv.inv_amount || 0),
                    balance: parseFloat(inv.balance_amount || inv.inv_amount || 0)
                });
            });
        }
        
        // Add credit notes
        if (creditNoteData.success && creditNoteData.credit_notes) {
            creditNoteData.credit_notes.forEach(cn => {
                allInvoices.push({
                    id: cn.id,
                    type: 'CREDIT_NOTE',
                    trans_no: cn.credit_note_no || '',
                    date: cn.credit_note_date_formatted || '',
                    bill_amount: parseFloat(cn.cn_amount || 0),
                    balance: parseFloat(cn.balance_amount || cn.cn_amount || 0)
                });
            });
        }
        
        if (adjustmentsData.success) {
            existingAdjustments = adjustmentsData.adjustments || [];
        } else {
            existingAdjustments = [];
        }
        
        renderAdjustmentTable();
        // Auto-focus first input
        setTimeout(function() {
            const firstInput = document.querySelector('.adjustment-input');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
    });
}

function handleAdjustmentKeys(e) {
    const adjustmentModal = document.getElementById('adjustmentModal');
    if (!adjustmentModal || !adjustmentModal.classList.contains('show')) return;

    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeAdjustmentModal();
        return;
    }

    if (e.key === 's' && e.ctrlKey) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveAdjustments();
        return;
    }

    const activeElement = document.activeElement;
    const inputs = Array.from(adjustmentModal.querySelectorAll('.adjustment-input'));
    const saveBtn = adjustmentModal.querySelector('.custom-modal-footer .btn-success');
    
    // Global up/down if nothing targeted
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        if (!activeElement || (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'BUTTON')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (inputs.length > 0) {
                inputs[0].focus();
                inputs[0].select();
            }
            return;
        }
    }

    // Navigating adjustment inputs
    if (activeElement && activeElement.classList.contains('adjustment-input')) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            let index = inputs.indexOf(activeElement);
            
            if (e.key === 'ArrowDown' || e.key === 'Enter') {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                    inputs[index + 1].select();
                } else if (saveBtn) {
                    saveBtn.focus();
                }
            } else if (e.key === 'ArrowUp') {
                if (index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].select();
                } else {
                    const autoInput = document.getElementById('autoAdjustAmount');
                    if (autoInput) {
                        autoInput.focus();
                        autoInput.select();
                    }
                }
            }
        }
    }
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
}

function renderAdjustmentTable() {
    const tbody = document.getElementById('adjustmentModalBody');
    
    // Merge invoices with existing adjustments
    const mergedInvoices = [];
    
    // First add invoices that have existing adjustments
    existingAdjustments.forEach(adj => {
        const invType = adj.adjustment_type || (adj.purchase_transaction_id ? 'PURCHASE' : 'CREDIT_NOTE');
        const adjAmount = parseFloat(adj.adjusted_amount || 0);
        // Balance from API = bill_amount - other adjustments (excluding current)
        // So actual available balance for this invoice = balance from API
        // This balance already includes "room" for current adjustment
        const balanceFromApi = parseFloat(adj.balance || adj.bill_amount || adj.invoice_amount || 0);
        
        mergedInvoices.push({
            id: adj.purchase_transaction_id || adj.credit_note_id || adj.invoice_id,
            type: invType,
            trans_no: adj.trans_no || adj.invoice_no || '',
            date: adj.date || adj.invoice_date || '',
            bill_amount: parseFloat(adj.bill_amount || adj.invoice_amount || 0),
            balance: balanceFromApi, // This is the max adjustable amount
            adjusted_amount: adjAmount,
            is_existing: true
        });
    });
    
    // Then add remaining invoices (not already adjusted)
    allInvoices.forEach(inv => {
        const existingAdj = existingAdjustments.find(a => {
            if (inv.type === 'PURCHASE') {
                return a.purchase_transaction_id == inv.id;
            } else {
                return a.credit_note_id == inv.id;
            }
        });
        if (!existingAdj) {
            mergedInvoices.push({
                id: inv.id,
                type: inv.type,
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
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No invoices or credit notes found</td></tr>';
        updateAdjustmentBalance();
        return;
    }
    
    tbody.innerHTML = mergedInvoices.map((inv, index) => {
        const typeLabel = inv.type === 'PURCHASE' ? '<span class="badge bg-primary">Purchase</span>' : '<span class="badge bg-warning">CN</span>';
        const remaining = inv.balance - inv.adjusted_amount;
        return `
        <tr class="${inv.is_existing ? 'table-success' : ''}">
            <td class="text-center">${index + 1}</td>
            <td>${typeLabel}</td>
            <td><strong>${inv.trans_no}</strong></td>
            <td>${inv.date}</td>
            <td class="text-end text-primary fw-bold">₹ ${inv.balance.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm adjustment-input" 
                       id="adj_${inv.id}_${inv.type}" 
                       data-invoice-id="${inv.id}"
                       data-invoice-type="${inv.type}"
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
            <td class="text-end" id="balance_${inv.id}_${inv.type}">
                <span class="${remaining > 0 ? 'text-warning' : 'text-success'}">₹ ${remaining.toFixed(2)}</span>
            </td>
        </tr>
    `}).join('');
    
    updateAdjustmentBalance();
}

function updateAdjustmentBalance() {
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        let value = parseFloat(input.value) || 0;
        const balance = parseFloat(input.dataset.balance) || 0;
        const invoiceId = input.dataset.invoiceId;
        const invoiceType = input.dataset.invoiceType;
        
        // Validate - can't be negative or more than balance
        if (value < 0) {
            value = 0;
            input.value = '0.00';
        }
        if (value > balance) {
            value = balance;
            input.value = balance.toFixed(2);
        }
        
        totalAdjusted += value;
        
        // Update remaining display - show (balance - adjusted)
        const remaining = balance - value;
        const balanceEl = document.getElementById(`balance_${invoiceId}_${invoiceType}`);
        if (balanceEl) {
            // Green if fully adjusted (0), orange/warning if partial
            const colorClass = remaining === 0 ? 'text-success' : 'text-warning';
            const fontWeight = remaining === 0 ? 'fw-bold' : '';
            balanceEl.innerHTML = `<span class="${colorClass} ${fontWeight}">₹ ${remaining.toFixed(2)}</span>`;
        }
    });
    
    // Update total remaining DN amount to adjust
    const remaining = dnAmount - totalAdjusted;
    document.getElementById('adjustmentBalance').textContent = remaining.toFixed(2);
    
    // Change color based on remaining
    const balanceSpan = document.getElementById('adjustmentBalance');
    balanceSpan.classList.remove('text-primary', 'text-success', 'text-danger');
    if (remaining < 0) {
        balanceSpan.classList.add('text-danger');
    } else if (remaining === 0) {
        balanceSpan.classList.add('text-success');
    } else {
        balanceSpan.classList.add('text-danger');
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
    const debitNoteId = document.getElementById('debitNoteId').value;
    const adjustments = [];
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        const adjustedAmount = parseFloat(input.value) || 0;
        if (adjustedAmount > 0) {
            adjustments.push({
                invoice_id: input.dataset.invoiceId,
                invoice_type: input.dataset.invoiceType,
                adjusted_amount: adjustedAmount
            });
        }
    });
    
    // Save adjustments
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}/save-adjustments`, {
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
            // Redirect to invoices list after saving
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + (result.message || 'Failed to save adjustments'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving adjustments');
    });
}

// ============ SAVE OPTIONS MODAL FUNCTIONS ============
function showSaveOptionsModal() {
    // Validate form first
    const debitNoteNo = document.getElementById('debitNoteNo').value;
    const partyId = document.getElementById('partySelect').value;
    const dnAmount = parseFloat(document.getElementById('dnAmount').value) || 0;
    
    if (!debitNoteNo) {
        alert('Please load a debit note first');
        return;
    }
    
    if (!partyId) {
        alert('Please select a party');
        return;
    }
    
    if (dnAmount <= 0) {
        alert('Please add HSN items');
        return;
    }
    
    // Check party type - only show adjustment option for Supplier
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    const saveWithAdjBtn = document.getElementById('saveWithAdjustmentBtn');
    if (partyType !== 'S') {
        saveWithAdjBtn.style.display = 'none';
    } else {
        saveWithAdjBtn.style.display = 'block';
    }
    
    document.getElementById('saveOptionsModalBackdrop').classList.add('show');
    document.getElementById('saveOptionsModal').classList.add('show');
    
    setTimeout(function() {
        // Forcefully remove focus from anything in the background (like the HSN inputs)
        if (document.activeElement && document. activeElement.tagName === 'INPUT') {
            document.activeElement.blur();
        }
        
        const firstBtn = document.getElementById('saveWithoutAdjustmentBtn');
        if (firstBtn) highlightSaveButton(firstBtn);
    }, 100);
}

function highlightSaveButton(btn) {
    const saveOptionsModal = document.getElementById('saveOptionsModal');
    if (!saveOptionsModal) return;
    const buttons = Array.from(saveOptionsModal.querySelectorAll('.btn-lg'));
    buttons.forEach(b => {
        b.style.boxShadow = '';
        b.style.transform = '';
        b.style.border = '';
    });
    if (btn) {
        btn.focus();
        btn.style.boxShadow = '0 0 0 0.25rem rgba(13, 110, 253, 0.5)';
        btn.style.transform = 'scale(1.02)';
        btn.style.transition = 'all 0.1s ease-in-out';
    }
}

function handleSaveOptionsKeys(e) {
    const saveOptionsModal = document.getElementById('saveOptionsModal');
    if (!saveOptionsModal || !saveOptionsModal.classList.contains('show')) return;

    // Aggressively swallow ALL keyboard events while this modal is open so they NEVER bleed into the background HSN table
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    if (e.key === 'Escape') {
        closeSaveOptionsModal();
        return;
    }
    
    // Find visible buttons
    const buttons = Array.from(saveOptionsModal.querySelectorAll('.btn-lg')).filter(btn => btn.style.display !== 'none');
    if (buttons.length === 0) return;
    
    // Find the currently active button based on our JS styling or document target
    let activeItem = buttons.find(b => b.style.transform !== '');
    if (!activeItem && buttons.includes(document.activeElement)) {
        activeItem = document.activeElement;
    }
    
    let index = buttons.indexOf(activeItem);
    if (index === -1) index = 0;

    if (e.key === 'ArrowDown' || e.key === 'ArrowRight' || e.key === 'Tab') {
        index = index < buttons.length - 1 ? index + 1 : 0;
        highlightSaveButton(buttons[index]);
    } 
    else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        index = index > 0 ? index - 1 : buttons.length - 1;
        highlightSaveButton(buttons[index]);
    }
    else if (e.key === 'Enter' || e.key === ' ') {
        buttons[index].click();
    }
}

function closeSaveOptionsModal() {
    document.getElementById('saveOptionsModalBackdrop').classList.remove('show');
    document.getElementById('saveOptionsModal').classList.remove('show');
}

function saveWithoutAdjustment() {
    closeSaveOptionsModal();
    updateDebitNote();
}

function saveWithAdjustment() {
    closeSaveOptionsModal();
    // First update the debit note, then open adjustment modal
    updateDebitNoteAndOpenAdjustment();
}

function updateDebitNoteAndOpenAdjustment() {
    const debitNoteId = document.getElementById('debitNoteId')?.value;
    
    if (!debitNoteId) {
        alert('Please load a debit note first');
        return;
    }
    
    // Helper function to safely get element value
    const getVal = (id) => document.getElementById(id)?.value || '';
    const getChecked = (name) => document.querySelector(`input[name="${name}"]:checked`)?.value || '';
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value || row.querySelector('input[name*="hsn_code"]')?.value;
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
    
    const partyName = document.getElementById('partySearchInput').value;
    
    // Use same data structure as updateDebitNote
    const data = {
        header: {
            debit_note_date: getVal('debitNoteDate'),
            day_name: getVal('dayName'),
            debit_party_type: getChecked('debit_party_type'),
            debit_party_id: getVal('partySelect'),
            debit_party_name: partyName,
            credit_account_type: getChecked('credit_account_type'),
            credit_account_no: getVal('accountNo'),
            inv_ref_no: getVal('invRefNo'),
            invoice_date: getVal('invoiceDate') || null,
            gst_vno: getVal('gstVno'),
            party_trn_no: getVal('partyTrnNo'),
            party_trn_date: getVal('partyTrnDate') || null,
            amount: getVal('amount'),
            salesman_id: getVal('salesmanSelect') || null,
            reason: getVal('reason'),
            gross_amount: getVal('grossAmount'),
            total_gst: getVal('totalGst'),
            net_amount: getVal('netAmount'),
            tcs_amount: getVal('tcsAmount'),
            round_off: getVal('roundOff'),
            dn_amount: getVal('dnAmount'),
            narration: getVal('narration'),
        },
        items: items
    };
    
    // Update debit note first
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
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
            // Now open adjustment modal
            openAdjustmentModal();
        } else {
            alert('Error: ' + (result.message || 'Failed to update debit note'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating debit note');
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
    background: linear-gradient(135deg, #dc3545, #c82333);
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

.adjustment-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.adjustment-input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 20px;
    color: #fff;
    cursor: pointer;
    padding: 5px;
    line-height: 1;
    transition: color 0.2s;
}

.btn-close-modal:hover {
    color: rgba(255,255,255,0.7);
}

/* HSN Modal Styles */
.hsn-modal {
    display: none;
    position: fixed;
    top: 60px;
    right: -450px;
    width: 450px;
    bottom: 45px;
    background: #fff;
    z-index: 1060;
    box-shadow: -5px 0 25px rgba(0,0,0,0.15);
    transition: right 0.3s ease;
    border-radius: 8px 0 0 8px;
    overflow: hidden;
}

.hsn-modal.show {
    right: 0;
}

.hsn-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 1055;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.hsn-modal-backdrop.show {
    opacity: 1;
}

.hsn-modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.hsn-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #fff;
    border-bottom: 1px solid #e9ecef;
}

.hsn-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 20px;
    color: #666;
    cursor: pointer;
    padding: 5px;
    line-height: 1;
    transition: color 0.2s;
}

.btn-close-modal:hover {
    color: #dc3545;
}

.hsn-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #fff;
}

.hsn-modal-body .table {
    font-size: 13px;
}

.hsn-modal-body .table th {
    border-top: none;
    padding: 10px 8px;
    background: #f8f9fa;
    font-weight: 600;
}

.hsn-modal-body .table td {
    padding: 10px 8px;
    vertical-align: middle;
}

.hsn-modal-body .table tbody tr:hover {
    background-color: #f8f9fa;
}

.hsn-modal-body .badge {
    font-weight: 500;
    padding: 5px 10px;
}

.hsn-modal-body .btn-sm {
    padding: 4px 12px;
    font-size: 12px;
}
</style>
@endpush