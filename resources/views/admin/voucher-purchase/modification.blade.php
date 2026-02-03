@extends('layouts.admin')

@section('title', 'Voucher Purchase (Input GST) - Modification')

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
    .debit-section { background: #ffe0e0; border: 1px solid #cc0000; padding: 5px; }
    .debit-title { color: #cc0000; font-weight: bold; font-size: 11px; font-style: italic; }
    .credit-section { background: #e0ffe0; border: 1px solid #008000; padding: 5px; }
    .credit-title { color: #008000; font-weight: bold; font-size: 11px; font-style: italic; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 2px 5px; }
    .summary-label { font-size: 10px; }
    .summary-value { font-size: 11px; color: #0000ff; font-weight: bold; text-align: right; min-width: 80px; }
    .btn-action { font-size: 10px; padding: 3px 10px; }
    .footer-section { background: #d0d0d0; padding: 8px; border: 1px solid #999; }
    
    /* Modal z-index fix */
    .modal { z-index: 99999 !important; }
    .modal-dialog { z-index: 99999 !important; }
    .modal-content { z-index: 99999 !important; }
    .modal-backdrop { z-index: 99998 !important; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="voucherId" value="">

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto">
                        <span class="field-label">Search Voucher No:</span>
                        <input type="number" class="form-control" id="searchVoucherNo" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-primary btn-action" onclick="loadVoucher()">Load</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="showVoucherList()">List</button>
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Voucher</span>
                        <input type="date" class="form-control" name="voucher_date" id="voucherDate" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Voucher No :</span>
                        <input type="text" class="form-control bg-light" id="voucherNoDisplay" readonly style="width: 60px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Bill No. :</span>
                        <input type="text" class="form-control" name="bill_no" id="billNo" style="width: 100px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Bill Date :</span>
                        <input type="date" class="form-control" name="bill_date" id="billDate" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">L(ocal) / I(nter</span>
                        <input type="text" class="form-control" name="local_inter" id="localInter" value="L" maxlength="1" style="width: 30px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">RCM (Y/N) :</span>
                        <input type="text" class="form-control" name="rcm" id="rcm" value="N" maxlength="1" style="width: 30px;">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <span class="field-label">Description :</span>
                        <input type="text" class="form-control" name="description" id="description" style="width: 100%;">
                    </div>
                </div>
            </div>

            <!-- Main Content Area (same as transaction) -->
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
                            <span class="field-label-green">Supplier</span>
                            <select class="form-select" name="supplier_id" id="supplierId" style="width: 150px;">
                                <option value="">Select</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-gst="{{ $supplier->gst_no }}" data-pan="{{ $supplier->pan_no }}" data-city="{{ $supplier->city }}" data-pin="{{ $supplier->pin }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="openAccountModal()">Add Account (F9)</button></div>
                        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteAccount()">Delete Account</button></div>
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
                    <div class="debit-section mb-2">
                        <div class="debit-title mb-2">Debit</div>
                        <div class="summary-row"><span class="summary-label">Amount</span><span class="summary-value" id="debitAmount">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Total GST</span><span class="summary-value" id="debitTotalGst">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Net Amt.</span><span class="summary-value" id="debitNetAmt">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">R/Off</span><span class="summary-value" id="debitRoff">0.00</span></div>
                        <div class="summary-row" style="border-top: 1px solid #cc0000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Db. Amt.</strong></span><span class="summary-value" id="totalDebitAmt" style="color: #cc0000;">0.00</span></div>
                    </div>
                    <div class="credit-section">
                        <div class="credit-title mb-2">Credit</div>
                        <div class="row g-1 mb-1">
                            <div class="col-auto"><span class="field-label">TDS @</span><input type="number" class="form-control text-end" name="tds_percent" id="tdsPercent" value="0" step="0.01" style="width: 50px;" onchange="calculateTds()"></div>
                            <div class="col-auto"><span class="field-label">%</span><input type="text" class="form-control bg-light text-end" id="tdsAmount" readonly style="width: 80px;"></div>
                        </div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">1. Cash & Bank / 2. General</span><input type="text" class="form-control" name="payment_type" id="paymentType" value="1" maxlength="1" style="width: 30px;"></div></div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Account</span><select class="form-select" name="credit_account_id" id="creditAccountId" style="width: 100%;" onchange="updateCreditAccount()"><option value="">Select Account</option></select><input type="hidden" name="credit_account_type" id="creditAccountType"><input type="hidden" name="credit_account_name" id="creditAccountName"></div></div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Cheque No.</span><input type="text" class="form-control" name="cheque_no" id="chequeNo" style="width: 100%;"></div></div>
                        <div class="summary-row" style="border-top: 1px solid #008000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Cr</strong></span><span class="summary-value" id="totalCreditAmt" style="color: #008000;">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="footer-section d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()" id="btnDelete" disabled>Delete Voucher</button>
                <button type="button" class="btn btn-secondary btn-action" onclick="printVoucher()">Print</button>
                <button type="button" class="btn btn-warning btn-action" onclick="reverseVoucher()" id="btnReverse" disabled>Reverse</button>
                <button type="button" class="btn btn-success btn-action" onclick="updateVoucher()" id="btnUpdate" disabled>Update</button>
                <a href="{{ route('admin.voucher-purchase.index') }}" class="btn btn-secondary btn-action">Exit</a>
            </div>
        </form>
    </div>
</div>

<script>
const purchaseLedgers = @json($purchaseLedgers);
const generalLedgers = @json($generalLedgers);
const cashBankBooks = @json($cashBankBooks);
const hsnCodes = @json($hsnCodes);
let currentVoucherId = null;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('supplierId').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        document.getElementById('gstNo').value = option.dataset.gst || '';
        document.getElementById('panNo').value = option.dataset.pan || '';
        document.getElementById('city').value = option.dataset.city || '';
        document.getElementById('pin').value = option.dataset.pin || '';
    });
    document.getElementById('paymentType').addEventListener('change', loadCreditAccounts);
    loadCreditAccounts();
});

function loadVoucher() {
    const voucherNo = document.getElementById('searchVoucherNo').value;
    if (!voucherNo) { alert('Please enter voucher number'); return; }
    
    fetch(`{{ url('admin/voucher-purchase/get-by-voucher-no') }}/${voucherNo}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                populateForm(data.voucher);
                document.getElementById('btnDelete').disabled = false;
                document.getElementById('btnReverse').disabled = false;
                document.getElementById('btnUpdate').disabled = false;
            } else alert('Voucher not found');
        })
        .catch(e => alert('Error loading voucher'));
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('voucherNoDisplay').value = voucher.voucher_no;
    document.getElementById('voucherDate').value = voucher.voucher_date?.split('T')[0] || '';
    document.getElementById('billNo').value = voucher.bill_no || '';
    document.getElementById('billDate').value = voucher.bill_date?.split('T')[0] || '';
    document.getElementById('localInter').value = voucher.local_inter || 'L';
    document.getElementById('rcm').value = voucher.rcm || 'N';
    document.getElementById('description').value = voucher.description || '';
    document.getElementById('supplierId').value = voucher.supplier_id || '';
    document.getElementById('gstNo').value = voucher.gst_no || '';
    document.getElementById('panNo').value = voucher.pan_no || '';
    document.getElementById('city').value = voucher.city || '';
    document.getElementById('pin').value = voucher.pin || '';
    document.getElementById('tdsPercent').value = voucher.tds_percent || 0;
    document.getElementById('paymentType').value = voucher.payment_type || '1';
    loadCreditAccounts();
    document.getElementById('creditAccountId').value = voucher.credit_account_id || '';
    document.getElementById('chequeNo').value = voucher.cheque_no || '';
    
    // Clear and populate accounts
    document.getElementById('accountsTableBody').innerHTML = '';
    if (voucher.accounts) voucher.accounts.forEach(acc => addAccountRow(acc));
    if (!voucher.accounts || voucher.accounts.length < 3) for (let i = (voucher.accounts?.length || 0); i < 3; i++) addAccountRow();
    
    // Clear and populate HSN items
    document.getElementById('hsnTableBody').innerHTML = '';
    if (voucher.items) voucher.items.forEach(item => addHsnRow(item));
    if (!voucher.items || voucher.items.length < 5) for (let i = (voucher.items?.length || 0); i < 5; i++) addHsnRow();
    
    calculateTotals();
}

let accountRowCount = 0;
let hsnRowCount = 0;
let selectedAccountRow = null;
let selectedHsnRow = null;

function addAccountRow(data = null) {
    accountRowCount++;
    const tbody = document.getElementById('accountsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', accountRowCount);
    row.innerHTML = `
        <td><input type="text" class="account-code" name="accounts[${accountRowCount}][account_code]" value="${data?.account_code || ''}" onclick="selectAccountRowEl(this.closest('tr'))"></td>
        <td>
            <input type="text" class="account-name" name="accounts[${accountRowCount}][account_name]" value="${data?.account_name || ''}" readonly onclick="selectAccountRowEl(this.closest('tr')); openAccountModal();">
            <input type="hidden" class="account-type" name="accounts[${accountRowCount}][account_type]" value="${data?.account_type || ''}">
            <input type="hidden" class="account-id" name="accounts[${accountRowCount}][account_id]" value="${data?.account_id || ''}">
        </td>
    `;
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
    row.innerHTML = `
        <td><input type="text" class="hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${data?.hsn_code || ''}" onchange="fetchHsnDetails(this)" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-amount text-end" name="items[${hsnRowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-gst text-end" name="items[${hsnRowCount}][gst_percent]" value="${data?.gst_percent || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-pct text-end bg-light" name="items[${hsnRowCount}][cgst_percent]" value="${data?.cgst_percent || ''}" readonly onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-amt text-end bg-light" name="items[${hsnRowCount}][cgst_amount]" value="${data?.cgst_amount || ''}" readonly onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-sgst-pct text-end bg-light" name="items[${hsnRowCount}][sgst_percent]" value="${data?.sgst_percent || ''}" readonly onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-sgst-amt text-end bg-light" name="items[${hsnRowCount}][sgst_amount]" value="${data?.sgst_amount || ''}" readonly onclick="selectHsnRowEl(this.closest('tr'))"></td>
    `;
    row.onclick = () => selectHsnRowEl(row);
    tbody.appendChild(row);
}

function selectHsnRowEl(row) {
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('table-warning'));
    row.classList.add('table-warning');
    selectedHsnRow = row;
}

function fetchHsnDetails(input) {
    const hsnCode = input.value.trim();
    if (!hsnCode) return;
    
    // Find ALL matching HSN codes (same code can have different GST rates)
    const matchingHsns = hsnCodes.filter(h => h.hsn_code && h.hsn_code.toString() === hsnCode.toString());
    
    if (matchingHsns.length === 0) return;
    
    // Get unique GST rates
    const uniqueRates = [];
    const seenRates = new Set();
    matchingHsns.forEach(hsn => {
        const gst = parseFloat(hsn.total_gst_percent) || 0;
        if (!seenRates.has(gst)) {
            seenRates.add(gst);
            uniqueRates.push(hsn);
        }
    });
    
    // Sort by GST rate (non-zero first)
    uniqueRates.sort((a, b) => {
        const gstA = parseFloat(a.total_gst_percent) || 0;
        const gstB = parseFloat(b.total_gst_percent) || 0;
        if (gstA === 0 && gstB !== 0) return 1;
        if (gstA !== 0 && gstB === 0) return -1;
        return gstA - gstB;
    });
    
    const row = input.closest('tr');
    
    if (uniqueRates.length > 1) {
        showGstRateSelector(row, uniqueRates, hsnCode);
    } else {
        applyHsnToRow(row, uniqueRates[0]);
    }
}

function showGstRateSelector(row, hsnOptions, hsnCode) {
    const ratesList = hsnOptions.map((hsn, idx) => {
        const gst = parseFloat(hsn.total_gst_percent) || 0;
        return `${idx + 1}. ${gst}% GST`;
    }).join('\n');
    
    const selection = prompt(`HSN ${hsnCode} has multiple GST rates:\n${ratesList}\n\nEnter number (1-${hsnOptions.length}):`, '1');
    
    if (selection) {
        const idx = parseInt(selection) - 1;
        if (idx >= 0 && idx < hsnOptions.length) {
            applyHsnToRow(row, hsnOptions[idx]);
        }
    }
}

function applyHsnToRow(row, hsn) {
    let cgstPct = parseFloat(hsn.cgst_percent) || 0;
    let sgstPct = parseFloat(hsn.sgst_percent) || 0;
    let totalGst = parseFloat(hsn.total_gst_percent) || 0;
    
    if (totalGst > 0 && cgstPct === 0 && sgstPct === 0) {
        cgstPct = totalGst / 2;
        sgstPct = totalGst / 2;
    }
    
    if (totalGst === 0 && (cgstPct > 0 || sgstPct > 0)) {
        totalGst = cgstPct + sgstPct;
    }
    
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
    const localInter = document.getElementById('localInter').value.toUpperCase();
    
    // Check if CGST/SGST percentages are already set (from HSN lookup)
    let cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct').value) || 0;
    let sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct').value) || 0;
    
    // Only recalculate percentages if they're not already set or if GST% changed manually
    if (cgstPct === 0 && sgstPct === 0 && gstPct > 0) {
        if (localInter === 'L') {
            cgstPct = gstPct / 2;
            sgstPct = gstPct / 2;
        } else {
            cgstPct = gstPct / 2;
            sgstPct = gstPct / 2;
        }
        row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
        row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    }
    
    // Calculate amounts
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
    const totalDebit = Math.round(netAmt);
    
    document.getElementById('grossDisplay').value = totalAmount.toFixed(2);
    document.getElementById('totalGstDisplay').textContent = totalGst.toFixed(2);
    document.getElementById('netAmtDisplay').textContent = netAmt.toFixed(2);
    
    document.getElementById('debitAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('debitTotalGst').textContent = totalGst.toFixed(2);
    document.getElementById('debitNetAmt').textContent = netAmt.toFixed(2);
    document.getElementById('debitRoff').textContent = roundOff.toFixed(2);
    document.getElementById('totalDebitAmt').textContent = totalDebit.toFixed(2);
    
    document.getElementById('totalCgstAmt').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgstAmt').textContent = totalSgst.toFixed(2);
    
    calculateTds();
    updateGstSummary();
}

function calculateTds() {
    const netAmt = parseFloat(document.getElementById('debitNetAmt').textContent) || 0;
    const tdsPct = parseFloat(document.getElementById('tdsPercent').value) || 0;
    const tdsAmt = netAmt * tdsPct / 100;
    const totalDebit = parseFloat(document.getElementById('totalDebitAmt').textContent) || 0;
    const totalCredit = totalDebit - tdsAmt;
    
    document.getElementById('tdsAmount').value = tdsAmt.toFixed(2);
    document.getElementById('totalCreditAmt').textContent = totalCredit.toFixed(2);
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
            gstMap[key].totalAmt += amount;
            gstMap[key].cgstAmt += cgstAmt;
            gstMap[key].sgstAmt += sgstAmt;
        }
    });
    
    const tbody = document.getElementById('gstSummaryBody');
    tbody.innerHTML = '';
    Object.values(gstMap).forEach(g => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-end">${g.cgstPct.toFixed(2)}</td>
            <td class="text-end">${g.sgstPct.toFixed(2)}</td>
            <td class="text-end">${g.totalAmt.toFixed(2)}</td>
            <td class="text-end">${g.cgstAmt.toFixed(2)}</td>
            <td class="text-end">${g.sgstAmt.toFixed(2)}</td>
        `;
        tbody.appendChild(row);
    });
}

function loadCreditAccounts() {
    const type = document.getElementById('paymentType').value;
    const select = document.getElementById('creditAccountId');
    select.innerHTML = '<option value="">Select Account</option>';
    
    const accounts = type === '1' ? cashBankBooks : generalLedgers;
    const accountType = type === '1' ? 'CB' : 'GL';
    
    accounts.forEach(acc => {
        const option = document.createElement('option');
        option.value = acc.id;
        option.textContent = acc.name || acc.account_name;
        option.dataset.type = accountType;
        option.dataset.name = acc.name || acc.account_name;
        select.appendChild(option);
    });
}

function updateCreditAccount() {
    const select = document.getElementById('creditAccountId');
    const option = select.options[select.selectedIndex];
    document.getElementById('creditAccountType').value = option.dataset.type || '';
    document.getElementById('creditAccountName').value = option.dataset.name || '';
}

function openAccountModal() {
    // Simple prompt for now - can be enhanced with modal
    const type = prompt('Enter account type (PL for Purchase Ledger, GL for General Ledger):', 'PL');
    if (!type) return;
    
    const accounts = type.toUpperCase() === 'PL' ? purchaseLedgers : generalLedgers;
    let list = accounts.map((acc, i) => `${i+1}. ${acc.ledger_name || acc.account_name}`).join('\n');
    const idx = prompt(`Select account:\n${list}`, '1');
    if (!idx) return;
    
    const acc = accounts[parseInt(idx) - 1];
    if (!acc) { alert('Invalid selection'); return; }
    
    let targetRow = selectedAccountRow;
    if (!targetRow) {
        targetRow = document.querySelector('#accountsTableBody tr');
        if (!targetRow) { addAccountRow(); targetRow = document.querySelector('#accountsTableBody tr:last-child'); }
    }
    
    targetRow.querySelector('.account-code').value = acc.alter_code || acc.account_code || '';
    targetRow.querySelector('.account-name').value = acc.ledger_name || acc.account_name;
    targetRow.querySelector('.account-type').value = type.toUpperCase();
    targetRow.querySelector('.account-id').value = acc.id;
}

function deleteAccount() {
    if (selectedAccountRow) { selectedAccountRow.remove(); selectedAccountRow = null; }
    else alert('Please select an account row to delete');
}

function deleteHsnRow() {
    if (selectedHsnRow) { selectedHsnRow.remove(); selectedHsnRow = null; calculateTotals(); }
    else alert('Please select an HSN row to delete');
}

function showVoucherList() {
    window.location.href = '{{ route("admin.voucher-purchase.index") }}';
}

function updateVoucher() {
    if (!currentVoucherId) { alert('Please load a voucher first'); return; }
    
    const items = [], accounts = [];
    
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        if (hsnCode || amount) {
            items.push({
                hsn_code: hsnCode,
                amount: amount || 0,
                gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-pct')?.value || 0,
                cgst_amount: row.querySelector('.hsn-cgst-amt')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-pct')?.value || 0,
                sgst_amount: row.querySelector('.hsn-sgst-amt')?.value || 0,
            });
        }
    });
    
    document.querySelectorAll('#accountsTableBody tr').forEach(row => {
        const name = row.querySelector('.account-name')?.value;
        if (name) {
            accounts.push({
                account_type: row.querySelector('.account-type')?.value,
                account_id: row.querySelector('.account-id')?.value,
                account_code: row.querySelector('.account-code')?.value,
                account_name: name,
            });
        }
    });
    
    const supplierSelect = document.getElementById('supplierId');
    const supplierOption = supplierSelect.options[supplierSelect.selectedIndex];
    
    const formData = {
        voucher_date: document.getElementById('voucherDate').value,
        bill_no: document.getElementById('billNo').value,
        bill_date: document.getElementById('billDate').value,
        local_inter: document.getElementById('localInter').value,
        rcm: document.getElementById('rcm').value,
        description: document.getElementById('description').value,
        supplier_id: supplierSelect.value,
        supplier_name: supplierOption?.text || '',
        gst_no: document.getElementById('gstNo').value,
        pan_no: document.getElementById('panNo').value,
        city: document.getElementById('city').value,
        pin: document.getElementById('pin').value,
        amount: parseFloat(document.getElementById('grossDisplay').value) || 0,
        total_gst: parseFloat(document.getElementById('totalGstDisplay').textContent) || 0,
        net_amount: parseFloat(document.getElementById('netAmtDisplay').textContent) || 0,
        round_off: parseFloat(document.getElementById('debitRoff').textContent) || 0,
        total_debit: parseFloat(document.getElementById('totalDebitAmt').textContent) || 0,
        tds_percent: parseFloat(document.getElementById('tdsPercent').value) || 0,
        tds_amount: parseFloat(document.getElementById('tdsAmount').value) || 0,
        payment_type: document.getElementById('paymentType').value,
        credit_account_id: document.getElementById('creditAccountId').value,
        credit_account_type: document.getElementById('creditAccountType').value,
        credit_account_name: document.getElementById('creditAccountName').value,
        cheque_no: document.getElementById('chequeNo').value,
        total_credit: parseFloat(document.getElementById('totalCreditAmt').textContent) || 0,
        total_cgst_amount: parseFloat(document.getElementById('totalCgstAmt').textContent) || 0,
        total_sgst_amount: parseFloat(document.getElementById('totalSgstAmt').textContent) || 0,
        items: items,
        accounts: accounts,
        _token: '{{ csrf_token() }}'
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/voucher-purchase') }}/${currentVoucherId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Voucher updated successfully!');
        } else alert('Error: ' + data.message);
    })
    .catch(e => { console.error('Error:', e); alert('Failed to update voucher'); });
}

function deleteVoucher() {
    if (!currentVoucherId) { alert('Please load a voucher first'); return; }
    
    if (confirm('Are you sure you want to delete this voucher?')) {
        fetch(`{{ url('admin/voucher-purchase') }}/${currentVoucherId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = '{{ route("admin.voucher-purchase.index") }}';
            } else alert('Error: ' + data.message);
        })
        .catch(e => alert('Failed to delete voucher'));
    }
}

function reverseVoucher() {
    if (!currentVoucherId) { alert('Please load a voucher first'); return; }
    
    if (confirm('Are you sure you want to reverse this voucher?')) {
        fetch(`{{ url('admin/voucher-purchase') }}/${currentVoucherId}/reverse`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadVoucher();
            } else alert('Error: ' + data.message);
        })
        .catch(e => alert('Failed to reverse voucher'));
    }
}

function printVoucher() {
    window.print();
}
</script>
@endsection
