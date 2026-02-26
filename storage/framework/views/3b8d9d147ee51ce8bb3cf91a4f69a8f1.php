<?php $__env->startSection('title', 'Credit Note Transaction'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-minus me-2"></i> Credit Note Transaction</h4>
        <div class="text-muted small">Create new credit note</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.credit-note.invoices')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
    </div>
</div>

<form id="creditNoteForm" autocomplete="off">
    <?php echo csrf_field(); ?>
    
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
                           value="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly tabindex="-1">
                </div>
                <div class="col-md-2">
                    <label for="creditNoteNo" class="form-label">Credit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="creditNoteNo" 
                           value="<?php echo e($nextCreditNoteNo); ?>" readonly tabindex="-1">
                </div>
                <div class="col-md-3">
                    <label for="reason" class="form-label">Reason</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="reasonDisplay" placeholder="Select Reason" readonly style="cursor: pointer; background-color: #fff;">
                        <input type="hidden" id="reason" name="reason">
                        <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="pointer-events: none;"></i>
                        <div id="reasonOptions" class="list-group position-absolute w-100 shadow-sm start-0" style="display:none; z-index: 1050; border: 1px solid #dee2e6;">
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
                                <select class="form-select" id="salesmanSelect" name="salesman_id">
                                    <option value="">Select Salesman</option>
                                    <?php $__currentLoopData = $salesmen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($salesman->id); ?>"><?php echo e($salesman->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #fff;">
                        <h6 class="text-secondary mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Account)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Account Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountPurchase" value="P" checked>
                                    <label class="btn btn-outline-secondary" for="accountPurchase">Purchase</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountSale" value="S">
                                    <label class="btn btn-outline-secondary" for="accountSale">Sale</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountGeneral" value="G">
                                    <label class="btn btn-outline-secondary" for="accountGeneral">General</label>
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
            <button type="button" class="btn btn-light btn-sm" onclick="openHsnModal()">
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
                            <label class="form-label small fw-bold text-success">CN Amount</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-success readonly-field" id="cnAmount" name="cn_amount" value="0.00" readonly style="font-size: 16px;">
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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo e(route('admin.credit-note.invoices')); ?>'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="saveCreditNote()">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveCreditNote(true)">
                        <i class="bi bi-printer me-1"></i> Save & Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Hidden field for credit note ID (for modification) -->
<input type="hidden" id="creditNoteId" value="">

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
            <h5><i class="bi bi-save me-2"></i>Save Credit Note</h5>
            <button type="button" class="save-options-close-btn" onclick="closeSaveOptionsModal()">&times;</button>
        </div>
        <div class="save-options-modal-body">
            <p>How would you like to save this Credit Note?</p>
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
let searchTimeout = null;
let partySearchResults = [];
let currentFocusIndex = -1; // For search results navigation

// Field Navigation Order
const FIELD_ORDER = [
    'creditNoteDate',
    'creditNoteNo', // Readonly
    'reasonDisplay', // Use Display Input for Custom Dropdown
    'partySupplier', // Radio Group Start (Supplier)
    'partySearchInput',
    'salesmanSelect',
    'accountPurchase', // Radio Group Start (Purchase)
    'accountNo',
    'invRefNo',
    'invoiceDate',
    'gstVno',
    // Save buttons logic handled separately
];

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // PREVENT form submission on Enter key globally
    document.getElementById('creditNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Initialize Custom Party Search
    initPartySearch();
    
    // Initialize Custom Reason Dropdown
    initCustomReasonDropdown();
    
    // Initialize Keyboard Navigation
    initKeyboardNavigation();
    
    // Date change handler
    document.getElementById('creditNoteDate').addEventListener('change', updateDayName);
    
    // Party type change handler
    document.querySelectorAll('input[name="credit_party_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentPartyType = this.value;
            updatePartyDropdown();
        });
    });
    
    // HSN search handler
    document.getElementById('hsn_modal_search').addEventListener('input', debounce(filterHsnCodes, 300));
    
    // Load HSN codes
    loadHsnCodes();
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeHsnModal();
    });
});

