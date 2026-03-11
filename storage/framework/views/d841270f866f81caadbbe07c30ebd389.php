<?php $__env->startSection('title', 'Sample Received - New Transaction'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .sr-form { font-size: 11px; }
    .sr-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .sr-form input, .sr-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #d4edda; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-complete { background-color: #d4edda !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #28a745; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }

    /* ── MOBILE RESPONSIVE ── */
@media (max-width: 767px) {

    body { overflow-x: hidden !important; }
    .card-body { padding: 8px !important; }

    /* ── Page title row ── */
    .d-flex.justify-content-between.align-items-center.mb-3 {
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .d-flex.justify-content-between.align-items-center.mb-3 > div:last-child {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        width: 100% !important;
    }
    .d-flex.justify-content-between.align-items-center.mb-3 .btn {
        flex: 1 1 calc(50% - 4px) !important;
        text-align: center !important;
    }

    /* ── Header Row 1 & Row 2: col-md-* → 100% ── */
    .header-section .row.g-2 > [class*="col-md-"] {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    .header-section .field-group {
        flex-wrap: nowrap !important;
    }
    .header-section .field-group input {
        flex: 1 !important;
        width: auto !important;
        min-width: 0 !important;
    }
    /* Party type & party dropdowns full width */
    .header-section .col-md-2 [style*="position:relative"],
    .header-section .col-md-4 [style*="position:relative"] {
        width: 100% !important;
        flex: 1 !important;
    }
    /* On/Rate row — keep inline with gap */
    .header-section .col-md-2 .field-group { gap: 4px !important; }

    /* ── Items Table → horizontal scroll ── */
    #itemsTableContainer {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    #itemsTableContainer .table-compact { min-width: 560px !important; }

    /* ── Summary → full width ── */
    .summary-section.mb-2.d-flex.justify-content-end {
        justify-content: stretch !important;
    }
    .summary-section .field-group {
        width: 100% !important;
    }
    .summary-section .field-group input {
        flex: 1 !important;
        width: auto !important;
        min-width: 0 !important;
    }

    /* ── Footer: col-md-8 spacer hide, col-md-2 → 50% ── */
    .footer-section .row.g-2 > .col-md-8 {
        display: none !important;
    }
    .footer-section .row.g-2 > .col-md-2 {
        flex: 0 0 50% !important;
        max-width: 50% !important;
    }
    .footer-section .field-group input {
        flex: 1 !important;
        width: auto !important;
        min-width: 0 !important;
    }

    /* ── Action Buttons → wrap ── */
    .d-flex.justify-content-between.mt-3 {
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .d-flex.justify-content-between.mt-3 > div {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .d-flex.justify-content-between.mt-3 .btn {
        flex: 1 !important;
        padding: 10px 4px !important;
        text-align: center !important;
    }

    .toast-container { left: 10px !important; right: 10px !important; max-width: calc(100vw - 20px) !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="sr-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-down me-2"></i> Sample Received - New Transaction</h4>
                <div class="text-muted small">Receive samples from customer/doctor/salesman</div>
            </div>
            <div>
                <a href="<?php echo e(route('admin.sample-received.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="srForm" method="POST" autocomplete="off" onsubmit="return false;">
                    <?php echo csrf_field(); ?>
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="<?php echo e(date('l')); ?>" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="<?php echo e($trnNo); ?>" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 70px;">Party Type :</label>
                                    <input type="hidden" id="party_type" name="party_type" value="<?php echo e(array_key_first($partyTypes)); ?>">
                                    <div style="position:relative;flex:1;">
                                        <input type="text" id="partyTypeSearchInput" class="form-control"
                                            placeholder="Select type..." autocomplete="off" readonly
                                            value="<?php echo e(collect($partyTypes)->first()); ?>"
                                            onfocus="_ptShowDrop()"
                                            onclick="_ptShowDrop()">
                                        <div id="partyTypeDropList" style="display:none;position:absolute;top:100%;left:0;right:0;
                                            max-height:180px;overflow-y:auto;background:#fff;border:1px solid #ccc;
                                            box-shadow:0 4px 8px rgba(0,0,0,.15);z-index:9999;font-size:12px;">
                                            <?php $__currentLoopData = $partyTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="pt-item" data-value="<?php echo e($key); ?>"
                                                style="padding:5px 10px;cursor:pointer;border-bottom:1px solid #f0f0f0;"
                                                onmousedown="_ptSelectByEl(this)"><?php echo e($label); ?></div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <input type="hidden" id="party_id" name="party_id" value="">
                                    <input type="hidden" id="party_name" name="party_name" value="">
                                    <div style="position:relative;flex:1;">
                                        <input type="text" id="partySearchInput" class="form-control"
                                            placeholder="Search party..." autocomplete="off"
                                            oninput="_partyFilter()"
                                            onfocus="_partyShowDrop()">
                                        <div id="partyDropList" style="display:none;position:absolute;top:100%;left:0;right:0;
                                            max-height:200px;overflow-y:auto;background:#fff;border:1px solid #ccc;
                                            box-shadow:0 4px 8px rgba(0,0,0,.15);z-index:9999;font-size:12px;"></div>
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 30px;">On :</label>
                                    <input type="text" id="on_field" name="on_field" class="form-control" style="width: 50px;">
                                    <label style="width: 35px;">Rate :</label>
                                    <input type="number" id="rate" name="rate" class="form-control text-end" step="0.01" value="0" style="width: 70px;">
                                </div>
                                <div class="field-group">
                                    <label style="width: 30px;">Tag :</label>
                                    <input type="text" id="tag" name="tag" class="form-control" style="width: 80px;">
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No. :</label>
                                    <input type="text" id="gr_no" name="gr_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date :</label>
                                    <input type="date" id="gr_date" name="gr_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases :</label>
                                    <input type="number" id="cases" name="cases" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Road Permit :</label>
                                    <input type="text" id="road_permit_no" name="road_permit_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">Truck No. :</label>
                                    <input type="text" id="truck_no" name="truck_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport :</label>
                                    <input type="text" id="transport" name="transport" class="form-control" onkeydown="handleTransportKeydown(event)">
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
                                        <th style="width: 70px;">Expiry</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showItemSelectionModal()">
                                <i class="bi bi-search"></i> Add Items
                            </button>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Net :</label>
                            <input type="text" id="net_amount" name="net_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 40px;">Pack :</label>
                                    <input type="text" id="packing" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Unit :</label>
                                    <input type="text" id="unit" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">Cl.Qty :</label>
                                    <input type="text" id="cl_qty" class="form-control readonly-field text-end" value="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-8"></div>
                            <div class="col-md-2 text-end">
                                <div class="field-group justify-content-end">
                                    <label style="width: 40px;">Srlno :</label>
                                    <input type="text" id="srlno" class="form-control text-end" style="width: 80px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="saveTransaction()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelSampleReceive()">
                                <i class="bi bi-x-circle"></i> Cancel Sample Receive
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
    'id' => 'sampleReceivedItemModal',
    'module' => 'sample-received',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'sampleReceivedBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'sampleReceivedBatchModal',
    'module' => 'sample-received',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    loadPartyList(); // pre-load party list
    setTimeout(() => document.getElementById('transaction_date')?.focus(), 150);
});

function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

function loadItems() {
    fetch('<?php echo e(route("admin.sample-received.getItems")); ?>')
        .then(response => response.json())
        .then(data => {
            itemsData = data || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ============ PARTY DROPDOWN FUNCTIONS ============
// ── Party Type custom dropdown ──
let _ptHilIdx = -1;

function _ptShowDrop() {
    document.getElementById('partyTypeDropList').style.display = 'block';
    _ptHilIdx = -1;
    // highlight current
    const cur = document.getElementById('party_type').value;
    Array.from(document.querySelectorAll('#partyTypeDropList .pt-item')).forEach((el,i) => {
        el.style.background = el.dataset.value === cur ? '#0d6efd' : '';
        el.style.color      = el.dataset.value === cur ? '#fff' : '';
        if (el.dataset.value === cur) _ptHilIdx = i;
    });
}

function _ptHideDrop() {
    document.getElementById('partyTypeDropList').style.display = 'none';
}

function _ptHilAt(idx) {
    const items = Array.from(document.querySelectorAll('#partyTypeDropList .pt-item'));
    items.forEach(i => { i.style.background=''; i.style.color=''; });
    if (idx < 0) idx = 0;
    if (idx >= items.length) idx = items.length - 1;
    _ptHilIdx = idx;
    if (items[idx]) { items[idx].style.background='#0d6efd'; items[idx].style.color='#fff'; items[idx].scrollIntoView({block:'nearest'}); }
}

function _ptSelectByEl(el) {
    document.getElementById('party_type').value = el.dataset.value;
    document.getElementById('partyTypeSearchInput').value = el.textContent.trim();
    _ptHideDrop();
    // Reset party selection
    _partyAllItems = [];
    document.getElementById('party_id').value = '';
    document.getElementById('party_name').value = '';
    document.getElementById('partySearchInput').value = '';
    // Fetch new list, then focus + open dropdown
    const partyType = el.dataset.value;
    fetch(`<?php echo e(url('admin/sample-received/get-party-list')); ?>?party_type=${partyType}`)
        .then(r => r.json())
        .then(data => {
            _partyAllItems = data || [];
            document.getElementById('partySearchInput')?.focus();
            _partyBuildDrop(_partyAllItems);
        })
        .catch(() => {
            _partyAllItems = [];
            document.getElementById('partySearchInput')?.focus();
        });
}

// Party type keyboard
window.addEventListener('keydown', function(e) {
    if (document.activeElement?.id !== 'partyTypeSearchInput') return;
    const MANAGED = ['ArrowDown','ArrowUp','Enter','Escape'];
    if (!MANAGED.includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const list = document.getElementById('partyTypeDropList');
    const isOpen = list.style.display === 'block';

    if (e.key === 'Escape') { _ptHideDrop(); return; }
    if (e.key === 'ArrowDown') {
        if (!isOpen) _ptShowDrop();
        const items = document.querySelectorAll('#partyTypeDropList .pt-item');
        if (_ptHilIdx < items.length - 1) _ptHilAt(_ptHilIdx + 1);
        return;
    }
    if (e.key === 'ArrowUp') {
        if (_ptHilIdx > 0) _ptHilAt(_ptHilIdx - 1);
        return;
    }
    if (e.key === 'Enter') {
        const items = Array.from(document.querySelectorAll('#partyTypeDropList .pt-item'));
        const el = _ptHilIdx >= 0 ? items[_ptHilIdx] : items[0];
        if (el) { _ptSelectByEl(el); } // focus handled inside after fetch
        else _ptHideDrop();
        return;
    }
}, true);

document.addEventListener('click', function(e) {
    if (!e.target.closest('#partyTypeDropList') && e.target.id !== 'partyTypeSearchInput') _ptHideDrop();
});

function selectPartyType(val, btn) {} // kept for compatibility

// ── Party list AJAX ──
let _partyAllItems = [];
let _partyHilIdx = -1;

function loadPartyList() {
    const partyType = document.getElementById('party_type').value;
    fetch(`<?php echo e(url('admin/sample-received/get-party-list')); ?>?party_type=${partyType}`)
        .then(r => r.json())
        .then(data => {
            _partyAllItems = data || [];
        })
        .catch(() => { _partyAllItems = []; });
}

function updatePartyName() {} // kept for compatibility

function _partyBuildDrop(items) {
    const list = document.getElementById('partyDropList');
    list.innerHTML = '';
    if (!items.length) {
        list.innerHTML = '<div style="padding:6px 10px;color:#999;">No results</div>';
        list.style.display = 'block';
        return;
    }
    items.forEach(p => {
        const d = document.createElement('div');
        d.style.cssText = 'padding:5px 10px;cursor:pointer;border-bottom:1px solid #f0f0f0;';
        d.textContent = p.name || p.text || '';
        d.dataset.id = p.id;
        d.dataset.name = p.name || p.text || '';
        d.addEventListener('mousedown', ev => { ev.preventDefault(); _partySelect(d); });
        list.appendChild(d);
    });
    list.style.display = 'block';
    _partyHilIdx = -1;
}

function _partyFilter() {
    const q = document.getElementById('partySearchInput').value.toLowerCase();
    const filtered = q ? _partyAllItems.filter(p => (p.name||p.text||'').toLowerCase().includes(q)) : _partyAllItems;
    _partyBuildDrop(filtered);
}

function _partyShowDrop() {
    if (!_partyAllItems.length) loadPartyList();
    _partyBuildDrop(_partyAllItems);
}

function _partyHideDrop() {
    document.getElementById('partyDropList').style.display = 'none';
}

function _partyHilAt(idx) {
    const items = Array.from(document.querySelectorAll('#partyDropList div[data-id]'));
    items.forEach(i => i.style.background = '');
    if (idx < 0) idx = 0;
    if (idx >= items.length) idx = items.length - 1;
    _partyHilIdx = idx;
    if (items[idx]) { items[idx].style.background = '#0d6efd'; items[idx].style.color = '#fff'; items[idx].scrollIntoView({block:'nearest'}); }
}

function _partySelect(el) {
    document.getElementById('party_id').value = el.dataset.id;
    document.getElementById('party_name').value = el.dataset.name;
    document.getElementById('partySearchInput').value = el.dataset.name;
    _partyHideDrop();
    document.getElementById('remarks')?.focus();
}

// Keyboard nav for party search
window.addEventListener('keydown', function(e) {
    if (document.activeElement?.id !== 'partySearchInput') return;
    const MANAGED = ['ArrowDown','ArrowUp','Enter','Escape','Tab'];
    if (!MANAGED.includes(e.key)) return;
    const list = document.getElementById('partyDropList');
    const isOpen = list.style.display === 'block';

    if (e.key === 'Escape') { e.preventDefault(); _partyHideDrop(); return; }
    if (e.key === 'Tab')    { _partyHideDrop(); return; }

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (!isOpen) _partyShowDrop();
        const items = document.querySelectorAll('#partyDropList div[data-id]');
        if (_partyHilIdx < items.length - 1) _partyHilAt(_partyHilIdx + 1);
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (_partyHilIdx > 0) _partyHilAt(_partyHilIdx - 1);
        return;
    }
    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        const items = Array.from(document.querySelectorAll('#partyDropList div[data-id]'));
        if (_partyHilIdx >= 0 && items[_partyHilIdx]) {
            _partySelect(items[_partyHilIdx]);
        } else if (items.length > 0) {
            _partySelect(items[0]);
        } else {
            _partyHideDrop();
            document.getElementById('remarks')?.focus();
        }
        return;
    }
}, true);

// Close party drop on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#partyDropList') && e.target.id !== 'partySearchInput') _partyHideDrop();
});



// ============ REUSABLE MODAL BRIDGE FUNCTION ============
// This function is called by the reusable modal components
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Received: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Received: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemData.id;
    row.dataset.itemData = JSON.stringify(itemData);
    if (batchData && batchData.id) {
        row.dataset.batchId = batchData.id;
        row.dataset.batchData = JSON.stringify(batchData);
    }
    row.onclick = function() { selectRow(rowIndex); };
    
    // Complete row HTML with all fields
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || '1'}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    // Update footer with item/batch details
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('cl_qty').value = batchData?.qty || 0;
    
    console.log('✅ Sample Received: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
    
    // Focus on qty field
    row.querySelector('input[name*="[qty]"]')?.focus();
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Received: showItemSelectionModal called - opening reusable modal');
    
    // Check if modal functions exist
    if (typeof window.openItemModal_sampleReceivedItemModal === 'function') {
        window.openItemModal_sampleReceivedItemModal();
    } else {
        console.error('❌ Sample Received: openItemModal_sampleReceivedItemModal function not found. Modal component may not be loaded.');
        alert('Error: Modal component not loaded. Please refresh the page.');
    }
}

// ============ LEGACY ITEM SELECTION MODAL (RENAMED TO AVOID CONFLICT) ============
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
                                <th>S.Rate</th>
                                <th>MRP</th>
                            </tr>
                        </thead>
                        <tbody id="itemsListBody">`;
    
    itemsData.forEach(item => {
        html += `
            <tr class="item-row" onclick="_legacy_selectItemFromModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                <td><strong>${item.id || ''}</strong></td>
                <td>${item.name || ''}</td>
                <td>${item.packing || ''}</td>
                <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
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

function _legacy_selectItemFromModal(item) {
    closeItemModal();
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(item.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || '1'}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    _legacy_showBatchSelectionForItem(item, rowIndex);
}

function _legacy_showBatchSelectionForItem(item, rowIndex) {
    fetch(`<?php echo e(url('admin/api/item-batches')); ?>/${item.id}`)
        .then(response => response.json())
        .then(data => {
            const batches = data.batches || data || [];
            _legacy_showBatchSelectionModal(Array.isArray(batches) ? batches : [], rowIndex, item);
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
            _legacy_showBatchSelectionModal([], rowIndex, item);
        });
}

function _legacy_showBatchSelectionModal(batches, rowIndex, itemData) {
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Select Batch for Sample Receive</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2" style="background: #f8f9fa; border-radius: 5px;">
                    <div>
                        <strong>ITEM:</strong> <span style="color: #6f42c1; font-weight: bold;">${itemData.name || ''}</span>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm" onclick="_legacy_skipBatchSelection(${rowIndex})">
                        <i class="bi bi-skip-forward me-1"></i> Skip (No Batch)
                    </button>
                </div>`;
    
    if (batches.length > 0) {
        html += `
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px;">
                        <thead style="background: #90EE90;">
                            <tr>
                                <th>BATCH</th>
                                <th>S.RATE</th>
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
                <tr style="cursor: pointer;">
                    <td><strong>${batch.batch_no || ''}</strong></td>
                    <td class="text-end">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                    <td class="text-end">${batch.qty || 0}</td>
                    <td>${expiry}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick='_legacy_selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
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
                    <p class="mb-0 mt-2"><strong>No batches found for this item.</strong></p>
                    <p class="text-muted small">Click "Skip" to continue without batch selection.</p>
                </div>`;
    }
    
    html += `</div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

function _legacy_skipBatchSelection(rowIndex) {
    closeBatchModal();
    const row = document.getElementById(`row-${rowIndex}`);
    row?.querySelector('input[name*="[qty]"]')?.focus();
}

function _legacy_selectBatchFromModal(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
    if (batch.expiry_date) {
        const d = new Date(batch.expiry_date);
        row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }
    row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || 0).toFixed(2);
    row.querySelector('input[name*="[batch_id]"]').value = batch.id || '';
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    document.getElementById('cl_qty').value = batch.qty || 0;
    
    closeBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

// ============ KEYBOARD NAVIGATION ============
// Transport field handler - Opens Add Items modal on Enter
function handleTransportKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        // Open Add Items modal
        showItemSelectionModal();
    }
}

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            // Shift+Enter: Go back to transport field
            document.getElementById('transport')?.focus();
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[expiry]"]')?.focus();
    }
}

function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            // Shift+Enter: Go back to batch field
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[batch]"]')?.focus();
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key !== 'Enter' && event.key !== 'Tab') return;

    const qtyInput = event.target;
    const qty = parseFloat(qtyInput?.value) || 0;
    if (qty <= 0) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        qtyInput?.focus();
        qtyInput?.select();
        return;
    }

    // Keep native Tab behavior when qty is valid.
    if (event.key === 'Tab') return;

    event.preventDefault();
    if (event.shiftKey) {
        // Shift+Enter: Go back to expiry field
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[expiry]"]')?.focus();
        return;
    }
    calculateRowAmount(rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    row?.querySelector('input[name*="[rate]"]')?.focus();
}

