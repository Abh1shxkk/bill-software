@extends('layouts.admin')

@section('title', 'Replacement Received Transaction')

@push('styles')
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
</style>
@endpush

@section('content')
<section class="rn-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-down me-2"></i> Replacement Received Transaction</h4>
                <div class="text-muted small">Create new replacement received</div>
            </div>
            <div>
                <a href="{{ route('admin.replacement-received.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="rnForm" method="POST" autocomplete="off">
                    @csrf
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="d-flex gap-4 align-items-center">
                            <!-- Date Field -->
                            <div class="field-group">
                                <label style="width: 40px;">Date :</label>
                                <input type="date" id="transaction_date" name="transaction_date" class="form-control" style="width: 140px;" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required>
                            </div>

                            <!-- Day Field -->
                            <div class="field-group">
                                <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" style="width: 100px;" value="{{ date('l') }}" readonly>
                            </div>

                            <!-- Supplier Field -->
                            <div class="field-group flex-grow-1">
                                <label style="width: 70px;">Supplier :</label>
                                <select id="supplier_id" name="supplier_id" class="form-control" style="flex: 1; max-width: 400px;" required>
                                    <option value="">-</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rpl.No Field -->
                            <div class="field-group">
                                <label style="width: 60px;">Rpl.No. :</label>
                                <input type="text" id="rr_no" name="rr_no" class="form-control" style="width: 100px;" placeholder="Enter No." required>
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
                                        <th style="width: 70px;">MRP</th>
                                        <th style="width: 50px;">Dis.%</th>
                                        <th style="width: 80px;">F.T. Rate</th>
                                        <th style="width: 90px;">F.T. Amt.</th>
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
                            <button type="button" class="btn btn-sm btn-info" onclick="openItemModal()">
                                <i class="bi bi-list-check"></i> Select Item
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
                            <div class="col-md-2">
                                <label class="small">Excise</label>
                                <input type="number" id="excise" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">Spl.Rate</label>
                                <input type="number" id="spl_rate" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">W.S.Rate</label>
                                <input type="number" id="ws_rate" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-1">
                                <label class="small">P.Rate</label>
                                <input type="number" id="p_rate" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">MRP</label>
                                <input type="number" id="batch_mrp" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="small">S.Rate</label>
                                <input type="number" id="s_rate" class="form-control form-control-sm" step="0.01" value="0" onkeydown="handleSRateKeydown(event, selectedRowIndex)">
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
                            <div class="col-md-3">
                                <div class="field-group mb-1">
                                    <label style="width: 60px;">P.SCM.</label>
                                    <input type="number" id="p_scm_percent" name="p_scm_percent" class="form-control text-end" style="width: 60px;" step="0.01" value="0">
                                    <span>+</span>
                                    <input type="number" id="p_scm_amount" name="p_scm_amount" class="form-control text-end" style="width: 80px;" step="0.01" value="0.00">
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">S.SCM.</label>
                                    <input type="number" id="s_scm_percent" name="s_scm_percent" class="form-control text-end" style="width: 60px;" step="0.01" value="0">
                                    <span>+</span>
                                    <input type="number" id="s_scm_amount" name="s_scm_amount" class="form-control text-end" style="width: 80px;" step="0.01" value="0.00">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">AMT.</label>
                                    <input type="number" id="amt" name="amt" class="form-control readonly-field text-end" style="width: 100px;" value="0.00" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Srl.No.</label>
                                    <input type="text" id="srlno" name="srlno" class="form-control text-end" style="width: 100px;">
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
                            <button type="button" class="btn btn-primary" onclick="openInsertItemsModal()">
                                <i class="bi bi-plus-square"></i> Insert Item
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelTransaction()">
                                <i class="bi bi-x-circle"></i> Cancel Replacement
                            </button>
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
    'id' => 'replacementReceivedItemModal',
    'module' => 'replacement-received',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'replacementReceivedBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'replacementReceivedBatchModal',
    'module' => 'replacement-received',
    'showOnlyAvailable' => false,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@push('scripts')
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
    
    const mrp = parseFloat(batch.mrp || batch.avg_mrp || item.mrp || 0);
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.batchId = batch.id;
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.batchData = JSON.stringify(batch);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" value="0" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" value="0" step="1" min="0" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][discount_percent]" value="0" step="0.01" min="0" max="100" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    
    tbody.appendChild(row);
    
    // Update footer display
    updateFooterFromRow(row);
    
    // Select the row
    selectRow(rowIndex);
    
    // Focus qty field
    setTimeout(() => {
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
    
    calculateRowAmount(rowIndex);
    calculateTotals();
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Don't add empty row on load - items will be added via Insert Items button
    loadItems();
});

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

