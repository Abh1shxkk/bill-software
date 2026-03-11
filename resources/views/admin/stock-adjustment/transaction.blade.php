@extends('layouts.admin')

@section('title', 'Stock Adjustment Transaction')

@section('content')
<style>
    /* Compact form adjustments - matching sale module */
    .compact-form {
        font-size: 11px;
        padding: 8px;
        background: #f5f5f5;
    }
    
    .compact-form label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .compact-form input,
    .compact-form select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }

    input:focus {
        box-shadow: none !important;
    }

    .header-section {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }

    .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .field-group label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .field-group input,
    .field-group select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }

    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    .table-compact {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    .table-compact th,
    .table-compact td {
        padding: 4px;
        vertical-align: middle;
        height: 45px;
    }
    
    .table-compact th {
        background: #6c757d;
        color: white;
        font-weight: 600;
        text-align: center;
        border: 1px solid #5a6268;
        height: 40px;
    }
    
    .table-compact input {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }

    .table-compact select {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }
    
    /* Table container */
    #itemsTableContainer {
        max-height: 310px !important;
    }

    /* Row utility styles */
    .row-shortage {
        background-color: #ffe6e6 !important;
    }

    .row-excess {
        background-color: #e6ffe6 !important;
    }

    .row-completed {
        background-color: #d4edda !important;
    }

    .row-selected {
        background-color: #e7f3ff !important;
    }

    /* Custom Dropdown Styles */
    .custom-dropdown-wrapper {
        position: relative;
    }
    
    .custom-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        min-width: 60px;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1050;
        padding: 4px 0;
        margin-top: 1px;
    }
    
    .custom-dropdown-menu.show {
        display: block;
    }
    
    .custom-dropdown-item {
        padding: 6px 10px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    
    .custom-dropdown-item:hover,
    .custom-dropdown-item.highlighted {
        background-color: #e9ecef;
        color: #0d6efd;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-sliders me-2"></i> Stock Adjustment Transaction</h4>
        <div class="text-muted small">Adjust stock quantities (Shortage/Excess)</div>
    </div>
    <div>
        <a href="{{ route('admin.stock-adjustment.invoices') }}" class="btn btn-primary">
            <i class="bi bi-receipt-cutoff me-1"></i> Stock Adjustment Invoices
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="stockAdjustmentForm" method="POST" autocomplete="off" onkeydown="return event.key !== 'Enter';">
            @csrf

            <!-- Header Section -->
            <div class="header-section">
                <div class="header-row">
                    <div class="field-group">
                        <label><strong>Date:</strong></label>
                        <input type="date" class="form-control" id="adjustmentDate" name="adjustment_date" style="width: 140px;" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field-group">
                        <label><strong>Day:</strong></label>
                        <input type="text" class="form-control readonly-field" id="dayName" style="width: 90px;" readonly>
                    </div>
                    <div class="field-group">
                        <label><strong>Trn No.:</strong></label>
                        <input type="text" class="form-control readonly-field" id="trnNo" name="trn_no" style="width: 70px;" value="{{ $nextTrnNo }}" readonly>
                    </div>
                    <div class="field-group flex-grow-1">
                        <label><strong>Remarks:</strong></label>
                        <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Enter remarks..." style="flex: 1;">
                    </div>
                    <div class="field-group">
                        <button type="button" class="btn btn-info btn-sm" onclick="openItemModal()">
                            <i class="bi bi-plus-circle me-1"></i> Insert Item
                        </button>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 350px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 80px;">Code</th>
                                <th style="width: 220px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 80px;">Expiry</th>
                                <th style="width: 60px;">Sh/Ex</th>
                                <th style="width: 70px;">Qty</th>
                                <th style="width: 90px;">Cost</th>
                                <th style="width: 100px;">Amount</th>
                                <th style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-info" onclick="openItemModal()">
                        <i class="bi bi-plus-circle me-1"></i> Insert Item
                    </button>
                </div>
            </div>

            <!-- Additional Details Section -->
            <div class="bg-light border rounded p-2 mb-2" style="background: #f5f5f5 !important;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="d-flex align-items-center gap-3" style="font-size: 12px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>Packing</strong></label>
                                <input type="text" id="detailPacking" class="form-control form-control-sm" style="width: 60px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>Comp:</strong></label>
                                <input type="text" id="detailCompany" class="form-control form-control-sm" style="width: 100px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>Lctn:</strong></label>
                                <input type="text" id="detailLocation" class="form-control form-control-sm" style="width: 60px; height: 24px; background: #fff; border: 1px solid #ccc;" value="" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>MRP:</strong></label>
                                <input type="text" id="detailMrp" class="form-control form-control-sm text-end" style="width: 80px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-auto">
                                <label class="mb-0" style="color: #8B4513;"><strong>SrNo</strong></label>
                                <input type="text" id="detailSrNo" class="form-control form-control-sm text-center" style="width: 50px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-1" style="font-size: 12px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>Unit</strong></label>
                                <input type="text" id="detailUnit" class="form-control form-control-sm" style="width: 60px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="color: #8B4513;"><strong>Cl. Qty</strong></label>
                                <input type="text" id="detailClQty" class="form-control form-control-sm text-end" style="width: 70px; height: 24px; background: #fff; border: 1px solid #ccc;" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex justify-content-end align-items-center h-100">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="font-weight: bold; color: #dc3545;">Total:</label>
                                <input type="number" id="summaryTotal" class="form-control form-control-sm readonly-field text-end fw-bold" readonly step="0.01" style="width: 100px; height: 28px; background: #fff3cd; font-size: 13px;" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" onclick="cancelTransaction()">
                        <i class="bi bi-x-circle me-1"></i> Cancel Stock Adjustment
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteSelectedItem()">
                        <i class="bi bi-trash me-1"></i> Delete Item
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-success" onclick="saveTransaction()">
                        <i class="bi bi-check-circle me-1"></i> Save (End)
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'stockAdjustmentItemModal',
    'module' => 'stock-adjustment',
    'showStock' => true,
    'rateType' => 'cost',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'stockAdjustmentBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'stockAdjustmentBatchModal',
    'module' => 'stock-adjustment',
    'showOnlyAvailable' => true,
    'rateType' => 'cost',
    'showCostDetails' => true,
])