function handleRateKeydown(event, rowIndex) {
    console.log('🎹 Rate keydown:', event.key, 'Row:', rowIndex);
    if (event.key === 'Enter') {
        event.preventDefault();
        console.log('✅ Enter pressed on Rate field');
        if (event.shiftKey) {
            // Shift+Enter: Go back to qty field
            console.log('⬅️ Shift+Enter: Going back to Qty');
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[qty]"]')?.focus();
            return;
        }
        console.log('➡️ Forward navigation from Rate');
        calculateRowAmount(rowIndex);
        completeRow(rowIndex);
        // Check if next row exists
        const currentRow = document.getElementById(`row-${rowIndex}`);
        const nextRow = currentRow ? currentRow.nextElementSibling : null;
        console.log('🔍 Next row check:', nextRow ? 'Found' : 'Not found');
        if (nextRow && nextRow.id && nextRow.id.startsWith('row-')) {
            const nextRowIdx = parseInt(nextRow.id.replace('row-', ''));
            console.log('➡️ Moving to next row:', nextRowIdx);
            selectRow(nextRowIdx);
            const nextQty = nextRow.querySelector('input[name*="[qty]"]');
            if (nextQty) { 
                console.log('✅ Focusing next row Qty');
                nextQty.focus(); 
                nextQty.select(); 
                return; 
            }
        }
        // No next row - trigger Add Items
        console.log('🎯 No next row - Opening Add Items modal');
        showItemSelectionModal();
    }
}

function completeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.remove('row-selected');
        row.classList.add('row-complete');
        calculateTotalAmount();
        selectedRowIndex = null;
    }
}

function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="">
        <input type="hidden" name="items[${rowIndex}][unit]" value="">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
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

function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code);
    if (item) {
        fillRowWithItem(rowIndex, item);
    }
}

function fillRowWithItem(rowIndex, item) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[code]"]').value = item.id || '';
    row.querySelector('input[name*="[name]"]').value = item.name || '';
    row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
    row.querySelector('input[name*="[item_id]"]').value = item.id;
    row.querySelector('input[name*="[packing]"]').value = item.packing || '';
    row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
    row.querySelector('input[name*="[hsn_code]"]').value = item.hsn_code || '';
    row.querySelector('input[name*="[mrp]"]').value = item.mrp || 0;
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id;
    
    updateFooterFromRow(row);
    _legacy_showBatchSelectionForItem(item, rowIndex);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => {
        r.classList.remove('row-selected');
    });
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('cl_qty').value = batchData.qty || itemData.qty || '0';
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
    const amount = qty * rate;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    let totalQty = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        total += amount;
        totalQty += qty;
    });
    
    document.getElementById('net_amount').value = total.toFixed(2);
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
    } else {
        alert('Please select an item to delete');
    }
}