// Update Supplier Name
function updateSupplierName() {
    const select = document.getElementById('supplier_id');
    const option = select.options[select.selectedIndex];
    // Store supplier name for later use
    window.selectedSupplierName = option ? option.dataset.name : '';
}

// Load Items
function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][free_qty]" step="1" min="0" value="0"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][discount_percent]" step="0.01" min="0" max="100" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly></td>
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
    
    console.log('updateFooterFromRow - itemData:', itemData);
    console.log('updateFooterFromRow - batchData:', batchData);
    
    // Use item data for company (company_short_name) and total_qty (sum of all batches)
    const packEl = document.getElementById('packing');
    const unitEl = document.getElementById('unit');
    const compEl = document.getElementById('comp');
    const clQtyEl = document.getElementById('cl_qty');
    const lctnEl = document.getElementById('lctn');
    
    if (packEl) packEl.value = itemData.packing || '';
    if (unitEl) unitEl.value = itemData.unit || '1';
    if (compEl) compEl.value = itemData.company_short_name || itemData.mfg_by || '';
    if (clQtyEl) clQtyEl.value = itemData.total_qty || batchData.qty || '0';
    if (lctnEl) lctnEl.value = batchData.location || itemData.location || '';
    
    // Populate Rates Section - Prefer batch data, then item data
    const incEl = document.getElementById('inclusive');
    const exciseEl = document.getElementById('excise');
    const splRateEl = document.getElementById('spl_rate');
    const wsRateEl = document.getElementById('ws_rate');
    const pRateEl = document.getElementById('p_rate');
    const mrpEl = document.getElementById('batch_mrp');
    const sRateEl = document.getElementById('s_rate');
    
    if (incEl) incEl.value = batchData.inc || itemData.inc || 'Y';
    if (exciseEl) exciseEl.value = parseFloat(batchData.excise || itemData.excise || 0).toFixed(2);
    if (splRateEl) splRateEl.value = parseFloat(batchData.spl_rate || itemData.spl_rate || 0).toFixed(2);
    if (wsRateEl) wsRateEl.value = parseFloat(batchData.ws_rate || itemData.ws_rate || 0).toFixed(2);
    if (pRateEl) pRateEl.value = parseFloat(batchData.pur_rate || itemData.pur_rate || 0).toFixed(2);
    if (mrpEl) mrpEl.value = parseFloat(batchData.mrp || itemData.mrp || 0).toFixed(2);
    if (sRateEl) sRateEl.value = parseFloat(batchData.s_rate || itemData.s_rate || 0).toFixed(2);
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
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
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
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[free_qty]"]')?.focus();
    }
}

function handleFreeQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[mrp]"]')?.focus();
    }
}

function handleMrpKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        // Move cursor to footer S.Rate field
        document.getElementById('s_rate')?.focus();
    }
}

function handleSRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        // Mark row as complete (green)
        row?.classList.add('row-complete');
        // Remove focus/blur the field
        event.target.blur();
    }
}

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
            <div class="modal-body-custom" style="background: #e8e8e8;">
                <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                    <strong>Item Name:</strong> <span style="color: #0d6efd; font-weight: bold;">${itemData.name || ''}</span>
                </div>
                <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                    <strong>Pack :</strong> <span style="color: #0d6efd; font-weight: bold;">${itemData.packing || ''}</span>
                </div>
                
                <!-- Batch Number and Expiry -->
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Batch No. :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchNo" value="${batchNo}" readonly style="background: #fff; font-weight: bold;">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Expiry :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY" style="background: #fff;">
                    </div>
                </div>
                
                <!-- All Rate Fields -->
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Inc. :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchInc" value="0.00" step="0.01" style="background: #fff;">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Excise :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchExcise" value="0.00" step="0.01" style="background: #fff;">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Spl.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchSplRate" value="${parseFloat(itemData.spl_rate || 0).toFixed(2)}" step="0.01" style="background: #fff;">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>W.S.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchWSRate" value="${parseFloat(itemData.ws_rate || 0).toFixed(2)}" step="0.01" style="background: #fff;">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>P.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchPRate" value="${parseFloat(itemData.pur_rate || 0).toFixed(2)}" step="0.01" style="background: #fff;">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>MRP :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${parseFloat(itemData.mrp || 0).toFixed(2)}" step="0.01" style="background: #fff;">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>S.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${parseFloat(itemData.s_rate || itemData.mrp || 0).toFixed(2)}" step="0.01" style="background: #fff;">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="color: #333; font-size: 12px;"><strong>Location :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchLocation" value="" style="background: #fff;">
                    </div>
                </div>
                
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
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = document.getElementById('newBatchMRP').value;
    const sRate = document.getElementById('newBatchSRate').value;
    const pRate = document.getElementById('newBatchPRate').value;
    const wsRate = document.getElementById('newBatchWSRate').value;
    const splRate = document.getElementById('newBatchSplRate').value;
    const inc = document.getElementById('newBatchInc').value;
    const excise = document.getElementById('newBatchExcise').value;
    const location = document.getElementById('newBatchLocation').value;
    
    if (!batchNo || !itemId) {
        alert('Batch number and item are required');
        return;
    }
    
    // Validate expiry format
    if (expiry && !/^\d{2}\/\d{4}$/.test(expiry)) {
        alert('Expiry must be in MM/YYYY format');
        document.getElementById('newBatchExpiry').focus();
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
            mrp: parseFloat(mrp),
            s_rate: parseFloat(sRate),
            pur_rate: parseFloat(pRate),
            ws_rate: parseFloat(wsRate),
            spl_rate: parseFloat(splRate),
            inc: parseFloat(inc),
            excise: parseFloat(excise),
            location: location
        });
        
        // Store s_rate for later use
        row.dataset.sRate = sRate;
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

