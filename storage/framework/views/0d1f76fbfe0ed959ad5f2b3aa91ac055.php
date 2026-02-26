<?php $__env->startSection('title', 'Stock Transfer Incoming Modification'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .sti-form { font-size: 11px; }
    .sti-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .sti-form input, .sti-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group input, .field-group select { flex: 1; min-width: 0; }
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
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
    .item-row.selected { background-color: #bbdefb !important; }
    .invoice-row:hover { background-color: #d1ecf1 !important; }

    /* Custom Dropdown Styles */
    .custom-dropdown-item { padding: 5px 10px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 11px; }
    .custom-dropdown-item:hover, .custom-dropdown-item.active { background-color: #f0f8ff; }

    /* Invoice row highlight */
    #invoiceListBody tr.invoice-row-active { background-color: #cce5ff !important; outline: 2px solid #007bff; }
    #invoiceListBody tr.invoice-row-active td { background-color: #cce5ff !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="sti-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Stock Transfer Incoming Modification</h4>
                <div class="text-muted small">Modify existing stock transfer incoming</div>
            </div>
            <div>
                <button type="button" class="btn btn-info btn-sm" id="stim_loadInvoiceBtn" onclick="showLoadInvoiceModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoice
                </button>
                <a href="<?php echo e(route('admin.stock-transfer-incoming.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="<?php echo e(route('admin.stock-transfer-incoming.transaction')); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="stiForm" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="transaction_id" name="transaction_id">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="stim_transaction_date" name="transaction_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" onchange="updateDayName()" required data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="<?php echo e(date('l')); ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label style="width: 60px;">Supplier :</label>
                                    <div class="custom-dropdown" id="stim_supplierDropdownWrapper" style="flex: 1; position: relative;">
                                        <input type="text" class="form-control" id="stim_supplierDisplay" 
                                               placeholder="Select Supplier..." autocomplete="off"
                                               style="background: #e8ffe8; border: 2px solid #28a745;"
                                               onfocus="openSupplierDropdown()" onkeyup="filterSuppliers(event)" data-custom-enter>
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                        <div class="custom-dropdown-list" id="stim_supplierList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="custom-dropdown-item" 
                                                     data-value="<?php echo e($supplier->supplier_id); ?>" 
                                                     data-name="<?php echo e($supplier->name); ?>"
                                                     onclick="selectSupplier('<?php echo e($supplier->supplier_id); ?>', '<?php echo e(addslashes($supplier->name)); ?>')">
                                                    <?php echo e($supplier->name); ?>

                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">Trf.No. :</label>
                                    <input type="text" id="trf_no" name="trf_no" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="stim_remarks" name="remarks" class="form-control" data-custom-enter>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">ST Date:</label>
                                    <input type="date" id="stim_st_date" name="st_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No.:</label>
                                    <input type="text" id="stim_gr_no" name="gr_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date:</label>
                                    <input type="date" id="stim_gr_date" name="gr_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases:</label>
                                    <input type="text" id="stim_cases" name="cases" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport:</label>
                                    <input type="text" id="stim_transport" name="transport" class="form-control" data-custom-enter>
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
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 90px;">Batch</th>
                                        <th style="width: 60px;">Exp.</th>
                                        <th style="width: 50px;">Qty</th>
                                        <th style="width: 50px;">F.Qty</th>
                                        <th style="width: 70px;">P.RATE</th>
                                        <th style="width: 50px;">GST%</th>
                                        <th style="width: 80px;">F.T. Rate</th>
                                        <th style="width: 90px;">F.T. Amt.</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="stim_addItemsBtn" onclick="showItemSelectionModal()">
                                <i class="bi bi-search"></i> Add Items
                            </button>
                        </div>
                    </div>

                    <!-- Rates Section -->
                    <div class="inner-card mb-2">
                        <div class="row g-2">
                            <div class="col-md-1">
                                <label class="small">Inc.</label>
                                <input type="text" id="inclusive" class="form-control form-control-sm" placeholder="Y/N">
                            </div>
                            <div class="col-md-1">
                                <label class="small">EX.</label>
                                <input type="number" id="excise" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-1">
                                <label class="small">TSR</label>
                                <input type="number" id="tsr" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">SPL.RT.</label>
                                <input type="number" id="spl_rate" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">WS.RT.</label>
                                <input type="number" id="ws_rate" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">MRP</label>
                                <input type="number" id="batch_mrp" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">S.RATE</label>
                                <input type="number" id="s_rate" class="form-control form-control-sm" step="0.01" value="0" onkeydown="handleSRateKeydown(event)">
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>TOTAL AMT.:</label>
                            <input type="text" id="total_amount" name="total_amount" class="form-control readonly-field text-end" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Packing:</label>
                                    <input type="text" id="packing" name="packing" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Unit:</label>
                                    <input type="text" id="unit" name="unit" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Cl. Qty:</label>
                                    <input type="text" id="cl_qty" name="cl_qty" class="form-control readonly-field text-end" value="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Comp :</label>
                                    <input type="text" id="comp" name="comp" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Lctn :</label>
                                    <input type="text" id="lctn" name="lctn" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-2 ms-auto">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">AMT.</label>
                                    <input type="number" id="amt" name="amt" class="form-control readonly-field text-end" value="0.00" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Srl.No.</label>
                                    <input type="text" id="srlno" name="srlno" class="form-control text-end">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="updateTransaction()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                            <button type="button" class="btn btn-primary" onclick="showItemSelectionModal()">
                                <i class="bi bi-plus-square"></i> Add Items
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelModification()">
                                <i class="bi bi-x-circle"></i> Cancel
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
    'id' => 'stockTransferIncomingModItemModal',
    'module' => 'stock-transfer-incoming',
    'showStock' => true,
    'rateType' => 'pur_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'stockTransferIncomingModBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'stockTransferIncomingModBatchModal',
    'module' => 'stock-transfer-incoming',
    'showOnlyAvailable' => false,
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let currentTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    // Focus Load Invoice button on page load
    setTimeout(() => {
        document.getElementById('stim_loadInvoiceBtn')?.focus();
    }, 300);
});

function updateDayName() {
    const dateInput = document.getElementById('stim_transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

// ====== CUSTOM SUPPLIER DROPDOWN ======
let supplierActiveIndex = -1;

function updateSupplierName() {
    // Compatibility - no-op
}

function openSupplierDropdown() {
    const display = document.getElementById('stim_supplierDisplay');
    display.select(); // Select all text so user can type to replace
    // Show all items (reset filter)
    document.querySelectorAll('#stim_supplierList .custom-dropdown-item').forEach(item => {
        item.style.display = '';
    });
    document.getElementById('stim_supplierList').style.display = 'block';
    supplierActiveIndex = 0;
    highlightSupplierItem();
}

function closeSupplierDropdown() {
    setTimeout(() => {
        const list = document.getElementById('stim_supplierList');
        if(list) list.style.display = 'none';
        supplierActiveIndex = -1;
    }, 200);
}

function filterSuppliers(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    const filter = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#stim_supplierList .custom-dropdown-item');
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
    supplierActiveIndex = -1;
    highlightSupplierItem();
}

function selectSupplier(id, name) {
    document.getElementById('supplier_id').value = id;
    document.getElementById('stim_supplierDisplay').value = name;
    document.getElementById('stim_supplierList').style.display = 'none';
    window.selectedSupplierName = name;
    supplierActiveIndex = -1;
    document.getElementById('stim_remarks')?.focus();
}

function highlightSupplierItem() {
    const items = Array.from(document.querySelectorAll('#stim_supplierList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => i.classList.remove('active'));
    if (supplierActiveIndex >= items.length) supplierActiveIndex = 0;
    if (supplierActiveIndex < -1) supplierActiveIndex = items.length - 1;
    if (supplierActiveIndex >= 0 && items[supplierActiveIndex]) {
        items[supplierActiveIndex].classList.add('active');
        items[supplierActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#stim_supplierDropdownWrapper')) {
        const list = document.getElementById('stim_supplierList');
        if (list) list.style.display = 'none';
    }
});

// ====== INVOICE MODAL KEYBOARD NAVIGATION ======
let invoiceActiveIndex = -1;

function highlightInvoiceRow() {
    const rows = document.querySelectorAll('#invoiceListBody tr.invoice-row');
    rows.forEach(r => r.classList.remove('invoice-row-active'));
    if (invoiceActiveIndex >= rows.length) invoiceActiveIndex = 0;
    if (invoiceActiveIndex < 0) invoiceActiveIndex = rows.length - 1;
    if (invoiceActiveIndex >= 0 && rows[invoiceActiveIndex]) {
        rows[invoiceActiveIndex].classList.add('invoice-row-active');
        rows[invoiceActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// ====== KEYBOARD NAVIGATION ======
document.addEventListener('keydown', function(e) {
    // Invoice modal keyboard handler
    const invoiceModal = document.getElementById('invoiceModal');
    if (invoiceModal) {
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            invoiceActiveIndex++;
            highlightInvoiceRow();
            return false;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            invoiceActiveIndex--;
            highlightInvoiceRow();
            return false;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const rows = document.querySelectorAll('#invoiceListBody tr.invoice-row');
            if (invoiceActiveIndex >= 0 && rows[invoiceActiveIndex]) {
                const loadBtn = rows[invoiceActiveIndex].querySelector('button');
                if (loadBtn) loadBtn.click();
            }
            return false;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeInvoiceModal();
            return false;
        }
        return;
    }

    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;

        // Skip if item/batch modal open
        const hasModalOpen = document.getElementById('itemModal') || document.getElementById('batchModal') ||
            document.querySelector('#stockTransferIncomingModItemModal.show') || document.querySelector('#stockTransferIncomingModBatchModal.show');
        if (hasModalOpen) return;

        // Ctrl+Enter → Inc. field
        if (e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('inclusive')?.focus();
            return false;
        }

        // Shift+Enter backward
        if (e.shiftKey) {
            const backMap = {
                'stim_supplierDisplay': 'stim_transaction_date',
                'stim_remarks': 'stim_supplierDisplay',
                'stim_st_date': 'stim_remarks',
                'stim_gr_no': 'stim_st_date',
                'stim_gr_date': 'stim_gr_no',
                'stim_cases': 'stim_gr_date',
                'stim_transport': 'stim_cases'
            };
            if (backMap[activeEl.id]) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById(backMap[activeEl.id])?.focus();
                return false;
            }
            return;
        }

        // Supplier Dropdown Intercept
        if (activeEl.id === 'stim_supplierDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingId = document.getElementById('supplier_id').value;
            const listContainer = document.getElementById('stim_supplierList');
            
            // If supplier already selected, just close dropdown and move on
            if (existingId) {
                if (listContainer) listContainer.style.display = 'none';
                supplierActiveIndex = -1;
                // Restore display name
                document.getElementById('stim_supplierDisplay').value = window.selectedSupplierName || '';
                document.getElementById('stim_remarks')?.focus();
                return false;
            }
            
            // No supplier yet - try to select from dropdown
            if (listContainer && listContainer.style.display === 'block') {
                const items = Array.from(document.querySelectorAll('#stim_supplierList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                if (supplierActiveIndex >= 0 && supplierActiveIndex < items.length) {
                    items[supplierActiveIndex].click();
                } else {
                    listContainer.style.display = 'none';
                    supplierActiveIndex = -1;
                    document.getElementById('stim_remarks')?.focus();
                }
            } else {
                document.getElementById('stim_remarks')?.focus();
            }
            return false;
        }

        // Date → Supplier
        if (activeEl.id === 'stim_transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_supplierDisplay')?.focus();
            setTimeout(() => { openSupplierDropdown(); }, 50);
            return false;
        }
        // Remarks → ST Date
        if (activeEl.id === 'stim_remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_st_date')?.focus();
            return false;
        }
        // ST Date → GR No.
        if (activeEl.id === 'stim_st_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_gr_no')?.focus();
            return false;
        }
        // GR No. → GR Date
        if (activeEl.id === 'stim_gr_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_gr_date')?.focus();
            return false;
        }
        // GR Date → Cases
        if (activeEl.id === 'stim_gr_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_cases')?.focus();
            return false;
        }
        // Cases → Transport
        if (activeEl.id === 'stim_cases') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stim_transport')?.focus();
            return false;
        }
        // Transport → first row Batch (if items exist) OR Add Items
        if (activeEl.id === 'stim_transport') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const firstRow = document.querySelector('#itemsTableBody tr');
            if (firstRow) {
                const batchInput = firstRow.querySelector('input[name*="[batch]"]');
                if (batchInput) {
                    selectRow(parseInt(firstRow.dataset.rowIndex));
                    batchInput.focus();
                    batchInput.select();
                    return false;
                }
            }
            const addBtn = document.getElementById('stim_addItemsBtn');
            if (addBtn) { addBtn.focus(); addBtn.click(); }
            return false;
        }
        // Load Invoice button → open modal
        if (activeEl.id === 'stim_loadInvoiceBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            showLoadInvoiceModal();
            return false;
        }

        // Add Items button
        if (activeEl.id === 'stim_addItemsBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            showItemSelectionModal();
            return false;
        }
    }

    // Dropdown arrow navigation
    if (document.activeElement && document.activeElement.id === 'stim_supplierDisplay') {
        const listContainer = document.getElementById('stim_supplierList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') { e.preventDefault(); supplierActiveIndex++; highlightSupplierItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); supplierActiveIndex--; highlightSupplierItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); closeSupplierDropdown(); return false; }
        }
    }

    // Ctrl+S save
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        updateTransaction();
        return false;
    }
}, true);