@endsection

@push('scripts')
<script>
let rowCount = 0;
let selectedRowIndex = -1;
let currentItemData = null;
let allItems = [];

// ============================================================
// MASTER KEYBOARD EVENT INTERCEPTOR
// Captures Enter keys for specific workflow paths
// ============================================================
window.addEventListener('keydown', function(e) {
    // Hard guard: qty must be > 0 before leaving qty field.
    if (e.key === 'Enter' || e.key === 'Tab') {
        var activeQty = document.activeElement;
        if (activeQty && activeQty.classList && activeQty.classList.contains('qty-input')) {
            var itemModalForQty = document.getElementById('stockAdjustmentItemModal');
            var batchModalForQty = document.getElementById('stockAdjustmentBatchModal');
            var hasOpenModalForQty =
                (itemModalForQty && itemModalForQty.classList.contains('show')) ||
                (batchModalForQty && batchModalForQty.classList.contains('show'));

            if (!hasOpenModalForQty) {
                var qtyVal = parseFloat(activeQty.value);
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

        // Skip if any modal is open
        var itemModal = document.getElementById('stockAdjustmentItemModal');
        var batchModal = document.getElementById('stockAdjustmentBatchModal');
        if ((itemModal && itemModal.classList.contains('show')) ||
            (batchModal && batchModal.classList.contains('show'))) {
            return; // Let modal handlers deal with it
        }

        // Determine if backwards navigation (Shift+Enter)
        if (e.shiftKey) {
            // Remarks -> Date
            if (activeEl.id === 'remarks') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                document.getElementById('adjustmentDate')?.focus();
                return false;
            }
            
            // Sh/Ex Display -> Previous Row Qty OR Remarks
            if (activeEl.classList.contains('adjustment-type-display')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Close dropdown if open
                document.querySelectorAll('.custom-dropdown-menu.show').forEach(m => m.classList.remove('show'));
                
                var row = activeEl.closest('tr');
                var previousRow = row ? row.previousElementSibling : null;
                
                if (previousRow && previousRow.tagName === 'TR') {
                    // Go back to previous row's Qty
                    var qtyInput = previousRow.querySelector('.qty-input');
                    if (qtyInput) {
                        qtyInput.focus();
                        qtyInput.select();
                    }
                } else {
                    // First row -> go to remarks
                    document.getElementById('remarks')?.focus();
                }
                return false;
            }
            
            // Qty -> Sh/Ex
            if (activeEl.classList.contains('qty-input')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                var row = activeEl.closest('tr');
                if (row) {
                    var shExDisplay = row.querySelector('.adjustment-type-display');
                    if (shExDisplay) {
                        shExDisplay.focus();
                    }
                }
                return false;
            }
            return; // Exit if shift+enter didn't match anything
        }

        // --- NORMAL ENTER (Forward Navigation) ---

        // Date -> Remarks
        if (activeEl.id === 'adjustmentDate') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            document.getElementById('remarks')?.focus();
            return false;
        }

        // Remarks -> Insert Item (open modal)
        if (activeEl.id === 'remarks') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (typeof openItemModal === 'function') openItemModal();
            return false;
        }

        // Sh/Ex -> Qty
        if (activeEl.classList.contains('adjustment-type-display')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Close dropdown if open
            document.querySelectorAll('.custom-dropdown-menu.show').forEach(m => m.classList.remove('show'));
                
            var row = activeEl.closest('tr');
            if (row) {
                var qtyInput = row.querySelector('.qty-input');
                if (qtyInput) {
                    qtyInput.focus();
                    qtyInput.select();
                }
            }
            return false;
        }

        // Qty inside row -> Insert Item
        if (activeEl.classList.contains('qty-input')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (typeof openItemModal === 'function') openItemModal();
            return false;
        }
    }
    
    // Ctrl+S -> Save (handled here AND below for compatibility)
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveTransaction();
        return false;
    }
}, true);

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    document.getElementById('adjustmentDate').addEventListener('change', updateDayName);
    
    // Auto-focus Date field initially
    setTimeout(() => {
        document.getElementById('adjustmentDate')?.focus();
    }, 100);
});