// Open Insert Items Modal - Use reusable modal component
function openInsertItemsModal() {
    if (typeof openItemModal_replacementReceivedItemModal === 'function') {
        openItemModal_replacementReceivedItemModal();
    } else {
        console.error('Reusable item modal not found');
        alert('Item selection modal not found. Please reload the page.');
    }
}

// Open Item Modal (legacy - for Select Item button)
function openItemModal() {
    openInsertItemsModal();
}

// Calculate Row Amount
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]').value) || 0;
    const discountPercent = parseFloat(row.querySelector('input[name*="[discount_percent]"]').value) || 0;
    
    // F.T. Rate = MRP - (MRP × Discount% / 100)
    const ftRate = mrp - (mrp * discountPercent / 100);
    
    // F.T. Amount = Qty × F.T. Rate
    const ftAmount = qty * ftRate;
    
    row.querySelector('input[name*="[ft_rate]"]').value = ftRate.toFixed(2);
    row.querySelector('input[name*="[ft_amount]"]').value = ftAmount.toFixed(2);
    
    calculateTotals();
}

// Calculate Totals
function calculateTotals() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const ftAmount = parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0;
        total += ftAmount;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
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

// Pending transaction data for adjustment
let pendingTransactionData = null;
let adjustmentData = [];

// Save Transaction - Shows adjustment confirmation first
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
        const freeQty = parseFloat(row.querySelector('input[name*="[free_qty]"]')?.value) || 0;
        const discountPercent = parseFloat(row.querySelector('input[name*="[discount_percent]"]')?.value) || 0;
        
        if (itemId && qty > 0) {
            const batchIdStr = row.dataset.batchId;
            const batchId = (batchIdStr && batchIdStr !== '' && !isNaN(parseInt(batchIdStr))) 
                ? parseInt(batchIdStr) 
                : null;
            
            const itemData = {
                item_id: itemId,
                batch_id: batchId,
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                name: row.querySelector('input[name*="[name]"]')?.value || '',
                batch_no: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: qty,
                free_qty: freeQty,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0,
                discount_percent: discountPercent,
                ft_rate: parseFloat(row.querySelector('input[name*="[ft_rate]"]')?.value) || 0,
                ft_amount: parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0
            };
            
            if (row.dataset.isNewBatch === 'true' && row.dataset.newBatchData) {
                try {
                    const newBatchData = JSON.parse(row.dataset.newBatchData);
                    itemData.new_batch_data = newBatchData;
                    itemData.new_batch_s_rate = newBatchData.s_rate;
                    itemData.new_batch_location = newBatchData.location;
                    itemData.new_batch_pur_rate = newBatchData.pur_rate;
                    itemData.new_batch_ws_rate = newBatchData.ws_rate;
                    itemData.new_batch_spl_rate = newBatchData.spl_rate;
                } catch(e) {}
            }
            
            items.push(itemData);
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const supplierSelect = document.getElementById('supplier_id');
    
    // Store pending transaction data
    pendingTransactionData = {
        _token: '{{ csrf_token() }}',
        rr_no: document.getElementById('rr_no')?.value || '',
        transaction_date: document.getElementById('transaction_date')?.value || '',
        supplier_id: supplierId,
        supplier_name: supplierSelect?.options[supplierSelect.selectedIndex]?.dataset.name || '',
        total_amount: document.getElementById('total_amount')?.value || '0',
        p_scm_percent: document.getElementById('p_scm_percent')?.value || '0',
        p_scm_amount: document.getElementById('p_scm_amount')?.value || '0',
        s_scm_percent: document.getElementById('s_scm_percent')?.value || '0',
        s_scm_amount: document.getElementById('s_scm_amount')?.value || '0',
        packing: document.getElementById('packing')?.value || '',
        unit: document.getElementById('unit')?.value || '',
        cl_qty: document.getElementById('cl_qty')?.value || '0',
        comp: document.getElementById('comp')?.value || '',
        lctn: document.getElementById('lctn')?.value || '',
        amt: document.getElementById('amt')?.value || '0',
        srlno: document.getElementById('srlno')?.value || '',
        items: items
    };
    
    // Show adjustment confirmation modal
    showAdjustmentConfirmModal();
}

