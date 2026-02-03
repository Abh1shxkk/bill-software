@extends('layouts.admin')

@section('title', 'Stock Adjustment Modification')

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
        background: #fd7e14;
        color: white;
        font-weight: 600;
        text-align: center;
        border: 1px solid #e76a00;
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

    /* Modal Styles */
    .item-modal-backdrop, .batch-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .item-modal-backdrop.show, .batch-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .item-modal, .batch-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 55%;
        transform: translate(-50%, -50%) scale(0.7);
        width: calc(100% - 200px);
        max-width: 800px;
        z-index: 1055;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .item-modal.show, .batch-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .item-modal-content, .batch-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .item-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .batch-modal-header {
        padding: 1rem 1.5rem;
        background: #17a2b8;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-modal-title, .batch-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .btn-close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    .item-modal-body, .batch-modal-body {
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .batch-modal-body {
        padding: 0;
    }

    .item-modal-footer, .batch-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

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

    /* Past Adjustments Modal */
    .past-adjustments-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .past-adjustments-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .past-adjustments-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 55%;
        transform: translate(-50%, -50%) scale(0.7);
        width: calc(100% - 200px);
        max-width: 700px;
        z-index: 1055;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .past-adjustments-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .past-adjustments-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .past-adjustments-modal-header {
        padding: 1rem 1.5rem;
        background: #6c757d;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Stock Adjustment Modification</h4>
        <div class="text-muted small">Modify existing stock adjustment</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock-adjustment.transaction') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Adjustment
        </a>
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
                        <label><strong>Trn No.:</strong></label>
                        <input type="text" class="form-control" id="searchTrnNo" style="width: 80px;" placeholder="Enter...">
                        <button type="button" class="btn btn-sm btn-primary" onclick="searchTransaction()">
                            <i class="bi bi-search"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="openPastAdjustmentsModal()">
                            <i class="bi bi-list"></i> Past
                        </button>
                    </div>
                    <div class="field-group">
                        <label><strong>Date:</strong></label>
                        <input type="date" class="form-control" id="adjustmentDate" name="adjustment_date" style="width: 140px;" disabled>
                    </div>
                    <div class="field-group">
                        <label><strong>Day:</strong></label>
                        <input type="text" class="form-control readonly-field" id="dayName" style="width: 90px;" readonly>
                    </div>
                    <div class="field-group flex-grow-1">
                        <label><strong>Remarks:</strong></label>
                        <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Enter remarks..." style="flex: 1;" disabled>
                    </div>
                    <div class="field-group">
                        <button type="button" class="btn btn-info btn-sm" onclick="openItemModal()" id="insertBtn" disabled>
                            <i class="bi bi-plus-circle me-1"></i> Insert Item
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" id="adjustmentId" value="">

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
                            <tr>
                                <td colspan="9" class="text-center text-muted">Search for a transaction to modify</td>
                            </tr>
                        </tbody>
                    </table>
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
                    <button type="button" class="btn btn-secondary" onclick="cancelModification()">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteSelectedItem()" id="deleteItemBtn" disabled>
                        <i class="bi bi-trash me-1"></i> Delete Item
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-warning" onclick="updateTransaction()" id="updateBtn" disabled>
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Item Selection Modal -->
<div id="itemModalBackdrop" class="item-modal-backdrop" onclick="closeItemModal()"></div>
<div id="itemModal" class="item-modal">
    <div class="item-modal-content">
        <div class="item-modal-header">
            <h5 class="item-modal-title"><i class="bi bi-box-seam me-2"></i>Select Item</h5>
            <button type="button" class="btn-close-modal" onclick="closeItemModal()">&times;</button>
        </div>
        <div class="item-modal-body">
            <div class="mb-3">
                <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." autocomplete="off">
            </div>
            <div id="itemsListContainer" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Code</th>
                            <th>Item Name</th>
                            <th>Packing</th>
                            <th>Company</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsListBody">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="item-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Close</button>
        </div>
    </div>
</div>

<!-- Batch Selection Modal -->
<div id="batchModalBackdrop" class="batch-modal-backdrop" onclick="closeBatchModal()"></div>
<div id="batchModal" class="batch-modal" style="max-width: 950px;">
    <div class="batch-modal-content">
        <div class="batch-modal-header">
            <h5 class="batch-modal-title"><i class="bi bi-layers me-2"></i>Select Batch - <span id="batchModalItemName"></span></h5>
            <span class="ms-3 small">Packing: <span id="batchModalPacking">1*10</span></span>
            <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
        </div>
        <div class="batch-modal-body" style="max-height: 350px; overflow-y: auto; padding: 0;">
            <table class="table table-hover table-sm mb-0" style="font-size: 11px;">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th>BATCH</th>
                        <th>DATE</th>
                        <th>RATE</th>
                        <th>P.RATE</th>
                        <th>MRP</th>
                        <th>QTY</th>
                        <th>EXP.</th>
                        <th>CODE</th>
                        <th>Cost+GST</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="batchesListBody">
                </tbody>
            </table>
        </div>
        <div class="batch-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBatchModal()">Close</button>
        </div>
    </div>
</div>

<!-- Past Adjustments Modal -->
<div id="pastAdjustmentsModalBackdrop" class="past-adjustments-modal-backdrop" onclick="closePastAdjustmentsModal()"></div>
<div id="pastAdjustmentsModal" class="past-adjustments-modal">
    <div class="past-adjustments-modal-content">
        <div class="past-adjustments-modal-header">
            <h5><i class="bi bi-clock-history me-2"></i>Past Stock Adjustments</h5>
            <button type="button" class="btn-close-modal" onclick="closePastAdjustmentsModal()">&times;</button>
        </div>
        <div class="item-modal-body">
            <div id="pastAdjustmentsListContainer" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Trn No</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="pastAdjustmentsListBody">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="item-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closePastAdjustmentsModal()">Close</button>
        </div>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'stockAdjustmentModItemModal',
    'module' => 'stock-adjustment',
    'showStock' => true,
    'rateType' => 'cost',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'stockAdjustmentModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'stockAdjustmentModBatchModal',
    'module' => 'stock-adjustment',
    'showOnlyAvailable' => false,
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
let currentAdjustmentId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    
    document.getElementById('adjustmentDate').addEventListener('change', updateDayName);
    document.getElementById('itemSearchInput').addEventListener('input', debounce(filterItems, 300));
    
    // Check for preload
    @if(isset($preloadTrnNo) && $preloadTrnNo)
        document.getElementById('searchTrnNo').value = '{{ $preloadTrnNo }}';
        searchTransaction();
    @endif
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeItemModal();
            closeBatchModal();
            closePastAdjustmentsModal();
        }
    });
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

