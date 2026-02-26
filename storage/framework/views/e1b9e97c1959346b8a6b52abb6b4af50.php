<?php $__env->startSection('title', 'Debit Note Transaction'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i> Debit Note Transaction</h4>
        <div class="text-muted small">Create new debit note</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.debit-note.invoices')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
    </div>
</div>

<form id="debitNoteForm" autocomplete="off">
    <?php echo csrf_field(); ?>
    
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-danger text-white py-2">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Debit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="debitNoteDate" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="debitNoteDate" name="debit_note_date" 
                           value="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly>
                </div>
                <div class="col-md-2">
                    <label for="debitNoteNo" class="form-label">Debit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="debitNoteNo" 
                           value="<?php echo e($nextDebitNoteNo); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label for="reason" class="form-label">Reason</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="reasonDisplay" placeholder="Select Reason" readonly style="cursor: pointer; background-color: #fff;">
                        <input type="hidden" id="reason" name="reason">
                        <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="pointer-events: none;"></i>
                        <div id="reasonOptions" class="list-group position-absolute w-100 shadow-sm start-0 custom-options" style="display:none; z-index: 1050; border: 1px solid #dee2e6; max-height: 200px; overflow-y: auto;">
                            <a href="#" class="list-group-item list-group-item-action py-2" data-value="Rate Diff.">Rate Diff.</a>
                            <a href="#" class="list-group-item list-group-item-action py-2" data-value="Other">Other</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Party Details Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-warning text-dark py-2">
            <h6 class="mb-0"><i class="bi bi-people me-2"></i> Party Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Debit Section (Left) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #fff;">
                        <h6 class="text-secondary mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Party)</h6>
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
                                <label for="partySelect" class="form-label">Party Name <span class="text-danger">*</span></label>
                                <div class="position-relative party-search-container">
                                    <!-- Visual Input for searching -->
                                    <input type="text" class="form-control" id="partySearchInput" placeholder="Type to search..." autocomplete="off">
                                    <!-- Hidden Input for actual value submission -->
                                    <input type="hidden" id="partySelect" name="debit_party_id">
                                    <!-- Results Dropdown -->
                                    <div id="partySearchResults" class="list-group position-absolute w-100 shadow-sm start-0" style="display:none; z-index: 1050; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #ddd;"></div>
                                </div>
                                <small class="text-muted">Start typing to search for suppliers</small>
                            </div>
                            <div class="col-md-6">
                                <label for="salesmanSelect" class="form-label">Sales Man</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="salesmanDisplay" placeholder="Select Salesman" readonly style="cursor: pointer; background-color: #fff;">
                                    <input type="hidden" id="salesmanSelect" name="salesman_id">
                                    <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="pointer-events: none;"></i>
                                    <div id="salesmanOptions" class="list-group position-absolute w-100 shadow-sm start-0 custom-options" style="display:none; z-index: 1050; border: 1px solid #dee2e6; max-height: 200px; overflow-y: auto;">
                                        <?php $__currentLoopData = $salesmen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="#" class="list-group-item list-group-item-action py-2" data-value="<?php echo e($salesman->id); ?>"><?php echo e($salesman->name); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #fff;">
                        <h6 class="text-secondary mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Credit (Account)</h6>
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
        <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i> HSN Details</h6>
            <button type="button" id="insertHsnBtn" class="btn btn-light btn-sm" onclick="openHsnModal()">
                <i class="bi bi-plus-circle me-1"></i> Insert
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="hsnTable" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 150px;">HSN Code</th>
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
                        <!-- Rows will be added via Insert button -->
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
                            <input type="number" class="form-control form-control-sm readonly-field" id="grossAmount" name="gross_amount" value="0.00" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total GST</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="totalGst" name="total_gst" value="0.00" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Net Amount</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="netAmount" name="net_amount" value="0.00" readonly>
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
                            <input type="number" class="form-control form-control-sm fw-bold text-danger readonly-field" id="dnAmount" name="dn_amount" value="0.00" readonly style="font-size: 16px;">
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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo e(route('admin.debit-note.invoices')); ?>'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="saveDebitNote()">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveDebitNote(true)">
                        <i class="bi bi-printer me-1"></i> Save & Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

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

<!-- Save Options Modal -->
<div id="saveOptionsModal" class="save-options-modal">
    <div class="save-options-modal-content">
        <div class="save-options-modal-header">
            <h5><i class="bi bi-save me-2"></i>Save Debit Note</h5>
            <button type="button" class="save-options-close-btn" onclick="closeSaveOptionsModal()">&times;</button>
        </div>
        <div class="save-options-modal-body">
            <p>How would you like to save this Debit Note?</p>
            <div class="save-options-buttons">
                <button type="button" class="btn btn-secondary" onclick="saveWithoutAdjustment()">
                    <i class="bi bi-file-earmark-check me-1"></i> Save Without Adjustment
                </button>
                <button type="button" class="btn btn-primary" onclick="saveWithAdjustment()">
                    <i class="bi bi-receipt me-1"></i> Save With Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let hsnRowCount = 0;
let hsnCodesData = [];
let currentPartyType = 'S'; // S = Supplier, C = Customer

// ============================================================
// FIELD NAVIGATION ORDER for Enter Key
// ============================================================
const FIELD_ORDER = [
    'debitNoteDate',
    'reasonDisplay',
    'partySupplier',
    'partySearchInput',
    'salesmanDisplay',
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
    'roundOff'
];

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // Initialize Custom Party Search (Replacing Select2)
    initPartySearch();
    
    // Initialize keyboard navigation
    initKeyboardNavigation();
    
    document.getElementById('debitNoteDate').addEventListener('change', updateDayName);
    
    document.querySelectorAll('input[name="debit_party_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentPartyType = this.value;
            updatePartyDropdown();
        });
    });
    
    // HSN search handler
    document.getElementById('hsn_modal_search').addEventListener('input', debounce(filterHsnCodes, 300));
    
    // Load HSN codes
    loadHsnCodes();
    
    // Auto-focus date field on page load
    setTimeout(function() {
        const dateField = document.getElementById('debitNoteDate');
        if (dateField) dateField.focus();
    }, 300);
    
    initCustomDropdowns();
});

