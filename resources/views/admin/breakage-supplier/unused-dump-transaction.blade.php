@extends('layouts.admin')

@section('title', 'Breakage/Expiry Dump - Transaction')
@section('disable_select2', '1')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .readonly-field { background-color: #e9ecef !important; }
    
    /* Table Styles - Brown Header */
    .items-table { font-size: 11px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .items-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 4px 3px; border: 1px solid #5a3a1a; white-space: nowrap; font-size: 11px; }
    .items-table td { padding: 3px; border: 1px solid #ccc; background: #fffacd; }
    .items-table input, .items-table select { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; }
    .items-table .row-selected td { background: #cce5ff !important; }
    
    /* Summary Row - Pink */
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .summary-section label { font-weight: bold; font-size: 11px; }
    .summary-section input { height: 24px; font-size: 11px; }
    
    /* Footer Section - Gray */
    .footer-section { background: #d4d4d4; padding: 8px; border: 1px solid #999; border-radius: 3px; }
    .footer-section label { font-size: 10px; margin-bottom: 1px; }
    .footer-section input { height: 22px; font-size: 10px; }
    
    /* First Footer Section - Purple */
    .first-footer-section { background: #e6d9f5; padding: 8px; border: 1px solid #b399d9; border-radius: 3px; }
    .first-footer-section label { font-size: 10px; margin-bottom: 1px; }
    .first-footer-section input { height: 22px; font-size: 10px; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .action-buttons .btn { min-width: 100px; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-trash me-1"></i> Breakage/Expiry Dump - Transaction</h6>
        <a href="{{ route('admin.breakage-supplier.unused-dump-modification') }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-list"></i> View All</a>
    </div>

    <form id="dumpForm" autocomplete="off">
        @csrf
        <!-- Header Section -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group"><label style="width:40px;">Date:</label><input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()"></div>
                    <div class="field-group"><label style="width:40px;"></label><input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width:85px;"></div>
                    <div class="field-group"><label style="width:40px;">Trn.No:</label><input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $trnNo }}" readonly style="width:60px;"></div>
                </div>
                <div class="col-md-10">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">Narration</label>
                            <textarea id="narration" name="narration" class="form-control form-control-sm" rows="3" placeholder="Enter narration or remarks..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <div class="table-responsive" style="overflow-x: auto; overflow-y: auto;">
                <table class="items-table" id="itemsTable" style="table-layout: fixed; width: 100%; min-width: 900px;">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 50px;">Code</th>
                            <th style="width: 150px;">Item Name</th>
                            <th style="width: 70px;">Batch</th>
                            <th style="width: 60px;">Exp</th>
                            <th style="width: 45px;">Qty</th>
                            <th style="width: 45px;">F.Q</th>
                            <th style="width: 60px;">Rate</th>
                            <th style="width: 50px;">Dis%</th>
                            <th style="width: 50px;">Scm%</th>
                            <th style="width: 55px;">Br/Ex</th>
                            <th style="width: 70px;">Amount</th>
                            <th style="width: 30px;">X</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody"></tbody>
                </table>
            </div>
            <div class="text-center mt-2 border-top pt-2">
                <button type="button" class="btn btn-link text-decoration-none fw-bold text-primary p-0" onclick="showItemModal()">
                    <i class="bi bi-plus-circle me-1"></i> Add Item (F2)
                </button>
            </div>
        </div>

        <!-- Section 2 - Gray (SC%, EXCISE, TAX%, etc.) -->
        <div class="footer-section mb-2">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-1 me-2"><label>SC %</label><input type="number" id="footer_sc_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>EXCISE</label><input type="number" id="footer_excise" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>TAX %</label><input type="number" id="footer_tax_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>HSN</label><input type="text" id="footer_hsn" class="form-control readonly-field" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-3"><label>Pack</label><input type="text" id="footer_pack2" class="form-control readonly-field" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-1"><label>Disallow</label><input type="text" id="footer_disallow" class="form-control readonly-field text-center" value="N" readonly style="width:30px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>MRP</label><input type="number" id="footer_mrp" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
            </div>
            <div class="d-flex align-items-center mt-1">
                <div class="d-flex align-items-center gap-1 me-2"><label>P.RATE</label><input type="number" id="footer_prate" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label>S.RATE</label><input type="number" id="footer_srate" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
            </div>
        </div>

        <!-- Section 1 - Pink Summary (N.T AMT, SC, DIS. AMT, etc.) -->
        <div class="summary-section mb-2">
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">N.T AMT</label><input type="number" id="total_nt_amt" name="total_nt_amt" class="form-control readonly-field text-end fw-bold" readonly style="width:90px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">SC</label><input type="number" id="total_sc" name="total_sc" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">DIS. AMT</label><input type="number" id="total_dis_amt" name="total_dis_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Scm. AMT</label><input type="number" id="total_scm_amt" name="total_scm_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Half.Scm.</label><input type="number" id="total_half_scm" name="total_half_scm" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Tax</label><input type="number" id="total_tax" name="total_tax" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">NET LOSS</label><input type="number" id="total_inv_amt" name="total_inv_amt" class="form-control text-end fw-bold" readonly style="width:100px;"></div>
            </div>
        </div>

        <!-- Section 3 - Purple Header (Pack, Comp, N.T Amt, etc.) -->
        <div class="first-footer-section mb-2">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-1 me-2"><label>Pack</label><input type="text" id="footer_pack" class="form-control readonly-field" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Comp :</label><input type="text" id="footer_comp" class="form-control readonly-field" readonly style="width:100px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>N.T Amt.</label><input type="number" id="footer_nt_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>DIS. Amt.</label><input type="number" id="footer_dis_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Net Amt.</label><input type="number" id="footer_net_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>P.Scm.</label><input type="number" id="footer_pscm" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>S.Scm.</label><input type="number" id="footer_sscm" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                <div class="d-flex align-items-center gap-1"><label>+</label></div>
            </div>
            <div class="d-flex align-items-center mt-1">
                <div class="d-flex align-items-center gap-1 me-2"><label>Unit</label><input type="text" id="footer_unit" class="form-control readonly-field" readonly style="width:50px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Bal.</label><input type="number" id="footer_bal" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Srlno.</label><input type="text" id="footer_srlno" class="form-control readonly-field" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Half Scm.</label><input type="number" id="footer_half_scm" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Scm.Amt.</label><input type="number" id="footer_scm_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Tax Amt.</label><input type="number" id="footer_tax_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label>+</label></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-success btn-sm" onclick="saveTransaction()"><i class="bi bi-check-lg me-1"></i> Save (End)</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()"><i class="bi bi-trash me-1"></i> Delete Item</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelTransaction()"><i class="bi bi-x-lg me-1"></i> Cancel</button>
        </div>
    </form>
</div>

<!-- Item Selection Modal -->
<div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="_legacy_closeItemModal()"></div>
<div class="custom-modal" id="itemModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-search me-1"></i> Select Item</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="_legacy_closeItemModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="itemSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by code or name..." onkeyup="_legacy_filterItems()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top"><tr><th>Code</th><th>Item Name</th><th>Pack</th><th>Company</th></tr></thead>
                <tbody id="itemsListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
    </div>
</div>

<!-- Batch Selection Modal -->
<div class="modal-backdrop-custom" id="batchModalBackdrop" onclick="_legacy_closeBatchModal()"></div>
<div class="custom-modal" id="batchModal">
    <div class="modal-header-custom batch">
        <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch</h6>
        <button type="button" class="btn btn-sm btn-dark" onclick="_legacy_closeBatchModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <div class="mb-2 p-2 bg-light rounded"><strong id="selectedItemName">-</strong></div>
        <div class="table-responsive" style="max-height: 280px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-warning sticky-top"><tr><th>Batch</th><th>Expiry</th><th>Qty</th><th>MRP</th><th>P.Rate</th><th>S.Rate</th></tr></thead>
                <tbody id="batchesListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeBatchModal()">Close</button>
    </div>
</div>
@endsection

<!-- Reusable Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'breakageSupplierDumpItemModal',
    'module' => 'breakage-supplier',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'breakageSupplierDumpBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'breakageSupplierDumpBatchModal',
    'module' => 'breakage-supplier',
    'showOnlyAvailable' => true,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@push('styles')
<style>
    /* Modal Styles */
    .modal-backdrop-custom { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; background: #fff; border-radius: 6px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1055; }
    .custom-modal.show { display: block; }
    .modal-header-custom { padding: 10px 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-header-custom.batch { background: #ffc107; color: #000; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .item-row:hover, .batch-row:hover { background: #e3f2fd !important; cursor: pointer; }
    .item-row.selected, .batch-row.selected { background: #007bff !important; color: #fff !important; }
</style>
@endpush

@push('scripts')
<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null, selectedItem = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    initHeaderFieldNavigation();
    initGlobalKeyboardShortcuts();
    
    // Focus date field on load
    setTimeout(() => {
        const dateField = document.getElementById('transaction_date');
        if (dateField) { dateField.focus(); }
    }, 200);
});

function updateDayName() {
    const d = new Date(document.getElementById('transaction_date').value);
    document.getElementById('day_name').value = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
}

function loadItems() {
    fetch('{{ route("admin.breakage-supplier.get-items") }}')
        .then(r => r.json())
        .then(data => { allItems = data || []; })
        .catch(e => console.error('Error loading items:', e));
}

// ============================================
// Header Field Navigation
// ============================================
function initHeaderFieldNavigation() {
    const headerFields = ['transaction_date', 'narration'];
    
    // Block Tab on header fields
    headerFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') { e.preventDefault(); }
            });
        }
    });
    
    // Date → Enter → focus narration
    const dateField = document.getElementById('transaction_date');
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                const narrationField = document.getElementById('narration');
                if (narrationField) { narrationField.focus(); }
            }
        });
    }
    
    // Narration → Enter → trigger Add Item (F2)
    const narrationField = document.getElementById('narration');
    if (narrationField) {
        narrationField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                e.stopPropagation();
                showItemModal();
            }
        });
    }
}

