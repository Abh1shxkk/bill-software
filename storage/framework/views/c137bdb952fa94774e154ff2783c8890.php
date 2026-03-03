<?php $__env->startSection('title', 'Replacement Note Transaction'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .rn-form { font-size: 11px; }
    .rn-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .rn-form input, .rn-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-selected td { background-color: #d4edff !important; }
    .row-complete { background-color: #d4edda !important; }
    .row-complete td { background-color: #d4edda !important; }
    .item-modal-backdrop, .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .item-modal-backdrop.show, .batch-modal-backdrop.show { display: block; }
    .item-modal, .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .item-modal.show, .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .create-batch-modal { max-width: 450px; }
    .create-batch-modal .modal-body-custom { background: #e8e8e8; }
    .create-batch-modal .form-label { color: #333; font-size: 12px; }
    .create-batch-modal input { font-size: 12px; }
    /* Custom dropdown styles */
    .custom-dropdown-container { position: relative; flex: 1; }
    .custom-dropdown-list {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        max-height: 200px; overflow-y: auto; background: white;
        border: 1px solid #ced4da; border-top: none; z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .custom-dropdown-item {
        padding: 4px 8px; cursor: pointer; font-size: 11px;
        border-bottom: 1px solid #f0f0f0;
    }
    .custom-dropdown-item:hover, .custom-dropdown-item.highlighted {
        background-color: #cce5ff;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="rn-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i> Replacement Note Transaction</h4>
                <div class="text-muted small">Create new replacement note</div>
            </div>
            <div>
                <a href="<?php echo e(route('admin.replacement-note.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="rnForm" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="d-flex gap-3 mb-2">
                            <!-- Left Side -->
                            <div style="width: 180px;">
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Date:</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" style="width: 120px;" value="<?php echo e(date('Y-m-d')); ?>" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Day:</label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field" style="width: 120px;" value="<?php echo e(date('l')); ?>" readonly>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Trn.No:</label>
                                    <input type="text" id="rn_no" name="rn_no" class="form-control readonly-field" style="width: 120px;" value="<?php echo e($nextRnNo); ?>" readonly>
                                </div>
                            </div>

                            <!-- Middle - Supplier Selection -->
                            <div class="inner-card flex-grow-1">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <div class="field-group">
                                            <label style="width: 60px;">Name:</label>
                                            <div class="custom-dropdown-container">
                                                <input type="text" id="supplierSearchInput" class="form-control" style="flex: 1;" placeholder="Search supplier..." autocomplete="off">
                                                <div class="custom-dropdown-list" id="supplierDropdownList">
                                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="custom-dropdown-item" data-value="<?php echo e($supplier->supplier_id); ?>" data-name="<?php echo e($supplier->name); ?>"><?php echo e($supplier->name); ?></div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                            <input type="hidden" id="supplier_id" name="supplier_id" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-sm w-100" id="insertItemsBtn" onclick="openInsertItemsModal()">
                                            <i class="bi bi-plus-square me-1"></i> Insert Items
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side - Summary -->
                            <div style="width: 200px;">
                                <div class="field-group mb-2">
                                    <label style="width: 120px;">Pending Br./Expiry:</label>
                                    <input type="text" id="pending_br_expiry" name="pending_br_expiry" class="form-control readonly-field text-end text-danger" style="width: 80px;" value="0.00" readonly>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 120px;">Balance Amount:</label>
                                    <input type="text" id="balance_amount" name="balance_amount" class="form-control readonly-field text-end text-danger" style="width: 80px;" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="itemsTableContainer">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 80px;">Code</th>
                                        <th style="width: 250px;">Item Name</th>
                                        <th style="width: 100px;">Batch</th>
                                        <th style="width: 80px;">Expiry</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">M.R.P</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-info" id="selectItemBtn" onclick="openItemModal()">
                                <i class="bi bi-list-check"></i> Select Item
                            </button>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Net:</label>
                            <input type="text" id="net_amount" name="net_amount" class="form-control readonly-field text-end" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 40px;">Pack:</label>
                                    <input type="text" id="pack" name="pack" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Unit:</label>
                                    <input type="text" id="unit" name="unit" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Cl. Qty:</label>
                                    <input type="text" id="cl_qty" name="cl_qty" class="form-control readonly-field text-end" value="0" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Comp:</label>
                                    <input type="text" id="comp" name="comp" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 40px;">Lctn:</label>
                                    <input type="text" id="lctn" name="lctn" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Srlno:</label>
                                    <input type="text" id="srlno" name="srlno" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 40px;">Case:</label>
                                    <input type="text" id="case_no" name="case_no" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Box:</label>
                                    <input type="text" id="box" name="box" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">SCM.</label>
                                    <input type="text" id="scm_percent" name="scm_percent" class="form-control text-end" style="width: 50px;" value="0">
                                    <span>+</span>
                                    <input type="text" id="scm_amount" name="scm_amount" class="form-control readonly-field text-end" style="width: 60px;" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <button type="button" class="btn btn-primary" onclick="saveTransaction()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#remarksModal">
                                <i class="bi bi-chat-text"></i> Remarks (F3)
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelTransaction()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'reusableItemsModal',
    'module' => 'replacement-note',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'reusableBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'reusableBatchModal',
    'module' => 'replacement-note',
    'showOnlyAvailable' => true,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="remarks" name="remarks" class="form-control" rows="4" placeholder="Enter remarks..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'replacement-note',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'replacement-note',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => true,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from reusable modal:', item);
    console.log('Batch selected from reusable modal:', batch);
    
    // Add a new row with item and batch data
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    // Format expiry date
    let expiryDisplay = '';
    if (batch.expiry_date) {
        try {
            const expiryDate = new Date(batch.expiry_date);
            expiryDisplay = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
        } catch (e) {
            expiryDisplay = batch.expiry_date;
        }
    }
    
    const purchaseRate = parseFloat(batch.p_rate || batch.pur_rate || batch.purchase_rate || 0);
    const mrp = parseFloat(batch.mrp || batch.avg_mrp || item.mrp || 0);
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.batchId = batch.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly onclick="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="1" step="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][amount]" value="${mrp.toFixed(2)}" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button>
        </td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
    `;
    
    tbody.appendChild(row);
    
    // Update footer display
    if (typeof updateFooter === 'function') {
        updateFooter(item);
    }
    
    // Focus qty field
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }
    
    calculateRowAmount(rowIndex);
    calculateTotals();
};

// Open Item Modal - use reusable modal
function openItemModal() {
    if (typeof openItemModal_reusableItemsModal === 'function') {
        openItemModal_reusableItemsModal();
    } else if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else {
        console.error('Item selection modal not found');
        alert('Item selection modal not found. Please reload the page.');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    initSupplierDropdown();
    initKeyboardNavigation();
    // Focus date field on page load
    document.getElementById('transaction_date')?.focus();
});

// ====================================================
// CUSTOM SEARCHABLE SUPPLIER DROPDOWN (No Select2)
// ====================================================
function initSupplierDropdown() {
    const searchInput = document.getElementById('supplierSearchInput');
    const dropdownList = document.getElementById('supplierDropdownList');
    const supplierIdInput = document.getElementById('supplier_id');
    if (!searchInput || !dropdownList) return;

    let highlightedIndex = -1;
    const allItems = dropdownList.querySelectorAll('.custom-dropdown-item');

    function showDropdown() {
        dropdownList.style.display = 'block';
        filterDropdown();
    }

    function hideDropdown() {
        dropdownList.style.display = 'none';
        highlightedIndex = -1;
        clearHighlights();
    }

    function filterDropdown() {
        const query = searchInput.value.toLowerCase().trim();
        allItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = (!query || name.includes(query)) ? 'block' : 'none';
        });
        highlightedIndex = -1;
        clearHighlights();
    }

    function getVisibleItems() {
        return Array.from(allItems).filter(item => item.style.display !== 'none');
    }

    function clearHighlights() {
        allItems.forEach(item => item.classList.remove('highlighted'));
    }

    function highlightItem(index) {
        const visible = getVisibleItems();
        clearHighlights();
        if (index >= 0 && index < visible.length) {
            visible[index].classList.add('highlighted');
            visible[index].scrollIntoView({ block: 'nearest' });
            highlightedIndex = index;
        }
    }

    function selectSupplierItem(item) {
        supplierIdInput.value = item.dataset.value;
        searchInput.value = item.dataset.name;
        window.selectedSupplierName = item.dataset.name;
        hideDropdown();
        // Move focus to Insert Items and trigger it immediately
        const insertBtn = document.getElementById('insertItemsBtn');
        if (insertBtn) {
            insertBtn.focus();
            setTimeout(() => {
                insertBtn.click();
            }, 20);
        }
    }

    searchInput.addEventListener('focus', showDropdown);
    searchInput.addEventListener('input', () => { showDropdown(); filterDropdown(); });
    searchInput.addEventListener('blur', function() { setTimeout(hideDropdown, 200); });

    function handleSupplierDropdownKeys(e) {
        const visible = getVisibleItems();
        if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;

        // Force supplier dropdown key flow; block any global/capture conflicts.
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (e.key === 'ArrowDown') {
            if (dropdownList.style.display !== 'block') showDropdown();
            if (highlightedIndex < visible.length - 1) highlightItem(highlightedIndex + 1);
        } else if (e.key === 'ArrowUp') {
            if (highlightedIndex > 0) highlightItem(highlightedIndex - 1);
        } else if (e.key === 'Enter') {
            if (e.shiftKey) {
                // Return to Date
                document.getElementById('transaction_date')?.focus();
            } else if (highlightedIndex >= 0 && visible[highlightedIndex]) {
                selectSupplierItem(visible[highlightedIndex]);
            } else if (visible.length > 0) {
                selectSupplierItem(visible[0]);
            } else {
                hideDropdown();
                const insertBtn = document.getElementById('insertItemsBtn');
                if (insertBtn) {
                    insertBtn.focus();
                }
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    }

    // Target key handler
    searchInput.addEventListener('keydown', handleSupplierDropdownKeys);

    // Capture-level guard to beat page/global handlers
    window.addEventListener('keydown', function(e) {
        if (document.activeElement !== searchInput) return;
        if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
        handleSupplierDropdownKeys(e);
    }, true);

    dropdownList.addEventListener('mousedown', function(e) {
        const item = e.target.closest('.custom-dropdown-item');
        if (item) { e.preventDefault(); selectSupplierItem(item); }
    });
}

// ====================================================
// KEYBOARD NAVIGATION: Date → Name → Insert Items
// ====================================================
function initKeyboardNavigation() {
    const dateField = document.getElementById('transaction_date');
    const insertBtn = document.getElementById('insertItemsBtn');

    // Date → Enter → Supplier Name
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('supplierSearchInput')?.focus();
            }
        });
    }

    // Insert Items button → Enter → open modal (or go back if Shift)
    if (insertBtn) {
        insertBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey) {
                    document.getElementById('supplierSearchInput')?.focus();
                } else {
                    openInsertItemsModal();
                }
            }
        });
    }
}


// Update Day Name
function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

// Update Supplier Name (compatibility wrapper)
function updateSupplierName() {
    // Custom dropdown handles this now
}

// Load Items
function loadItems() {
    fetch('<?php echo e(route("admin.items.get-all")); ?>')
        .then(response => response.json())
        .then(data => {
            itemsData = data.items || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// Add New Row (manual)
function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onblur="checkBatchOnBlur(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YYYY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

// Handle Code field keydown
function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            document.getElementById('insertItemsBtn')?.focus();
            return;
        }
        const codeInput = row?.querySelector('input[name*="[code]"]');
        if (codeInput && codeInput.value.trim()) {
            searchItemByCode(rowIndex, codeInput.value);
        }
    }
}

// Select Row
function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

// Update Footer from Row
function updateFooterFromRow(row) {
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
    
    // Use item data for company (company_short_name) and total_qty (sum of all batches)
    document.getElementById('pack').value = batchData.packing || itemData.packing || '';
    document.getElementById('unit').value = batchData.unit || itemData.unit || '1';
    document.getElementById('comp').value = itemData.company_short_name || itemData.mfg_by || '';
    // Cl. Qty shows total qty of all batches for this item (from itemData.total_qty)
    document.getElementById('cl_qty').value = itemData.total_qty || '0';
}

// Search Item by Code
function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code || i.item_code == code);
    if (item) {
        fillRowWithItem(rowIndex, item);
    }
}

// Fill Row with Item
function fillRowWithItem(rowIndex, item) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[code]"]').value = item.id || item.item_code || '';
    row.querySelector('input[name*="[name]"]').value = item.name || '';
    row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id;
    
    updateFooterFromRow(row);
    row.querySelector('input[name*="[batch]"]').focus();
}

// Check Batch - If batch doesn't exist, show create batch modal
let pendingBatchRowIndex = null;
let pendingBatchItemData = null;
let batchCheckInProgress = false;

function checkBatch(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const batchNo = row.querySelector('input[name*="[batch]"]').value.trim();
    
    if (!itemId || !batchNo) return;
    
    // Prevent duplicate checks
    if (batchCheckInProgress) return;
    batchCheckInProgress = true;
    
    // Store for later use
    pendingBatchRowIndex = rowIndex;
    pendingBatchItemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    fetch(`<?php echo e(route('admin.batches.check-batch')); ?>?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists && data.batches && data.batches.length > 0) {
                // Batch exists - show batch selection modal
                showBatchSelectionModal(data.batches, rowIndex, pendingBatchItemData);
            } else {
                // Batch doesn't exist - show create new batch modal
                showCreateBatchModal(rowIndex, batchNo, pendingBatchItemData);
            }
        })
        .catch(error => {
            console.error('Error checking batch:', error);
            // On error, show create batch modal
            showCreateBatchModal(rowIndex, batchNo, pendingBatchItemData);
        })
        .finally(() => {
            // Reset flag after a short delay to allow modal to open
            setTimeout(() => {
                batchCheckInProgress = false;
            }, 500);
        });
}

// Check Batch on Blur - Only triggers if not already checked via Enter key
function checkBatchOnBlur(rowIndex) {
    // Skip if a batch check is already in progress (triggered by Enter key)
    if (batchCheckInProgress) return;
    
    // Skip if a modal is already open
    if (document.getElementById('batchModal') || document.getElementById('createBatchModal')) return;
    
    checkBatch(rowIndex);
}

// Keyboard Navigation Handlers
function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[code]"]')?.focus();
            return;
        }
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        if (batchInput && batchInput.value.trim()) {
            checkBatch(rowIndex);
        } else {
            // No batch entered, move to expiry
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        }
    }
}

