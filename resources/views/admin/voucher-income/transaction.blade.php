@extends('layouts.admin')

@section('title', 'Voucher Income (Output GST)')

@section('content')
<style>
    .voucher-form { font-size: 11px; background: #f0f0f0; }
    .voucher-form label { font-weight: 600; font-size: 10px; margin-bottom: 0; }
    .voucher-form input, .voucher-form select { font-size: 11px; padding: 2px 5px; height: 24px; }
    .header-section { background: #e8e8e8; border: 1px solid #ccc; padding: 8px; }
    .field-label { color: #800080; font-weight: 600; font-size: 10px; }
    .field-label-green { color: #008000; font-weight: 600; font-size: 10px; }
    .table-grid { font-size: 10px; margin-bottom: 0; }
    .table-grid th { background: #008000; color: #fff; font-weight: 600; text-align: center; padding: 3px 5px; border: 1px solid #006600; }
    .table-grid td { padding: 2px 3px; border: 1px solid #ccc; background: #fff; }
    .table-grid input { font-size: 10px; padding: 1px 3px; height: 20px; border: 1px solid #ccc; width: 100%; }
    .table-hsn th { background: #800000; color: #fff; }
    .table-hsn td { background: #ffffcc; }
    .table-gst th { background: #606060; color: #fff; }
    .credit-section { background: #e0ffe0; border: 1px solid #008000; padding: 5px; }
    .credit-title { color: #008000; font-weight: bold; font-size: 11px; font-style: italic; }
    .debit-section { background: #ffe0e0; border: 1px solid #cc0000; padding: 5px; }
    .debit-title { color: #cc0000; font-weight: bold; font-size: 11px; font-style: italic; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 2px 5px; }
    .summary-label { font-size: 10px; }
    .summary-value { font-size: 11px; color: #0000ff; font-weight: bold; text-align: right; min-width: 80px; }
    .btn-action { font-size: 10px; padding: 3px 10px; }
    .footer-section { background: #d0d0d0; padding: 8px; border: 1px solid #999; }
    .custom-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; }
    .custom-modal-overlay.show { display: flex; align-items: center; justify-content: center; }
    .custom-modal { background: #fff; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 700px; max-height: 80vh; display: flex; flex-direction: column; }
    .custom-modal-header { background: #008000; color: #fff; padding: 10px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .custom-modal-header h6 { margin: 0; font-size: 14px; }
    .custom-modal-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; }
    .custom-modal-body { padding: 15px; overflow-y: auto; flex: 1; }
    .custom-modal-footer { padding: 10px 15px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Voucher</span>
                        <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="{{ date('Y-m-d') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Voucher No :</span>
                        <input type="text" class="form-control bg-light" id="voucherNoDisplay" value="{{ $nextVoucherNo }}" readonly style="width: 60px;">
                        <input type="hidden" name="voucher_no" id="voucherNo" value="{{ $nextVoucherNo }}">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">L(ocal) / I(nter</span>
                        <input type="text" class="form-control" name="local_inter" id="localInter" value="L" maxlength="1" style="width: 30px; text-transform: uppercase;">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <span class="field-label">Description :</span>
                        <input type="text" class="form-control" name="description" id="description" style="width: 100%;">
                    </div>
                </div>
            </div>

            <div class="row g-0">
                <div class="col-md-8 p-2">
                    <div class="table-responsive mb-2" style="max-height: 100px; overflow-y: auto;">
                        <table class="table table-grid mb-0">
                            <thead><tr><th style="width: 100px;">Code</th><th>Name</th></tr></thead>
                            <tbody id="accountsTableBody"></tbody>
                        </table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #f8f8f8; border: 1px solid #ddd;">
                        <div class="col-auto">
                            <span class="field-label-green">Customer</span>
                            <input type="text" class="form-control" name="customer_name_input" id="customerNameInput" style="width: 150px;" list="customerDatalist">
                            <input type="hidden" name="customer_id" id="customerId">
                            <datalist id="customerDatalist">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->name }}" data-id="{{ $customer->id }}" data-gst="{{ $customer->gst_number }}" data-pan="{{ $customer->pan_number }}" data-city="{{ $customer->city }}" data-address="{{ $customer->address }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="openAccountModal()">Add Account (F9)</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteAccount()">Delete Account</button>
                        </div>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-auto"><span class="field-label-green">GST No.</span><input type="text" class="form-control bg-light" name="gst_no" id="gstNo" readonly style="width: 150px;"></div>
                        <div class="col-auto"><span class="field-label-green">PAN No.</span><input type="text" class="form-control bg-light" name="pan_no" id="panNo" readonly style="width: 120px;"></div>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-auto"><span class="field-label-green">City</span><input type="text" class="form-control bg-light" name="city" id="city" readonly style="width: 120px;"></div>
                        <div class="col-auto"><span class="field-label-green">PIN</span><input type="text" class="form-control bg-light" name="pin" id="pin" readonly style="width: 80px;"></div>
                    </div>

                    <div class="table-responsive mb-2" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-grid table-hsn mb-0">
                            <thead><tr><th style="width: 80px;">HSN Code</th><th style="width: 80px;">Amount</th><th style="width: 50px;">GST%</th><th style="width: 50px;">CGST%</th><th style="width: 70px;">Amount</th><th style="width: 50px;">SGST%</th><th style="width: 70px;">Amount</th></tr></thead>
                            <tbody id="hsnTableBody"></tbody>
                        </table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #e0e0e0;">
                        <div class="col-auto"><span class="field-label">Gross</span><input type="text" class="form-control bg-light text-end" id="grossDisplay" readonly style="width: 100px;"></div>
                        <div class="col-auto ms-auto"><button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteHsnRow()">Delete Row</button></div>
                    </div>
                    <div class="row g-1 mb-1"><div class="col-auto"><span class="field-label-green">Total GST</span><span class="summary-value" id="totalGstDisplay">0.00</span></div></div>
                    <div class="row g-1 mb-2"><div class="col-auto"><span class="field-label-green">Net Amt.</span><span class="summary-value" id="netAmtDisplay">0.00</span></div></div>
                    <div class="table-responsive" style="max-height: 120px; overflow-y: auto;">
                        <table class="table table-grid table-gst mb-0">
                            <thead><tr><th style="width: 60px;">CGST(%)</th><th style="width: 60px;">SGST(%)</th><th style="width: 80px;">Total Amt.</th><th style="width: 80px;">CGST Amt.</th><th style="width: 80px;">SGST Amt.</th></tr></thead>
                            <tbody id="gstSummaryBody"></tbody>
                            <tfoot><tr style="background: #d0d0d0;"><td colspan="3" class="text-end"><strong>CGSTAMT</strong></td><td class="text-end"><span id="totalCgstAmt">0.00</span></td><td class="text-end"><strong>SGSTAMT</strong> <span id="totalSgstAmt">0.00</span></td></tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="credit-section mb-2">
                        <div class="credit-title mb-2">Credit</div>
                        <div class="summary-row"><span class="summary-label">Amount</span><span class="summary-value" id="creditAmount">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Total GST</span><span class="summary-value" id="creditTotalGst">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Net Amt.</span><span class="summary-value" id="creditNetAmt">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">R/Off</span><span class="summary-value" id="creditRoff">0.00</span></div>
                        <div class="summary-row" style="border-top: 1px solid #008000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Db. Amt.</strong></span><span class="summary-value" id="totalCreditAmt" style="color: #008000;">0.00</span></div>
                    </div>
                    <div class="debit-section">
                        <div class="debit-title mb-2">Debit</div>
                        <div class="row g-1 mb-1">
                            <div class="col-auto"><span class="field-label">TDS @</span><input type="number" class="form-control text-end" name="tds_percent" id="tdsPercent" value="0" step="0.01" style="width: 50px;" onchange="calculateTds()"></div>
                            <div class="col-auto"><span class="field-label">%</span><input type="text" class="form-control bg-light text-end" id="tdsAmount" readonly style="width: 80px;"></div>
                            <div class="col-auto"><span class="field-label">TDS</span></div>
                        </div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Account</span><select class="form-select" name="debit_account_id" id="debitAccountId" style="width: 100%;" onchange="updateDebitAccount()"><option value="">Select Account</option></select><input type="hidden" name="debit_account_type" id="debitAccountType"><input type="hidden" name="debit_account_name" id="debitAccountName"></div></div>
                        <div class="summary-row" style="border-top: 1px solid #cc0000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Cr</strong></span><span class="summary-value" id="totalDebitAmt" style="color: #cc0000;">0.00</span></div>
                    </div>
                    <div class="mt-2 p-2" style="background: #f0f0f0; border: 1px solid #ccc;">
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label-green">Customer</span><input type="text" class="form-control bg-light" id="customerDisplay" readonly></div></div>
                        <div class="row g-1"><div class="col-12"><span class="field-label-green">Address</span><textarea class="form-control bg-light" id="addressDisplay" rows="2" readonly></textarea></div></div>
                    </div>
                </div>
            </div>
            <div class="footer-section d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()">Delete Voucher</button>
                <button type="button" class="btn btn-secondary btn-action" onclick="printVoucher()">Print</button>
                <button type="button" class="btn btn-warning btn-action" onclick="reverseVoucher()">Reverse</button>
                <button type="button" class="btn btn-success btn-action" onclick="saveVoucher()">Save</button>
                <a href="{{ route('admin.voucher-income.index') }}" class="btn btn-secondary btn-action">Exit</a>
            </div>
        </form>
    </div>
</div>

<datalist id="hsnDatalist">
    @foreach($hsnCodes as $hsn)
        <option value="{{ $hsn['hsn_code'] }}">{{ $hsn['name'] }} ({{ $hsn['total_gst_percent'] ?: ($hsn['cgst_percent'] + $hsn['sgst_percent']) }}%)</option>
    @endforeach
</datalist>

<div class="custom-modal-overlay" id="accountModalOverlay">
    <div class="custom-modal">
        <div class="custom-modal-header"><h6>Select Account</h6><button type="button" class="custom-modal-close" onclick="closeAccountModal()">&times;</button></div>
        <div class="custom-modal-body">
            <div class="row mb-2">
                <div class="col-md-6"><input type="text" class="form-control form-control-sm" id="accountSearch" placeholder="Search..." onkeyup="filterAccountList()"></div>
                <div class="col-md-6"><select class="form-select form-select-sm" id="accountTypeFilter" onchange="loadAccountList()"><option value="SL">Sales Ledger</option><option value="GL">General Ledger</option></select></div>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover" id="accountListTable"><thead><tr><th>Code</th><th>Name</th></tr></thead><tbody id="accountListBody"></tbody></table>
            </div>
        </div>
        <div class="custom-modal-footer"><button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button><button type="button" class="btn btn-primary btn-sm" onclick="selectAccount()">Select</button></div>
    </div>
</div>

<script>
const salesLedgers = @json($salesLedgers);
const generalLedgers = @json($generalLedgers);
const cashBankBooks = @json($cashBankBooks);
const hsnCodes = @json($hsnCodes);
const customers = @json($customers);

let accountRowCount = 0, hsnRowCount = 0, selectedAccountRow = null, selectedHsnRow = null;

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 3; i++) addAccountRow();
    for (let i = 0; i < 5; i++) addHsnRow();
    
    document.getElementById('customerNameInput').addEventListener('change', function() {
        const name = this.value;
        const customer = customers.find(c => c.name === name);
        if (customer) {
            document.getElementById('customerId').value = customer.id;
            document.getElementById('gstNo').value = customer.gst_number || '';
            document.getElementById('panNo').value = customer.pan_number || '';
            document.getElementById('city').value = customer.city || '';
            document.getElementById('customerDisplay').value = customer.name;
            document.getElementById('addressDisplay').value = customer.address || '';
        }
    });
    
    loadDebitAccounts();
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F9') { e.preventDefault(); openAccountModal(); }
        if (e.key === 'Escape') closeAccountModal();
    });
});

function addAccountRow(data = null) {
    accountRowCount++;
    const tbody = document.getElementById('accountsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', accountRowCount);
    row.innerHTML = `<td><input type="text" class="account-code" name="accounts[${accountRowCount}][account_code]" value="${data?.account_code || ''}" onclick="selectAccountRowEl(this.closest('tr'))"></td>
        <td><input type="text" class="account-name" name="accounts[${accountRowCount}][account_name]" value="${data?.account_name || ''}" readonly onclick="selectAccountRowEl(this.closest('tr')); openAccountModal();">
            <input type="hidden" class="account-type" name="accounts[${accountRowCount}][account_type]" value="${data?.account_type || ''}">
            <input type="hidden" class="account-id" name="accounts[${accountRowCount}][account_id]" value="${data?.account_id || ''}"></td>`;
    row.onclick = () => selectAccountRowEl(row);
    tbody.appendChild(row);
}

function selectAccountRowEl(row) {
    document.querySelectorAll('#accountsTableBody tr').forEach(r => r.classList.remove('table-primary'));
    row.classList.add('table-primary');
    selectedAccountRow = row;
}

function addHsnRow(data = null) {
    hsnRowCount++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    row.innerHTML = `<td><input type="text" class="hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${data?.hsn_code || ''}" onchange="fetchHsnDetails(this)" onclick="selectHsnRowEl(this.closest('tr'))" list="hsnDatalist"></td>
        <td><input type="number" class="hsn-amount text-end" name="items[${hsnRowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr')); checkAddHsnRow();" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-gst text-end" name="items[${hsnRowCount}][gst_percent]" value="${data?.gst_percent || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-pct text-end bg-light" name="items[${hsnRowCount}][cgst_percent]" value="${data?.cgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-cgst-amt text-end bg-light" name="items[${hsnRowCount}][cgst_amount]" value="${data?.cgst_amount || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-pct text-end bg-light" name="items[${hsnRowCount}][sgst_percent]" value="${data?.sgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-amt text-end bg-light" name="items[${hsnRowCount}][sgst_amount]" value="${data?.sgst_amount || ''}" readonly></td>`;
    row.onclick = () => selectHsnRowEl(row);
    tbody.appendChild(row);
}

function selectHsnRowEl(row) {
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('table-warning'));
    row.classList.add('table-warning');
    selectedHsnRow = row;
}

function checkAddHsnRow() {
    const rows = document.querySelectorAll('#hsnTableBody tr');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        const amount = lastRow.querySelector('.hsn-amount')?.value;
        if (amount && parseFloat(amount) > 0) addHsnRow();
    }
}

function fetchHsnDetails(input) {
    const hsnCode = input.value.trim();
    if (!hsnCode) return;
    const matchingHsns = hsnCodes.filter(h => h.hsn_code && h.hsn_code.toString() === hsnCode.toString());
    if (matchingHsns.length === 0) return;
    const uniqueRates = [];
    const seenRates = new Set();
    matchingHsns.forEach(hsn => {
        const gst = parseFloat(hsn.total_gst_percent) || 0;
        if (!seenRates.has(gst)) { seenRates.add(gst); uniqueRates.push(hsn); }
    });
    uniqueRates.sort((a, b) => {
        const gstA = parseFloat(a.total_gst_percent) || 0;
        const gstB = parseFloat(b.total_gst_percent) || 0;
        if (gstA === 0 && gstB !== 0) return 1;
        if (gstA !== 0 && gstB === 0) return -1;
        return gstA - gstB;
    });
    const row = input.closest('tr');
    if (uniqueRates.length > 1) showGstRateSelector(row, uniqueRates, hsnCode);
    else applyHsnToRow(row, uniqueRates[0]);
}

function showGstRateSelector(row, hsnOptions, hsnCode) {
    const ratesList = hsnOptions.map((hsn, idx) => `${idx + 1}. ${parseFloat(hsn.total_gst_percent) || 0}% GST`).join('\n');
    const selection = prompt(`HSN ${hsnCode} has multiple GST rates:\n${ratesList}\n\nEnter number (1-${hsnOptions.length}):`, '1');
    if (selection) {
        const idx = parseInt(selection) - 1;
        if (idx >= 0 && idx < hsnOptions.length) applyHsnToRow(row, hsnOptions[idx]);
    }
}

function applyHsnToRow(row, hsn) {
    let cgstPct = parseFloat(hsn.cgst_percent) || 0;
    let sgstPct = parseFloat(hsn.sgst_percent) || 0;
    let totalGst = parseFloat(hsn.total_gst_percent) || 0;
    if (totalGst > 0 && cgstPct === 0 && sgstPct === 0) { cgstPct = totalGst / 2; sgstPct = totalGst / 2; }
    if (totalGst === 0 && (cgstPct > 0 || sgstPct > 0)) totalGst = cgstPct + sgstPct;
    row.querySelector('.hsn-gst').value = totalGst.toFixed(2);
    row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
    row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    if (amount > 0) {
        row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2);
        row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2);
    }
    calculateTotals();
}

function calculateHsnRow(row) {
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPct = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    let cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct').value) || 0;
    let sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct').value) || 0;
    if (cgstPct === 0 && sgstPct === 0 && gstPct > 0) {
        cgstPct = gstPct / 2; sgstPct = gstPct / 2;
        row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
        row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    }
    row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2);
    row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalAmount = 0, totalCgst = 0, totalSgst = 0;
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        totalAmount += parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        totalCgst += parseFloat(row.querySelector('.hsn-cgst-amt')?.value) || 0;
        totalSgst += parseFloat(row.querySelector('.hsn-sgst-amt')?.value) || 0;
    });
    const totalGst = totalCgst + totalSgst;
    const netAmt = totalAmount + totalGst;
    const roundOff = Math.round(netAmt) - netAmt;
    const totalCredit = Math.round(netAmt);
    document.getElementById('grossDisplay').value = totalAmount.toFixed(2);
    document.getElementById('totalGstDisplay').textContent = totalGst.toFixed(2);
    document.getElementById('netAmtDisplay').textContent = netAmt.toFixed(2);
    document.getElementById('creditAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('creditTotalGst').textContent = totalGst.toFixed(2);
    document.getElementById('creditNetAmt').textContent = netAmt.toFixed(2);
    document.getElementById('creditRoff').textContent = roundOff.toFixed(2);
    document.getElementById('totalCreditAmt').textContent = totalCredit.toFixed(2);
    document.getElementById('totalCgstAmt').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgstAmt').textContent = totalSgst.toFixed(2);
    calculateTds();
    updateGstSummary();
}

function calculateTds() {
    const netAmt = parseFloat(document.getElementById('creditNetAmt').textContent) || 0;
    const tdsPct = parseFloat(document.getElementById('tdsPercent').value) || 0;
    const tdsAmt = netAmt * tdsPct / 100;
    const totalCredit = parseFloat(document.getElementById('totalCreditAmt').textContent) || 0;
    document.getElementById('tdsAmount').value = tdsAmt.toFixed(2);
    document.getElementById('totalDebitAmt').textContent = (totalCredit - tdsAmt).toFixed(2);
}

function updateGstSummary() {
    const gstMap = {};
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct')?.value) || 0;
        const sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct')?.value) || 0;
        const amount = parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        const cgstAmt = parseFloat(row.querySelector('.hsn-cgst-amt')?.value) || 0;
        const sgstAmt = parseFloat(row.querySelector('.hsn-sgst-amt')?.value) || 0;
        if (cgstPct > 0 || sgstPct > 0) {
            const key = `${cgstPct}-${sgstPct}`;
            if (!gstMap[key]) gstMap[key] = { cgstPct, sgstPct, totalAmt: 0, cgstAmt: 0, sgstAmt: 0 };
            gstMap[key].totalAmt += amount; gstMap[key].cgstAmt += cgstAmt; gstMap[key].sgstAmt += sgstAmt;
        }
    });
    const tbody = document.getElementById('gstSummaryBody');
    tbody.innerHTML = '';
    Object.values(gstMap).forEach(g => {
        const row = document.createElement('tr');
        row.innerHTML = `<td class="text-end">${g.cgstPct.toFixed(2)}</td><td class="text-end">${g.sgstPct.toFixed(2)}</td><td class="text-end">${g.totalAmt.toFixed(2)}</td><td class="text-end">${g.cgstAmt.toFixed(2)}</td><td class="text-end">${g.sgstAmt.toFixed(2)}</td>`;
        tbody.appendChild(row);
    });
}

function loadDebitAccounts() {
    const select = document.getElementById('debitAccountId');
    select.innerHTML = '<option value="">Select Account</option>';
    cashBankBooks.forEach(acc => {
        const option = document.createElement('option');
        option.value = acc.id;
        option.textContent = acc.name;
        option.dataset.type = 'CB';
        option.dataset.name = acc.name;
        select.appendChild(option);
    });
}

function updateDebitAccount() {
    const select = document.getElementById('debitAccountId');
    const option = select.options[select.selectedIndex];
    document.getElementById('debitAccountType').value = option.dataset.type || '';
    document.getElementById('debitAccountName').value = option.dataset.name || '';
}

function openAccountModal() { loadAccountList(); document.getElementById('accountModalOverlay').classList.add('show'); document.getElementById('accountSearch').focus(); }
function closeAccountModal() { document.getElementById('accountModalOverlay').classList.remove('show'); }

function loadAccountList() {
    const type = document.getElementById('accountTypeFilter').value;
    const accounts = type === 'SL' ? salesLedgers : generalLedgers;
    const tbody = document.getElementById('accountListBody');
    tbody.innerHTML = '';
    accounts.forEach(acc => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.innerHTML = `<td>${acc.alter_code || acc.account_code || ''}</td><td>${acc.ledger_name || acc.account_name}</td>`;
        row.dataset.id = acc.id;
        row.dataset.code = acc.alter_code || acc.account_code || '';
        row.dataset.name = acc.ledger_name || acc.account_name;
        row.dataset.type = type;
        row.onclick = () => { document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary')); row.classList.add('table-primary'); };
        row.ondblclick = () => selectAccount();
        tbody.appendChild(row);
    });
}

function filterAccountList() {
    const search = document.getElementById('accountSearch').value.toLowerCase();
    document.querySelectorAll('#accountListBody tr').forEach(row => { row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none'; });
}

function selectAccount() {
    const selected = document.querySelector('#accountListBody tr.table-primary');
    if (!selected) { alert('Please select an account'); return; }
    let targetRow = selectedAccountRow;
    if (!targetRow || targetRow.querySelector('.account-name').value) {
        const rows = document.querySelectorAll('#accountsTableBody tr');
        for (let row of rows) { if (!row.querySelector('.account-name').value) { targetRow = row; break; } }
        if (!targetRow || targetRow.querySelector('.account-name').value) { addAccountRow(); targetRow = document.querySelector('#accountsTableBody tr:last-child'); }
    }
    targetRow.querySelector('.account-code').value = selected.dataset.code;
    targetRow.querySelector('.account-name').value = selected.dataset.name;
    targetRow.querySelector('.account-type').value = selected.dataset.type;
    targetRow.querySelector('.account-id').value = selected.dataset.id;
    addAccountRow();
    closeAccountModal();
    selectedAccountRow = null;
}

function deleteAccount() { if (selectedAccountRow) { selectedAccountRow.remove(); selectedAccountRow = null; } else alert('Please select an account row'); }
function deleteHsnRow() { if (selectedHsnRow) { selectedHsnRow.remove(); selectedHsnRow = null; calculateTotals(); } else alert('Please select an HSN row'); }

function saveVoucher() {
    const items = [], accounts = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        if (hsnCode || amount) {
            items.push({ hsn_code: hsnCode, amount: amount || 0, gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-pct')?.value || 0, cgst_amount: row.querySelector('.hsn-cgst-amt')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-pct')?.value || 0, sgst_amount: row.querySelector('.hsn-sgst-amt')?.value || 0 });
        }
    });
    document.querySelectorAll('#accountsTableBody tr').forEach(row => {
        const name = row.querySelector('.account-name')?.value;
        if (name) accounts.push({ account_type: row.querySelector('.account-type')?.value, account_id: row.querySelector('.account-id')?.value,
            account_code: row.querySelector('.account-code')?.value, account_name: name });
    });
    const formData = {
        voucher_date: document.getElementById('voucherDate').value, local_inter: document.getElementById('localInter').value,
        description: document.getElementById('description').value, customer_id: document.getElementById('customerId').value,
        customer_name: document.getElementById('customerNameInput').value, gst_no: document.getElementById('gstNo').value,
        pan_no: document.getElementById('panNo').value, city: document.getElementById('city').value, address: document.getElementById('addressDisplay').value,
        amount: parseFloat(document.getElementById('grossDisplay').value) || 0, total_gst: parseFloat(document.getElementById('totalGstDisplay').textContent) || 0,
        net_amount: parseFloat(document.getElementById('netAmtDisplay').textContent) || 0, round_off: parseFloat(document.getElementById('creditRoff').textContent) || 0,
        total_credit: parseFloat(document.getElementById('totalCreditAmt').textContent) || 0, tds_percent: parseFloat(document.getElementById('tdsPercent').value) || 0,
        tds_amount: parseFloat(document.getElementById('tdsAmount').value) || 0, debit_account_id: document.getElementById('debitAccountId').value,
        debit_account_type: document.getElementById('debitAccountType').value, debit_account_name: document.getElementById('debitAccountName').value,
        total_debit: parseFloat(document.getElementById('totalDebitAmt').textContent) || 0,
        total_cgst_amount: parseFloat(document.getElementById('totalCgstAmt').textContent) || 0,
        total_sgst_amount: parseFloat(document.getElementById('totalSgstAmt').textContent) || 0,
        items: items, accounts: accounts, _token: '{{ csrf_token() }}'
    };
    fetch('{{ route("admin.voucher-income.store") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(formData) })
    .then(r => r.json()).then(data => { if (data.success) { alert('Voucher #' + data.voucher_no + ' saved!'); window.location.reload(); } else alert('Error: ' + data.message); })
    .catch(e => { console.error('Error:', e); alert('Failed to save'); });
}

function deleteVoucher() { alert('Delete available in modification mode'); }
function printVoucher() { window.print(); }
function reverseVoucher() { alert('Reverse available in modification mode'); }
</script>
@endsection