// ============================================
// Global Keyboard Shortcuts
// ============================================
function initGlobalKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // F2 - Add Item
        if (e.key === 'F2') { e.preventDefault(); showItemModal(); }
        
        // Escape - Close Modals
        if (e.key === 'Escape') { closeItemModal(); closeBatchModal(); }
        
        // Ctrl+S - Save Transaction
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            e.stopPropagation();
            saveTransaction();
        }
    });
}

// ============================================
// Item Modal Bridge
// ============================================
function showItemModal() {
    if (typeof openItemModal_breakageSupplierDumpItemModal === 'function') {
        openItemModal_breakageSupplierDumpItemModal();
    } else {
        _legacy_showItemModal();
    }
}

function closeItemModal() {
    if (typeof closeItemModal_breakageSupplierDumpItemModal === 'function') {
        closeItemModal_breakageSupplierDumpItemModal();
    } else {
        _legacy_closeItemModal();
    }
}

function closeBatchModal() {
    if (typeof closeModal_breakageSupplierDumpBatchModal === 'function') {
        closeModal_breakageSupplierDumpBatchModal();
    } else {
        _legacy_closeBatchModal();
    }
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    const transformedItem = {
        id: item.id,
        item_code: item.bar_code || item.code || '',
        item_name: item.name || '',
        packing: item.packing || '',
        company_name: item.company_name || '',
        hsn_code: item.hsn_code || '',
        unit: item.unit || '',
        cgst: item.cgst_percent || 0,
        sgst: item.sgst_percent || 0
    };
    
    const transformedBatch = {
        id: batch.id,
        batch_no: batch.batch_no || '',
        expiry_date: batch.expiry_date || '',
        mrp: batch.mrp || 0,
        purchase_rate: batch.p_rate || batch.pur_rate || batch.purchase_rate || 0,
        sale_rate: batch.s_rate || batch.sale_rate || 0,
        quantity: batch.qty || batch.quantity || 0
    };
    
    addItemRow(transformedItem, transformedBatch);
};