// Close custom dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-dropdown-wrapper')) {
        document.querySelectorAll('.custom-dropdown-menu.show').forEach(m => m.classList.remove('show'));
    }
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function updateDayName() {
    const dateInput = document.getElementById('adjustmentDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

// Bridge function to open new component's item modal
function openItemModal() {
    if (typeof openItemModal_stockAdjustmentItemModal === 'function') {
        openItemModal_stockAdjustmentItemModal();
    } else {
        console.error('Stock adjustment item modal not found');
        alert('Item selection modal not found. Please reload the page.');
    }
}

// Callback when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Stock Adjustment - Item+Batch selected:', item?.name, batch?.batch_no);
    console.log('Item data:', item);
    console.log('Batch data:', batch);
    addItemRow(item, batch);
};

// Also support the simpler callback name
window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

// Listen for item selection (if batch modal doesn't open automatically)
window.onItemSelectedFromModal = function(item) {
    console.log('🔗 Item selected, opening batch modal for:', item?.name);
    if (typeof openBatchModal_batchSelectionModal === 'function') {
        openBatchModal_batchSelectionModal(item);
    }
};

// Add item row to table
function addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCount);
    row.setAttribute('data-item-id', item.id);
    row.setAttribute('data-batch-id', batch.id);
    
    const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '-';
    // Use item's cost from items table, fallback to purchase rate if cost not available
    const cost = parseFloat(item.cost || item.pur_rate || item.p_rate || 0);
    
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${item.id}" readonly>
            <input type="hidden" name="items[${rowCount}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${rowCount}][item_code]" value="${item.id}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${item.name}" readonly>
            <input type="hidden" name="items[${rowCount}][item_name]" value="${item.name}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${batch.batch_no || '-'}" readonly>
            <input type="hidden" name="items[${rowCount}][batch_id]" value="${batch.id}">
            <input type="hidden" name="items[${rowCount}][batch_no]" value="${batch.batch_no || ''}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${expiryDate}" readonly>
            <input type="hidden" name="items[${rowCount}][expiry_date]" value="${batch.expiry_date || ''}">
        </td>
        <td class="custom-dropdown-wrapper">
            <input type="text" class="form-control form-control-sm cursor-pointer adjustment-type-display text-center" 
                   value="S" readonly style="background: white !important; cursor: pointer; height: 26px; padding: 2px 4px;"
                   onkeydown="handleShExKeydown(event, this, ${rowCount})"
                   onclick="toggleShExDropdown(${rowCount})">
            <input type="hidden" class="adjustment-type" name="items[${rowCount}][adjustment_type]" value="S">
            <div class="custom-dropdown-menu" id="shExDropdown_${rowCount}">
                <div class="custom-dropdown-item" onclick="selectShEx('S', ${rowCount})">S (Shortage)</div>
                <div class="custom-dropdown-item" onclick="selectShEx('E', ${rowCount})">E (Excess)</div>
            </div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" name="items[${rowCount}][qty]" value="0" step="0.01" min="0" onchange="calculateRowAmount(${rowCount})" onkeyup="calculateRowAmount(${rowCount})">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field cost-input" name="items[${rowCount}][cost]" value="${cost.toFixed(2)}" step="0.01" readonly>
            <input type="hidden" name="items[${rowCount}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowCount}][company_name]" value="${item.company || ''}">
            <input type="hidden" name="items[${rowCount}][mrp]" value="${batch.mrp || 0}">
            <input type="hidden" name="items[${rowCount}][unit]" value="${item.unit || '1'}">
            <input type="hidden" name="items[${rowCount}][cl_qty]" value="${batch.total_cl_qty || batch.qty || 0}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field amount-input" name="items[${rowCount}][amount]" value="0.00" step="0.01" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${rowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Set row click handler
    row.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            selectRow(rowCount);
        }
    });
    
    // Focus on Sh/Ex custom input display after modal close animation
    setTimeout(function() {
        const shExDisplay = row.querySelector('.adjustment-type-display');
        if (shExDisplay) {
            shExDisplay.focus();
        }
    }, 120);
    
    // Update detail section
    updateDetailSection(item, batch);
    
    rowCount++;
    updateRowStyle(rowCount - 1);
}

