@extends('layouts.admin')

@section('title', 'Replacement Received Modification')

@push('styles')
<style>
    .compact-form { font-size: 11px; padding: 8px; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .header-section { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 35px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; width: 100%; }
    
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .row-selected { background-color: #d4edff !important; }
    .row-selected td { background-color: #d4edff !important; }
    .row-complete { background-color: #d4edda !important; }
    .row-complete td { background-color: #d4edda !important; }
    
    .calc-section { background: #f5f5f5; border: 1px solid #dee2e6; padding: 8px; }
    .summary-section { background: #ffcccc; border: 1px solid #dee2e6; padding: 8px; }
    .footer-section { background: #ffe4b5; border: 1px solid #dee2e6; padding: 8px; }

    /* Custom dropdown styles */
    .custom-dropdown-container { position: relative; flex: 1; max-width: 400px; }
    .custom-dropdown-list {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        max-height: 200px; overflow-y: auto; background: white;
        border: 1px solid #ced4da; border-top: none; z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .custom-dropdown-item {
        padding: 4px 8px; cursor: pointer; font-size: 11px;
        border-bottom: 1px solid #f0f0f0;
    }
    .custom-dropdown-item:hover, .custom-dropdown-item.highlighted {
        background-color: #cce5ff;
    }
</style>
@endpush

@section('disable_select2', '1')

@section('content')
<section class="compact-form py-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Replacement Received Modification</h5>
            <div>
                <a href="{{ route('admin.replacement-received.transaction') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> New
                </a>
                <a href="{{ route('admin.replacement-received.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-2">
                <form id="rnForm">
                    @csrf
                    <input type="hidden" id="transaction_id">
                    
                    <!-- Header -->
                    <div class="header-section mb-2">
                        <div class="d-flex gap-4 align-items-center">
                            <!-- Date Field -->
                            <div class="field-group">
                                <label style="width: 40px;">Date :</label>
                                <input type="date" id="transaction_date" class="form-control" style="width: 140px;" onchange="updateDayName()">
                            </div>

                            <!-- Day Field -->
                            <div class="field-group">
                                <input type="text" id="day_name" class="form-control readonly-field text-center" style="width: 100px;" readonly>
                            </div>
                            
                            <!-- Supplier Field -->
                            <div class="field-group flex-grow-1">
                                <label style="width: 70px;">Supplier :</label>
                                <div class="custom-dropdown-container">
                                    <input type="text" id="supplierSearchInput" class="form-control" placeholder="Search supplier..." autocomplete="off">
                                    <div class="custom-dropdown-list" id="supplierDropdownList">
                                        @foreach($suppliers as $supplier)
                                        <div class="custom-dropdown-item" data-value="{{ $supplier->supplier_id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="">
                            </div>
                            
                            <!-- Rpl.No Field -->
                            <div class="field-group">
                                <label style="width: 60px;">Rpl.No. :</label>
                                <input type="text" id="rr_no" class="form-control" style="width: 100px;" onkeydown="handleRplNoKeydown(event)">
                            </div>
                            
                            <!-- Insert Invoice Button -->
                            <button type="button" class="btn btn-success btn-sm ms-2" onclick="openInsertInvoiceModal()">
                                <i class="bi bi-file-earmark-plus"></i> Insert Invoice
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="border rounded mb-2" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-compact mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 65px;">Code</th>
                                    <th style="width: 200px;">Item Name</th>
                                    <th style="width: 85px;">Batch</th>
                                    <th style="width: 60px;">Exp.</th>
                                    <th style="width: 50px;">Qty</th>
                                    <th style="width: 50px;">F.Qty</th>
                                    <th style="width: 70px;">MRP</th>
                                    <th style="width: 50px;">Dis.%</th>
                                    <th style="width: 80px;">F.T. Rate</th>
                                    <th style="width: 90px;">F.T. Amt.</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody"></tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mb-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="addNewRow()">
                            <i class="bi bi-plus"></i> Add Row
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="selectItemBtn" onclick="openInsertItemsModal()">
                            <i class="bi bi-list-check"></i> Insert Item
                        </button>
                    </div>

                    <!-- Rates Section -->
                    <div class="calc-section mb-2">
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

                    <!-- Summary -->
                    <div class="summary-section mb-2">
                        <div class="d-flex justify-content-end">
                            <label class="fw-bold me-2">TOTAL AMT.</label>
                            <input type="text" id="total_amount" class="form-control readonly-field text-end fw-bold" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer-section mb-2">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label>Packing</label><input type="text" id="packing" class="form-control readonly-field" readonly></div>
                                <div class="field-group mb-1"><label>Unit</label><input type="text" id="unit" class="form-control readonly-field" readonly></div>
                                <div class="field-group"><label>Cl. Qty</label><input type="text" id="cl_qty" class="form-control readonly-field" readonly></div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label>Comp :</label><input type="text" id="comp" class="form-control readonly-field" readonly></div>
                                <div class="field-group"><label>Lctn :</label><input type="text" id="lctn" name="lctn" class="form-control"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group mb-1">
                                    <label style="width: 60px;">P.SCM.</label>
                                    <input type="number" id="p_scm_percent" name="p_scm_percent" class="form-control text-end" style="width: 60px;" step="0.01" value="0">
                                    <span class="mx-1">+</span>
                                    <input type="number" id="p_scm_amount" name="p_scm_amount" class="form-control text-end" style="width: 80px;" step="0.01" value="0.00">
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">S.SCM.</label>
                                    <input type="number" id="s_scm_percent" name="s_scm_percent" class="form-control text-end" style="width: 60px;" step="0.01" value="0">
                                    <span class="mx-1">+</span>
                                    <input type="number" id="s_scm_amount" name="s_scm_amount" class="form-control text-end" style="width: 80px;" step="0.01" value="0.00">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label>AMT.</label><input type="number" id="amt" name="amt" class="form-control readonly-field text-end" value="0.00" readonly></div>
                                <div class="field-group"><label>Srl.No.</label><input type="text" id="srlno" name="srlno" class="form-control text-end"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="saveTransaction()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openInsertItemsModal()">
                                <i class="bi bi-plus-circle"></i> Insert Item
                            </button>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.location='{{ route('admin.replacement-received.index') }}'">
                            <i class="bi bi-x-circle"></i> Cancel Replacement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Modal placeholder -->
<div id="modalContainer"></div>

@endsection

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'replacementReceivedModItemModal',
    'module' => 'replacement-received',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'replacementReceivedModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'replacementReceivedModBatchModal',
    'module' => 'replacement-received',
    'showOnlyAvailable' => false,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@push('scripts')
<script>
let currentRowIndex = 0, itemsData = [], currentTransactionId = null, selectedRowIndex = null;

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
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" step="1" min="1" value="0" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][free_qty]" value="0" step="1" min="0" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][discount_percent]" value="0" step="0.01" min="0" max="100" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleDiscountKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][ft_rate]" step="0.01" readonly></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][ft_amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger py-0" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
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

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    initSupplierDropdown();
    initKeyboardNavigation();
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id) loadTransaction(id);
    // Focus date field on page load
    document.getElementById('transaction_date')?.focus();
});

function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
        .then(r => r.json())
        .then(d => itemsData = d.items || [])
        .catch(e => console.error(e));
}

function loadTransaction(id) {
    fetch(`{{ url('admin/replacement-received/details') }}/${id}`)
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                populateForm(d.transaction, d.items);
                currentTransactionId = id;
                document.getElementById('deleteBtn').disabled = false;
            }
        }).catch(e => console.error(e));
}

