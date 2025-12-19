@extends('layouts.admin')

@section('title', 'Sale Voucher Modification')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    /* HSN Table Styles */
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
    .hsn-table thead { width: calc(100% - 17px); }
    
    /* Totals Section */
    .totals-section { background: #d4a5a5; padding: 10px; margin-top: 10px; border: 2px solid #8b4513; }
    .totals-table { font-size: 11px; }
    .totals-table td { padding: 4px 8px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    
    /* Button Styles */
    .btn-hsn { background: #9c6b6b; color: white; border: 1px solid #8b4513; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #8b5a5a; }
    
    /* Invoice List Modal */
    .invoice-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
    .invoice-modal.show { display: flex; align-items: center; justify-content: center; }
    .invoice-modal-content { background: white; width: 600px; max-height: 80vh; border-radius: 8px; overflow: hidden; }
    .invoice-modal-header { background: #9c6b6b; color: white; padding: 12px; display: flex; justify-content: space-between; }
    .invoice-modal-body { max-height: 60vh; overflow-y: auto; padding: 15px; }
    .invoice-list-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
    .invoice-list-item:hover { background: #f5f5f5; }
    
    /* HSN Modal */
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
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Sale Voucher Modification</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInvoiceListModal()">
            <i class="bi bi-list me-1"></i> Load Invoices
        </button>
        <button type="button" class="btn btn-info btn-sm" onclick="openHsnModal()">
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
                        <label>Inv. No :</label>
                        <input type="text" class="form-control" id="searchInvoice" placeholder="Enter Invoice No" style="width: 150px;">
                        <button type="button" class="btn btn-sm btn-primary" onclick="searchInvoice()">Load</button>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="saleDate" style="width: 130px;">
                        <input type="text" class="form-control" id="dayName" readonly style="width: 80px; background: #e9ecef;">
                    </div>
                    <div class="field-group">
                        <label>Due Date :</label>
                        <input type="date" class="form-control" id="dueDate" style="width: 130px;">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Name :</label>
                        <input type="hidden" id="voucherId">
                        <select class="form-control" id="customerSelect" style="width: 250px;">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Sales Man :</label>
                        <select class="form-control" id="salesmanSelect" style="width: 250px;">
                            <option value="">Select Salesman</option>
                            @foreach($salesmen as $sm)
                            <option value="{{ $sm->id }}">{{ $sm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Cash :</label>
                        <input type="text" class="form-control" id="cashFlag" value="N" style="width: 30px; cursor: pointer;" readonly onclick="toggleCash()">
                    </div>
                    <div class="border p-2" style="background: #fffbcc;">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">DUE :</span>
                            <span id="dueDisplay">0.00</span>
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
                            <td class="label">CGST AMT</td>
                            <td></td>
                            <td class="label">SGST AMT</td>
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
                        <input type="text" class="form-control" id="remarks" style="flex: 1;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="updateVoucher()" id="btnUpdate" disabled>
                <i class="bi bi-check-circle me-1"></i> Update
            </button>
            <button type="button" class="btn-hsn px-4" onclick="clearForm()">
                <i class="bi bi-x-circle me-1"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- Invoice List Modal -->
<div class="invoice-modal" id="invoiceModal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h6 class="mb-0"><i class="bi bi-list me-2"></i>Select Voucher</h6>
            <button type="button" onclick="closeInvoiceListModal()" style="background: none; border: none; color: white; font-size: 18px;">&times;</button>
        </div>
        <div class="p-2">
            <input type="text" class="form-control form-control-sm" id="invoiceSearch" placeholder="Search by Invoice No or Customer..." onkeyup="filterInvoices()">
        </div>
        <div class="invoice-modal-body" id="invoiceList">
            <!-- Vouchers will be loaded here -->
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
        <input type="text" class="form-control mb-2" id="hsnSearch" placeholder="Search HSN Code..." onkeyup="filterHsn()">
        <div id="hsnList">
            @foreach($hsnCodes as $hsn)
            <div class="hsn-list-item" onclick="selectHsn('{{ $hsn->hsn_code }}', {{ $hsn->cgst_percent }}, {{ $hsn->sgst_percent }}, {{ $hsn->total_gst_percent }})">
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
let rowCounter = 0;
let selectedRowIndex = null;
let currentVoucherId = null;
let vouchersData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load vouchers for modal
    loadVouchersForModal();
    
    // Update day name on date change
    document.getElementById('saleDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    // Add initial empty rows
    for (let i = 0; i < 5; i++) {
        addNewRow();
    }
    
    // Check if invoice parameter is passed
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceNo = urlParams.get('invoice');
    if (invoiceNo) {
        document.getElementById('searchInvoice').value = invoiceNo;
        searchInvoice();
    }
});

function loadVouchersForModal() {
    fetch('{{ route("admin.sale-voucher.get-vouchers") }}')
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            vouchersData = result.vouchers;
            renderInvoiceList(vouchersData);
        }
    })
    .catch(e => console.error('Error loading vouchers:', e));
}

function renderInvoiceList(vouchers) {
    const list = document.getElementById('invoiceList');
    list.innerHTML = vouchers.map(v => `
        <div class="invoice-list-item" onclick="loadVoucher(${v.id})">
            <div class="d-flex justify-content-between">
                <strong>${v.invoice_no}</strong>
                <span class="text-muted">${v.sale_date}</span>
            </div>
            <div class="text-muted small">${v.customer_name} - ₹${parseFloat(v.net_amount).toFixed(2)}</div>
        </div>
    `).join('');
}

function filterInvoices() {
    const search = document.getElementById('invoiceSearch').value.toLowerCase();
    const filtered = vouchersData.filter(v => 
        v.invoice_no.toLowerCase().includes(search) || 
        v.customer_name.toLowerCase().includes(search)
    );
    renderInvoiceList(filtered);
}

function openInvoiceListModal() {
    document.getElementById('invoiceModal').classList.add('show');
}

function closeInvoiceListModal() {
    document.getElementById('invoiceModal').classList.remove('show');
}

function loadVoucher(id) {
    closeInvoiceListModal();
    
    fetch(`{{ url('admin/sale-voucher') }}/${id}/details`)
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            populateForm(result.voucher);
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error loading voucher');
    });
}

function searchInvoice() {
    const invoiceNo = document.getElementById('searchInvoice').value.trim();
    if (!invoiceNo) {
        alert('Please enter invoice number');
        return;
    }
    
    fetch(`{{ url('admin/sale-voucher/search') }}?invoice_no=${encodeURIComponent(invoiceNo)}`)
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            populateForm(result.voucher);
        } else {
            alert('Voucher not found: ' + invoiceNo);
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error searching voucher');
    });
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('searchInvoice').value = voucher.invoice_no;
    document.getElementById('saleDate').value = voucher.sale_date;
    document.getElementById('dueDate').value = voucher.due_date || voucher.sale_date;
    document.getElementById('customerSelect').value = voucher.customer_id;
    document.getElementById('salesmanSelect').value = voucher.salesman_id || '';
    document.getElementById('remarks').value = voucher.remarks || '';
    document.getElementById('cashFlag').value = voucher.cash_flag || 'N';
    
    // Update day name
    const date = new Date(voucher.sale_date);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    document.getElementById('dayName').value = days[date.getDay()];
    
    // Clear existing rows
    document.getElementById('hsnTableBody').innerHTML = '';
    rowCounter = 0;
    
    // Add item rows
    if (voucher.items && voucher.items.length > 0) {
        voucher.items.forEach(item => {
            addNewRow();
            const row = document.querySelector(`#hsnTableBody tr:last-child`);
            row.querySelector('.hsn-code').value = item.hsn_code || '';
            row.querySelector('.amount').value = item.amount || 0;
            row.querySelector('.gst-percent').value = item.gst_percent || 0;
            row.querySelector('.cgst-percent').value = item.cgst_percent || 0;
            row.querySelector('.cgst-amount').value = item.cgst_amount || 0;
            row.querySelector('.sgst-percent').value = item.sgst_percent || 0;
            row.querySelector('.sgst-amount').value = item.sgst_amount || 0;
            row.querySelector('.qty').value = item.qty || 0;
        });
    }
    
    // Add at least one empty row
    if (voucher.items.length === 0) {
        for (let i = 0; i < 5; i++) addNewRow();
    } else {
        addNewRow(); // Add one more empty row
    }
    
    calculateTotals();
    document.getElementById('btnUpdate').disabled = false;
}

