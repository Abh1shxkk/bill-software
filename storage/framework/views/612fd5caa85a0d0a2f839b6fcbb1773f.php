<?php $__env->startSection('title', 'Sale Return Voucher'); ?>
<?php $__env->startSection('disable_select2', '1'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .hsn-table-container { background: #f5d4a5; padding: 10px; border: 2px solid #8b6914; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #c49c1a; color: white; padding: 6px 8px; text-align: center; border: 1px solid #8b6914; font-weight: 600; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table input:focus { outline: 2px solid #ffc107; }
    .hsn-table .row-selected { background: #fffbcc; }
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .totals-section { background: #f5d4a5; padding: 10px; margin-top: 10px; border: 2px solid #8b6914; }
    .totals-table { font-size: 11px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    .btn-hsn { background: #c49c1a; color: white; border: 1px solid #8b6914; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #a38316; }
    .hsn-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
    .hsn-modal-backdrop.show { display: block; }
    .hsn-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: #f0f0f0; border: 2px solid #666; z-index: 9999; }
    .hsn-modal.show { display: block; }
    .hsn-modal-header { background: #c49c1a; color: white; padding: 8px 12px; display: flex; justify-content: space-between; }
    .hsn-modal-body { max-height: 350px; overflow-y: auto; padding: 10px; }
    .hsn-list-item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; font-size: 11px; }
    .hsn-list-item:hover { background: #fff8e6; }
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
    <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Voucher (HSN Entry)</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-info btn-sm" onclick="openHsnModal()"><i class="bi bi-plus-circle me-1"></i> Open HSN</button>
        <a href="<?php echo e(route('admin.sale-return-voucher.index')); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Vouchers</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Invoice No :</label>
                        <input type="text" class="form-control" id="invoiceNo" value="<?php echo e($nextInvoiceNo); ?>" style="width: 120px;" readonly>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="returnDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 130px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Customer :</label>
                        <div class="searchable-dropdown" id="customerDropdownWrapper" style="width: 250px;">
                            <input type="text"
                                   id="customerSearchInput"
                                   class="form-control searchable-dropdown-input"
                                   placeholder="Type to search customer..."
                                   autocomplete="off"
                                   data-custom-enter="true">
                            <input type="hidden" id="customerSelect" value="">
                            <div class="searchable-dropdown-list" id="customerDropdownList" style="display: none;">
                                <div class="dropdown-item" data-value="" data-name="">Select Customer</div>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="dropdown-item"
                                     data-value="<?php echo e($customer->id); ?>"
                                     data-name="<?php echo e($customer->name); ?>">
                                    <?php echo e($customer->name); ?>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2" style="background: #fff8e6;">
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
                <button type="button" class="btn-hsn" onclick="openHsnModal()"><i class="bi bi-list-ol me-1"></i> Add HSN</button>
                <button type="button" class="btn-hsn" onclick="addNewRow()"><i class="bi bi-plus-circle me-1"></i> Add Row</button>
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
            <button type="button" class="btn-hsn px-4" onclick="saveVoucher()"><i class="bi bi-check-circle me-1"></i> Save</button>
            <button type="button" class="btn-hsn px-4" onclick="window.location.href='<?php echo e(route('admin.sale-return-voucher.index')); ?>'"><i class="bi bi-x-circle me-1"></i> Exit</button>
        </div>
    </div>
</div>

<!-- HSN Modal -->
<div class="hsn-modal-backdrop" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="hsn-modal" id="hsnModal">
    <div class="hsn-modal-header">
        <h5 style="font-size: 13px; margin: 0;"><i class="bi bi-list-ol me-2"></i>Select HSN Code</h5>
        <button type="button" onclick="closeHsnModal()" style="background: none; border: none; color: white; font-size: 18px;">&times;</button>
    </div>
    <div class="hsn-modal-body">
        <input type="text" class="form-control mb-2" id="hsnSearch" placeholder="Search HSN..." data-custom-enter="true">
        <div id="hsnList">
            <?php $__currentLoopData = $hsnCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hsn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="hsn-list-item"
                 data-hsn-code="<?php echo e($hsn->hsn_code); ?>"
                 data-cgst="<?php echo e($hsn->cgst_percent); ?>"
                 data-sgst="<?php echo e($hsn->sgst_percent); ?>"
                 data-gst="<?php echo e($hsn->total_gst_percent); ?>"
                 onclick="selectHsn('<?php echo e($hsn->hsn_code); ?>', <?php echo e($hsn->cgst_percent); ?>, <?php echo e($hsn->sgst_percent); ?>, <?php echo e($hsn->total_gst_percent); ?>)">
                <strong><?php echo e($hsn->hsn_code); ?></strong> - <?php echo e($hsn->name); ?> (<?php echo e($hsn->total_gst_percent); ?>%)
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let rowCounter = 0, selectedRowIndex = null;
window.SKIP_AUTO_FOCUS = true;

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 5; i++) addNewRow();
    initCustomerDropdown();
    initHeaderKeyboardNav();
    initTableKeyboardNav();
    initHsnModalKeyboard();
    initShortcuts();

    // Focus on Date field on load
    setTimeout(function() {
        const dateField = document.getElementById('returnDate');
        if (dateField) dateField.focus();
    }, 150);
});

// =============================================
// CUSTOM SEARCHABLE CUSTOMER DROPDOWN
// =============================================
function initCustomerDropdown() {
    const input = document.getElementById('customerSearchInput');
    const hiddenInput = document.getElementById('customerSelect');
    const dropdownList = document.getElementById('customerDropdownList');
    if (!input || !hiddenInput || !dropdownList) return;

    let highlightedIndex = -1, isOpen = false;

    function getVisible() { return Array.from(dropdownList.querySelectorAll('.dropdown-item:not(.hidden)')); }
    function show() {
        dropdownList.style.display = 'block'; isOpen = true; highlightedIndex = -1;
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
        if (idx >= 0 && idx < vis.length) { highlightedIndex = idx; vis[idx].classList.add('highlighted'); vis[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
    }
    function selectItem(item) {
        const val = item.dataset.value || '', name = item.dataset.name || '';
        hiddenInput.value = val; input.value = val ? name : '';
        dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
        item.classList.add('selected'); hide();
        focusFirstHsnCode();
    }

    input.addEventListener('focus', function() { show(); filter(this.value); });
    input.addEventListener('input', function() { show(); filter(this.value); });
    input.addEventListener('keydown', function(e) {
        if (!isOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) { show(); filter(this.value); return; }
        if (!isOpen) { if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); focusFirstHsnCode(); } return; }
        const vis = getVisible();
        switch (e.key) {
            case 'ArrowDown': e.preventDefault(); highlight(highlightedIndex < vis.length - 1 ? highlightedIndex + 1 : 0); break;
            case 'ArrowUp': e.preventDefault(); highlight(highlightedIndex > 0 ? highlightedIndex - 1 : vis.length - 1); break;
            case 'Enter': e.preventDefault(); e.stopPropagation();
                if (highlightedIndex >= 0 && highlightedIndex < vis.length) selectItem(vis[highlightedIndex]);
                else if (vis.length > 0) selectItem(vis[0]); break;
            case 'Escape': e.preventDefault(); hide(); break;
            case 'Tab': if (highlightedIndex >= 0 && highlightedIndex < vis.length) selectItem(vis[highlightedIndex]); hide(); break;
        }
    });
    dropdownList.addEventListener('click', function(e) { const item = e.target.closest('.dropdown-item'); if (item) selectItem(item); });
    document.addEventListener('click', function(e) { if (!e.target.closest('#customerDropdownWrapper')) hide(); });
}

// =============================================
// HEADER KEYBOARD NAVIGATION
// =============================================
function initHeaderKeyboardNav() {
    const returnDate = document.getElementById('returnDate');
    if (returnDate) {
        returnDate.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); e.stopPropagation();
                const cust = document.getElementById('customerSearchInput');
                if (cust) cust.focus();
            }
        });
    }
}