function populateForm(t, items) {
    const transactionIdEl = document.getElementById('transaction_id');
    const rrNoEl = document.getElementById('rr_no');
    const transactionDateEl = document.getElementById('transaction_date');
    const dayNameEl = document.getElementById('day_name');
    const lctnEl = document.getElementById('lctn');
    const srlnoEl = document.getElementById('srlno');
    const totalAmountEl = document.getElementById('total_amount');
    
    if (transactionIdEl) transactionIdEl.value = t.id;
    if (rrNoEl) rrNoEl.value = t.rr_no || '';
    
    // Parse date properly - handle Laravel Carbon format
    let dateValue = '';
    if (t.transaction_date) {
        try {
            let dateStr = '';
            if (typeof t.transaction_date === 'string') {
                dateStr = t.transaction_date;
            } else if (t.transaction_date.date) {
                dateStr = t.transaction_date.date;
            }
            // Extract YYYY-MM-DD from ISO format or datetime
            const dateMatch = dateStr.match(/(\d{4}-\d{2}-\d{2})/);
            if (dateMatch) {
                dateValue = dateMatch[1];
            }
        } catch (e) {
            console.error('Date parse error:', e);
        }
    }
    if (transactionDateEl) transactionDateEl.value = dateValue;
    
    // Set day name
    if (dayNameEl) {
        if (t.day_name) {
            dayNameEl.value = t.day_name;
        } else if (dateValue) {
            updateDayName();
        }
    }
    
    // Set supplier using custom dropdown
    setTimeout(() => {
        if (t.supplier_id) {
            const supplierIdInput = document.getElementById('supplier_id');
            const searchInput = document.getElementById('supplierSearchInput');
            if (supplierIdInput) supplierIdInput.value = t.supplier_id;
            // Find the supplier name from the dropdown items
            const dropdownItems = document.querySelectorAll('#supplierDropdownList .custom-dropdown-item');
            dropdownItems.forEach(item => {
                if (item.dataset.value == t.supplier_id) {
                    if (searchInput) searchInput.value = item.dataset.name;
                    window.selectedSupplierName = item.dataset.name;
                }
            });
            console.log('Supplier set to:', t.supplier_id);
        }
    }, 100);
    
    // Set other fields with null checks
    if (lctnEl) lctnEl.value = t.lctn || '';
    if (srlnoEl) srlnoEl.value = t.srlno || '';
    if (totalAmountEl) totalAmountEl.value = parseFloat(t.total_amount || 0).toFixed(2);
    
    document.getElementById('itemsTableBody').innerHTML = '';
    currentRowIndex = 0;
    (items || []).forEach(item => addNewRowWithData(item));
    if (items?.length > 0) {
        setTimeout(() => {
            selectRow(0);
            // Focus on the batch field of the first row
            const firstRow = document.getElementById('row-0');
            const batchInput = firstRow?.querySelector('input[name*="[batch]"]');
            if (batchInput) {
                batchInput.focus();
                batchInput.select();
            }
        }, 150);
    }
    calculateTotals();
}

function addNewRow() {
    addNewRowWithData({});
    setTimeout(() => {
        selectRow(currentRowIndex - 1);
        document.getElementById(`row-${currentRowIndex - 1}`)?.querySelector('input[name*="[code]"]')?.focus();
    }, 50);
}

function addNewRowWithData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.itemId = item.item_id || '';
    row.dataset.batchId = item.batch_id || '';
    row.dataset.sRate = parseFloat(item.s_rate || 0).toFixed(2);
    row.onclick = () => selectRow(rowIndex);
    
    if (item.item_id) {
        const itemObj = itemsData.find(i => i.id == item.item_id);
        row.dataset.itemData = JSON.stringify(itemObj || {
            id: item.item_id, name: item.item_name || item.name, item_code: item.item_code || item.code,
            packing: item.packing, unit: item.unit, company_short_name: item.company_name || item.company_short_name,
            mrp: item.mrp, ws_rate: item.ws_rate || item.wholesale_rate, 
            spl_rate: item.spl_rate || item.special_rate, s_rate: item.s_rate || item.sale_rate
        });
    }
    
    // Store batch data for rates section population
    if (item.batch_id || item.batch_no) {
        row.dataset.batchData = JSON.stringify({
            batch_no: item.batch_no,
            expiry: item.expiry,
            location: item.location,
            inc: item.inc,
            excise: item.excise,
            spl_rate: item.spl_rate,
            ws_rate: item.ws_rate,
            pur_rate: item.pur_rate || item.p_rate,
            mrp: item.mrp,
            s_rate: item.s_rate,
            qty: item.qty
        });
    }
    
    const qty = parseFloat(item.qty || 0);
    const freeQty = parseFloat(item.free_qty || 0);
    const mrp = parseFloat(item.mrp || 0);
    const discountPercent = parseFloat(item.discount_percent || 0);
    const ftRate = parseFloat(item.ft_rate || 0);
    const ftAmount = parseFloat(item.ft_amount || 0);
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.item_code || ''}" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="${qty > 0 ? qty : ''}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][free_qty]" value="${freeQty > 0 ? freeQty : '0'}" onkeydown="handleFreeQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][mrp]" value="${mrp > 0 ? mrp.toFixed(2) : ''}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleMrpKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][discount_percent]" value="${discountPercent > 0 ? discountPercent.toFixed(2) : '0'}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleDiscountKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][ft_rate]" value="${ftRate > 0 ? ftRate.toFixed(2) : ''}" readonly></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][ft_amount]" value="${ftAmount > 0 ? ftAmount.toFixed(2) : ''}" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger py-0" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    tbody.appendChild(row);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
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
    
    // Update footer fields
    const packEl = document.getElementById('packing');
    const unitEl = document.getElementById('unit');
    const compEl = document.getElementById('comp');
    const clQtyEl = document.getElementById('cl_qty');
    const lctnEl = document.getElementById('lctn');
    
    if (packEl) packEl.value = itemData.packing || '';
    if (unitEl) unitEl.value = itemData.unit || '1';
    if (compEl) compEl.value = itemData.company_short_name || '';
    if (clQtyEl) clQtyEl.value = batchData.qty || '0';
    if (lctnEl && batchData.location) lctnEl.value = batchData.location;
    
    // Update Rates Section
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

