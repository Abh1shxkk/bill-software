@extends('layouts.admin')

@section('title', 'Cash Deposited / Withdrawn from Bank - List')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cash Deposited / Withdrawn from Bank</h5>
        <a href="{{ route('admin.bank-transaction.transaction') }}" class="btn btn-light btn-sm">+ New Transaction</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="D" {{ request('type') == 'D' ? 'selected' : '' }}>Deposit</option>
                    <option value="W" {{ request('type') == 'W' ? 'selected' : '' }}>Withdrawal</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-auto">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.bank-transaction.index') }}" class="btn btn-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Trans. No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Bank</th>
                        <th>Cheque No</th>
                        <th>Amount</th>
                        <th>Narration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_no }}</td>
                        <td>{{ $transaction->transaction_date?->format('d-M-Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $transaction->transaction_type == 'D' ? 'success' : 'danger' }}">
                                {{ $transaction->transaction_type == 'D' ? 'Deposit' : 'Withdrawal' }}
                            </span>
                        </td>
                        <td>{{ $transaction->bank_name }}</td>
                        <td>{{ $transaction->cheque_no }}</td>
                        <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ Str::limit($transaction->narration, 30) }}</td>
                        <td>
                            <a href="{{ route('admin.bank-transaction.show', $transaction->id) }}" class="btn btn-info btn-sm">View</a>
                            <button class="btn btn-danger btn-sm" onclick="deleteTransaction({{ $transaction->id }})">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No transactions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <strong>Total Deposits:</strong> {{ number_format($transactions->where('transaction_type', 'D')->sum('amount'), 2) }}
                &nbsp;|&nbsp;
                <strong>Total Withdrawals:</strong> {{ number_format($transactions->where('transaction_type', 'W')->sum('amount'), 2) }}
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<script>
function deleteTransaction(id) {
    if (!confirm('Are you sure you want to delete this transaction?')) return;
    
    fetch(`{{ url('admin/bank-transaction') }}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
}
</script>
@endsection
