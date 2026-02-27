<?php $__env->startSection('title', 'Purchase Return Voucher Modification'); ?>
<?php $__env->startSection('disable_select2', '1'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; white-space: nowrap; color: #c00; }
    .hsn-table-container { background: #d4a5d4; padding: 10px; border: 2px solid #6b1482; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #8b2082; color: white; padding: 6px 8px; text-align: center; border: 1px solid #6b1482; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table input:focus { outline: 2px solid #ffc107; }
    .hsn-table .row-selected { background: #fffbcc; }
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .totals-section { background: #d4a5d4; padding: 10px; margin-top: 10px; border: 2px solid #6b1482; }
    .totals-table { font-size: 11px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    .btn-hsn { background: #8b2082; color: white; border: 1px solid #6b1482; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #6b1482; }
    .invoice-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
    .invoice-modal.show { display: flex; align-items: center; justify-content: center; }
    .invoice-modal-content { background: white; width: 600px; max-height: 80vh; border-radius: 8px; overflow: hidden; }
    .invoice-modal-header { background: #8b2082; color: white; padding: 12px; display: flex; justify-content: space-between; }
    .invoice-modal-body { max-height: 60vh; overflow-y: auto; padding: 15px; }
    .invoice-list-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
    .invoice-list-item:hover { background: #f8e6f8; }
    .invoice-list-item.inv-highlighted { background: #007bff !important; color: white !important; }
    .invoice-list-item.inv-highlighted .text-muted { color: #ddd !important; }
    .hsn-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
    .hsn-modal-backdrop.show { display: block; }
    .hsn-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: #f0f0f0; border: 2px solid #666; z-index: 9999; }
    .hsn-modal.show { display: block; }
    .hsn-modal-header { background: #8b2082; color: white; padding: 8px 12px; display: flex; justify-content: space-between; }
    .hsn-modal-body { max-height: 350px; overflow-y: auto; padding: 10px; }
    .hsn-list-item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; font-size: 11px; }
    .hsn-list-item:hover { background: #f8e6f8; }
    .hsn-list-item.hsn-highlighted { background: #007bff !important; color: white !important; }
    .hsn-list-item.hsn-highlighted strong { color: white !important; }

    /* Searchable Dropdown */
    .searchable-dropdown { position: relative; }
    .searchable-dropdown-input { width: 100%; cursor: text; }
    .searchable-dropdown-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .searchable-dropdown-list { position: absolute; top: 100%; left: 0; right: 0; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 4px 4px; z-index: 1080; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; }
    .searchable-dropdown-list .dropdown-item { padding: 6px 10px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #f0f0f0; }
    .searchable-dropdown-list .dropdown-item:hover { background-color: #f8f9fa; }
    .searchable-dropdown-list .dropdown-item.highlighted { background-color: #007bff !important; color: white !important; }
    .searchable-dropdown-list .dropdown-item.hidden { display: none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Purchase Return Voucher Modification</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm" id="loadInvoicesBtn" onclick="openInvoiceModal()"><i class="bi bi-list me-1"></i> Load Invoices</button>
        <a href="<?php echo e(route('admin.purchase-return-voucher.index')); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Vouchers</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Invoice No :</label>
                        <input type="text" class="form-control" id="searchInvoice" placeholder="Enter Invoice No" style="width: 150px;" data-custom-enter="true">
                        <button type="button" class="btn btn-sm btn-primary" id="loadBtn" onclick="searchVoucher()">Load</button>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="returnDate" style="width: 130px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Supplier :</label>
                        <input type="hidden" id="voucherId">
                        <div class="searchable-dropdown" id="supplierDropdownWrapper" style="width: 250px;">
                            <input type="text" id="supplierSearchInput" class="form-control searchable-dropdown-input" placeholder="Type to search supplier..." autocomplete="off" data-custom-enter="true">
                            <input type="hidden" id="supplierSelect" value="">
                            <div class="searchable-dropdown-list" id="supplierDropdownList" style="display: none;">
                                <div class="dropdown-item" data-value="" data-name="">Select Supplier</div>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="dropdown-item" data-value="<?php echo e($supplier->supplier_id); ?>" data-name="<?php echo e($supplier->name); ?>"><?php echo e($supplier->name); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2" style="background: #f8e6f8;">
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
                        <th style="width: 100px;">HSN Code</th><th style="width: 80px;">Amount</th><th style="width: 55px;">GST%</th>
                        <th style="width: 55px;">CGST%</th><th style="width: 75px;">Amount</th><th style="width: 55px;">SGST%</th>
                        <th style="width: 75px;">Amount</th><th style="width: 50px;">Qty.</th><th style="width: 50px;">Action</th>
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
                    <div class="field-group"><label style="width: 60px;">Remarks</label><input type="text" class="form-control" id="remarks" style="flex: 1;"></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="updateVoucher()" id="btnUpdate" disabled><i class="bi bi-check-circle me-1"></i> Update</button>
            <button type="button" class="btn-hsn px-4" onclick="clearForm()"><i class="bi bi-x-circle me-1"></i> Clear</button>
        </div>
    </div>
</div>

<!-- Invoice List Modal -->
<div class="invoice-modal" id="invoiceModal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h6 class="mb-0"><i class="bi bi-list me-2"></i>Select Voucher</h6>
            <button type="button" onclick="closeInvoiceModal()" style="background: none; border: none; color: white; font-size: 18px;">&times;</button>
        </div>
        <div class="p-2"><input type="text" class="form-control form-control-sm" id="invoiceSearch" placeholder="Search..." data-custom-enter="true"></div>
        <div class="invoice-modal-body" id="invoiceList"></div>
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
            <div class="hsn-list-item" data-hsn-code="<?php echo e($hsn->hsn_code); ?>" data-cgst="<?php echo e($hsn->cgst_percent); ?>" data-sgst="<?php echo e($hsn->sgst_percent); ?>" data-gst="<?php echo e($hsn->total_gst_percent); ?>" onclick="selectHsn('<?php echo e($hsn->hsn_code); ?>', <?php echo e($hsn->cgst_percent); ?>, <?php echo e($hsn->sgst_percent); ?>, <?php echo e($hsn->total_gst_percent); ?>)">
                <strong><?php echo e($hsn->hsn_code); ?></strong> - <?php echo e($hsn->name); ?> (<?php echo e($hsn->total_gst_percent); ?>%)
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let rowCounter = 0, selectedRowIndex = null, currentVoucherId = null, vouchersData = [];
window.SKIP_AUTO_FOCUS = true;

document.addEventListener('DOMContentLoaded', function() {
    loadVouchersForModal();
    for (let i = 0; i < 5; i++) addNewRow();
    const urlParams = new URLSearchParams(window.location.search);
    const inv = urlParams.get('invoice_no');
    if (inv) { document.getElementById('searchInvoice').value = inv; searchVoucher(); }

    initSupplierDropdown();
    initHeaderKeyboardNav();
    initTableKeyboardNav();
    initHsnModalKeyboard();
    initInvoiceModalKeyboard();
    initShortcuts();

    // Focus Load Invoices on page load
    setTimeout(function() { const btn = document.getElementById('loadInvoicesBtn'); if (btn) btn.focus(); }, 200);
});

// =============================================
// CUSTOM SEARCHABLE SUPPLIER DROPDOWN
// =============================================
function initSupplierDropdown() {
    const input = document.getElementById('supplierSearchInput'), hiddenInput = document.getElementById('supplierSelect'), dropdownList = document.getElementById('supplierDropdownList');
    if (!input || !hiddenInput || !dropdownList) return;
    let highlightedIndex = -1, isOpen = false;
    function getVisible() { return Array.from(dropdownList.querySelectorAll('.dropdown-item:not(.hidden)')); }
    function show() { dropdownList.style.display = 'block'; isOpen = true; highlightedIndex = -1; setTimeout(() => highlight(0), 0); }
    function hide() { dropdownList.style.display = 'none'; isOpen = false; highlightedIndex = -1; dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('highlighted')); }
    function filter(text) { const s = (text||'').toLowerCase().trim(); dropdownList.querySelectorAll('.dropdown-item').forEach(item => { item.classList.toggle('hidden', s !== '' && !item.textContent.toLowerCase().includes(s)); }); highlightedIndex = -1; }
    function highlight(idx) { const vis = getVisible(); vis.forEach(i => i.classList.remove('highlighted')); if (idx >= 0 && idx < vis.length) { highlightedIndex = idx; vis[idx].classList.add('highlighted'); vis[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' }); } }
    function selectItem(item) { hiddenInput.value = item.dataset.value||''; input.value = item.dataset.value ? (item.dataset.name||'') : ''; hide(); focusFirstHsnCode(); }

    input.addEventListener('focus', function() { show(); filter(this.value); });
    input.addEventListener('input', function() { show(); filter(this.value); });
    input.addEventListener('keydown', function(e) {
        if (!isOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) { show(); filter(this.value); return; }
        if (!isOpen) { if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); focusFirstHsnCode(); } return; }
        const vis = getVisible();
        if (e.key === 'ArrowDown') { e.preventDefault(); highlight(highlightedIndex < vis.length - 1 ? highlightedIndex + 1 : 0); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(highlightedIndex > 0 ? highlightedIndex - 1 : vis.length - 1); }
        else if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); if (highlightedIndex >= 0 && highlightedIndex < vis.length) selectItem(vis[highlightedIndex]); else if (vis.length > 0) selectItem(vis[0]); }
        else if (e.key === 'Escape') { e.preventDefault(); hide(); }
        else if (e.key === 'Tab') { if (highlightedIndex >= 0 && highlightedIndex < vis.length) selectItem(vis[highlightedIndex]); hide(); }
    });
    dropdownList.addEventListener('click', function(e) { const item = e.target.closest('.dropdown-item'); if (item) selectItem(item); });
    document.addEventListener('click', function(e) { if (!e.target.closest('#supplierDropdownWrapper')) hide(); });

    window.setSupplierById = function(id) { hiddenInput.value = id; const item = dropdownList.querySelector(`.dropdown-item[data-value="${id}"]`); input.value = item ? (item.dataset.name||'') : ''; };
}

// =============================================
// HEADER KEYBOARD NAVIGATION
// =============================================
function initHeaderKeyboardNav() {
    const search = document.getElementById('searchInvoice');
    if (search) search.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); searchVoucher(); } });
    const date = document.getElementById('returnDate');
    if (date) date.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); document.getElementById('supplierSearchInput')?.focus(); } });
}
function focusFirstHsnCode() { const r = document.querySelector('#hsnTableBody tr'); if (r) { const h = r.querySelector('.hsn-code'); if (h) { h.focus(); h.select(); } } }

// =============================================
// TABLE KEYBOARD NAVIGATION
// =============================================
function initTableKeyboardNav() {
    document.getElementById('hsnTableBody').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' || e.ctrlKey || e.metaKey) return;
        const target = e.target; if (target.tagName !== 'INPUT') return;
        const row = target.closest('tr'); if (!row) return;
        const rowId = row.getAttribute('data-row');
        e.preventDefault(); e.stopPropagation();
        const ec = ['hsn-code', 'amount', 'gst-percent', 'qty'];
        let cc = null; for (const c of ec) { if (target.classList.contains(c)) { cc = c; break; } } if (!cc) return;
        const ci = ec.indexOf(cc);
        if (cc === 'hsn-code' && !e.shiftKey) { if (!target.value.trim()) { selectRow(parseInt(rowId)); openHsnModal(); } else { const amt = row.querySelector('.amount'); if (amt) { amt.focus(); amt.select(); } } return; }
        if (cc === 'amount' || cc === 'gst-percent') calculateRowTax(parseInt(rowId));
        if (e.shiftKey) { if (ci > 0) { const p = row.querySelector('.'+ec[ci-1]); if (p) { p.focus(); p.select(); } } else { const pr = row.previousElementSibling; if (pr) { const pq = pr.querySelector('.qty'); if (pq) { pq.focus(); pq.select(); } } } return; }
        if (ci < ec.length - 1) { const n = row.querySelector('.'+ec[ci+1]); if (n) { n.focus(); n.select(); } }
        else { const nr = row.nextElementSibling; if (nr) { const nh = nr.querySelector('.hsn-code'); if (nh) { nh.focus(); nh.select(); } } else { addNewRow(); setTimeout(() => { const lr = document.querySelector('#hsnTableBody tr:last-child'); if (lr) { const h = lr.querySelector('.hsn-code'); if (h) h.focus(); } }, 50); } }
    });
}

// =============================================
// HSN MODAL KEYBOARD
// =============================================
let hsnHighlightedIndex = -1;
function getVisibleHsnItems() { return Array.from(document.querySelectorAll('.hsn-list-item')).filter(i => i.style.display !== 'none'); }
function highlightHsnItem(idx) { const items = getVisibleHsnItems(); items.forEach(i => i.classList.remove('hsn-highlighted')); if (idx >= 0 && idx < items.length) { hsnHighlightedIndex = idx; items[idx].classList.add('hsn-highlighted'); items[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' }); } }
function initHsnModalKeyboard() {
    document.getElementById('hsnSearch').addEventListener('input', filterHsn);
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('hsnModal'); if (!modal || !modal.classList.contains('show')) return;
        const items = getVisibleHsnItems(); if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); highlightHsnItem(hsnHighlightedIndex < items.length - 1 ? hsnHighlightedIndex + 1 : 0); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlightHsnItem(hsnHighlightedIndex > 0 ? hsnHighlightedIndex - 1 : items.length - 1); }
        else if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); if (hsnHighlightedIndex >= 0 && hsnHighlightedIndex < items.length) { const item = items[hsnHighlightedIndex]; selectHsn(item.dataset.hsnCode, parseFloat(item.dataset.cgst), parseFloat(item.dataset.sgst), parseFloat(item.dataset.gst)); } }
    }, true);
}

// =============================================
// INVOICE MODAL KEYBOARD
// =============================================
let invHighlightedIndex = -1;
function getVisibleInvItems() { return Array.from(document.querySelectorAll('#invoiceList .invoice-list-item')); }
function highlightInvItem(idx) { const items = getVisibleInvItems(); items.forEach(i => i.classList.remove('inv-highlighted')); if (idx >= 0 && idx < items.length) { invHighlightedIndex = idx; items[idx].classList.add('inv-highlighted'); items[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' }); } }
function initInvoiceModalKeyboard() {
    document.getElementById('invoiceSearch').addEventListener('input', function() { filterInvoices(); setTimeout(() => highlightInvItem(0), 50); });
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('invoiceModal'); if (!modal || !modal.classList.contains('show')) return;
        const items = getVisibleInvItems();
        if (e.key === 'ArrowDown') { e.preventDefault(); highlightInvItem(invHighlightedIndex < items.length - 1 ? invHighlightedIndex + 1 : 0); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlightInvItem(invHighlightedIndex > 0 ? invHighlightedIndex - 1 : items.length - 1); }
        else if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); if (invHighlightedIndex >= 0 && invHighlightedIndex < items.length) items[invHighlightedIndex].click(); }
    }, true);
}

