@extends('layouts.admin')

@section('title', 'Multi Voucher Entry - Modification')

@section('content')
<style>
    .voucher-form { font-size: 11px; background: #e8e0f0; }
    .voucher-form input, .voucher-form select { font-size: 11px; padding: 2px 5px; height: 24px; }
    .header-section { background: #d8d0e8; border: 1px solid #999; padding: 10px; }
    .field-label { color: #800080; font-weight: 600; font-size: 11px; }
    .table-grid { font-size: 11px; margin-bottom: 0; }
    .table-grid th { background: #000080; color: #fff; padding: 5px; border: 1px solid #000060; }
    .table-grid td { padding: 2px; border: 1px solid #999; background: #f8f0ff; }
    .table-grid input { font-size: 11px; padding: 2px 4px; height: 22px; border: 1px solid #ccc; width: 100%; }
    .footer-section { background: #d0d0d0; padding: 10px; border: 1px solid #999; }
    .btn-action { font-size: 11px; padding: 5px 15px; }
    .total-row { background: #d8d0e8; font-weight: bold; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-2">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="voucherId" value="">
            <div class="header-section mb-2">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Search Voucher No:</span>
                        <input type="number" class="form-control" id="searchVoucherNo" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-primary" onclick="loadVoucher()">Load</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showList()">List</button>
                    </div>
                    <div class="col-auto ms-3">
                        <span class="field-label">Voucher No:</span>
                        <input type="text" class="form-control bg-light" id="voucherNoDisplay" readonly style="width: 60px;">
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-2">
                <table class="table table-grid mb-0">
                    <thead>
                        <tr><th style="width: 100px;">DATE</th><th>DEBIT</th><th>CREDIT</th><th style="width: 120px;">AMOUNT</th><th style="width: 80px;">DrSlcd</th></tr>
                    </thead>
                    <tbody id="entriesBody"></tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-end pe-3">Total :</td>
                            <td class="text-end pe-2"><span id="totalAmount" style="color: #0000ff;">0.00</span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-12"><span class="field-label">Narration :</span><input type="text" class="form-control" name="narration" id="narration"></div>
            </div>

            <div class="footer-section d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()" id="btnDelete" disabled>Delete</button>
                <button type="button" class="btn btn-success btn-action" onclick="updateVoucher()" id="btnUpdate" disabled>Update</button>
                <a href="{{ route('admin.multi-voucher.transaction') }}" class="btn btn-primary btn-action">New Entry</a>
                <a href="{{ route('admin.multi-voucher.index') }}" class="btn btn-secondary btn-action">Close</a>
            </div>
        </form>
    </div>
</div>

<script>
const customers = @json($customers);
const suppliers = @json($suppliers);
const generalLedgers = @json($generalLedgers);
let currentVoucherId = null, rowCount = 0;

function loadVoucher() {
    const voucherNo = document.getElementById('searchVoucherNo').value;
    if (!voucherNo) { alert('Enter voucher number'); return; }
    fetch(`{{ url('admin/multi-voucher/get-by-voucher-no') }}/${voucherNo}`)
        .then(r => r.json()).then(data => {
            if (data.success) { populateForm(data.voucher); document.getElementById('btnDelete').disabled = false; document.getElementById('btnUpdate').disabled = false; }
            else alert('Voucher not found');
        });
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('voucherNoDisplay').value = voucher.voucher_no;
    document.getElementById('narration').value = voucher.narration || '';
    document.getElementById('entriesBody').innerHTML = '';
    rowCount = 0;
    if (voucher.entries) voucher.entries.forEach(entry => addRow(entry));
    for (let i = voucher.entries?.length || 0; i < 10; i++) addRow();
    calculateTotal();
}

function addRow(data = null) {
    rowCount++;
    const tbody = document.getElementById('entriesBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="date" class="entry-date" name="entries[${rowCount}][entry_date]" value="${data?.entry_date?.split('T')[0] || ''}"></td>
        <td><input type="text" class="debit-name" name="entries[${rowCount}][debit_account_name]" value="${data?.debit_account_name || ''}" readonly>
            <input type="hidden" class="debit-type" name="entries[${rowCount}][debit_account_type]" value="${data?.debit_account_type || ''}">
            <input type="hidden" class="debit-id" name="entries[${rowCount}][debit_account_id]" value="${data?.debit_account_id || ''}"></td>
        <td><input type="text" class="credit-name" name="entries[${rowCount}][credit_account_name]" value="${data?.credit_account_name || ''}" readonly>
            <input type="hidden" class="credit-type" name="entries[${rowCount}][credit_account_type]" value="${data?.credit_account_type || ''}">
            <input type="hidden" class="credit-id" name="entries[${rowCount}][credit_account_id]" value="${data?.credit_account_id || ''}"></td>
        <td><input type="number" class="entry-amount text-end" name="entries[${rowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateTotal()"></td>
        <td><input type="text" class="dr-slcd" name="entries[${rowCount}][dr_slcd]" value="${data?.dr_slcd || ''}"></td>`;
    tbody.appendChild(row);
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('#entriesBody tr').forEach(row => { total += parseFloat(row.querySelector('.entry-amount')?.value) || 0; });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function updateVoucher() {
    if (!currentVoucherId) { alert('Load a voucher first'); return; }
    const entries = [];
    document.querySelectorAll('#entriesBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.entry-amount')?.value) || 0;
        if (amount > 0) entries.push({
            entry_date: row.querySelector('.entry-date')?.value,
            debit_account_type: row.querySelector('.debit-type')?.value, debit_account_id: row.querySelector('.debit-id')?.value, debit_account_name: row.querySelector('.debit-name')?.value,
            credit_account_type: row.querySelector('.credit-type')?.value, credit_account_id: row.querySelector('.credit-id')?.value, credit_account_name: row.querySelector('.credit-name')?.value,
            amount: amount, dr_slcd: row.querySelector('.dr-slcd')?.value
        });
    });
    fetch(`{{ url('admin/multi-voucher') }}/${currentVoucherId}`, {
        method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ voucher_date: entries[0]?.entry_date, narration: document.getElementById('narration').value, total_amount: parseFloat(document.getElementById('totalAmount').textContent), entries })
    }).then(r => r.json()).then(data => { if (data.success) alert('Updated!'); else alert('Error: ' + data.message); });
}

function deleteVoucher() {
    if (!currentVoucherId || !confirm('Delete this voucher?')) return;
    fetch(`{{ url('admin/multi-voucher') }}/${currentVoucherId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(data => { alert(data.message); if (data.success) window.location.href = '{{ route("admin.multi-voucher.index") }}'; });
}

function showList() { window.location.href = '{{ route("admin.multi-voucher.index") }}'; }
</script>
@endsection
