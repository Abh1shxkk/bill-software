@extends('layouts.admin')

@section('title', 'Purchase Return Voucher Modification')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; white-space: nowrap; color: #c00; }
    .hsn-table-container { background: #d4a5d4; padding: 10px; border: 2px solid #6b1482; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #8b2082; color: white; padding: 6px 8px; text-align: center; border: 1px solid #6b1482; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .totals-section { background: #d4a5d4; padding: 10px; margin-top: 10px; border: 2px solid #6b1482; }
    .totals-table { font-size: 11px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    .btn-hsn { background: #8b2082; color: white; border: 1px solid #6b1482; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #6b1482; }
    .invoice-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
    .invoice-modal.show { display: flex; align-items: center; justify-content: center; }
    .invoice-modal-content { background: white; width: 600px; max-height: 80vh; border-radius: 8px; overflow: hidden; }
    .invoice-modal-header { background: #8b2082; color: white; padding: 12px; display: flex; justify-content: space-between; }
    .invoice-modal-body { max-height: 60vh; overflow-y: auto; padding: 15px; }
    .invoice-list-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
    .invoice-list-item:hover { background: #f8e6f8; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Purchase Return Voucher Modification</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInvoiceModal()"><i class="bi bi-list me-1"></i> Load Invoices</button>
        <a href="{{ route('admin.purchase-return-voucher.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Vouchers</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Invoice No :</label>
                        <input type="text" class="form-control" id="searchInvoice" placeholder="Enter Invoice No" style="width: 150px;">
                        <button type="button" class="btn btn-sm btn-primary" onclick="searchVoucher()">Load</button>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="returnDate" style="width: 130px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Supplier :</label>
                        <input type="hidden" id="voucherId">
                        <select class="form-control" id="supplierSelect" style="width: 250px;">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2" style="background: #f8e6f8;">
                        <div class="d-flex justify-content-between" style="font-size: 11px;">
                            <span style="color: #c00; font-weight:600;">TOTAL :</span>
                            <span id="totalDisplay">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hsn-table-container">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn-hsn" onclick="addNewRow()"><i class="bi bi-plus-circle me-1"></i> Add Row</button>
            </div>
            <table class="hsn-table" id="hsnTable">
                <thead>
                    <tr>
                        <th style="width: 100px;">HSN Code</th><th style="width: 80px;">Amount</th><th style="width: 55px;">GST%</th>
                        <th style="width: 55px;">CGST%</th><th style="width: 75px;">Amount</th><th style="width: 55px;">SGST%</th>
                        <th style="width: 75px;">Amount</th><th style="width: 50px;">Qty.</th><th style="width: 50px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hsnTableBody" class="hsn-table-scroll"></tbody>
            </table>
        </div>

        <div class="totals-section">
            <div class="row">
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr><td class="label">Gross Amt.</td><td class="value" id="grossAmt">0.00</td></tr>
                        <tr><td class="label">Total GST</td><td class="value" id="totalGst">0.00</td></tr>
                        <tr><td class="label">Net Amt.</td><td class="value" id="netAmt">0.00</td></tr>
                        <tr><td class="label">Round Off</td><td class="value" id="roundOff">0.00</td></tr>
                        <tr><td class="label">Amount</td><td class="value" id="finalAmount">0.00</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="totals-table">
                        <tr><td></td><td class="label">CGST AMT</td><td></td><td class="label">SGST AMT</td></tr>
                        <tr><td></td><td class="value" id="totalCgst">0.00</td><td></td><td class="value" id="totalSgst">0.00</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="field-group"><label style="width: 60px;">Remarks</label><input type="text" class="form-control" id="remarks" style="flex: 1;"></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="updateVoucher()" id="btnUpdate" disabled><i class="bi bi-check-circle me-1"></i> Update</button>
            <button type="button" class="btn-hsn px-4" onclick="clearForm()"><i class="bi bi-x-circle me-1"></i> Clear</button>
        </div>
    </div>
</div>

<!-- Invoice List Modal -->
<div class="invoice-modal" id="invoiceModal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h6 class="mb-0"><i class="bi bi-list me-2"></i>Select Voucher</h6>
            <button type="button" onclick="closeInvoiceModal()" style="background: none; border: none; color: white; font-size: 18px;">&times;</button>
        </div>
        <div class="p-2"><input type="text" class="form-control form-control-sm" id="invoiceSearch" placeholder="Search..." onkeyup="filterInvoices()"></div>
        <div class="invoice-modal-body" id="invoiceList"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let rowCounter = 0, selectedRowIndex = null, currentVoucherId = null, vouchersData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadVouchersForModal();
    for (let i = 0; i < 5; i++) addNewRow();
    const urlParams = new URLSearchParams(window.location.search);
    const inv = urlParams.get('invoice_no');
    if (inv) { document.getElementById('searchInvoice').value = inv; searchVoucher(); }
});

function loadVouchersForModal() {
    fetch('{{ route("admin.purchase-return-voucher.get-vouchers") }}')
    .then(r => r.json()).then(res => { if (res.success) { vouchersData = res.vouchers; renderInvoiceList(vouchersData); } });
}

function renderInvoiceList(vouchers) {
    document.getElementById('invoiceList').innerHTML = vouchers.map(v => `
        <div class="invoice-list-item" onclick="loadVoucher(${v.id})">
            <div class="d-flex justify-content-between"><strong>${v.invoice_no}</strong><span class="text-muted">${v.return_date}</span></div>
            <div class="text-muted small">${v.supplier_name} - ₹${parseFloat(v.net_amount).toFixed(2)}</div>
        </div>
    `).join('');
}

function filterInvoices() { const s = document.getElementById('invoiceSearch').value.toLowerCase(); renderInvoiceList(vouchersData.filter(v => v.invoice_no.toLowerCase().includes(s) || v.supplier_name.toLowerCase().includes(s))); }
function openInvoiceModal() { document.getElementById('invoiceModal').classList.add('show'); }
function closeInvoiceModal() { document.getElementById('invoiceModal').classList.remove('show'); }

function loadVoucher(id) {
    closeInvoiceModal();
    fetch(`{{ url('admin/purchase-return-voucher') }}/${id}/details`).then(r => r.json()).then(res => { if (res.success) populateForm(res.voucher); else alert('Error: ' + res.message); });
}

function searchVoucher() {
    const inv = document.getElementById('searchInvoice').value.trim();
    if (!inv) { alert('Enter invoice number'); return; }
    fetch(`{{ route('admin.purchase-return-voucher.search') }}?invoice_no=${encodeURIComponent(inv)}`).then(r => r.json()).then(res => { if (res.success) populateForm(res.voucher); else alert('Not found'); });
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('searchInvoice').value = voucher.invoice_no;
    document.getElementById('returnDate').value = voucher.return_date;
    document.getElementById('supplierSelect').value = voucher.supplier_id;
    document.getElementById('remarks').value = voucher.remarks || '';
    document.getElementById('hsnTableBody').innerHTML = '';
    rowCounter = 0;
    if (voucher.items && voucher.items.length > 0) {
        voucher.items.forEach(item => {
            addNewRow();
            const row = document.querySelector('#hsnTableBody tr:last-child');
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
    addNewRow();
    calculateTotals();
    document.getElementById('btnUpdate').disabled = false;
}

function addNewRow() {
    rowCounter++;
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCounter);
    row.innerHTML = `
        <td><input type="text" class="hsn-code" onclick="selectRow(${rowCounter})" placeholder="HSN"></td>
        <td><input type="number" class="amount" step="0.01" onchange="calculateRowTax(${rowCounter})" oninput="calculateRowTax(${rowCounter})"></td>
        <td><input type="number" class="gst-percent" step="0.01" onchange="calculateRowTax(${rowCounter})"></td>
        <td><input type="number" class="cgst-percent" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="cgst-amount" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-percent" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="sgst-amount" readonly style="background:#e9ecef;"></td>
        <td><input type="number" class="qty" value="0" min="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${rowCounter})"><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('hsnTableBody').appendChild(row);
}

function selectRow(i) { selectedRowIndex = i; }
function deleteRow(i) { const row = document.querySelector(`#hsnTableBody tr[data-row="${i}"]`); if (row) { row.remove(); calculateTotals(); if (!document.querySelectorAll('#hsnTableBody tr').length) addNewRow(); } }

function calculateRowTax(i) {
    const row = document.querySelector(`#hsnTableBody tr[data-row="${i}"]`);
    if (!row) return;
    const amt = parseFloat(row.querySelector('.amount').value) || 0;
    const gst = parseFloat(row.querySelector('.gst-percent').value) || 0;
    row.querySelector('.cgst-percent').value = (gst/2).toFixed(2);
    row.querySelector('.cgst-amount').value = (amt*gst/200).toFixed(2);
    row.querySelector('.sgst-percent').value = (gst/2).toFixed(2);
    row.querySelector('.sgst-amount').value = (amt*gst/200).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let g=0, c=0, s=0;
    document.querySelectorAll('#hsnTableBody tr').forEach(r => { g += parseFloat(r.querySelector('.amount')?.value)||0; c += parseFloat(r.querySelector('.cgst-amount')?.value)||0; s += parseFloat(r.querySelector('.sgst-amount')?.value)||0; });
    const t = c+s, n = g+t, ro = Math.round(n)-n, f = Math.round(n);
    document.getElementById('grossAmt').textContent = g.toFixed(2);
    document.getElementById('totalGst').textContent = t.toFixed(2);
    document.getElementById('netAmt').textContent = n.toFixed(2);
    document.getElementById('roundOff').textContent = ro.toFixed(2);
    document.getElementById('finalAmount').textContent = f.toFixed(2);
    document.getElementById('totalCgst').textContent = c.toFixed(2);
    document.getElementById('totalSgst').textContent = s.toFixed(2);
    document.getElementById('totalDisplay').textContent = f.toFixed(2);
}

function clearForm() {
    currentVoucherId = null;
    document.getElementById('voucherId').value = '';
    document.getElementById('searchInvoice').value = '';
    document.getElementById('returnDate').value = '';
    document.getElementById('supplierSelect').value = '';
    document.getElementById('remarks').value = '';
    document.getElementById('hsnTableBody').innerHTML = '';
    document.getElementById('btnUpdate').disabled = true;
    rowCounter = 0;
    for (let i = 0; i < 5; i++) addNewRow();
    calculateTotals();
}

function updateVoucher() {
    if (!currentVoucherId) { alert('Load a voucher first'); return; }
    const sid = document.getElementById('supplierSelect').value;
    if (!sid) { alert('Select a supplier'); return; }
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(r => {
        const h = r.querySelector('.hsn-code').value, a = parseFloat(r.querySelector('.amount').value)||0;
        if (h && a > 0) items.push({ hsn_code: h, amount: a, gst_percent: parseFloat(r.querySelector('.gst-percent').value)||0, cgst_percent: parseFloat(r.querySelector('.cgst-percent').value)||0, cgst_amount: parseFloat(r.querySelector('.cgst-amount').value)||0, sgst_percent: parseFloat(r.querySelector('.sgst-percent').value)||0, sgst_amount: parseFloat(r.querySelector('.sgst-amount').value)||0, qty: parseInt(r.querySelector('.qty').value)||0 });
    });
    if (!items.length) { alert('Add at least one item'); return; }
    fetch(`{{ url('admin/purchase-return-voucher') }}/${currentVoucherId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ return_date: document.getElementById('returnDate').value, supplier_id: sid, remarks: document.getElementById('remarks').value, items })
    }).then(r => r.json()).then(res => { if (res.success) { alert('Updated!'); loadVouchersForModal(); } else alert('Error: ' + res.message); });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeInvoiceModal(); });
</script>
@endpush