// Search transaction
function searchTransaction() {
    const trnNo = document.getElementById('searchTrnNo').value.trim();
    if (!trnNo) {
        alert('Please enter a transaction number');
        return;
    }
    
    fetch(`{{ url('admin/stock-adjustment/fetch') }}/${trnNo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTransactionData(data.adjustment);
            } else {
                alert(data.message || 'Transaction not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching transaction');
        });
}

// Load transaction data
function loadTransactionData(adjustment) {
    currentAdjustmentId = adjustment.id;
    document.getElementById('adjustmentId').value = adjustment.id;
    document.getElementById('adjustmentDate').value = adjustment.adjustment_date ? adjustment.adjustment_date.split('T')[0] : '';
    document.getElementById('adjustmentDate').disabled = false;
    document.getElementById('remarks').value = adjustment.remarks || '';
    document.getElementById('remarks').disabled = false;
    
    updateDayName();
    
    // Enable buttons
    document.getElementById('insertBtn').disabled = false;
    document.getElementById('deleteItemBtn').disabled = false;
    document.getElementById('updateBtn').disabled = false;
    
    // Clear and populate items
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    rowCount = 0;
    
    if (adjustment.items && adjustment.items.length > 0) {
        adjustment.items.forEach(item => {
            addExistingItemRow(item);
        });
        
        // Select first row to populate detail section
        setTimeout(() => {
            selectRow(0);
        }, 100);
    }
    
    calculateTotals();
}

// Add existing item row
function addExistingItemRow(item) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCount);
    row.setAttribute('data-item-id', item.item_id);
    row.setAttribute('data-batch-id', item.batch_id);
    
    const expiryDate = item.expiry_date ? new Date(item.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '-';
    
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${item.item_id}" readonly>
            <input type="hidden" name="items[${rowCount}][item_id]" value="${item.item_id}">
            <input type="hidden" name="items[${rowCount}][item_code]" value="${item.item_code || item.item_id}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${item.item_name}" readonly>
            <input type="hidden" name="items[${rowCount}][item_name]" value="${item.item_name}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${item.batch_no || '-'}" readonly>
            <input type="hidden" name="items[${rowCount}][batch_id]" value="${item.batch_id}">
            <input type="hidden" name="items[${rowCount}][batch_no]" value="${item.batch_no || ''}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${expiryDate}" readonly>
            <input type="hidden" name="items[${rowCount}][expiry_date]" value="${item.expiry_date || ''}">
        </td>
        <td>
            <select class="form-control form-control-sm adjustment-type" name="items[${rowCount}][adjustment_type]" onchange="updateRowStyle(${rowCount})">
                <option value="S" ${item.adjustment_type === 'S' ? 'selected' : ''}>S</option>
                <option value="E" ${item.adjustment_type === 'E' ? 'selected' : ''}>E</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" name="items[${rowCount}][qty]" value="${parseFloat(item.qty || 0).toFixed(2)}" step="0.01" min="0" onchange="calculateRowAmount(${rowCount})" onkeyup="calculateRowAmount(${rowCount})">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field cost-input" name="items[${rowCount}][cost]" value="${parseFloat(item.cost || 0).toFixed(2)}" step="0.01" readonly>
            <input type="hidden" name="items[${rowCount}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowCount}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowCount}][mrp]" value="${item.mrp || 0}">
            <input type="hidden" name="items[${rowCount}][unit]" value="${item.unit || '1'}">
            <input type="hidden" name="items[${rowCount}][cl_qty]" value="${item.cl_qty || 0}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field amount-input" name="items[${rowCount}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${rowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    const currentRowIndex = rowCount;
    row.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            selectRow(currentRowIndex);
        }
    });
    
    rowCount++;
    updateRowStyle(currentRowIndex);
}

// Load all items
function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
        .then(response => response.json())
        .then(data => {
            // API returns {success: true, items: [...]}
            if (data.success && data.items) {
                allItems = data.items;
            } else if (Array.isArray(data)) {
                allItems = data;
            } else {
                allItems = [];
            }
            renderItems(allItems);
        })
        .catch(error => {
            console.error('Error loading items:', error);
            allItems = [];
        });
}

function renderItems(items) {
    const tbody = document.getElementById('itemsListBody');
    tbody.innerHTML = '';
    
    // Ensure items is an array
    if (!Array.isArray(items)) {
        items = [];
    }
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No items found</td></tr>';
        return;
    }
    
    items.slice(0, 100).forEach(item => {
        const tr = document.createElement('tr');
        const itemCost = parseFloat(item.cost || 0).toFixed(2);
        const itemUnit = item.unit || '1';
        const itemCompany = item.company_short_name || item.mfg_by || '';
        tr.innerHTML = `
            <td>${item.id}</td>
            <td>${item.name}</td>
            <td>${item.packing || '-'}</td>
            <td>${itemCompany || '-'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick="selectItem(${item.id}, '${escapeHtml(item.name)}', '${escapeHtml(item.packing || '')}', '${escapeHtml(itemCompany)}', ${itemCost}, '${escapeHtml(itemUnit)}')">
                    <i class="bi bi-check"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function filterItems() {
    const searchTerm = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = allItems.filter(item => 
        item.id.toString().includes(searchTerm) ||
        item.name.toLowerCase().includes(searchTerm)
    );
    renderItems(filtered);
}

function openItemModal() {
    // Use new component if available
    if (typeof openItemModal_stockAdjustmentModItemModal === 'function') {
        console.log('✅ Opening reusable item modal for stock adjustment modification');
        openItemModal_stockAdjustmentModItemModal();
        return;
    }
    // Fallback to legacy
    console.log('⚠️ Falling back to legacy item modal');
    _legacy_openItemModal();
}

function _legacy_openItemModal() {
    document.getElementById('itemModalBackdrop').classList.add('show');
    document.getElementById('itemModal').classList.add('show');
    document.getElementById('itemSearchInput').value = '';
    renderItems(allItems);
    setTimeout(() => document.getElementById('itemSearchInput').focus(), 100);
}

// ====== NEW MODAL COMPONENT BRIDGE ======
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Stock Adjustment Modification - Item+Batch selected:', item?.name, batch?.batch_no);
    console.log('Item data:', item);
    console.log('Batch data:', batch);
    
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCount);
    row.setAttribute('data-item-id', item.id);
    row.setAttribute('data-batch-id', batch?.id || '');
    
    const expiryDate = batch?.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '-';
    // Use cost with fallbacks: batch cost_gst > item cost > item pur_rate > item p_rate > 0
    const cost = parseFloat(batch?.cost_gst || item.cost || item.pur_rate || item.p_rate || 0).toFixed(2);
    
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
            <input type="text" class="form-control form-control-sm readonly-field" value="${batch?.batch_no || '-'}" readonly>
            <input type="hidden" name="items[${rowCount}][batch_id]" value="${batch?.id || ''}">
            <input type="hidden" name="items[${rowCount}][batch_no]" value="${batch?.batch_no || ''}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm readonly-field" value="${expiryDate}" readonly>
            <input type="hidden" name="items[${rowCount}][expiry_date]" value="${batch?.expiry_date || ''}">
        </td>
        <td>
            <select class="form-control form-control-sm adjustment-type" name="items[${rowCount}][adjustment_type]" onchange="updateRowStyle(${rowCount})">
                <option value="S">S</option>
                <option value="E">E</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" name="items[${rowCount}][qty]" value="0.00" step="0.01" min="0" onchange="calculateRowAmount(${rowCount})" onkeyup="calculateRowAmount(${rowCount})">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field cost-input" name="items[${rowCount}][cost]" value="${cost}" step="0.01" readonly>
            <input type="hidden" name="items[${rowCount}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowCount}][company_name]" value="${item.company_short_name || ''}">
            <input type="hidden" name="items[${rowCount}][mrp]" value="${batch?.mrp || item.mrp || 0}">
            <input type="hidden" name="items[${rowCount}][unit]" value="${item.unit || '1'}">
            <input type="hidden" name="items[${rowCount}][cl_qty]" value="${batch?.qty || item.total_qty || 0}">
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
    
    const currentRowIndex = rowCount;
    row.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            selectRow(currentRowIndex);
        }
    });
    
    rowCount++;
    updateRowStyle(currentRowIndex);
    selectRow(currentRowIndex);
    calculateTotals();
    
    row.querySelector('.qty-input')?.focus();
};