// Update row style based on adjustment type
function updateRowStyle(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const adjustmentType = row.querySelector('.adjustment-type').value;
    row.classList.remove('row-shortage', 'row-excess');
    
    if (adjustmentType === 'S') {
        row.classList.add('row-shortage');
    } else {
        row.classList.add('row-excess');
    }
}

// Calculate row amount
function calculateRowAmount(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const adjustmentType = row.querySelector('.adjustment-type').value;
    
    let amount = qty * cost;
    if (adjustmentType === 'S') {
        amount = -Math.abs(amount); // Negative for shortage
    }
    
    row.querySelector('.amount-input').value = amount.toFixed(2);
    
    calculateTotals();
}

// Calculate totals
function calculateTotals() {
    let total = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.amount-input')?.value) || 0;
        total += amount;
    });
    
    document.getElementById('summaryTotal').value = total.toFixed(2);
}

// Select row
function selectRow(rowIndex) {
    // Remove selection from all rows
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        row.classList.remove('row-selected');
    });
    
    // Select current row
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        
        // Update detail section from row data
        const packing = row.querySelector('input[name*="[packing]"]')?.value || '';
        const company = row.querySelector('input[name*="[company_name]"]')?.value || '';
        const mrp = row.querySelector('input[name*="[mrp]"]')?.value || '0';
        const unit = row.querySelector('input[name*="[unit]"]')?.value || '1';
        const clQty = row.querySelector('input[name*="[cl_qty]"]')?.value || '0';
        
        document.getElementById('detailPacking').value = packing;
        document.getElementById('detailCompany').value = company;
        document.getElementById('detailMrp').value = parseFloat(mrp).toFixed(2);
        document.getElementById('detailUnit').value = unit;
        document.getElementById('detailClQty').value = clQty;
        document.getElementById('detailSrNo').value = parseInt(rowIndex) + 1;
        document.getElementById('detailLocation').value = 'Main';
    }
}

// Update detail section
function updateDetailSection(item, batch) {
    document.getElementById('detailPacking').value = item.packing || '';
    document.getElementById('detailCompany').value = item.company || '';
    document.getElementById('detailMrp').value = parseFloat(batch.mrp || 0).toFixed(2);
    // Show total quantity across all batches for this item
    document.getElementById('detailClQty').value = parseFloat(batch.total_cl_qty || batch.qty || 0).toFixed(0);
    document.getElementById('detailUnit').value = item.unit || '1';
    document.getElementById('detailSrNo').value = rowCount + 1;
    document.getElementById('detailLocation').value = 'Main';
}

