@extends('layouts.admin')

@section('title', 'Godown Breakage/Expiry - Modification')

@push('styles')
<style>
    .gbe-form { font-size: 11px; }
    .gbe-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .gbe-form input, .gbe-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #87CEEB; font-weight: 600; text-align: center; border: 1px solid #dee2e6; }
    .table-compact input, .table-compact select { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; outline: 2px solid #007bff !important; outline-offset: -2px; }
    .row-complete { background-color: #d4edda !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #17a2b8; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover, .batch-row:hover, .invoice-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
</style>
@endpush

@section('content')
<section class="gbe-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Godown Breakage/Expiry - Modification</h4>
                <div class="text-muted small">Load and modify existing transaction</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" onclick="showLoadInvoiceModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoice
                </button>
                <a href="{{ route('admin.godown-breakage-expiry.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="{{ route('admin.godown-breakage-expiry.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="gbeForm" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="transaction_id" name="transaction_id" value="">
                    
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="field-group">
                                    <label style="width: 70px;">Narration :</label>
                                    <input type="text" id="narration" name="narration" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 90px;">Batch</th>
                                        <th style="width: 70px;">Expiry</th>
                                        <th style="width: 70px;">Br/Ex</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Cost</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()"><i class="bi bi-plus-circle"></i> Add Row</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="showItemSelectionModal()"><i class="bi bi-search"></i> Add Items</button>
                        </div>
                    </div>

                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Total :</label>
                            <input type="text" id="total_amount" name="total_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="field-group mb-1"><label style="width: 50px;">Packing :</label><input type="text" id="packing" class="form-control readonly-field" readonly></div>
                                <div class="field-group mb-1"><label style="width: 50px;">Unit :</label><input type="text" id="unit" class="form-control readonly-field" readonly></div>
                                <div class="field-group"><label style="width: 50px;">Cl.Qty :</label><input type="text" id="cl_qty" class="form-control readonly-field text-end" value="0" readonly></div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1"><label style="width: 50px;">Pur.Rate :</label><input type="text" id="p_rate" class="form-control readonly-field text-end" readonly></div>
                                <div class="field-group mb-1"><label style="width: 50px;">S.Rate :</label><input type="text" id="s_rate" class="form-control readonly-field text-end" readonly></div>
                                <div class="field-group"><label style="width: 50px;">Mrp :</label><input type="text" id="mrp" class="form-control readonly-field text-end" readonly></div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="field-group justify-content-end"><label style="width: 40px;">Srlno :</label><input type="text" id="srlno" class="form-control text-end" style="width: 80px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="updateTransaction()"><i class="bi bi-save"></i> Update (End)</button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()"><i class="bi bi-trash"></i> Delete Item</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelModification()"><i class="bi bi-x-circle"></i> Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'godownBreakageExpiryModItemModal',
    'module' => 'godown-breakage-expiry-mod',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'godownBreakageExpiryModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'godownBreakageExpiryModBatchModal',
    'module' => 'godown-breakage-expiry-mod',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@push('scripts')
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let loadedTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    const urlParams = new URLSearchParams(window.location.search);
    const loadId = urlParams.get('load');
    if (loadId) {
        setTimeout(() => loadTransactionData(loadId), 300);
    }
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
    fetch('{{ route("admin.godown-breakage-expiry.getItems") }}')
        .then(response => response.json())
        .then(data => { itemsData = data || []; })
        .catch(error => console.error('Error loading items:', error));
}

// ============================================================================
// BRIDGE FUNCTIONS FOR REUSABLE MODAL COMPONENTS
// ============================================================================

/**
 * Bridge function called by reusable modal components after item and batch selection
 * Supports both creating new rows and updating existing rows based on targetRowIndex
 */
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Godown Breakage Expiry Mod: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Godown Breakage Expiry Mod: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    
    // Check if we should update existing row or create new one
    const targetRowIndex = window.targetRowIndexForModal;
    const shouldCreateNewRow = (targetRowIndex === null || targetRowIndex === undefined);
    
    if (shouldCreateNewRow) {
        // CREATE NEW ROW
        const rowIndex = currentRowIndex++;
        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        row.dataset.rowIndex = rowIndex;
        row.dataset.itemId = itemData.id;
        row.onclick = function() { selectRow(rowIndex); };
        
        // Store item and batch data
        row.dataset.itemData = JSON.stringify({
            packing: itemData.packing || '',
            unit: itemData.unit || '1',
            mrp: itemData.mrp || 0,
            s_rate: itemData.s_rate || 0,
            p_rate: itemData.p_rate || itemData.pur_rate || 0,
            company_name: itemData.company_name || ''
        });
        
        if (batchData && batchData.id) {
            row.dataset.batchId = batchData.id;
            row.dataset.batchData = JSON.stringify({
                qty: batchData.qty || batchData.available_qty || 0,
                location: batchData.location || ''
            });
        }
        
        const cost = batchData?.pur_rate || batchData?.cost || batchData?.avg_pur_rate || itemData.p_rate || itemData.pur_rate || 0;
        const qty = 1;
        const amount = (qty * cost).toFixed(2);
        
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_display || batchData?.expiry || ''}" readonly></td>
            <td>
                <select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
                    <option value="BREAKAGE">Brk</option>
                    <option value="EXPIRY">Exp</option>
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${qty}" onchange="calculateRowAmount(${rowIndex})"></td>
            <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="${cost}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
            <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${amount}" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id || ''}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || '1'}">
        `;
        
        tbody.appendChild(row);
        row.classList.add('row-complete');
        selectRow(rowIndex);
        calculateRowAmount(rowIndex);
        
        console.log('✅ Godown Breakage Expiry Mod: New row created successfully', rowIndex);
        
        // Focus qty field
        setTimeout(() => {
            row.querySelector('input[name*="[qty]"]')?.focus();
        }, 100);
        
    } else {
        // UPDATE EXISTING ROW
        const row = document.getElementById(`row-${targetRowIndex}`);
        if (!row) {
            console.error('❌ Godown Breakage Expiry Mod: Target row not found', targetRowIndex);
            return;
        }
        
        row.dataset.itemId = itemData.id;
        row.dataset.mrp = itemData.mrp || 0;
        row.dataset.sRate = itemData.s_rate || 0;
        
        if (batchData && batchData.id) {
            row.dataset.batchId = batchData.id;
            row.dataset.clQty = batchData.qty || batchData.available_qty || 0;
        }
        
        const cost = batchData?.pur_rate || batchData?.cost || batchData?.avg_pur_rate || itemData.p_rate || itemData.pur_rate || 0;
        
        // Update row fields
        row.querySelector('input[name*="[code]"]').value = itemData.id || '';
        row.querySelector('input[name*="[name]"]').value = itemData.name || '';
        row.querySelector('input[name*="[batch]"]').value = batchData?.batch_no || '';
        row.querySelector('input[name*="[expiry]"]').value = batchData?.expiry_display || batchData?.expiry || '';
        row.querySelector('input[name*="[cost]"]').value = cost;
        row.querySelector('input[name*="[item_id]"]').value = itemData.id || '';
        row.querySelector('input[name*="[batch_id]"]').value = batchData?.id || '';
        row.querySelector('input[name*="[packing]"]').value = itemData.packing || '';
        row.querySelector('input[name*="[unit]"]').value = itemData.unit || '1';
        
        row.classList.add('row-complete');
        calculateRowAmount(targetRowIndex);
        
        console.log('✅ Godown Breakage Expiry Mod: Existing row updated successfully', targetRowIndex);
    }
    
    // Update footer
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('p_rate').value = itemData.p_rate || itemData.pur_rate || '0';
    document.getElementById('s_rate').value = itemData.s_rate || '0';
    document.getElementById('mrp').value = itemData.mrp || '0';
    document.getElementById('cl_qty').value = batchData?.qty || batchData?.available_qty || '0';
    
    calculateTotalAmount();
    
    // Clear target row index
    window.targetRowIndexForModal = null;
    
    console.log('✅ Godown Breakage Expiry Mod: Footer updated, targetRowIndex cleared');
}

/**
 * Bridge function to open item selection modal
 * Supports targetRowIndex parameter for updating existing rows
 * @param {number|null} targetRowIndex - Row index to update, or null to create new row
 */
function showItemSelectionModal(targetRowIndex = null) {
    console.log('🎯 Godown Breakage Expiry Mod: showItemSelectionModal called', {targetRowIndex});
    
    // Store targetRowIndex for later use
    window.targetRowIndexForModal = targetRowIndex;
    
    // Check if modal component function exists
    if (typeof window.openItemModal_godownBreakageExpiryModItemModal === 'function') {
        console.log('✅ Godown Breakage Expiry Mod: Opening reusable item modal');
        window.openItemModal_godownBreakageExpiryModItemModal();
    } else {
        console.error('❌ Godown Breakage Expiry Mod: openItemModal_godownBreakageExpiryModItemModal function not found. Modal component may not be loaded.');
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}

// ============================================================================
// LEGACY FUNCTIONS (Kept as fallback, prefixed with _legacy_)
// ============================================================================

function showLoadInvoiceModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="loadInvoiceBackdrop"></div>
        <div class="batch-modal show" id="loadInvoiceModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #fd7e14;">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Invoice</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeLoadInvoiceModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3"><input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by TRN No..." onkeyup="searchInvoices()"></div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr><th>TRN No.</th><th>Date</th><th>Narration</th><th class="text-end">Amount</th><th>Action</th></tr>
                        </thead>
                        <tbody id="invoicesListBody"><tr><td colspan="5" class="text-center">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadInvoiceModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    loadPastInvoices();
}

function loadPastInvoices(search = '') {
    fetch(`{{ route("admin.godown-breakage-expiry.getPastInvoices") }}?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('invoicesListBody');
            if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No invoices found</td></tr>'; return; }
            tbody.innerHTML = data.map(inv => `
                <tr class="invoice-row" onclick="selectInvoice(${inv.id})" style="cursor: pointer;">
                    <td><strong>${inv.trn_no}</strong></td>
                    <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString('en-GB') : '-'}</td>
                    <td>${inv.narration || '-'}</td>
                    <td class="text-end">₹${parseFloat(inv.total_amount || 0).toFixed(2)}</td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectInvoice(${inv.id})"><i class="bi bi-check"></i></button></td>
                </tr>
            `).join('');
        });
}

function searchInvoices() { loadPastInvoices(document.getElementById('invoiceSearchInput').value); }
function closeLoadInvoiceModal() { document.getElementById('loadInvoiceModal')?.remove(); document.getElementById('loadInvoiceBackdrop')?.remove(); }
function selectInvoice(id) { closeLoadInvoiceModal(); loadTransactionData(id); }

function loadTransactionData(id) {
    fetch(`{{ url('admin/godown-breakage-expiry') }}/${id}`, { headers: { 'Accept': 'application/json' } })
    .then(response => response.json())
    .then(data => { if (data && data.id) { populateForm(data); } else { alert('Transaction not found'); } })
    .catch(error => { console.error('Error:', error); alert('Error loading transaction'); });
}

function populateForm(transaction) {
    loadedTransactionId = transaction.id;
    document.getElementById('transaction_id').value = transaction.id;
    document.getElementById('transaction_date').value = transaction.transaction_date ? transaction.transaction_date.split('T')[0] : '';
    updateDayName();
    document.getElementById('trn_no').value = transaction.trn_no || '';
    document.getElementById('narration').value = transaction.narration || '';
    document.getElementById('total_amount').value = parseFloat(transaction.total_amount || 0).toFixed(2);
    
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach((item, index) => {
            addItemRowFromData(item);
            if (index === 0) selectRow(0);
        });
    }
    calculateTotalAmount();
    alert('Transaction loaded: ' + transaction.trn_no);
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.item_id || '';
    row.dataset.batchId = item.batch_id || '';
    row.dataset.mrp = item.mrp || '0';
    row.dataset.sRate = item.s_rate || '0';
    row.dataset.clQty = '0';
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || item.item_id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}"></td>
        <td><select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
            <option value="BREAKAGE" ${item.br_ex_type == 'BREAKAGE' ? 'selected' : ''}>Brk</option>
            <option value="EXPIRY" ${item.br_ex_type == 'EXPIRY' ? 'selected' : ''}>Exp</option>
        </select></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="${parseFloat(item.cost || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${item.batch_id || ''}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onclick="_legacy_showBatchModal(${rowIndex})" readonly style="cursor: pointer;"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" readonly></td>
        <td><select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]"><option value="BREAKAGE">Brk</option><option value="EXPIRY">Exp</option></select></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="0" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="">
        <input type="hidden" name="items[${rowIndex}][unit]" value="">
    `;
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) { 
        row.classList.add('row-selected'); 
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    if (!row) return;
    document.getElementById('packing').value = row.querySelector('input[name*="[packing]"]')?.value || '';
    document.getElementById('unit').value = row.querySelector('input[name*="[unit]"]')?.value || '';
    document.getElementById('p_rate').value = row.querySelector('input[name*="[cost]"]')?.value || '0';
    document.getElementById('s_rate').value = row.dataset.sRate || '0';
    document.getElementById('mrp').value = row.dataset.mrp || '0';
    document.getElementById('cl_qty').value = row.dataset.clQty || '0';
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const code = event.target.value.trim();
        if (code) { findItemByCode(code, rowIndex); } else { showItemSelectionModal(rowIndex); }
    }
}

function findItemByCode(code, rowIndex) {
    const item = itemsData.find(i => i.id == code || i.name.toLowerCase().includes(code.toLowerCase()));
    if (item) { selectItemForRow(item, rowIndex); } else { showItemSelectionModal(rowIndex); }
}

function _legacy_showItemSelectionModal(targetRowIndex = null) {
    const rowIndex = targetRowIndex !== null ? targetRowIndex : selectedRowIndex;
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal" style="max-width: 900px;">
            <div class="modal-header-custom"><h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5><button type="button" class="btn-close btn-close-white" onclick="_legacy_closeItemModal()"></button></div>
            <div class="modal-body-custom">
                <div class="mb-3"><input type="text" class="form-control" id="itemSearchInput" placeholder="Search..." onkeyup="_legacy_filterItems()"></div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-info" style="position: sticky; top: 0;"><tr><th>Code</th><th>Item Name</th><th>Packing</th><th>Company</th><th class="text-end">MRP</th></tr></thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.body.dataset.targetRowIndex = rowIndex;
    document.getElementById('itemSearchInput').focus();
    _legacy_renderItemsList();
}

function _legacy_renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = filter ? itemsData.filter(item => item.name.toLowerCase().includes(filter.toLowerCase()) || item.id.toString().includes(filter)) : itemsData;
    tbody.innerHTML = filtered.slice(0, 100).map(item => `
        <tr class="item-row" onclick="_legacy_selectItem(${item.id})" style="cursor: pointer;">
            <td>${item.id}</td><td>${item.name}</td><td>${item.packing || '-'}</td><td>${item.company_name || '-'}</td><td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function _legacy_filterItems() { _legacy_renderItemsList(document.getElementById('itemSearchInput').value); }

