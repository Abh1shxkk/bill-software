<?php $__env->startSection('title', 'Purchase Voucher'); ?>
<?php $__env->startSection('disable_select2', '1'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .hsn-table-container { background: #a5c4d4; padding: 10px; border: 2px solid #2c5282; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #2c5282; color: white; padding: 6px 8px; text-align: center; border: 1px solid #1a365d; font-weight: 600; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table input:focus { outline: 2px solid #ffc107; }
    .hsn-table .row-selected { background: #fffbcc; }
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .hsn-table thead { width: calc(100% - 17px); }
    .totals-section { background: #a5c4d4; padding: 10px; margin-top: 10px; border: 2px solid #2c5282; }
    .totals-table { font-size: 11px; }
    .totals-table td { padding: 4px 8px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    .btn-hsn { background: #2c5282; color: white; border: 1px solid #1a365d; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #1a365d; }
    .hsn-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
    .hsn-modal-backdrop.show { display: block; }
    .hsn-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: #f0f0f0; border: 2px solid #666; z-index: 9999; box-shadow: 0 5px 20px rgba(0,0,0,0.4); }
    .hsn-modal.show { display: block; }
    .hsn-modal-header { background: #2c5282; color: white; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; }
    .hsn-modal-title { font-size: 13px; font-weight: 600; margin: 0; }
    .hsn-modal-body { max-height: 350px; overflow-y: auto; padding: 10px; }
    .hsn-modal-footer { background: #e0e0e0; padding: 8px; display: flex; justify-content: flex-end; gap: 8px; }
    .hsn-list-item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; font-size: 11px; }
    .hsn-list-item:hover { background: #e6f3ff; }
    .hsn-list-item.hsn-highlighted { background: #007bff !important; color: white !important; }
    .hsn-list-item.hsn-highlighted strong { color: white !important; }

    /* Searchable Dropdown Styles */
    .searchable-dropdown { position: relative; }
    .searchable-dropdown-input { width: 100%; cursor: text; }
    .searchable-dropdown-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .searchable-dropdown-list { position: absolute; top: 100%; left: 0; right: 0; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 4px 4px; z-index: 1080; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; }
    .searchable-dropdown-list .dropdown-item { padding: 6px 10px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #f0f0f0; transition: background-color 0.15s; }
    .searchable-dropdown-list .dropdown-item:last-child { border-bottom: none; }
    .searchable-dropdown-list .dropdown-item:hover { background-color: #f8f9fa; }
    .searchable-dropdown-list .dropdown-item.highlighted { background-color: #007bff !important; color: white !important; }
    .searchable-dropdown-list .dropdown-item.selected { background-color: #e7f3ff; font-weight: 600; }
    .searchable-dropdown-list .dropdown-item.hidden { display: none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i> Purchase Voucher (HSN Entry)</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-info btn-sm" onclick="openHsnModal()">
            <i class="bi bi-plus-circle me-1"></i> Open HSN
        </button>
        <a href="<?php echo e(route('admin.purchase-voucher.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-list"></i> All Vouchers
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Bill No. :</label>
                        <input type="text" class="form-control" id="billNo" value="<?php echo e($nextBillNo); ?>" style="width: 120px;" placeholder="Enter Bill No">
                    </div>
                    <div class="field-group">
                        <label>Trn.No. :</label>
                        <input type="text" class="form-control" id="trnNo" value="<?php echo e(str_pad($nextTrnNo, 6, '0', STR_PAD_LEFT)); ?>" style="width: 80px; background: #e9ecef;" readonly>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="billDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 130px;">
                        <input type="text" class="form-control" id="dayName" value="<?php echo e(date('l')); ?>" readonly style="width: 80px; background: #e9ecef;">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Supplier :</label>
                        <div class="searchable-dropdown" id="supplierDropdownWrapper" style="width: 250px;">
                            <input type="text"
                                   id="supplierSearchInput"
                                   class="form-control searchable-dropdown-input"
                                   placeholder="Type to search supplier..."
                                   autocomplete="off"
                                   data-custom-enter="true">
                            <input type="hidden" id="supplierSelect" value="">
                            <div class="searchable-dropdown-list" id="supplierDropdownList" style="display: none;">
                                <div class="dropdown-item" data-value="" data-name="">Select Supplier</div>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="dropdown-item"
                                     data-value="<?php echo e($supplier->supplier_id); ?>"
                                     data-name="<?php echo e($supplier->name); ?>">
                                    <?php echo e($supplier->name); ?>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="border p-2" style="background: #e6f7ff;">
                        <div class="d-flex justify-content-between" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">TOTAL :</span>
                            <span id="totalDisplay">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hsn-table-container">
            <div class="d-flex justify-content-end mb-2 gap-2">
                <button type="button" class="btn-hsn" onclick="addNewRow()">
                    <i class="bi bi-plus-circle me-1"></i> Add Row
                </button>
            </div>
            
            <table class="hsn-table" id="hsnTable">
                <thead>
                    <tr>
                        <th style="width: 100px;">HSN Code</th>
                        <th style="width: 80px;">Amount</th>
                        <th style="width: 55px;">GST%</th>
                        <th style="width: 55px;">CGST%</th>
                        <th style="width: 75px;">Amount</th>
                        <th style="width: 55px;">SGST%</th>
                        <th style="width: 75px;">Amount</th>
                        <th style="width: 50px;">Qty.</th>
                        <th style="width: 50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hsnTableBody" class="hsn-table-scroll"></tbody>
            </table>
        </div>

        <div class="totals-section">
            <div class="row">
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr><td class="label">Gross Amt.</td><td class="value" id="grossAmt">0.00</td></tr>
                        <tr><td class="label">Total GST</td><td class="value" id="totalGst">0.00</td></tr>
                        <tr><td class="label">Net Amt.</td><td class="value" id="netAmt">0.00</td></tr>
                        <tr><td class="label">Round Off</td><td class="value" id="roundOff">0.00</td></tr>
                        <tr><td class="label">Amount</td><td class="value" id="finalAmount">0.00</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr><td></td><td class="label">CGST AMT</td><td></td><td class="label">SGST AMT</td></tr>
                        <tr><td></td><td class="value" id="totalCgst">0.00</td><td></td><td class="value" id="totalSgst">0.00</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label style="width: 60px;">Remarks</label>
                        <input type="text" class="form-control" id="remarks" style="flex: 1;">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="saveVoucher()">
                <i class="bi bi-check-circle me-1"></i> Save
            </button>
            <button type="button" class="btn-hsn px-4" onclick="window.location.href='<?php echo e(route('admin.purchase-voucher.index')); ?>'">
                <i class="bi bi-x-circle me-1"></i> Exit
            </button>
        </div>
    </div>
</div>

<!-- HSN Modal -->
<div class="hsn-modal-backdrop" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="hsn-modal" id="hsnModal">
    <div class="hsn-modal-header">
        <h5 class="hsn-modal-title"><i class="bi bi-list-ol me-2"></i>Select HSN Code</h5>
        <button type="button" onclick="closeHsnModal()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="hsn-modal-body">
        <input type="text" class="form-control mb-2" id="hsnSearch" placeholder="Search HSN Code..." data-custom-enter="true">
        <div id="hsnList">
            <?php $__currentLoopData = $hsnCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hsn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="hsn-list-item"
                 data-hsn-code="<?php echo e($hsn->hsn_code); ?>"
                 data-cgst="<?php echo e($hsn->cgst_percent); ?>"
                 data-sgst="<?php echo e($hsn->sgst_percent); ?>"
                 data-gst="<?php echo e($hsn->total_gst_percent); ?>"
                 onclick="selectHsn('<?php echo e($hsn->hsn_code); ?>', <?php echo e($hsn->cgst_percent); ?>, <?php echo e($hsn->sgst_percent); ?>, <?php echo e($hsn->total_gst_percent); ?>)">
                <strong><?php echo e($hsn->hsn_code); ?></strong> - <?php echo e($hsn->name); ?> (GST: <?php echo e($hsn->total_gst_percent); ?>%)
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="hsn-modal-footer">
        <button type="button" class="btn-hsn" onclick="closeHsnModal()">Close</button>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let rowCounter = 0;
let selectedRowIndex = null;
window.SKIP_AUTO_FOCUS = true;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('billDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    for (let i = 0; i < 5; i++) addNewRow();

    initSupplierDropdown();
    initHeaderKeyboardNav();
    initTableKeyboardNav();
    initCtrlSShortcut();

    // Focus billNo on load
    setTimeout(function() {
        const billNo = document.getElementById('billNo');
        if (billNo) { billNo.focus(); billNo.select(); }
    }, 150);
});

// =============================================
// CUSTOM SEARCHABLE SUPPLIER DROPDOWN
// =============================================
function initSupplierDropdown() {
    const input = document.getElementById('supplierSearchInput');
    const hiddenInput = document.getElementById('supplierSelect');
    const dropdownList = document.getElementById('supplierDropdownList');
    if (!input || !hiddenInput || !dropdownList) return;

    let highlightedIndex = -1;
    let isOpen = false;

    function getVisible() {
        return Array.from(dropdownList.querySelectorAll('.dropdown-item:not(.hidden)'));
    }
    function show() {
        dropdownList.style.display = 'block'; isOpen = true; highlightedIndex = -1;
        // Auto-highlight first visible item
        setTimeout(function() { highlight(0); }, 0);
    }
    function hide() {
        dropdownList.style.display = 'none'; isOpen = false; highlightedIndex = -1;
        dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('highlighted'));
    }
    function filter(text) {
        const s = (text || '').toLowerCase().trim();
        dropdownList.querySelectorAll('.dropdown-item').forEach(item => {
            const match = s === '' || item.textContent.toLowerCase().includes(s) || (item.dataset.name || '').toLowerCase().includes(s);
            item.classList.toggle('hidden', !match);
        });
        highlightedIndex = -1;
        dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('highlighted'));
    }
    function highlight(idx) {
        const vis = getVisible();
        vis.forEach(i => i.classList.remove('highlighted'));
        if (idx >= 0 && idx < vis.length) {
            highlightedIndex = idx;
            vis[idx].classList.add('highlighted');
            vis[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    function selectItem(item) {
        const val = item.dataset.value || '';
        const name = item.dataset.name || '';
        hiddenInput.value = val;
        input.value = val ? name : '';
        dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
        hide();
        // Move to first HSN code field
        focusFirstHsnCode();
    }

    input.addEventListener('focus', function() { show(); filter(this.value); });
    input.addEventListener('input', function() { show(); filter(this.value); });
    input.addEventListener('keydown', function(e) {
        if (!isOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
            show(); filter(this.value); return;
        }
        if (!isOpen) {
            if (e.key === 'Enter') {
                e.preventDefault(); e.stopPropagation();
                focusFirstHsnCode();
            }
            return;
        }
        const vis = getVisible();
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                highlight(highlightedIndex < vis.length - 1 ? highlightedIndex + 1 : 0);
                break;
            case 'ArrowUp':
                e.preventDefault();
                highlight(highlightedIndex > 0 ? highlightedIndex - 1 : vis.length - 1);
                break;
            case 'Enter':
                e.preventDefault(); e.stopPropagation();
                if (highlightedIndex >= 0 && highlightedIndex < vis.length) {
                    selectItem(vis[highlightedIndex]);
                } else if (vis.length > 0) {
                    selectItem(vis[0]);
                }
                break;
            case 'Escape':
                e.preventDefault(); hide(); break;
            case 'Tab':
                if (highlightedIndex >= 0 && highlightedIndex < vis.length) selectItem(vis[highlightedIndex]);
                hide(); break;
        }
    });
    dropdownList.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (item) selectItem(item);
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#supplierDropdownWrapper')) hide();
    });
}

// =============================================
// HEADER KEYBOARD NAVIGATION
// =============================================
function initHeaderKeyboardNav() {
    const headerFields = ['billNo', 'billDate', 'supplierSearchInput'];

    headerFields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (id === 'supplierSearchInput') return; // Handled by dropdown

        el.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault(); e.stopPropagation();

            const idx = headerFields.indexOf(id);
            if (idx < headerFields.length - 1) {
                const nextEl = document.getElementById(headerFields[idx + 1]);
                if (nextEl) { nextEl.focus(); if (nextEl.select) nextEl.select(); }
            } else {
                focusFirstHsnCode();
            }
        });
    });
}

function focusFirstHsnCode() {
    const firstRow = document.querySelector('#hsnTableBody tr');
    if (firstRow) {
        const hsnInput = firstRow.querySelector('.hsn-code');
        if (hsnInput) { hsnInput.focus(); hsnInput.select(); }
    }
}

// =============================================
// TABLE KEYBOARD NAVIGATION
// =============================================
function initTableKeyboardNav() {
    document.getElementById('hsnTableBody').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (e.ctrlKey || e.metaKey) return; // Let Ctrl+Enter pass through to remarks shortcut
        const target = e.target;
        if (target.tagName !== 'INPUT') return;

        const row = target.closest('tr');
        if (!row) return;
        const rowId = row.getAttribute('data-row');

        e.preventDefault(); e.stopPropagation();

        // Editable fields order in each row
        const editableClasses = ['hsn-code', 'amount', 'gst-percent', 'qty'];
        let currentClass = null;
        for (const cls of editableClasses) {
            if (target.classList.contains(cls)) { currentClass = cls; break; }
        }
        if (!currentClass) return;

        const currentIdx = editableClasses.indexOf(currentClass);

        // If on HSN Code field and NOT shift, open the HSN modal
        if (currentClass === 'hsn-code' && !e.shiftKey) {
            selectRow(parseInt(rowId));
            openHsnModal();
            return;
        }

        // If on amount or gst-percent, trigger calculation
        if (currentClass === 'amount' || currentClass === 'gst-percent') {
            calculateRowTax(parseInt(rowId));
        }

        // SHIFT+ENTER: Move backwards
        if (e.shiftKey) {
            if (currentIdx > 0) {
                const prevInput = row.querySelector('.' + editableClasses[currentIdx - 1]);
                if (prevInput) { prevInput.focus(); prevInput.select(); }
            } else {
                // On hsn-code (first field) → move to previous row's qty
                const prevRow = row.previousElementSibling;
                if (prevRow) {
                    const prevQty = prevRow.querySelector('.qty');
                    if (prevQty) { prevQty.focus(); prevQty.select(); }
                }
            }
            return;
        }

        // ENTER: Move forward
        if (currentIdx < editableClasses.length - 1) {
            // Move to next field in same row
            const nextInput = row.querySelector('.' + editableClasses[currentIdx + 1]);
            if (nextInput) { nextInput.focus(); nextInput.select(); }
        } else {
            // On qty (last field) → move to next row's hsn-code
            const nextRow = row.nextElementSibling;
            if (nextRow) {
                const nextHsn = nextRow.querySelector('.hsn-code');
                if (nextHsn) { nextHsn.focus(); nextHsn.select(); }
            } else {
                // Add new row and focus it
                addNewRow();
                setTimeout(() => {
                    const lastRow = document.querySelector('#hsnTableBody tr:last-child');
                    if (lastRow) {
                        const hsn = lastRow.querySelector('.hsn-code');
                        if (hsn) { hsn.focus(); }
                    }
                }, 50);
            }
        }
    });
}

// =============================================
// CTRL+S SAVE & CTRL+ENTER REMARKS SHORTCUT
// =============================================
function initCtrlSShortcut() {
    document.addEventListener('keydown', function(e) {
        if (!e.ctrlKey && !e.metaKey) return;
        if (e.key === 's') {
            e.preventDefault();
            saveVoucher();
        } else if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            const remarks = document.getElementById('remarks');
            if (remarks) { remarks.focus(); remarks.select(); }
        }
    }, true); // capture phase to fire first
}

function addNewRow() {
    rowCounter++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    const currentRowId = rowCounter;
    row.innerHTML = `
        <td><input type="text" class="hsn-code" data-row="${currentRowId}" data-custom-enter="true" onclick="selectRow(${currentRowId})" onfocus="selectRow(${currentRowId})" placeholder="Enter HSN"></td>
        <td><input type="number" class="amount" step="0.01" data-custom-enter="true" onchange="calculateRowTax(${currentRowId})" oninput="calculateRowTax(${currentRowId})" placeholder="0.00"></td>
        <td><input type="number" class="gst-percent" step="0.01" data-custom-enter="true" onchange="calculateRowTax(${currentRowId})" placeholder="0"></td>
        <td><input type="number" class="cgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="cgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="qty" value="0" step="1" min="0" data-custom-enter="true" placeholder="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${currentRowId})" title="Delete"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
}

function selectRow(rowIndex) {
    selectedRowIndex = rowIndex;
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('row-selected'));
    document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`)?.classList.add('row-selected');
}

function deleteRow(rowIndex) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        if (selectedRowIndex === rowIndex) selectedRowIndex = null;
        calculateTotals();
        if (document.querySelectorAll('#hsnTableBody tr').length === 0) addNewRow();
    }
}

function calculateRowTax(rowIndex) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
    if (!row) return;
    const grossAmount = parseFloat(row.querySelector('.amount').value) || 0;
    const gstPercent = parseFloat(row.querySelector('.gst-percent').value) || 0;
    const cgstPercent = gstPercent / 2;
    const sgstPercent = gstPercent / 2;
    const cgstAmount = (grossAmount * cgstPercent) / 100;
    const sgstAmount = (grossAmount * sgstPercent) / 100;
    row.querySelector('.cgst-percent').value = cgstPercent.toFixed(2);
    row.querySelector('.cgst-amount').value = cgstAmount.toFixed(2);
    row.querySelector('.sgst-percent').value = sgstPercent.toFixed(2);
    row.querySelector('.sgst-amount').value = sgstAmount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalGross = 0, totalCgst = 0, totalSgst = 0;
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        totalGross += parseFloat(row.querySelector('.amount')?.value) || 0;
        totalCgst += parseFloat(row.querySelector('.cgst-amount')?.value) || 0;
        totalSgst += parseFloat(row.querySelector('.sgst-amount')?.value) || 0;
    });
    const totalGst = totalCgst + totalSgst;
    const netAmt = totalGross + totalGst;
    const roundOff = Math.round(netAmt) - netAmt;
    const finalAmount = Math.round(netAmt);
    document.getElementById('grossAmt').textContent = totalGross.toFixed(2);
    document.getElementById('totalGst').textContent = totalGst.toFixed(2);
    document.getElementById('netAmt').textContent = netAmt.toFixed(2);
    document.getElementById('roundOff').textContent = roundOff.toFixed(2);
    document.getElementById('finalAmount').textContent = finalAmount.toFixed(2);
    document.getElementById('totalCgst').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgst').textContent = totalSgst.toFixed(2);
    document.getElementById('totalDisplay').textContent = finalAmount.toFixed(2);
}
let hsnHighlightedIndex = -1;

