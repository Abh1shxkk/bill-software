@extends('layouts.admin')

@section('title', 'Bank Transaction #' . $transaction->transaction_no)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bank Transaction #{{ $transaction->transaction_no }}</h5>
        <a href="{{ route('admin.bank-transaction.index') }}" class="btn btn-light btn-sm">Back to List</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Transaction No:</strong></td>
                        <td>{{ $transaction->transaction_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ $transaction->transaction_date?->format('d-M-Y (l)') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td>
                            <span class="badge bg-{{ $transaction->transaction_type == 'D' ? 'success' : 'danger' }} fs-6">
                                {{ $transaction->transaction_type == 'D' ? 'Cash Deposited' : 'Cash Withdrawn' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Bank:</strong></td>
                        <td>{{ $transaction->bank_name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Cheque No:</strong></td>
                        <td>{{ $transaction->cheque_no ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount:</strong></td>
                        <td class="fs-4 text-primary"><strong>₹ {{ number_format($transaction->amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($transaction->narration)
        <div class="mt-3 p-3 bg-light rounded">
            <strong>Narration:</strong><br>
            {{ $transaction->narration }}
        </div>
        @endif
        
        <div class="mt-4">
            <button class="btn btn-danger" onclick="deleteTransaction({{ $transaction->id }})">Delete Transaction</button>
            <a href="{{ route('admin.bank-transaction.transaction') }}" class="btn btn-primary ms-2">New Transaction</a>
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
        if (data.success) window.location.href = '{{ route("admin.bank-transaction.index") }}';
    });
}
</script>
@endsection
