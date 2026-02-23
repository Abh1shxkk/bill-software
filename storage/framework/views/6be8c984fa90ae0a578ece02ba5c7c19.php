<?php $__env->startSection('title', 'Quotation - Modification'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .qt-form { font-size: 11px; }
    .qt-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .qt-form input, .qt-form select, .qt-form textarea { font-size: 11px; padding: 2px 6px; height: 26px; }
    .qt-form textarea { height: auto; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #fd7e14; color: white; display: flex; justify-content: space-between; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; text-align: right; }
    .invoice-row:hover { background-color: #fff3cd !important; cursor: pointer; }
    .invoice-row.row-highlighted {
        background-color: #cce5ff !important;
        outline: 2px solid #0d6efd;
        outline-offset: -2px;
        border-left: 4px solid #0d6efd;
        font-weight: 600;
        transition: all 0.1s ease;
    }
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
        background-color: #e9ecef;
    }
    /* Visible focus outline for buttons and inputs */
    #loadQuotationBtn:focus {
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.7), 0 0 8px rgba(255, 193, 7, 0.5) !important;
        outline: 2px solid #ffc107 !important;
        outline-offset: 2px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="qt-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Quotation - Modification</h4>
                <div class="text-muted small">Load and modify existing quotation</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" id="loadQuotationBtn" onclick="showLoadModal()" tabindex="1" style="transition: box-shadow 0.2s;">
                    <i class="bi bi-folder2-open me-1"></i> Load Quotation
                </button>
                <a href="<?php echo e(route('admin.quotation.transaction')); ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Quotation
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="qtForm" method="POST" autocomplete="off" onsubmit="return false;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="quotation_id" name="quotation_id" value="">
                    
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="quotation_date" name="date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required tabindex="1" data-custom-enter>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">T.No :</label>
                                    <input type="text" id="quotation_no" class="form-control readonly-field" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <div class="custom-dropdown-container">
                                        <input type="text" id="customerSearchInput" class="form-control" placeholder="Search customer..." autocomplete="off" tabindex="10" data-custom-enter>
                                        <div class="custom-dropdown-list" id="customerDropdownList">
                                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="custom-dropdown-item" data-value="<?php echo e($customer->id); ?>" data-name="<?php echo e($customer->name); ?>"><?php echo e($customer->name); ?></div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <input type="hidden" id="customer_id" name="customer_id" value="">
                                    <input type="hidden" id="customer_name" name="customer_name" value="">
                                </div>
                                <div class="field-group mb-1">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control" data-custom-enter>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Terms :</label>
                                    <textarea id="terms" name="terms" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">Dis % :</label>
                                    <input type="number" id="discount_percent" name="discount_percent" class="form-control text-end" step="0.01" value="0" style="width: 80px;" onchange="calculateTotalAmount()" data-custom-enter>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 250px;">Item Name</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 80px;">MRP</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="addItemsBtn" onclick="showAddItemsModal()">
                                <i class="bi bi-search"></i> Add Items
                            </button>
                        </div>
                    </div>

                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Net :</label>
                            <input type="text" id="net_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label style="width: 40px;">Pack :</label><input type="text" id="packing" class="form-control readonly-field" readonly></div>
                                <div class="field-group"><label style="width: 40px;">Unit :</label><input type="text" id="unit" class="form-control readonly-field" readonly></div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label style="width: 45px;">Comp :</label><input type="text" id="company" class="form-control readonly-field" readonly></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="updateQuotation()"><i class="bi bi-save"></i> Update (End)</button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()"><i class="bi bi-trash"></i> Delete Item</button>
                            <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="bi bi-plus-circle"></i> Insert Item</button>
                            <button type="button" class="btn btn-info" onclick="showAddItemsModal()"><i class="bi bi-search"></i> Add Items</button>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="cancelQuotation()"><i class="bi bi-x-circle"></i> Cancel Quotation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'quotation-modification',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'quotation-modification',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Prevent global transaction-shortcuts from auto-focusing the first input
window.SKIP_AUTO_FOCUS = true;

let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let loadedQuotationId = null;

// MUST register on document BEFORE transaction-shortcuts.blade.php loads.
// Our script tag loads before the global shortcuts, so this handler registers first.
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const active = document.activeElement;
    if (!active) return;

    // Date field → Load Quotation button
    if (active.id === 'quotation_date') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        document.getElementById('loadQuotationBtn')?.focus();
        return;
    }

    // Table qty/rate inputs — block global handler, let inline onkeydown handle it
    if (active.closest('#itemsTableBody') && active.tagName === 'INPUT') {
        e.preventDefault();
        e.stopImmediatePropagation();
        // Manually call the correct handler
        const row = active.closest('tr');
        if (row) {
            const rowIndex = parseInt(row.dataset.rowIndex);
            if (active.name && active.name.includes('[qty]')) {
                handleQtyKeydown(e, rowIndex);
            } else if (active.name && active.name.includes('[rate]')) {
                handleRateKeydown(e, rowIndex);
            }
        }
        return;
    }

    // Remarks field
    if (active.id === 'remarks') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (e.shiftKey) {
            document.getElementById('customerSearchInput')?.focus();
        } else {
            document.getElementById('terms')?.focus();
        }
        return;
    }

    // Discount field
    if (active.id === 'discount_percent') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (e.shiftKey) {
            document.getElementById('terms')?.focus();
        } else {
            focusFirstRowQty();
        }
        return;
    }
}, true);

