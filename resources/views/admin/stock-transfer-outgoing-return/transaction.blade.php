@extends('layouts.admin')

@section('title', 'Stock Transfer Outgoing Return - Transaction')

@section('content')
<style>
    /* Compact form adjustments */
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .header-row { display: flex; align-items: center; gap: 15px; margin-bottom: 6px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .gr-section { background: #f0f0f0; border: 1px solid #ccc; padding: 8px; margin-bottom: 8px; border-radius: 4px; }
    
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; }
    .table-compact td { border: 1px solid #dee2e6; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    
    #itemsTableContainer { max-height: 280px !important; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-selected td { background-color: #d4edff !important; }
    
    .net-section { background: #ffcccc; padding: 8px; border: 1px solid #cc0000; margin-top: 8px; border-radius: 4px; }
    .footer-section { background: #ffe4c4; padding: 8px; border: 1px solid #deb887; margin-top: 8px; border-radius: 4px; }
    .btn-section { background: #f0f0f0; padding: 8px; border: 1px solid #ccc; margin-top: 8px; text-align: center; border-radius: 4px; }

    /* Modal Styles */
    .item-modal-backdrop, .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .item-modal-backdrop.show, .batch-modal-backdrop.show { display: block; }
    .item-modal, .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .item-modal.show, .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #17a2b8; color: white; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; border-radius: 0 0 8px 8px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-in-left me-2"></i> Stock Transfer Outgoing Return - Transaction</h4>
        <div class="text-muted small">Create new stock transfer outgoing return transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.stock-transfer-outgoing-return.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-list-ul me-1"></i> View All
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body p-3">
        <form id="stockTransferOutgoingForm" method="POST" autocomplete="off" onkeydown="return event.key !== 'Enter';">
            @csrf

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Date:</label>
                            <input type="date" class="form-control form-control-sm" name="transaction_date" style="width: 130px;" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="field-group">
                            <label>Name:</label>
                            <select class="form-control form-control-sm" name="transfer_to" id="customerSelect" style="flex: 1; background: #e8ffe8; border: 2px solid #28a745;" onchange="updateCustomerName()">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->code ?? $customer->id }} - {{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="transfer_to_name" id="transfer_to_name">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Trf. Return No.:</label>
                            <input type="text" class="form-control form-control-sm" name="trf_return_no" id="trf_return_no" style="width: 120px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="field-group">
                            <label>Remarks:</label>
                            <input type="text" class="form-control form-control-sm" name="remarks" placeholder="Remarks">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Trn.No:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" name="trn_no" value="{{ $nextSrNo ?? '1' }}" readonly style="width: 80px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- GR Section -->
            <div class="gr-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>GR No.:</label>
                            <input type="text" class="form-control form-control-sm" name="gr_no" style="width: 100px;">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>GR Date:</label>
                            <input type="date" class="form-control form-control-sm" name="gr_date" style="width: 130px;" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Cases:</label>
                            <input type="number" class="form-control form-control-sm" name="cases" style="width: 70px;" value="0">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Transport:</label>
                            <input type="text" class="form-control form-control-sm" name="transport" style="width: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 280px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Expiry</th>
                                <th style="width: 60px;">Qty</th>
                                <th style="width: 80px;">Rate</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 60px;">Action</th>
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
                    <button type="button" class="btn btn-sm btn-info" onclick="openInsertItemsModal()">
                        <i class="bi bi-list-check"></i> Insert Items
                    </button>
                </div>
            </div>

            <!-- Net Section -->
            <div class="net-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Net:</label>
                            <input type="text" class="form-control form-control-sm readonly-field text-end" id="summary_net" name="summary_net" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="footer-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Pack:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_pack" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Comp:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_comp" style="width: 100px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Unit:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_unit" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Lctn:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_lctn" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Cl.Qty:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_cl_qty" style="width: 70px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>SrNo:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_srno" style="width: 60px;" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Section -->
            <div class="btn-section">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveTransaction()">
                    <i class="bi bi-check-circle me-1"></i> Save (End)
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()">
                    <i class="bi bi-trash me-1"></i> Delete Item
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="cancelTransfer()">
                    <i class="bi bi-x-circle me-1"></i> Cancel Transfer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'stockTransferOutgoingReturnItemModal',
    'module' => 'stock-transfer-outgoing-return',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'stockTransferOutgoingReturnBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'stockTransferOutgoingReturnBatchModal',
    'module' => 'stock-transfer-outgoing-return',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection

@push('scripts')
<script>
let currentRowIndex = 0;
let selectedRowIndex = null;
let itemsData = [];
let pendingItemForBatch = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadItems();
});

// Load Items from API (for legacy row-based lookup)
function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data.items || [];
            console.log('Items loaded:', itemsData.length);
        })
        .catch(error => console.error('Error loading items:', error));
}