function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[batch]"]')?.focus();
        } else {
            row?.querySelector('input[name*="[qty]"]')?.focus();
        }
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        } else {
            row?.querySelector('input[name*="[mrp]"]')?.focus();
        }
    }
}

function handleMrpKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        triggerSelectItemFromMrp(rowIndex, event.shiftKey);
    }
}

let lastMrpEnterTriggerAt = 0;
function triggerSelectItemFromMrp(rowIndex, isShiftKey = false) {
    const now = Date.now();
    if (now - lastMrpEnterTriggerAt < 180) return; // de-duplicate fast double handlers
    lastMrpEnterTriggerAt = now;

    calculateRowAmount(rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (isShiftKey) {
        row?.querySelector('input[name*="[qty]"]')?.focus();
        return;
    }

    row?.classList.add('row-complete');
    const selectItemBtn = document.getElementById('selectItemBtn');
    if (selectItemBtn) {
        selectItemBtn.focus();
        setTimeout(() => {
            selectItemBtn.click();
        }, 20);
    }
}

// Capture fallback:
// If any global handler is stealing Enter from MRP field, force this flow here.
document.addEventListener('keydown', function(event) {
    if (event.key !== 'Enter') return;
    const activeEl = document.activeElement;
    if (!activeEl || activeEl.tagName !== 'INPUT') return;
    const nameAttr = activeEl.getAttribute('name') || '';
    if (!nameAttr.includes('[mrp]')) return;

    const row = activeEl.closest('tr');
    const rowIndex = row?.dataset?.rowIndex;
    if (rowIndex === undefined || rowIndex === null || rowIndex === '') return;

    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    triggerSelectItemFromMrp(parseInt(rowIndex, 10), event.shiftKey);
}, true);

// Show Batch Selection Modal (when batch exists) - Only shows batches with qty > 0
function showBatchSelectionModal(batches, rowIndex, itemData) {
    // Filter to only available batches (qty > 0)
    const availableBatches = batches.filter(b => (b.qty || 0) > 0);
    
    // If no available batches, show create new batch modal instead
    if (availableBatches.length === 0) {
        const batchNo = document.getElementById(`row-${rowIndex}`)?.querySelector('input[name*="[batch]"]')?.value || '';
        showCreateBatchModal(rowIndex, batchNo, itemData);
        return;
    }
    
    // Calculate total available stock
    const totalAvailableStock = availableBatches.reduce((sum, b) => sum + (b.qty || 0), 0);
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0">Batch Details</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-2">
                    <strong>BRAND:</strong> <span style="color: #6f42c1;">${itemData.name || ''}</span>
                    <span class="float-end"><strong>Packing:</strong> <span style="color: #6f42c1;">${itemData.packing || ''}</span></span>
                </div>
                <div class="mb-2">
                    <strong>Total Available Stock:</strong> <span class="text-success fw-bold">${totalAvailableStock}</span>
                </div>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px; background: #ffcccc;">
                        <thead style="background: #ffcccc;">
                            <tr>
                                <th>BATCH</th>
                                <th>DATE</th>
                                <th>RATE</th>
                                <th>P.RATE</th>
                                <th>MRP</th>
                                <th>Avl. QTY</th>
                                <th>EXP.</th>
                                <th>CODE</th>
                                <th>Cost+GST</th>
                                <th>SCM</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
    availableBatches.forEach(batch => {
        const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : '';
        const createdDate = batch.created_at ? new Date(batch.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '';
        html += `
            <tr style="cursor: pointer;" ondblclick='selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                <td><strong>${batch.batch_no || ''}</strong></td>
                <td>${createdDate}</td>
                <td class="text-end">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                <td class="text-end">${batch.qty || 0}</td>
                <td>${expiry}</td>
                <td>${batch.item_code || itemData.id || ''}</td>
                <td class="text-end">${parseFloat(batch.cost_gst || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.sale_scheme || 0).toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success py-0 px-1" onclick='selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check"></i>
                    </button>
                </td>
            </tr>`;
    });
    
    html += `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

// Select Batch from Modal
function selectBatchFromModal(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
    if (batch.expiry_date) {
        const d = new Date(batch.expiry_date);
        row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }
    row.querySelector('input[name*="[mrp]"]').value = parseFloat(batch.mrp || 0).toFixed(2);
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    // Update footer with batch data
    updateFooterFromRow(row);
    
    closeBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

// Show Create New Batch Modal
function showCreateBatchModal(rowIndex, batchNo, itemData) {
    const html = `
        <div class="batch-modal-backdrop show" id="createBatchBackdrop"></div>
        <div class="batch-modal show" id="createBatchModal" style="max-width: 450px;">
            <div class="modal-header-custom" style="background: #6c757d;">
                <h5 class="mb-0">Opening New Batch</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeCreateBatchModal()"></button>
            </div>
            <div class="modal-body-custom" style="background: #f0f0f0;">
                <div class="mb-3">
                    <strong>Item Name:</strong> <span style="color: #0d6efd; font-weight: bold;">${itemData.name || ''}</span>
                </div>
                <div class="mb-3">
                    <strong>Pack</strong> <span class="ms-3">:</span> <span style="color: #0d6efd; font-weight: bold;">${itemData.packing || ''}</span>
                </div>
                <div class="row mb-2">
                    <div class="col-4">
                        <label class="form-label mb-1"><strong>S.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${parseFloat(itemData.s_rate || itemData.mrp || 0).toFixed(2)}" step="0.01">
                    </div>
                    <div class="col-4">
                        <label class="form-label mb-1"><strong>Expiry :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY">
                    </div>
                    <div class="col-4">
                        <label class="form-label mb-1"><strong>MRP :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${parseFloat(itemData.mrp || 0).toFixed(2)}" step="0.01">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1"><strong>Location :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchLocation" value="">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1"><strong>Inclusive :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchInclusive" value="Y" maxlength="1" style="width: 50px;">
                    </div>
                </div>
                <input type="hidden" id="newBatchNo" value="${batchNo}">
                <input type="hidden" id="newBatchRowIndex" value="${rowIndex}">
                <input type="hidden" id="newBatchItemId" value="${itemData.id || ''}">
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-primary btn-sm" onclick="createNewBatch()">
                    <i class="bi bi-check-circle me-1"></i> OK
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('newBatchExpiry')?.focus();
}

// Close Create Batch Modal
function closeCreateBatchModal() {
    document.getElementById('createBatchModal')?.remove();
    document.getElementById('createBatchBackdrop')?.remove();
}

// Create New Batch - Just stores data in row, actual batch created on save
function createNewBatch() {
    const batchNo = document.getElementById('newBatchNo').value;
    const rowIndex = document.getElementById('newBatchRowIndex').value;
    const itemId = document.getElementById('newBatchItemId').value;
    const sRate = document.getElementById('newBatchSRate').value;
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = document.getElementById('newBatchMRP').value;
    const location = document.getElementById('newBatchLocation').value;
    
    if (!batchNo || !itemId) {
        alert('Batch number and item are required');
        return;
    }
    
    // Just update the row with new batch data - don't call API
    // Batch will be created in controller when saving transaction
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batchNo;
        row.querySelector('input[name*="[expiry]"]').value = expiry;
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(mrp).toFixed(2);
        
        // Mark as new batch (no batch_id means new batch will be created on save)
        row.dataset.batchId = ''; // Empty = new batch to be created
        row.dataset.isNewBatch = 'true';
        row.dataset.newBatchData = JSON.stringify({
            batch_no: batchNo,
            expiry: expiry,
            mrp: mrp,
            s_rate: sRate,
            location: location
        });
    }
    
    closeCreateBatchModal();
    
    // Focus on qty field
    setTimeout(() => {
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }, 100);
}

// Close Batch Modal
function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

// Select Batch (legacy - used by Insert Items flow)
function selectBatch(rowIndex, batch) {
    selectBatchFromModal(rowIndex, batch);
}

// Open Insert Items Modal - Redirect to reusable modal component
let pendingItemForBatch = null;

function openInsertItemsModal() {
    // Use reusable item selection modal
    if (typeof openItemModal_reusableItemsModal === 'function') {
        openItemModal_reusableItemsModal();
    } else {
        console.error('Reusable item modal not found');
        alert('Item selection modal not available. Please reload the page.');
    }
}

// Display Insert Item List
function displayInsertItemList(items) {
    const tbody = document.getElementById('insertItemListBody');
    if (!tbody) return;
    
    tbody.innerHTML = items.slice(0, 100).map(item => `
        <tr style="cursor: pointer;" ondblclick='selectInsertItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
            <td>${item.id || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.packing || ''}</td>
            <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-success" onclick='selectInsertItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                    <i class="bi bi-check"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Filter Insert Item List
function filterInsertItemList() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = itemsData.filter(i => 
        (i.name || '').toLowerCase().includes(search) || 
        (i.id || '').toString().includes(search)
    );
    displayInsertItemList(filtered);
}

// Select Insert Item - Close item modal and show batch modal
function selectInsertItem(item) {
    pendingItemForBatch = item;
    closeItemModal();
    
    // Fetch batches for this item
    fetch(`<?php echo e(url('admin/api/item-batches')); ?>/${item.id}`)
        .then(response => response.json())
        .then(data => {
            // Filter to only show batches with qty > 0 (available stock)
            const availableBatches = (data.batches || []).filter(b => (b.qty || 0) > 0);
            
            if (availableBatches.length > 0) {
                showInsertBatchModal(availableBatches, item);
            } else {
                // No available batches found, add item without batch
                addItemToTable(item, null);
            }
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
            addItemToTable(item, null);
        });
}

// Show Insert Batch Modal - Only shows batches with qty > 0
function showInsertBatchModal(batches, item) {
    // Filter to only available batches (qty > 0)
    const availableBatches = batches.filter(b => (b.qty || 0) > 0);
    
    // Calculate total available stock
    const totalAvailableStock = availableBatches.reduce((sum, b) => sum + (b.qty || 0), 0);
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch - ${item.name}</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModalAndReopen()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-2">
                    <strong>Total Available Stock:</strong> <span class="text-success fw-bold">${totalAvailableStock}</span>
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm" style="font-size: 11px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                            <tr>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th class="text-end">MRP</th>
                                <th class="text-end">P.Rate</th>
                                <th class="text-end">Avl. Qty</th>
                                <th style="width: 70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
    availableBatches.forEach(batch => {
        const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '';
        html += `
            <tr style="cursor: pointer; background: #d4edda;" ondblclick='selectInsertBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                <td><strong>${batch.batch_no || ''}</strong></td>
                <td>${expiry}</td>
                <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end"><strong>${batch.qty || 0}</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success" onclick='selectInsertBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check"></i>
                    </button>
                </td>
            </tr>`;
    });
    
    html += `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItemWithoutBatch()">
                    <i class="bi bi-plus"></i> Add Without Batch
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModalAndReopen()">Back to Items</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

// Select Insert Batch
function selectInsertBatch(batch) {
    closeBatchModal();
    addItemToTable(pendingItemForBatch, batch);
    pendingItemForBatch = null;
}

// Add Item Without Batch
function addItemWithoutBatch() {
    closeBatchModal();
    addItemToTable(pendingItemForBatch, null);
    pendingItemForBatch = null;
}

// Close Batch Modal and Reopen Item Modal
function closeBatchModalAndReopen() {
    closeBatchModal();
    pendingItemForBatch = null;
    openInsertItemsModal();
}

// Add Item to Table
function addItemToTable(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    if (batch) {
        row.dataset.batchId = batch.id;
        row.dataset.batchData = JSON.stringify(batch);
    }
    row.onclick = function() { selectRow(rowIndex); };
    
    const batchNo = batch ? batch.batch_no : '';
    const expiry = batch && batch.expiry_date ? (() => {
        const d = new Date(batch.expiry_date);
        return `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    })() : '';
    const mrp = batch ? parseFloat(batch.mrp || 0).toFixed(2) : parseFloat(item.mrp || 0).toFixed(2);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchNo}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onblur="checkBatchOnBlur(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiry}" placeholder="MM/YYYY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" value="${mrp}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    updateFooterFromRow(row);
    selectRow(rowIndex);
    
    // Focus based on whether batch is already filled
    setTimeout(() => {
        if (batchNo) {
            // Batch already selected, focus on qty
            row.querySelector('input[name*="[qty]"]')?.focus();
        } else {
            // No batch, focus on batch field
            row.querySelector('input[name*="[batch]"]')?.focus();
        }
    }, 100);
}

// Open Item Modal (legacy - for Select Item button)
function openItemModal() {
    openInsertItemsModal();
}

// Close Item Modal
function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

// Display Item List (legacy)
function displayItemList(items) {
    displayInsertItemList(items);
}

// Filter Item List (legacy)
function filterItemList() {
    filterInsertItemList();
}

// Select Item from Modal (legacy)
function selectItemFromModal(item) {
    selectInsertItem(item);
}

// Calculate Row Amount
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]').value) || 0;
    const amount = qty * mrp;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    calculateTotals();
}

// Calculate Totals
function calculateTotals() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        total += amount;
    });
    document.getElementById('net_amount').value = total.toFixed(2);
}

// Remove Row
function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

// Delete Selected Item
function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    }
}

// Cancel Transaction
function cancelTransaction() {
    if (confirm('Are you sure you want to cancel? All data will be lost.')) {
        window.location.reload();
    }
}

// Save Transaction
function saveTransaction() {
    const supplierId = document.getElementById('supplier_id').value;
    if (!supplierId) {
        alert('Please select a supplier');
        return;
    }
    
    // Collect items
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemId = row.dataset.itemId;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        
        if (itemId && qty > 0) {
            // Get batch_id - only if it's a valid number (existing batch)
            const batchIdStr = row.dataset.batchId;
            const batchId = (batchIdStr && batchIdStr !== '' && !isNaN(parseInt(batchIdStr))) 
                ? parseInt(batchIdStr) 
                : null;
            
            items.push({
                item_id: itemId,
                batch_id: batchId, // null for new batch, number for existing batch
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                name: row.querySelector('input[name*="[name]"]')?.value || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: qty,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0,
                amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const supplierName = window.selectedSupplierName || document.getElementById('supplierSearchInput')?.value || '';
    
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        transaction_date: document.getElementById('transaction_date').value,
        day_name: document.getElementById('day_name').value,
        supplier_id: supplierId,
        supplier_name: supplierName,
        pending_br_expiry: document.getElementById('pending_br_expiry').value,
        balance_amount: document.getElementById('balance_amount').value,
        net_amount: document.getElementById('net_amount').value,
        scm_percent: document.getElementById('scm_percent').value,
        scm_amount: document.getElementById('scm_amount').value,
        pack: document.getElementById('pack').value,
        unit: document.getElementById('unit').value,
        cl_qty: document.getElementById('cl_qty').value,
        comp: document.getElementById('comp').value,
        lctn: document.getElementById('lctn').value,
        srlno: document.getElementById('srlno').value,
        case_no: document.getElementById('case_no').value,
        box: document.getElementById('box').value,
        remarks: document.getElementById('remarks')?.value || '',
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.replacement-note.store")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Replacement note saved successfully! RN No: ' + result.rn_no);
            window.location.href = '<?php echo e(route("admin.replacement-note.index")); ?>';
        } else {
            alert('Error: ' + (result.message || 'Failed to save'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving replacement note');
    });
}
// ====================================================
// GLOBAL SHORTCUTS: Ctrl+S to Save, Ctrl+Enter to SCM
// ====================================================
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        e.stopPropagation();
        saveTransaction();
        return;
    }
    
    // Ctrl+Enter to jump to SCM field
    if (e.ctrlKey && e.key === 'Enter') {
        const hasOpenModal = document.querySelector('.modal.show, [id$="Modal"].show') || document.getElementById('batchModal') || document.getElementById('createBatchModal');
        if (hasOpenModal) return; // Don't trigger if a modal is open
        
        const scmField = document.getElementById('scm_percent');
        if (scmField) {
            e.preventDefault();
            e.stopPropagation();
            scmField.focus();
            scmField.select();
        }
    }
}, true);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/replacement-note/transaction.blade.php ENDPATH**/ ?>