function initCustomDropdowns() {
    // Reason Dropdown Helper
    var reasonDisplay = document.getElementById('reasonDisplay');
    var reasonOptions = document.getElementById('reasonOptions');
    var reasonHidden = document.getElementById('reason');

    if (reasonDisplay && reasonOptions) {
        reasonDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            var isShowing = reasonOptions.style.display === 'block';
            document.querySelectorAll('.list-group.position-absolute').forEach(el => {
                if(el.id !== 'partySearchResults') el.style.display = 'none';
            });
            reasonOptions.style.display = isShowing ? 'none' : 'block';
        });

        reasonOptions.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                e.stopPropagation();
                reasonHidden.value = e.target.getAttribute('data-value');
                reasonDisplay.value = e.target.textContent.trim();
                reasonOptions.style.display = 'none';
                
                // Trigger transition
                setTimeout(function() {
                    const checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
                    if (checkedRadio) checkedRadio.focus();
                }, 30);
            }
        });
    }

    // Salesman Dropdown Helper
    var salesmanDisplay = document.getElementById('salesmanDisplay');
    var salesmanOptions = document.getElementById('salesmanOptions');
    var salesmanHidden = document.getElementById('salesmanSelect');

    if (salesmanDisplay && salesmanOptions) {
        salesmanDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            var isShowing = salesmanOptions.style.display === 'block';
            document.querySelectorAll('.list-group.position-absolute').forEach(el => {
                if(el.id !== 'partySearchResults') el.style.display = 'none';
            });
            salesmanOptions.style.display = isShowing ? 'none' : 'block';
        });

        salesmanOptions.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                e.stopPropagation();
                salesmanHidden.value = e.target.getAttribute('data-value');
                salesmanDisplay.value = e.target.textContent.trim();
                salesmanOptions.style.display = 'none';
                
                // Trigger transition
                setTimeout(function() {
                    const checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
                    if (checkedRadio) checkedRadio.focus();
                }, 30);
            }
        });
    }

    // Close options when clicking outside
    document.addEventListener('click', function(e) {
        if (reasonDisplay && !reasonDisplay.contains(e.target) && !reasonOptions.contains(e.target)) {
            reasonOptions.style.display = 'none';
        }
        if (salesmanDisplay && !salesmanDisplay.contains(e.target) && !salesmanOptions.contains(e.target)) {
            salesmanOptions.style.display = 'none';
        }
    });

    // Keyboard navigation within custom options
    function setupOptionsKeys(displayEl, optionsEl, hiddenInput) {
        if (!displayEl || !optionsEl) return;
        
        displayEl.addEventListener('keydown', function(e) {
            var isShowing = optionsEl.style.display === 'block';
            
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!isShowing) {
                    optionsEl.style.display = 'block';
                    var firstOpt = optionsEl.querySelector('.list-group-item');
                    if (firstOpt) {
                        optionsEl.querySelectorAll('.list-group-item').forEach(i => i.classList.remove('active'));
                        firstOpt.classList.add('active');
                    }
                    return;
                }
                
                var items = Array.from(optionsEl.querySelectorAll('.list-group-item'));
                var activeIdx = items.findIndex(i => i.classList.contains('active'));
                
                if (activeIdx === -1) {
                    activeIdx = 0;
                } else if (e.key === 'ArrowDown') {
                    activeIdx = (activeIdx + 1) % items.length;
                } else {
                    activeIdx = (activeIdx - 1 + items.length) % items.length;
                }
                
                items.forEach(i => i.classList.remove('active'));
                items[activeIdx].classList.add('active');
                items[activeIdx].scrollIntoView({ block: 'nearest' });
            }
        });
    }

    setupOptionsKeys(reasonDisplay, reasonOptions, reasonHidden);
    setupOptionsKeys(salesmanDisplay, salesmanOptions, salesmanHidden);
}

// --- Custom Party Search Logic (Replacing Select2) ---
let searchTimeout = null;
let currentFocusIndex = -1; // For search results navigation