// --- Custom Reason Dropdown Logic ---
function initCustomReasonDropdown() {
    const displayInput = document.getElementById('reasonDisplay');
    const hiddenInput = document.getElementById('reason');
    const optionsContainer = document.getElementById('reasonOptions');
    const options = optionsContainer.querySelectorAll('.list-group-item');
    let activeIndex = -1;
    let openedProgrammatically = false; // Flag to prevent focus handler from resetting state

    if (!displayInput || !optionsContainer) return;

    // Expose a method to open dropdown programmatically from outside
    window.openReasonDropdownProgrammatically = function() {
        openedProgrammatically = true;
        displayInput.focus();
        optionsContainer.style.display = 'block';
        activeIndex = 0; // Pre-highlight first option for immediate arrow key use
        highlightOption(activeIndex);
        // Reset flag after a tick
        setTimeout(function() {
            openedProgrammatically = false;
        }, 50);
    };

    // Open on Focus (only if not opened programmatically)
    displayInput.addEventListener('focus', function() {
        if (!openedProgrammatically) {
            optionsContainer.style.display = 'block';
            activeIndex = -1; // Reset active index for manual focus
            highlightOption(activeIndex);
        }
    });

    // Close on Blur (with timeout to allow click)
    displayInput.addEventListener('blur', function() {
        setTimeout(() => {
            optionsContainer.style.display = 'none';
        }, 200);
    });
    
    // Toggle on Click
    displayInput.addEventListener('click', function() {
        if (optionsContainer.style.display === 'none') {
            optionsContainer.style.display = 'block';
            activeIndex = -1;
            highlightOption(activeIndex);
            displayInput.focus();
        } else {
            optionsContainer.style.display = 'none';
        }
    });

    // Handle Option Selection
    options.forEach((option, index) => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            selectOption(this);
        });
        
        // Ensure mouseover updates active index for visual feedback
        option.addEventListener('mouseenter', function() {
            activeIndex = index;
            highlightOption(activeIndex);
        });
    });

    function selectOption(optionElement) {
        const value = optionElement.getAttribute('data-value');
        const text = optionElement.textContent.trim();
        
        hiddenInput.value = value;
        displayInput.value = text; // Show text but value is used in backend
        optionsContainer.style.display = 'none';
        
        // After selecting reason, move cursor to Party Name search field
        setTimeout(function() {
            var partySearch = document.getElementById('partySearchInput');
            if (partySearch) {
                partySearch.focus();
            }
        }, 30);
    }

    function highlightOption(index) {
        options.forEach(opt => opt.classList.remove('active'));
        if (index >= 0 && index < options.length) {
            options[index].classList.add('active');
            options[index].scrollIntoView({ block: 'nearest' });
        }
    }

    // Keyboard Handling for Dropdown
    displayInput.addEventListener('keydown', function(e) {
        // If dropdown is closed and user presses navigation keys, open it
        if (optionsContainer.style.display === 'none' || optionsContainer.style.display === '') {
            if (e.key === 'ArrowDown' || e.key === ' ') {
                e.preventDefault();
                optionsContainer.style.display = 'block';
                activeIndex = 0;
                highlightOption(activeIndex);
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                optionsContainer.style.display = 'block';
                activeIndex = 0;
                highlightOption(activeIndex);
                return;
            }
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % options.length;
            highlightOption(activeIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + options.length) % options.length;
            highlightOption(activeIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeIndex < options.length) {
                selectOption(options[activeIndex]);
            } else if (optionsContainer.style.display === 'block') {
                 // If open but no selection, select first
                 if (options.length > 0) selectOption(options[0]);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            optionsContainer.style.display = 'none';
        } else if (e.key === 'Tab') {
            optionsContainer.style.display = 'none';
        }
    });
}