function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code || i.item_code == code);
    if (item) {
        const row = document.getElementById(`row-${rowIndex}`);
        row.querySelector('input[name*="[name]"]').value = item.name || '';
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.dataset.itemId = item.id;
        row.dataset.itemData = JSON.stringify(item);
        selectRow(rowIndex);
        calculateRowAmount(rowIndex);
    }
}

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

function calculateTotals() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        total += parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) {
    document.getElementById(`row-${rowIndex}`)?.remove();
    calculateTotals();
}

function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('day_name').value = days[date.getDay()];
    }
}

// ====================================================
// CUSTOM SEARCHABLE SUPPLIER DROPDOWN (No Select2)
// ====================================================
function initSupplierDropdown() {
    const searchInput = document.getElementById('supplierSearchInput');
    const dropdownList = document.getElementById('supplierDropdownList');
    const supplierIdInput = document.getElementById('supplier_id');
    if (!searchInput || !dropdownList) return;

    let highlightedIndex = -1;
    const allItems = dropdownList.querySelectorAll('.custom-dropdown-item');

    function showDropdown() {
        dropdownList.style.display = 'block';
        filterDropdown();
    }

    function hideDropdown() {
        dropdownList.style.display = 'none';
        highlightedIndex = -1;
        clearHighlights();
    }

    function filterDropdown() {
        const query = searchInput.value.toLowerCase().trim();
        allItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = (!query || name.includes(query)) ? 'block' : 'none';
        });
        highlightedIndex = -1;
        clearHighlights();
    }

    function getVisibleItems() {
        return Array.from(allItems).filter(item => item.style.display !== 'none');
    }

    function clearHighlights() {
        allItems.forEach(item => item.classList.remove('highlighted'));
    }

    function highlightItem(index) {
        const visible = getVisibleItems();
        clearHighlights();
        if (index >= 0 && index < visible.length) {
            visible[index].classList.add('highlighted');
            visible[index].scrollIntoView({ block: 'nearest' });
            highlightedIndex = index;
        }
    }

    function selectSupplierItem(item) {
        supplierIdInput.value = item.dataset.value;
        searchInput.value = item.dataset.name;
        window.selectedSupplierName = item.dataset.name;
        hideDropdown();
        // Move focus to Rpl.No field
        document.getElementById('rr_no')?.focus();
    }

    searchInput.addEventListener('focus', showDropdown);
    searchInput.addEventListener('input', () => { showDropdown(); filterDropdown(); });
    searchInput.addEventListener('blur', function() { setTimeout(hideDropdown, 200); });

    searchInput.addEventListener('keydown', function(e) {
        const visible = getVisibleItems();

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (dropdownList.style.display !== 'block') showDropdown();
            if (highlightedIndex < visible.length - 1) highlightItem(highlightedIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (highlightedIndex > 0) highlightItem(highlightedIndex - 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) {
                // Return to Date
                document.getElementById('transaction_date')?.focus();
            } else if (highlightedIndex >= 0 && visible[highlightedIndex]) {
                selectSupplierItem(visible[highlightedIndex]);
            } else if (visible.length > 0) {
                selectSupplierItem(visible[0]);
            } else {
                hideDropdown();
                document.getElementById('rr_no')?.focus();
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    dropdownList.addEventListener('mousedown', function(e) {
        const item = e.target.closest('.custom-dropdown-item');
        if (item) { e.preventDefault(); selectSupplierItem(item); }
    });
}

// ====================================================
// KEYBOARD NAVIGATION: Date → Supplier → Rpl.No → Insert Items
// ====================================================
function initKeyboardNavigation() {
    const dateField = document.getElementById('transaction_date');

    // Date → Enter → Supplier Name
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('supplierSearchInput')?.focus();
            }
        });
    }

    // Ctrl+S handled by window capture handler below

    // Ctrl+Enter -> jump directly to Inc. field
    document.addEventListener('keydown', function(e) {
        if (!(e.ctrlKey || e.metaKey) || e.key !== 'Enter') return;

        const hasModalOpen = document.querySelector(
            '.pending-orders-modal.show, #pendingOrdersModal.show, #alertModal.show, ' +
            '[id$="Modal"].show, [id$="Backdrop"].show, .modal.show'
        );
        if (hasModalOpen) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const inclusiveField = document.getElementById('inclusive');
        inclusiveField?.focus();
        inclusiveField?.select?.();
    }, true);

    // Intercept Enter on Rpl.No before global handlers.
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        // Skip if invoice modal is open
        if (document.getElementById('invoiceModal')) return;
        const activeElement = document.activeElement;
        if (activeElement?.id !== 'rr_no') return;
        handleRplNoKeydown(e);
    }, true);
}

// Handle Rpl.No. Enter key - trigger Insert Invoice
function handleRplNoKeydown(event) {
    if (event.key === 'Enter') {
        // Skip if invoice modal is already open
        if (document.getElementById('invoiceModal')) return;
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        if (event.shiftKey) {
            document.getElementById('supplierSearchInput')?.focus();
        } else {
            openInsertInvoiceModal();
        }
        return false;
    }
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

// Handle Expiry field keydown
function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

// Handle Qty field keydown
function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[free_qty]"]')?.focus();
    }
}

// Handle Free Qty field keydown
function handleFreeQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[mrp]"]')?.focus();
    }
}

// Handle MRP field keydown
function handleMrpKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        if (event.shiftKey) {
            row?.querySelector('input[name*="[free_qty]"]')?.focus();
        } else {
            const discountInput = row?.querySelector('input[name*="[discount_percent]"]');
            discountInput?.focus();
            discountInput?.select?.();
        }
    }
}