function getVisibleHsnItems() {
    return Array.from(document.querySelectorAll('.hsn-list-item')).filter(item => item.style.display !== 'none');
}

function highlightHsnItem(idx) {
    const items = getVisibleHsnItems();
    items.forEach(i => i.classList.remove('hsn-highlighted'));
    if (idx >= 0 && idx < items.length) {
        hsnHighlightedIndex = idx;
        items[idx].classList.add('hsn-highlighted');
        items[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
}

function openHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
    hsnHighlightedIndex = -1;
    const search = document.getElementById('hsnSearch');
    if (search) { search.value = ''; }
    filterHsn();
    setTimeout(() => {
        if (search) search.focus();
        highlightHsnItem(0);
    }, 100);
}

function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
    hsnHighlightedIndex = -1;
    document.querySelectorAll('.hsn-list-item').forEach(i => i.classList.remove('hsn-highlighted'));
}

function filterHsn() {
    const search = document.getElementById('hsnSearch').value.toLowerCase();
    document.querySelectorAll('.hsn-list-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
    hsnHighlightedIndex = -1;
    highlightHsnItem(0);
}

// HSN Modal keyboard handler
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('hsnModal');
    if (!modal || !modal.classList.contains('show')) return;

    const items = getVisibleHsnItems();
    if (items.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlightHsnItem(hsnHighlightedIndex < items.length - 1 ? hsnHighlightedIndex + 1 : 0);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlightHsnItem(hsnHighlightedIndex > 0 ? hsnHighlightedIndex - 1 : items.length - 1);
    } else if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation();
        if (hsnHighlightedIndex >= 0 && hsnHighlightedIndex < items.length) {
            const item = items[hsnHighlightedIndex];
            selectHsn(
                item.dataset.hsnCode,
                parseFloat(item.dataset.cgst),
                parseFloat(item.dataset.sgst),
                parseFloat(item.dataset.gst)
            );
        }
    }
}, true);

