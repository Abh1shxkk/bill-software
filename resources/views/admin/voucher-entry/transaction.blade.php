@extends('layouts.admin')

@section('title', 'Voucher Entry')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #d8d8ff; border: 1px solid #9999cc; padding: 10px; margin-bottom: 0; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #800080; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .table-compact { font-size: 11px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px 8px; vertical-align: middle; }
    .table-compact th { background: #9999cc; font-weight: 600; text-align: center; border: 1px solid #7777aa; color: #000; }
    .table-compact td { border: 1px solid #ccc; background: #fff; }
    .table-compact input { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #ced4da; width: 100%; border-radius: 0 !important; }
    
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .narration-section { background: #f0f0f0; padding: 8px; border: 1px solid #ccc; }
    .narration-label { color: #008000; font-weight: bold; font-size: 12px; }
    .total-display { color: #0000ff; font-weight: bold; font-size: 12px; text-align: right; }
    
    .row-complete { background-color: #e8f5e9 !important; }
    .row-complete td { background-color: #e8f5e9 !important; }
    .row-complete input { background-color: #e8f5e9 !important; }
    
    /* Row selection */
    .row-selected td { border-top: 2px solid #007bff !important; border-bottom: 2px solid #007bff !important; }
    .row-selected td:first-child { border-left: 2px solid #007bff !important; }
    .row-selected td:last-child { border-right: 2px solid #007bff !important; }
    #itemsTableBody tr { cursor: pointer; }
    #itemsTableBody tr:hover { background-color: #f0f7ff; }
    
    .voucher-type-btn { padding: 4px 12px; font-size: 11px; border-radius: 0; }
    .voucher-type-btn.active { background: #800080; color: white; border-color: #800080; }
    
    .footer-section { background: #e0e0e0; padding: 8px 15px; border: 1px solid #ccc; border-top: none; }
    .footer-btn { font-size: 11px; padding: 4px 15px; }
    
    /* Account Selection Modal */
    .account-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7); width: 90%; max-width: 800px; z-index: 9999; opacity: 0; transition: all 0.3s ease-in-out; }
    .account-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .account-modal-content { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .account-modal-header { padding: 1rem 1.5rem; background: #9999cc; color: #000; display: flex; justify-content: space-between; align-items: center; }
    .account-modal-title { margin: 0; font-size: 1.2rem; font-weight: 600; }
    .account-modal-body { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .account-modal-footer { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9998; }
    .modal-backdrop.show { display: block; }
    .btn-close-modal { background: transparent; border: none; color: #000; font-size: 1.5rem; cursor: pointer; }
    .account-list-item { padding: 8px 12px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; justify-content: space-between; }
    .account-list-item:hover { background: #e8e8ff; }
    .account-list-item.selected { background: #9999cc; color: white; }
    .account-type-badge { font-size: 9px; padding: 2px 6px; border-radius: 3px; }
    .account-type-GL { background: #e3f2fd; color: #1565c0; }
    .account-type-CB { background: #e8f5e9; color: #2e7d32; }
    .account-type-CL { background: #fff3e0; color: #ef6c00; }
    .account-type-SU { background: #fce4ec; color: #c2185b; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <!-- Left: Multi Narration & Voucher Type -->
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="field-group">
                                <label>Multi Narration :</label>
                                <select class="form-control" name="multi_narration" id="multiNarration" style="width: 50px;">
                                    <option value="0">N</option>
                                    <option value="1">Y</option>
                                </select>
                            </div>
                            <select class="form-control voucher-type-select" name="voucher_type" id="voucherType" style="width: 140px; background: #800080; color: white; font-weight: bold;">
                                <option value="receipt" {{ $voucherType == 'receipt' ? 'selected' : '' }}>Receipt Voucher</option>
                                <option value="payment" {{ $voucherType == 'payment' ? 'selected' : '' }}>Payment Voucher</option>
                                <option value="contra" {{ $voucherType == 'contra' ? 'selected' : '' }}>Contra Voucher</option>
                                <option value="journal" {{ $voucherType == 'journal' ? 'selected' : '' }}>Journal Voucher</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Center: Date & Day -->
                    <div class="col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <label class="field-group" style="color: #008000;">Voucher Date :</label>
                            <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                            <span id="dayName" style="color: #0000ff; font-weight: bold;">{{ date('l') }}</span>
                        </div>
                    </div>
                    
                    <!-- Right: Voucher No -->
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <label style="color: #800080; font-weight: bold;">Voucher No :</label>
                            <span id="voucherNoDisplay" style="font-weight: bold; font-size: 14px;">{{ $nextVoucherNo }}</span>
                            <input type="hidden" name="voucher_no" id="voucherNo" value="{{ $nextVoucherNo }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border" style="border-top: none !important;">
                <div class="table-responsive" style="overflow-y: auto; max-height: 350px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact mb-0">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 50%;">NAME</th>
                                <th style="width: 20%;">DEBIT</th>
                                <th style="width: 20%;">CREDIT</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Narration Section -->
            <div class="narration-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2">
                            <label class="narration-label">Narration :</label>
                            <input type="text" class="form-control" name="narration" id="narration" style="flex: 1; height: 28px; font-size: 11px;">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="total-display">
                            <span id="totalDebitDisplay">0.00</span>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span id="totalCreditDisplay">0.00</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="footer-section d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-outline-primary footer-btn" onclick="showStandardNarrations()">
                        Standard Narrations [ F1 ]
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger footer-btn" onclick="deleteEntry()">Delete Entry</button>
                    <button type="button" class="btn btn-primary footer-btn" onclick="openAccountModal()">New Account (F9)</button>
                    <button type="button" class="btn btn-warning footer-btn" onclick="cancelVoucher()">Cancel Voucher</button>
                    <button type="button" class="btn btn-success footer-btn" onclick="saveVoucher()">Save (End)</button>
                    <a href="{{ route('admin.voucher-entry.index') }}" class="btn btn-secondary footer-btn">Exit (Esc)</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Account Selection Modal -->
<div class="modal-backdrop" id="accountModalBackdrop" onclick="closeAccountModal()"></div>
<div class="account-modal" id="accountModal">
    <div class="account-modal-content">
        <div class="account-modal-header">
            <h5 class="account-modal-title"><i class="bi bi-journal-text me-2"></i>Select Account</h5>
            <button type="button" class="btn-close-modal" onclick="closeAccountModal()">&times;</button>
        </div>
        <div class="account-modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" id="accountSearch" placeholder="Search by code or name..." onkeyup="filterAccounts()">
                </div>
                <div class="col-md-6">
                    <select class="form-control form-control-sm" id="accountTypeFilter" onchange="filterAccounts()">
                        <option value="">All Types</option>
                        <option value="GL">General Ledger</option>
                        <option value="CB">Cash/Bank</option>
                        <option value="CL">Customer</option>
                        <option value="SU">Supplier</option>
                    </select>
                </div>
            </div>
            <div id="accountList" style="max-height: 300px; overflow-y: auto;">
            </div>
        </div>
        <div class="account-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmAccountSelection()">Select</button>
        </div>
    </div>
</div>

<script>
let itemRowCount = 0;
let selectedAccount = null;
let currentEditRow = null;
let allAccounts = [];

// Combined accounts data
const generalLedgers = @json($generalLedgers);
const cashBankBooks = @json($cashBankBooks);
const customers = @json($customers);
const suppliers = @json($suppliers);

document.addEventListener('DOMContentLoaded', function() {
    // Build combined accounts list
    buildAccountsList();
    
    // Add initial empty rows
    for (let i = 0; i < 10; i++) {
        addItemRow();
    }
    
    // Update day name on date change
    document.getElementById('voucherDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').textContent = days[date.getDay()];
    });
    
    // Voucher type change - update voucher number
    document.getElementById('voucherType').addEventListener('change', function() {
        fetchNextVoucherNo(this.value);
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F1') {
            e.preventDefault();
            showStandardNarrations();
        }
        if (e.key === 'F9') {
            e.preventDefault();
            openAccountModal();
        }
        if (e.key === 'Escape') {
            closeAccountModal();
        }
        if (e.key === 'End') {
            e.preventDefault();
            saveVoucher();
        }
    });
});

function buildAccountsList() {
    allAccounts = [];
    
    // General Ledgers
    generalLedgers.forEach(gl => {
        allAccounts.push({
            type: 'GL',
            id: gl.id,
            code: gl.account_code || gl.alter_code || '',
            name: gl.account_name,
            label: 'General Ledger'
        });
    });
    
    // Cash/Bank Books
    cashBankBooks.forEach(cb => {
        allAccounts.push({
            type: 'CB',
            id: cb.id,
            code: cb.alter_code || '',
            name: cb.name,
            label: 'Cash/Bank'
        });
    });
    
    // Customers
    customers.forEach(cl => {
        allAccounts.push({
            type: 'CL',
            id: cl.id,
            code: cl.code || '',
            name: cl.name,
            label: 'Customer'
        });
    });
    
    // Suppliers
    suppliers.forEach(su => {
        allAccounts.push({
            type: 'SU',
            id: su.id,
            code: su.code || '',
            name: su.name,
            label: 'Supplier'
        });
    });
    
    renderAccountList();
}

function renderAccountList() {
    const container = document.getElementById('accountList');
    container.innerHTML = allAccounts.map(acc => `
        <div class="account-list-item" data-type="${acc.type}" data-id="${acc.id}" data-code="${acc.code}" data-name="${acc.name}" onclick="selectAccountItem(this)">
            <div>
                <span class="account-type-badge account-type-${acc.type}">${acc.label}</span>
                <strong class="ms-2">${acc.code || '-'}</strong> - ${acc.name}
            </div>
        </div>
    `).join('');
}

function filterAccounts() {
    const search = document.getElementById('accountSearch').value.toLowerCase();
    const typeFilter = document.getElementById('accountTypeFilter').value;
    
    document.querySelectorAll('#accountList .account-list-item').forEach(item => {
        const code = item.dataset.code.toLowerCase();
        const name = item.dataset.name.toLowerCase();
        const type = item.dataset.type;
        
        const matchesSearch = code.includes(search) || name.includes(search);
        const matchesType = !typeFilter || type === typeFilter;
        
        item.style.display = (matchesSearch && matchesType) ? '' : 'none';
    });
}

function selectAccountItem(el) {
    document.querySelectorAll('#accountList .account-list-item').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    selectedAccount = {
        type: el.dataset.type,
        id: el.dataset.id,
        code: el.dataset.code,
        name: el.dataset.name
    };
}

function openAccountModal(row = null) {
    currentEditRow = row;
    selectedAccount = null;
    document.getElementById('accountSearch').value = '';
    document.getElementById('accountTypeFilter').value = '';
    filterAccounts();
    document.querySelectorAll('#accountList .account-list-item').forEach(item => item.classList.remove('selected'));
    document.getElementById('accountModalBackdrop').classList.add('show');
    document.getElementById('accountModal').classList.add('show');
    document.getElementById('accountSearch').focus();
}

function closeAccountModal() {
    document.getElementById('accountModalBackdrop').classList.remove('show');
    document.getElementById('accountModal').classList.remove('show');
}

function confirmAccountSelection() {
    if (!selectedAccount) {
        alert('Please select an account');
        return;
    }
    
    if (currentEditRow) {
        // Update existing row
        currentEditRow.querySelector('.account-type').value = selectedAccount.type;
        currentEditRow.querySelector('.account-id').value = selectedAccount.id;
        currentEditRow.querySelector('.account-code').value = selectedAccount.code;
        currentEditRow.querySelector('.account-name').value = selectedAccount.name;
        updateRowStatus(currentEditRow);
    } else {
        // Find first empty row or add new
        let targetRow = null;
        document.querySelectorAll('#itemsTableBody tr').forEach(row => {
            if (!targetRow && !row.querySelector('.account-name').value) {
                targetRow = row;
            }
        });
        
        if (!targetRow) {
            addItemRow();
            targetRow = document.querySelector('#itemsTableBody tr:last-child');
        }
        
        targetRow.querySelector('.account-type').value = selectedAccount.type;
        targetRow.querySelector('.account-id').value = selectedAccount.id;
        targetRow.querySelector('.account-code').value = selectedAccount.code;
        targetRow.querySelector('.account-name').value = selectedAccount.name;
        updateRowStatus(targetRow);
        
        // Focus on debit field
        targetRow.querySelector('.debit-amount').focus();
    }
    
    closeAccountModal();
}

function addItemRow(data = null) {
    itemRowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', itemRowCount);
    row.innerHTML = `
        <td>
            <div class="d-flex align-items-center gap-1">
                <input type="text" class="form-control account-name" name="items[${itemRowCount}][account_name]" 
                       value="${data?.account_name || ''}" 
                       onfocus="openAccountModal(this.closest('tr'))" readonly style="cursor: pointer; flex: 1;">
                <input type="hidden" class="account-type" name="items[${itemRowCount}][account_type]" value="${data?.account_type || ''}">
                <input type="hidden" class="account-id" name="items[${itemRowCount}][account_id]" value="${data?.account_id || ''}">
                <input type="hidden" class="account-code" name="items[${itemRowCount}][account_code]" value="${data?.account_code || ''}">
            </div>
        </td>
        <td>
            <input type="number" class="form-control text-end debit-amount" name="items[${itemRowCount}][debit_amount]" 
                   step="0.01" value="${data?.debit_amount || ''}" 
                   onchange="calculateTotals(); updateRowStatus(this.closest('tr')); clearOtherAmount(this, 'credit')">
        </td>
        <td>
            <input type="number" class="form-control text-end credit-amount" name="items[${itemRowCount}][credit_amount]" 
                   step="0.01" value="${data?.credit_amount || ''}" 
                   onchange="calculateTotals(); updateRowStatus(this.closest('tr')); clearOtherAmount(this, 'debit')">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    
    if (data) {
        updateRowStatus(row);
    }
}

function clearOtherAmount(input, otherType) {
    const row = input.closest('tr');
    const value = parseFloat(input.value) || 0;
    
    if (value > 0) {
        if (otherType === 'credit') {
            row.querySelector('.credit-amount').value = '';
        } else {
            row.querySelector('.debit-amount').value = '';
        }
    }
}

function removeRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    calculateTotals();
    
    // Ensure at least 5 empty rows
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length < 5) {
        addItemRow();
    }
}

function updateRowStatus(row) {
    const name = row.querySelector('.account-name').value;
    const debit = parseFloat(row.querySelector('.debit-amount').value) || 0;
    const credit = parseFloat(row.querySelector('.credit-amount').value) || 0;
    
    if (name && (debit > 0 || credit > 0)) {
        row.classList.add('row-complete');
    } else {
        row.classList.remove('row-complete');
    }
}

function calculateTotals() {
    let totalDebit = 0, totalCredit = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        totalDebit += parseFloat(row.querySelector('.debit-amount')?.value) || 0;
        totalCredit += parseFloat(row.querySelector('.credit-amount')?.value) || 0;
    });
    
    document.getElementById('totalDebitDisplay').textContent = totalDebit.toFixed(2);
    document.getElementById('totalCreditDisplay').textContent = totalCredit.toFixed(2);
}

function fetchNextVoucherNo(type) {
    fetch(`{{ url('admin/voucher-entry/next-voucher-no') }}?type=${type}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('voucherNo').value = data.voucher_no;
                document.getElementById('voucherNoDisplay').textContent = data.voucher_no;
            }
        });
}

function showStandardNarrations() {
    // TODO: Implement standard narrations modal
    alert('Standard Narrations feature coming soon!');
}

function saveVoucher() {
    const items = [];
    let hasItems = false;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const accountName = row.querySelector('.account-name')?.value;
        const accountType = row.querySelector('.account-type')?.value;
        const accountId = row.querySelector('.account-id')?.value;
        const accountCode = row.querySelector('.account-code')?.value;
        const debitAmount = row.querySelector('.debit-amount')?.value;
        const creditAmount = row.querySelector('.credit-amount')?.value;
        
        if (accountName || parseFloat(debitAmount) > 0 || parseFloat(creditAmount) > 0) {
            hasItems = true;
            items.push({
                account_type: accountType,
                account_id: accountId,
                account_code: accountCode,
                account_name: accountName,
                debit_amount: debitAmount || 0,
                credit_amount: creditAmount || 0,
            });
        }
    });
    
    if (!hasItems) {
        alert('Please add at least one entry');
        return;
    }
    
    // Validate debit = credit
    const totalDebit = parseFloat(document.getElementById('totalDebitDisplay').textContent) || 0;
    const totalCredit = parseFloat(document.getElementById('totalCreditDisplay').textContent) || 0;
    
    if (Math.abs(totalDebit - totalCredit) > 0.01) {
        alert('Debit and Credit totals must be equal!\nDebit: ' + totalDebit.toFixed(2) + '\nCredit: ' + totalCredit.toFixed(2));
        return;
    }
    
    const formData = {
        voucher_date: document.getElementById('voucherDate').value,
        voucher_type: document.getElementById('voucherType').value,
        multi_narration: document.getElementById('multiNarration').value,
        narration: document.getElementById('narration').value,
        items: items,
        _token: '{{ csrf_token() }}'
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.voucher-entry.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Voucher #' + data.voucher_no + ' saved successfully!');
            window.location.href = '{{ route("admin.voucher-entry.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Failed to save voucher');
    });
}

function deleteEntry() {
    const selectedRow = document.querySelector('#itemsTableBody tr.row-selected');
    if (selectedRow) {
        selectedRow.remove();
        calculateTotals();
    } else {
        alert('Please select a row to delete');
    }
}

function cancelVoucher() {
    if (confirm('Are you sure you want to cancel this voucher entry?')) {
        window.location.href = '{{ route("admin.voucher-entry.index") }}';
    }
}
</script>
@endsection