// Handle Discount field keydown
function handleDiscountKeydown(event, rowIndex) {
    if (event.key !== 'Enter') return;
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    const row = document.getElementById(`row-${rowIndex}`);
    if (event.shiftKey) {
        row?.querySelector('input[name*="[mrp]"]')?.focus();
        return;
    }

    calculateRowAmount(rowIndex);
    row?.classList.add('row-complete');

    // Find next row with a qty input
    const allRows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
    const myIdx  = allRows.indexOf(row);
    const nextRow = allRows[myIdx + 1];

    if (nextRow) {
        const nextQty = nextRow.querySelector('input[name*="[qty]"]');
        if (nextQty) {
            nextQty.focus();
            nextQty.select();
            selectRow(parseInt(nextRow.dataset.rowIndex));
            return;
        }
    }

    // No next row (or next row empty) → open Insert Item modal
    openInsertItemsModal();
}

// Handle S.Rate field keydown
function handleSRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        row?.classList.add('row-complete');
        event.target.blur();
    }
}

function clearForm() {
    document.getElementById('rnForm').reset();
    document.getElementById('itemsTableBody').innerHTML = '';
    document.getElementById('total_amount').value = '0.00';
    currentTransactionId = null;
    currentRowIndex = 0;
    document.getElementById('deleteBtn').disabled = true;
}

// Pending transaction data for adjustment
let pendingTransactionData = null;
let existingAdjustments = [];

function saveTransaction() {
    if (!currentTransactionId) { alert('Please select a transaction'); return; }
    
    const transactionDate = document.getElementById('transaction_date').value;
    if (!transactionDate) { alert('Please select transaction date'); return; }
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        if (qty > 0) {
            const itemData = {
                item_id: row.dataset.itemId,
                batch_id: row.dataset.batchId || null,
                item_code: row.querySelector('input[name*="[code]"]')?.value,
                item_name: row.querySelector('input[name*="[name]"]')?.value,
                batch_no: row.querySelector('input[name*="[batch]"]')?.value,
                expiry: row.querySelector('input[name*="[expiry]"]')?.value,
                qty: qty,
                free_qty: parseFloat(row.querySelector('input[name*="[free_qty]"]')?.value) || 0,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0,
                discount_percent: parseFloat(row.querySelector('input[name*="[discount_percent]"]')?.value) || 0,
                ft_rate: parseFloat(row.querySelector('input[name*="[ft_rate]"]')?.value) || 0,
                ft_amount: parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value) || 0
            };
            
            if (row.dataset.isNewBatch === 'true' && row.dataset.newBatchData) {
                try {
                    const batchData = JSON.parse(row.dataset.newBatchData);
                    itemData.is_new_batch = true;
                    itemData.new_batch_data = batchData;
                } catch (e) {}
            }
            
            items.push(itemData);
        }
    });
    if (items.length === 0) { alert('Please add items'); return; }
    
    const supplierId = document.getElementById('supplier_id')?.value;
    const supplierName = window.selectedSupplierName || document.getElementById('supplierSearchInput')?.value || '';
    
    // Store pending transaction data
    pendingTransactionData = {
        rr_no: document.getElementById('rr_no')?.value || '',
        transaction_date: transactionDate,
        day_name: document.getElementById('day_name')?.value || '',
        supplier_id: supplierId || null,
        supplier_name: supplierName,
        total_amount: parseFloat(document.getElementById('total_amount')?.value) || 0,
        packing: document.getElementById('packing')?.value || '',
        unit: document.getElementById('unit')?.value || '',
        cl_qty: document.getElementById('cl_qty')?.value || 0,
        comp: document.getElementById('comp')?.value || '',
        lctn: document.getElementById('lctn')?.value || '',
        srlno: document.getElementById('srlno')?.value || '',
        items: items
    };
    
    // Show adjustment confirmation modal
    showAdjustmentConfirmModal();
}

// Show Adjustment Confirmation Modal
let adjustConfirmKeyHandler = null;

function showAdjustmentConfirmModal() {
    const totalAmount = pendingTransactionData.total_amount || 0;
    
    let html = `
        <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" id="adjustConfirmBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:400px;background:white;border-radius:8px;z-index:1055;" id="adjustConfirmModal">
            <div style="padding:1rem;background:#17a2b8;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Amount Adjustment</h6>
                <button type="button" class="btn-close btn-close-white" onclick="closeAdjustConfirmModal()"></button>
            </div>
            <div style="padding:2rem;text-align:center;">
                <p class="mb-3">Total Amount: <strong class="text-success fs-5">₹${totalAmount.toFixed(2)}</strong></p>
                <p class="mb-0">Do you want to adjust this amount against Purchase Return transactions?</p>
            </div>
            <div style="padding:1rem;background:#f8f9fa;border-radius:0 0 8px 8px;display:flex;justify-content:center;gap:1rem;">
                <button type="button" class="btn btn-success adjust-confirm-btn" id="btnYesAdjust" data-action="yes" onclick="openPurchaseReturnAdjustmentModal()">
                    <i class="bi bi-check-circle me-1"></i> Yes, Adjust
                </button>
                <button type="button" class="btn btn-secondary adjust-confirm-btn" id="btnNoSave" data-action="no" onclick="saveWithoutAdjustment()">
                    <i class="bi bi-x-circle me-1"></i> No, Save Directly
                </button>
            </div>
            <div style="padding:0.5rem;text-align:center;border-top:1px solid #eee;">
                <span class="text-muted small"><i class="bi bi-arrow-left-right me-1"></i>Arrow keys to switch | Enter to confirm | Esc to close</span>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    initAdjustConfirmKeyboard();
}

function initAdjustConfirmKeyboard() {
    let focusedIndex = 0; // 0 = Yes, 1 = No
    const buttons = document.querySelectorAll('.adjust-confirm-btn');
    if (buttons.length === 0) return;

    function highlightButton(index) {
        buttons.forEach(btn => {
            btn.style.outline = '';
            btn.style.boxShadow = '';
            btn.style.transform = '';
        });
        focusedIndex = index;
        buttons[index].style.outline = '3px solid #007bff';
        buttons[index].style.boxShadow = '0 0 10px rgba(0,123,255,0.5)';
        buttons[index].style.transform = 'scale(1.05)';
        buttons[index].focus();
    }

    // Highlight "Yes, Adjust" by default
    highlightButton(0);

    if (adjustConfirmKeyHandler) {
        document.removeEventListener('keydown', adjustConfirmKeyHandler, true);
    }

    adjustConfirmKeyHandler = function(e) {
        const modal = document.getElementById('adjustConfirmModal');
        if (!modal) return;

        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (focusedIndex > 0) highlightButton(focusedIndex - 1);
        } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (focusedIndex < buttons.length - 1) highlightButton(focusedIndex + 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            buttons[focusedIndex]?.click();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeAdjustConfirmModal();
        }
    };

    document.addEventListener('keydown', adjustConfirmKeyHandler, true);
}

function closeAdjustConfirmModal() {
    if (adjustConfirmKeyHandler) {
        document.removeEventListener('keydown', adjustConfirmKeyHandler, true);
        adjustConfirmKeyHandler = null;
    }
    document.getElementById('adjustConfirmModal')?.remove();
    document.getElementById('adjustConfirmBackdrop')?.remove();
}

function saveWithoutAdjustment() {
    closeAdjustConfirmModal();
    pendingTransactionData.adjustments = [];
    submitTransaction();
}

function openPurchaseReturnAdjustmentModal() {
    closeAdjustConfirmModal();
    
    const supplierId = pendingTransactionData.supplier_id;
    const totalAmount = pendingTransactionData.total_amount;
    
    // Fetch purchase returns for this supplier
    fetch(`{{ url('admin/replacement-received/supplier-purchase-returns') }}/${supplierId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Also fetch existing adjustments for this transaction
                fetch(`{{ url('admin/replacement-received/adjustments') }}/${currentTransactionId}`)
                    .then(r => r.json())
                    .then(adjData => {
                        existingAdjustments = adjData.success ? adjData.adjustments : [];
                        showPurchaseReturnAdjustmentModal(data.purchase_returns, totalAmount);
                    })
                    .catch(() => {
                        existingAdjustments = [];
                        showPurchaseReturnAdjustmentModal(data.purchase_returns, totalAmount);
                    });
            } else {
                alert('Error loading purchase returns');
                saveWithoutAdjustment();
            }
        })
        .catch(e => {
            console.error('Error:', e);
            saveWithoutAdjustment();
        });
}

