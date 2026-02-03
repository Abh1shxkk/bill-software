@extends('layouts.admin')

@section('title', 'Stock Transfer Incoming Return Transaction')

@push('styles')
<style>
    .stir-form { font-size: 11px; }
    .stir-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .stir-form input, .stir-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group input, .field-group select { flex: 1; min-width: 0; }
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
</style>
@endpush

@section('content')
<section class="stir-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-arrow-up-right me-2"></i> Stock Transfer Incoming Return</h4>
                <div class="text-muted small">Create new stock transfer incoming return (Stock will be REDUCED)</div>
            </div>
            <div>
                <a href="{{ route('admin.stock-transfer-incoming-return.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="stirForm" method="POST" autocomplete="off">
                    @csrf
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required>
                                </div>
                                <div class="ms-5 mt-1">
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <select id="supplier_id" name="supplier_id" class="form-control" required onchange="updateNameFromSupplier()">
                                        <option value="">-</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="name" name="name">
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $nextTrnNo }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No.:</label>
                                    <input type="text" id="gr_no" name="gr_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date:</label>
                                    <input type="date" id="gr_date" name="gr_date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases:</label>
                                    <input type="text" id="cases" name="cases" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport:</label>
                                    <input type="text" id="transport" name="transport" class="form-control">
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
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showItemSelectionModal()">
                                <i class="bi bi-search"></i> Add Items
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
                                    <input type="text" id="packing" name="packing" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Unit:</label>
                                    <input type="text" id="unit" name="unit" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">Cl. Qty:</label>
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
                                <div class="field-group">
                                    <label style="width: 40px;">Srlno:</label>
                                    <input type="text" id="srlno" name="srlno" class="form-control text-end">
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
                            <button type="button" class="btn btn-secondary" onclick="cancelTransaction()">
                                <i class="bi bi-x-circle"></i> Cancel Transfer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'stock-transfer-incoming-return',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'stock-transfer-incoming-return',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection

@push('scripts')
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
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

function updateNameFromSupplier() {
    const select = document.getElementById('supplier_id');
    const nameInput = document.getElementById('name');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.dataset.name) {
        nameInput.value = selectedOption.dataset.name;
    } else {
        nameInput.value = '';
    }
}

function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data.items || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ====== NEW MODAL COMPONENT BRIDGE ======
function showItemSelectionModal() {
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    }
}

window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Item+Batch selected:', item?.name, batch?.batch_no);
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
    const rate = batch ? parseFloat(batch.s_rate || item.s_rate || 0).toFixed(2) : parseFloat(item.s_rate || 0).toFixed(2);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || item.item_code || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchNo}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiry}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${rate}" onchange="calculateRowAmount(${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    updateFooterFromRow(row);
    
    setTimeout(() => { row.querySelector('input[name*="[qty]"]')?.focus(); }, 100);
};

window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

window.onItemSelectedFromModal = function(item) {
    if (typeof openBatchModal_batchSelectionModal === 'function') {
        openBatchModal_batchSelectionModal(item);
    }
};
// ====== END MODAL COMPONENT BRIDGE ======

// ============ LEGACY ITEM SELECTION MODAL (RENAMED) ============
function _legacy_showItemSelectionModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #dc3545;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item (Stock will be REDUCED)</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." onkeyup="filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-danger" style="position: sticky; top: 0;">
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(item.s_rate || item.mrp || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    updateFooterFromRow(row);
    
    // Show batch selection modal
    showBatchSelectionForItem(item, rowIndex);
}

function showBatchSelectionForItem(item, rowIndex) {
    // Fetch ALL batches for this item
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(data => {
            const batches = data.batches || data || [];
            // Filter to only batches with stock
            const availableBatches = Array.isArray(batches) ? batches.filter(b => (b.qty || 0) > 0) : [];
            showBatchSelectionModal(availableBatches, rowIndex, item);
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
            alert('No batches found for this item. This item cannot be returned.');
            removeRow(rowIndex);
        });
}

// ============ BATCH SELECTION MODAL ============
function showBatchSelectionModal(batches, rowIndex, itemData, showAddWithoutBatch = false) {
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #dc3545;">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Select Batch (Stock will be REDUCED)</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModalAndRemove(${rowIndex})"></button>
            </div>
            <div class="modal-body-custom">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2" style="background: #f8d7da; border-radius: 5px;">
                    <div>
                        <strong>ITEM:</strong> <span style="color: #6f42c1; font-weight: bold;">${itemData.name || ''}</span>
                        <span class="ms-3"><strong>Packing:</strong> <span style="color: #6f42c1;">${itemData.packing || ''}</span></span>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm" onclick="addWithoutBatch(${rowIndex})">
                        <i class="bi bi-plus-circle me-1"></i> Add Without Batch
                    </button>
                </div>`;
    if (batches.length > 0) {
        html += `
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px;">
                        <thead class="table-danger">
                            <tr>
                                <th>BATCH</th>
                                <th>RATE</th>
                                <th>MRP</th>
                                <th>Avl. QTY</th>
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
                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                    <td class="text-end text-success fw-bold">${batch.qty || 0}</td>
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
                    <p class="mb-0 mt-2"><strong>No existing batches with stock found.</strong></p>
                    <p class="text-muted small">Click "Add Without Batch" to enter a new batch number.</p>
                </div>`;
    }
    
    html += `</div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModalAndRemove(${rowIndex})">Cancel</button>
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
    row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || 0).toFixed(2);
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    updateFooterFromRow(row);
    closeBatchModal();
    
    // Focus on qty field
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

function closeBatchModalAndRemove(rowIndex) {
    closeBatchModal();
    removeRow(rowIndex);
}

// ============ KEYBOARD NAVIGATION ============
let batchCheckInProgress = false;

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        const batchNo = batchInput?.value.trim();
        
        if (batchNo) {
            checkBatchAndProcess(rowIndex, batchNo);
        } else {
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        }
    }
}