// Filter on input event for hsnSearch
document.addEventListener('DOMContentLoaded', function() {
    const hsnSearch = document.getElementById('hsnSearch');
    if (hsnSearch) {
        hsnSearch.addEventListener('input', filterHsn);
    }
});

function selectHsn(code, cgst, sgst, gst) {
    let targetRow = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    if (!targetRow) {
        const rows = document.querySelectorAll('#hsnTableBody tr');
        for (let row of rows) {
            if (!row.querySelector('.hsn-code').value) { targetRow = row; break; }
        }
    }
    if (!targetRow) { addNewRow(); targetRow = document.querySelector('#hsnTableBody tr:last-child'); }
    targetRow.querySelector('.hsn-code').value = code;
    targetRow.querySelector('.gst-percent').value = gst;
    targetRow.querySelector('.cgst-percent').value = cgst;
    targetRow.querySelector('.sgst-percent').value = sgst;
    closeHsnModal();
    targetRow.querySelector('.amount').focus();
}

function saveVoucher() {
    const supplierId = document.getElementById('supplierSelect').value;
    if (!supplierId) { alert('Please select a supplier'); return; }
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code').value;
        const amount = parseFloat(row.querySelector('.amount').value) || 0;
        if (hsnCode && amount > 0) {
            items.push({
                hsn_code: hsnCode, amount: amount,
                gst_percent: parseFloat(row.querySelector('.gst-percent').value) || 0,
                cgst_percent: parseFloat(row.querySelector('.cgst-percent').value) || 0,
                cgst_amount: parseFloat(row.querySelector('.cgst-amount').value) || 0,
                sgst_percent: parseFloat(row.querySelector('.sgst-percent').value) || 0,
                sgst_amount: parseFloat(row.querySelector('.sgst-amount').value) || 0,
                qty: parseInt(row.querySelector('.qty').value) || 0
            });
        }
    });
    if (items.length === 0) { alert('Please add at least one item'); return; }
    
    const data = {
        bill_no: document.getElementById('billNo').value,
        bill_date: document.getElementById('billDate').value,
        supplier_id: supplierId,
        remarks: document.getElementById('remarks').value,
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.purchase-voucher.store")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Purchase Voucher saved! Bill No: ' + result.bill_no);
            window.location.href = '<?php echo e(route("admin.purchase-voucher.transaction")); ?>';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(e => { console.error(e); alert('Error saving voucher'); });
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeHsnModal(); });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/purchase-voucher/transaction.blade.php ENDPATH**/ ?>