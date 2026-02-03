@extends('layouts.admin')

@section('title', 'Voucher Income (Output GST) - Modification')

@section('content')
<style>
    .voucher-form { font-size: 11px; background: #f0f0f0; }
    .voucher-form input, .voucher-form select { font-size: 11px; padding: 2px 5px; height: 24px; }
    .header-section { background: #e8e8e8; border: 1px solid #ccc; padding: 8px; }
    .field-label { color: #800080; font-weight: 600; font-size: 10px; }
    .field-label-green { color: #008000; font-weight: 600; font-size: 10px; }
    .table-grid { font-size: 10px; margin-bottom: 0; }
    .table-grid th { background: #008000; color: #fff; padding: 3px 5px; border: 1px solid #006600; }
    .table-grid td { padding: 2px 3px; border: 1px solid #ccc; background: #fff; }
    .table-grid input { font-size: 10px; padding: 1px 3px; height: 20px; border: 1px solid #ccc; width: 100%; }
    .table-hsn th { background: #800000; color: #fff; }
    .table-hsn td { background: #ffffcc; }
    .credit-section { background: #e0ffe0; border: 1px solid #008000; padding: 5px; }
    .credit-title { color: #008000; font-weight: bold; font-size: 11px; }
    .debit-section { background: #ffe0e0; border: 1px solid #cc0000; padding: 5px; }
    .debit-title { color: #cc0000; font-weight: bold; font-size: 11px; }
    .summary-row { display: flex; justify-content: space-between; padding: 2px 5px; }
    .summary-value { font-size: 11px; color: #0000ff; font-weight: bold; min-width: 80px; text-align: right; }
    .btn-action { font-size: 10px; padding: 3px 10px; }
    .footer-section { background: #d0d0d0; padding: 8px; border: 1px solid #999; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="voucherId" value="">
            <div class="header-section">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto"><span class="field-label">Search Voucher No:</span>
                        <input type="number" class="form-control" id="searchVoucherNo" style="width: 80px;"></div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-primary btn-action" onclick="loadVoucher()">Load</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="showVoucherList()">List</button>
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-auto"><span class="field-label">Voucher</span><input type="date" class="form-control" name="voucher_date" id="voucherDate" style="width: 120px;"></div>
                    <div class="col-auto"><span class="field-label">Voucher No :</span><input type="text" class="form-control bg-light" id="voucherNoDisplay" readonly style="width: 60px;"></div>
                    <div class="col-auto"><span class="field-label">L(ocal) / I(nter</span><input type="text" class="form-control" name="local_inter" id="localInter" value="L" maxlength="1" style="width: 30px;"></div>
                </div>
                <div class="row g-2 mt-1"><div class="col-12"><span class="field-label">Description :</span><input type="text" class="form-control" name="description" id="description" style="width: 100%;"></div></div>
            </div>
            <div class="row g-0">
                <div class="col-md-8 p-2">
                    <div class="table-responsive mb-2" style="max-height: 100px; overflow-y: auto;">
                        <table class="table table-grid mb-0"><thead><tr><th style="width: 100px;">Code</th><th>Name</th></tr></thead><tbody id="accountsTableBody"></tbody></table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #f8f8f8; border: 1px solid #ddd;">
                        <div class="col-auto"><span class="field-label-green">Customer</span><input type="text" class="form-control" name="customer_name" id="customerNameInput" style="width: 150px;"><input type="hidden" name="customer_id" id="customerId"></div>
                        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="openAccountModal()">Add Account</button></div>
                        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteAccount()">Delete Account</button></div>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-auto"><span class="field-label-green">GST No.</span><input type="text" class="form-control bg-light" name="gst_no" id="gstNo" readonly style="width: 150px;"></div>
                        <div class="col-auto"><span class="field-label-green">PAN No.</span><input type="text" class="form-control bg-light" name="pan_no" id="panNo" readonly style="width: 120px;"></div>
                    </div>
                    <div class="table-responsive mb-2" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-grid table-hsn mb-0">
                            <thead><tr><th>HSN Code</th><th>Amount</th><th>GST%</th><th>CGST%</th><th>Amount</th><th>SGST%</th><th>Amount</th></tr></thead>
                            <tbody id="hsnTableBody"></tbody>
                        </table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #e0e0e0;">
                        <div class="col-auto"><span class="field-label">Gross</span><input type="text" class="form-control bg-light text-end" id="grossDisplay" readonly style="width: 100px;"></div>
                        <div class="col-auto ms-auto"><button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteHsnRow()">Delete Row</button></div>
                    </div>
                    <div class="row g-1 mb-1"><div class="col-auto"><span class="field-label-green">Total GST</span><span class="summary-value" id="totalGstDisplay">0.00</span></div></div>
                    <div class="row g-1 mb-2"><div class="col-auto"><span class="field-label-green">Net Amt.</span><span class="summary-value" id="netAmtDisplay">0.00</span></div></div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="credit-section mb-2">
                        <div class="credit-title mb-2">Credit</div>
                        <div class="summary-row"><span>Amount</span><span class="summary-value" id="creditAmount">0.00</span></div>
                        <div class="summary-row"><span>Total GST</span><span class="summary-value" id="creditTotalGst">0.00</span></div>
                        <div class="summary-row"><span>Net Amt.</span><span class="summary-value" id="creditNetAmt">0.00</span></div>
                        <div class="summary-row"><span>R/Off</span><span class="summary-value" id="creditRoff">0.00</span></div>
                        <div class="summary-row" style="border-top: 1px solid #008000; margin-top: 5px; padding-top: 5px;"><strong>Total Db. Amt.</strong><span class="summary-value" id="totalCreditAmt" style="color: #008000;">0.00</span></div>
                    </div>
                    <div class="debit-section">
                        <div class="debit-title mb-2">Debit</div>
                        <div class="row g-1 mb-1">
                            <div class="col-auto"><span class="field-label">TDS @</span><input type="number" class="form-control text-end" name="tds_percent" id="tdsPercent" value="0" step="0.01" style="width: 50px;" onchange="calculateTds()"></div>
                            <div class="col-auto"><span class="field-label">%</span><input type="text" class="form-control bg-light text-end" id="tdsAmount" readonly style="width: 80px;"></div>
                        </div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Account</span><select class="form-select" name="debit_account_id" id="debitAccountId" style="width: 100%;"><option value="">Select</option></select></div></div>
                        <div class="summary-row" style="border-top: 1px solid #cc0000; margin-top: 5px; padding-top: 5px;"><strong>Total Cr</strong><span class="summary-value" id="totalDebitAmt" style="color: #cc0000;">0.00</span></div>
                    </div>
                </div>
            </div>
            <div class="footer-section d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()" id="btnDelete" disabled>Delete</button>
                <button type="button" class="btn btn-secondary btn-action" onclick="printVoucher()">Print</button>
                <button type="button" class="btn btn-warning btn-action" onclick="reverseVoucher()" id="btnReverse" disabled>Reverse</button>
                <button type="button" class="btn btn-success btn-action" onclick="updateVoucher()" id="btnUpdate" disabled>Update</button>
                <a href="{{ route('admin.voucher-income.index') }}" class="btn btn-secondary btn-action">Exit</a>
            </div>
        </form>
    </div>
</div>

<script>
const salesLedgers = @json($salesLedgers);
const generalLedgers = @json($generalLedgers);
const cashBankBooks = @json($cashBankBooks);
const hsnCodes = @json($hsnCodes);
let currentVoucherId = null, accountRowCount = 0, hsnRowCount = 0, selectedAccountRow = null, selectedHsnRow = null;

document.addEventListener('DOMContentLoaded', function() { loadDebitAccounts(); });

function loadVoucher() {
    const voucherNo = document.getElementById('searchVoucherNo').value;
    if (!voucherNo) { alert('Enter voucher number'); return; }
    fetch(`{{ url('admin/voucher-income/get-by-voucher-no') }}/${voucherNo}`)
        .then(r => r.json()).then(data => {
            if (data.success) { populateForm(data.voucher); document.getElementById('btnDelete').disabled = false; document.getElementById('btnReverse').disabled = false; document.getElementById('btnUpdate').disabled = false; }
            else alert('Voucher not found');
        }).catch(e => alert('Error loading voucher'));
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('voucherNoDisplay').value = voucher.voucher_no;
    document.getElementById('voucherDate').value = voucher.voucher_date?.split('T')[0] || '';
    document.getElementById('localInter').value = voucher.local_inter || 'L';
    document.getElementById('description').value = voucher.description || '';
    document.getElementById('customerId').value = voucher.customer_id || '';
    document.getElementById('customerNameInput').value = voucher.customer_name || '';
    document.getElementById('gstNo').value = voucher.gst_no || '';
    document.getElementById('panNo').value = voucher.pan_no || '';
    document.getElementById('tdsPercent').value = voucher.tds_percent || 0;
    document.getElementById('debitAccountId').value = voucher.debit_account_id || '';
    document.getElementById('accountsTableBody').innerHTML = '';
    if (voucher.accounts) voucher.accounts.forEach(acc => addAccountRow(acc));
    for (let i = (voucher.accounts?.length || 0); i < 3; i++) addAccountRow();
    document.getElementById('hsnTableBody').innerHTML = '';
    if (voucher.items) voucher.items.forEach(item => addHsnRow(item));
    for (let i = (voucher.items?.length || 0); i < 5; i++) addHsnRow();
    calculateTotals();
}

function addAccountRow(data = null) {
    accountRowCount++;
    const tbody = document.getElementById('accountsTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `<td><input type="text" class="account-code" name="accounts[${accountRowCount}][account_code]" value="${data?.account_code || ''}" onclick="selectAccountRowEl(this.closest('tr'))"></td>
        <td><input type="text" class="account-name" name="accounts[${accountRowCount}][account_name]" value="${data?.account_name || ''}" readonly onclick="selectAccountRowEl(this.closest('tr'))">
        <input type="hidden" class="account-type" name="accounts[${accountRowCount}][account_type]" value="${data?.account_type || ''}">
        <input type="hidden" class="account-id" name="accounts[${accountRowCount}][account_id]" value="${data?.account_id || ''}"></td>`;
    row.onclick = () => selectAccountRowEl(row);
    tbody.appendChild(row);
}

function selectAccountRowEl(row) { document.querySelectorAll('#accountsTableBody tr').forEach(r => r.classList.remove('table-primary')); row.classList.add('table-primary'); selectedAccountRow = row; }

function addHsnRow(data = null) {
    hsnRowCount++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `<td><input type="text" class="hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${data?.hsn_code || ''}" onchange="fetchHsnDetails(this)" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-amount text-end" name="items[${hsnRowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-gst text-end" name="items[${hsnRowCount}][gst_percent]" value="${data?.gst_percent || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-pct text-end bg-light" name="items[${hsnRowCount}][cgst_percent]" value="${data?.cgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-cgst-amt text-end bg-light" name="items[${hsnRowCount}][cgst_amount]" value="${data?.cgst_amount || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-pct text-end bg-light" name="items[${hsnRowCount}][sgst_percent]" value="${data?.sgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-amt text-end bg-light" name="items[${hsnRowCount}][sgst_amount]" value="${data?.sgst_amount || ''}" readonly></td>`;
    row.onclick = () => selectHsnRowEl(row);
    tbody.appendChild(row);
}

function selectHsnRowEl(row) { document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('table-warning')); row.classList.add('table-warning'); selectedHsnRow = row; }

function fetchHsnDetails(input) {
    const hsnCode = input.value.trim();
    if (!hsnCode) return;
    const matchingHsns = hsnCodes.filter(h => h.hsn_code && h.hsn_code.toString() === hsnCode.toString());
    if (matchingHsns.length === 0) return;
    const uniqueRates = []; const seenRates = new Set();
    matchingHsns.forEach(hsn => { const gst = parseFloat(hsn.total_gst_percent) || 0; if (!seenRates.has(gst)) { seenRates.add(gst); uniqueRates.push(hsn); } });
    uniqueRates.sort((a, b) => { const gstA = parseFloat(a.total_gst_percent) || 0; const gstB = parseFloat(b.total_gst_percent) || 0; return gstA === 0 ? 1 : gstB === 0 ? -1 : gstA - gstB; });
    const row = input.closest('tr');
    if (uniqueRates.length > 1) {
        const ratesList = uniqueRates.map((hsn, idx) => `${idx + 1}. ${parseFloat(hsn.total_gst_percent) || 0}% GST`).join('\n');
        const selection = prompt(`HSN ${hsnCode} has multiple GST rates:\n${ratesList}\n\nEnter number:`, '1');
        if (selection) { const idx = parseInt(selection) - 1; if (idx >= 0 && idx < uniqueRates.length) applyHsnToRow(row, uniqueRates[idx]); }
    } else applyHsnToRow(row, uniqueRates[0]);
}

function applyHsnToRow(row, hsn) {
    let cgstPct = parseFloat(hsn.cgst_percent) || 0, sgstPct = parseFloat(hsn.sgst_percent) || 0, totalGst = parseFloat(hsn.total_gst_percent) || 0;
    if (totalGst > 0 && cgstPct === 0 && sgstPct === 0) { cgstPct = totalGst / 2; sgstPct = totalGst / 2; }
    row.querySelector('.hsn-gst').value = totalGst.toFixed(2);
    row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
    row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    if (amount > 0) { row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2); row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2); }
    calculateTotals();
}

function calculateHsnRow(row) {
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPct = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    let cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct').value) || 0, sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct').value) || 0;
    if (cgstPct === 0 && sgstPct === 0 && gstPct > 0) { cgstPct = gstPct / 2; sgstPct = gstPct / 2; row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2); row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2); }
    row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2);
    row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalAmount = 0, totalCgst = 0, totalSgst = 0;
    document.querySelectorAll('#hsnTableBody tr').forEach(row => { totalAmount += parseFloat(row.querySelector('.hsn-amount')?.value) || 0; totalCgst += parseFloat(row.querySelector('.hsn-cgst-amt')?.value) || 0; totalSgst += parseFloat(row.querySelector('.hsn-sgst-amt')?.value) || 0; });
    const totalGst = totalCgst + totalSgst, netAmt = totalAmount + totalGst, roundOff = Math.round(netAmt) - netAmt, totalCredit = Math.round(netAmt);
    document.getElementById('grossDisplay').value = totalAmount.toFixed(2);
    document.getElementById('totalGstDisplay').textContent = totalGst.toFixed(2);
    document.getElementById('netAmtDisplay').textContent = netAmt.toFixed(2);
    document.getElementById('creditAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('creditTotalGst').textContent = totalGst.toFixed(2);
    document.getElementById('creditNetAmt').textContent = netAmt.toFixed(2);
    document.getElementById('creditRoff').textContent = roundOff.toFixed(2);
    document.getElementById('totalCreditAmt').textContent = totalCredit.toFixed(2);
    calculateTds();
}

function calculateTds() {
    const netAmt = parseFloat(document.getElementById('creditNetAmt').textContent) || 0;
    const tdsPct = parseFloat(document.getElementById('tdsPercent').value) || 0;
    const tdsAmt = netAmt * tdsPct / 100;
    const totalCredit = parseFloat(document.getElementById('totalCreditAmt').textContent) || 0;
    document.getElementById('tdsAmount').value = tdsAmt.toFixed(2);
    document.getElementById('totalDebitAmt').textContent = (totalCredit - tdsAmt).toFixed(2);
}

function loadDebitAccounts() {
    const select = document.getElementById('debitAccountId');
    select.innerHTML = '<option value="">Select</option>';
    cashBankBooks.forEach(acc => { const opt = document.createElement('option'); opt.value = acc.id; opt.textContent = acc.name; select.appendChild(opt); });
}

function openAccountModal() { alert('Select account from Sales Ledger or General Ledger'); }
function deleteAccount() { if (selectedAccountRow) { selectedAccountRow.remove(); selectedAccountRow = null; } else alert('Select an account row'); }
function deleteHsnRow() { if (selectedHsnRow) { selectedHsnRow.remove(); selectedHsnRow = null; calculateTotals(); } else alert('Select an HSN row'); }
function showVoucherList() { window.location.href = '{{ route("admin.voucher-income.index") }}'; }

function updateVoucher() {
    if (!currentVoucherId) { alert('Load a voucher first'); return; }
    const items = [], accounts = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => { const hsnCode = row.querySelector('.hsn-code')?.value; const amount = row.querySelector('.hsn-amount')?.value;
        if (hsnCode || amount) items.push({ hsn_code: hsnCode, amount: amount || 0, gst_percent: row.querySelector('.hsn-gst')?.value || 0, cgst_percent: row.querySelector('.hsn-cgst-pct')?.value || 0, cgst_amount: row.querySelector('.hsn-cgst-amt')?.value || 0, sgst_percent: row.querySelector('.hsn-sgst-pct')?.value || 0, sgst_amount: row.querySelector('.hsn-sgst-amt')?.value || 0 }); });
    document.querySelectorAll('#accountsTableBody tr').forEach(row => { const name = row.querySelector('.account-name')?.value; if (name) accounts.push({ account_type: row.querySelector('.account-type')?.value, account_id: row.querySelector('.account-id')?.value, account_code: row.querySelector('.account-code')?.value, account_name: name }); });
    const formData = { voucher_date: document.getElementById('voucherDate').value, local_inter: document.getElementById('localInter').value, description: document.getElementById('description').value,
        customer_id: document.getElementById('customerId').value, customer_name: document.getElementById('customerNameInput').value, gst_no: document.getElementById('gstNo').value, pan_no: document.getElementById('panNo').value,
        amount: parseFloat(document.getElementById('grossDisplay').value) || 0, total_gst: parseFloat(document.getElementById('totalGstDisplay').textContent) || 0, net_amount: parseFloat(document.getElementById('netAmtDisplay').textContent) || 0,
        round_off: parseFloat(document.getElementById('creditRoff').textContent) || 0, total_credit: parseFloat(document.getElementById('totalCreditAmt').textContent) || 0, tds_percent: parseFloat(document.getElementById('tdsPercent').value) || 0,
        tds_amount: parseFloat(document.getElementById('tdsAmount').value) || 0, debit_account_id: document.getElementById('debitAccountId').value, total_debit: parseFloat(document.getElementById('totalDebitAmt').textContent) || 0, items, accounts, _token: '{{ csrf_token() }}' };
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    fetch(`{{ url('admin/voucher-income') }}/${currentVoucherId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(formData) })
        .then(r => r.json()).then(data => { if (data.success) alert('Updated!'); else alert('Error: ' + data.message); }).catch(e => alert('Failed'));
}

function deleteVoucher() {
    if (!currentVoucherId || !confirm('Delete this voucher?')) return;
    fetch(`{{ url('admin/voucher-income') }}/${currentVoucherId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(data => { alert(data.message); if (data.success) window.location.href = '{{ route("admin.voucher-income.index") }}'; });
}

function reverseVoucher() {
    if (!currentVoucherId || !confirm('Reverse this voucher?')) return;
    fetch(`{{ url('admin/voucher-income') }}/${currentVoucherId}/reverse`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(data => { alert(data.message); if (data.success) loadVoucher(); });
}

function printVoucher() { window.print(); }
</script>
@endsection