function addNewRow() {
    rowCounter++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    const currentRowId = rowCounter;
    row.innerHTML = `
        <td><input type="text" class="hsn-code" data-row="${currentRowId}" onclick="selectRow(${currentRowId})" placeholder="Enter HSN"></td>
        <td><input type="number" class="amount" step="0.01" onchange="calculateRowTax(${currentRowId})" oninput="calculateRowTax(${currentRowId})" placeholder="0.00"></td>
        <td><input type="number" class="gst-percent" step="0.01" onchange="calculateRowTax(${currentRowId})" placeholder="0"></td>
        <td><input type="number" class="cgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="cgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-percent" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-amount" step="0.01" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="qty" value="0" step="1" min="0" placeholder="0"></td>
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
    let totalGross = 0;
    let totalCgst = 0;
    let totalSgst = 0;
    
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

// HSN Modal functions
function openHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
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
}

function selectHsn(code, cgst, sgst, gst) {
    let targetRow = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    
    if (!targetRow) {
        const rows = document.querySelectorAll('#hsnTableBody tr');
        for (let row of rows) {
            if (!row.querySelector('.hsn-code').value) {
                targetRow = row;
                break;
            }
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
    targetRow.querySelector('.amount').focus();
}

function toggleCash() {
    const cashField = document.getElementById('cashFlag');
    cashField.value = cashField.value === 'N' ? 'Y' : 'N';
}

function clearForm() {
    currentVoucherId = null;
    document.getElementById('voucherId').value = '';
    document.getElementById('searchInvoice').value = '';
    document.getElementById('saleDate').value = '';
    document.getElementById('dueDate').value = '';
    document.getElementById('dayName').value = '';
    document.getElementById('customerSelect').value = '';
    document.getElementById('salesmanSelect').value = '';
    document.getElementById('remarks').value = '';
    document.getElementById('cashFlag').value = 'N';
    document.getElementById('hsnTableBody').innerHTML = '';
    document.getElementById('btnUpdate').disabled = true;
    rowCounter = 0;
    for (let i = 0; i < 5; i++) addNewRow();
    calculateTotals();
}

function updateVoucher() {
    if (!currentVoucherId) {
        alert('Please load a voucher first');
        return;
    }
    
    const customerId = document.getElementById('customerSelect').value;
    if (!customerId) {
        alert('Please select a customer');
        return;
    }
    
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
                qty: parseInt(row.querySelector('.qty').value) || 0
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const data = {
        sale_date: document.getElementById('saleDate').value,
        due_date: document.getElementById('dueDate').value,
        customer_id: customerId,
        salesman_id: document.getElementById('salesmanSelect').value || null,
        remarks: document.getElementById('remarks').value,
        items: items
    };
    
    fetch(`{{ url('admin/sale-voucher') }}/${currentVoucherId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Voucher updated successfully!');
            loadVouchersForModal(); // Refresh list
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error updating voucher');
    });
}

// ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHsnModal();
        closeInvoiceListModal();
    }
});
</script>
@endpush
