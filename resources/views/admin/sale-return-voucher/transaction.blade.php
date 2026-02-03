@extends('layouts.admin')

@section('title', 'Sale Return Voucher')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 10px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; color: #c00; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .field-group label { width: 80px; font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .hsn-table-container { background: #f5d4a5; padding: 10px; border: 2px solid #8b6914; }
    .hsn-table { width: 100%; border-collapse: collapse; font-size: 11px; background: white; }
    .hsn-table th { background: #c49c1a; color: white; padding: 6px 8px; text-align: center; border: 1px solid #8b6914; font-weight: 600; }
    .hsn-table td { padding: 4px; border: 1px solid #ccc; }
    .hsn-table input { width: 100%; border: 1px solid #ccc; padding: 3px 5px; font-size: 11px; height: 24px; }
    .hsn-table-scroll { max-height: 180px; overflow-y: auto; display: block; }
    .hsn-table thead, .hsn-table tbody tr { display: table; width: 100%; table-layout: fixed; }
    .totals-section { background: #f5d4a5; padding: 10px; margin-top: 10px; border: 2px solid #8b6914; }
    .totals-table { font-size: 11px; }
    .totals-table .label { font-weight: 600; color: #c00; text-align: right; }
    .totals-table .value { background: #fff; border: 1px solid #ccc; padding: 3px 8px; min-width: 80px; text-align: right; }
    .btn-hsn { background: #c49c1a; color: white; border: 1px solid #8b6914; padding: 4px 12px; font-size: 11px; cursor: pointer; }
    .btn-hsn:hover { background: #a38316; }
    .hsn-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
    .hsn-modal-backdrop.show { display: block; }
    .hsn-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: #f0f0f0; border: 2px solid #666; z-index: 9999; }
    .hsn-modal.show { display: block; }
    .hsn-modal-header { background: #c49c1a; color: white; padding: 8px 12px; display: flex; justify-content: space-between; }
    .hsn-modal-body { max-height: 350px; overflow-y: auto; padding: 10px; }
    .hsn-list-item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; font-size: 11px; }
    .hsn-list-item:hover { background: #fff8e6; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Voucher (HSN Entry)</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-info btn-sm" onclick="openHsnModal()"><i class="bi bi-plus-circle me-1"></i> Open HSN</button>
        <a href="{{ route('admin.sale-return-voucher.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Vouchers</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <div class="header-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Invoice No :</label>
                        <input type="text" class="form-control" id="invoiceNo" value="{{ $nextInvoiceNo }}" style="width: 120px;" readonly>
                    </div>
                    <div class="field-group">
                        <label>Date :</label>
                        <input type="date" class="form-control" id="returnDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <label>Customer :</label>
                        <select class="form-control" id="customerSelect" style="width: 250px;">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2" style="background: #fff8e6;">
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
                    <div class="field-group">
                        <label style="width: 60px;">Remarks</label>
                        <input type="text" class="form-control" id="remarks" style="flex: 1;">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn-hsn px-4" onclick="saveVoucher()"><i class="bi bi-check-circle me-1"></i> Save</button>
            <button type="button" class="btn-hsn px-4" onclick="window.location.href='{{ route('admin.sale-return-voucher.index') }}'"><i class="bi bi-x-circle me-1"></i> Exit</button>
        </div>
    </div>
</div>

<!-- HSN Modal -->
<div class="hsn-modal-backdrop" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="hsn-modal" id="hsnModal">
    <div class="hsn-modal-header">
        <h5 style="font-size: 13px; margin: 0;"><i class="bi bi-list-ol me-2"></i>Select HSN Code</h5>
        <button type="button" onclick="closeHsnModal()" style="background: none; border: none; color: white; font-size: 18px;">&times;</button>
    </div>
    <div class="hsn-modal-body">
        <input type="text" class="form-control mb-2" id="hsnSearch" placeholder="Search HSN..." onkeyup="filterHsn()">
        <div id="hsnList">
            @foreach($hsnCodes as $hsn)
            <div class="hsn-list-item" onclick="selectHsn('{{ $hsn->hsn_code }}', {{ $hsn->cgst_percent }}, {{ $hsn->sgst_percent }}, {{ $hsn->total_gst_percent }})">
                <strong>{{ $hsn->hsn_code }}</strong> - {{ $hsn->name }} ({{ $hsn->total_gst_percent }}%)
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let rowCounter = 0, selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() { for (let i = 0; i < 5; i++) addNewRow(); });

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

function selectRow(i) { selectedRowIndex = i; document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('row-selected')); document.querySelector(`#hsnTableBody tr[data-row="${i}"]`)?.classList.add('row-selected'); }
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

function openHsnModal() { document.getElementById('hsnModalBackdrop').classList.add('show'); document.getElementById('hsnModal').classList.add('show'); }
function closeHsnModal() { document.getElementById('hsnModalBackdrop').classList.remove('show'); document.getElementById('hsnModal').classList.remove('show'); }
function filterHsn() { const s = document.getElementById('hsnSearch').value.toLowerCase(); document.querySelectorAll('.hsn-list-item').forEach(i => i.style.display = i.textContent.toLowerCase().includes(s) ? '' : 'none'); }

function selectHsn(code, cgst, sgst, gst) {
    let t = selectedRowIndex ? document.querySelector(`#hsnTableBody tr[data-row="${selectedRowIndex}"]`) : null;
    if (!t) for (let r of document.querySelectorAll('#hsnTableBody tr')) if (!r.querySelector('.hsn-code').value) { t = r; break; }
    if (!t) { addNewRow(); t = document.querySelector('#hsnTableBody tr:last-child'); }
    t.querySelector('.hsn-code').value = code; t.querySelector('.gst-percent').value = gst; t.querySelector('.cgst-percent').value = cgst; t.querySelector('.sgst-percent').value = sgst;
    closeHsnModal(); t.querySelector('.amount').focus();
}

function saveVoucher() {
    const cid = document.getElementById('customerSelect').value;
    if (!cid) { alert('Select a customer'); return; }
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(r => {
        const h = r.querySelector('.hsn-code').value, a = parseFloat(r.querySelector('.amount').value)||0;
        if (h && a > 0) items.push({ hsn_code: h, amount: a, gst_percent: parseFloat(r.querySelector('.gst-percent').value)||0, cgst_percent: parseFloat(r.querySelector('.cgst-percent').value)||0, cgst_amount: parseFloat(r.querySelector('.cgst-amount').value)||0, sgst_percent: parseFloat(r.querySelector('.sgst-percent').value)||0, sgst_amount: parseFloat(r.querySelector('.sgst-amount').value)||0, qty: parseInt(r.querySelector('.qty').value)||0 });
    });
    if (!items.length) { alert('Add at least one item'); return; }
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    fetch('{{ route("admin.sale-return-voucher.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ return_date: document.getElementById('returnDate').value, customer_id: cid, remarks: document.getElementById('remarks').value, items })
    }).then(r => r.json()).then(res => { if (res.success) { alert('Saved! Invoice: ' + res.invoice_no); location.reload(); } else alert('Error: ' + res.message); });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeHsnModal(); });
</script>
@endpush
