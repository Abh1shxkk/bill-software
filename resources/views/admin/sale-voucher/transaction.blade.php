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
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Sale Voucher (HSN Entry)</h5>
    <div class="d-flex gap-2">
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
                        <label>Series :</label>
                        <input type="text" class="form-control" id="series" value="S2" style="width: 60px;" readonly>
                        <span style="font-weight: 600; margin-left: 5px;">GST VOUCHER</span>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="saleDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                        <input type="text" class="form-control" id="dayName" value="{{ date('l') }}" readonly style="width: 80px; background: #e9ecef;">
                    </div>
                    <div class="field-group">
                        <label>Inv. No :</label>
                        <input type="text" class="form-control" id="invoiceNo" value="{{ $nextInvoiceNo }}" style="width: 120px;" readonly>
                    </div>
                    <div class="field-group">
                        <label>Due Date :</label>
                        <input type="date" class="form-control" id="dueDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Name :</label>
                        <input type="hidden" id="customerId">
                        <select class="form-control" id="customerSelect" style="width: 250px;">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-code="{{ $customer->code }}">{{ $customer->name }}</option>
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
                        <input type="text" class="form-control" id="cashFlag" value="N" style="width: 30px; text-transform: uppercase; cursor: pointer;" readonly onclick="toggleCash()" title="Click to toggle Y/N">
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
                        <input type="text" class="form-control" id="remarks" style="flex: 1;">
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
document.addEventListener('DOMContentLoaded', function() {
    // Update day name on date change
    document.getElementById('saleDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    // Customer change - update hidden ID
    document.getElementById('customerSelect').addEventListener('change', function() {
        document.getElementById('customerId').value = this.value;
    });
    
    // Add initial empty rows
    for (let i = 0; i < 5; i++) {
        addNewRow();
    }
});

let rowCounter = 0;
let selectedRowIndex = null;

function addNewRow() {
    rowCounter++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    const currentRowId = rowCounter;
    row.innerHTML = `
        <td><input type="text" class="hsn-code" data-row="${currentRowId}" onclick="selectRow(${currentRowId})" onchange="onHsnChange(this)" placeholder="Enter HSN"></td>
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

function deleteSelectedRow() {
    if (!selectedRowIndex) {
        alert('Please select a row first');
        return;
    }
    deleteRow(selectedRowIndex);
}

function deleteRow(rowIndex) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        if (selectedRowIndex === rowIndex) {
            selectedRowIndex = null;
        }
        calculateTotals();
        
        // Ensure at least one row exists
        if (document.querySelectorAll('#hsnTableBody tr').length === 0) {
            addNewRow();
        }
    }
}

function onHsnChange(input) {
    const row = input.closest('tr');
    // Check if last row and has value, add new row
    const lastRow = document.querySelector('#hsnTableBody tr:last-child');
    if (row === lastRow && input.value.trim() !== '') {
        addNewRow();
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
    // Find selected row or first empty row
    let targetRow = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    
    if (!targetRow) {
        // Find first empty row
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

function saveVoucher() {
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
                qty: parseInt(row.querySelector('.qty').value) || 1
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
    
    fetch('{{ route("admin.sale-voucher.store") }}', {
        method: 'POST',
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
            alert('Voucher saved successfully! Invoice No: ' + result.invoice_no);
            window.location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error saving voucher');
    });
}

// Toggle Cash Y/N
function toggleCash() {
    const cashField = document.getElementById('cashFlag');
    cashField.value = cashField.value === 'N' ? 'Y' : 'N';
}

// ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHsnModal();
    }
});
</script>
@endpush
