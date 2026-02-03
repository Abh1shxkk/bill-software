@extends('layouts.admin')

@section('title', 'Godown Breakage/Expiry - Transaction')

@push('styles')
<style>
    .gbe-form { font-size: 11px; }
    .gbe-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .gbe-form input, .gbe-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #87CEEB; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input, .table-compact select { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-selected td { background-color: #d4edff !important; }
    .row-complete { background-color: #d4edda !important; }
    .row-h-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #17a2b8; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
    .batch-row:hover { background-color: #fff3cd !important; cursor: pointer; }
</style>
@endpush

@section('content')
<section class="gbe-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-seam me-2"></i> Godown Breakage/Expiry - Transaction</h4>
                <div class="text-muted small">Record breakage and expiry items from godown</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.godown-breakage-expiry.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="gbeForm" method="POST" autocomplete="off">
                    @csrf
                    
                    <!-- Header Section -->
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
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $trnNo }}" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="field-group">
                                    <label style="width: 70px;">Narration :</label>
                                    <input type="text" id="narration" name="narration" class="form-control" placeholder="Enter narration/remarks...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;" id="itemsTableContainer">
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
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="showItemSelectionModal()">
                                <i class="bi bi-search"></i> Add Items
                            </button>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Total :</label>
                            <input type="text" id="total_amount" name="total_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="field-group mb-1">
                                            <label style="width: 50px;">Packing :</label>
                                            <input type="text" id="packing" class="form-control readonly-field" readonly>
                                        </div>
                                        <div class="field-group mb-1">
                                            <label style="width: 50px;">Unit :</label>
                                            <input type="text" id="unit" class="form-control readonly-field" readonly>
                                        </div>
                                        <div class="field-group">
                                            <label style="width: 50px;">Cl.Qty :</label>
                                            <input type="text" id="cl_qty" class="form-control readonly-field text-end" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="field-group mb-1">
                                            <label style="width: 50px;">Pur.Rate :</label>
                                            <input type="text" id="p_rate" class="form-control readonly-field text-end" readonly>
                                        </div>
                                        <div class="field-group mb-1">
                                            <label style="width: 50px;">S.Rate :</label>
                                            <input type="text" id="s_rate" class="form-control readonly-field text-end" readonly>
                                        </div>
                                        <div class="field-group">
                                            <label style="width: 50px;">Mrp :</label>
                                            <input type="text" id="mrp" class="form-control readonly-field text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Comp :</label>
                                    <input type="text" id="company_name" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Lctn :</label>
                                    <input type="text" id="location" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
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
                            <button type="button" class="btn btn-secondary" onclick="cancelTransaction()">
                                <i class="bi bi-x-circle"></i> Cancel Godown Brk./Expiry
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
    'id' => 'godownBreakageExpiryItemModal',
    'module' => 'godown-breakage-expiry',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'godownBreakageExpiryBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'godownBreakageExpiryBatchModal',
    'module' => 'godown-breakage-expiry',
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
    // Initially no row - user will click Add Row button
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
        .then(data => {
            itemsData = data || [];
        })
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
    console.log('🎯 Godown Breakage Expiry: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Godown Breakage Expiry: Invalid item data received');
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
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
            <input type="hidden" name="items[${rowIndex}][s_rate]" value="${itemData.s_rate || 0}">
            <input type="hidden" name="items[${rowIndex}][p_rate]" value="${cost}">
        `;
        
        tbody.appendChild(row);
        row.classList.add('row-complete');
        selectRow(rowIndex);
        calculateRowAmount(rowIndex);
        
        console.log('✅ Godown Breakage Expiry: New row created successfully', rowIndex);
        
        // Focus qty field
        setTimeout(() => {
            row.querySelector('input[name*="[qty]"]')?.focus();
        }, 100);
        
    } else {
        // UPDATE EXISTING ROW
        const row = document.getElementById(`row-${targetRowIndex}`);
        if (!row) {
            console.error('❌ Godown Breakage Expiry: Target row not found', targetRowIndex);
            return;
        }
        
        row.dataset.itemId = itemData.id;
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
        row.querySelector('input[name*="[company_name]"]').value = itemData.company_name || '';
        row.querySelector('input[name*="[mrp]"]').value = itemData.mrp || 0;
        row.querySelector('input[name*="[s_rate]"]').value = itemData.s_rate || 0;
        row.querySelector('input[name*="[p_rate]"]').value = cost;
        
        row.classList.add('row-complete');
        calculateRowAmount(targetRowIndex);
        
        console.log('✅ Godown Breakage Expiry: Existing row updated successfully', targetRowIndex);
    }
    
    // Update footer
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('p_rate').value = itemData.p_rate || itemData.pur_rate || '0';
    document.getElementById('s_rate').value = itemData.s_rate || '0';
    document.getElementById('mrp').value = itemData.mrp || '0';
    document.getElementById('company_name').value = itemData.company_name || '';
    document.getElementById('cl_qty').value = batchData?.qty || batchData?.available_qty || '0';
    document.getElementById('location').value = batchData?.location || '';
    
    calculateTotalAmount();
    
    // Clear target row index
    window.targetRowIndexForModal = null;
    
    console.log('✅ Godown Breakage Expiry: Footer updated, targetRowIndex cleared');
}

/**
 * Bridge function to open item selection modal
 * Supports targetRowIndex parameter for updating existing rows
 * @param {number|null} targetRowIndex - Row index to update, or null to create new row
 */
function showItemSelectionModal(targetRowIndex = null) {
    console.log('🎯 Godown Breakage Expiry: showItemSelectionModal called', {targetRowIndex});
    
    // Store targetRowIndex for later use
    window.targetRowIndexForModal = targetRowIndex;
    
    // Check if modal component function exists
    if (typeof window.openItemModal_godownBreakageExpiryItemModal === 'function') {
        console.log('✅ Godown Breakage Expiry: Opening reusable item modal');
        window.openItemModal_godownBreakageExpiryItemModal();
    } else {
        console.error('❌ Godown Breakage Expiry: openItemModal_godownBreakageExpiryItemModal function not found. Modal component may not be loaded.');
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}

// ============================================================================
// LEGACY FUNCTIONS (Kept as fallback, prefixed with _legacy_)
// ============================================================================

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
        <td>
            <select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
                <option value="BREAKAGE">Brk</option>
                <option value="EXPIRY">Exp</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="0" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="">
        <input type="hidden" name="items[${rowIndex}][unit]" value="">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
        <input type="hidden" name="items[${rowIndex}][s_rate]" value="0">
        <input type="hidden" name="items[${rowIndex}][p_rate]" value="0">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

function _legacy_showItemSelectionModal(targetRowIndex = null) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onclick="showBatchModal(${rowIndex})" readonly style="cursor: pointer;"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" readonly></td>
        <td>
            <select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
                <option value="BREAKAGE">Brk</option>
                <option value="EXPIRY">Exp</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="0" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="">
        <input type="hidden" name="items[${rowIndex}][unit]" value="">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
        <input type="hidden" name="items[${rowIndex}][s_rate]" value="0">
        <input type="hidden" name="items[${rowIndex}][p_rate]" value="0">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => {
        tr.classList.remove('row-selected');
    });
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    if (!row) return;
    
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    const batchData = row.dataset.batchData ? JSON.parse(row.dataset.batchData) : {};
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '';
    document.getElementById('p_rate').value = itemData.p_rate || '0';
    document.getElementById('s_rate').value = itemData.s_rate || '0';
    document.getElementById('mrp').value = itemData.mrp || '0';
    document.getElementById('company_name').value = itemData.company_name || '';
    document.getElementById('cl_qty').value = batchData.qty || '0';
    document.getElementById('location').value = batchData.location || '';
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const code = event.target.value.trim();
        if (code) {
            findItemByCode(code, rowIndex);
        } else {
            showItemSelectionModal(rowIndex);
        }
    }
}

function findItemByCode(code, rowIndex) {
    const item = itemsData.find(i => i.id == code || i.name.toLowerCase().includes(code.toLowerCase()));
    if (item) {
        selectItemForRow(item, rowIndex);
    } else {
        showItemSelectionModal(rowIndex);
    }
}

function _legacy_showItemSelectionModal_OLD(targetRowIndex = null) {
    // Store if we need to create new row or use existing
    window.createNewRowAfterSelection = (targetRowIndex === null);
    const rowIndex = targetRowIndex !== null ? targetRowIndex : -1;
    
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="_legacy_closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by name or code..." onkeyup="_legacy_filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-info" style="position: sticky; top: 0;">
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>Company</th>
                                <th class="text-end">MRP</th>
                            </tr>
                        </thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.body.dataset.targetRowIndex = rowIndex;
    document.getElementById('itemSearchInput').focus();
    _legacy_renderItemsList();
}

function _legacy_renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = filter ? itemsData.filter(item => 
        item.name.toLowerCase().includes(filter.toLowerCase()) || 
        item.id.toString().includes(filter)
    ) : itemsData;
    
    tbody.innerHTML = filtered.slice(0, 100).map(item => `
        <tr class="item-row" onclick="_legacy_selectItem(${item.id})" style="cursor: pointer;">
            <td>${item.id}</td>
            <td>${item.name}</td>
            <td>${item.packing || '-'}</td>
            <td>${item.company_name || '-'}</td>
            <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function _legacy_filterItems() {
    const search = document.getElementById('itemSearchInput').value;
    _legacy_renderItemsList(search);
}