// Delete row
function deleteRow(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

// Delete selected item
function deleteSelectedItem() {
    if (selectedRowIndex >= 0) {
        deleteRow(selectedRowIndex);
        selectedRowIndex = -1;
    } else {
        alert('Please select a row to delete');
    }
}

// Cancel transaction
function cancelTransaction() {
    if (confirm('Are you sure you want to cancel this transaction? All data will be lost.')) {
        window.location.href = '{{ route("admin.stock-adjustment.invoices") }}';
    }
}

// Save transaction
function saveTransaction() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const items = [];
    let hasValidItem = false;
    
    rows.forEach(row => {
        const itemId = row.querySelector('input[name*="[item_id]"]')?.value;
        const batchId = row.querySelector('input[name*="[batch_id]"]')?.value;
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        
        if (itemId && batchId && qty > 0) {
            hasValidItem = true;
            items.push({
                item_id: itemId,
                item_code: row.querySelector('input[name*="[item_code]"]')?.value,
                item_name: row.querySelector('input[name*="[item_name]"]')?.value,
                batch_id: batchId,
                batch_no: row.querySelector('input[name*="[batch_no]"]')?.value,
                expiry_date: row.querySelector('input[name*="[expiry_date]"]')?.value,
                adjustment_type: row.querySelector('.adjustment-type')?.value || 'S',
                qty: qty,
                cost: parseFloat(row.querySelector('.cost-input')?.value) || 0,
                amount: parseFloat(row.querySelector('.amount-input')?.value) || 0,
                packing: row.querySelector('input[name*="[packing]"]')?.value,
                company_name: row.querySelector('input[name*="[company_name]"]')?.value,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0
            });
        }
    });
    
    if (!hasValidItem) {
        alert('Please add at least one item with quantity');
        return;
    }
    
    const formData = {
        adjustment_date: document.getElementById('adjustmentDate').value,
        day_name: document.getElementById('dayName').value,
        remarks: document.getElementById('remarks').value,
        items: items,
        _token: document.querySelector('input[name="_token"]').value
    };
    
    // Show loading
    const saveBtn = document.querySelector('button[onclick="saveTransaction()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    saveBtn.disabled = true;
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.stock-adjustment.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof crudNotification !== 'undefined') {
                crudNotification.showToast('success', 'Success', data.message);
            } else {
                alert(data.message);
            }
            setTimeout(() => {
                window.location.href = '{{ route("admin.stock-adjustment.invoices") }}';
            }, 1000);
        } else {
            throw new Error(data.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof crudNotification !== 'undefined') {
            crudNotification.showToast('error', 'Error', error.message);
        } else {
            alert('Error: ' + error.message);
        }
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function toggleShExDropdown(rowIndex) {
    // Hide all other open dropdowns first
    document.querySelectorAll('.custom-dropdown-menu.show').forEach(m => {
        if(m.id !== 'shExDropdown_' + rowIndex) m.classList.remove('show');
    });
    
    const menu = document.getElementById('shExDropdown_' + rowIndex);
    if (menu) {
        menu.classList.toggle('show');
    }
}

function selectShEx(val, rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    // Update display input
    const displayInput = row.querySelector('.adjustment-type-display');
    if (displayInput) displayInput.value = val;
    
    // Update hidden input
    const hiddenInput = row.querySelector('.adjustment-type');
    if (hiddenInput) {
        hiddenInput.value = val;
        // Trigger style and amount updates
        updateRowStyle(rowIndex);
        calculateRowAmount(rowIndex);
    }
    
    // Close dropdown
    const menu = document.getElementById('shExDropdown_' + rowIndex);
    if (menu) menu.classList.remove('show');
}

function handleShExKeydown(e, el, rowIndex) {
    const menu = document.getElementById('shExDropdown_' + rowIndex);
    if (!menu) return;
    
    // Press 's'
    if (e.key.toLowerCase() === 's') {
        e.preventDefault();
        selectShEx('S', rowIndex);
        return;
    }
    
    // Press 'e'
    if (e.key.toLowerCase() === 'e') {
        e.preventDefault();
        selectShEx('E', rowIndex);
        return;
    }
    
    // Enter key
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        if (menu.classList.contains('show')) {
            menu.classList.remove('show');
        }
        
        // Move to Qty input
        const row = el.closest('tr');
        if (row) {
            const qtyInput = row.querySelector('.qty-input');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        }
    }
    
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (!menu.classList.contains('show')) {
            menu.classList.add('show');
        } else {
            const currentVal = el.value;
            selectShEx(currentVal === 'S' ? 'E' : 'S', rowIndex);
            menu.classList.add('show');
        }
    }
    
    if (e.key === 'Escape') {
        e.preventDefault();
        menu.classList.remove('show');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
}
</script>