// ============================================
// Legacy Item/Batch Modal Functions
// ============================================
function _legacy_showItemModal() {
    document.getElementById('itemModalBackdrop').classList.add('show');
    document.getElementById('itemModal').classList.add('show');
    document.getElementById('itemSearchInput').value = '';
    _legacy_renderItemsList(allItems);
    setTimeout(() => document.getElementById('itemSearchInput').focus(), 100);
}

function _legacy_closeItemModal() {
    document.getElementById('itemModalBackdrop').classList.remove('show');
    document.getElementById('itemModal').classList.remove('show');
}

function _legacy_filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = allItems.filter(item => 
        (item.item_code && item.item_code.toLowerCase().includes(search)) ||
        (item.item_name && item.item_name.toLowerCase().includes(search))
    );
    _legacy_renderItemsList(filtered);
}

function _legacy_renderItemsList(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No items found</td></tr>';
        return;
    }
    tbody.innerHTML = items.slice(0, 100).map(item => `
        <tr class="item-row" onclick="_legacy_selectItem(${item.id})">
            <td>${item.item_code || ''}</td>
            <td>${item.item_name || ''}</td>
            <td>${item.packing || ''}</td>
            <td>${item.company_name || ''}</td>
        </tr>
    `).join('');
}

function _legacy_selectItem(itemId) {
    selectedItem = allItems.find(i => i.id === itemId);
    if (!selectedItem) return;
    _legacy_closeItemModal();
    document.getElementById('selectedItemName').textContent = `${selectedItem.item_code} - ${selectedItem.item_name}`;
    _legacy_loadBatches(itemId);
}