// ====================================================
// CUSTOM SEARCHABLE CUSTOMER DROPDOWN (No Select2)
// ====================================================
function initCustomerDropdown() {
    const searchInput = document.getElementById('customerSearchInput');
    const dropdownList = document.getElementById('customerDropdownList');
    const customerIdInput = document.getElementById('customer_id');
    const customerNameInput = document.getElementById('customer_name');
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
        let visibleCount = 0;
        allItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            if (!query || name.includes(query)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
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

    function selectCustomerItem(item) {
        customerIdInput.value = item.dataset.value;
        customerNameInput.value = item.dataset.name;
        searchInput.value = item.dataset.name;
        hideDropdown();
        // Move focus to Remarks
        document.getElementById('remarks')?.focus();
    }

    searchInput.addEventListener('focus', showDropdown);
    searchInput.addEventListener('input', () => {
        showDropdown();
        filterDropdown();
    });

    searchInput.addEventListener('blur', function() {
        setTimeout(hideDropdown, 200);
    });

    searchInput.addEventListener('keydown', function(e) {
        const visible = getVisibleItems();

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (dropdownList.style.display !== 'block') showDropdown();
            if (highlightedIndex < visible.length - 1) highlightItem(highlightedIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (highlightedIndex > 0) highlightItem(highlightedIndex - 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) {
                hideDropdown();
                document.getElementById('quotation_date')?.focus();
                return;
            }
            if (highlightedIndex >= 0 && visible[highlightedIndex]) {
                selectCustomerItem(visible[highlightedIndex]);
            } else if (visible.length > 0) {
                selectCustomerItem(visible[0]);
            } else {
                hideDropdown();
                document.getElementById('remarks')?.focus();
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    dropdownList.addEventListener('mousedown', function(e) {
        const item = e.target.closest('.custom-dropdown-item');
        if (item) {
            e.preventDefault();
            selectCustomerItem(item);
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
            hideDropdown();
        }
    });
}

// ====================================================
// KEYBOARD NAVIGATION: Date → LoadBtn → Modal → Table → Qty → Rate → loop
// ====================================================
function initKeyboardNavigation() {
    const dateField = document.getElementById('quotation_date');
    const loadBtn = document.getElementById('loadQuotationBtn');
    const customerSearch = document.getElementById('customerSearchInput');
    const remarksField = document.getElementById('remarks');
    const termsField = document.getElementById('terms');
    const discField = document.getElementById('discount_percent');

    // Date → Enter → focus Load Quotation button
    // Using capture-phase listener because type="date" inputs consume Enter internally
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                loadBtn?.focus();
            }
        }, true);
    }

    // Load Quotation button → Enter → open modal
    if (loadBtn) {
        loadBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                showLoadModal();
            }
        });
    }

    // Remarks → Enter → Terms, Shift+Enter → Name
    if (remarksField) {
        remarksField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey) {
                    customerSearch?.focus();
                } else {
                    termsField?.focus();
                }
            }
        });
    }

    // Terms → Enter → Dis%, Shift+Enter → Remarks
    if (termsField) {
        termsField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey) {
                    remarksField?.focus();
                } else {
                    discField?.focus();
                }
            }
        });
    }

    // Dis% → Enter → first row qty or Add Items, Shift+Enter → Terms
    if (discField) {
        discField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey) {
                    termsField?.focus();
                } else {
                    focusFirstRowQty();
                }
            }
        });
    }
}