function showPurchaseReturnAdjustmentModal(purchaseReturns, totalAmount) {
    // Build map of existing adjustments
    const existingMap = {};
    existingAdjustments.forEach(adj => {
        existingMap[adj.purchase_return_id] = adj;
    });
    
    let html = `
        <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" id="prAdjustBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;max-width:95%;background:white;border-radius:8px;z-index:1055;" id="prAdjustModal">
            <div style="padding:1rem;background:#6f42c1;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Purchase Return Adjustment</h6>
                <button type="button" class="btn-close btn-close-white" onclick="closePRAdjustModal()"></button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <table class="table table-bordered table-sm" style="font-size:11px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">SRNO.</th>
                            <th style="width:100px;">TRANS NO.</th>
                            <th style="width:100px;">DATE</th>
                            <th class="text-end" style="width:110px;">BILL AMT.</th>
                            <th class="text-end" style="width:110px;">BALANCE</th>
                            <th class="text-end" style="width:120px;">ADJUST AMT.</th>
                        </tr>
                    </thead>
                    <tbody id="prAdjustTableBody">`;
    
    let srNo = 1;
    
    // First show existing adjustments
    existingAdjustments.forEach((adj) => {
        const existingAmount = parseFloat(adj.adjusted_amount) || 0;
        const billAmount = parseFloat(adj.bill_amount) || 0;
        html += `
            <tr data-pr-id="${adj.purchase_return_id}" data-original-balance="${billAmount}" class="table-warning">
                <td class="text-center">${srNo++}</td>
                <td><strong>${adj.pr_no}</strong> <span class="badge bg-info">Existing</span></td>
                <td>${adj.return_date}</td>
                <td class="text-end">${billAmount.toFixed(2)}</td>
                <td class="text-end text-primary fw-bold balance-cell" data-original="${billAmount}">${(billAmount - existingAmount).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end adjust-amount-input" 
                           data-pr-id="${adj.purchase_return_id}" data-max-balance="${billAmount}"
                           step="0.01" min="0" value="${existingAmount.toFixed(2)}"
                           oninput="updateRowBalance(this)" onchange="updateRowBalance(this)">
                </td>
            </tr>`;
    });
    
    // Then show available purchase returns (not already adjusted)
    const existingPrIds = existingAdjustments.map(a => a.purchase_return_id);
    purchaseReturns.filter(pr => !existingPrIds.includes(pr.id)).forEach((pr) => {
        const balance = parseFloat(pr.balance_amount);
        html += `
            <tr data-pr-id="${pr.id}" data-original-balance="${balance}">
                <td class="text-center">${srNo++}</td>
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
    
    if (purchaseReturns.length === 0 && existingAdjustments.length === 0) {
        html += `<tr><td colspan="6" class="text-center text-muted py-3">No purchase returns found</td></tr>`;
    }
    
    html += `</tbody></table></div>
            <div style="padding:1rem;background:#f0f0f0;border-radius:0 0 8px 8px;display:flex;justify-content:space-between;align-items:center;">
                <span class="text-danger small">EXIT : &lt;ESC&gt;</span>
                <div class="d-flex align-items-center gap-3">
                    <span>Total: <strong class="text-success">₹${totalAmount.toFixed(2)}</strong></span>
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
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    updateAdjustmentTotals();
    initPRAdjustKeyboard();
}

let prAdjustKeyHandler = null;

function initPRAdjustKeyboard() {
    const inputs = document.querySelectorAll('.adjust-amount-input');
    if (inputs.length === 0) return;

    // Focus first input and highlight its row
    highlightPRAdjustRow(0);
    inputs[0].focus();
    inputs[0].select();

    if (prAdjustKeyHandler) {
        document.removeEventListener('keydown', prAdjustKeyHandler, true);
    }

    prAdjustKeyHandler = function(e) {
        const modal = document.getElementById('prAdjustModal');
        if (!modal) return;

        const inputs = document.querySelectorAll('.adjust-amount-input');
        if (inputs.length === 0) return;

        // Find current input index
        let currentIndex = -1;
        inputs.forEach((inp, i) => {
            if (inp === document.activeElement) currentIndex = i;
        });

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (currentIndex < inputs.length - 1) {
                const nextIndex = currentIndex + 1;
                highlightPRAdjustRow(nextIndex);
                inputs[nextIndex].focus();
                inputs[nextIndex].select();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (currentIndex > 0) {
                const prevIndex = currentIndex - 1;
                highlightPRAdjustRow(prevIndex);
                inputs[prevIndex].focus();
                inputs[prevIndex].select();
            }
        } else if (e.key === 'Enter' && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            // Update balance for current input
            if (currentIndex >= 0) {
                updateRowBalance(inputs[currentIndex]);
            }
            // Move to next input or save
            if (currentIndex < inputs.length - 1) {
                const nextIndex = currentIndex + 1;
                highlightPRAdjustRow(nextIndex);
                inputs[nextIndex].focus();
                inputs[nextIndex].select();
            } else {
                // Last input - trigger save
                saveWithAdjustments();
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closePRAdjustModal();
        }
    };

    document.addEventListener('keydown', prAdjustKeyHandler, true);
}

function highlightPRAdjustRow(index) {
    const rows = document.querySelectorAll('#prAdjustTableBody tr');
    rows.forEach(r => {
        r.style.backgroundColor = '';
        r.style.outline = '';
    });
    if (index >= 0 && index < rows.length) {
        rows[index].style.backgroundColor = '#e8f0fe';
        rows[index].style.outline = '2px solid #007bff';
        rows[index].scrollIntoView({ block: 'nearest' });
    }
}

function closePRAdjustModal() {
    if (prAdjustKeyHandler) {
        document.removeEventListener('keydown', prAdjustKeyHandler, true);
        prAdjustKeyHandler = null;
    }
    document.getElementById('prAdjustModal')?.remove();
    document.getElementById('prAdjustBackdrop')?.remove();
}

// Update row balance instantly when adjustment amount changes
function updateRowBalance(input) {
    const row = input.closest('tr');
    const balanceCell = row.querySelector('.balance-cell');
    const maxBalance = parseFloat(input.dataset.maxBalance) || 0;
    const adjustAmount = parseFloat(input.value) || 0;
    
    // Validate amount doesn't exceed max balance
    if (adjustAmount > maxBalance) {
        input.value = maxBalance;
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
    }
    
    // Update balance cell instantly
    const newBalance = maxBalance - Math.min(adjustAmount, maxBalance);
    balanceCell.textContent = newBalance.toFixed(2);
    
    updateAdjustmentTotals();
}

function updateAdjustmentTotals() {
    const totalAmount = pendingTransactionData.total_amount || 0;
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjust-amount-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const maxBalance = parseFloat(input.dataset.maxBalance) || 0;
        totalAdjusted += Math.min(amount, maxBalance);
    });
    
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

function saveWithAdjustments() {
    const totalAmount = pendingTransactionData.total_amount || 0;
    let totalAdjusted = 0;
    const adjustments = [];
    
    document.querySelectorAll('.adjust-amount-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const prId = input.dataset.prId;
        
        if (amount > 0) {
            adjustments.push({ purchase_return_id: prId, amount: amount });
            totalAdjusted += amount;
        }
    });
    
    if (totalAdjusted > totalAmount) {
        alert('Total adjusted amount cannot exceed transaction amount!');
        return;
    }
    
    closePRAdjustModal();
    pendingTransactionData.adjustments = adjustments;
    submitTransaction();
}

function submitTransaction() {
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/replacement-received/update') }}/${currentTransactionId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(pendingTransactionData)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Updated successfully!');
            window.location.reload();
        } else alert('Error: ' + (result.message || 'Update failed'));
    }).catch(e => {
        console.error('Save error:', e);
        alert('Error updating transaction');
    });
}