function _legacy_selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (item) { _legacy_selectItemForRow(item, parseInt(document.body.dataset.targetRowIndex)); _legacy_closeItemModal(); }
}

function _legacy_selectItemForRow(item, rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    row.querySelector('input[name*="[code]"]').value = item.id;
    row.querySelector('input[name*="[name]"]').value = item.name;
    row.querySelector('input[name*="[item_id]"]').value = item.id;
    row.querySelector('input[name*="[packing]"]').value = item.packing || '';
    row.querySelector('input[name*="[unit]"]').value = item.unit || '';
    row.querySelector('input[name*="[cost]"]').value = item.p_rate || 0;
    row.dataset.itemId = item.id;
    row.dataset.mrp = item.mrp || 0;
    row.dataset.sRate = item.s_rate || 0;
    updateFooterFromRow(row);
    _legacy_showBatchModal(rowIndex);
}

function _legacy_closeItemModal() { document.getElementById('itemModal')?.remove(); document.getElementById('itemModalBackdrop')?.remove(); }

function _legacy_showBatchModal(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row || !row.dataset.itemId) { alert('Please select an item first'); return; }
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchModalBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #ffc107; color: #000;"><h5 class="mb-0"><i class="bi bi-box me-2"></i>Select Batch</h5><button type="button" class="btn-close" onclick="_legacy_closeBatchModal()"></button></div>
            <div class="modal-body-custom"><div class="text-center py-3"><div class="spinner-border text-warning"></div><div class="mt-2">Loading...</div></div></div>
            <div class="modal-footer-custom"><button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeBatchModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.body.dataset.batchRowIndex = rowIndex;
    
    fetch(`{{ url('admin/api/item-batches') }}/${row.dataset.itemId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#batchModal .modal-body-custom');
            // Handle response format: { success: true, batches: [...] }
            const batches = data.batches || data || [];
            if (!batches || batches.length === 0) { modalBody.innerHTML = '<div class="text-center text-muted py-3">No batches available</div>'; return; }
            modalBody.innerHTML = `<div class="table-responsive"><table class="table table-bordered table-sm" style="font-size: 11px;">
                <thead class="table-warning"><tr><th>Batch No</th><th>Expiry</th><th class="text-end">Qty</th><th class="text-end">MRP</th><th class="text-end">P.Rate</th><th class="text-end">S.Rate</th></tr></thead>
                <tbody>${batches.map(b => `<tr class="batch-row" onclick="_legacy_selectBatch(${b.id}, '${(b.batch_no || '').replace(/'/g, "\\'")}', '${(b.expiry_display || '').replace(/'/g, "\\'")}', ${b.qty || 0}, ${b.pur_rate || b.cost || 0}, ${b.mrp || 0}, ${b.s_rate || 0})" style="cursor: pointer;"><td>${b.batch_no || '-'}</td><td>${b.expiry_display || '-'}</td><td class="text-end">${b.qty || 0}</td><td class="text-end">${parseFloat(b.mrp || 0).toFixed(2)}</td><td class="text-end">${parseFloat(b.pur_rate || b.cost || 0).toFixed(2)}</td><td class="text-end">${parseFloat(b.s_rate || 0).toFixed(2)}</td></tr>`).join('')}</tbody>
            </table></div>`;
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            document.querySelector('#batchModal .modal-body-custom').innerHTML = '<div class="text-center text-danger py-3">Error loading batches</div>';
        });
}

function _legacy_selectBatch(batchId, batchNo, expiry, qty, pRate, mrp, sRate) {
    const row = document.getElementById(`row-${document.body.dataset.batchRowIndex}`);
    if (!row) return;
    row.querySelector('input[name*="[batch]"]').value = batchNo;
    row.querySelector('input[name*="[expiry]"]').value = expiry;
    row.querySelector('input[name*="[batch_id]"]').value = batchId;
    row.querySelector('input[name*="[cost]"]').value = pRate || 0;
    row.dataset.batchId = batchId;
    row.dataset.clQty = qty || 0;
    row.dataset.mrp = mrp || 0;
    row.dataset.sRate = sRate || 0;
    row.classList.add('row-complete');
    _legacy_closeBatchModal();
    updateFooterFromRow(row);
    row.querySelector('input[name*="[qty]"]').focus();
}

function _legacy_closeBatchModal() { document.getElementById('batchModal')?.remove(); document.getElementById('batchModalBackdrop')?.remove(); }

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const cost = parseFloat(row.querySelector('input[name*="[cost]"]').value) || 0;
    row.querySelector('input[name*="[amount]"]').value = (qty * cost).toFixed(2);
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => { total += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0; });
    document.getElementById('total_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) { document.getElementById(`row-${rowIndex}`)?.remove(); calculateTotalAmount(); }
function deleteSelectedItem() { if (selectedRowIndex !== null) { removeRow(selectedRowIndex); selectedRowIndex = null; } }

let isSubmitting = false;

function updateTransaction() {
    if (!loadedTransactionId) { alert('Please load an invoice first'); return; }
    
    // Prevent double submission
    if (isSubmitting) { return; }
    isSubmitting = true;
    
    // Disable button and show loading
    const updateBtn = document.querySelector('button[onclick="updateTransaction()"]');
    const originalBtnHtml = updateBtn.innerHTML;
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
    
    const formData = new FormData(document.getElementById('gbeForm'));
    let totalQty = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => { totalQty += parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0; });
    formData.append('total_qty', totalQty);
    formData.set('_method', 'PUT');
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/godown-breakage-expiry') }}/${loadedTransactionId}`, {
        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { alert(data.message); window.location.href = '{{ route("admin.godown-breakage-expiry.index") }}'; }
        else { 
            alert(data.message || 'Error updating');
            isSubmitting = false;
            updateBtn.disabled = false;
            updateBtn.innerHTML = originalBtnHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
    });
}

function cancelModification() { if (confirm('Cancel modification?')) { window.location.href = '{{ route("admin.godown-breakage-expiry.index") }}'; } }
</script>
@endpush