window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

window.onItemSelectedFromModal = function(item) {
    console.log('✅ Item selected, opening batch modal:', item?.name);
    if (typeof openBatchModal_stockAdjustmentModBatchModal === 'function') {
        openBatchModal_stockAdjustmentModBatchModal(item);
    } else {
        console.error('❌ Batch modal function not found');
    }
};
// ====== END MODAL COMPONENT BRIDGE ======

function closeItemModal() {
    document.getElementById('itemModalBackdrop').classList.remove('show');
    document.getElementById('itemModal').classList.remove('show');
}

function openBatchModal(itemId, itemName, packing) {
    document.getElementById('batchModalItemName').textContent = itemName;
    document.getElementById('batchModalPacking').textContent = packing || '1*10';
    loadBatches(itemId);
}

function loadBatches(itemId) {
    fetch(`{{ url('admin/api/item-batches') }}/${itemId}`)
        .then(response => response.json())
        .then(data => {
            // API returns {success: true, batches: [...]}
            let batches = [];
            if (data.success && data.batches) {
                batches = data.batches;
            } else if (Array.isArray(data)) {
                batches = data;
            }
            renderBatches(batches);
            document.getElementById('batchModalBackdrop').classList.add('show');
            document.getElementById('batchModal').classList.add('show');
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            alert('Error loading batches');
        });
}