// Helper: focus first item row qty or trigger Add Items
function focusFirstRowQty() {
    const firstRow = document.querySelector('#itemsTableBody tr');
    if (firstRow) {
        const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
        if (qtyInput) { qtyInput.focus(); qtyInput.select(); return; }
    }
    showAddItemsModal();
}

// ====================================================
// ITEM TABLE KEYBOARD HANDLERS
// ====================================================
function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter' || event.key === 'F2') {
        event.preventDefault();
        showItemModal(rowIndex);
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        if (!event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            if (row) {
                const rateInput = row.querySelector('input[name*="[rate]"]');
                if (rateInput) { rateInput.focus(); rateInput.select(); }
            }
        }
    }
}

function handleRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            if (row) {
                const qtyInput = row.querySelector('input[name*="[qty]"]');
                if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
            }
        } else {
            // Find next row that has an item (non-empty item_name)
            const currentRow = document.getElementById(`row-${rowIndex}`);
            if (currentRow) {
                let nextRow = currentRow.nextElementSibling;
                while (nextRow) {
                    const itemName = nextRow.querySelector('input[name*="[item_name]"]')?.value?.trim();
                    const qtyInput = nextRow.querySelector('input[name*="[qty]"]');
                    if (itemName && qtyInput) {
                        qtyInput.focus();
                        qtyInput.select();
                        return;
                    }
                    nextRow = nextRow.nextElementSibling;
                }
            }
            // No valid next row with item → trigger Add Items modal
            showAddItemsModal();
        }
    }
}