function loadItems() {
    fetch('<?php echo e(route("admin.items.get-all")); ?>')
        .then(response => response.json())
        .then(data => {
            itemsData = data.items || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ====== NEW MODAL COMPONENT BRIDGE ======
function showItemSelectionModal() {
    console.log('📦 Opening stock transfer incoming modification item modal');
    if (typeof openItemModal_stockTransferIncomingModItemModal === 'function') {
        openItemModal_stockTransferIncomingModItemModal();
        return;
    }
    // Fallback to legacy
    console.warn('⚠️ Falling back to legacy item modal');
    _legacy_showItemSelectionModal();
}

window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Stock Transfer Incoming Modification - Item+Batch selected:', item?.name, batch?.batch_no);
    console.log('Item data:', item);
    console.log('Batch data:', batch);
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || item.item_code || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batch?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batch?.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB',{month:'2-digit',year:'2-digit'}) : ''}" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" step="1" min="0" value="0" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][p_rate]" step="0.01" value="${parseFloat(batch?.pur_rate || item.pur_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handlePRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][gst_percent]" step="0.01" min="0" value="${item.gst_percent || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGstKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    updateFooterFromRow(row);
    row.querySelector('input[name*="[qty]"]')?.focus();
};

window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

window.onItemSelectedFromModal = function(item) {
    console.log('🔗 Item selected, opening batch modal for:', item?.name);
    if (typeof openBatchModal_stockTransferIncomingModBatchModal === 'function') {
        openBatchModal_stockTransferIncomingModBatchModal(item);
    } else {
        console.error('❌ Batch modal function not found');
    }
};
// ====== END MODAL COMPONENT BRIDGE ======

// ============ LOAD INVOICE MODAL ============
function showLoadInvoiceModal() {
    // Show loading modal first
    let html = `
        <div class="batch-modal-backdrop show" id="invoiceModalBackdrop"></div>
        <div class="batch-modal show" id="invoiceModal" style="max-width: 700px;">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Past Invoice</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeInvoiceModal()"></button>
            </div>
            <div class="modal-body-custom" style="max-height: 450px;">
                <div class="mb-3">
                    <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by Trf No. or Supplier..." onkeyup="filterInvoices()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm" style="font-size: 11px;">
                        <thead class="table-info" style="position: sticky; top: 0;">
                            <tr>
                                <th>Trf No.</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceListBody">
                            <tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Fetch invoices
    fetch('<?php echo e(route("admin.stock-transfer-incoming.past-transactions")); ?>')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('invoiceListBody');
            if (data.success && data.transactions.length > 0) {
                tbody.innerHTML = '';
                data.transactions.forEach(t => {
                    const row = document.createElement('tr');
                    row.className = 'invoice-row';
                    row.style.cursor = 'pointer';
                    row.ondblclick = () => selectInvoice(t.id);
                    row.innerHTML = `
                        <td><strong>${t.trf_no}</strong></td>
                        <td>${t.transaction_date}</td>
                        <td>${t.supplier_name || '-'}</td>
                        <td class="text-end">₹${parseFloat(t.total_amount || 0).toFixed(2)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-success btn-sm py-0 px-2" onclick="selectInvoice(${t.id})">
                                <i class="bi bi-check"></i> Load
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
                // Auto-highlight first row
                invoiceActiveIndex = 0;
                highlightInvoiceRow();
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No past invoices found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('invoiceListBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading invoices</td></tr>';
        });
    
    document.getElementById('invoiceSearchInput')?.focus();
}

function filterInvoices() {
    const search = document.getElementById('invoiceSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#invoiceListBody tr.invoice-row');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal')?.remove();
    document.getElementById('invoiceModalBackdrop')?.remove();
}

function selectInvoice(id) {
    closeInvoiceModal();
    loadTransaction(id);
}

function loadTransaction(id) {
    fetch(`<?php echo e(url('admin/stock-transfer-incoming')); ?>/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateForm(data.transaction, data.items);
            } else {
                alert(data.message || 'Error loading transaction');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading transaction');
        });
}

function populateForm(transaction, items) {
    console.log('Loading transaction:', transaction);
    console.log('Loading items:', items);
    
    currentTransactionId = transaction.id;
    document.getElementById('transaction_id').value = transaction.id;
    document.getElementById('trf_no').value = transaction.trf_no || '';
    document.getElementById('stim_transaction_date').value = transaction.transaction_date || '';
    
    // Set supplier using custom dropdown
    if (transaction.supplier_id) {
        document.getElementById('supplier_id').value = transaction.supplier_id;
        // Find supplier name from dropdown items
        const supplierItems = document.querySelectorAll('#stim_supplierList .custom-dropdown-item');
        let supplierName = transaction.supplier_name || '';
        supplierItems.forEach(item => {
            if (item.getAttribute('data-value') === String(transaction.supplier_id)) {
                supplierName = item.textContent.trim();
            }
        });
        document.getElementById('stim_supplierDisplay').value = supplierName;
        window.selectedSupplierName = supplierName;
    }
    
    // Set other header fields
    document.getElementById('stim_st_date').value = transaction.st_date || '';
    document.getElementById('stim_gr_no').value = transaction.gr_no || '';
    document.getElementById('stim_gr_date').value = transaction.gr_date || '';
    document.getElementById('stim_cases').value = transaction.cases || '';
    document.getElementById('stim_transport').value = transaction.transport || '';
    document.getElementById('stim_remarks').value = transaction.remarks || '';
    document.getElementById('total_amount').value = parseFloat(transaction.total_amount || 0).toFixed(2);
    
    // Footer fields
    document.getElementById('packing').value = transaction.packing || '';
    document.getElementById('unit').value = transaction.unit || '';
    document.getElementById('cl_qty').value = transaction.cl_qty || 0;
    document.getElementById('comp').value = transaction.comp || '';
    document.getElementById('lctn').value = transaction.lctn || '';
    document.getElementById('srlno').value = transaction.srlno || '';
    
    updateDayName();
    
    // Clear existing rows and add items
    document.getElementById('itemsTableBody').innerHTML = '';
    currentRowIndex = 0;
    
    if (items && items.length > 0) {
        items.forEach((item, idx) => {
            addRowWithData(item);
        });
        
        // Select first row and show its rates
        setTimeout(() => {
            if (items.length > 0) {
                selectRow(0);
            }
        }, 100);
    }
    
    calculateTotalAmount();
    
    alert('Invoice loaded successfully!\nTrf No: ' + transaction.trf_no);
    // Focus Date field after loading
    setTimeout(() => {
        document.getElementById('stim_transaction_date')?.focus();
    }, 300);
}

function addRowWithData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.item_id;
    row.dataset.batchId = item.batch_id || '';
    
    // Store item data for rates section
    row.dataset.itemData = JSON.stringify({
        packing: item.packing || '',
        unit: item.unit || '',
        company_short_name: item.company_short_name || item.comp || '',
        location: item.location || item.lctn || '',
        total_qty: item.cl_qty || 0,
        mrp: item.mrp || 0,
        s_rate: item.s_rate || 0,
        ws_rate: item.ws_rate || 0,
        spl_rate: item.spl_rate || 0
    });
    
    // Store batch data
    row.dataset.batchData = JSON.stringify({
        batch_no: item.batch_no || '',
        expiry_date: item.expiry || '',
        pur_rate: item.p_rate || 0,
        mrp: item.mrp || 0,
        s_rate: item.s_rate || 0,
        ws_rate: item.ws_rate || 0,
        spl_rate: item.spl_rate || 0,
        location: item.lctn || ''
    });
    
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || item.item_id || ''}" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" value="${item.free_qty || 0}" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][p_rate]" value="${parseFloat(item.p_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handlePRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][gst_percent]" value="${item.gst_percent || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGstKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" value="${parseFloat(item.ft_rate || 0).toFixed(2)}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" value="${parseFloat(item.ft_amount || 0).toFixed(2)}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
}

// ============ LEGACY ITEM SELECTION MODAL ============
function _legacy_showItemSelectionModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #28a745;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." onkeyup="filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success" style="position: sticky; top: 0;">
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>P.Rate</th>
                                <th>GST%</th>
                                <th>MRP</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="itemsListBody">`;
    
    itemsData.forEach(item => {
        html += `
            <tr class="item-row" onclick="selectItemFromModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                <td><strong>${item.id || item.item_code || ''}</strong></td>
                <td>${item.name || ''}</td>
                <td>${item.packing || ''}</td>
                <td class="text-end">${parseFloat(item.pur_rate || 0).toFixed(2)}</td>
                <td class="text-center">${item.gst_percent || 0}%</td>
                <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
                <td class="text-end">${item.total_qty || 0}</td>
            </tr>`;
    });
    
    html += `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
}

function filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#itemsListBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemModalBackdrop')?.remove();
}

function selectItemFromModal(item) {
    closeItemModal();
    
    // Add new row and fill with item data
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || item.item_code || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" step="1" min="0" value="0" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][p_rate]" step="0.01" value="${parseFloat(item.pur_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handlePRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][gst_percent]" step="0.01" min="0" value="${item.gst_percent || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGstKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    // Update footer with item rates
    updateFooterFromRow(row);
    
    // Show batch selection modal
    showBatchSelectionForItem(item, rowIndex);
}

function showBatchSelectionForItem(item, rowIndex) {
    // Fetch ALL batches for this item using item-batches API
    fetch(`<?php echo e(url('admin/api/item-batches')); ?>/${item.id}`)
        .then(response => response.json())
        .then(data => {
            // Always show batch modal - with batches or empty with "Add Without Batch" option
            const batches = data.batches || data || [];
            showBatchSelectionModal(Array.isArray(batches) ? batches : [], rowIndex, item, true);
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
            // Show empty batch modal with Add Without Batch option
            showBatchSelectionModal([], rowIndex, item, true);
        });
}

// ============ BATCH SELECTION MODAL ============
function showBatchSelectionModal(batches, rowIndex, itemData, showWithoutBatchOption = false) {
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Select Batch</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2" style="background: #f8f9fa; border-radius: 5px;">
                    <div>
                        <strong>ITEM:</strong> <span style="color: #6f42c1; font-weight: bold;">${itemData.name || ''}</span>
                        <span class="ms-3"><strong>Packing:</strong> <span style="color: #6f42c1;">${itemData.packing || ''}</span></span>
                    </div>
                    ${showWithoutBatchOption ? `
                    <button type="button" class="btn btn-warning btn-sm" onclick="addWithoutBatch(${rowIndex})">
                        <i class="bi bi-plus-circle me-1"></i> Add Without Batch (New Batch)
                    </button>
                    ` : ''}
                </div>`;
    
    if (batches.length > 0) {
        html += `
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px;">
                        <thead style="background: #90EE90;">
                            <tr>
                                <th>BATCH</th>
                                <th>RATE</th>
                                <th>P.RATE</th>
                                <th>MRP</th>
                                <th>QTY</th>
                                <th>EXP.</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
        batches.forEach(batch => {
            const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : '';
            html += `
                <tr style="cursor: pointer;" ondblclick='selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                    <td><strong>${batch.batch_no || ''}</strong></td>
                    <td class="text-end">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                    <td class="text-end">${batch.qty || 0}</td>
                    <td>${expiry}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick='selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                            <i class="bi bi-check"></i> Select
                        </button>
                    </td>
                </tr>`;
        });
    
        html += `</tbody></table></div>`;
    } else {
        html += `
                <div class="text-center py-4" style="background: #fff3cd; border-radius: 5px;">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2"><strong>No existing batches found for this item.</strong></p>
                    <p class="text-muted small">Click "Add Without Batch" to enter a new batch number.</p>
                </div>`;
    }
    
    html += `</div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

function addWithoutBatch(rowIndex) {
    closeBatchModal();
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]')?.focus();
    }
}

function selectBatchFromModal(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
    if (batch.expiry_date) {
        const d = new Date(batch.expiry_date);
        row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }
    row.querySelector('input[name*="[p_rate]"]').value = parseFloat(batch.pur_rate || 0).toFixed(2);
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    updateFooterFromRow(row);
    closeBatchModal();
    
    // Focus on expiry field
    row.querySelector('input[name*="[expiry]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

// ============ KEYBOARD NAVIGATION ============
function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            // Shift+Enter: go back to Transport
            document.getElementById('stim_transport')?.focus();
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        const batchValue = batchInput?.value.trim();
        
        if (batchValue) {
            checkBatch(rowIndex);
        } else {
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
            return;
        }
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[expiry]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        row?.querySelector('input[name*="[free_qty]"]')?.focus();
    }
}

function handleFreeQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[qty]"]')?.focus();
            return;
        }
        row?.querySelector('input[name*="[p_rate]"]')?.focus();
    }
}

function handlePRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[free_qty]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        row?.querySelector('input[name*="[gst_percent]"]')?.focus();
    }
}

function handleGstKeydown(event, rowIndex) {
    if (event.key !== 'Enter') return;
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    if (event.shiftKey) {
        document.getElementById('row-' + rowIndex)?.querySelector('input[name*="[p_rate]"]')?.focus();
        return;
    }

    calculateRowAmount(rowIndex);
    completeRow(rowIndex);

    // Find next row with a batch input that has a value
    const currentRow = document.getElementById('row-' + rowIndex);
    let sibling = currentRow ? currentRow.nextElementSibling : null;
    while (sibling) {
        const batchInput = sibling.querySelector('input[name*="[batch]"]');
        if (batchInput && batchInput.value.trim() !== '') {
            // Next filled row found — focus its batch input
            const nextIdx = parseInt(sibling.id.replace('row-', ''));
            selectRow(nextIdx);
            batchInput.focus();
            batchInput.select();
            return;
        }
        sibling = sibling.nextElementSibling;
    }

    // No filled next row → open Insert Items modal
    showItemSelectionModal();
}

function handleSRateKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        // Complete the row
        if (window.currentEditingRowIndex !== undefined) {
            completeRow(window.currentEditingRowIndex);
            window.currentEditingRowIndex = undefined;
        }
        // Remove focus from S.Rate
        event.target.blur();
    }
}

function completeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.remove('row-selected');
        row.classList.add('row-complete');
        
        // Remove focus from all inputs in row
        row.querySelectorAll('input').forEach(input => input.blur());
        
        calculateTotalAmount();
        selectedRowIndex = null;
    }
}

// ============ ADD ROW (MANUAL) ============
function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" step="1" min="0" value="0" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][p_rate]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handlePRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][gst_percent]" step="0.01" min="0" value="0" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGstKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const codeInput = row?.querySelector('input[name*="[code]"]');
        if (codeInput && codeInput.value.trim()) {
            searchItemByCode(rowIndex, codeInput.value);
        }
    }
}

function selectRow(rowIndex) {
    // Remove selection from all rows
    document.querySelectorAll('#itemsTableBody tr').forEach(r => {
        r.classList.remove('row-selected');
    });
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        // Add blue border selection (even for complete rows when viewing)
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        window.currentEditingRowIndex = rowIndex;
        
        // Update footer with this row's data
        updateFooterFromRow(row);
        
        // Update AMT field with this row's amount
        const ftAmount = row.querySelector('input[name*="[ft_amount]"]')?.value || 0;
        document.getElementById('amt').value = parseFloat(ftAmount).toFixed(2);
    }
}

function updateFooterFromRow(row) {
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('comp').value = itemData.company_short_name || itemData.mfg_by || '';
    document.getElementById('cl_qty').value = itemData.total_qty || batchData.qty || '0';
    document.getElementById('lctn').value = batchData.location || itemData.location || '';
    
    // Rates from item/batch
    document.getElementById('spl_rate').value = parseFloat(batchData.spl_rate || itemData.spl_rate || 0).toFixed(2);
    document.getElementById('ws_rate').value = parseFloat(batchData.ws_rate || itemData.ws_rate || 0).toFixed(2);
    document.getElementById('batch_mrp').value = parseFloat(batchData.mrp || itemData.mrp || 0).toFixed(2);
    document.getElementById('s_rate').value = parseFloat(batchData.s_rate || itemData.s_rate || 0).toFixed(2);
}

