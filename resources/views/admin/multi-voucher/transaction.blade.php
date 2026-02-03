@extends('layouts.admin')

@section('title', 'Multi Voucher Entry')

@section('content')
<style>
    .voucher-form { font-size: 11px; background: #e8e0f0; }
    .voucher-form input, .voucher-form select { font-size: 11px; padding: 2px 5px; height: 24px; }
    .header-section { background: #d8d0e8; border: 1px solid #999; padding: 10px; }
    .field-label { color: #800080; font-weight: 600; font-size: 11px; }
    .table-grid { font-size: 11px; margin-bottom: 0; }
    .table-grid th { background: #000080; color: #fff; font-weight: 600; text-align: center; padding: 5px; border: 1px solid #000060; }
    .table-grid td { padding: 2px; border: 1px solid #999; background: #f8f0ff; }
    .table-grid input { font-size: 11px; padding: 2px 4px; height: 22px; border: 1px solid #ccc; width: 100%; }
    .footer-section { background: #d0d0d0; padding: 10px; border: 1px solid #999; }
    .btn-action { font-size: 11px; padding: 5px 15px; }
    .total-row { background: #d8d0e8; font-weight: bold; }
    .account-link { color: #800080; cursor: pointer; text-decoration: underline; }
    .account-link:hover { color: #400040; }
    
    /* Custom Modal Styles */
    .custom-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; }
    .custom-modal-overlay.show { display: flex; align-items: center; justify-content: center; }
    .custom-modal { background: #fff; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 600px; max-height: 80vh; display: flex; flex-direction: column; }
    .custom-modal-header { background: #000080; color: #fff; padding: 10px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .custom-modal-header h6 { margin: 0; font-size: 14px; }
    .custom-modal-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; line-height: 1; }
    .custom-modal-body { padding: 15px; overflow-y: auto; flex: 1; }
    .custom-modal-footer { padding: 10px 15px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 10px; }
    .account-type-tabs { display: flex; gap: 5px; margin-bottom: 10px; }
    .account-type-tab { padding: 5px 15px; border: 1px solid #ccc; background: #f0f0f0; cursor: pointer; font-size: 11px; }
    .account-type-tab.active { background: #000080; color: #fff; border-color: #000080; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-2">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <div class="header-section mb-2">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Fixed Account :</span>
                        <input type="checkbox" id="fixedDebit"> <label for="fixedDebit">Debit</label>
                        <input type="checkbox" id="fixedCredit" class="ms-2"> <label for="fixedCredit">Credit</label>
                        <input type="checkbox" id="fixedAmount" class="ms-2"> <label for="fixedAmount">Amount</label>
                    </div>
                    <div class="col-auto ms-auto">
                        <span class="account-link" onclick="showAccountSelector('Customer')">Customer : F5</span>
                        <span class="account-link ms-3" onclick="showAccountSelector('Supplier')">Supplier : F6</span>
                        <span class="account-link ms-3" onclick="showAccountSelector('General')">General : F8</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-2">
                <table class="table table-grid mb-0" id="entriesTable">
                    <thead>
                        <tr>
                            <th style="width: 100px;">DATE</th>
                            <th style="width: 25%;">DEBIT</th>
                            <th style="width: 25%;">CREDIT</th>
                            <th style="width: 120px;">AMOUNT</th>
                            <th style="width: 80px;">DrSlcd</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="entriesBody"></tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-end pe-3">Total :</td>
                            <td class="text-end pe-2"><span id="totalAmount" style="color: #0000ff;">0.00</span></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-12">
                    <span class="field-label">Narration :</span>
                    <input type="text" class="form-control" name="narration" id="narration" style="width: 100%;">
                </div>
            </div>

            <div class="footer-section d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary btn-action" onclick="changeMode()">Change Mode</button>
                <button type="button" class="btn btn-primary btn-action" onclick="insertRow()">Insert</button>
                <button type="button" class="btn btn-danger btn-action" onclick="deleteRow()">Delete</button>
                <button type="button" class="btn btn-success btn-action" onclick="saveVoucher()">Save (End)</button>
                <button type="button" class="btn btn-info btn-action" onclick="saveAndExport()">Save & Export (F9)</button>
                <button type="button" class="btn btn-warning btn-action" onclick="importData()">Import (F5)</button>
                <a href="{{ route('admin.multi-voucher.index') }}" class="btn btn-secondary btn-action">Close</a>
            </div>
        </form>
    </div>
</div>

<!-- Custom Account Selector Modal -->
<div class="custom-modal-overlay" id="accountModalOverlay">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h6 id="accountModalTitle">Select Account</h6>
            <button type="button" class="custom-modal-close" onclick="closeAccountModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <div class="account-type-tabs">
                <div class="account-type-tab" data-type="Customer" onclick="switchAccountType('Customer')">Customer (F5)</div>
                <div class="account-type-tab" data-type="Supplier" onclick="switchAccountType('Supplier')">Supplier (F6)</div>
                <div class="account-type-tab active" data-type="General" onclick="switchAccountType('General')">General (F8)</div>
            </div>
            <input type="text" class="form-control form-control-sm mb-2" id="accountSearchInput" placeholder="Search..." onkeyup="filterAccounts()">
            <div style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover" id="accountListTable">
                    <thead><tr><th>Code</th><th>Name</th></tr></thead>
                    <tbody id="accountListBody"></tbody>
                </table>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="selectAccount()">Select</button>
        </div>
    </div>
</div>

<script>
const customers = @json($customers);
const suppliers = @json($suppliers);
const generalLedgers = @json($generalLedgers);
const defaultDate = '{{ date("Y-m-d") }}';

let rowCount = 0;
let selectedRow = null;
let currentAccountType = 'General';
let currentField = 'debit';

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 12; i++) addRow();
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F5') { e.preventDefault(); showAccountSelector('Customer'); }
        if (e.key === 'F6') { e.preventDefault(); showAccountSelector('Supplier'); }
        if (e.key === 'F8') { e.preventDefault(); showAccountSelector('General'); }
        if (e.key === 'F9') { e.preventDefault(); saveAndExport(); }
        if (e.key === 'Escape') { closeAccountModal(); }
    });
});

function addRow(data = null) {
    rowCount++;
    const tbody = document.getElementById('entriesBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', rowCount);
    row.innerHTML = `
        <td><input type="date" class="entry-date" name="entries[${rowCount}][entry_date]" value="${data?.entry_date || defaultDate}" onclick="selectRowEl(this.closest('tr'))"></td>
        <td>
            <input type="text" class="debit-name" name="entries[${rowCount}][debit_account_name]" value="${data?.debit_account_name || ''}" readonly onclick="selectRowEl(this.closest('tr')); openAccountSelector('debit')">
            <input type="hidden" class="debit-type" name="entries[${rowCount}][debit_account_type]" value="${data?.debit_account_type || ''}">
            <input type="hidden" class="debit-id" name="entries[${rowCount}][debit_account_id]" value="${data?.debit_account_id || ''}">
        </td>
        <td>
            <input type="text" class="credit-name" name="entries[${rowCount}][credit_account_name]" value="${data?.credit_account_name || ''}" readonly onclick="selectRowEl(this.closest('tr')); openAccountSelector('credit')">
            <input type="hidden" class="credit-type" name="entries[${rowCount}][credit_account_type]" value="${data?.credit_account_type || ''}">
            <input type="hidden" class="credit-id" name="entries[${rowCount}][credit_account_id]" value="${data?.credit_account_id || ''}">
        </td>
        <td><input type="number" class="entry-amount text-end" name="entries[${rowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateTotal(); checkAddRow();" onclick="selectRowEl(this.closest('tr'))"></td>
        <td><input type="text" class="dr-slcd" name="entries[${rowCount}][dr_slcd]" value="${data?.dr_slcd || ''}" onclick="selectRowEl(this.closest('tr'))"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" style="padding: 0 5px; font-size: 10px;">×</button></td>
    `;
    row.onclick = () => selectRowEl(row);
    tbody.appendChild(row);
}

function selectRowEl(row) {
    document.querySelectorAll('#entriesBody tr').forEach(r => r.classList.remove('table-primary'));
    row.classList.add('table-primary');
    selectedRow = row;
}

function checkAddRow() {
    const rows = document.querySelectorAll('#entriesBody tr');
    const lastRow = rows[rows.length - 1];
    if (lastRow && parseFloat(lastRow.querySelector('.entry-amount')?.value) > 0) addRow();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('#entriesBody tr').forEach(row => {
        total += parseFloat(row.querySelector('.entry-amount')?.value) || 0;
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function openAccountSelector(field) {
    currentField = field;
    showAccountSelector(currentAccountType);
}

function showAccountSelector(type) {
    currentAccountType = type;
    document.getElementById('accountModalTitle').textContent = `Select ${type} Account`;
    document.querySelectorAll('.account-type-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.type === type);
    });
    loadAccountList(type);
    document.getElementById('accountModalOverlay').classList.add('show');
    document.getElementById('accountSearchInput').value = '';
    document.getElementById('accountSearchInput').focus();
}

function closeAccountModal() {
    document.getElementById('accountModalOverlay').classList.remove('show');
}

function switchAccountType(type) {
    currentAccountType = type;
    document.getElementById('accountModalTitle').textContent = `Select ${type} Account`;
    document.querySelectorAll('.account-type-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.type === type);
    });
    loadAccountList(type);
}

function loadAccountList(type) {
    let accounts = [];
    if (type === 'Customer') accounts = customers.map(c => ({ id: c.id, code: c.code || '', name: c.name }));
    else if (type === 'Supplier') accounts = suppliers.map(s => ({ id: s.supplier_id, code: s.code || '', name: s.name }));
    else accounts = generalLedgers.map(g => ({ id: g.id, code: g.account_code || '', name: g.account_name }));
    
    const tbody = document.getElementById('accountListBody');
    tbody.innerHTML = '';
    accounts.forEach(acc => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.innerHTML = `<td>${acc.code}</td><td>${acc.name}</td>`;
        row.dataset.id = acc.id;
        row.dataset.name = acc.name;
        row.dataset.type = currentAccountType;
        row.onclick = () => {
            document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary'));
            row.classList.add('table-primary');
        };
        row.ondblclick = () => selectAccount();
        tbody.appendChild(row);
    });
}

function filterAccounts() {
    const search = document.getElementById('accountSearchInput').value.toLowerCase();
    document.querySelectorAll('#accountListBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
}

function selectAccount() {
    const selected = document.querySelector('#accountListBody tr.table-primary');
    if (!selected) { alert('Please select an account'); return; }
    
    if (!selectedRow) {
        const rows = document.querySelectorAll('#entriesBody tr');
        for (let row of rows) {
            if (!row.querySelector(`.${currentField}-name`).value) { selectedRow = row; break; }
        }
        if (!selectedRow) { addRow(); selectedRow = document.querySelector('#entriesBody tr:last-child'); }
    }
    
    selectedRow.querySelector(`.${currentField}-name`).value = selected.dataset.name;
    selectedRow.querySelector(`.${currentField}-type`).value = selected.dataset.type;
    selectedRow.querySelector(`.${currentField}-id`).value = selected.dataset.id;
    
    closeAccountModal();
}

function insertRow() { addRow(); }
function removeRow(btn) { btn.closest('tr').remove(); calculateTotal(); }
function deleteRow() { if (selectedRow) { selectedRow.remove(); selectedRow = null; calculateTotal(); } else alert('Select a row first'); }
function changeMode() { window.location.href = '{{ route("admin.multi-voucher.modification") }}'; }

function saveVoucher() {
    const entries = [];
    document.querySelectorAll('#entriesBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.entry-amount')?.value) || 0;
        if (amount > 0) {
            entries.push({
                entry_date: row.querySelector('.entry-date')?.value,
                debit_account_type: row.querySelector('.debit-type')?.value,
                debit_account_id: row.querySelector('.debit-id')?.value,
                debit_account_name: row.querySelector('.debit-name')?.value,
                credit_account_type: row.querySelector('.credit-type')?.value,
                credit_account_id: row.querySelector('.credit-id')?.value,
                credit_account_name: row.querySelector('.credit-name')?.value,
                amount: amount,
                dr_slcd: row.querySelector('.dr-slcd')?.value,
            });
        }
    });
    
    if (entries.length === 0) { alert('Add at least one entry'); return; }
    
    const formData = {
        voucher_date: entries[0]?.entry_date || '{{ date("Y-m-d") }}',
        narration: document.getElementById('narration').value,
        total_amount: parseFloat(document.getElementById('totalAmount').textContent) || 0,
        entries: entries,
        _token: '{{ csrf_token() }}'
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.multi-voucher.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { alert('Voucher #' + data.voucher_no + ' saved!'); window.location.reload(); }
        else alert('Error: ' + data.message);
    })
    .catch(e => alert('Failed to save'));
}

function saveAndExport() { saveVoucher(); }
function importData() { alert('Import feature coming soon'); }
</script>
@endsection