function _legacy_loadBatches(itemId) {
    fetch(`{{ url('admin/breakage-supplier/get-batches') }}/${itemId}`)
        .then(r => r.json())
        .then(batches => {
            document.getElementById('batchModalBackdrop').classList.add('show');
            document.getElementById('batchModal').classList.add('show');
            _legacy_renderBatchesList(batches);
        })
        .catch(e => {
            console.error('Error loading batches:', e);
            addItemRow(selectedItem, null);
        });
}

function _legacy_renderBatchesList(batches) {
    const tbody = document.getElementById('batchesListBody');
    if (!batches.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No batches found</td></tr>';
        return;
    }
    tbody.innerHTML = batches.map(batch => `
        <tr class="batch-row" onclick="_legacy_selectBatch(${JSON.stringify(batch).replace(/"/g, '&quot;')})">
            <td>${batch.batch_no || ''}</td>
            <td>${batch.expiry_date || ''}</td>
            <td class="text-end">${batch.quantity || 0}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.sale_rate || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function _legacy_closeBatchModal() {
    document.getElementById('batchModalBackdrop').classList.remove('show');
    document.getElementById('batchModal').classList.remove('show');
}

function _legacy_selectBatch(batch) {
    _legacy_closeBatchModal();
    addItemRow(selectedItem, batch);
}

// ============================================
// Add Item Row with Keyboard Handlers
// ============================================
function addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const idx = rowIndex++;
    const rate = batch ? parseFloat(batch.purchase_rate || 0) : 0;
    
    const tr = document.createElement('tr');
    tr.id = `row_${idx}`;
    tr.onclick = function() { selectRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'item_code')"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'item_name')"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${batch?.batch_no || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'batch_no')"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${batch?.expiry_date || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'expiry')"></td>
        <td><input type="number" name="items[${idx}][qty]" value="" min="0" class="text-end" 
                   onchange="calculateRowAmount(${idx})" 
                   onkeydown="handleGridEnterKey(event, ${idx}, 'qty')"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="0" min="0" class="text-end"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'free_qty')"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${rate.toFixed(2)}" step="0.01" class="text-end" 
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'rate')"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="0" step="0.01" class="text-end" 
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'dis_percent')"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="0" step="0.01" class="text-end" 
                   data-custom-enter="true"
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'scm_percent')"></td>
        <td style="position:relative;">
            <input type="text" class="form-control text-center br-ex-input" id="br_ex_display_${idx}" 
                   value="Brk" readonly style="cursor:pointer;width:55px;"
                   onfocus="openBrExDropdown(${idx})"
                   onclick="openBrExDropdown(${idx})"
                   onkeydown="handleBrExKeyDown(event, ${idx})">
            <input type="hidden" name="items[${idx}][br_ex]" id="br_ex_${idx}" value="B">
            <div class="br-ex-dropdown" id="br_ex_dropdown_${idx}" style="display:none;position:absolute;top:100%;left:0;z-index:100;background:#fff;border:1px solid #ccc;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,0.15);width:55px;">
                <div class="br-ex-option" data-value="B" onclick="selectBrEx(${idx},'B','Brk')" style="padding:4px 8px;cursor:pointer;font-size:11px;">Brk</div>
                <div class="br-ex-option" data-value="E" onclick="selectBrEx(${idx},'E','Exp')" style="padding:4px 8px;cursor:pointer;font-size:11px;">Exp</div>
            </div>
        </td>
        <td><input type="number" name="items[${idx}][amount]" value="0" step="0.01" class="text-end readonly-field" readonly></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm py-0 px-1" onclick="removeRow(${idx})">&times;</button>
            <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${idx}][batch_id]" value="${batch?.id || ''}">
            <input type="hidden" name="items[${idx}][mrp]" value="${batch?.mrp || 0}">
            <input type="hidden" name="items[${idx}][purchase_rate]" value="${batch?.purchase_rate || 0}">
            <input type="hidden" name="items[${idx}][sale_rate]" value="${batch?.sale_rate || 0}">
            <input type="hidden" name="items[${idx}][cgst]" value="${item.cgst || 0}">
            <input type="hidden" name="items[${idx}][sgst]" value="${item.sgst || 0}">
            <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${idx}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${idx}][unit]" value="${item.unit || ''}">
            <input type="hidden" name="items[${idx}][hsn_code]" value="${item.hsn_code || ''}">
        </td>
    `;
    tbody.appendChild(tr);
    selectRow(idx);
    
    // Focus on qty field after adding row
    setTimeout(() => {
        const qtyInput = tr.querySelector('input[name*="[qty]"]');
        if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
    }, 100);
    
    calculateTotals();
}

// ============================================
// Grid Enter Key Navigation
// ============================================
function handleGridEnterKey(event, rowIndex, currentField) {
    if (event.key !== 'Enter') return;
    
    event.preventDefault();
    event.stopPropagation();
    
    const fieldOrder = [
        'item_code', 'item_name', 'batch_no', 'expiry',
        'qty', 'free_qty', 'rate', 'dis_percent', 'scm_percent', 'br_ex'
    ];
    
    const currentIndex = fieldOrder.indexOf(currentField);
    
    // Shift+Enter: move to PREVIOUS field
    if (event.shiftKey) {
        if (currentIndex > 0) {
            moveToPrevField(rowIndex, fieldOrder[currentIndex - 1]);
        }
        return;
    }
    
    // item_code → open item modal
    if (currentField === 'item_code') { showItemModal(); return; }
    
    // scm_percent → calculate, then open item modal for NEXT item
    if (currentField === 'scm_percent') {
        calculateRowAmount(rowIndex);
        calculateTotals();
        setTimeout(() => { showItemModal(); }, 100);
        return;
    }
    
    // br_ex (last field) → handleRowComplete
    if (currentField === 'br_ex') { handleRowComplete(rowIndex); return; }
    
    // Otherwise: move to next field
    if (currentIndex >= 0 && currentIndex < fieldOrder.length - 1) {
        moveToNextField(rowIndex, fieldOrder[currentIndex + 1]);
    }
}

// Handle Row Complete
function handleRowComplete(rowIndex) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    calculateRowAmount(rowIndex);
    calculateTotals();
    setTimeout(() => { showItemModal(); }, 100);
}

