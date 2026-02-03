@extends('layouts.admin')

@section('title', 'Voucher Entry - Modification')

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
    
    .footer-section { background: #e0e0e0; padding: 8px 15px; border: 1px solid #ccc; border-top: none; }
    .footer-btn { font-size: 11px; padding: 4px 15px; }
    
    .search-section { background: #ffffcc; padding: 10px; border: 1px solid #ccc; margin-bottom: 10px; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form p-0">
        <!-- Search Section -->
        <div class="search-section">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="field-group">
                        <label style="color: #c00;">Voucher No :</label>
                        <input type="number" class="form-control" id="searchVoucherNo" style="width: 100px;" placeholder="Enter No">
                        <button type="button" class="btn btn-primary btn-sm" onclick="loadVoucher()">Load</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="field-group">
                        <label>Type :</label>
                        <select class="form-control" id="searchVoucherType" style="width: 130px;">
                            <option value="">All Types</option>
                            <option value="receipt">Receipt</option>
                            <option value="payment">Payment</option>
                            <option value="contra">Contra</option>
                            <option value="journal">Journal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="showVoucherList()">
                        <i class="bi bi-list"></i> Browse Vouchers
                    </button>
                </div>
            </div>
        </div>

        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="voucherId" value="">

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="field-group">
                                <label>Multi Narration :</label>
                                <select class="form-control" name="multi_narration" id="multiNarration" style="width: 50px;">
                                    <option value="0">N</option>
                                    <option value="1">Y</option>
                                </select>
                            </div>
                            <select class="form-control" name="voucher_type" id="voucherType" style="width: 140px; background: #800080; color: white; font-weight: bold;">
                                <option value="receipt">Receipt Voucher</option>
                                <option value="payment">Payment Voucher</option>
                                <option value="contra">Contra Voucher</option>
                                <option value="journal">Journal Voucher</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <label class="field-group" style="color: #008000;">Voucher Date :</label>
                            <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                            <span id="dayName" style="color: #0000ff; font-weight: bold;">{{ date('l') }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <label style="color: #800080; font-weight: bold;">Voucher No :</label>
                            <span id="voucherNoDisplay" style="font-weight: bold; font-size: 14px;">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border" style="border-top: none !important;">
                <div class="table-responsive" style="overflow-y: auto; max-height: 300px;">
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
                    <span class="text-muted small">Load a voucher to modify</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger footer-btn" onclick="deleteVoucher()">Delete Voucher</button>
                    <button type="button" class="btn btn-success footer-btn" onclick="updateVoucher()" id="btnUpdate" disabled>Update (End)</button>
                    <a href="{{ route('admin.voucher-entry.index') }}" class="btn btn-secondary footer-btn">Exit (Esc)</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let itemRowCount = 0;
let currentVoucherId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Add initial empty rows
    for (let i = 0; i < 5; i++) {
        addItemRow();
    }
    
    // Update day name on date change
    document.getElementById('voucherDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').textContent = days[date.getDay()];
    });
    
    // Check URL params for voucher_no
    const urlParams = new URLSearchParams(window.location.search);
    const voucherNo = urlParams.get('voucher_no');
    const type = urlParams.get('type');
    
    if (voucherNo) {
        document.getElementById('searchVoucherNo').value = voucherNo;
        if (type) {
            document.getElementById('searchVoucherType').value = type;
        }
        loadVoucher();
    }
});