function checkBatchAndProcess(rowIndex, batchNo) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    if (!itemId) {
        row?.querySelector('input[name*="[expiry]"]')?.focus();
        return;
    }
    
    if (batchCheckInProgress) return;
    batchCheckInProgress = true;
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
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
                row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || 0).toFixed(2);
                row.dataset.batchId = batch.id;
                row.dataset.batchData = JSON.stringify(batch);
                updateFooterFromRow(row);
                row?.querySelector('input[name*="[qty]"]')?.focus();
            } else {
                // Batch doesn't exist - show new batch modal
                showNewBatchModal(rowIndex, batchNo, itemData);
            }
        })
        .catch(error => {
            console.error('Error checking batch:', error);
            // Show new batch modal on error too
            showNewBatchModal(rowIndex, batchNo, itemData);
        })
        .finally(() => {
            setTimeout(() => { batchCheckInProgress = false; }, 500);
        });
}

// ============ OPENING NEW BATCH MODAL ============
function showNewBatchModal(rowIndex, batchNo, itemData) {
    let html = `
        <div class="batch-modal-backdrop show" id="newBatchBackdrop"></div>
        <div class="batch-modal show" id="newBatchModal" style="max-width: 450px;">
            <div class="modal-header-custom" style="background: #6c757d; padding: 0.7rem 1rem;">
                <h6 class="mb-0">Opening New Batch</h6>
                <button type="button" class="btn-close btn-close-white" onclick="closeNewBatchModal()"></button>
            </div>
            <div class="modal-body-custom" style="background: #e9ecef;">
                <div class="mb-2">
                    <label class="small fw-bold" style="color: blue;">Item Name :</label>
                    <span style="color: blue; font-weight: bold;">${itemData.name || ''}</span>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold" style="color: blue;">Pack :</label>
                    <span style="color: blue;">${itemData.packing || ''}</span>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <label class="small fw-bold">S.Rate :</label>
                        <input type="number" class="form-control form-control-sm" id="newBatch_sRate" step="0.01" value="${parseFloat(itemData.s_rate || 0).toFixed(2)}" style="background: #c6e3ff;">
                    </div>
                    <div class="col-4">
                        <label class="small fw-bold">Expiry :</label>
                        <input type="text" class="form-control form-control-sm" id="newBatch_expiry" placeholder="MM/YYYY">
                    </div>
                    <div class="col-4">
                        <label class="small fw-bold">MRP :</label>
                        <input type="number" class="form-control form-control-sm" id="newBatch_mrp" step="0.01" value="${parseFloat(itemData.mrp || 0).toFixed(2)}">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Location :</label>
                    <input type="text" class="form-control form-control-sm" id="newBatch_location" value="${itemData.location || ''}">
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Inclusive :</label>
                    <select class="form-control form-control-sm" id="newBatch_inclusive" style="width: 60px;">
                        <option value="Y">Y</option>
                        <option value="N">N</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer-custom" style="padding: 0.5rem 1rem;">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveNewBatch(${rowIndex}, '${batchNo.replace(/'/g, "\\'")}')">
                    OK
                </button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('newBatch_sRate')?.focus();
}

function closeNewBatchModal() {
    document.getElementById('newBatchModal')?.remove();
    document.getElementById('newBatchBackdrop')?.remove();
}

function saveNewBatch(rowIndex, batchNo) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        closeNewBatchModal();
        return;
    }
    
    const sRate = parseFloat(document.getElementById('newBatch_sRate')?.value) || 0;
    const expiry = document.getElementById('newBatch_expiry')?.value || '';
    const mrp = parseFloat(document.getElementById('newBatch_mrp')?.value) || 0;
    const location = document.getElementById('newBatch_location')?.value || '';
    const inclusive = document.getElementById('newBatch_inclusive')?.value || 'Y';
    
    // Store new batch data
    row.dataset.isNewBatch = 'true';
    row.dataset.newBatchData = JSON.stringify({
        batch_no: batchNo,
        s_rate: sRate,
        expiry: expiry,
        mrp: mrp,
        location: location,
        inclusive: inclusive
    });
    
    // Fill row fields
    row.querySelector('input[name*="[batch]"]').value = batchNo;
    row.querySelector('input[name*="[expiry]"]').value = expiry;
    row.querySelector('input[name*="[rate]"]').value = sRate.toFixed(2);
    
    closeNewBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        completeRow(rowIndex);
    }
}

function completeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.remove('row-selected');
        row.classList.add('row-complete');
    }
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        row.classList.remove('row-complete');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('comp').value = itemData.company_short_name || '';
    document.getElementById('cl_qty').value = batchData.qty || itemData.total_qty || '0';
    document.getElementById('lctn').value = batchData.location || '';
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
    const amount = qty * rate;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    calculateNetAmount();
}

function calculateNetAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        total += amount;
    });
    document.getElementById('net_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        calculateNetAmount();
    }
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    }
}

function saveTransaction() {
    const name = document.getElementById('name').value;
    if (!name) {
        alert('Please select a supplier');
        return;
    }
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemId = row.dataset.itemId;
        const batchId = row.dataset.batchId;
        const isNewBatch = row.dataset.isNewBatch === 'true';
        const newBatchData = row.dataset.newBatchData ? JSON.parse(row.dataset.newBatchData) : null;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        
        // Allow items with either existing batch OR new batch
        if (itemId && qty > 0 && (batchId || isNewBatch)) {
            items.push({
                item_id: itemId,
                batch_id: batchId || '',
                is_new_batch: isNewBatch,
                new_batch_data: newBatchData,
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                name: row.querySelector('input[name*="[name]"]')?.value || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: qty,
                rate: parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0,
                amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item with valid batch and quantity');
        return;
    }
    
    const data = {
        _token: '{{ csrf_token() }}',
        transaction_date: document.getElementById('transaction_date').value,
        day_name: document.getElementById('day_name').value,
        name: name,
        gr_no: document.getElementById('gr_no').value,
        gr_date: document.getElementById('gr_date').value,
        cases: document.getElementById('cases').value,
        transport: document.getElementById('transport').value,
        remarks: document.getElementById('remarks').value,
        net_amount: document.getElementById('net_amount').value,
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
    
    fetch('{{ route("admin.stock-transfer-incoming-return.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message + '\nTrn No: ' + result.trn_no);
            window.location.href = '{{ route("admin.stock-transfer-incoming-return.index") }}';
        } else {
            alert(result.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Are you sure you want to cancel? All unsaved data will be lost.')) {
        window.location.href = '{{ route("admin.stock-transfer-incoming-return.index") }}';
    }
}
</script>
@endpush