// =============================================
// SHORTCUTS: CTRL+S, CTRL+ENTER
// =============================================
function initShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!e.ctrlKey && !e.metaKey) return;
        if (e.key === 's') { e.preventDefault(); updateVoucher(); }
        else if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation(); const r = document.getElementById('remarks'); if (r) { r.focus(); r.select(); } }
    }, true);
}

// =============================================
// DATA FUNCTIONS
// =============================================
function loadVouchersForModal() {
    fetch('<?php echo e(route("admin.purchase-return-voucher.get-vouchers")); ?>')
    .then(r => r.json()).then(res => { if (res.success) { vouchersData = res.vouchers; renderInvoiceList(vouchersData); } });
}
function renderInvoiceList(vouchers) {
    document.getElementById('invoiceList').innerHTML = vouchers.map(v => `
        <div class="invoice-list-item" onclick="loadVoucher(${v.id})" data-voucher-id="${v.id}">
            <div class="d-flex justify-content-between"><strong>${v.invoice_no}</strong><span class="text-muted">${v.return_date}</span></div>
            <div class="text-muted small">${v.supplier_name} - ₹${parseFloat(v.net_amount).toFixed(2)}</div>
        </div>
    `).join('');
}
function filterInvoices() { const s = document.getElementById('invoiceSearch').value.toLowerCase(); renderInvoiceList(vouchersData.filter(v => v.invoice_no.toLowerCase().includes(s) || v.supplier_name.toLowerCase().includes(s))); }
function openInvoiceModal() { document.getElementById('invoiceModal').classList.add('show'); invHighlightedIndex = -1; setTimeout(() => { const s = document.getElementById('invoiceSearch'); if (s) { s.value = ''; s.focus(); } highlightInvItem(0); }, 100); }
function closeInvoiceModal() { document.getElementById('invoiceModal').classList.remove('show'); invHighlightedIndex = -1; }