// Show Adjustment Confirmation Modal
function showAdjustmentConfirmModal() {
    const totalAmount = parseFloat(pendingTransactionData.total_amount) || 0;
    
    let html = `
        <div class="batch-modal-backdrop show" id="adjustConfirmBackdrop"></div>
        <div class="batch-modal show" id="adjustConfirmModal" style="max-width: 400px;">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>Amount Adjustment</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeAdjustConfirmModal()"></button>
            </div>
            <div class="modal-body-custom text-center py-4">
                <p class="mb-3">Total Amount: <strong class="text-success fs-5">₹${totalAmount.toFixed(2)}</strong></p>
                <p class="mb-0">Do you want to adjust this amount against Purchase Return transactions?</p>
            </div>
            <div class="modal-footer-custom d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-success" onclick="openPurchaseReturnAdjustmentModal()">
                    <i class="bi bi-check-circle me-1"></i> Yes, Adjust
                </button>
                <button type="button" class="btn btn-secondary" onclick="saveWithoutAdjustment()">
                    <i class="bi bi-x-circle me-1"></i> No, Save Directly
                </button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

// Close Adjustment Confirmation Modal
function closeAdjustConfirmModal() {
    document.getElementById('adjustConfirmModal')?.remove();
    document.getElementById('adjustConfirmBackdrop')?.remove();
}

// Save Without Adjustment
function saveWithoutAdjustment() {
    closeAdjustConfirmModal();
    pendingTransactionData.adjustments = [];
    submitTransaction();
}

// Open Purchase Return Adjustment Modal
function openPurchaseReturnAdjustmentModal() {
    closeAdjustConfirmModal();
    
    const supplierId = pendingTransactionData.supplier_id;
    const totalAmount = parseFloat(pendingTransactionData.total_amount) || 0;
    
    // Fetch purchase returns for this supplier
    fetch(`{{ url('admin/replacement-received/supplier-purchase-returns') }}/${supplierId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPurchaseReturnAdjustmentModal(data.purchase_returns, totalAmount);
            } else {
                alert('Error loading purchase returns: ' + (data.message || 'Unknown error'));
                saveWithoutAdjustment();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading purchase returns');
            saveWithoutAdjustment();
        });
}

