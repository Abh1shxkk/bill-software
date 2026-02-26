@extends('layouts.admin')

@section('title', 'Sale Voucher')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    /* HSN Table Styles - matching image */
    .hsn-table-container { background: #d4a5a5; padding: 10px; border: 2px solid #8b4513; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #9c6b6b; color: white; padding: 6px 8px; text-align: center; border: 1px solid #8b4513; font-weight: 600; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table input:focus { outline: 2px solid #ffc107; }
    .hsn-table .row-selected { background: #fffbcc; }
    
    /* Scrollable table body */
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .hsn-table thead { width: calc(100% - 17px); } /* Account for scrollbar */
    
    /* Totals Section */
    .totals-section { background: #d4a5a5; padding: 10px; margin-top: 10px; border: 2px solid #8b4513; }
    .totals-table { font-size: 11px; }
    .totals-table td { padding: 4px 8px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    
    /* Button Styles */
    .btn-hsn { background: #9c6b6b; color: white; border: 1px solid #8b4513; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #8b5a5a; }
    
    /* Modal */
    .hsn-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
    .hsn-modal-backdrop.show { display: block; }
    .hsn-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: #f0f0f0; border: 2px solid #666; z-index: 9999; box-shadow: 0 5px 20px rgba(0,0,0,0.4); }
    .hsn-modal.show { display: block; }
    .hsn-modal-header { background: #9c6b6b; color: white; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; }
    .hsn-modal-title { font-size: 13px; font-weight: 600; margin: 0; }
    .hsn-modal-body { max-height: 350px; overflow-y: auto; padding: 10px; }
    .hsn-modal-footer { background: #e0e0e0; padding: 8px; display: flex; justify-content: flex-end; gap: 8px; }
    .hsn-list-item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; font-size: 11px; }
    .hsn-list-item:hover { background: #e6f3ff; }
    .hsn-list-item.selected { background: #007bff; color: white; }

    /* Custom Dropdown Styles */
    .sv-custom-dropdown { position: relative; flex: 1; }
    .sv-custom-dropdown .sv-dd-input { width: 100%; font-size: 11px; padding: 2px 6px; height: 26px; border: 1px solid #ced4da; border-radius: 4px; background: #fff; }
    .sv-custom-dropdown .sv-dd-input:focus { border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25); }
    .sv-custom-dropdown .sv-dd-list { display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .sv-custom-dropdown .sv-dd-list.show { display: block; }
    .sv-dd-item { padding: 5px 10px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 11px; }
    .sv-dd-item:hover, .sv-dd-item.active { background-color: #e6f3ff; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Sale Voucher (HSN Entry)</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-info btn-sm" id="sv_openHsnBtn" onclick="openHsnModal()">
            <i class="bi bi-plus-circle me-1"></i> Open HSN
        </button>
        <a href="{{ route('admin.sale-voucher.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-list"></i> All Vouchers
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <!-- Header Section -->
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Series :</label>
                        <input type="text" class="form-control" id="series" value="S2" style="width: 60px;" readonly>
                        <span style="font-weight: 600; margin-left: 5px;">GST VOUCHER</span>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="saleDate" value="{{ date('Y-m-d') }}" style="width: 130px;" data-custom-enter>
                        <input type="text" class="form-control" id="dayName" value="{{ date('l') }}" readonly style="width: 80px; background: #e9ecef;">
                    </div>
                    <div class="field-group">
                        <label>Inv. No :</label>
                        <input type="text" class="form-control" id="invoiceNo" value="{{ $nextInvoiceNo }}" style="width: 120px;" readonly>
                    </div>
                    <div class="field-group">
                        <label>Due Date :</label>
                        <input type="date" class="form-control" id="dueDate" value="{{ date('Y-m-d') }}" style="width: 130px;" data-custom-enter>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Name :</label>
                        <input type="hidden" id="customerId">
                        <div class="sv-custom-dropdown" id="sv_customerDropdownWrapper">
                            <input type="text" class="sv-dd-input" id="sv_customerDisplay" placeholder="Select Customer..." autocomplete="off" onfocus="openCustomerDropdown()" oninput="filterCustomerInput()" data-custom-enter>
                            <div class="sv-dd-list" id="sv_customerList">
                                @foreach($customers as $customer)
                                <div class="sv-dd-item" data-value="{{ $customer->id }}" data-name="{{ $customer->name }}" onclick="selectCustomer('{{ $customer->id }}', '{{ addslashes($customer->name) }}')">{{ $customer->name }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Sales Man :</label>
                        <div class="sv-custom-dropdown" id="sv_salesmanDropdownWrapper">
                            <input type="text" class="sv-dd-input" id="sv_salesmanDisplay" placeholder="Select Salesman..." autocomplete="off" onfocus="openSalesmanDropdown()" oninput="filterSalesmanInput()" data-custom-enter>
                            <input type="hidden" id="salesmanId">
                            <div class="sv-dd-list" id="sv_salesmanList">
                                @foreach($salesmen as $sm)
                                <div class="sv-dd-item" data-value="{{ $sm->id }}" data-name="{{ $sm->name }}" onclick="selectSalesman('{{ $sm->id }}', '{{ addslashes($sm->name) }}')">{{ $sm->name }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Cash :</label>
                        <input type="text" class="form-control" id="cashFlag" value="N" style="width: 30px; text-transform: uppercase; cursor: pointer;" readonly onclick="toggleCash()" title="Click to toggle Y/N" data-custom-enter>
                    </div>
                    <div class="border p-2" style="background: #fffbcc;">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">DUE :</span>
                            <span id="dueDisplay">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">PDC :</span>
                            <span>0.00</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">TOTAL :</span>
                            <span id="totalDisplay">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HSN Table Section -->
        <div class="hsn-table-container">
            <div class="d-flex justify-content-end mb-2 gap-2">
                <button type="button" class="btn-hsn" onclick="addNewRow()">
                    <i class="bi bi-plus-circle me-1"></i> Add Row
                </button>
            </div>
            
            <table class="hsn-table" id="hsnTable">
                <thead>
                    <tr>
                        <th style="width: 100px;">HSN Code</th>
                        <th style="width: 80px;">Amount</th>
                        <th style="width: 55px;">GST%</th>
                        <th style="width: 55px;">CGST%</th>
                        <th style="width: 75px;">Amount</th>
                        <th style="width: 55px;">SGST%</th>
                        <th style="width: 75px;">Amount</th>
                        <th style="width: 50px;">Qty.</th>
                        <th style="width: 50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hsnTableBody" class="hsn-table-scroll">
                    <!-- Rows will be added dynamically -->
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="row">
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Gross Amt.</td>
                            <td class="value" id="grossAmt">0.00</td>
                        </tr>
                        <tr>
                            <td class="label">Total GST</td>
                            <td class="value" id="totalGst">0.00</td>
                        </tr>
                        <tr>
                            <td class="label">Net Amt.</td>
                            <td class="value" id="netAmt">0.00</td>
                        </tr>
                        <tr>
                            <td class="label">Round Off</td>
                            <td class="value" id="roundOff">0.00</td>
                        </tr>
                        <tr>
                            <td class="label">Amount</td>
                            <td class="value" id="finalAmount">0.00</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr>
                            <td></td>
                            <td class="label">CGSTAMT</td>
                            <td></td>
                            <td class="label">SGSTAMT</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="value" id="totalCgst">0.00</td>
                            <td></td>
                            <td class="value" id="totalSgst">0.00</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label style="width: 60px;">Remarks</label>
                        <input type="text" class="form-control" id="remarks" style="flex: 1;" data-custom-enter>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="saveVoucher()">
                <i class="bi bi-check-circle me-1"></i> Save
            </button>
            <button type="button" class="btn-hsn px-4" onclick="window.location.href='{{ route('admin.sale-voucher.index') }}'">
                <i class="bi bi-x-circle me-1"></i> Exit
            </button>
        </div>
    </div>
</div>

<!-- HSN Selection Modal -->
<div class="hsn-modal-backdrop" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="hsn-modal" id="hsnModal">
    <div class="hsn-modal-header">
        <h5 class="hsn-modal-title"><i class="bi bi-list-ol me-2"></i>Select HSN Code</h5>
        <button type="button" onclick="closeHsnModal()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="hsn-modal-body">
        <input type="text" class="form-control mb-2" id="hsnSearch" placeholder="Search HSN Code..." oninput="filterHsn()">
        <div id="hsnList">
            @foreach($hsnCodes as $hsn)
            <div class="hsn-list-item" data-hsn="{{ $hsn->hsn_code }}" onclick="selectHsn('{{ $hsn->hsn_code }}', {{ $hsn->cgst_percent }}, {{ $hsn->sgst_percent }}, {{ $hsn->total_gst_percent }})">
                <strong>{{ $hsn->hsn_code }}</strong> - {{ $hsn->name }} (GST: {{ $hsn->total_gst_percent }}%)
            </div>
            @endforeach
        </div>
    </div>
    <div class="hsn-modal-footer">
        <button type="button" class="btn-hsn" onclick="closeHsnModal()">Close</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('saleDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    // Add initial empty row
    addNewRow();
    
    // Focus date field on page load
    setTimeout(() => { document.getElementById('saleDate')?.focus(); }, 300);
});

let rowCounter = 0;
let selectedRowIndex = null;

// ============================================================================
// CUSTOM CUSTOMER DROPDOWN
// ============================================================================
let customerActiveIndex = -1;

function openCustomerDropdown() {
    const existingId = document.getElementById('customerId').value;
    if (existingId) return;
    document.getElementById('sv_customerDisplay').select();
    document.querySelectorAll('#sv_customerList .sv-dd-item').forEach(i => { i.style.display = ''; });
    document.getElementById('sv_customerList').classList.add('show');
    customerActiveIndex = 0;
    highlightCustomerItem();
}

function closeCustomerDropdown() {
    setTimeout(() => {
        document.getElementById('sv_customerList')?.classList.remove('show');
        customerActiveIndex = -1;
    }, 200);
}

function filterCustomerInput() {
    const existingId = document.getElementById('customerId').value;
    if (existingId) {
        document.getElementById('customerId').value = '';
        window.selectedCustomerName = '';
    }
    const list = document.getElementById('sv_customerList');
    if (!list.classList.contains('show')) {
        document.querySelectorAll('#sv_customerList .sv-dd-item').forEach(i => { i.style.display = ''; });
        list.classList.add('show');
    }
    const filter = document.getElementById('sv_customerDisplay').value.toLowerCase();
    document.querySelectorAll('#sv_customerList .sv-dd-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
    customerActiveIndex = 0;
    highlightCustomerItem();
}

function selectCustomer(id, name) {
    document.getElementById('customerId').value = id;
    document.getElementById('sv_customerDisplay').value = name;
    document.getElementById('sv_customerList').classList.remove('show');
    window.selectedCustomerName = name;
    customerActiveIndex = -1;
    document.getElementById('sv_salesmanDisplay')?.focus();
    setTimeout(() => { openSalesmanDropdown(); }, 50);
}

function highlightCustomerItem() {
    const items = Array.from(document.querySelectorAll('#sv_customerList .sv-dd-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => { i.classList.remove('active'); i.style.backgroundColor = ''; });
    if (customerActiveIndex >= items.length) customerActiveIndex = 0;
    if (customerActiveIndex < 0) customerActiveIndex = items.length - 1;
    if (customerActiveIndex >= 0 && items[customerActiveIndex]) {
        items[customerActiveIndex].classList.add('active');
        items[customerActiveIndex].style.backgroundColor = '#e6f3ff';
        items[customerActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// ============================================================================
// CUSTOM SALESMAN DROPDOWN
// ============================================================================
let salesmanActiveIndex = -1;

function openSalesmanDropdown() {
    const existingId = document.getElementById('salesmanId').value;
    if (existingId) return;
    document.getElementById('sv_salesmanDisplay').select();
    document.querySelectorAll('#sv_salesmanList .sv-dd-item').forEach(i => { i.style.display = ''; });
    document.getElementById('sv_salesmanList').classList.add('show');
    salesmanActiveIndex = 0;
    highlightSalesmanItem();
}

function closeSalesmanDropdown() {
    setTimeout(() => {
        document.getElementById('sv_salesmanList')?.classList.remove('show');
        salesmanActiveIndex = -1;
    }, 200);
}

function filterSalesmanInput() {
    const existingId = document.getElementById('salesmanId').value;
    if (existingId) {
        document.getElementById('salesmanId').value = '';
        window.selectedSalesmanName = '';
    }
    const list = document.getElementById('sv_salesmanList');
    if (!list.classList.contains('show')) {
        document.querySelectorAll('#sv_salesmanList .sv-dd-item').forEach(i => { i.style.display = ''; });
        list.classList.add('show');
    }
    const filter = document.getElementById('sv_salesmanDisplay').value.toLowerCase();
    document.querySelectorAll('#sv_salesmanList .sv-dd-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
    salesmanActiveIndex = 0;
    highlightSalesmanItem();
}

function selectSalesman(id, name) {
    document.getElementById('salesmanId').value = id;
    document.getElementById('sv_salesmanDisplay').value = name;
    document.getElementById('sv_salesmanList').classList.remove('show');
    window.selectedSalesmanName = name;
    salesmanActiveIndex = -1;
    // Move to Cash field with delay to ensure focus
    setTimeout(() => { document.getElementById('cashFlag')?.focus(); }, 50);
}

function highlightSalesmanItem() {
    const items = Array.from(document.querySelectorAll('#sv_salesmanList .sv-dd-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => { i.classList.remove('active'); i.style.backgroundColor = ''; });
    if (salesmanActiveIndex >= items.length) salesmanActiveIndex = 0;
    if (salesmanActiveIndex < 0) salesmanActiveIndex = items.length - 1;
    if (salesmanActiveIndex >= 0 && items[salesmanActiveIndex]) {
        items[salesmanActiveIndex].classList.add('active');
        items[salesmanActiveIndex].style.backgroundColor = '#e6f3ff';
        items[salesmanActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#sv_customerDropdownWrapper')) {
        document.getElementById('sv_customerList')?.classList.remove('show');
    }
    if (!e.target.closest('#sv_salesmanDropdownWrapper')) {
        document.getElementById('sv_salesmanList')?.classList.remove('show');
    }
});

// ============================================================================
// HSN TABLE FUNCTIONS
// ============================================================================

function addNewRow() {
    rowCounter++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    const currentRowId = rowCounter;
    row.innerHTML = `
        <td><input type="text" class="hsn-code" data-row="${currentRowId}" onclick="selectRow(${currentRowId})" onfocus="selectRow(${currentRowId})" onchange="onHsnChange(this)" placeholder="Enter HSN" data-custom-enter onkeydown="handleHsnKeydown(event, ${currentRowId})"></td>
        <td><input type="number" class="amount" step="0.01" onchange="calculateRowTax(${currentRowId})" oninput="calculateRowTax(${currentRowId})" placeholder="0.00" data-custom-enter onfocus="selectRow(${currentRowId})" onkeydown="handleAmountKeydown(event, ${currentRowId})"></td>
        <td><input type="number" class="gst-percent" step="0.01" onchange="calculateRowTax(${currentRowId})" placeholder="0" data-custom-enter onfocus="selectRow(${currentRowId})" onkeydown="handleGstKeydown(event, ${currentRowId})"></td>
        <td><input type="number" class="cgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="cgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="qty" value="0" step="1" min="0" placeholder="0" data-custom-enter onfocus="selectRow(${currentRowId})" onkeydown="handleQtyKeydown(event, ${currentRowId})"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${currentRowId})" title="Delete Row"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
}

function selectRow(rowIndex) {
    selectedRowIndex = rowIndex;
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('row-selected'));
    document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`)?.classList.add('row-selected');
}

function deleteRow(rowIndex) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        if (selectedRowIndex === rowIndex) selectedRowIndex = null;
        calculateTotals();
        if (document.querySelectorAll('#hsnTableBody tr').length === 0) addNewRow();
    }
}

function onHsnChange(input) {
    const row = input.closest('tr');
    const lastRow = document.querySelector('#hsnTableBody tr:last-child');
    if (row === lastRow && input.value.trim() !== '') addNewRow();
}

// ============================================================================
// HSN ROW KEYBOARD HANDLERS
// ============================================================================

function handleHsnKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        if (event.ctrlKey) return;
        event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation();
        if (event.shiftKey) {
            // Go back to previous row's Qty or Cash field
            const currentRow = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
            const prevRow = currentRow?.previousElementSibling;
            if (prevRow) {
                const prevRowId = prevRow.getAttribute('data-row');
                selectRow(parseInt(prevRowId));
                prevRow.querySelector('.qty')?.focus();
            } else {
                document.getElementById('cashFlag')?.focus();
            }
            return;
        }
        const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
        if (!row) return;
        const hsnCode = row.querySelector('.hsn-code').value.trim();
        if (!hsnCode) {
            openHsnModal();
            return;
        }
        row.querySelector('.amount')?.focus();
    }
}

function handleAmountKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        if (event.ctrlKey) return;
        event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation();
        calculateRowTax(rowIndex);
        if (event.shiftKey) {
            const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
            row?.querySelector('.hsn-code')?.focus();
            return;
        }
        const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
        row?.querySelector('.gst-percent')?.focus();
    }
}

function handleGstKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        if (event.ctrlKey) return;
        event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation();
        calculateRowTax(rowIndex);
        if (event.shiftKey) {
            const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
            row?.querySelector('.amount')?.focus();
            return;
        }
        const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
        row?.querySelector('.qty')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        if (event.ctrlKey) return;
        event.preventDefault(); event.stopPropagation(); event.stopImmediatePropagation();
        if (event.shiftKey) {
            const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
            row?.querySelector('.gst-percent')?.focus();
            return;
        }
        // Move to next row's HSN Code, or add new row
        const currentRow = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
        const nextRow = currentRow?.nextElementSibling;
        if (nextRow) {
            const nextRowId = nextRow.getAttribute('data-row');
            selectRow(parseInt(nextRowId));
            nextRow.querySelector('.hsn-code')?.focus();
        } else {
            addNewRow();
            setTimeout(() => {
                const lastRow = document.querySelector('#hsnTableBody tr:last-child');
                if (lastRow) {
                    const lastRowId = lastRow.getAttribute('data-row');
                    selectRow(parseInt(lastRowId));
                    lastRow.querySelector('.hsn-code')?.focus();
                }
            }, 50);
        }
    }
}

function calculateRowTax(rowIndex) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const grossAmount = parseFloat(row.querySelector('.amount').value) || 0;
    const gstPercent = parseFloat(row.querySelector('.gst-percent').value) || 0;
    
    const cgstPercent = gstPercent / 2;
    const sgstPercent = gstPercent / 2;
    const cgstAmount = (grossAmount * cgstPercent) / 100;
    const sgstAmount = (grossAmount * sgstPercent) / 100;
    
    row.querySelector('.cgst-percent').value = cgstPercent.toFixed(2);
    row.querySelector('.cgst-amount').value = cgstAmount.toFixed(2);
    row.querySelector('.sgst-percent').value = sgstPercent.toFixed(2);
    row.querySelector('.sgst-amount').value = sgstAmount.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let totalGross = 0, totalCgst = 0, totalSgst = 0;
    
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        totalGross += parseFloat(row.querySelector('.amount')?.value) || 0;
        totalCgst += parseFloat(row.querySelector('.cgst-amount')?.value) || 0;
        totalSgst += parseFloat(row.querySelector('.sgst-amount')?.value) || 0;
    });
    
    const totalGst = totalCgst + totalSgst;
    const netAmt = totalGross + totalGst;
    const roundOff = Math.round(netAmt) - netAmt;
    const finalAmount = Math.round(netAmt);
    
    document.getElementById('grossAmt').textContent = totalGross.toFixed(2);
    document.getElementById('totalGst').textContent = totalGst.toFixed(2);
    document.getElementById('netAmt').textContent = netAmt.toFixed(2);
    document.getElementById('roundOff').textContent = roundOff.toFixed(2);
    document.getElementById('finalAmount').textContent = finalAmount.toFixed(2);
    document.getElementById('totalCgst').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgst').textContent = totalSgst.toFixed(2);
    document.getElementById('totalDisplay').textContent = finalAmount.toFixed(2);
}

// ============================================================================
// HSN MODAL
// ============================================================================
let hsnActiveIndex = -1;

function openHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
    hsnActiveIndex = -1;
    document.getElementById('hsnSearch').value = '';
    filterHsn();
    setTimeout(() => { document.getElementById('hsnSearch')?.focus(); }, 100);
}

function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
}

function filterHsn() {
    const search = document.getElementById('hsnSearch').value.toLowerCase();
    document.querySelectorAll('.hsn-list-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
    hsnActiveIndex = 0;
    highlightHsnItem();
}

function highlightHsnItem() {
    const items = Array.from(document.querySelectorAll('.hsn-list-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => { i.classList.remove('selected'); });
    if (hsnActiveIndex < 0) hsnActiveIndex = 0;
    if (hsnActiveIndex >= items.length) hsnActiveIndex = items.length - 1;
    if (hsnActiveIndex >= 0 && items[hsnActiveIndex]) {
        items[hsnActiveIndex].classList.add('selected');
        items[hsnActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

function selectHsn(code, cgst, sgst, gst) {
    let targetRow = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    
    if (!targetRow) {
        const rows = document.querySelectorAll('#hsnTableBody tr');
        for (let row of rows) {
            if (!row.querySelector('.hsn-code').value) { targetRow = row; break; }
        }
    }
    
    if (!targetRow) {
        addNewRow();
        targetRow = document.querySelector('#hsnTableBody tr:last-child');
    }
    
    targetRow.querySelector('.hsn-code').value = code;
    targetRow.querySelector('.gst-percent').value = gst;
    targetRow.querySelector('.cgst-percent').value = cgst;
    targetRow.querySelector('.sgst-percent').value = sgst;
    
    closeHsnModal();
    targetRow.querySelector('.amount')?.focus();
}

// ============================================================================
// SAVE VOUCHER
// ============================================================================

function saveVoucher() {
    const customerId = document.getElementById('customerId').value;
    if (!customerId) { alert('Please select a customer'); return; }
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code').value;
        const grossAmount = parseFloat(row.querySelector('.amount').value) || 0;
        
        if (hsnCode && grossAmount > 0) {
            items.push({
                hsn_code: hsnCode,
                gross_amount: grossAmount,
                amount: grossAmount,
                gst_percent: parseFloat(row.querySelector('.gst-percent').value) || 0,
                cgst_percent: parseFloat(row.querySelector('.cgst-percent').value) || 0,
                cgst_amount: parseFloat(row.querySelector('.cgst-amount').value) || 0,
                sgst_percent: parseFloat(row.querySelector('.sgst-percent').value) || 0,
                sgst_amount: parseFloat(row.querySelector('.sgst-amount').value) || 0,
                qty: parseInt(row.querySelector('.qty').value) || 1
            });
        }
    });
    
    if (items.length === 0) { alert('Please add at least one item'); return; }
    
    const data = {
        sale_date: document.getElementById('saleDate').value,
        due_date: document.getElementById('dueDate').value,
        customer_id: customerId,
        salesman_id: document.getElementById('salesmanId').value || null,
        remarks: document.getElementById('remarks').value,
        items: items
    };
    
    if (typeof window.markAsSaving === 'function') window.markAsSaving();
    
    fetch('{{ route("admin.sale-voucher.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) { alert('Voucher saved! Invoice No: ' + result.invoice_no); window.location.reload(); }
        else { alert('Error: ' + result.message); }
    })
    .catch(e => { console.error(e); alert('Error saving voucher'); });
}

// Toggle Cash Y/N
function toggleCash() {
    const cashField = document.getElementById('cashFlag');
    cashField.value = cashField.value === 'N' ? 'Y' : 'N';
}

// ============================================================================
// GLOBAL KEYBOARD NAVIGATION
// ============================================================================
document.addEventListener('keydown', function(e) {
    // HSN Modal keyboard handler
    const hsnModal = document.getElementById('hsnModal');
    if (hsnModal && hsnModal.classList.contains('show')) {
        if (e.key === 'ArrowDown') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); hsnActiveIndex++; highlightHsnItem(); return false; }
        if (e.key === 'ArrowUp') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); hsnActiveIndex--; highlightHsnItem(); return false; }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const items = Array.from(document.querySelectorAll('.hsn-list-item')).filter(i => i.style.display !== 'none');
            if (hsnActiveIndex >= 0 && items[hsnActiveIndex]) { items[hsnActiveIndex].click(); }
            return false;
        }
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); closeHsnModal(); return false; }
        e.stopPropagation(); e.stopImmediatePropagation();
        return;
    }

    const activeEl = document.activeElement;

    if (e.key === 'Enter') {
        if (!activeEl) return;

        // Ctrl+Enter → Remarks
        if (e.ctrlKey && !e.shiftKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('remarks')?.focus();
            document.getElementById('remarks')?.select();
            return false;
        }

        // Shift+Enter backward navigation for header fields
        if (e.shiftKey && !e.ctrlKey) {
            const backMap = {
                'saleDate': null, // already at first
                'dueDate': 'saleDate',
                'sv_customerDisplay': 'dueDate',
                'sv_salesmanDisplay': 'sv_customerDisplay',
                'cashFlag': 'sv_salesmanDisplay'
            };
            if (backMap[activeEl.id] !== undefined) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                if (backMap[activeEl.id]) {
                    // Close any open dropdown
                    document.getElementById('sv_customerList')?.classList.remove('show');
                    document.getElementById('sv_salesmanList')?.classList.remove('show');
                    document.getElementById(backMap[activeEl.id])?.focus();
                }
                return false;
            }
        }

        // Customer Dropdown
        if (activeEl.id === 'sv_customerDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingId = document.getElementById('customerId').value;
            const list = document.getElementById('sv_customerList');
            if (existingId) {
                list.classList.remove('show');
                document.getElementById('sv_customerDisplay').value = window.selectedCustomerName || '';
                document.getElementById('sv_salesmanDisplay')?.focus();
                setTimeout(() => { openSalesmanDropdown(); }, 50);
                return false;
            }
            if (list.classList.contains('show')) {
                const items = Array.from(document.querySelectorAll('#sv_customerList .sv-dd-item')).filter(i => i.style.display !== 'none');
                if (customerActiveIndex >= 0 && customerActiveIndex < items.length) { items[customerActiveIndex].click(); }
                else { list.classList.remove('show'); document.getElementById('sv_salesmanDisplay')?.focus(); setTimeout(() => { openSalesmanDropdown(); }, 50); }
            } else { document.getElementById('sv_salesmanDisplay')?.focus(); setTimeout(() => { openSalesmanDropdown(); }, 50); }
            return false;
        }

        // Salesman Dropdown
        if (activeEl.id === 'sv_salesmanDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingId = document.getElementById('salesmanId').value;
            const list = document.getElementById('sv_salesmanList');
            if (existingId) {
                list.classList.remove('show');
                document.getElementById('sv_salesmanDisplay').value = window.selectedSalesmanName || '';
                document.getElementById('cashFlag')?.focus();
                return false;
            }
            if (list.classList.contains('show')) {
                const items = Array.from(document.querySelectorAll('#sv_salesmanList .sv-dd-item')).filter(i => i.style.display !== 'none');
                if (salesmanActiveIndex >= 0 && salesmanActiveIndex < items.length) { items[salesmanActiveIndex].click(); }
                else { list.classList.remove('show'); document.getElementById('cashFlag')?.focus(); }
            } else { document.getElementById('cashFlag')?.focus(); }
            return false;
        }

        // Date → Due Date
        if (activeEl.id === 'saleDate') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('dueDate')?.focus(); return false;
        }
        // Due Date → Customer Name
        if (activeEl.id === 'dueDate') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sv_customerDisplay')?.focus();
            setTimeout(() => { openCustomerDropdown(); }, 50);
            return false;
        }
        // Cash → first row HSN
        if (activeEl.id === 'cashFlag') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const firstRow = document.querySelector('#hsnTableBody tr');
            if (firstRow) {
                selectRow(parseInt(firstRow.getAttribute('data-row')));
                firstRow.querySelector('.hsn-code')?.focus();
            }
            return false;
        }
    }

    // Cash field - Arrow keys and Y/N keys to toggle
    if (activeEl && activeEl.id === 'cashFlag') {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            activeEl.value = activeEl.value === 'N' ? 'Y' : 'N';
            return false;
        }
        if (e.key === 'y' || e.key === 'Y') { e.preventDefault(); activeEl.value = 'Y'; return false; }
        if (e.key === 'n' || e.key === 'N') { e.preventDefault(); activeEl.value = 'N'; return false; }
    }

    // Dropdown arrow navigation - Customer
    if (activeEl && activeEl.id === 'sv_customerDisplay') {
        const list = document.getElementById('sv_customerList');
        if (list && list.classList.contains('show')) {
            if (e.key === 'ArrowDown') { e.preventDefault(); customerActiveIndex++; highlightCustomerItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); customerActiveIndex--; highlightCustomerItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); list.classList.remove('show'); return false; }
        }
    }
    // Dropdown arrow navigation - Salesman
    if (activeEl && activeEl.id === 'sv_salesmanDisplay') {
        const list = document.getElementById('sv_salesmanList');
        if (list && list.classList.contains('show')) {
            if (e.key === 'ArrowDown') { e.preventDefault(); salesmanActiveIndex++; highlightSalesmanItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); salesmanActiveIndex--; highlightSalesmanItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); list.classList.remove('show'); return false; }
        }
    }

    // Ctrl+S save
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) { e.preventDefault(); saveVoucher(); return false; }
}, true);
</script>
@endpush