function loadVoucher(id) { closeInvoiceModal(); fetch(`<?php echo e(url('admin/purchase-return-voucher')); ?>/${id}/details`).then(r => r.json()).then(res => { if (res.success) populateForm(res.voucher); else alert('Error: ' + res.message); }); }
function searchVoucher() { const inv = document.getElementById('searchInvoice').value.trim(); if (!inv) { alert('Enter invoice number'); return; } fetch(`<?php echo e(route('admin.purchase-return-voucher.search')); ?>?invoice_no=${encodeURIComponent(inv)}`).then(r => r.json()).then(res => { if (res.success) populateForm(res.voucher); else alert('Not found'); }); }

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('searchInvoice').value = voucher.invoice_no;
    document.getElementById('returnDate').value = voucher.return_date;
    if (typeof window.setSupplierById === 'function') window.setSupplierById(voucher.supplier_id);
    else document.getElementById('supplierSelect').value = voucher.supplier_id;
    document.getElementById('remarks').value = voucher.remarks || '';
    document.getElementById('hsnTableBody').innerHTML = '';
    rowCounter = 0;
    if (voucher.items && voucher.items.length > 0) {
        voucher.items.forEach(item => {
            addNewRow();
            const row = document.querySelector('#hsnTableBody tr:last-child');
            row.querySelector('.hsn-code').value = item.hsn_code || '';
            row.querySelector('.amount').value = item.amount || 0;
            row.querySelector('.gst-percent').value = item.gst_percent || 0;
            row.querySelector('.cgst-percent').value = item.cgst_percent || 0;
            row.querySelector('.cgst-amount').value = item.cgst_amount || 0;
            row.querySelector('.sgst-percent').value = item.sgst_percent || 0;
            row.querySelector('.sgst-amount').value = item.sgst_amount || 0;
            row.querySelector('.qty').value = item.qty || 0;
        });
    }
    addNewRow(); calculateTotals();
    document.getElementById('btnUpdate').disabled = false;
    setTimeout(focusFirstHsnCode, 200);
}