function focusFirstHsnCode() {
    const firstRow = document.querySelector('#hsnTableBody tr');
    if (firstRow) { const h = firstRow.querySelector('.hsn-code'); if (h) { h.focus(); h.select(); } }
}

// =============================================
// TABLE KEYBOARD NAVIGATION
// =============================================
function initTableKeyboardNav() {
    document.getElementById('hsnTableBody').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (e.ctrlKey || e.metaKey) return;
        const target = e.target;
        if (target.tagName !== 'INPUT') return;
        const row = target.closest('tr');
        if (!row) return;
        const rowId = row.getAttribute('data-row');
        e.preventDefault(); e.stopPropagation();

        const editableClasses = ['hsn-code', 'amount', 'gst-percent', 'qty'];
        let currentClass = null;
        for (const cls of editableClasses) { if (target.classList.contains(cls)) { currentClass = cls; break; } }
        if (!currentClass) return;
        const currentIdx = editableClasses.indexOf(currentClass);

        if (currentClass === 'hsn-code' && !e.shiftKey) { selectRow(parseInt(rowId)); openHsnModal(); return; }
        if (currentClass === 'amount' || currentClass === 'gst-percent') calculateRowTax(parseInt(rowId));

        // Shift+Enter: backwards
        if (e.shiftKey) {
            if (currentIdx > 0) { const p = row.querySelector('.' + editableClasses[currentIdx - 1]); if (p) { p.focus(); p.select(); } }
            else { const pr = row.previousElementSibling; if (pr) { const pq = pr.querySelector('.qty'); if (pq) { pq.focus(); pq.select(); } } }
            return;
        }
        // Enter: forward
        if (currentIdx < editableClasses.length - 1) { const n = row.querySelector('.' + editableClasses[currentIdx + 1]); if (n) { n.focus(); n.select(); } }
        else {
            const nr = row.nextElementSibling;
            if (nr) { const nh = nr.querySelector('.hsn-code'); if (nh) { nh.focus(); nh.select(); } }
            else { addNewRow(); setTimeout(() => { const lr = document.querySelector('#hsnTableBody tr:last-child'); if (lr) { const h = lr.querySelector('.hsn-code'); if (h) h.focus(); } }, 50); }
        }
    });
}