function _legacy_selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (item) {
        // Store selected item for batch selection
        window.selectedItemData = item;
        _legacy_closeItemModal();
        // Show batch modal - row will be created after batch selection
        _legacy_showBatchModalForItem(item);
    }
}

function _legacy_showBatchModalForItem(item) {
    let html = `
        <div class="batch-modal-backdrop show" id="batchModalBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #ffc107; color: #000;">
                <h5 class="mb-0"><i class="bi bi-box me-2"></i>Select Batch - ${item.name}</h5>
                <button type="button" class="btn-close" onclick="_legacy_closeBatchModalAndClear()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="text-center py-3">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="mt-2">Loading batches...</div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeBatchModalAndClear()">Cancel</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#batchModal .modal-body-custom');
            // Handle response format: { success: true, batches: [...] }
            const batches = data.batches || data || [];
            
            if (!batches || batches.length === 0) {
                modalBody.innerHTML = '<div class="text-center text-muted py-3">No batches available for this item</div>';
                return;
            }
            
            modalBody.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning">
                            <tr>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">MRP</th>
                                <th class="text-end">P.Rate</th>
                                <th>Company</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${batches.map(batch => `
                                <tr class="batch-row" onclick="_legacy_selectBatchAndCreateRow('${batch.id}', '${(batch.batch_no || '').replace(/'/g, "\\'")}', '${(batch.expiry_display || batch.expiry || '').replace(/'/g, "\\'")}', ${batch.qty || 0}, ${batch.mrp || 0}, ${batch.pur_rate || batch.cost || 0}, '${(batch.company_name || '').replace(/'/g, "\\'")}')" style="cursor: pointer;">
                                    <td>${batch.batch_no || '-'}</td>
                                    <td>${batch.expiry_display || '-'}</td>
                                    <td class="text-end">${batch.qty || 0}</td>
                                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                                    <td class="text-end">${parseFloat(batch.pur_rate || batch.cost || 0).toFixed(2)}</td>
                                    <td>${batch.company_name || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>`;
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            document.querySelector('#batchModal .modal-body-custom').innerHTML = '<div class="text-center text-danger py-3">Error loading batches</div>';
        });
}

function _legacy_selectBatchAndCreateRow(batchId, batchNo, expiry, qty, mrp, pRate, location) {
    const item = window.selectedItemData;
    if (!item) return;
    
    // Create new row
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchNo}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiry}" readonly></td>
        <td>
            <select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
                <option value="BREAKAGE">Brk</option>
                <option value="EXPIRY">Exp</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="1" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="${pRate || item.p_rate || 0}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${pRate || item.p_rate || 0}" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchId}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${mrp || item.mrp || 0}">
        <input type="hidden" name="items[${rowIndex}][s_rate]" value="${item.s_rate || 0}">
        <input type="hidden" name="items[${rowIndex}][p_rate]" value="${pRate || item.p_rate || 0}">
    `;
    
    tbody.appendChild(row);
    
    // Store data for footer
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify({
        packing: item.packing || '',
        unit: item.unit || '',
        mrp: mrp || item.mrp || 0,
        s_rate: item.s_rate || 0,
        p_rate: pRate || item.p_rate || 0,
        company_name: item.company_name || ''
    });
    row.dataset.batchData = JSON.stringify({ qty: qty, location: location });
    
    row.classList.add('row-complete');
    selectRow(rowIndex);
    calculateRowAmount(rowIndex);
    calculateTotalAmount();
    
    closeBatchModal();
    window.selectedItemData = null;
    
    // Focus qty field
    row.querySelector('input[name*="[qty]"]').focus();
}

function _legacy_closeBatchModalAndClear() {
    _legacy_closeBatchModal();
    window.selectedItemData = null;
}

function selectItemForRow(item, rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[code]"]').value = item.id;
    row.querySelector('input[name*="[name]"]').value = item.name;
    row.querySelector('input[name*="[item_id]"]').value = item.id;
    row.querySelector('input[name*="[packing]"]').value = item.packing || '';
    row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
    row.querySelector('input[name*="[mrp]"]').value = item.mrp || 0;
    row.querySelector('input[name*="[s_rate]"]').value = item.s_rate || 0;
    row.querySelector('input[name*="[p_rate]"]').value = item.p_rate || 0;
    row.querySelector('input[name*="[cost]"]').value = item.p_rate || 0;
    
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify({
        packing: item.packing || '',
        unit: item.unit || '',
        mrp: item.mrp || 0,
        s_rate: item.s_rate || 0,
        p_rate: item.p_rate || 0,
        company_name: item.company_name || ''
    });
    
    updateFooterFromRow(row);
    _legacy_showBatchModal(rowIndex);
}

function _legacy_closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemModalBackdrop')?.remove();
}

function _legacy_showBatchModal(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    if (!itemId) {
        alert('Please select an item first');
        return;
    }
    
    let html = `
        <div class="batch-modal-backdrop show" id="batchModalBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #ffc107; color: #000;">
                <h5 class="mb-0"><i class="bi bi-box me-2"></i>Select Batch</h5>
                <button type="button" class="btn-close" onclick="_legacy_closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="text-center py-3">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="mt-2">Loading batches...</div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeBatchModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.body.dataset.batchRowIndex = rowIndex;
    
    fetch(`{{ url('admin/api/item-batches') }}/${itemId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#batchModal .modal-body-custom');
            // Handle response format: { success: true, batches: [...] }
            const batches = data.batches || data || [];
            
            if (!batches || batches.length === 0) {
                modalBody.innerHTML = '<div class="text-center text-muted py-3">No batches available for this item</div>';
                return;
            }
            
            modalBody.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning">
                            <tr>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">MRP</th>
                                <th>Company</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${batches.map(batch => `
                                <tr class="batch-row" onclick="_legacy_selectBatch(${batch.id}, '${(batch.batch_no || '').replace(/'/g, "\\'")}', '${(batch.expiry_display || '').replace(/'/g, "\\'")}', ${batch.qty || 0}, ${batch.mrp || 0}, ${batch.pur_rate || batch.cost || 0}, '${(batch.company_name || '').replace(/'/g, "\\'")}')" style="cursor: pointer;">
                                    <td>${batch.batch_no || '-'}</td>
                                    <td>${batch.expiry_display || '-'}</td>
                                    <td class="text-end">${batch.qty || 0}</td>
                                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                                    <td>${batch.company_name || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>`;
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            document.querySelector('#batchModal .modal-body-custom').innerHTML = '<div class="text-center text-danger py-3">Error loading batches</div>';
        });
}

function _legacy_selectBatch(batchId, batchNo, expiry, qty, mrp, pRate, location) {
    const rowIndex = parseInt(document.body.dataset.batchRowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[batch]"]').value = batchNo;
    row.querySelector('input[name*="[expiry]"]').value = expiry;
    row.querySelector('input[name*="[batch_id]"]').value = batchId;
    row.querySelector('input[name*="[cost]"]').value = pRate || 0;
    
    row.dataset.batchId = batchId;
    row.dataset.batchData = JSON.stringify({ qty: qty, location: location });
    
    row.classList.add('row-complete');
    updateFooterFromRow(row);
    closeBatchModal();
    
    row.querySelector('input[name*="[qty]"]').focus();
}

function _legacy_closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchModalBackdrop')?.remove();
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const cost = parseFloat(row.querySelector('input[name*="[cost]"]').value) || 0;
    const amount = qty * cost;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        total += amount;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
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

function saveTransaction() {
    const form = document.getElementById('gbeForm');
    const formData = new FormData(form);
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let validRows = 0;
    let totalQty = 0;
    
    rows.forEach(row => {
        const itemId = row.querySelector('input[name*="[item_id]"]')?.value;
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        if (itemId && qty > 0) {
            validRows++;
            totalQty += qty;
        }
    });
    
    if (validRows === 0) {
        alert('Please add at least one item with quantity');
        return;
    }
    
    formData.append('total_qty', totalQty);
    
    fetch('{{ route("admin.godown-breakage-expiry.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + '\nTRN No: ' + data.trn_no);
            window.location.href = '{{ route("admin.godown-breakage-expiry.index") }}';
        } else {
            alert(data.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
        window.location.href = '{{ route("admin.godown-breakage-expiry.index") }}';
    }
}
</script>
@endpush