function addNewRow() {
    rowCounter++;
    const row = document.createElement('tr'); row.setAttribute('data-row', rowCounter);
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
    const row = document.querySelector(`#hsnTableBody tr[data-row="${i}"]`); if (!row) return;
    const amt = parseFloat(row.querySelector('.amount').value)||0, gst = parseFloat(row.querySelector('.gst-percent').value)||0;
    row.querySelector('.cgst-percent').value = (gst/2).toFixed(2); row.querySelector('.cgst-amount').value = (amt*gst/200).toFixed(2);
    row.querySelector('.sgst-percent').value = (gst/2).toFixed(2); row.querySelector('.sgst-amount').value = (amt*gst/200).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let g=0, c=0, s=0;
    document.querySelectorAll('#hsnTableBody tr').forEach(r => { g += parseFloat(r.querySelector('.amount')?.value)||0; c += parseFloat(r.querySelector('.cgst-amount')?.value)||0; s += parseFloat(r.querySelector('.sgst-amount')?.value)||0; });
    const t = c+s, n = g+t, ro = Math.round(n)-n, f = Math.round(n);
    document.getElementById('grossAmt').textContent = g.toFixed(2); document.getElementById('totalGst').textContent = t.toFixed(2);
    document.getElementById('netAmt').textContent = n.toFixed(2); document.getElementById('roundOff').textContent = ro.toFixed(2);
    document.getElementById('finalAmount').textContent = f.toFixed(2); document.getElementById('totalCgst').textContent = c.toFixed(2);
    document.getElementById('totalSgst').textContent = s.toFixed(2); document.getElementById('totalDisplay').textContent = f.toFixed(2);
}

function openHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show'); document.getElementById('hsnModal').classList.add('show');
    hsnHighlightedIndex = -1; const s = document.getElementById('hsnSearch'); if (s) s.value = ''; filterHsn();
    setTimeout(() => { if (s) s.focus(); highlightHsnItem(0); }, 100);
}
function closeHsnModal() { document.getElementById('hsnModalBackdrop').classList.remove('show'); document.getElementById('hsnModal').classList.remove('show'); hsnHighlightedIndex = -1; }
function filterHsn() { const s = document.getElementById('hsnSearch').value.toLowerCase(); document.querySelectorAll('.hsn-list-item').forEach(i => i.style.display = i.textContent.toLowerCase().includes(s) ? '' : 'none'); hsnHighlightedIndex = -1; highlightHsnItem(0); }

function selectHsn(code, cgst, sgst, gst) {
    let t = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    if (!t) for (let r of document.querySelectorAll('#hsnTableBody tr')) if (!r.querySelector('.hsn-code').value) { t = r; break; }
    if (!t) { addNewRow(); t = document.querySelector('#hsnTableBody tr:last-child'); }
    t.querySelector('.hsn-code').value = code; t.querySelector('.gst-percent').value = gst; t.querySelector('.cgst-percent').value = cgst; t.querySelector('.sgst-percent').value = sgst;
    closeHsnModal(); t.querySelector('.amount').focus();
}