// ====== NEW MODAL COMPONENT BRIDGE ======
// Open Insert Items Modal using new component
function openInsertItemsModal() {
    console.log('📦 Opening stock transfer outgoing return item modal');
    if (typeof openItemModal_stockTransferOutgoingReturnItemModal === 'function') {
        openItemModal_stockTransferOutgoingReturnItemModal();
    } else {
        console.error('❌ Item modal function not found');
    }
}

// Callback when item and batch are selected from new modal component
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Stock Transfer Outgoing Return - Item+Batch selected:', item?.name, batch?.batch_no);
    console.log('Item data:', item);
    console.log('Batch data:', batch);
    addItemToTable(item, batch);
};

window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

window.onItemSelectedFromModal = function(item) {
    console.log('🔗 Item selected, opening batch modal for:', item?.name);
    if (typeof openBatchModal_stockTransferOutgoingReturnBatchModal === 'function') {
        openBatchModal_stockTransferOutgoingReturnBatchModal(item);
    } else {
        console.error('❌ Batch modal function not found');
    }
};
// ====== END MODAL COMPONENT BRIDGE ======

function updateCustomerName() {
    const select = document.getElementById('customerSelect');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        document.getElementById('transfer_to_name').value = selectedOption.getAttribute('data-name') || '';
    } else {
        document.getElementById('transfer_to_name').value = '';
    }
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
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="0" min="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][rate]" value="0" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button></td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
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
    
    document.getElementById('detail_pack').value = itemData.packing || '';
    document.getElementById('detail_comp').value = itemData.company_short_name || itemData.mfg_by || '';
    document.getElementById('detail_unit').value = itemData.unit || '';
    document.getElementById('detail_lctn').value = batchData.location || itemData.location || '';
    document.getElementById('detail_cl_qty').value = batchData.qty || itemData.total_qty || '0';
    document.getElementById('detail_srno').value = (parseInt(row.dataset.rowIndex) || 0) + 1;
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
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id;
    
    updateFooterFromRow(row);
    row.querySelector('input[name*="[batch]"]').focus();
}

// Handle Batch field keydown
function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        const itemId = row?.dataset.itemId;
        
        if (itemId && batchInput && batchInput.value.trim()) {
            // Check if batch exists, if not show create batch modal
            checkBatchAndCreate(rowIndex);
        } else if (itemId) {
            // Open batch selection modal
            const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
            fetchBatchesForItem(itemId, rowIndex, itemData);
        }
    }
}

// Check Batch - if exists select it, if not show create batch modal
function checkBatchAndCreate(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const batchNo = row.querySelector('input[name*="[batch]"]').value.trim();
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    if (!itemId || !batchNo) return;
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists && data.batches && data.batches.length > 0) {
                // Batch exists - check if has stock
                const availableBatches = data.batches.filter(b => (b.qty || 0) > 0);
                if (availableBatches.length > 0) {
                    // Auto-select first batch with stock
                    const batch = availableBatches[0];
                    row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
                    if (batch.expiry_date) {
                        const d = new Date(batch.expiry_date);
                        row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
                    }
                    row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2);
                    row.dataset.batchId = batch.id;
                    row.dataset.batchData = JSON.stringify(batch);
                    updateFooterFromRow(row);
                    row.querySelector('input[name*="[qty]"]')?.focus();
                } else {
                    // Batch exists but no stock - show create new batch modal
                    showCreateBatchModal(rowIndex, batchNo, itemData);
                }
            } else {
                // Batch doesn't exist - show create new batch modal
                showCreateBatchModal(rowIndex, batchNo, itemData);
            }
        })
        .catch(error => {
            console.error('Error checking batch:', error);
            // On error, show create batch modal
            showCreateBatchModal(rowIndex, batchNo, itemData);
        });
}