function saveTransaction() {
    const form = document.getElementById('srForm');
    const formData = new FormData(form);
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    let totalQty = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        totalQty += qty;
    });
    formData.append('total_qty', totalQty);
    formData.append('total_amount', document.getElementById('net_amount').value);
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.sample-received.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + '\nTRN No: ' + data.trn_no);
            window.location.href = '<?php echo e(route("admin.sample-received.index")); ?>';
        } else {
            alert(data.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving transaction');
    });
}

function cancelSampleReceive() {
    if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
        window.location.href = '<?php echo e(route("admin.sample-received.index")); ?>';
    }
}

// ====== GLOBAL KEYBOARD NAVIGATION ======
window.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === 'Tab') {
        const activeQty = document.activeElement;
        if (activeQty && activeQty.name && activeQty.name.includes('[qty]')) {
            const hasModalOpenForQty = document.getElementById('itemModal') || document.getElementById('batchModal') ||
                document.querySelector('#sampleReceivedItemModal.show') || document.querySelector('#sampleReceivedBatchModal.show');

            if (!hasModalOpenForQty) {
                const qtyVal = parseFloat(activeQty.value);
                if (!(activeQty.value || '').trim() || !Number.isFinite(qtyVal) || qtyVal <= 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    activeQty.focus();
                    activeQty.select();
                    return false;
                }
            }
        }
    }

    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;

        // Skip if modal is open
        const hasModalOpen = document.getElementById('itemModal') || document.getElementById('batchModal') ||
            document.querySelector('#sampleReceivedItemModal.show') || document.querySelector('#sampleReceivedBatchModal.show');
        if (hasModalOpen) return;

        // Skip if inside items table - let row handlers take care of it
        if (activeEl.closest('#itemsTableBody')) return;

        // custom dropdowns handled by their own window listeners
        if (activeEl.id === 'partySearchInput' || activeEl.id === 'partyTypeSearchInput') return;

        // Ctrl+Enter → Srlno field
        if (e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('srlno')?.focus();
            document.getElementById('srlno')?.select();
            return false;
        }

        // Shift+Enter backward navigation
        if (e.shiftKey && !e.ctrlKey) {
            const backMap = {
                'partyTypeSearchInput': 'transaction_date',
                'remarks': 'partySearchInput',
                'on_field': 'remarks',
                'rate': 'on_field',
                'tag': 'rate',
                'gr_no': 'tag',
                'gr_date': 'gr_no',
                'cases': 'gr_date',
                'road_permit_no': 'cases',
                'truck_no': 'road_permit_no',
                'transport': 'truck_no'
            };
            if (backMap[activeEl.id]) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById(backMap[activeEl.id])?.focus();
                return false;
            }
            return;
        }

        // Date → Party Type dropdown
        if (activeEl.id === 'transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('partyTypeSearchInput')?.focus();
            return false;
        }
        // Party Type button → Party Name search input
        if (activeEl.classList.contains('party-type-btn')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('partySearchInput')?.focus();
            return false;
        }
        // Remarks → On
        if (activeEl.id === 'remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('on_field')?.focus();
            return false;
        }
        // On → Rate
        if (activeEl.id === 'on_field') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('rate')?.focus();
            return false;
        }
        // Rate → Tag
        if (activeEl.id === 'rate') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('tag')?.focus();
            return false;
        }
        // Tag → GR No
        if (activeEl.id === 'tag') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('gr_no')?.focus();
            return false;
        }
        // GR No → GR Date
        if (activeEl.id === 'gr_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('gr_date')?.focus();
            return false;
        }
        // GR Date → Cases
        if (activeEl.id === 'gr_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('cases')?.focus();
            return false;
        }
        // Cases → Road Permit
        if (activeEl.id === 'cases') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('road_permit_no')?.focus();
            return false;
        }
        // Road Permit → Truck No
        if (activeEl.id === 'road_permit_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('truck_no')?.focus();
            return false;
        }
        // Truck No → Transport
        if (activeEl.id === 'truck_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('transport')?.focus();
            return false;
        }
        // Transport → first row Qty (if items exist) OR Add Items
        if (activeEl.id === 'transport') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const firstRow = document.querySelector('#itemsTableBody tr');
            if (firstRow) {
                const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
                if (qtyInput) {
                    const rowIdx = parseInt(firstRow.dataset.rowIndex || firstRow.id.replace('row-', ''));
                    selectRow(rowIdx);
                    qtyInput.focus();
                    qtyInput.select();
                    return false;
                }
            }
            // No items - open Add Items modal
            showItemSelectionModal();
            return false;
        }
    }

    // Ctrl+S save
    if ((e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey) && !e.shiftKey) {
        e.preventDefault();
        saveTransaction();
        return false;
    }
}, true);
</script>