function deleteTransaction() {
    if (!currentTransactionId || !confirm('Delete this Replacement Received?')) return;
    fetch(`{{ url('admin/replacement-received') }}/${currentTransactionId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Deleted!');
            window.location.href = '{{ route("admin.replacement-received.index") }}';
        } else alert('Error: ' + (result.message || 'Delete failed'));
    }).catch(e => alert('Error deleting'));
}

function openInsertInvoiceModal() {
    fetch('{{ route("admin.replacement-received.past-transactions") }}')
        .then(r => r.json())
        .then(d => {
            const txs = d.transactions || [];
            let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" id="invoiceBackdrop"></div>
                <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1055;" id="invoiceModal">
                    <div style="padding:1rem;background:#6f42c1;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                        <h6 class="mb-0">Select Invoice</h6>
                        <button onclick="closeModal()" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
                    </div>
                    <div style="padding:1rem;max-height:400px;overflow-y:auto;" id="invoiceModalBody">
                        <table class="table table-sm table-hover"><thead class="table-primary"><tr><th>RR No</th><th>Date</th><th>Supplier</th><th>Amount</th><th></th></tr></thead><tbody id="invoiceTableBody">`;
            txs.forEach((t, index) => {
                html += `<tr class="invoice-row" data-invoice-id="${t.id}" data-index="${index}" ondblclick="loadInvoiceFromModal(${t.id})" style="cursor:pointer;">
                    <td>${t.rr_no}</td><td>${t.transaction_date||''}</td><td>${t.supplier_name||''}</td><td class="text-end">${t.amount||'0.00'}</td>
                    <td><button class="btn btn-sm btn-primary py-0" onclick="loadInvoiceFromModal(${t.id})"><i class="bi bi-eye"></i></button></td></tr>`;
            });
            html += `</tbody></table></div>
                    <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;border-radius:0 0 8px 8px;display:flex;justify-content:space-between;align-items:center;">
                        <span class="text-muted small"><i class="bi bi-arrow-up-down me-1"></i>Arrow keys to navigate | Enter to select | Esc to close</span>
                        <button class="btn btn-secondary btn-sm" onclick="closeModal()">Close</button>
                    </div>
                </div>`;
            document.getElementById('modalContainer').innerHTML = html;
            initInvoiceModalKeyboard();
        });
}

let invoiceHighlightedIndex = -1;
let invoiceModalKeyHandler = null;