// =============================================
// HSN MODAL KEYBOARD
// =============================================
let hsnHighlightedIndex = -1;
function getVisibleHsnItems() { return Array.from(document.querySelectorAll('.hsn-list-item')).filter(i => i.style.display !== 'none'); }
function highlightHsnItem(idx) {
    const items = getVisibleHsnItems();
    items.forEach(i => i.classList.remove('hsn-highlighted'));
    if (idx >= 0 && idx < items.length) { hsnHighlightedIndex = idx; items[idx].classList.add('hsn-highlighted'); items[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
}
function initHsnModalKeyboard() {
    document.getElementById('hsnSearch').addEventListener('input', filterHsn);
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('hsnModal');
        if (!modal || !modal.classList.contains('show')) return;
        const items = getVisibleHsnItems();
        if (items.length === 0) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); highlightHsnItem(hsnHighlightedIndex < items.length - 1 ? hsnHighlightedIndex + 1 : 0); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlightHsnItem(hsnHighlightedIndex > 0 ? hsnHighlightedIndex - 1 : items.length - 1); }
        else if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation();
            if (hsnHighlightedIndex >= 0 && hsnHighlightedIndex < items.length) {
                const item = items[hsnHighlightedIndex];
                selectHsn(item.dataset.hsnCode, parseFloat(item.dataset.cgst), parseFloat(item.dataset.sgst), parseFloat(item.dataset.gst));
            }
        }
    }, true);
}

// =============================================
// SHORTCUTS: CTRL+S, CTRL+ENTER
// =============================================
function initShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!e.ctrlKey && !e.metaKey) return;
        if (e.key === 's') { e.preventDefault(); saveVoucher(); }
        else if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            const remarks = document.getElementById('remarks');
            if (remarks) { remarks.focus(); remarks.select(); }
        }
    }, true);
}