function renderBatches(batches) {
    const tbody = document.getElementById('batchesListBody');
    tbody.innerHTML = '';
    
    // Ensure batches is an array
    if (!Array.isArray(batches)) {
        batches = [];
    }
    
    // Update packing display
    if (batches.length > 0 && batches[0].packing) {
        document.getElementById('batchModalPacking').textContent = batches[0].packing;
    }
    
    if (batches.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No batches found</td></tr>';
        return;
    }
    
    batches.forEach(batch => {
        // Format expiry date as MM/YY
        let expiryDisplay = '-';
        if (batch.expiry_date) {
            try {
                const expDate = new Date(batch.expiry_date);
                expiryDisplay = String(expDate.getMonth() + 1).padStart(2, '0') + '/' + String(expDate.getFullYear()).slice(-2);
            } catch(e) {
                expiryDisplay = batch.expiry_display || '-';
            }
        }
        
        // Format purchase date as DD-MMM-YY
        let purchaseDateDisplay = '-';
        if (batch.purchase_date) {
            try {
                const pDate = new Date(batch.purchase_date);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                purchaseDateDisplay = String(pDate.getDate()).padStart(2, '0') + '-' + months[pDate.getMonth()] + '-' + String(pDate.getFullYear()).slice(-2);
            } catch(e) {
                purchaseDateDisplay = batch.purchase_date_display || '-';
            }
        }
        
        const qty = parseFloat(batch.qty || 0);
        const qtyClass = qty < 0 ? 'text-danger fw-bold' : '';
        
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.innerHTML = `
            <td>${batch.batch_no || '-'}</td>
            <td>${purchaseDateDisplay}</td>
            <td class="text-end">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td class="text-end ${qtyClass}">${qty}</td>
            <td>${expiryDisplay}</td>
            <td>${batch.item_code || batch.id || '-'}</td>
            <td class="text-end">${parseFloat(batch.cost_gst || batch.cost || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick='selectBatch(${JSON.stringify(batch)})'>
                    <i class="bi bi-check"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function closeBatchModal() {
    document.getElementById('batchModalBackdrop').classList.remove('show');
    document.getElementById('batchModal').classList.remove('show');
}

function selectBatch(batch) {
    closeBatchModal();
    addItemRow(currentItemData, batch);
}

function addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCount);
    row.setAttribute('data-item-id', item.id);
    row.setAttribute('data-batch-id', batch.id);
    
    const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: 'numeric'}) : '-';
    // Use item's cost from items table, not batch cost
    const cost = parseFloat(item.cost || 0);
    // Use total quantity across all batches for this item
    const clQty = batch.total_cl_qty || batch.qty || 0;
    
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
        <td>
            <select class="form-control form-control-sm adjustment-type" name="items[${rowCount}][adjustment_type]" onchange="updateRowStyle(${rowCount})">
                <option value="S">S</option>
                <option value="E">E</option>
            </select>
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
            <input type="hidden" name="items[${rowCount}][cl_qty]" value="${clQty}">
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
    
    row.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            selectRow(rowCount);
        }
    });
    
    const shExSelect = row.querySelector('.adjustment-type');
    shExSelect.focus();
    
    updateDetailSection(item, batch);
    
    rowCount++;
    updateRowStyle(rowCount - 1);
}

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

function calculateRowAmount(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const adjustmentType = row.querySelector('.adjustment-type').value;
    
    let amount = qty * cost;
    if (adjustmentType === 'S') {
        amount = -Math.abs(amount);
    }
    
    row.querySelector('.amount-input').value = amount.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let total = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.amount-input')?.value) || 0;
        total += amount;
    });
    
    document.getElementById('summaryTotal').value = total.toFixed(2);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        row.classList.remove('row-selected');
    });
    
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        
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

function deleteRow(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function deleteSelectedItem() {
    if (selectedRowIndex >= 0) {
        deleteRow(selectedRowIndex);
        selectedRowIndex = -1;
    } else {
        alert('Please select a row to delete');
    }
}

function cancelModification() {
    window.location.href = '{{ route("admin.stock-adjustment.invoices") }}';
}

// Open past adjustments modal
function openPastAdjustmentsModal() {
    console.log('📋 Opening past adjustments modal...');
    fetch('{{ route("admin.stock-adjustment.past-adjustments") }}')
        .then(response => {
            console.log('📋 Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 Past adjustments data:', data);
            if (data.success) {
                renderPastAdjustments(data.adjustments);
                document.getElementById('pastAdjustmentsModalBackdrop').classList.add('show');
                document.getElementById('pastAdjustmentsModal').classList.add('show');
            } else {
                console.error('❌ Failed to load past adjustments:', data.message);
                alert(data.message || 'Error loading past adjustments');
            }
        })
        .catch(error => {
            console.error('❌ Error loading past adjustments:', error);
            alert('Error loading past adjustments: ' + error.message);
        });
}

function renderPastAdjustments(adjustments) {
    console.log('📋 Rendering past adjustments:', adjustments);
    const tbody = document.getElementById('pastAdjustmentsListBody');
    tbody.innerHTML = '';
    
    if (!adjustments || adjustments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No adjustments found</td></tr>';
        return;
    }
    
    adjustments.forEach(adj => {
        console.log('📋 Processing adjustment:', adj);
        const date = new Date(adj.adjustment_date).toLocaleDateString('en-GB');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${adj.trn_no}</td>
            <td>${date}</td>
            <td>${adj.total_items || 0}</td>
            <td>${parseFloat(adj.total_amount || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick="loadAdjustmentFromList('${adj.trn_no}')">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    console.log('✅ Past adjustments rendered successfully');
}

function loadAdjustmentFromList(trnNo) {
    closePastAdjustmentsModal();
    document.getElementById('searchTrnNo').value = trnNo;
    searchTransaction();
}

function closePastAdjustmentsModal() {
    document.getElementById('pastAdjustmentsModalBackdrop').classList.remove('show');
    document.getElementById('pastAdjustmentsModal').classList.remove('show');
}

// Update transaction
function updateTransaction() {
    if (!currentAdjustmentId) {
        alert('No transaction loaded');
        return;
    }
    
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
    
    const updateBtn = document.getElementById('updateBtn');
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
    updateBtn.disabled = true;
    
    fetch(`{{ url('admin/stock-adjustment') }}/${currentAdjustmentId}`, {
        method: 'PUT',
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
            throw new Error(data.message || 'Error updating transaction');
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
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
}
</script>
@endpush