// Move to Next Field in Row
function moveToNextField(rowIndex, nextFieldName) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    
    if (nextFieldName === 'br_ex') {
        const brExDisplay = document.getElementById(`br_ex_display_${rowIndex}`);
        if (brExDisplay) {
            setTimeout(() => { brExDisplay.focus(); openBrExDropdown(rowIndex); }, 50);
        }
        return;
    }
    
    const nextField = row.querySelector(`[name*="[${nextFieldName}]"]`);
    if (nextField) {
        setTimeout(() => {
            nextField.focus();
            if (nextField.tagName === 'INPUT' && nextField.type === 'number') { nextField.select(); }
        }, 50);
    }
}

// Move to Previous Field in Row
function moveToPrevField(rowIndex, prevFieldName) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    
    if (prevFieldName === 'br_ex') {
        const brExDisplay = document.getElementById(`br_ex_display_${rowIndex}`);
        if (brExDisplay) {
            setTimeout(() => { brExDisplay.focus(); openBrExDropdown(rowIndex); }, 50);
        }
        return;
    }
    
    const prevField = row.querySelector(`[name*="[${prevFieldName}]"]`);
    if (prevField) {
        setTimeout(() => {
            prevField.focus();
            if (prevField.tagName === 'INPUT' && prevField.type === 'number') { prevField.select(); }
        }, 50);
    }
}