function addItemRow(data = null) {
    itemRowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', itemRowCount);
    row.innerHTML = `
        <td>
            <input type="text" class="form-control account-name" name="items[${itemRowCount}][account_name]" value="${data?.account_name || ''}">
            <input type="hidden" class="account-type" name="items[${itemRowCount}][account_type]" value="${data?.account_type || ''}">
            <input type="hidden" class="account-id" name="items[${itemRowCount}][account_id]" value="${data?.account_id || ''}">
            <input type="hidden" class="account-code" name="items[${itemRowCount}][account_code]" value="${data?.account_code || ''}">
        </td>
        <td>
            <input type="number" class="form-control text-end debit-amount" name="items[${itemRowCount}][debit_amount]" 
                   step="0.01" value="${data?.debit_amount || ''}" onchange="calculateTotals()">
        </td>
        <td>
            <input type="number" class="form-control text-end credit-amount" name="items[${itemRowCount}][credit_amount]" 
                   step="0.01" value="${data?.credit_amount || ''}" onchange="calculateTotals()">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); calculateTotals();">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    
    if (data && (data.account_name || parseFloat(data.debit_amount) > 0 || parseFloat(data.credit_amount) > 0)) {
        row.classList.add('row-complete');
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

function loadVoucher() {
    const voucherNo = document.getElementById('searchVoucherNo').value;
    const voucherType = document.getElementById('searchVoucherType').value;
    
    if (!voucherNo) {
        alert('Please enter a voucher number');
        return;
    }
    
    let url = `{{ url('admin/voucher-entry/get-by-voucher-no') }}/${voucherNo}`;
    if (voucherType) {
        url += `?type=${voucherType}`;
    }
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.voucher) {
                populateForm(data.voucher);
            } else {
                alert('Voucher not found');
            }
        })
        .catch(e => {
            console.error('Error:', e);
            alert('Failed to load voucher');
        });
}

function populateForm(voucher) {
    currentVoucherId = voucher.id;
    document.getElementById('voucherId').value = voucher.id;
    document.getElementById('voucherNoDisplay').textContent = voucher.voucher_no;
    document.getElementById('voucherType').value = voucher.voucher_type;
    document.getElementById('voucherDate').value = voucher.voucher_date ? voucher.voucher_date.split('T')[0] : '';
    document.getElementById('multiNarration').value = voucher.multi_narration ? '1' : '0';
    document.getElementById('narration').value = voucher.narration || '';
    
    // Update day name
    if (voucher.voucher_date) {
        const date = new Date(voucher.voucher_date);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').textContent = days[date.getDay()];
    }
    
    // Clear existing rows
    document.getElementById('itemsTableBody').innerHTML = '';
    itemRowCount = 0;
    
    // Add items
    if (voucher.items && voucher.items.length > 0) {
        voucher.items.forEach(item => {
            addItemRow({
                account_type: item.account_type,
                account_id: item.account_id,
                account_code: item.account_code,
                account_name: item.account_name,
                debit_amount: parseFloat(item.debit_amount) || '',
                credit_amount: parseFloat(item.credit_amount) || ''
            });
        });
    }
    
    // Add empty rows
    for (let i = 0; i < 3; i++) {
        addItemRow();
    }
    
    calculateTotals();
    document.getElementById('btnUpdate').disabled = false;
}

function updateVoucher() {
    if (!currentVoucherId) {
        alert('No voucher loaded');
        return;
    }
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const accountName = row.querySelector('.account-name')?.value;
        const debitAmount = row.querySelector('.debit-amount')?.value;
        const creditAmount = row.querySelector('.credit-amount')?.value;
        
        if (accountName || parseFloat(debitAmount) > 0 || parseFloat(creditAmount) > 0) {
            items.push({
                account_type: row.querySelector('.account-type')?.value,
                account_id: row.querySelector('.account-id')?.value,
                account_code: row.querySelector('.account-code')?.value,
                account_name: accountName,
                debit_amount: debitAmount || 0,
                credit_amount: creditAmount || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one entry');
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
    
    fetch(`{{ url('admin/voucher-entry') }}/${currentVoucherId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Voucher updated successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Failed to update voucher');
    });
}

function deleteVoucher() {
    if (!currentVoucherId) {
        alert('No voucher loaded');
        return;
    }
    
    if (confirm('Are you sure you want to delete this voucher?')) {
        fetch(`{{ url('admin/voucher-entry') }}/${currentVoucherId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = '{{ route("admin.voucher-entry.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function showVoucherList() {
    window.location.href = '{{ route("admin.voucher-entry.index") }}';
}
</script>
@endsection