// Check Batch (legacy)
function checkBatch(rowIndex) {
    checkBatchAndCreate(rowIndex);
}

// Fetch Batches for Item
function fetchBatchesForItem(itemId, rowIndex, itemData) {
    fetch(`{{ url('admin/api/item-batches') }}/${itemId}`)
        .then(response => response.json())
        .then(data => {
            const availableBatches = (data.batches || []).filter(b => (b.qty || 0) > 0);
            if (availableBatches.length > 0) {
                showBatchSelectionModal(availableBatches, rowIndex, itemData);
            } else {
                alert('No batches with stock available for this item');
            }
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
        });
}

// Show Batch Selection Modal
function showBatchSelectionModal(batches, rowIndex, itemData) {
    const availableBatches = batches.filter(b => (b.qty || 0) > 0);
    if (availableBatches.length === 0) {
        alert('No batches with stock available');
        return;
    }
    
    const totalAvailableStock = availableBatches.reduce((sum, b) => sum + (b.qty || 0), 0);
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0">Select Batch - ${itemData.name || ''}</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-2">
                    <strong>Total Available Stock:</strong> <span class="text-success fw-bold">${totalAvailableStock}</span>
                </div>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-light">
                            <tr>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th class="text-end">MRP</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Avl. Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
    availableBatches.forEach(batch => {
        const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '';
        html += `
            <tr style="cursor: pointer;" ondblclick='selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                <td><strong>${batch.batch_no || ''}</strong></td>
                <td>${expiry}</td>
                <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end"><strong>${batch.qty || 0}</strong></td>
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
    row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2);
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    updateFooterFromRow(row);
    closeBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

// Close Batch Modal
function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

// OLD LEGACY - Renamed to avoid conflict with new component bridge
function _legacy_openInsertItemsModal() {
    let html = `
        <div class="item-modal-backdrop show" id="itemBackdrop"></div>
        <div class="item-modal show" id="itemModal">
            <div class="modal-header-custom" style="background: #198754;">
                <h5 class="mb-0"><i class="bi bi-plus-square me-1"></i> Insert Items</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <input type="text" class="form-control mb-2" id="itemSearchInput" placeholder="Search by item name or code..." onkeyup="filterInsertItemList()" autofocus>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm" style="font-size: 11px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th>Item Name</th>
                                <th style="width: 80px;">Packing</th>
                                <th style="width: 70px;">MRP</th>
                                <th style="width: 70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="insertItemListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    displayInsertItemList(itemsData);
    document.getElementById('itemSearchInput')?.focus();
}

// Display Insert Item List
function displayInsertItemList(items) {
    const tbody = document.getElementById('insertItemListBody');
    if (!tbody) return;
    
    tbody.innerHTML = items.slice(0, 100).map(item => `
        <tr style="cursor: pointer;" ondblclick='selectInsertItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
            <td>${item.id || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.packing || ''}</td>
            <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-success" onclick='selectInsertItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                    <i class="bi bi-check"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Filter Insert Item List
function filterInsertItemList() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = itemsData.filter(i => 
        (i.name || '').toLowerCase().includes(search) || 
        (i.id || '').toString().includes(search)
    );
    displayInsertItemList(filtered);
}

// Select Insert Item
function selectInsertItem(item) {
    pendingItemForBatch = item;
    closeItemModal();
    
    // Fetch batches for this item
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(data => {
            const availableBatches = (data.batches || []).filter(b => (b.qty || 0) > 0);
            if (availableBatches.length > 0) {
                showInsertBatchModal(availableBatches, item);
            } else {
                alert('No batches with stock available for this item');
            }
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
        });
}

// Show Insert Batch Modal
function showInsertBatchModal(batches, item) {
    const availableBatches = batches.filter(b => (b.qty || 0) > 0);
    const totalAvailableStock = availableBatches.reduce((sum, b) => sum + (b.qty || 0), 0);
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch - ${item.name}</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModalAndReopen()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-2">
                    <strong>Total Available Stock:</strong> <span class="text-success fw-bold">${totalAvailableStock}</span>
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm" style="font-size: 11px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                            <tr>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th class="text-end">MRP</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Avl. Qty</th>
                                <th style="width: 70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
    availableBatches.forEach(batch => {
        const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '';
        html += `
            <tr style="cursor: pointer; background: #d4edda;" ondblclick='selectInsertBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                <td><strong>${batch.batch_no || ''}</strong></td>
                <td>${expiry}</td>
                <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end"><strong>${batch.qty || 0}</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success" onclick='selectInsertBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check"></i>
                    </button>
                </td>
            </tr>`;
    });
    
    html += `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItemWithoutBatch()">
                    <i class="bi bi-plus"></i> Add Without Batch
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModalAndReopen()">Back to Items</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

// Select Insert Batch
function selectInsertBatch(batch) {
    closeBatchModal();
    addItemToTable(pendingItemForBatch, batch);
    pendingItemForBatch = null;
}

// Add Item Without Batch - cursor goes to batch field
function addItemWithoutBatch() {
    closeBatchModal();
    addItemToTableWithoutBatch(pendingItemForBatch);
    pendingItemForBatch = null;
}

// Add Item to Table Without Batch (cursor on batch field)
function addItemToTableWithoutBatch(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="0" min="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][rate]" value="${parseFloat(item.s_rate || item.mrp || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button></td>
    `;
    
    tbody.appendChild(row);
    updateFooterFromRow(row);
    selectRow(rowIndex);
    
    // Focus on batch field
    setTimeout(() => {
        row.querySelector('input[name*="[batch]"]')?.focus();
    }, 100);
}

// Close Batch Modal and Reopen Item Modal
function closeBatchModalAndReopen() {
    closeBatchModal();
    pendingItemForBatch = null;
    openInsertItemsModal();
}

// Close Item Modal
function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

// Show Create New Batch Modal
function showCreateBatchModal(rowIndex, batchNo, itemData) {
    // Debug: log itemData to see what fields are available
    console.log('Create Batch Modal - itemData:', itemData);
    
    const html = `
        <div class="batch-modal-backdrop show" id="createBatchBackdrop"></div>
        <div class="batch-modal show" id="createBatchModal" style="max-width: 400px;">
            <div class="modal-header-custom" style="background: #6c757d;">
                <h5 class="mb-0">Opening New Batch</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeCreateBatchModal()"></button>
            </div>
            <div class="modal-body-custom" style="background: #e8e8e8;">
                <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                    <strong>Item Name :</strong> <span style="color: #0d6efd; font-weight: bold;">${itemData.name || ''}</span>
                </div>
                <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                    <strong>Pack :</strong> <span style="color: #0d6efd; font-weight: bold;">${itemData.packing || ''}</span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="font-size: 12px;"><strong>S.Rate :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${parseFloat(itemData.s_rate || 0).toFixed(2)}" step="0.01">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="font-size: 12px;"><strong>Expiry :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="font-size: 12px;"><strong>MRP :</strong></label>
                        <input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${parseFloat(itemData.mrp || 0).toFixed(2)}" step="0.01">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1" style="font-size: 12px;"><strong>Location :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchLocation" value="${itemData.location || ''}">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1" style="font-size: 12px;"><strong>Inclusive :</strong></label>
                        <input type="text" class="form-control form-control-sm" id="newBatchInclusive" value="Y" maxlength="1">
                    </div>
                </div>
                
                <input type="hidden" id="newBatchNo" value="${batchNo}">
                <input type="hidden" id="newBatchRowIndex" value="${rowIndex}">
                <input type="hidden" id="newBatchItemId" value="${itemData.id || ''}">
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveNewBatch()">
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

// Save New Batch - stores data in row, batch created on transaction save
function saveNewBatch() {
    const rowIndex = document.getElementById('newBatchRowIndex').value;
    const batchNo = document.getElementById('newBatchNo').value;
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = document.getElementById('newBatchMRP').value;
    const sRate = document.getElementById('newBatchSRate').value;
    const location = document.getElementById('newBatchLocation').value;
    const inclusive = document.getElementById('newBatchInclusive').value;
    const itemId = document.getElementById('newBatchItemId').value;
    
    if (!batchNo) {
        alert('Batch number is required');
        return;
    }
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batchNo;
        row.querySelector('input[name*="[expiry]"]').value = expiry;
        row.querySelector('input[name*="[rate]"]').value = parseFloat(sRate).toFixed(2);
        
        // Mark as new batch
        row.dataset.batchId = '';
        row.dataset.isNewBatch = 'true';
        row.dataset.newBatchData = JSON.stringify({
            batch_no: batchNo,
            expiry: expiry,
            mrp: parseFloat(mrp),
            s_rate: parseFloat(sRate),
            location: location,
            inclusive: inclusive
        });
    }
    
    closeCreateBatchModal();
    
    // Focus on qty field
    setTimeout(() => {
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }, 100);
}

// Add Item to Table
function addItemToTable(item, batch) {
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
    const rate = batch ? parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2) : '0.00';
    
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batchNo}" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiry}" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="0" min="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][rate]" value="${rate}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button></td>
    `;
    
    tbody.appendChild(row);
    updateFooterFromRow(row);
    selectRow(rowIndex);
    
    setTimeout(() => {
        row.querySelector('input[name*="[qty]"]')?.focus();
    }, 100);
}

// Calculate Row Amount
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    const amount = qty * rate;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    calculateTotals();
}

// Calculate Totals
function calculateTotals() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        total += amount;
    });
    document.getElementById('summary_net').value = total.toFixed(2);
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
    } else {
        alert('Please select a row to delete');
    }
}

// Cancel Transfer
function cancelTransfer() {
    if (confirm('Are you sure you want to cancel this transfer?')) {
        window.location.href = '{{ route("admin.stock-transfer-outgoing-return.index") }}';
    }
}

// Save Transaction
function saveTransaction() {
    const form = document.getElementById('stockTransferOutgoingForm');
    const customerId = document.getElementById('customerSelect').value;
    
    if (!customerId) {
        alert('Please select a customer');
        return;
    }
    
    // Collect items
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemId = row.dataset.itemId;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        
        if (itemId && qty > 0) {
            // Get batch data if available
            const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
            const itemDataStored = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
            
            const itemData = {
                code: itemId, // Controller expects 'code' as item_id
                name: row.querySelector('input[name*="[name]"]')?.value || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: qty,
                s_rate: parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0, // Sale Rate
                mrp: batchData.mrp || itemDataStored.mrp || 0, // MRP from batch or item
                p_rate: batchData.pur_rate || itemDataStored.pur_rate || 0, // Purchase Rate
                amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0
            };
            
            // If new batch was created, include that data
            if (row.dataset.isNewBatch === 'true' && row.dataset.newBatchData) {
                try {
                    const newBatchData = JSON.parse(row.dataset.newBatchData);
                    itemData.new_batch_data = newBatchData;
                    itemData.mrp = newBatchData.mrp || itemData.mrp;
                    itemData.s_rate = newBatchData.s_rate || itemData.s_rate;
                } catch(e) {}
            }
            
            items.push(itemData);
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const data = {
        _token: '{{ csrf_token() }}',
        transaction_date: form.querySelector('[name="transaction_date"]').value,
        transfer_to: customerId,
        transfer_to_name: document.getElementById('transfer_to_name').value,
        trf_return_no: form.querySelector('[name="trf_return_no"]').value,
        remarks: form.querySelector('[name="remarks"]').value,
        gr_no: form.querySelector('[name="gr_no"]').value,
        gr_date: form.querySelector('[name="gr_date"]').value,
        cases: form.querySelector('[name="cases"]').value,
        transport: form.querySelector('[name="transport"]').value,
        summary_net: document.getElementById('summary_net').value,
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.stock-transfer-outgoing-return.transaction.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Transaction saved successfully! Sr No: ' + result.sr_no);
            window.location.href = '{{ route("admin.stock-transfer-outgoing-return.index") }}';
        } else {
            alert('Error: ' + (result.message || 'Failed to save'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving transaction');
    });
}
</script>
@endpush