// ============================================
// Br/Ex Custom Dropdown Functions
// ============================================
function openBrExDropdown(idx) {
    closeAllBrExDropdowns();
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    if (dropdown) {
        dropdown.style.display = 'block';
        const currentVal = document.getElementById(`br_ex_${idx}`).value;
        dropdown.querySelectorAll('.br-ex-option').forEach(opt => {
            if (opt.dataset.value === currentVal) {
                opt.style.backgroundColor = '#0d6efd'; opt.style.color = '#fff';
            } else {
                opt.style.backgroundColor = ''; opt.style.color = '';
            }
        });
    }
}

function closeBrExDropdown(idx) {
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    if (dropdown) dropdown.style.display = 'none';
}

function closeAllBrExDropdowns() {
    document.querySelectorAll('.br-ex-dropdown').forEach(d => d.style.display = 'none');
}

function selectBrEx(idx, value, label) {
    document.getElementById(`br_ex_${idx}`).value = value;
    document.getElementById(`br_ex_display_${idx}`).value = label;
    closeBrExDropdown(idx);
    handleRowComplete(idx);
}

function handleBrExKeyDown(event, idx) {
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    const isOpen = dropdown && dropdown.style.display !== 'none';
    
    if (event.key === ' ' || event.key === 'Spacebar') {
        event.preventDefault(); event.stopPropagation(); return;
    }
    
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault(); event.stopPropagation();
        const currentVal = document.getElementById(`br_ex_${idx}`).value;
        if (currentVal === 'B') {
            document.getElementById(`br_ex_${idx}`).value = 'E';
            document.getElementById(`br_ex_display_${idx}`).value = 'Exp';
        } else {
            document.getElementById(`br_ex_${idx}`).value = 'B';
            document.getElementById(`br_ex_display_${idx}`).value = 'Brk';
        }
        if (isOpen) openBrExDropdown(idx);
        return;
    }
    
    if (event.key === 'Enter') {
        event.preventDefault(); event.stopPropagation();
        closeBrExDropdown(idx);
        if (event.shiftKey) { moveToPrevField(idx, 'scm_percent'); }
        else { handleRowComplete(idx); }
        return;
    }
    
    if (event.key === 'Escape') { event.preventDefault(); closeBrExDropdown(idx); return; }
    
    if (event.key.toLowerCase() === 'b') {
        event.preventDefault();
        document.getElementById(`br_ex_${idx}`).value = 'B';
        document.getElementById(`br_ex_display_${idx}`).value = 'Brk';
        if (isOpen) openBrExDropdown(idx);
        return;
    }
    if (event.key.toLowerCase() === 'e') {
        event.preventDefault();
        document.getElementById(`br_ex_${idx}`).value = 'E';
        document.getElementById(`br_ex_display_${idx}`).value = 'Exp';
        if (isOpen) openBrExDropdown(idx);
        return;
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.br-ex-input') && !e.target.closest('.br-ex-dropdown')) {
        closeAllBrExDropdowns();
    }
});