function initInvoiceModalKeyboard() {
    const rows = document.querySelectorAll('#invoiceTableBody .invoice-row');
    if (rows.length === 0) return;

    // Highlight first row by default
    invoiceHighlightedIndex = 0;
    highlightInvoiceRow(0);

    // Remove any old handler
    if (invoiceModalKeyHandler) {
        document.removeEventListener('keydown', invoiceModalKeyHandler, true);
    }

    invoiceModalKeyHandler = function(e) {
        const modal = document.getElementById('invoiceModal');
        if (!modal) return;

        const rows = document.querySelectorAll('#invoiceTableBody .invoice-row');
        if (rows.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            if (invoiceHighlightedIndex < rows.length - 1) {
                highlightInvoiceRow(invoiceHighlightedIndex + 1);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            if (invoiceHighlightedIndex > 0) {
                highlightInvoiceRow(invoiceHighlightedIndex - 1);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (invoiceHighlightedIndex >= 0 && rows[invoiceHighlightedIndex]) {
                const invoiceId = rows[invoiceHighlightedIndex].dataset.invoiceId;
                if (invoiceId) {
                    loadInvoiceFromModal(parseInt(invoiceId));
                }
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        }
    };

    document.addEventListener('keydown', invoiceModalKeyHandler, true);
}

function highlightInvoiceRow(index) {
    const rows = document.querySelectorAll('#invoiceTableBody .invoice-row');
    rows.forEach(r => {
        r.style.backgroundColor = '';
        r.style.outline = '';
    });
    if (index >= 0 && index < rows.length) {
        invoiceHighlightedIndex = index;
        rows[index].style.backgroundColor = '#cce5ff';
        rows[index].style.outline = '2px solid #007bff';
        rows[index].scrollIntoView({ block: 'nearest' });
    }
}

let pendingItemForBatch = null;
let pendingBatchRowIndex = null;
let batchCheckInProgress = false;

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        if (batchInput && batchInput.value.trim()) {
            checkBatch(rowIndex);
        } else {
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        }
    }
}

function checkBatch(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const batchNo = row.querySelector('input[name*="[batch]"]').value.trim();
    
    if (!itemId || !batchNo) return;
    if (batchCheckInProgress) return;
    
    batchCheckInProgress = true;
    pendingBatchRowIndex = rowIndex;
    
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(r => r.json())
        .then(d => {
            if (d.exists && d.batches && d.batches.length > 0) {
                showExistingBatchModal(d.batches, rowIndex, itemData);
            } else {
                showCreateBatchModal(rowIndex, batchNo, itemData);
            }
        })
        .catch(e => {
            console.error(e);
            showCreateBatchModal(rowIndex, batchNo, itemData);
        })
        .finally(() => {
            setTimeout(() => { batchCheckInProgress = false; }, 500);
        });
}

function showExistingBatchModal(batches, rowIndex, itemData) {
    // Store batches data for keyboard selection
    window._batchModalBatches = batches;
    window._batchModalRowIndex = rowIndex;

    let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1060;" id="existingBatchBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1065;" id="existingBatchModal">
            <div style="padding:0.75rem;background:#17a2b8;color:white;border-radius:8px 8px 0 0;">
                <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Existing Batch - ${itemData.name || ''}</h6>
                <button onclick="closeExistingBatchModal()" style="position:absolute;top:10px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;" id="existingBatchModalBody">
                <table class="table table-sm table-hover"><thead class="table-info"><tr><th>Batch</th><th>Expiry</th><th>MRP</th><th>Qty</th><th>Action</th></tr></thead><tbody id="existingBatchTableBody">`;
    batches.forEach((batch, index) => {
        const exp = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}) : '';
        html += `<tr class="batch-row" data-batch-index="${index}" ondblclick='selectExistingBatch(${rowIndex}, ${JSON.stringify(batch).replace(/'/g,"&apos;")})'  style="cursor:pointer;">
            <td><strong>${batch.batch_no||''}</strong></td><td>${exp}</td><td>${parseFloat(batch.mrp||0).toFixed(2)}</td><td>${batch.qty||0}</td>
            <td><button class="btn btn-sm btn-success py-0" onclick='selectExistingBatch(${rowIndex}, ${JSON.stringify(batch).replace(/'/g,"&apos;")})'><i class="bi bi-check"></i></button></td></tr>`;
    });
    html += `</tbody></table></div>
        <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;display:flex;justify-content:space-between;align-items:center;">
            <span class="text-muted small"><i class="bi bi-arrow-up-down me-1"></i>Arrow keys to navigate | Enter to select | Esc to close</span>
            <button class="btn btn-secondary btn-sm" onclick="closeExistingBatchModal()">Cancel</button>
        </div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
    initExistingBatchKeyboard();
}

let batchHighlightedIndex = -1;
let batchModalKeyHandler = null;

function initExistingBatchKeyboard() {
    const rows = document.querySelectorAll('#existingBatchTableBody .batch-row');
    if (rows.length === 0) return;

    // Highlight first row by default
    batchHighlightedIndex = 0;
    highlightBatchRow(0);

    // Remove any old handler
    if (batchModalKeyHandler) {
        document.removeEventListener('keydown', batchModalKeyHandler, true);
    }

    batchModalKeyHandler = function(e) {
        const modal = document.getElementById('existingBatchModal');
        if (!modal) return;

        const rows = document.querySelectorAll('#existingBatchTableBody .batch-row');
        if (rows.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (batchHighlightedIndex < rows.length - 1) {
                highlightBatchRow(batchHighlightedIndex + 1);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (batchHighlightedIndex > 0) {
                highlightBatchRow(batchHighlightedIndex - 1);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (batchHighlightedIndex >= 0 && window._batchModalBatches && window._batchModalBatches[batchHighlightedIndex]) {
                selectExistingBatch(window._batchModalRowIndex, window._batchModalBatches[batchHighlightedIndex]);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeExistingBatchModal();
        }
    };

    document.addEventListener('keydown', batchModalKeyHandler, true);
}

function highlightBatchRow(index) {
    const rows = document.querySelectorAll('#existingBatchTableBody .batch-row');
    rows.forEach(r => {
        r.style.backgroundColor = '';
        r.style.outline = '';
    });
    if (index >= 0 && index < rows.length) {
        batchHighlightedIndex = index;
        rows[index].style.backgroundColor = '#cce5ff';
        rows[index].style.outline = '2px solid #007bff';
        rows[index].scrollIntoView({ block: 'nearest' });
    }
}

function selectExistingBatch(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
        row.querySelector('input[name*="[expiry]"]').value = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}).replace('/','') : '';
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(batch.mrp || 0).toFixed(2);
        row.dataset.batchId = batch.id;
        row.dataset.sRate = parseFloat(batch.s_rate || 0).toFixed(2);
        row.dataset.batchData = JSON.stringify(batch);
        updateFooterFromRow(row);
        calculateRowAmount(rowIndex);
    }
    closeExistingBatchModal();
    setTimeout(() => row?.querySelector('input[name*="[qty]"]')?.focus(), 100);
}

function closeExistingBatchModal() {
    if (batchModalKeyHandler) {
        document.removeEventListener('keydown', batchModalKeyHandler, true);
        batchModalKeyHandler = null;
    }
    batchHighlightedIndex = -1;
    window._batchModalBatches = null;
    window._batchModalRowIndex = null;
    document.getElementById('existingBatchBackdrop')?.remove();
    document.getElementById('existingBatchModal')?.remove();
}

function showCreateBatchModal(rowIndex, batchNo, itemData) {
    const html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1060;" id="createBatchBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:450px;background:white;border-radius:8px;z-index:1065;" id="createBatchModal">
            <div style="padding:0.75rem;background:#6c757d;color:white;border-radius:8px 8px 0 0;">
                <h6 class="mb-0">Opening New Batch</h6>
                <button onclick="closeCreateBatchModal()" style="position:absolute;top:10px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;background:#f0f0f0;">
                <div class="mb-3"><strong>Item:</strong> <span style="color:#0d6efd;font-weight:bold;">${itemData.name || ''}</span></div>
                <div class="mb-3"><strong>Pack:</strong> <span style="color:#0d6efd;font-weight:bold;">${itemData.packing || ''}</span></div>
                <div class="row mb-2">
                    <div class="col-4"><label class="form-label mb-1"><strong>S.Rate:</strong></label><input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${parseFloat(itemData.s_rate || itemData.mrp || 0).toFixed(2)}" step="0.01"></div>
                    <div class="col-4"><label class="form-label mb-1"><strong>Expiry:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY"></div>
                    <div class="col-4"><label class="form-label mb-1"><strong>MRP:</strong></label><input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${parseFloat(itemData.mrp || 0).toFixed(2)}" step="0.01"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><label class="form-label mb-1"><strong>Location:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchLocation" value=""></div>
                    <div class="col-6"><label class="form-label mb-1"><strong>Inclusive:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchInclusive" value="Y" maxlength="1" style="width:50px;"></div>
                </div>
                <input type="hidden" id="newBatchNo" value="${batchNo}">
                <input type="hidden" id="newBatchRowIndex" value="${rowIndex}">
                <input type="hidden" id="newBatchItemId" value="${itemData.id || ''}">
            </div>
            <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;">
                <button class="btn btn-primary btn-sm" onclick="createNewBatch()"><i class="bi bi-check-circle me-1"></i> OK</button>
                <button class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
            </div>
        </div>`;
    document.getElementById('modalContainer').innerHTML = html;
    document.getElementById('newBatchExpiry')?.focus();
}