// ====================================================
// CALLBACK: Item + Batch selected from reusable modal
// ====================================================
window.onItemBatchSelectedFromModal = function(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    let rowIndex, row;

    if (selectedRowIndex !== null && document.getElementById(`row-${selectedRowIndex}`)) {
        rowIndex = selectedRowIndex;
        row = document.getElementById(`row-${rowIndex}`);
    } else {
        rowIndex = currentRowIndex++;
        row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        tbody.appendChild(row);
    }

    let expiryDisplay = '';
    if (batch.expiry_date) {
        try {
            const d = new Date(batch.expiry_date);
            expiryDisplay = `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getFullYear()).slice(-2)}`;
        } catch (e) { expiryDisplay = batch.expiry_date; }
    }

    const saleRate = parseFloat(batch.s_rate || batch.avg_s_rate || item.s_rate || 0);
    const mrp = parseFloat(batch.mrp || batch.avg_mrp || item.mrp || 0);

    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.batchId = batch.id;
    row.onclick = function() { selectRow(rowIndex); };

    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" value="${item.name || ''}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="1" step="0.001" data-custom-enter onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="${saleRate.toFixed(2)}" step="0.01" data-custom-enter onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="${saleRate.toFixed(2)}" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        </td>
    `;

    const qtyInput = row.querySelector('input[name*="[qty]"]');
    if (qtyInput) { qtyInput.focus(); qtyInput.select(); }

    if (typeof calculateRowAmount === 'function') calculateRowAmount(rowIndex);
    if (typeof calculateTotalAmount === 'function') calculateTotalAmount();
};

// Show item modal - redirect to reusable modal
function showItemModal(rowIndex) {
    selectedRowIndex = rowIndex;
    if (typeof window.openItemModal_chooseItemsModal === 'function') {
        window.openItemModal_chooseItemsModal();
    } else {
        alert('Item selection modal not available. Please reload the page.');
    }
}

// Show Add Items modal - for button click
function showAddItemsModal() {
    selectedRowIndex = null;
    if (typeof window.openItemModal_chooseItemsModal === 'function') {
        window.openItemModal_chooseItemsModal();
    } else {
        alert('Item selection modal not available. Please reload the page.');
    }
}

// ====================================================
// INIT ON DOM READY
// ====================================================
document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    initCustomerDropdown();
    initKeyboardNavigation();
    // Focus Load Quotation button on page load
    setTimeout(function() {
        document.getElementById('loadQuotationBtn').focus();
    }, 200);
});

function loadItems() {
    fetch('<?php echo e(route("admin.quotation.getItems")); ?>')
        .then(response => response.json())
        .then(data => { itemsData = data || []; })
        .catch(error => console.error('Error:', error));
}

let loadModalHighlightedIndex = -1;

function showLoadModal() {
    loadModalHighlightedIndex = -1;
    let html = `
        <div class="batch-modal-backdrop show" id="loadBackdrop"></div>
        <div class="batch-modal show" id="loadModal" style="max-width: 900px;">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Quotation</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeLoadModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by T.No or Customer...">
                </div>
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr><th>T.No</th><th>Date</th><th>Customer</th><th class="text-end">Amount</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody id="quotationsListBody"><tr><td colspan="6" class="text-center">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    
    const searchInput = document.getElementById('searchInput');
    searchInput?.focus();
    
    // Search on keyup (for typing), but handle navigation keys on keydown
    searchInput?.addEventListener('input', function() {
        loadModalHighlightedIndex = -1;
        searchQuotations();
    });
    
    searchInput?.addEventListener('keydown', function(e) {
        const rows = document.querySelectorAll('#quotationsListBody tr.invoice-row');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (rows.length > 0) {
                loadModalHighlightedIndex = Math.min(loadModalHighlightedIndex + 1, rows.length - 1);
                highlightLoadModalRow(rows);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (loadModalHighlightedIndex > 0) {
                loadModalHighlightedIndex--;
                highlightLoadModalRow(rows);
            } else {
                loadModalHighlightedIndex = -1;
                clearLoadModalHighlight(rows);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (loadModalHighlightedIndex >= 0 && rows[loadModalHighlightedIndex]) {
                rows[loadModalHighlightedIndex].click();
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeLoadModal();
        }
    });
    
    loadQuotations();
}

function highlightLoadModalRow(rows) {
    clearLoadModalHighlight(rows);
    if (loadModalHighlightedIndex >= 0 && rows[loadModalHighlightedIndex]) {
        rows[loadModalHighlightedIndex].classList.add('row-highlighted');
        rows[loadModalHighlightedIndex].scrollIntoView({ block: 'nearest' });
    }
}

function clearLoadModalHighlight(rows) {
    rows.forEach(row => row.classList.remove('row-highlighted'));
}

function loadQuotations(search = '') {
    fetch(`<?php echo e(route("admin.quotation.getQuotations")); ?>?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('quotationsListBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No quotations found</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(q => `
                <tr class="invoice-row" onclick="selectQuotation(${q.id})" style="cursor: pointer;">
                    <td><strong>${q.quotation_no}</strong></td>
                    <td>${new Date(q.quotation_date).toLocaleDateString('en-GB')}</td>
                    <td>${q.customer_name || '-'}</td>
                    <td class="text-end">₹${parseFloat(q.net_amount || 0).toFixed(2)}</td>
                    <td><span class="badge bg-${q.status === 'active' ? 'success' : 'danger'}">${q.status}</span></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectQuotation(${q.id})"><i class="bi bi-check"></i> Load</button></td>
                </tr>
            `).join('');
            loadModalHighlightedIndex = -1;
        })
        .catch(error => {
            document.getElementById('quotationsListBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading</td></tr>';
        });
}

function searchQuotations() {
    loadQuotations(document.getElementById('searchInput').value);
}

function closeLoadModal() {
    document.getElementById('loadModal')?.remove();
    document.getElementById('loadBackdrop')?.remove();
    loadModalHighlightedIndex = -1;
}

function selectQuotation(id) {
    closeLoadModal();
    fetch(`<?php echo e(url('admin/quotation')); ?>/${id}/edit`, { headers: { 'Accept': 'application/json' } })
        .then(response => response.json())
        .then(data => { if (data && data.id) populateForm(data); else alert('Quotation not found'); })
        .catch(error => { console.error('Error:', error); alert('Error loading quotation'); });
}

function updateCustomerName() {
    // Not needed anymore with custom dropdown, but kept for compatibility
}

function populateForm(quotation) {
    loadedQuotationId = quotation.id;
    document.getElementById('quotation_id').value = quotation.id;
    document.getElementById('quotation_date').value = quotation.quotation_date ? quotation.quotation_date.split('T')[0] : '';
    document.getElementById('quotation_no').value = quotation.quotation_no || '';
    
    const customerIdInput = document.getElementById('customer_id');
    const customerNameInput = document.getElementById('customer_name');
    const customerSearchInput = document.getElementById('customerSearchInput');
    
    const customerId = quotation.customer?.id || quotation.customer_id;
    const customerName = quotation.customer?.name || quotation.customer_name;
    
    customerIdInput.value = customerId || '';
    customerNameInput.value = customerName || '';
    if (customerSearchInput) customerSearchInput.value = customerName || '';
    
    document.getElementById('discount_percent').value = quotation.discount_percent || 0;
    document.getElementById('remarks').value = quotation.remarks || '';
    document.getElementById('terms').value = quotation.terms || '';
    document.getElementById('net_amount').value = parseFloat(quotation.net_amount || 0).toFixed(2);
    
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    if (quotation.items && quotation.items.length > 0) {
        quotation.items.forEach(item => addItemRowFromData(item));
    }
    calculateTotalAmount();
    
    // After loading quotation, focus first row qty.
    // Two-step: first focus at 150ms, then verify at 400ms and re-grab if stolen.
    setTimeout(() => {
        focusFirstRowQty();
        // Check again at 400ms — if focus was stolen by global scripts, re-grab it
        setTimeout(() => {
            const active = document.activeElement;
            const inTable = active && active.closest('#itemsTableBody');
            if (!inTable) {
                // Focus was stolen — take it back
                focusFirstRowQty();
            }
        }, 250);
    }, 150);
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || ''}" onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="${item.qty || 0}" data-custom-enter onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" data-custom-enter onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="${parseFloat(item.mrp || 0).toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.item_id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        </td>
    `;
    tbody.appendChild(row);
}

function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="0" data-custom-enter onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="0" data-custom-enter onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="0" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
        </td>
    `;
    tbody.appendChild(row);
    selectRow(rowIndex);

    setTimeout(() => {
        const codeInput = row.querySelector('input[name*="[code]"]');
        if (codeInput) codeInput.focus();
    }, 50);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) { row.classList.add('row-selected'); selectedRowIndex = rowIndex; }
}

function removeRow(rowIndex) {
    document.getElementById(`row-${rowIndex}`)?.remove();
    calculateTotalAmount();
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) { removeRow(selectedRowIndex); selectedRowIndex = null; }
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    row.querySelector('input[name*="[amount]"]').value = (qty * rate).toFixed(2);
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        total += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    });
    const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
    document.getElementById('net_amount').value = (total * (1 - discountPercent / 100)).toFixed(2);
}

function showItemModal(rowIndex) {
    selectedRowIndex = rowIndex;
    let html = `
        <div class="batch-modal-backdrop show" id="itemBackdrop"></div>
        <div class="batch-modal show" id="itemModal">
            <div class="modal-header-custom" style="background: #28a745;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <input type="text" class="form-control mb-3" id="itemSearchInput" placeholder="Search..." onkeyup="filterItems()">
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success"><tr><th>Code</th><th>Item Name</th><th>Packing</th><th class="text-end">Rate</th><th class="text-end">MRP</th></tr></thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
    renderItemsList();
}

function renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = itemsData.filter(item => !filter || item.name?.toLowerCase().includes(filter.toLowerCase()) || item.bar_code?.toLowerCase().includes(filter.toLowerCase()));
    tbody.innerHTML = filtered.map(item => `
        <tr class="item-row" onclick="selectItem(${item.id})" style="cursor: pointer;">
            <td>${item.bar_code || ''}</td><td>${item.name || ''}</td><td>${item.packing || ''}</td>
            <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td><td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function filterItems() { renderItemsList(document.getElementById('itemSearchInput').value); }

function selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (!item || selectedRowIndex === null) return;
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        calculateRowAmount(selectedRowIndex);
    }
    closeItemModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

function updateQuotation() {
    if (!loadedQuotationId) { alert('Please load a quotation first'); return; }
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value;
        if (itemName) {
            items.push({
                item_id: row.querySelector('input[name*="[item_id]"]')?.value || null,
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                item_name: itemName,
                qty: row.querySelector('input[name*="[qty]"]')?.value || 0,
                rate: row.querySelector('input[name*="[rate]"]')?.value || 0,
                mrp: row.querySelector('input[name*="[mrp]"]')?.value || 0,
                packing: row.querySelector('input[name*="[packing]"]')?.value || '',
                company_name: row.querySelector('input[name*="[company_name]"]')?.value || '',
                unit: row.querySelector('input[name*="[unit]"]')?.value || '',
            });
        }
    });
    if (items.length === 0) { alert('Please add at least one item'); return; }
    
    // Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`<?php echo e(url('admin/quotation')); ?>/${loadedQuotationId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({
            date: document.getElementById('quotation_date').value,
            customer_id: document.getElementById('customer_id').value,
            customer_name: document.getElementById('customer_name').value,
            discount_percent: document.getElementById('discount_percent').value,
            remarks: document.getElementById('remarks').value,
            terms: document.getElementById('terms').value,
            items: items
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) { alert('Quotation updated: ' + result.quotation_no); window.location.href = '<?php echo e(route("admin.quotation.index")); ?>'; }
        else { alert('Error: ' + result.message); }
    })
    .catch(error => { console.error('Error:', error); alert('Error updating quotation'); });
}

function cancelQuotation() {
    if (!loadedQuotationId) { alert('Please load a quotation first'); return; }
    if (!confirm('Are you sure you want to cancel this quotation?')) return;
    
    fetch(`<?php echo e(url('admin/quotation')); ?>/${loadedQuotationId}/cancel`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) { alert('Quotation cancelled'); window.location.href = '<?php echo e(route("admin.quotation.index")); ?>'; }
        else { alert('Error: ' + result.message); }
    })
    .catch(error => { console.error('Error:', error); alert('Error cancelling quotation'); });
}

// ====================================================
// GLOBAL SHORTCUTS: Ctrl+S to Save, Prevent Enter on table inputs from triggering buttons
// ====================================================
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save/update quotation
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        updateQuotation();
        return;
    }

    // Prevent Enter on table inputs from activating the X (delete) button
    // Only preventDefault here - do NOT stopPropagation so inline handlers still fire
    if (e.key === 'Enter' && !e.ctrlKey && !e.metaKey) {
        const active = document.activeElement;
        if (active && active.closest('#itemsTableBody')) {
            e.preventDefault();
        }
    }
}, true /* capture phase */);

// Block keyup Enter on table inputs to fully prevent browser button activation
window.addEventListener('keyup', function(e) {
    if (e.key !== 'Enter') return;
    const active = document.activeElement;
    if (active && active.closest('#itemsTableBody')) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }
}, true);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/quotation/modification.blade.php ENDPATH**/ ?>