// =============================================
// CORE FUNCTIONS
// =============================================
function addNewRow() {
    rowCounter++;
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    row.innerHTML = `
        <td><input type="text" class="hsn-code" data-custom-enter="true" onclick="selectRow(${rowCounter})" onfocus="selectRow(${rowCounter})" placeholder="HSN"></td>
        <td><input type="number" class="amount" step="0.01" data-custom-enter="true" onchange="calculateRowTax(${rowCounter})" oninput="calculateRowTax(${rowCounter})"></td>
        <td><input type="number" class="gst-percent" step="0.01" data-custom-enter="true" onchange="calculateRowTax(${rowCounter})"></td>
        <td><input type="number" class="cgst-percent" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="cgst-amount" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-percent" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-amount" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="qty" value="0" min="0" data-custom-enter="true"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${rowCounter})"><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('hsnTableBody').appendChild(row);
}

function selectRow(i) { selectedRowIndex = i; document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('row-selected')); document.querySelector(`#hsnTableBody tr[data-row="${i}"]`)?.classList.add('row-selected'); }
function deleteRow(i) { const row = document.querySelector(`#hsnTableBody tr[data-row="${i}"]`); if (row) { row.remove(); calculateTotals(); if (!document.querySelectorAll('#hsnTableBody tr').length) addNewRow(); } }

function calculateRowTax(i) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${i}"]`);
    if (!row) return;
    const amt = parseFloat(row.querySelector('.amount').value) || 0;
    const gst = parseFloat(row.querySelector('.gst-percent').value) || 0;
    row.querySelector('.cgst-percent').value = (gst/2).toFixed(2);
    row.querySelector('.cgst-amount').value = (amt*gst/200).toFixed(2);
    row.querySelector('.sgst-percent').value = (gst/2).toFixed(2);
    row.querySelector('.sgst-amount').value = (amt*gst/200).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let g=0, c=0, s=0;
    document.querySelectorAll('#hsnTableBody tr').forEach(r => { g += parseFloat(r.querySelector('.amount')?.value)||0; c += parseFloat(r.querySelector('.cgst-amount')?.value)||0; s += parseFloat(r.querySelector('.sgst-amount')?.value)||0; });
    const t = c+s, n = g+t, ro = Math.round(n)-n, f = Math.round(n);
    document.getElementById('grossAmt').textContent = g.toFixed(2);
    document.getElementById('totalGst').textContent = t.toFixed(2);
    document.getElementById('netAmt').textContent = n.toFixed(2);
    document.getElementById('roundOff').textContent = ro.toFixed(2);
    document.getElementById('finalAmount').textContent = f.toFixed(2);
    document.getElementById('totalCgst').textContent = c.toFixed(2);
    document.getElementById('totalSgst').textContent = s.toFixed(2);
    document.getElementById('totalDisplay').textContent = f.toFixed(2);
}

function openHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
    hsnHighlightedIndex = -1;
    const search = document.getElementById('hsnSearch');
    if (search) search.value = '';
    filterHsn();
    setTimeout(() => { if (search) search.focus(); highlightHsnItem(0); }, 100);
}
function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
    hsnHighlightedIndex = -1;
}
function filterHsn() {
    const s = document.getElementById('hsnSearch').value.toLowerCase();
    document.querySelectorAll('.hsn-list-item').forEach(i => i.style.display = i.textContent.toLowerCase().includes(s) ? '' : 'none');
    hsnHighlightedIndex = -1; highlightHsnItem(0);
}

function selectHsn(code, cgst, sgst, gst) {
    let t = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    if (!t) for (let r of document.querySelectorAll('#hsnTableBody tr')) if (!r.querySelector('.hsn-code').value) { t = r; break; }
    if (!t) { addNewRow(); t = document.querySelector('#hsnTableBody tr:last-child'); }
    t.querySelector('.hsn-code').value = code; t.querySelector('.gst-percent').value = gst; t.querySelector('.cgst-percent').value = cgst; t.querySelector('.sgst-percent').value = sgst;
    closeHsnModal(); t.querySelector('.amount').focus();
}

function saveVoucher() {
    const cid = document.getElementById('customerSelect').value;
    if (!cid) { alert('Select a customer'); return; }
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(r => {
        const h = r.querySelector('.hsn-code').value, a = parseFloat(r.querySelector('.amount').value)||0;
        if (h && a > 0) items.push({ hsn_code: h, amount: a, gst_percent: parseFloat(r.querySelector('.gst-percent').value)||0, cgst_percent: parseFloat(r.querySelector('.cgst-percent').value)||0, cgst_amount: parseFloat(r.querySelector('.cgst-amount').value)||0, sgst_percent: parseFloat(r.querySelector('.sgst-percent').value)||0, sgst_amount: parseFloat(r.querySelector('.sgst-amount').value)||0, qty: parseInt(r.querySelector('.qty').value)||0 });
    });
    if (!items.length) { alert('Add at least one item'); return; }
    if (typeof window.markAsSaving === 'function') window.markAsSaving();
    fetch('<?php echo e(route("admin.sale-return-voucher.store")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({ return_date: document.getElementById('returnDate').value, customer_id: cid, remarks: document.getElementById('remarks').value, items })
    }).then(r => r.json()).then(res => { if (res.success) { alert('Saved! Invoice: ' + res.invoice_no); location.reload(); } else alert('Error: ' + res.message); });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeHsnModal(); });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sale-return-voucher/transaction.blade.php ENDPATH**/ ?>