// --- Keyboard Navigation Logic ---
function initKeyboardNavigation() {
    // ==============================================
    // Ctrl+S to Save Credit Note
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            
            if (typeof saveCreditNote === 'function') {
                saveCreditNote();
            }
        }
    });

    // ==============================================
    // DOCUMENT-LEVEL CAPTURE: Intercept Enter on Date field
    // This fires BEFORE the date input's internal shadow DOM processing
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const activeEl = document.activeElement;

            // HSN modal is active -> let modal-specific handler manage Enter
            if (isHsnModalOpen()) {
                return;
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
            
            // ---- Handle Date field Enter → jump to Reason ----
            if (activeEl && activeEl.id === 'creditNoteDate' && !e.shiftKey) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                activeEl.blur();
                
                setTimeout(function() {
                    var reasonDisplay = document.getElementById('reasonDisplay');
                    var reasonOptions = document.getElementById('reasonOptions');
                    
                    if (reasonDisplay) {
                        reasonDisplay.focus();
                        
                        setTimeout(function() {
                            if (reasonOptions) {
                                reasonOptions.style.display = 'block';
                                var firstOption = reasonOptions.querySelector('.list-group-item');
                                if (firstOption) {
                                    reasonOptions.querySelectorAll('.list-group-item').forEach(function(opt) {
                                        opt.classList.remove('active');
                                    });
                                    firstOption.classList.add('active');
                                }
                            }
                        }, 20);
                    }
                }, 50);
                
                return false;
            }
            
            // ---- Handle Reason dropdown Enter → select highlighted option ----
            if (activeEl && activeEl.id === 'reasonDisplay') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                var reasonOptions = document.getElementById('reasonOptions');
                var reasonHidden = document.getElementById('reason');
                
                // Check if dropdown is open and has a highlighted option
                var activeOption = reasonOptions ? reasonOptions.querySelector('.list-group-item.active') : null;
                
                if (activeOption) {
                    // SELECT the highlighted option
                    var value = activeOption.getAttribute('data-value');
                    var text = activeOption.textContent.trim();
                    
                    reasonHidden.value = value;
                    activeEl.value = text;
                    
                    // Close dropdown
                    if (reasonOptions) reasonOptions.style.display = 'none';
                    
                    // Blur reason field
                    activeEl.blur();
                    
                    // Move cursor to Supplier radio button
                    setTimeout(function() {
                        var supplierRadio = document.getElementById('partySupplier');
                        if (supplierRadio) {
                            supplierRadio.focus();
                        }
                    }, 50);
                } else {
                    // No option highlighted - open dropdown and highlight first
                    if (reasonOptions) {
                        reasonOptions.style.display = 'block';
                        var firstOpt = reasonOptions.querySelector('.list-group-item');
                        if (firstOpt) {
                            firstOpt.classList.add('active');
                        }
                    }
                }
                
                return false;
            }
            
            // ---- Handle Supplier/Customer radio Enter → jump to Party Name ----
            if (activeEl && (activeEl.id === 'partySupplier' || activeEl.id === 'partyCustomer')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                setTimeout(function() {
                    var partySearch = document.getElementById('partySearchInput');
                    if (partySearch) {
                        partySearch.focus();
                    }
                }, 30);
                
                return false;
            }
            
            // ---- Handle Party Name search Enter → select highlighted party ----
            if (activeEl && activeEl.id === 'partySearchInput') {
                var partyResults = document.getElementById('partySearchResults');
                
                if (partyResults && partyResults.style.display === 'block') {
                    var activeItem = partyResults.querySelector('.list-group-item.active');
                    
                    if (activeItem) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        
                        // Click the highlighted item to select the party
                        activeItem.click();
                        
                        // selectParty() already handles moving to Salesman
                        
                        return false;
                    }
                }
            }
            
            // ---- Handle Account Type radio Enter → jump to Account No ----
            if (activeEl && (activeEl.id === 'accountPurchase' || activeEl.id === 'accountSale' || activeEl.id === 'accountGeneral')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                setTimeout(function() {
                    var accountNo = document.getElementById('accountNo');
                    if (accountNo) {
                        accountNo.focus();
                    }
                }, 30);
                
                return false;
            }
            
            // ---- Handle Amount field Enter → trigger Insert button ----
            if (activeEl && activeEl.id === 'amount') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (typeof openHsnModal === 'function') {
                    openHsnModal();
                }
                
                return false;
            }
            
            // ---- Handle HSN row Amount field Enter → trigger Insert button ----
            if (activeEl && activeEl.classList.contains('hsn-amount')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (typeof openHsnModal === 'function') {
                    openHsnModal();
                }
                
                return false;
            }
        }
    }, true); // CAPTURE PHASE - fires before any element-level handlers

    // ==============================================
    // General Enter key navigation for other fields
    // ==============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const target = e.target;

            // Block page-level Enter navigation while HSN modal is open
            if (isHsnModalOpen()) return;
            
            // Allow Custom Reason Dropdown to handle its own Enter
            if (target.id === 'reasonDisplay') return;

            // Allow Party Search to handle its own Enter
            if (target.id === 'partySearchInput') return;

            if (target.tagName === 'BUTTON' || target.tagName === 'A') return;
            if (target.tagName === 'TEXTAREA') return;

            // Allow native behavior for Select elements
            if (target.tagName === 'SELECT') return;

            // Date field is handled by capture-phase listener above
            if (target.id === 'creditNoteDate') return;

            e.preventDefault();
            const direction = e.shiftKey ? -1 : 1;
            navigateField(target, direction);
        }
    });

    // Auto-advance on Select change
    FIELD_ORDER.forEach(id => {
        const el = document.getElementById(id);
        if (el && el.tagName === 'SELECT') {
            el.addEventListener('change', function() {
                navigateField(this, 1);
            });
        }
    });
}

