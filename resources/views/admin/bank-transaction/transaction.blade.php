@extends('layouts.admin')

@section('title', 'Cash Deposited / Withdrawn from Bank')

@section('content')
<style>
    .bank-form { background: #e0e0e0; padding: 20px; }
    .bank-form-inner { background: #f5f5f5; border: 1px solid #999; padding: 20px; max-width: 600px; margin: 0 auto; }
    .form-title { text-align: center; font-weight: bold; font-size: 16px; color: #000; margin-bottom: 20px; }
    .form-row { display: flex; align-items: center; margin-bottom: 12px; }
    .form-label { width: 140px; font-weight: 600; font-size: 13px; }
    .form-input { flex: 1; }
    .form-input input, .form-input select { font-size: 13px; padding: 5px 10px; height: 30px; border: 1px solid #999; }
    .form-input input:focus, .form-input select:focus { outline: none; border-color: #666; }
    .bank-display { background: #f0f0f0; border: 1px solid #999; padding: 5px 10px; font-size: 13px; min-width: 250px; display: inline-block; }
    .day-display { margin-left: 15px; font-size: 13px; font-weight: 600; }
    .footer-section { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ccc; }
    .btn-action { font-size: 13px; padding: 8px 30px; min-width: 100px; }
</style>

<div class="bank-form">
    <div class="bank-form-inner">
        <div class="form-title">-: CASH DEPOSITED / WITHDRAWN FROM BANK :-</div>
        
        <form id="transactionForm" autocomplete="off">
            @csrf
            <div class="form-row">
                <div class="form-label">Date :</div>
                <div class="form-input">
                    <input type="date" name="transaction_date" id="transactionDate" value="{{ date('Y-m-d') }}" style="width: 150px;" onchange="updateDayName()">
                    <span class="day-display" id="dayName">{{ date('l') }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">D(eposited) / W(ithdrawn) :</div>
                <div class="form-input">
                    <input type="text" name="transaction_type" id="transactionType" value="D" maxlength="1" style="width: 40px; text-transform: uppercase; text-align: center; cursor: pointer;" onclick="toggleType()" onchange="validateType()" readonly>
                    <span id="typeLabel" style="margin-left: 10px; font-size: 12px; color: #666;">(Click to toggle)</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Bank :</div>
                <div class="form-input">
                    <select name="bank_id" id="bankId" style="width: 350px;" onchange="updateBankName()">
                        <option value="">--- Select Bank ---</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" data-name="{{ $bank->name }}">{{ $bank->alter_code ?? $bank->id }} - {{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Cheque No. :</div>
                <div class="form-input">
                    <input type="text" name="cheque_no" id="chequeNo" style="width: 200px;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Amount :</div>
                <div class="form-input">
                    <input type="number" name="amount" id="amount" step="0.01" style="width: 200px;">
                </div>
            </div>

            <div class="form-row" style="margin-top: 20px;">
                <div class="form-label">Narration :</div>
                <div class="form-input">
                    <input type="text" name="narration" id="narration" style="width: 100%;">
                </div>
            </div>

            <div class="footer-section">
                <button type="button" class="btn btn-primary btn-action" onclick="saveTransaction()">Save</button>
                <a href="{{ route('admin.bank-transaction.index') }}" class="btn btn-secondary btn-action ms-2">Exit (Esc)</a>
            </div>
        </form>
    </div>
</div>

<script>
const banks = @json($banks);

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.bank-transaction.index") }}';
        }
    });
    updateDayName();
});

function updateDayName() {
    const date = new Date(document.getElementById('transactionDate').value);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    document.getElementById('dayName').textContent = days[date.getDay()];
}

function toggleType() {
    const input = document.getElementById('transactionType');
    input.value = input.value === 'D' ? 'W' : 'D';
}

function validateType() {
    const input = document.getElementById('transactionType');
    const val = input.value.toUpperCase();
    if (val !== 'D' && val !== 'W') {
        alert('Please enter D for Deposit or W for Withdrawal');
        input.value = 'D';
    } else {
        input.value = val;
    }
}

function updateBankName() {
    const select = document.getElementById('bankId');
    const option = select.options[select.selectedIndex];
    document.getElementById('bankNameDisplay').textContent = option.dataset.name || '';
}

function saveTransaction() {
    const bankId = document.getElementById('bankId').value;
    const amount = document.getElementById('amount').value;
    const type = document.getElementById('transactionType').value.toUpperCase();
    
    if (!bankId) { alert('Please select a bank'); return; }
    if (!amount || parseFloat(amount) <= 0) { alert('Please enter a valid amount'); return; }
    if (type !== 'D' && type !== 'W') { alert('Please enter D or W for transaction type'); return; }
    
    const formData = {
        transaction_date: document.getElementById('transactionDate').value,
        transaction_type: type,
        bank_id: bankId,
        cheque_no: document.getElementById('chequeNo').value,
        amount: parseFloat(amount),
        narration: document.getElementById('narration').value,
        _token: '{{ csrf_token() }}'
    };
    
    fetch('{{ route("admin.bank-transaction.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Transaction #' + data.transaction_no + ' saved successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => alert('Failed to save transaction'));
}
</script>
@endsection