// ============================================
// Row Selection & Footer
// ============================================
function selectRow(idx) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`row_${idx}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = idx;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    const getValue = (name) => row.querySelector(`input[name*="[${name}]"]`)?.value || '';
    
    const cgst = parseFloat(getValue('cgst')) || 0;
    const sgst = parseFloat(getValue('sgst')) || 0;
    const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
    
    document.getElementById('footer_sc_percent').value = scmPercent.toFixed(2);
    document.getElementById('footer_excise').value = '0.00';
    document.getElementById('footer_tax_percent').value = (cgst + sgst).toFixed(2);
    document.getElementById('footer_hsn').value = getValue('hsn_code');
    document.getElementById('footer_pack2').value = getValue('packing');
    document.getElementById('footer_mrp').value = getValue('mrp');
    document.getElementById('footer_prate').value = getValue('purchase_rate');
    document.getElementById('footer_srate').value = getValue('sale_rate');
    
    document.getElementById('footer_pack').value = getValue('packing');
    document.getElementById('footer_comp').value = getValue('company_name');
    document.getElementById('footer_unit').value = getValue('unit');
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
    const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
    
    const ntAmt = qty * rate;
    const disAmt = ntAmt * disPercent / 100;
    const netAmt = ntAmt - disAmt;
    const taxAmt = netAmt * (cgst + sgst) / 100;
    
    document.getElementById('footer_nt_amt').value = ntAmt.toFixed(2);
    document.getElementById('footer_dis_amt').value = disAmt.toFixed(2);
    document.getElementById('footer_net_amt').value = netAmt.toFixed(2);
    document.getElementById('footer_pscm').value = '0.00';
    document.getElementById('footer_sscm').value = '0.00';
    document.getElementById('footer_bal').value = '0';
    document.getElementById('footer_srlno').value = '';
    document.getElementById('footer_half_scm').value = '0.00';
    document.getElementById('footer_scm_amt').value = '0.00';
    document.getElementById('footer_tax_amt').value = taxAmt.toFixed(2);
}

// ============================================
// Calculations
// ============================================
function calculateRowAmount(idx) {
    const row = document.getElementById(`row_${idx}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    const amount = qty * rate;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    
    if (selectedRowIndex === idx) { updateFooterFromRow(row); }
    calculateTotals();
}

function calculateTotals() {
    let totalNtAmt = 0, totalDisAmt = 0, totalScmAmt = 0, totalTax = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
        const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
        const cgst = parseFloat(row.querySelector('input[name*="[cgst]"]')?.value) || 0;
        const sgst = parseFloat(row.querySelector('input[name*="[sgst]"]')?.value) || 0;
        
        const ntAmt = qty * rate;
        const disAmt = ntAmt * disPercent / 100;
        const scmAmt = ntAmt * scmPercent / 100;
        const taxableAmt = ntAmt - disAmt - scmAmt;
        const taxAmt = taxableAmt * (cgst + sgst) / 100;
        
        totalNtAmt += ntAmt;
        totalDisAmt += disAmt;
        totalScmAmt += scmAmt;
        totalTax += taxAmt;
    });
    
    const invAmt = totalNtAmt - totalDisAmt - totalScmAmt + totalTax;
    
    document.getElementById('total_nt_amt').value = totalNtAmt.toFixed(2);
    document.getElementById('total_sc').value = '0.00';
    document.getElementById('total_dis_amt').value = totalDisAmt.toFixed(2);
    document.getElementById('total_scm_amt').value = totalScmAmt.toFixed(2);
    document.getElementById('total_half_scm').value = '0.00';
    document.getElementById('total_tax').value = totalTax.toFixed(2);
    document.getElementById('total_inv_amt').value = invAmt.toFixed(2);
}

// ============================================
// Row Management
// ============================================
function removeRow(idx) {
    document.getElementById(`row_${idx}`)?.remove();
    calculateTotals();
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    } else {
        alert('Please select an item first');
    }
}

// ============================================
// Save & Cancel
// ============================================
function saveTransaction() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const formData = new FormData(document.getElementById('dumpForm'));
    
    fetch('{{ route("admin.breakage-supplier.store-unused-dump") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Transaction saved successfully!\nTRN No: ' + data.trn_no);
            window.location.href = '{{ route("admin.breakage-supplier.unused-dump-modification") }}';
        } else {
            alert('Error: ' + (data.message || 'Failed to save'));
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Discard changes?')) {
        window.location.href = '{{ route("admin.breakage-supplier.unused-dump-modification") }}';
    }
}
</script>
@endpush