function isHsnModalOpen() {
    var modal = document.getElementById('hsnCodeModal');
    return !!(modal && modal.style.display === 'block');
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

    // Focus / Blur Handlers
    searchInput.addEventListener('focus', function() {
        // Trigger search immediately on focus, even if empty
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
    const searchUrl = '<?php echo e(route("admin.credit-note.search-parties")); ?>';
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
            // Open the select dropdown automatically
            try {
                salesmanSelect.showPicker();
            } catch(err) {
                // Fallback: simulate mousedown to open native dropdown
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

// --- Keyboard Navigation Logic ---
function navigateField(currentElement, direction) {
    let currentIndex = -1;
    let currentId = currentElement.id;

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

    // Special handling: Date field -> skip Day field -> go directly to Reason Custom Dropdown
    if (currentId === 'creditNoteDate' && direction === 1) {
        if (typeof window.openReasonDropdownProgrammatically === 'function') {
            window.openReasonDropdownProgrammatically();
        }
        return;
    }

    let nextIndex = currentIndex + direction;

    if (nextIndex < 0) return; 
    if (nextIndex >= FIELD_ORDER.length) {
        const saveBtn = document.querySelector('.btn-primary');
        if (saveBtn) saveBtn.focus();
        return;
    }

    const nextId = FIELD_ORDER[nextIndex];
    let nextElement = document.getElementById(nextId);

    if (nextId === 'partySupplier' || nextId === 'accountPurchase') {
        const groupName = (nextId === 'partySupplier') ? 'credit_party_type' : 'debit_account_type';
        const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
        if (checkedRadio) nextElement = checkedRadio;
    }
    
    if (nextElement) {
        // Skip disabled, hidden, or readonly fields 
        // EXCEPTION: 'reasonDisplay' is readonly but interactive
        // EXCEPTION: SELECT and TEXTAREA are interactive even if readonly (rare but safer)
        if (nextElement.disabled || nextElement.offsetParent === null) {
             navigateField({ id: nextId, type: nextElement.type, name: nextElement.name }, direction);
             return;
        }

        if (nextElement.readOnly && nextElement.id !== 'reasonDisplay' && nextElement.tagName !== 'SELECT' && nextElement.tagName !== 'TEXTAREA') {
            navigateField({ id: nextId, type: nextElement.type, name: nextElement.name }, direction);
            return;
        }
        
        nextElement.focus();
        
        if (nextElement.tagName === 'INPUT' && nextElement.select && nextElement.id !== 'reasonDisplay') {
            nextElement.select(); 
        }

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
    
    if (partyType === 'S') {
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
    const dateInput = document.getElementById('creditNoteDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

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

var hsnModalActiveIndex = -1;

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
    }, 10);
}

// HSN Modal keyboard navigation
function handleHsnModalKeyboard(e) {
    if (!isHsnModalOpen()) return;
    if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;

    // Handle at window capture level so modal key flow wins over page/global handlers
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    console.log('[KB-CN][HSN] key', { key: e.key, activeIndex: hsnModalActiveIndex });

    if (e.key === 'Escape') {
        closeHsnModal();
        return;
    }

    var rows = document.querySelectorAll('#hsn_codes_list tr');
    if (rows.length === 0) return;

    if (e.key === 'ArrowDown') {
        hsnModalActiveIndex++;
        if (hsnModalActiveIndex >= rows.length) hsnModalActiveIndex = 0;
        highlightHsnRow(hsnModalActiveIndex);
        return;
    }

    if (e.key === 'ArrowUp') {
        hsnModalActiveIndex--;
        if (hsnModalActiveIndex < 0) hsnModalActiveIndex = rows.length - 1;
        highlightHsnRow(hsnModalActiveIndex);
        return;
    }

    // Enter -> select highlighted row (default first row if none highlighted yet)
    if (hsnModalActiveIndex < 0) {
        hsnModalActiveIndex = 0;
        highlightHsnRow(hsnModalActiveIndex);
    }

    var selectedRow = rows[hsnModalActiveIndex];
    if (!selectedRow) return;

    var hsnCode = selectedRow.getAttribute('data-hsn-code');
    var cgst = parseFloat(selectedRow.getAttribute('data-cgst') || 0);
    var sgst = parseFloat(selectedRow.getAttribute('data-sgst') || 0);
    console.log('[KB-CN][HSN] Enter select', { index: hsnModalActiveIndex, hsnCode: hsnCode, cgst: cgst, sgst: sgst });
    selectHsnCode(hsnCode, cgst, sgst);
}
window.addEventListener('keydown', handleHsnModalKeyboard, true);

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
    addHsnRowWithData(hsnCode, gstPercent, cgstPercent, sgstPercent);
    closeHsnModal();
}

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
    const cnAmount = netAmount + tcsAmount + roundOff;
    
    document.getElementById('grossAmount').value = grossAmount.toFixed(2);
    document.getElementById('totalGst').value = totalGst.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('cnAmount').value = cnAmount.toFixed(2);
}

function saveCreditNote(print = false) {
    window.printAfterSave = print;
    
    const creditNoteDate = document.getElementById('creditNoteDate').value;
    const partyId = document.getElementById('partySelect').value;
    
    if (!creditNoteDate) {
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
            items.push({ hsn_code: hsnCode, amount: amount }); // Simplified check
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one HSN item');
        return;
    }
    
    window.cnAmount = parseFloat(document.getElementById('cnAmount').value || 0);
    showSaveOptionsModal();
}



var saveModalActiveIndex = 0;

function showSaveOptionsModal() {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.add('show');
    saveModalActiveIndex = 0;
    
    // Use capture phase so arrow keys work reliably
    document.addEventListener('keydown', handleSaveOptionsKeys, true);

    setTimeout(function() {
        highlightSaveButton(saveModalActiveIndex);
    }, 100);
}

function closeSaveOptionsModal() {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.remove('show');
    document.removeEventListener('keydown', handleSaveOptionsKeys, true);
}

function highlightSaveButton(index) {
    var modal = document.getElementById('saveOptionsModal');
    var buttons = modal.querySelectorAll('.save-options-buttons .btn');
    
    buttons.forEach(function(btn) {
        btn.style.outline = '';
        btn.style.outlineOffset = '';
        btn.style.transform = '';
        btn.style.boxShadow = '';
    });
    
    if (index >= 0 && index < buttons.length) {
        buttons[index].focus();
        buttons[index].style.outline = '3px solid #fff';
        buttons[index].style.outlineOffset = '2px';
        buttons[index].style.transform = 'scale(1.05)';
        buttons[index].style.boxShadow = '0 0 15px rgba(0,0,0,0.3)';
    }
}

function handleSaveOptionsKeys(e) {
    var modal = document.getElementById('saveOptionsModal');
    if (!modal.classList.contains('show')) return;
    
    var buttons = modal.querySelectorAll('.save-options-buttons .btn');
    if (buttons.length === 0) return;
    
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopImmediatePropagation();
        closeSaveOptionsModal();
        return;
    }
    
    if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
        e.preventDefault();
        e.stopImmediatePropagation();
        saveModalActiveIndex++;
        if (saveModalActiveIndex >= buttons.length) saveModalActiveIndex = 0;
        highlightSaveButton(saveModalActiveIndex);
    } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        e.preventDefault();
        e.stopImmediatePropagation();
        saveModalActiveIndex--;
        if (saveModalActiveIndex < 0) saveModalActiveIndex = buttons.length - 1;
        highlightSaveButton(saveModalActiveIndex);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (saveModalActiveIndex >= 0 && buttons[saveModalActiveIndex]) {
            buttons[saveModalActiveIndex].click();
        }
    }
}