function initPartySearch() {
    const searchInput = document.getElementById('partySearchInput');
    const hiddenInput = document.getElementById('partySelect');
    const resultsContainer = document.getElementById('partySearchResults');
    
    console.log('Initializing Party Search...');
    console.log('Search Input:', searchInput);
    console.log('Hidden Input:', hiddenInput);
    console.log('Results Container:', resultsContainer);
    
    if (!searchInput || !hiddenInput || !resultsContainer) {
        console.error('Party search elements not found!');
        return;
    }

    // Input Handler
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        console.log('Search query:', query);
        hiddenInput.value = ''; // Clear ID while typing
        
        if (query.length === 0) {
            // Wait, fetch default parties instead of hiding
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
    const searchUrl = '<?php echo e(route("admin.debit-note.search-parties")); ?>';
    const resultsContainer = document.getElementById('partySearchResults');
    
    console.log('Fetching parties with query:', query);
    console.log('Search URL:', searchUrl);
    console.log('Party Type:', currentPartyType);
    
    resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="bi bi-hourglass-split"></i> Searching...</div>';
    resultsContainer.style.display = 'block';

    fetch(`${searchUrl}?q=${encodeURIComponent(query)}&party_type=${currentPartyType}&page=1`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
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
        const salesmanDisplay = document.getElementById('salesmanDisplay');
        const salesmanOptions = document.getElementById('salesmanOptions');
        if (salesmanDisplay) {
            salesmanDisplay.focus();
            if(salesmanOptions) {
                salesmanOptions.style.display = 'block';
                var firstOption = salesmanOptions.querySelector('.list-group-item');
                if (firstOption) {
                    salesmanOptions.querySelectorAll('.list-group-item').forEach(o => o.classList.remove('active'));
                    firstOption.classList.add('active');
                }
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

function updatePartyDropdown() {
    const searchInput = document.getElementById('partySearchInput');
    const hiddenInput = document.getElementById('partySelect');
    
    searchInput.value = '';
    hiddenInput.value = '';
    document.getElementById('partySearchResults').style.display = 'none';
    
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    
    // Update help text
    const helpText = document.querySelector('.party-search-container + small');
    if (helpText) {
        helpText.textContent = partyType === 'S' ? 'Start typing to search for suppliers' : 'Start typing to search for customers';
    }
    searchInput.placeholder = partyType === 'S' ? 'Search supplier...' : 'Search customer...';
    
    // Auto-select appropriate account type
    if (currentPartyType === 'S') {
        document.getElementById('accountPurchase').checked = true;
    } else {
        document.getElementById('accountSale').checked = true;
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function updateDayName() {
    const dateInput = document.getElementById('debitNoteDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

// Load HSN codes from server
function loadHsnCodes() {
    fetch('<?php echo e(route("admin.hsn-codes.index")); ?>?all=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        hsnCodesData = data;
        renderHsnCodes(hsnCodesData);
        document.getElementById('hsn_modal_loading').style.display = 'none';
        document.getElementById('hsn_modal_table_container').style.display = 'block';
    })
    .catch(error => {
        console.error('Error loading HSN codes:', error);
        document.getElementById('hsn_modal_loading').innerHTML = '<p class="text-danger">Error loading HSN codes</p>';
    });
}

// Render HSN codes in modal
function renderHsnCodes(codes) {
    const tbody = document.getElementById('hsn_codes_list');
    tbody.innerHTML = '';
    
    if (codes.length === 0) {
        document.getElementById('hsn_modal_table_container').style.display = 'none';
        document.getElementById('hsn_modal_no_results').style.display = 'block';
        return;
    }
    
    document.getElementById('hsn_modal_table_container').style.display = 'block';
    document.getElementById('hsn_modal_no_results').style.display = 'none';
    
    codes.forEach(code => {
        const gstPercent = parseFloat(code.cgst_percent || 0) + parseFloat(code.sgst_percent || 0);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${code.hsn_code}</strong></td>
            <td class="small">${code.description || '-'}</td>
            <td class="text-center"><span class="badge bg-success">${gstPercent.toFixed(2)}%</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="selectHsnCode('${code.hsn_code}', ${code.cgst_percent || 0}, ${code.sgst_percent || 0})">
                    <i class="bi bi-plus-circle"></i> Select
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Filter HSN codes
function filterHsnCodes() {
    const searchTerm = document.getElementById('hsn_modal_search').value.toLowerCase();
    const filtered = hsnCodesData.filter(code => 
        code.hsn_code.toLowerCase().includes(searchTerm) || 
        (code.description && code.description.toLowerCase().includes(searchTerm))
    );
    renderHsnCodes(filtered);
}

// Open HSN modal
function openHsnModal() {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    // Store the currently focused element
    modalTriggerElement = document.activeElement;
    
    document.getElementById('hsn_modal_search').value = '';
    renderHsnCodes(hsnCodesData);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
        document.getElementById('hsn_modal_search').focus();
        
        // Initialize HSN modal keyboard navigation
        initHsnModalKeyboard();
    }, 10);
}

// HSN Modal Keyboard Navigation
let hsnSelectedIndex = -1;

function initHsnModalKeyboard() {
    // Remove existing listener if any
    document.removeEventListener('keydown', handleHsnModalKeyboard);
    
    // Add keyboard navigation
    document.addEventListener('keydown', handleHsnModalKeyboard, true);
}

function handleHsnModalKeyboard(e) {
    const modal = document.getElementById('hsnCodeModal');
    if (!modal || !modal.classList.contains('show')) return;
    
    const tbody = document.getElementById('hsn_codes_list');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Arrow Down → next row
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        e.stopPropagation();
        
        // Blur search input
        const searchInput = document.getElementById('hsn_modal_search');
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
        
        const searchInput = document.getElementById('hsn_modal_search');
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
        const searchInput = document.getElementById('hsn_modal_search');
        if (document.activeElement === searchInput && hsnSelectedIndex === -1) {
            return; // Let search work normally
        }
        
        e.preventDefault();
        e.stopPropagation();
        
        if (!rows.length) return;
        
        if (hsnSelectedIndex === -1 && rows.length > 0) {
            hsnSelectedIndex = 0;
            highlightHsnRow(rows, hsnSelectedIndex);
            return;
        }
        
        if (hsnSelectedIndex >= 0 && hsnSelectedIndex < rows.length) {
            const selectBtn = rows[hsnSelectedIndex].querySelector('button');
            if (selectBtn) selectBtn.click();
        }
        return;
    }
    
    // Escape → close modal
    if (e.key === 'Escape') {
        e.preventDefault();
        closeHsnModal(false); // No HSN selected
        hsnSelectedIndex = -1;
        return;
    }
    
    // Any letter/number key → refocus search input
    if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
        const searchInput = document.getElementById('hsn_modal_search');
        if (searchInput && document.activeElement !== searchInput) {
            searchInput.focus();
            hsnSelectedIndex = -1;
            highlightHsnRow(rows, -1);
        }
    }
}

function highlightHsnRow(rows, index) {
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

// Close HSN modal
function closeHsnModal(hsnSelected = false) {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    // Remove keyboard listener
    document.removeEventListener('keydown', handleHsnModalKeyboard, true);
    hsnSelectedIndex = -1;
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        document.body.style.overflow = '';
        
        // If no HSN was selected, restore focus to the trigger element
        if (!hsnSelected && modalTriggerElement) {
            setTimeout(() => {
                modalTriggerElement.focus();
                if (modalTriggerElement.select) modalTriggerElement.select();
            }, 100);
        }
    }, 300);
}

// Select HSN code and add row
function selectHsnCode(hsnCode, cgstPercent, sgstPercent) {
    const gstPercent = parseFloat(cgstPercent) + parseFloat(sgstPercent);
    addHsnRowWithData(hsnCode, gstPercent, cgstPercent, sgstPercent);
    closeHsnModal(true); // Pass true to indicate HSN was selected
}

// Add HSN row with data
function addHsnRowWithData(hsnCode, gstPercent, cgstPercent, sgstPercent) {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm hsn-code readonly-field" name="items[${hsnRowCount}][hsn_code]" value="${hsnCode}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})" onkeyup="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst readonly-field" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="${gstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="${cgstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="${sgstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="0.00" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Focus on amount field
    row.querySelector('.hsn-amount').focus();
    
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
    const cgstPercent = parseFloat(row.querySelector('.hsn-cgst-percent').value) || 0;
    const sgstPercent = parseFloat(row.querySelector('.hsn-sgst-percent').value) || 0;
    
    const cgstAmount = (amount * cgstPercent) / 100;
    const sgstAmount = (amount * sgstPercent) / 100;
    
    row.querySelector('.hsn-cgst-amount').value = cgstAmount.toFixed(2);
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

// Save debit note - Entry point
let modalTriggerElement = null; // Global variable to store trigger element for all modals

function saveDebitNote(print = false) {
    window.printAfterSave = print;
    
    const debitNoteDate = document.getElementById('debitNoteDate').value;
    const partyId = document.getElementById('partySelect').value;
    
    if (!debitNoteDate) {
        alert('Please select date');
        return;
    }
    
    if (!partyId) {
        alert('Please select party');
        return;
    }
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
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
    
    if (items.length === 0) {
        alert('Please add at least one HSN item');
        return;
    }
    
    // Store DN amount for adjustment
    window.dnAmount = parseFloat(document.getElementById('dnAmount').value || 0);
    
    // Store current focused element
    modalTriggerElement = document.activeElement;
    
    // Show save options modal
    showSaveOptionsModal();
}

// Show Save Options Modal
function showSaveOptionsModal() {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.add('show');
    
    document.addEventListener('keydown', handleSaveOptionsKeyboard, true);
    
    // Focus first button
    setTimeout(() => {
        const firstBtn = modal.querySelector('.save-options-buttons button.btn-secondary');
        if (firstBtn) firstBtn.focus();
    }, 100);
}

// Close Save Options Modal
function closeSaveOptionsModal(optionSelected = false) {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.remove('show');
    document.removeEventListener('keydown', handleSaveOptionsKeyboard, true);
    
    // If no option selected, restore focus
    if (!optionSelected && modalTriggerElement) {
        setTimeout(() => {
            modalTriggerElement.focus();
            if (modalTriggerElement.select) modalTriggerElement.select();
        }, 100);
    }
}

function handleSaveOptionsKeyboard(e) {
    const modal = document.getElementById('saveOptionsModal');
    if (!modal || !modal.classList.contains('show')) return;
    
    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (activeEl && activeEl.tagName === 'BUTTON' && modal.contains(activeEl)) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            activeEl.click();
            return;
        }
    }
    
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeSaveOptionsModal(false);
        return;
    }
    
    if (e.key === 'ArrowDown' || e.key === 'ArrowRight' || e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        const buttons = Array.from(modal.querySelectorAll('.save-options-buttons button'));
        if (buttons.length === 0) return;
        
        const activeIdx = buttons.indexOf(document.activeElement);
        let nextIdx = 0;
        
        if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
            nextIdx = activeIdx >= 0 ? (activeIdx + 1) % buttons.length : 0;
        } else {
            nextIdx = activeIdx >= 0 ? (activeIdx - 1 + buttons.length) % buttons.length : buttons.length - 1;
        }
        
        buttons[nextIdx].focus();
    }
}

// Save Without Adjustment
function saveWithoutAdjustment() {
    closeSaveOptionsModal(true); // Option selected
    submitDebitNote(false, []);
}

// Save With Adjustment
function saveWithAdjustment() {
    closeSaveOptionsModal(true); // Option selected
    
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="debit_party_type"]:checked')?.value;
    
    if (!partyId) {
        alert('Please select a party first');
        return;
    }
    
    // Fetch supplier invoices and credit notes for adjustment
    fetchSupplierInvoices(partyId);
}

// Fetch Supplier Invoices for Adjustment
function fetchSupplierInvoices(supplierId) {
    console.log('Fetching invoices for supplier:', supplierId);
    
    // Fetch both purchase invoices and credit notes
    Promise.all([
        fetch(`<?php echo e(url('admin/debit-note/supplier')); ?>/${supplierId}/purchase-invoices`)
            .then(r => {
                console.log('Purchase invoices response:', r.status);
                return r.json();
            }),
        fetch(`<?php echo e(url('admin/debit-note/supplier')); ?>/${supplierId}/credit-notes`)
            .then(r => {
                console.log('Credit notes response:', r.status);
                return r.json();
            })
    ])
    .then(([purchaseData, creditNoteData]) => {
        console.log('Purchase data:', purchaseData);
        console.log('Credit note data:', creditNoteData);
        
        const allInvoices = [];
        
        // Add purchase invoices
        if (purchaseData.success && purchaseData.invoices) {
            purchaseData.invoices.forEach(inv => {
                allInvoices.push({
                    id: inv.id,
                    type: 'PURCHASE',
                    trans_no: inv.bill_no || inv.trans_no,
                    date: inv.bill_date_formatted,
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
                    trans_no: cn.credit_note_no,
                    date: cn.credit_note_date_formatted,
                    balance: parseFloat(cn.balance_amount || cn.cn_amount || 0)
                });
            });
        }
        
        console.log('All invoices:', allInvoices);
        console.log('DN Amount:', window.dnAmount);
        
        showAdjustmentModal(allInvoices, window.dnAmount);
    })
    .catch(error => {
        console.error('Error fetching invoices:', error);
        alert('Error loading invoices: ' + error.message);
    });
}

// Show Adjustment Modal
function showAdjustmentModal(invoices, dnAmount) {
    const modalHTML = `
        <div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop"></div>
        <div class="adjustment-modal" id="adjustmentModal">
            <div class="adjustment-modal-content">
                <div class="adjustment-modal-header">
                    <h5 class="adjustment-modal-title"><i class="bi bi-receipt me-2"></i>Debit Note Adjustment</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeAdjustmentModal()"></button>
                </div>
                <div class="adjustment-modal-body">
                    <div style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" style="font-size: 12px; margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th style="width: 50px; text-align: center;">Sr.</th>
                                    <th style="width: 80px; text-align: center;">Type</th>
                                    <th style="width: 120px; text-align: center;">Invoice/CN No.</th>
                                    <th style="width: 100px; text-align: center;">Date</th>
                                    <th style="width: 110px; text-align: right;">Balance Amt.</th>
                                    <th style="width: 110px; text-align: center;">Adjust</th>
                                    <th style="width: 110px; text-align: right;">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoices.length > 0 ? invoices.map((invoice, index) => {
                                    const balance = parseFloat(invoice.balance || 0);
                                    const typeLabel = invoice.type === 'PURCHASE' ? '<span class="badge bg-primary">Purchase</span>' : '<span class="badge bg-warning">CN</span>';
                                    return `
                                    <tr>
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td style="text-align: center;">${typeLabel}</td>
                                        <td style="text-align: center;">${invoice.trans_no || '-'}</td>
                                        <td style="text-align: center;">${invoice.date || '-'}</td>
                                        <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${balance.toFixed(2)}</td>
                                        <td style="text-align: center;">
                                            <input type="number" class="form-control form-control-sm adjustment-input" 
                                                   id="adj_${invoice.id}_${invoice.type}" 
                                                   data-invoice-id="${invoice.id}"
                                                   data-invoice-type="${invoice.type}"
                                                   data-balance="${balance}"
                                                   value="0.00" 
                                                   min="0" 
                                                   max="${balance}"
                                                   step="0.01"
                                                   oninput="updateAdjustmentBalance()"
                                                   onchange="updateAdjustmentBalance()"
                                                   style="width: 90px; text-align: right;">
                                        </td>
                                        <td style="text-align: right;" id="balance_${invoice.id}_${invoice.type}">
                                            <span style="color: #ffc107;">₹ ${balance.toFixed(2)}</span>
                                        </td>
                                    </tr>
                                `}).join('') : '<tr><td colspan="7" class="text-center text-muted py-3">No pending invoices or credit notes found</td></tr>'}
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 500; color: #6c757d;"><kbd>ESC</kbd> to close</span>
                            <span style="font-weight: bold; font-size: 15px; color: #dc3545;">
                                Amount to Adjust: <span id="adjustmentBalance">₹ ${dnAmount.toFixed(2)}</span>
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="font-weight: 500; color: #495057; white-space: nowrap;">Auto Adjust:</label>
                            <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" 
                                   style="width: 120px;" step="0.01" value="${dnAmount.toFixed(2)}">
                            <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                                <i class="bi bi-magic me-1"></i>Distribute
                            </button>
                        </div>
                    </div>
                </div>
                <div class="adjustment-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustment()">
                        <i class="bi bi-check-circle me-1"></i>Save & Submit
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('adjustmentModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('adjustmentModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    window.adjustmentAmount = dnAmount;
    
    setTimeout(() => {
        document.getElementById('adjustmentModalBackdrop').classList.add('show');
        document.getElementById('adjustmentModal').classList.add('show');
    }, 10);
    
    document.addEventListener('keydown', handleAdjustmentKeyboard, true);
}

function handleAdjustmentKeyboard(e) {
    const modal = document.getElementById('adjustmentModal');
    if (!modal || !modal.classList.contains('show')) return;
    
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeAdjustmentModal(false);
    } else if (e.key === 's' && e.ctrlKey) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveAdjustment();
    }
}

// Update adjustment balance
function updateAdjustmentBalance() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.getAttribute('data-invoice-id');
        const invoiceType = input.getAttribute('data-invoice-type');
        const maxBalance = parseFloat(input.getAttribute('data-balance')) || 0;
        
        // Validate - can't adjust more than balance
        if (adjusted < 0) {
            adjusted = 0;
            input.value = '0.00';
        }
        if (adjusted > maxBalance) {
            adjusted = maxBalance;
            input.value = maxBalance.toFixed(2);
        }
        
        totalAdjusted += adjusted;
        
        // Update remaining column - show (balance - adjusted)
        const remaining = maxBalance - adjusted;
        const balanceCell = document.getElementById(`balance_${invoiceId}_${invoiceType}`);
        if (balanceCell) {
            // Green if fully adjusted (0), orange/warning if partial, red if negative
            let color = '#ffc107'; // warning/orange for partial
            if (remaining === 0) {
                color = '#28a745'; // green for fully adjusted
            } else if (remaining < 0) {
                color = '#dc3545'; // red for over-adjusted
            }
            balanceCell.innerHTML = `<span style="color: ${color}; font-weight: ${remaining === 0 ? 'bold' : 'normal'};">₹ ${remaining.toFixed(2)}</span>`;
        }
    });
    
    // Update total remaining to adjust
    const remaining = window.adjustmentAmount - totalAdjusted;
    const balanceEl = document.getElementById('adjustmentBalance');
    balanceEl.textContent = `₹ ${remaining.toFixed(2)}`;
    
    // Color: green if 0, red if negative or positive (still needs adjustment)
    if (remaining === 0) {
        balanceEl.style.color = '#28a745';
    } else if (remaining < 0) {
        balanceEl.style.color = '#dc3545';
    } else {
        balanceEl.style.color = '#dc3545';
    }
}

// Auto Distribute Amount
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        alert('Please enter a valid amount');
        return;
    }
    
    document.querySelectorAll('.adjustment-input').forEach(input => input.value = '');
    
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    const transactions = inputs.map(input => ({
        input: input,
        balance: parseFloat(input.getAttribute('data-balance'))
    })).filter(t => t.balance > 0).sort((a, b) => b.balance - a.balance);
    
    let remaining = totalAmount;
    
    transactions.forEach(t => {
        if (remaining <= 0) return;
        const adjustAmount = Math.min(remaining, t.balance);
        t.input.value = adjustAmount.toFixed(2);
        remaining -= adjustAmount;
    });
    
    updateAdjustmentBalance();
}

// Close Adjustment Modal
function closeAdjustmentModal(adjustmentSaved = false) {
    const modal = document.getElementById('adjustmentModal');
    const backdrop = document.getElementById('adjustmentModalBackdrop');
    
    if (modal) modal.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
        
        // If no adjustment saved, restore focus
        if (!adjustmentSaved && modalTriggerElement) {
            setTimeout(() => {
                modalTriggerElement.focus();
                if (modalTriggerElement.select) modalTriggerElement.select();
            }, 100);
        }
    }, 300);
    
    document.removeEventListener('keydown', handleAdjustmentKeyboard, true);
}

// Save Adjustment
function saveAdjustment() {
    const inputs = document.querySelectorAll('.adjustment-input');
    const adjustments = [];
    
    inputs.forEach(input => {
        const adjusted = parseFloat(input.value || 0);
        if (adjusted > 0) {
            adjustments.push({
                invoice_id: input.getAttribute('data-invoice-id'),
                invoice_type: input.getAttribute('data-invoice-type'),
                adjusted_amount: adjusted
            });
        }
    });
    
    const remainingText = document.getElementById('adjustmentBalance').textContent.replace('₹', '').trim();
    const remaining = parseFloat(remainingText);
    
    if (remaining !== 0) {
        if (!confirm(`Balance remaining is ₹${remaining.toFixed(2)}. Continue anyway?`)) {
            return;
        }
    }
    
    closeAdjustmentModal(true); // Adjustment saved
    submitDebitNote(true, adjustments);
}

// Submit Debit Note
function submitDebitNote(withAdjustment = false, adjustments = []) {
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
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
    
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    const partyId = document.getElementById('partySelect').value;
    // Get party name from custom search input
    const partyName = document.getElementById('partySearchInput').value || window.selectedPartyName || '';
    
    const data = {
        header: {
            debit_note_date: document.getElementById('debitNoteDate').value,
            day_name: document.getElementById('dayName').value,
            debit_party_type: partyType,
            debit_party_id: partyId,
            debit_party_name: partyName,
            credit_account_type: document.querySelector('input[name="credit_account_type"]:checked').value,
            credit_account_no: document.getElementById('accountNo').value,
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
            dn_amount: document.getElementById('dnAmount').value,
            narration: document.getElementById('narration').value,
        },
        items: items,
        with_adjustment: withAdjustment,
        adjustments: adjustments
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.debit-note.store")); ?>', {
        method: 'POST',
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
            if (window.printAfterSave) {
                window.open(`<?php echo e(url('admin/debit-note')); ?>/${result.id}/show`, '_blank');
            }
            window.location.href = '<?php echo e(route("admin.debit-note.invoices")); ?>';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving debit note');
    });
}

// ============================================================
// KEYBOARD NAVIGATION SYSTEM
// ============================================================
function initKeyboardNavigation() {
    // PREVENT form submission on Enter key globally
    document.getElementById('debitNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    // ==============================================
    // TAB KEY - Switch between Left (Debit) and Right (Credit) sections
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab' && !e.shiftKey && !e.ctrlKey && !e.altKey) {
            const activeEl = document.activeElement;
            
            // Define left section fields (Debit - Party)
            const leftSectionFields = ['partySupplier', 'partyCustomer', 'partySelect', 'salesmanSelect'];
            
            // Define right section fields (Credit - Account)
            const rightSectionFields = ['accountPurchase', 'accountSale', 'accountGeneral', 'accountNo', 'invRefNo', 'invoiceDate', 'gstVno'];
            
            // Check if we're in left section and on last field (salesmanSelect)
            if (activeEl.id === 'salesmanDisplay') {
                e.preventDefault();
                e.stopPropagation();
                
                // Jump to right section - Account Type radio
                setTimeout(function() {
                    const accountRadio = document.querySelector('input[name="credit_account_type"]:checked');
                    if (accountRadio) {
                        accountRadio.focus();
                    }
                }, 30);
                return false;
            }
            
            // Check if we're in right section and on last field (gstVno)
            if (activeEl.id === 'gstVno') {
                e.preventDefault();
                e.stopPropagation();
                
                // Jump to Party Transaction Details section
                setTimeout(function() {
                    const partyTrnNo = document.getElementById('partyTrnNo');
                    if (partyTrnNo) {
                        partyTrnNo.focus();
                        partyTrnNo.select();
                    }
                }, 30);
                return false;
            }
        }
        
        // ==============================================
        // SHIFT+TAB - Reverse section switching
        // ==============================================
        if (e.key === 'Tab' && e.shiftKey && !e.ctrlKey && !e.altKey) {
            const activeEl = document.activeElement;
            
            // If on first field of right section (Account Type), go back to left section last field
            if (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral') {
                e.preventDefault();
                e.stopPropagation();
                
                // Jump back to left section - Salesman
                setTimeout(function() {
                    const salesmanDisplay = document.getElementById('salesmanDisplay');
                    if (salesmanDisplay) {
                        salesmanDisplay.focus();
                    }
                }, 30);
                return false;
            }
            
            // If on first field of Party Transaction Details, go back to right section last field
            if (activeEl.id === 'partyTrnNo') {
                e.preventDefault();
                e.stopPropagation();
                
                // Jump back to right section - GST Vno
                setTimeout(function() {
                    const gstVno = document.getElementById('gstVno');
                    if (gstVno) {
                        gstVno.focus();
                        gstVno.select();
                    }
                }, 30);
                return false;
            }
        }
    }, true);
    
    // ==============================================
    // Escape Key → Close any open modal
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Close HSN Modal
            const hsnModal = document.getElementById('hsnCodeModal');
            if (hsnModal && hsnModal.classList.contains('show')) {
                closeHsnModal();
                return false;
            }
            
            // Close Adjustment Modal
            const adjustmentModal = document.getElementById('adjustmentModal');
            if (adjustmentModal && adjustmentModal.classList.contains('show')) {
                closeAdjustmentModal();
                return false;
            }
            
            // Close Save Options Modal
            const saveOptionsModal = document.getElementById('saveOptionsModal');
            if (saveOptionsModal && saveOptionsModal.classList.contains('show')) {
                closeSaveOptionsModal();
                return false;
            }
        }
    }, true);

    // ==============================================
    // Ctrl+S → Save Debit Note
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            // Skip global save if any modal is open
            const hsnModal = document.getElementById('hsnCodeModal');
            const adjustmentModal = document.getElementById('adjustmentModal');
            const saveOptionsModal = document.getElementById('saveOptionsModal');
            
            if ((hsnModal && hsnModal.classList.contains('show')) ||
                (adjustmentModal && adjustmentModal.classList.contains('show')) ||
                (saveOptionsModal && saveOptionsModal.classList.contains('show'))) {
                return; // Let modal handlers deal with it
            }

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            saveDebitNote(false);
        }
    }, true);
    
    // ==============================================
    // F2 → Open HSN Modal
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            openHsnModal();
        }
    }, true);
    
    // ==============================================
    // Ctrl+Enter → Jump to TCS Amount
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const tcsField = document.getElementById('tcsAmount');
            if (tcsField) {
                tcsField.focus();
                tcsField.select();
            }
            return false;
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
        const hsnModal = document.getElementById('hsnCodeModal');
        const adjustmentModal = document.getElementById('adjustmentModal');
        const saveOptionsModal = document.getElementById('saveOptionsModal');
        if ((hsnModal && hsnModal.classList.contains('show')) ||
            (adjustmentModal && adjustmentModal.classList.contains('show')) ||
            (saveOptionsModal && saveOptionsModal.classList.contains('show'))) {
            return; // Let modal handlers deal with it
        }
        
        // ============================================
        // SHIFT+ENTER → BACKWARD NAVIGATION
        // ============================================
        if (e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Date → nothing (first field)
            if (activeEl.id === 'debitNoteDate') {
                return false;
            }
            
            // Reason → back to Date
            if (activeEl.id === 'reasonDisplay') {
                const dateField = document.getElementById('debitNoteDate');
                if (dateField) dateField.focus();
                return false;
            }
            
            // Party Type radios → back to Reason
            if (activeEl.id === 'partySupplier' || activeEl.id === 'partyCustomer') {
                const reasonDisplay = document.getElementById('reasonDisplay');
                if (reasonDisplay) {
                    reasonDisplay.focus();
                    var reasonOptions = document.getElementById('reasonOptions');
                    if (reasonOptions) reasonOptions.style.display = 'block';
                }
                return false;
            }
            
            // Party Search Input → back to Party Type radio
            if (activeEl.id === 'partySearchInput') {
                const checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
                if (checkedRadio) checkedRadio.focus();
                return false;
            }
            
            // Salesman → back to Party Search Input
            if (activeEl.id === 'salesmanDisplay') {
                const partySearch = document.getElementById('partySearchInput');
                if (partySearch) { partySearch.focus(); partySearch.select(); }
                return false;
            }
            
            // Account Type radios → back to Salesman
            if (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral') {
                const salesmanDisplay = document.getElementById('salesmanDisplay');
                if (salesmanDisplay) { 
                    salesmanDisplay.focus(); 
                    var salesmanOptions = document.getElementById('salesmanOptions');
                    if (salesmanOptions) salesmanOptions.style.display = 'block';
                }
                return false;
            }
            
            // Account No → back to Account Type radio
            if (activeEl.id === 'accountNo') {
                const checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
                if (checkedRadio) checkedRadio.focus();
                return false;
            }
            
            // Inv Ref No → back to Account No
            if (activeEl.id === 'invRefNo') {
                const accountNo = document.getElementById('accountNo');
                if (accountNo) { accountNo.focus(); accountNo.select(); }
                return false;
            }
            
            // Invoice Date → back to Inv Ref No
            if (activeEl.id === 'invoiceDate') {
                const invRefNo = document.getElementById('invRefNo');
                if (invRefNo) { invRefNo.focus(); invRefNo.select(); }
                return false;
            }
            
            // GST Vno → back to Invoice Date
            if (activeEl.id === 'gstVno') {
                const invoiceDate = document.getElementById('invoiceDate');
                if (invoiceDate) invoiceDate.focus();
                return false;
            }
            
            // Party Trn No → back to GST Vno
            if (activeEl.id === 'partyTrnNo') {
                const gstVno = document.getElementById('gstVno');
                if (gstVno) { gstVno.focus(); gstVno.select(); }
                return false;
            }
            
            // Party Trn Date → back to Party Trn No
            if (activeEl.id === 'partyTrnDate') {
                const partyTrnNo = document.getElementById('partyTrnNo');
                if (partyTrnNo) { partyTrnNo.focus(); partyTrnNo.select(); }
                return false;
            }
            
            // Amount → back to Party Trn Date
            if (activeEl.id === 'amount') {
                const partyTrnDate = document.getElementById('partyTrnDate');
                if (partyTrnDate) partyTrnDate.focus();
                return false;
            }
            
            // Narration → back to Amount
            if (activeEl.id === 'narration') {
                const amount = document.getElementById('amount');
                if (amount) { amount.focus(); amount.select(); }
                return false;
            }
            
            // TCS Amount → back to Narration
            if (activeEl.id === 'tcsAmount') {
                const narration = document.getElementById('narration');
                if (narration) narration.focus();
                return false;
            }
            
            // Round Off → back to TCS Amount
            if (activeEl.id === 'roundOff') {
                const tcsAmount = document.getElementById('tcsAmount');
                if (tcsAmount) { tcsAmount.focus(); tcsAmount.select(); }
                return false;
            }
            
            // HSN amount → back to previous HSN row or Amount field
            if (activeEl.classList.contains('hsn-amount')) {
                const allHsnAmounts = Array.from(document.querySelectorAll('#hsnTableBody .hsn-amount'));
                const currentIdx = allHsnAmounts.indexOf(activeEl);
                
                if (currentIdx > 0) {
                    allHsnAmounts[currentIdx - 1].focus();
                    allHsnAmounts[currentIdx - 1].select();
                } else {
                    const amount = document.getElementById('amount');
                    if (amount) { amount.focus(); amount.select(); }
                }
                return false;
            }
            
            return false;
        }
        
        // ============================================
        // ENTER → FORWARD NAVIGATION
        // ============================================
        
        // Handle buttons - explicitly trigger their click to ensure they work reliably on Enter
        if (activeEl.tagName === 'BUTTON' || activeEl.tagName === 'A') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            activeEl.click();
            return false;
        }
        
        // ---- Handle Date field Enter → jump to Reason ----
        if (activeEl.id === 'debitNoteDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            activeEl.blur();
            
            setTimeout(function() {
                const reasonDisplay = document.getElementById('reasonDisplay');
                const reasonOptions = document.getElementById('reasonOptions');
                if (reasonDisplay) {
                    reasonDisplay.focus();
                    if (reasonOptions) {
                        reasonOptions.style.display = 'block';
                        var firstOption = reasonOptions.querySelector('.list-group-item');
                        if (firstOption) {
                            reasonOptions.querySelectorAll('.list-group-item').forEach(o => o.classList.remove('active'));
                            firstOption.classList.add('active');
                        }
                    }
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle Reason select Enter → jump to checked Party Type radio ----
        if (activeEl.id === 'reasonDisplay') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var reasonOptions = document.getElementById('reasonOptions');
            var activeOption = reasonOptions ? reasonOptions.querySelector('.list-group-item.active') : null;
            
            if (activeOption) {
                var value = activeOption.getAttribute('data-value');
                var text = activeOption.textContent.trim();
                document.getElementById('reason').value = value;
                activeEl.value = text;
                if (reasonOptions) reasonOptions.style.display = 'none';
                
                setTimeout(function() {
                    const checkedRadio = document.querySelector('input[name="debit_party_type"]:checked');
                    if (checkedRadio) checkedRadio.focus();
                }, 30);
            } else {
                if (reasonOptions) reasonOptions.style.display = 'block';
            }
            
            return false;
        }
        
        // ---- Handle Supplier/Customer radio Enter → jump to Party Search Input ----
        if (activeEl.id === 'partySupplier' || activeEl.id === 'partyCustomer') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const partySearch = document.getElementById('partySearchInput');
                if (partySearch) {
                    partySearch.focus();
                    partySearch.select();
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle Party Search Input Enter → jump to Salesman (only if party selected) ----
        if (activeEl.id === 'partySearchInput') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const partyResults = document.getElementById('partySearchResults');
            
            // If dropdown is visible, select highlighted item
            if (partyResults && partyResults.style.display === 'block') {
                const activeItem = partyResults.querySelector('.list-group-item.active');
                
                if (activeItem) {
                    // Click the highlighted item to select the party
                    activeItem.click();
                } else {
                    // No item highlighted → highlight the first one
                    const firstItem = partyResults.querySelector('.list-group-item');
                    if (firstItem) {
                        currentFocusIndex = 0;
                        const allItems = partyResults.querySelectorAll('.list-group-item');
                        highlightItem(allItems, 0);
                    }
                }
            } else {
                // Dropdown not visible → if party already selected, move to Salesman
                const hiddenInput = document.getElementById('partySelect');
                if (hiddenInput && hiddenInput.value) {
                    setTimeout(function() {
                        const salesmanDisplay = document.getElementById('salesmanDisplay');
                        const salesmanOptions = document.getElementById('salesmanOptions');
                        if (salesmanDisplay) {
                            salesmanDisplay.focus();
                            if(salesmanOptions) {
                                salesmanOptions.style.display = 'block';
                                var firstOption = salesmanOptions.querySelector('.list-group-item');
                                if (firstOption) {
                                    salesmanOptions.querySelectorAll('.list-group-item').forEach(o => o.classList.remove('active'));
                                    firstOption.classList.add('active');
                                }
                            }
                        }
                    }, 50);
                }
            }
            return false;
        }
        
        // ---- Handle Salesman select Enter → jump to Account Type radio ----
        if (activeEl.id === 'salesmanDisplay') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var salesmanOptions = document.getElementById('salesmanOptions');
            var activeOption = salesmanOptions ? salesmanOptions.querySelector('.list-group-item.active') : null;
            
            if (activeOption) {
                var value = activeOption.getAttribute('data-value');
                var text = activeOption.textContent.trim();
                document.getElementById('salesmanSelect').value = value;
                activeEl.value = text;
                if (salesmanOptions) salesmanOptions.style.display = 'none';
                
                setTimeout(function() {
                    const checkedRadio = document.querySelector('input[name="credit_account_type"]:checked');
                    if (checkedRadio) checkedRadio.focus();
                }, 30);
            } else {
                if (salesmanOptions) salesmanOptions.style.display = 'block';
            }
            
            return false;
        }
        
        // ---- Handle Account Type radio Enter → jump to Account No ----
        if (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const accountNo = document.getElementById('accountNo');
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
                const invRefNo = document.getElementById('invRefNo');
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
                const invoiceDate = document.getElementById('invoiceDate');
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
            
            activeEl.blur();
            
            setTimeout(function() {
                const gstVno = document.getElementById('gstVno');
                if (gstVno) {
                    gstVno.focus();
                    gstVno.select();
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle GST Vno. Enter → jump to Party Trn. No. ----
        if (activeEl.id === 'gstVno') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const partyTrnNo = document.getElementById('partyTrnNo');
                if (partyTrnNo) {
                    partyTrnNo.focus();
                    partyTrnNo.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Party Trn. No. Enter → jump to Party Trn. Date ----
        if (activeEl.id === 'partyTrnNo') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const partyTrnDate = document.getElementById('partyTrnDate');
                if (partyTrnDate) {
                    partyTrnDate.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Party Trn. Date Enter → jump to Amount ----
        if (activeEl.id === 'partyTrnDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            activeEl.blur();
            
            setTimeout(function() {
                const amount = document.getElementById('amount');
                if (amount) {
                    amount.focus();
                    amount.select();
                }
            }, 50);
            
            return false;
        }
        
        // ---- Handle Amount field Enter → jump to Insert button ----
        if (activeEl.id === 'amount') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const insertBtn = document.getElementById('insertHsnBtn');
                if (insertBtn) {
                    insertBtn.focus();
                    insertBtn.click(); // Auto-open modal as soon as focus lands
                }
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
                const roundOff = document.getElementById('roundOff');
                if (roundOff) {
                    roundOff.focus();
                    roundOff.select();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle Round Off Enter → focus Save button ----
        if (activeEl.id === 'roundOff') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            setTimeout(function() {
                const saveBtn = document.querySelector('button[onclick*="saveDebitNote"]');
                if (saveBtn) {
                    saveBtn.focus();
                }
            }, 30);
            
            return false;
        }
        
        // ---- Handle HSN row Amount field Enter → next row ----
        if (activeEl.classList.contains('hsn-amount')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const allHsnAmounts = Array.from(document.querySelectorAll('#hsnTableBody .hsn-amount'));
            const currentIdx = allHsnAmounts.indexOf(activeEl);
            
            if (currentIdx >= 0 && currentIdx < allHsnAmounts.length - 1) {
                // Move to next row's amount
                allHsnAmounts[currentIdx + 1].focus();
                allHsnAmounts[currentIdx + 1].select();
            } else {
                // Last HSN row → move to Insert button and trigger it
                setTimeout(function() {
                    const insertBtn = document.getElementById('insertHsnBtn');
                    if (insertBtn) {
                        insertBtn.focus();
                        insertBtn.click();
                    }
                }, 30);
            }
            
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
    
    // We removed auto-advance on Select change since the custom dropdown handles it directly
}

// ============================================================
// FIELD NAVIGATION HELPER
// ============================================================
function navigateField(currentElement, direction) {
    const currentIndex = FIELD_ORDER.indexOf(currentElement.id);
    if (currentIndex === -1) return;

    const nextIndex = currentIndex + direction;
    if (nextIndex < 0 || nextIndex >= FIELD_ORDER.length) return;

    const nextId = FIELD_ORDER[nextIndex];
    let nextElement = document.getElementById(nextId);

    // Handle radio groups
    if (nextId === 'partySupplier' || nextId === 'accountPurchase') {
        const groupName = (nextId === 'partySupplier') ? 'debit_party_type' : 'credit_account_type';
        const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
        if (checkedRadio) nextElement = checkedRadio;
    }
    
    // Handle custom party search input
    if (nextId === 'partySearchInput') {
        const partySearch = document.getElementById('partySearchInput');
        if (partySearch) {
            partySearch.focus();
            partySearch.select();
        }
        return;
    }

    if (!nextElement || nextElement.disabled || nextElement.offsetParent === null) {
        navigateField({ id: nextId }, direction);
        return;
    }

    if (nextElement.readOnly && nextElement.tagName !== 'SELECT' && nextElement.tagName !== 'TEXTAREA') {
        navigateField({ id: nextId }, direction);
        return;
    }

    nextElement.focus();

    if (nextElement.tagName === 'INPUT' && nextElement.select) {
        nextElement.select();
    }
    
    if (nextElement.tagName === 'SELECT') {
        try {
            nextElement.showPicker();
        } catch(err) {
            const event = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
            nextElement.dispatchEvent(event);
        }
    }
}

</script>

<style>
.readonly-field {
    background-color: #e9ecef !important;
    cursor: not-allowed;
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

/* Adjustment Modal Styles */
.adjustment-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1080;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.adjustment-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.adjustment-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    width: 750px;
    max-width: 95%;
    z-index: 1085;
    opacity: 0;
    transition: all 0.3s ease;
}

.adjustment-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.adjustment-modal-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.adjustment-modal-header {
    padding: 15px 20px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.adjustment-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.adjustment-modal-body {
    padding: 20px;
    background: #fff;
}

.adjustment-modal-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.adjustment-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.adjustment-input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
}

/* Save Options Modal Styles */
.save-options-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1070;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.save-options-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
}

.save-options-modal-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    width: 420px;
    max-width: 90%;
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.save-options-modal.show .save-options-modal-content {
    transform: scale(1);
}

.save-options-modal-header {
    padding: 15px 20px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 10px 10px 0 0;
}

.save-options-modal-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.save-options-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
}

.save-options-modal-body {
    padding: 25px;
    text-align: center;
}

.save-options-modal-body p {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 20px;
}

.save-options-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.save-options-buttons .btn {
    padding: 10px 20px;
    font-weight: 500;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/debit-note/transaction.blade.php ENDPATH**/ ?>