// Show Purchase Return Adjustment Modal
function showPurchaseReturnAdjustmentModal(purchaseReturns, totalAmount) {
    adjustmentData = [];
    
    let html = `
        <div class="batch-modal-backdrop show" id="prAdjustBackdrop"></div>
        <div class="batch-modal show" id="prAdjustModal" style="max-width: 800px;">
            <div class="modal-header-custom" style="background: #6f42c1;">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Purchase Return Adjustment</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closePRAdjustModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                            <tr>
                                <th style="width: 50px;">SRNO.</th>
                                <th style="width: 100px;">TRANS NO.</th>
                                <th style="width: 100px;">DATE</th>
                                <th class="text-end" style="width: 110px;">BILL AMT.</th>
                                <th class="text-end" style="width: 110px;">BALANCE</th>
                                <th class="text-end" style="width: 120px;">ADJUST AMT.</th>
                            </tr>
                        </thead>
                        <tbody id="prAdjustTableBody">`;
    
    if (purchaseReturns.length === 0) {
        html += `<tr><td colspan="6" class="text-center text-muted py-3">No purchase returns found for this supplier</td></tr>`;
    } else {
        purchaseReturns.forEach((pr, index) => {
            const balance = parseFloat(pr.balance_amount);
            html += `
                <tr data-pr-id="${pr.id}" data-original-balance="${balance}">
                    <td class="text-center">${index + 1}</td>
                    <td><strong>${pr.pr_no}</strong></td>
                    <td>${pr.return_date}</td>
                    <td class="text-end">${parseFloat(pr.total_amount).toFixed(2)}</td>
                    <td class="text-end text-primary fw-bold balance-cell" data-original="${balance}">${balance.toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end adjust-amount-input" 
                               data-pr-id="${pr.id}" data-max-balance="${balance}"
                               step="0.01" min="0" max="${balance}" value="0"
                               oninput="updateRowBalance(this)" onchange="updateRowBalance(this)">
                    </td>
                </tr>`;
        });
    }
    
    html += `</tbody></table></div>
            </div>
            <div class="modal-footer-custom" style="background: #f0f0f0;">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <span class="text-danger small">EXIT : &lt;ESC&gt;</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span>Total Amount: <strong class="text-success">₹${totalAmount.toFixed(2)}</strong></span>
                        <span>Adjusted: <strong class="text-primary" id="totalAdjustedDisplay">₹0.00</strong></span>
                        <span>Balance: <strong class="text-danger" id="remainingBalanceDisplay">₹${totalAmount.toFixed(2)}</strong></span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="saveWithAdjustments()">
                            <i class="bi bi-check-circle me-1"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closePRAdjustModal()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.addEventListener('keydown', handlePRAdjustEsc);
}

// Update row balance instantly when adjustment amount changes
function updateRowBalance(input) {
    const row = input.closest('tr');
    const balanceCell = row.querySelector('.balance-cell');
    const originalBalance = parseFloat(input.dataset.maxBalance) || 0;
    const adjustAmount = parseFloat(input.value) || 0;
    
    // Validate amount doesn't exceed original balance
    if (adjustAmount > originalBalance) {
        input.value = originalBalance;
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
    }
    
    // Update balance cell instantly
    const newBalance = originalBalance - Math.min(adjustAmount, originalBalance);
    balanceCell.textContent = newBalance.toFixed(2);
    
    // Update totals
    updateAdjustmentTotals();
}

// Handle ESC key for PR Adjustment Modal
function handlePRAdjustEsc(e) {
    if (e.key === 'Escape') {
        closePRAdjustModal();
    }
}

// Close PR Adjustment Modal
function closePRAdjustModal() {
    document.getElementById('prAdjustModal')?.remove();
    document.getElementById('prAdjustBackdrop')?.remove();
    document.removeEventListener('keydown', handlePRAdjustEsc);
}

// Update Adjustment Totals
function updateAdjustmentTotals() {
    const totalAmount = parseFloat(pendingTransactionData.total_amount) || 0;
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjust-amount-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const maxBalance = parseFloat(input.dataset.maxBalance) || 0;
        totalAdjusted += Math.min(amount, maxBalance);
    });
    
    // Validate total doesn't exceed transaction amount
    const adjustedEl = document.getElementById('totalAdjustedDisplay');
    const balanceEl = document.getElementById('remainingBalanceDisplay');
    
    if (totalAdjusted > totalAmount) {
        adjustedEl.classList.add('text-danger');
        adjustedEl.classList.remove('text-primary');
    } else {
        adjustedEl.classList.remove('text-danger');
        adjustedEl.classList.add('text-primary');
    }
    
    adjustedEl.textContent = '₹' + totalAdjusted.toFixed(2);
    balanceEl.textContent = '₹' + (totalAmount - totalAdjusted).toFixed(2);
}

// Save With Adjustments
function saveWithAdjustments() {
    const totalAmount = parseFloat(pendingTransactionData.total_amount) || 0;
    let totalAdjusted = 0;
    const adjustments = [];
    
    document.querySelectorAll('.adjust-amount-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const prId = input.dataset.prId;
        
        if (amount > 0) {
            adjustments.push({
                purchase_return_id: prId,
                amount: amount
            });
            totalAdjusted += amount;
        }
    });
    
    // Validate total doesn't exceed transaction amount
    if (totalAdjusted > totalAmount) {
        alert('Total adjusted amount cannot exceed transaction amount!');
        return;
    }
    
    closePRAdjustModal();
    pendingTransactionData.adjustments = adjustments;
    submitTransaction();
}

// Submit Transaction to Server
function submitTransaction() {
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.replacement-received.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(pendingTransactionData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Replacement received saved successfully! RR No: ' + result.rr_no);
            window.location.href = '{{ route("admin.replacement-received.index") }}';
        } else {
            alert('Error: ' + (result.message || 'Failed to save'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving replacement received');
    });
}
</script>
@endpush