function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code || i.item_code == code);
    if (item) {
        fillRowWithItem(rowIndex, item);
    }
}

function fillRowWithItem(rowIndex, item) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[code]"]').value = item.id || item.item_code || '';
    row.querySelector('input[name*="[name]"]').value = item.name || '';
    row.querySelector('input[name*="[p_rate]"]').value = parseFloat(item.pur_rate || 0).toFixed(2);
    row.querySelector('input[name*="[gst_percent]"]').value = item.gst_percent || 0;
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id;
    
    updateFooterFromRow(row);
    row.querySelector('input[name*="[batch]"]').focus();
}

let batchCheckInProgress = false;

function checkBatch(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const batchNo = row.querySelector('input[name*="[batch]"]').value.trim();
    
    if (!itemId || !batchNo) {
        row?.querySelector('input[name*="[expiry]"]')?.focus();
        return;
    }
    if (batchCheckInProgress) return;
    batchCheckInProgress = true;
    
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    fetch(`<?php echo e(route('admin.batches.check-batch')); ?>?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists && data.batches && data.batches.length > 0) {
                // Batch exists - auto-fill data
                const batch = data.batches[0];
                row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
                if (batch.expiry_date) {
                    const d = new Date(batch.expiry_date);
                    row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
                }
                row.querySelector('input[name*="[p_rate]"]').value = parseFloat(batch.pur_rate || 0).toFixed(2);
                row.dataset.batchId = batch.id;
                row.dataset.batchData = JSON.stringify(batch);
                updateFooterFromRow(row);
            } else {
                // New batch - will be created on save
                row.dataset.batchId = '';
                row.dataset.isNewBatch = 'true';
                row.dataset.newBatchData = JSON.stringify({
                    batch_no: batchNo,
                    expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                    pur_rate: parseFloat(row.querySelector('input[name*="[p_rate]"]')?.value) || 0,
                    mrp: parseFloat(itemData.mrp || 0),
                    s_rate: parseFloat(itemData.s_rate || 0),
                    ws_rate: parseFloat(itemData.ws_rate || 0),
                    spl_rate: parseFloat(itemData.spl_rate || 0)
                });
            }
            // Move to expiry field
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        })
        .catch(error => {
            console.error('Error checking batch:', error);
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        })
        .finally(() => {
            setTimeout(() => { batchCheckInProgress = false; }, 500);
        });
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const pRate = parseFloat(row.querySelector('input[name*="[p_rate]"]')?.value) || 0;
    const gstPercent = parseFloat(row.querySelector('input[name*="[gst_percent]"]')?.value) || 0;
    
    const ftRate = pRate * (1 + gstPercent / 100);
    const ftAmount = qty * ftRate;
    
    row.querySelector('input[name*="[ft_rate]"]').value = ftRate.toFixed(2);
    row.querySelector('input[name*="[ft_amount]"]').value = ftAmount.toFixed(2);
    
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const ftAmount = parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0;
        total += ftAmount;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
    // AMT field is updated only when row is selected (individual row amount)
}

function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        calculateTotalAmount();
    }
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    }
}

let isSubmitting = false;

function updateTransaction() {
    if (!currentTransactionId) {
        alert('Please select a transaction to modify');
        return;
    }
    
    // Prevent double submission
    if (isSubmitting) {
        return;
    }
    isSubmitting = true;
    
    // Disable button and show loading
    const updateBtn = document.querySelector('button[onclick="updateTransaction()"]');
    const originalBtnHtml = updateBtn.innerHTML;
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemId = row.dataset.itemId;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        
        if (itemId && qty > 0) {
            items.push({
                item_id: itemId,
                batch_id: row.dataset.batchId || '',
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                name: row.querySelector('input[name*="[name]"]')?.value || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: qty,
                free_qty: parseFloat(row.querySelector('input[name*="[free_qty]"]')?.value) || 0,
                p_rate: parseFloat(row.querySelector('input[name*="[p_rate]"]')?.value) || 0,
                gst_percent: parseFloat(row.querySelector('input[name*="[gst_percent]"]')?.value) || 0,
                ft_rate: parseFloat(row.querySelector('input[name*="[ft_rate]"]')?.value) || 0,
                ft_amount: parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item with quantity');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
        return;
    }
    
    const supplierName = window.selectedSupplierName || '';
    
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        _method: 'PUT',
        transaction_date: document.getElementById('stim_transaction_date').value,
        day_name: document.getElementById('day_name').value,
        supplier_id: document.getElementById('supplier_id').value,
        supplier_name: supplierName,
        st_date: document.getElementById('stim_st_date').value,
        gr_no: document.getElementById('stim_gr_no').value,
        gr_date: document.getElementById('stim_gr_date').value,
        cases: document.getElementById('stim_cases').value,
        transport: document.getElementById('stim_transport').value,
        remarks: document.getElementById('stim_remarks').value,
        total_amount: document.getElementById('total_amount').value,
        packing: document.getElementById('packing').value,
        unit: document.getElementById('unit').value,
        cl_qty: document.getElementById('cl_qty').value,
        comp: document.getElementById('comp').value,
        lctn: document.getElementById('lctn').value,
        srlno: document.getElementById('srlno').value,
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`<?php echo e(url('admin/stock-transfer-incoming')); ?>/${currentTransactionId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message || 'Transaction updated successfully!');
            window.location.reload();
        } else {
            alert(result.message || 'Error updating transaction');
            isSubmitting = false;
            updateBtn.disabled = false;
            updateBtn.innerHTML = originalBtnHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating transaction');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
    });
}

function cancelModification() {
    if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
        window.location.href = '<?php echo e(route("admin.stock-transfer-incoming.index")); ?>';
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/stock-transfer-incoming/modification.blade.php ENDPATH**/ ?>