function createNewBatch() {
    const batchNo = document.getElementById('newBatchNo').value;
    const rowIndex = document.getElementById('newBatchRowIndex').value;
    const itemId = document.getElementById('newBatchItemId').value;
    const sRate = parseFloat(document.getElementById('newBatchSRate').value) || 0;
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = parseFloat(document.getElementById('newBatchMRP').value) || 0;
    const location = document.getElementById('newBatchLocation').value;
    
    if (!batchNo || !itemId) { alert('Batch number and item are required'); return; }
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batchNo;
        row.querySelector('input[name*="[expiry]"]').value = expiry;
        row.querySelector('input[name*="[mrp]"]').value = mrp.toFixed(2);
        row.dataset.batchId = '';
        row.dataset.isNewBatch = 'true';
        row.dataset.sRate = sRate.toFixed(2);
        row.dataset.newBatchData = JSON.stringify({ batch_no: batchNo, expiry: expiry, mrp: mrp, s_rate: sRate, location: location });
    }
    
    closeCreateBatchModal();
    setTimeout(() => row?.querySelector('input[name*="[qty]"]')?.focus(), 100);
}

function closeCreateBatchModal() {
    document.getElementById('createBatchModal')?.remove();
    document.getElementById('createBatchBackdrop')?.remove();
}

function openInsertItemsModal() {
    if (typeof openItemModal_replacementReceivedModItemModal === 'function') {
        openItemModal_replacementReceivedModItemModal();
    } else {
        console.error('Reusable item modal not found');
        alert('Item selection modal not found. Please reload the page.');
    }
}

function loadInvoiceFromModal(id) {
    closeModal();
    loadTransaction(id);
}

function closeModal() {
    if (invoiceModalKeyHandler) {
        document.removeEventListener('keydown', invoiceModalKeyHandler, true);
        invoiceModalKeyHandler = null;
    }
    invoiceHighlightedIndex = -1;
    document.getElementById('modalContainer').innerHTML = '';
}

// ═══════════════════════════════════════════════════
// GLOBAL WINDOW CAPTURE KEYBOARD HANDLERS
// ═══════════════════════════════════════════════════

function _rrAnyModalOpen() {
    return !!(document.querySelector(
        '#batchModal, #adjustConfirmModal, #prAdjustModal, #createBatchModal, ' +
        '.modal.show, [id$="Modal"].show, [id$="Backdrop"].show, #modalContainer .batch-modal'
    ));
}

/* ── Ctrl+S → save/update transaction ── */
window.addEventListener('keydown', function(e) {
    if (!(e.ctrlKey || e.metaKey) || (e.key !== 's' && e.key !== 'S')) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const prAdjustModal = document.getElementById('prAdjustModal');
    if (prAdjustModal) { saveWithAdjustments(); return; }
    saveTransaction();
}, true);

/* ── Discount field Enter → next row qty OR Insert Item modal ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const active = document.activeElement;
    if (!active || !active.name?.includes('[discount_percent]')) return;
    if (_rrAnyModalOpen()) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

    const row = active.closest('tr');
    const rowIndex = row ? parseInt(row.dataset.rowIndex) : null;

    if (e.shiftKey) {
        row?.querySelector('input[name*="[mrp]"]')?.focus();
        return;
    }

    if (rowIndex !== null) {
        calculateRowAmount(rowIndex);
        row.classList.add('row-complete');
    }

    // Find next row
    const allRows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
    const myIdx   = allRows.indexOf(row);
    const nextRow = allRows[myIdx + 1];

    if (nextRow) {
        const nextQty = nextRow.querySelector('input[name*="[qty]"]');
        if (nextQty) {
            nextQty.focus();
            nextQty.select();
            selectRow(parseInt(nextRow.dataset.rowIndex));
            return;
        }
    }

    // No next row → Insert Item modal
    openInsertItemsModal();
}, true);

</script>
@endpush