<script>
// ============================================================
// AUTO-SAVE  —  stock_adjustment_transaction_autosave_v1
// ============================================================
(function(){
'use strict';
const KEY = 'stock_adjustment_transaction_autosave_v1';
let _t = null;

function _val(id){ const el=document.getElementById(id); return el?el.value:''; }
function _set(id,v){ const el=document.getElementById(id); if(el) el.value=v; }
function _esc(v){ if(v===undefined||v===null)return''; return String(v).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function _expiryDisp(raw){
    if(!raw||raw==='-') return '-';
    try{ return new Date(raw).toLocaleDateString('en-GB',{month:'2-digit',year:'numeric'}); }catch(e){ return raw; }
}

function save(){
    const rows=[];
    document.querySelectorAll('#itemsTableBody tr').forEach(function(tr){
        const ri=tr.getAttribute('data-row');
        if(ri===null||ri==='') return;
        const g=function(n){ const el=tr.querySelector('input[name="items['+ri+']['+n+']"]'); return el?el.value:''; };
        if(!g('item_id')) return;
        const adjDisp=tr.querySelector('.adjustment-type-display');
        rows.push({
            ri:ri,
            itemId:tr.getAttribute('data-item-id')||'',
            batchId:tr.getAttribute('data-batch-id')||'',
            item_id:g('item_id'), item_code:g('item_code'), item_name:g('item_name'),
            batch_id:g('batch_id'), batch_no:g('batch_no'), expiry_date:g('expiry_date'),
            adjustment_type:g('adjustment_type'), adjustment_type_display:adjDisp?adjDisp.value:'S',
            qty:g('qty'), cost:g('cost'), amount:g('amount'),
            packing:g('packing'), company_name:g('company_name'), mrp:g('mrp'),
            unit:g('unit'), cl_qty:g('cl_qty'),
        });
    });

    const state={
        savedAt:new Date().toISOString(),
        adjustmentDate:_val('adjustmentDate'),
        remarks:_val('remarks'),
        rows:rows,
    };
    if(!state.adjustmentDate && !rows.length) return;
    try{ localStorage.setItem(KEY,JSON.stringify(state)); }catch(e){}
    _badge();
}
function _sched(){ clearTimeout(_t); _t=setTimeout(save,700); }

function restore(){
    let state; try{ const r=localStorage.getItem(KEY); if(!r)return; state=JSON.parse(r); }catch(e){return;}
    if(!state||(!state.adjustmentDate&&!state.rows.length)) return;
    _banner(state.savedAt, function keep(){
        if(state.adjustmentDate) _set('adjustmentDate',state.adjustmentDate);
        if(typeof updateDayName==='function') updateDayName();
        _set('remarks',state.remarks||'');

        const tbody=document.getElementById('itemsTableBody');
        if(tbody) tbody.innerHTML='';
        if(typeof rowCount!=='undefined') window.rowCount=0;

        (state.rows||[]).forEach(function(saved){
            const ri=saved.ri;
            if(typeof rowCount!=='undefined'&&parseInt(ri)>=rowCount) window.rowCount=parseInt(ri)+1;
            const adjType=saved.adjustment_type||'S';
            const adjDisp=saved.adjustment_type_display||adjType;
            const tr=document.createElement('tr');
            tr.setAttribute('data-row',ri);
            tr.setAttribute('data-item-id',saved.itemId||'');
            tr.setAttribute('data-batch-id',saved.batchId||'');
            tr.innerHTML=
                '<td>'+
                    '<input type="text" class="form-control form-control-sm readonly-field" value="'+_esc(saved.item_code)+'" readonly>'+
                    '<input type="hidden" name="items['+ri+'][item_id]" value="'+_esc(saved.item_id)+'">'+
                    '<input type="hidden" name="items['+ri+'][item_code]" value="'+_esc(saved.item_code)+'">'+
                '</td>'+
                '<td>'+
                    '<input type="text" class="form-control form-control-sm readonly-field" value="'+_esc(saved.item_name)+'" readonly>'+
                    '<input type="hidden" name="items['+ri+'][item_name]" value="'+_esc(saved.item_name)+'">'+
                '</td>'+
                '<td>'+
                    '<input type="text" class="form-control form-control-sm readonly-field" value="'+_esc(saved.batch_no||'-')+'" readonly>'+
                    '<input type="hidden" name="items['+ri+'][batch_id]" value="'+_esc(saved.batch_id)+'">'+
                    '<input type="hidden" name="items['+ri+'][batch_no]" value="'+_esc(saved.batch_no)+'">'+
                '</td>'+
                '<td>'+
                    '<input type="text" class="form-control form-control-sm readonly-field" value="'+_esc(_expiryDisp(saved.expiry_date))+'" readonly>'+
                    '<input type="hidden" name="items['+ri+'][expiry_date]" value="'+_esc(saved.expiry_date)+'">'+
                '</td>'+
                '<td class="custom-dropdown-wrapper">'+
                    '<input type="text" class="form-control form-control-sm cursor-pointer adjustment-type-display text-center" value="'+_esc(adjDisp)+'" readonly style="background:white!important;cursor:pointer;height:26px;padding:2px 4px;" onkeydown="handleShExKeydown(event,this,'+ri+')" onclick="toggleShExDropdown('+ri+')">'+
                    '<input type="hidden" class="adjustment-type" name="items['+ri+'][adjustment_type]" value="'+_esc(adjType)+'">'+
                    '<div class="custom-dropdown-menu" id="shExDropdown_'+ri+'">'+
                        '<div class="custom-dropdown-item" onclick="selectShEx(\'S\','+ri+')">S (Shortage)</div>'+
                        '<div class="custom-dropdown-item" onclick="selectShEx(\'E\','+ri+')">E (Excess)</div>'+
                    '</div>'+
                '</td>'+
                '<td><input type="number" class="form-control form-control-sm qty-input" name="items['+ri+'][qty]" value="'+_esc(saved.qty||0)+'" step="0.01" min="0" onchange="calculateRowAmount('+ri+')" onkeyup="calculateRowAmount('+ri+')"></td>'+
                '<td>'+
                    '<input type="number" class="form-control form-control-sm readonly-field cost-input" name="items['+ri+'][cost]" value="'+parseFloat(saved.cost||0).toFixed(2)+'" step="0.01" readonly>'+
                    '<input type="hidden" name="items['+ri+'][packing]" value="'+_esc(saved.packing)+'">'+
                    '<input type="hidden" name="items['+ri+'][company_name]" value="'+_esc(saved.company_name)+'">'+
                    '<input type="hidden" name="items['+ri+'][mrp]" value="'+_esc(saved.mrp||0)+'">'+
                    '<input type="hidden" name="items['+ri+'][unit]" value="'+_esc(saved.unit||'1')+'">'+
                    '<input type="hidden" name="items['+ri+'][cl_qty]" value="'+_esc(saved.cl_qty||0)+'">'+
                '</td>'+
                '<td><input type="number" class="form-control form-control-sm readonly-field amount-input" name="items['+ri+'][amount]" value="'+_esc(saved.amount||'0.00')+'" step="0.01" readonly></td>'+
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow('+ri+')" title="Delete"><i class="bi bi-trash"></i></button></td>';
            if(tbody) tbody.appendChild(tr);
            // row click handler
            tr.addEventListener('click',function(e){
                if(e.target.tagName!=='BUTTON'&&e.target.tagName!=='I'){
                    if(typeof selectRow==='function') selectRow(parseInt(ri));
                }
            });
        });

        if(typeof calculateTotals==='function') calculateTotals();
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
        // saveTransaction uses window.location.href after setTimeout — patch location.href setter
        const _origSave=(typeof saveTransaction==='function')?saveTransaction:null;
        if(_origSave){
            window.saveTransaction=function(){
                // will clear on success inside the setTimeout redirect — patch location
                clearAutoSave();
                _origSave.apply(this,arguments);
            };
        }
    },800);
    setTimeout(restore,900);
    const form=document.getElementById('stockAdjustmentForm');
    if(form){ form.addEventListener('input',_sched); form.addEventListener('change',_sched); }
    const tbody=document.getElementById('itemsTableBody');
    if(tbody) new MutationObserver(_sched).observe(tbody,{childList:true,subtree:true});
});
})();
</script>

@endpush