function clearForm() {
    currentVoucherId = null; document.getElementById('voucherId').value = ''; document.getElementById('searchInvoice').value = '';
    document.getElementById('returnDate').value = ''; document.getElementById('supplierSelect').value = ''; document.getElementById('supplierSearchInput').value = '';
    document.getElementById('remarks').value = ''; document.getElementById('hsnTableBody').innerHTML = ''; document.getElementById('btnUpdate').disabled = true;
    rowCounter = 0; for (let i = 0; i < 5; i++) addNewRow(); calculateTotals();
}

let isSubmitting = false;
function updateVoucher() {
    if (!currentVoucherId) { alert('Load a voucher first'); return; }
    if (isSubmitting) return; isSubmitting = true;
    const updateBtn = document.getElementById('btnUpdate'), originalBtnHtml = updateBtn.innerHTML;
    updateBtn.disabled = true; updateBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating...';
    const sid = document.getElementById('supplierSelect').value;
    if (!sid) { alert('Select a supplier'); isSubmitting = false; updateBtn.disabled = false; updateBtn.innerHTML = originalBtnHtml; return; }
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(r => {
        const h = r.querySelector('.hsn-code').value, a = parseFloat(r.querySelector('.amount').value)||0;
        if (h && a > 0) items.push({ hsn_code: h, amount: a, gst_percent: parseFloat(r.querySelector('.gst-percent').value)||0, cgst_percent: parseFloat(r.querySelector('.cgst-percent').value)||0, cgst_amount: parseFloat(r.querySelector('.cgst-amount').value)||0, sgst_percent: parseFloat(r.querySelector('.sgst-percent').value)||0, sgst_amount: parseFloat(r.querySelector('.sgst-amount').value)||0, qty: parseInt(r.querySelector('.qty').value)||0 });
    });
    if (!items.length) { alert('Add at least one item'); isSubmitting = false; updateBtn.disabled = false; updateBtn.innerHTML = originalBtnHtml; return; }
    if (typeof window.markAsSaving === 'function') window.markAsSaving();
    fetch(`<?php echo e(url('admin/purchase-return-voucher')); ?>/${currentVoucherId}`, {
        method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({ return_date: document.getElementById('returnDate').value, supplier_id: sid, remarks: document.getElementById('remarks').value, items })
    }).then(r => r.json()).then(res => {
        if (res.success) { alert('Updated!'); loadVouchersForModal(); }
        else { alert('Error: ' + res.message); isSubmitting = false; updateBtn.disabled = false; updateBtn.innerHTML = originalBtnHtml; }
    }).catch(e => { console.error(e); alert('Error updating'); isSubmitting = false; updateBtn.disabled = false; updateBtn.innerHTML = originalBtnHtml; });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeHsnModal(); closeInvoiceModal(); } });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/purchase-return-voucher/modification.blade.php ENDPATH**/ ?>