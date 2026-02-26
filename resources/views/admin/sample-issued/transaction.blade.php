@extends('layouts.admin')

@section('title', 'Sample Issued - New Transaction')

@push('styles')
<style>
    .si-form { font-size: 11px; }
    .si-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .si-form input, .si-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #ffb6c1; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-complete { background-color: #d4edda !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }

    /* Custom Dropdown Styles */
    .custom-dropdown-item { padding: 5px 10px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 11px; }
    .custom-dropdown-item:hover, .custom-dropdown-item.active { background-color: #f0f8ff; }
</style>
@endpush

@section('content')
<section class="si-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i> Sample Issued - New Transaction</h4>
                <div class="text-muted small">Issue samples to customer/doctor/salesman</div>
            </div>
            <div>
                <a href="{{ route('admin.sample-issued.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="siForm" method="POST" autocomplete="off">
                    @csrf
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="sit_transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required data-custom-enter>
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
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 70px;">Party Type :</label>
                                    <div class="custom-dropdown" id="sit_partyTypeDropdownWrapper" style="flex: 1; position: relative;">
                                        <input type="text" class="form-control" id="sit_partyTypeDisplay" 
                                               placeholder="Select Type..." autocomplete="off"
                                               style="background: #fff3e0; border: 2px solid #ff9800;"
                                               onfocus="openPartyTypeDropdown()" onkeyup="filterPartyTypes(event)" data-custom-enter
                                               value="{{ collect($partyTypes)->first() }}">
                                        <input type="hidden" id="sit_party_type" name="party_type" value="{{ collect($partyTypes)->keys()->first() }}">
                                        <div class="custom-dropdown-list" id="sit_partyTypeList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            @foreach($partyTypes as $key => $label)
                                                <div class="custom-dropdown-item" 
                                                     data-value="{{ $key }}" 
                                                     data-name="{{ $label }}"
                                                     onclick="selectPartyType('{{ $key }}', '{{ addslashes($label) }}')">
                                                    {{ $label }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <div class="custom-dropdown" id="sit_partyDropdownWrapper" style="flex: 1; position: relative;">
                                        <input type="text" class="form-control" id="sit_partyDisplay" 
                                               placeholder="Select Party..." autocomplete="off"
                                               style="background: #e8ffe8; border: 2px solid #28a745;"
                                               onfocus="openPartyDropdown()" onkeyup="filterParties(event)" data-custom-enter>
                                        <input type="hidden" id="party_id" name="party_id">
                                        <input type="hidden" id="party_name" name="party_name">
                                        <div class="custom-dropdown-list" id="sit_partyList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            @foreach($customers as $customer)
                                                <div class="custom-dropdown-item" 
                                                     data-value="{{ $customer->id }}" 
                                                     data-name="{{ $customer->name }}"
                                                     onclick="selectParty('{{ $customer->id }}', '{{ addslashes($customer->name) }}')">
                                                    {{ $customer->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="sit_remarks" name="remarks" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 30px;">On :</label>
                                    <input type="text" id="sit_on_field" name="on_field" class="form-control" style="width: 50px;" data-custom-enter>
                                    <label style="width: 35px;">Rate :</label>
                                    <input type="number" id="sit_rate" name="rate" class="form-control text-end" step="0.01" value="0" style="width: 70px;" data-custom-enter>
                                </div>
                                <div class="field-group">
                                    <label style="width: 30px;">Tag :</label>
                                    <input type="text" id="sit_tag" name="tag" class="form-control" style="width: 80px;" data-custom-enter>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No. :</label>
                                    <input type="text" id="sit_gr_no" name="gr_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date :</label>
                                    <input type="date" id="sit_gr_date" name="gr_date" class="form-control" value="{{ date('Y-m-d') }}" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases :</label>
                                    <input type="number" id="sit_cases" name="cases" class="form-control" value="0" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Road Permit :</label>
                                    <input type="text" id="sit_road_permit" name="road_permit_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">Truck No. :</label>
                                    <input type="text" id="sit_truck_no" name="truck_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport :</label>
                                    <input type="text" id="sit_transport" name="transport" class="form-control" data-custom-enter>
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
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="sit_addItemsBtn" onclick="showItemSelectionModal()">
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
                            <button type="button" class="btn btn-secondary" onclick="cancelSampleIssue()">
                                <i class="bi bi-x-circle"></i> Cancel Sample Issue
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
    'id' => 'sampleIssuedItemModal',
    'module' => 'sample-issued',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'sampleIssuedBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'sampleIssuedBatchModal',
    'module' => 'sample-issued',
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
    const dateInput = document.getElementById('sit_transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

function loadItems() {
    fetch('{{ route("admin.sample-issued.getItems") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ============ PARTY DROPDOWN FUNCTIONS ============
function loadPartyList() {
    const partyType = document.getElementById('sit_party_type').value;
    const listContainer = document.getElementById('sit_partyList');
    
    // Clear and show loading
    listContainer.innerHTML = '<div class="custom-dropdown-item" style="color: #999;">Loading...</div>';
    document.getElementById('party_id').value = '';
    document.getElementById('party_name').value = '';
    document.getElementById('sit_partyDisplay').value = '';
    
    fetch(`{{ url('admin/sample-issued/get-party-list') }}?party_type=${partyType}`)
        .then(response => response.json())
        .then(data => {
            listContainer.innerHTML = '';
            data.forEach(party => {
                const div = document.createElement('div');
                div.className = 'custom-dropdown-item';
                div.dataset.value = party.id;
                div.dataset.name = party.name;
                div.textContent = party.name;
                div.onclick = function() { selectParty(party.id, party.name); };
                listContainer.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error loading party list:', error);
            listContainer.innerHTML = '<div class="custom-dropdown-item" style="color: red;">Error loading</div>';
        });
}

function updatePartyName() {
    // No-op - handled by selectParty now
}

// ====== CUSTOM PARTY DROPDOWN ======
let partyActiveIndex = -1;

function openPartyDropdown() {
    const display = document.getElementById('sit_partyDisplay');
    display.select();
    document.querySelectorAll('#sit_partyList .custom-dropdown-item').forEach(item => {
        item.style.display = '';
    });
    document.getElementById('sit_partyList').style.display = 'block';
    partyActiveIndex = 0;
    highlightPartyItem();
}

function closePartyDropdown() {
    setTimeout(() => {
        const list = document.getElementById('sit_partyList');
        if (list) list.style.display = 'none';
        partyActiveIndex = -1;
    }, 200);
}

function filterParties(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    const filter = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#sit_partyList .custom-dropdown-item');
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
    partyActiveIndex = 0;
    highlightPartyItem();
}

function selectParty(id, name) {
    document.getElementById('party_id').value = id;
    document.getElementById('sit_partyDisplay').value = name;
    document.getElementById('party_name').value = name;
    document.getElementById('sit_partyList').style.display = 'none';
    window.selectedPartyName = name;
    partyActiveIndex = -1;
    document.getElementById('sit_remarks')?.focus();
}

function highlightPartyItem() {
    const items = Array.from(document.querySelectorAll('#sit_partyList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => i.classList.remove('active'));
    if (partyActiveIndex >= items.length) partyActiveIndex = 0;
    if (partyActiveIndex < -1) partyActiveIndex = items.length - 1;
    if (partyActiveIndex >= 0 && items[partyActiveIndex]) {
        items[partyActiveIndex].classList.add('active');
        items[partyActiveIndex].style.backgroundColor = '#f0f8ff';
        items[partyActiveIndex].scrollIntoView({ block: 'nearest' });
    }
    items.forEach((item, idx) => {
        if (idx !== partyActiveIndex) item.style.backgroundColor = '';
    });
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#sit_partyDropdownWrapper')) {
        const list = document.getElementById('sit_partyList');
        if (list) list.style.display = 'none';
    }
    if (!e.target.closest('#sit_partyTypeDropdownWrapper')) {
        const list = document.getElementById('sit_partyTypeList');
        if (list) list.style.display = 'none';
    }
});

// ====== CUSTOM PARTY TYPE DROPDOWN ======
let partyTypeActiveIndex = -1;

function openPartyTypeDropdown() {
    const display = document.getElementById('sit_partyTypeDisplay');
    display.select();
    document.querySelectorAll('#sit_partyTypeList .custom-dropdown-item').forEach(item => {
        item.style.display = '';
    });
    document.getElementById('sit_partyTypeList').style.display = 'block';
    partyTypeActiveIndex = 0;
    highlightPartyTypeItem();
}

function closePartyTypeDropdown() {
    setTimeout(() => {
        const list = document.getElementById('sit_partyTypeList');
        if (list) list.style.display = 'none';
        partyTypeActiveIndex = -1;
    }, 200);
}

function filterPartyTypes(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    const filter = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#sit_partyTypeList .custom-dropdown-item');
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
    partyTypeActiveIndex = 0;
    highlightPartyTypeItem();
}

function selectPartyType(id, name) {
    document.getElementById('sit_party_type').value = id;
    document.getElementById('sit_partyTypeDisplay').value = name;
    document.getElementById('sit_partyTypeList').style.display = 'none';
    window.selectedPartyTypeName = name;
    partyTypeActiveIndex = -1;
    // Load matching party list
    loadPartyList();
    // Move to party name
    document.getElementById('sit_partyDisplay')?.focus();
    setTimeout(() => { openPartyDropdown(); }, 100);
}

function highlightPartyTypeItem() {
    const items = Array.from(document.querySelectorAll('#sit_partyTypeList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => i.classList.remove('active'));
    if (partyTypeActiveIndex >= items.length) partyTypeActiveIndex = 0;
    if (partyTypeActiveIndex < -1) partyTypeActiveIndex = items.length - 1;
    if (partyTypeActiveIndex >= 0 && items[partyTypeActiveIndex]) {
        items[partyTypeActiveIndex].classList.add('active');
        items[partyTypeActiveIndex].style.backgroundColor = '#f0f8ff';
        items[partyTypeActiveIndex].scrollIntoView({ block: 'nearest' });
    }
    items.forEach((item, idx) => {
        if (idx !== partyTypeActiveIndex) item.style.backgroundColor = '';
    });
}

// ============ REUSABLE MODAL BRIDGE FUNCTION ============
// This function is called by the reusable modal components
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Issued: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Issued: Invalid item data received');
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
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
    
    console.log('✅ Sample Issued: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
    
    // Focus on qty field
    row.querySelector('input[name*="[qty]"]')?.focus();
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Issued: showItemSelectionModal called - opening reusable modal');
    
    // Check if modal functions exist
    if (typeof window.openItemModal_sampleIssuedItemModal === 'function') {
        window.openItemModal_sampleIssuedItemModal();
    } else {
        console.error('❌ Sample Issued: openItemModal_sampleIssuedItemModal function not found. Modal component may not be loaded.');
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(item.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
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
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
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
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Select Batch for Sample</h5>
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
                        <thead style="background: #ffb6c1;">
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
    
    // Update Cl.Qty in footer
    document.getElementById('cl_qty').value = batch.qty || 0;
    
    closeBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            document.getElementById('sit_transport')?.focus();
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
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[batch]"]')?.focus();
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[expiry]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[rate]"]')?.focus();
    }
}

function handleRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[qty]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        completeRow(rowIndex);
        // Check if next row exists
        const currentRow = document.getElementById(`row-${rowIndex}`);
        const nextRow = currentRow ? currentRow.nextElementSibling : null;
        if (nextRow && nextRow.id && nextRow.id.startsWith('row-')) {
            const nextRowIdx = parseInt(nextRow.id.replace('row-', ''));
            selectRow(nextRowIdx);
            const nextQty = nextRow.querySelector('input[name*="[qty]"]');
            if (nextQty) { nextQty.focus(); nextQty.select(); return; }
        }
        // No next row - trigger Add Items
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
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
    showBatchSelectionForItem(item, rowIndex);
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
    const form = document.getElementById('siForm');
    const formData = new FormData(form);
    
    // Validate
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    // Add total values
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
    
    // Submit
    fetch('{{ route("admin.sample-issued.store") }}', {
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
            window.location.href = '{{ route("admin.sample-issued.index") }}';
        } else {
            alert(data.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving transaction');
    });
}

function cancelSampleIssue() {
    if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
        window.location.href = '{{ route("admin.sample-issued.index") }}';
    }
}

// ====== KEYBOARD NAVIGATION ======
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;

        // Skip if modal is open
        const hasModalOpen = document.getElementById('itemModal') || document.getElementById('batchModal') ||
            document.querySelector('#sampleIssuedItemModal.show') || document.querySelector('#sampleIssuedBatchModal.show');
        if (hasModalOpen) return;

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
                'sit_partyTypeDisplay': 'sit_transaction_date',
                'sit_partyDisplay': 'sit_partyTypeDisplay',
                'sit_remarks': 'sit_partyDisplay',
                'sit_on_field': 'sit_remarks',
                'sit_rate': 'sit_on_field',
                'sit_tag': 'sit_rate',
                'sit_gr_no': 'sit_tag',
                'sit_gr_date': 'sit_gr_no',
                'sit_cases': 'sit_gr_date',
                'sit_road_permit': 'sit_cases',
                'sit_truck_no': 'sit_road_permit',
                'sit_transport': 'sit_truck_no'
            };
            if (backMap[activeEl.id]) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById(backMap[activeEl.id])?.focus();
                return false;
            }
            return;
        }

        // Party Dropdown Intercept
        if (activeEl.id === 'sit_partyDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingId = document.getElementById('party_id').value;
            const listContainer = document.getElementById('sit_partyList');
            if (existingId) {
                if (listContainer) listContainer.style.display = 'none';
                partyActiveIndex = -1;
                document.getElementById('sit_partyDisplay').value = window.selectedPartyName || '';
                document.getElementById('sit_remarks')?.focus();
                return false;
            }
            if (listContainer && listContainer.style.display === 'block') {
                const items = Array.from(document.querySelectorAll('#sit_partyList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                if (partyActiveIndex >= 0 && partyActiveIndex < items.length) {
                    items[partyActiveIndex].click();
                } else {
                    listContainer.style.display = 'none';
                    partyActiveIndex = -1;
                    document.getElementById('sit_remarks')?.focus();
                }
            } else {
                document.getElementById('sit_remarks')?.focus();
            }
            return false;
        }

        // Party Type Dropdown Intercept
        if (activeEl.id === 'sit_partyTypeDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingVal = document.getElementById('sit_party_type').value;
            const listContainer = document.getElementById('sit_partyTypeList');
            if (existingVal) {
                if (listContainer && listContainer.style.display === 'block') {
                    const items = Array.from(document.querySelectorAll('#sit_partyTypeList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                    if (partyTypeActiveIndex >= 0 && partyTypeActiveIndex < items.length) {
                        items[partyTypeActiveIndex].click();
                    } else {
                        listContainer.style.display = 'none';
                        partyTypeActiveIndex = -1;
                        document.getElementById('sit_partyDisplay')?.focus();
                        setTimeout(() => { openPartyDropdown(); }, 50);
                    }
                } else {
                    // Already selected, skip to party name
                    document.getElementById('sit_partyDisplay')?.focus();
                    setTimeout(() => { openPartyDropdown(); }, 50);
                }
            } else {
                if (listContainer && listContainer.style.display === 'block') {
                    const items = Array.from(document.querySelectorAll('#sit_partyTypeList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                    if (partyTypeActiveIndex >= 0 && partyTypeActiveIndex < items.length) {
                        items[partyTypeActiveIndex].click();
                    }
                } else {
                    document.getElementById('sit_partyDisplay')?.focus();
                    setTimeout(() => { openPartyDropdown(); }, 50);
                }
            }
            return false;
        }

        // Date → Party Type
        if (activeEl.id === 'sit_transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_partyTypeDisplay')?.focus();
            setTimeout(() => { openPartyTypeDropdown(); }, 50);
            return false;
        }
        // Remarks → On
        if (activeEl.id === 'sit_remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_on_field')?.focus();
            return false;
        }
        // On → Rate
        if (activeEl.id === 'sit_on_field') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_rate')?.focus();
            return false;
        }
        // Rate → Tag
        if (activeEl.id === 'sit_rate') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_tag')?.focus();
            return false;
        }
        // Tag → GR No
        if (activeEl.id === 'sit_tag') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_gr_no')?.focus();
            return false;
        }
        // GR No → GR Date
        if (activeEl.id === 'sit_gr_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_gr_date')?.focus();
            return false;
        }
        // GR Date → Cases
        if (activeEl.id === 'sit_gr_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_cases')?.focus();
            return false;
        }
        // Cases → Road Permit
        if (activeEl.id === 'sit_cases') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_road_permit')?.focus();
            return false;
        }
        // Road Permit → Truck No
        if (activeEl.id === 'sit_road_permit') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_truck_no')?.focus();
            return false;
        }
        // Truck No → Transport
        if (activeEl.id === 'sit_truck_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sit_transport')?.focus();
            return false;
        }
        // Transport → first row Qty (if items exist) OR Add Items
        if (activeEl.id === 'sit_transport') {
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
            const addBtn = document.getElementById('sit_addItemsBtn');
            if (addBtn) { addBtn.focus(); addBtn.click(); }
            return false;
        }
        // Add Items button
        if (activeEl.id === 'sit_addItemsBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            showItemSelectionModal();
            return false;
        }
    }

    // Dropdown arrow navigation - Party Name
    if (document.activeElement && document.activeElement.id === 'sit_partyDisplay') {
        const listContainer = document.getElementById('sit_partyList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') { e.preventDefault(); partyActiveIndex++; highlightPartyItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); partyActiveIndex--; highlightPartyItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); closePartyDropdown(); return false; }
        }
    }

    // Dropdown arrow navigation - Party Type
    if (document.activeElement && document.activeElement.id === 'sit_partyTypeDisplay') {
        const listContainer = document.getElementById('sit_partyTypeList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') { e.preventDefault(); partyTypeActiveIndex++; highlightPartyTypeItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); partyTypeActiveIndex--; highlightPartyTypeItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); closePartyTypeDropdown(); return false; }
        }
    }

    // Ctrl+S save
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        saveTransaction();
        return false;
    }
}, true);
</script>
@endpush