function saveWithoutAdjustment() {
    closeSaveOptionsModal();
    submitCreditNote(false, []);
}

function saveWithAdjustment() {
    closeSaveOptionsModal();
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    fetchPartyInvoices(partyId, partyType);
}

function fetchPartyInvoices(partyId, partyType) {
    fetch('<?php echo e(route("admin.credit-note.party-invoices")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ party_id: partyId, party_type: partyType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdjustmentModal(data.invoices, window.cnAmount);
        } else {
            alert(data.message || 'Failed to load invoices');
        }
    })
    .catch(error => {
        alert('Error loading invoices');
    });
}

function showAdjustmentModal(invoices, cnAmount) {
    const modalHTML = `
        <div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop"></div>
        <div class="adjustment-modal" id="adjustmentModal">
            <div class="adjustment-modal-content">
                <div class="adjustment-modal-header">
                    <h5 class="adjustment-modal-title"><i class="bi bi-receipt me-2"></i>Credit Note Adjustment</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeAdjustmentModal()"></button>
                </div>
                <div class="adjustment-modal-body">
                    <div style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Ref No.</th>
                                    <th>Bill Amt</th>
                                    <th>Balance</th>
                                    <th style="width: 150px;">Adjust Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoices.map((inv, index) => `
                                    <tr>
                                        <td>${inv.date}</td>
                                        <td>${inv.trans_no}</td>
                                        <td>${inv.bill_amount.toFixed(2)}</td>
                                        <td>${inv.balance.toFixed(2)}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm adjust-amount" 
                                                data-id="${inv.id}" 
                                                data-balance="${inv.balance}"
                                                step="0.01" 
                                                min="0" 
                                                max="${inv.balance}"
                                                placeholder="0.00">
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Adjusted:</th>
                                    <th id="totalAdjustedDisplay">0.00 / ${cnAmount.toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="closeAdjustmentModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitAdjustments(${cnAmount})">Save Adjustments</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const div = document.createElement('div');
    div.innerHTML = modalHTML;
    document.body.appendChild(div);
    
    autoDistributeAdjustment(cnAmount);
    
    document.querySelectorAll('.adjust-amount').forEach(input => {
        input.addEventListener('input', updateTotalAdjusted);
    });
}

function closeAdjustmentModal() {
    const backdrop = document.getElementById('adjustmentModalBackdrop');
    const modal = document.getElementById('adjustmentModal');
    if (backdrop) backdrop.remove();
    if (modal) modal.parentElement.remove(); 
}

function autoDistributeAdjustment(totalAmount) {
    let remaining = totalAmount;
    const inputs = document.querySelectorAll('.adjust-amount');
    
    inputs.forEach(input => {
        if (remaining <= 0) {
            input.value = '';
            return;
        }
        
        const balance = parseFloat(input.dataset.balance);
        const adjust = Math.min(balance, remaining);
        
        input.value = adjust.toFixed(2);
        remaining -= adjust;
    });
    
    updateTotalAdjusted();
}

function updateTotalAdjusted() {
    let total = 0;
    document.querySelectorAll('.adjust-amount').forEach(input => {
        total += parseFloat(input.value || 0);
    });
    
    const display = document.getElementById('totalAdjustedDisplay');
    const split = display.textContent.split('/');
    const cnTotal = split[1].trim();
    
    display.textContent = `${total.toFixed(2)} / ${cnTotal}`;
    
    if (total > parseFloat(cnTotal)) {
        display.classList.add('text-danger');
    } else {
        display.classList.remove('text-danger');
    }
}

function submitAdjustments(maxAmount) {
    let total = 0;
    const adjustments = [];
    
    document.querySelectorAll('.adjust-amount').forEach(input => {
        const val = parseFloat(input.value || 0);
        if (val > 0) {
            total += val;
            adjustments.push({
                invoice_id: input.dataset.id,
                adjusted_amount: val
            });
        }
    });
    
    if (total > maxAmount) {
        alert(`Total adjustment (${total.toFixed(2)}) cannot exceed Credit Note amount (${maxAmount.toFixed(2)})`);
        return;
    }
    
    if (adjustments.length === 0) {
        if (!confirm('No adjustments made. Save without linking invoices?')) return;
    }
    
    closeAdjustmentModal();
    submitCreditNote(true, adjustments);
}

function submitCreditNote(withAdjustment = false, adjustments = []) {
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
    
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    const partyId = document.getElementById('partySelect').value;
    // Fix: Get Name from Custom Input, NOT Select2
    const partyName = document.getElementById('partySearchInput').value;
    
    const data = {
        header: {
            credit_note_date: document.getElementById('creditNoteDate').value,
            day_name: document.getElementById('dayName').value,
            credit_party_type: partyType,
            credit_party_id: partyId,
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
        items: items,
        with_adjustment: withAdjustment,
        adjustments: adjustments
    };
    
    const creditNoteId = document.getElementById('creditNoteId').value;
    const url = creditNoteId 
        ? `<?php echo e(url('admin/credit-note')); ?>/${creditNoteId}`
        : '<?php echo e(route("admin.credit-note.store")); ?>';
    const method = creditNoteId ? 'PUT' : 'POST';
    
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(url, {
        method: method,
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
                window.open(`<?php echo e(url('admin/credit-note')); ?>/${result.id}/show`, '_blank');
            }
            window.location.href = '<?php echo e(route("admin.credit-note.invoices")); ?>';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving credit note');
    });
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
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
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
    width: 700px;
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
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
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
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/credit-note/transaction.blade.php ENDPATH**/ ?>