<script>
// ============================================================
// AUTO-SAVE  —  sample_received_transaction_autosave_v1
// ============================================================
(function(){
'use strict';
const KEY = 'sample_received_transaction_autosave_v1';
let _t = null;

function _val(id){ const el=document.getElementById(id); return el?el.value:''; }
function _set(id,v){ const el=document.getElementById(id); if(el) el.value=v; }
function _esc(v){ if(v===undefined||v===null)return''; return String(v).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function save(){
    const rows=[];
    document.querySelectorAll('#itemsTableBody tr').forEach(function(tr){
        const ri=tr.dataset.rowIndex;
        if(ri===undefined||ri===null||ri==='') return;
        const g=function(n){ const el=tr.querySelector('[name="items['+ri+']['+n+']"]'); return el?el.value:''; };
        if(!g('code')&&!g('name')) return;
        rows.push({
            ri:ri,
            itemId:tr.dataset.itemId||'', itemData:tr.dataset.itemData||'{}',
            batchId:tr.dataset.batchId||'', batchData:tr.dataset.batchData||'{}',
            code:g('code'), name:g('name'), batch:g('batch'), expiry:g('expiry'),
            qty:g('qty'), rate:g('rate'), amount:g('amount'),
            item_id:g('item_id'), batch_id:g('batch_id'), packing:g('packing'),
            unit:g('unit'), company_name:g('company_name'), hsn_code:g('hsn_code'), mrp:g('mrp'),
        });
    });

    const state={
        savedAt:new Date().toISOString(),
        transaction_date:_val('transaction_date'),
        party_type:_val('party_type'),
        partyTypeSearchInput:_val('partyTypeSearchInput'),
        party_id:_val('party_id'),
        party_name:_val('party_name'),
        partySearchInput:_val('partySearchInput'),
        remarks:_val('remarks'),
        on_field:_val('on_field'),
        rate:_val('rate'),
        tag:_val('tag'),
        gr_no:_val('gr_no'),
        gr_date:_val('gr_date'),
        cases:_val('cases'),
        road_permit_no:_val('road_permit_no'),
        truck_no:_val('truck_no'),
        transport:_val('transport'),
        srlno:_val('srlno'),
        rows:rows,
    };
    if(!state.party_id && !rows.length) return;
    try{ localStorage.setItem(KEY,JSON.stringify(state)); }catch(e){}
    _badge();
}
function _sched(){ clearTimeout(_t); _t=setTimeout(save,700); }

function restore(){
    let state; try{ const r=localStorage.getItem(KEY); if(!r)return; state=JSON.parse(r); }catch(e){return;}
    if(!state||(!state.party_id&&!state.rows.length)) return;
    _banner(state.savedAt, function keep(){
        if(state.transaction_date) _set('transaction_date',state.transaction_date);
        if(typeof updateDayName==='function') updateDayName();
        _set('party_type',state.party_type||'');
        const ptd=document.getElementById('partyTypeSearchInput'); if(ptd) ptd.value=state.partyTypeSearchInput||'';
        _set('party_id',state.party_id||'');
        _set('party_name',state.party_name||'');
        const psd=document.getElementById('partySearchInput'); if(psd) psd.value=state.partySearchInput||'';
        _set('remarks',state.remarks||'');
        _set('on_field',state.on_field||'');
        _set('rate',state.rate||'0');
        _set('tag',state.tag||'');
        _set('gr_no',state.gr_no||'');
        if(state.gr_date) _set('gr_date',state.gr_date);
        _set('cases',state.cases||'0');
        _set('road_permit_no',state.road_permit_no||'');
        _set('truck_no',state.truck_no||'');
        _set('transport',state.transport||'');
        _set('srlno',state.srlno||'');

        const tbody=document.getElementById('itemsTableBody');
        if(tbody) tbody.innerHTML='';
        if(typeof currentRowIndex!=='undefined') window.currentRowIndex=0;

        (state.rows||[]).forEach(function(saved){
            const ri=saved.ri;
            if(typeof currentRowIndex!=='undefined'&&parseInt(ri)>=currentRowIndex) window.currentRowIndex=parseInt(ri)+1;
            const tr=document.createElement('tr');
            tr.id='row-'+ri;
            tr.dataset.rowIndex=ri;
            tr.dataset.itemId=saved.itemId||''; tr.dataset.itemData=saved.itemData||'{}';
            tr.dataset.batchId=saved.batchId||''; tr.dataset.batchData=saved.batchData||'{}';
            tr.onclick=function(){ if(typeof selectRow==='function') selectRow(parseInt(ri)); };
            tr.innerHTML=
                '<td><input type="text" class="form-control form-control-sm" name="items['+ri+'][code]" value="'+_esc(saved.code)+'" readonly></td>'+
                '<td><input type="text" class="form-control form-control-sm" name="items['+ri+'][name]" value="'+_esc(saved.name)+'" readonly></td>'+
                '<td><input type="text" class="form-control form-control-sm" name="items['+ri+'][batch]" value="'+_esc(saved.batch)+'" onkeydown="handleBatchKeydown(event,'+ri+')" data-custom-enter></td>'+
                '<td><input type="text" class="form-control form-control-sm" name="items['+ri+'][expiry]" value="'+_esc(saved.expiry)+'" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event,'+ri+')" data-custom-enter></td>'+
                '<td><input type="number" class="form-control form-control-sm" name="items['+ri+'][qty]" value="'+_esc(saved.qty||'')+'" step="1" min="1" onchange="calculateRowAmount('+ri+')" onkeydown="handleQtyKeydown(event,'+ri+')" data-custom-enter></td>'+
                '<td><input type="number" class="form-control form-control-sm" name="items['+ri+'][rate]" value="'+parseFloat(saved.rate||0).toFixed(2)+'" step="0.01" onchange="calculateRowAmount('+ri+')" onkeydown="handleRateKeydown(event,'+ri+')" data-custom-enter></td>'+
                '<td><input type="number" class="form-control form-control-sm readonly-field" name="items['+ri+'][amount]" value="'+_esc(saved.amount||'')+'" step="0.01" readonly></td>'+
                '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow('+ri+')"><i class="bi bi-x"></i></button></td>'+
                '<input type="hidden" name="items['+ri+'][item_id]" value="'+_esc(saved.item_id)+'">'+
                '<input type="hidden" name="items['+ri+'][batch_id]" value="'+_esc(saved.batch_id)+'">'+
                '<input type="hidden" name="items['+ri+'][packing]" value="'+_esc(saved.packing)+'">'+
                '<input type="hidden" name="items['+ri+'][unit]" value="'+_esc(saved.unit)+'">'+
                '<input type="hidden" name="items['+ri+'][company_name]" value="'+_esc(saved.company_name)+'">'+
                '<input type="hidden" name="items['+ri+'][hsn_code]" value="'+_esc(saved.hsn_code)+'">'+
                '<input type="hidden" name="items['+ri+'][mrp]" value="'+_esc(saved.mrp||0)+'">';
            if(tbody) tbody.appendChild(tr);
        });

        if(typeof calculateTotals==='function') calculateTotals();
        else if(typeof calculateTotal==='function') calculateTotal();
    }, function discard(){ clearAutoSave(); });
}

window.clearAutoSave=function(){ try{ localStorage.removeItem(KEY); }catch(e){} };

function _badge(){
    let b=document.getElementById('_asBadge');
    if(!b){ b=document.createElement('div'); b.id='_asBadge';
      b.style.cssText='position:fixed;bottom:18px;right:18px;background:#198754;color:#fff;padding:5px 12px;border-radius:20px;font-size:11px;z-index:9999;opacity:0;transition:opacity 0.3s;pointer-events:none;';
      document.body.appendChild(b); }
    b.textContent='\u2713 Draft saved'; b.style.opacity='1';
    setTimeout(function(){ b.style.opacity='0'; },2200);
}
function _banner(savedAt,onKeep,onDiscard){
    const old=document.getElementById('_asBanner'); if(old) old.remove();
    const t=savedAt?new Date(savedAt).toLocaleTimeString():'';
    const d=document.createElement('div'); d.id='_asBanner';
    d.style.cssText='position:fixed;top:10px;left:calc(240px + 50%);transform:translateX(-50%);background:#fff3cd;border:1px solid #ffc107;padding:8px 16px;border-radius:6px;z-index:9999;display:flex;align-items:center;gap:10px;font-size:12px;box-shadow:0 2px 8px rgba(0,0,0,0.15);';
    d.innerHTML='<span>\uD83D\uDCCB Unsaved draft restored'+(t?' ('+t+')':'')+' </span>'+
        '<button id="_asKeep" style="background:#198754;color:#fff;border:none;padding:3px 10px;border-radius:4px;cursor:pointer;font-size:11px;">Keep</button>'+
        '<button id="_asDiscard" style="background:#dc3545;color:#fff;border:none;padding:3px 10px;border-radius:4px;cursor:pointer;font-size:11px;">Discard</button>';
    document.body.appendChild(d);
    let done=false;
    function dismiss(){ if(done)return; done=true; d.remove(); }
    document.getElementById('_asKeep').onclick=function(){ dismiss(); if(onKeep) onKeep(); };
    document.getElementById('_asDiscard').onclick=function(){ dismiss(); if(onDiscard) onDiscard(); };
    setTimeout(function(){ if(!done){ dismiss(); if(onKeep) onKeep(); } },12000);
}

document.addEventListener('DOMContentLoaded',function(){
    setTimeout(function(){
        const _origMark=(typeof window.markAsSaving==='function')?window.markAsSaving:null;
        window.markAsSaving=function(){ clearAutoSave(); if(_origMark) _origMark.apply(this,arguments); };
    },800);
    setTimeout(restore,900);
    const form=document.getElementById('srForm');
    if(form){ form.addEventListener('input',_sched); form.addEventListener('change',_sched); }
    const tbody=document.getElementById('itemsTableBody');
    if(tbody) new MutationObserver(_sched).observe(tbody,{childList:true,subtree:true});
});
})();
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sample-received/transaction.blade.php ENDPATH**/ ?>