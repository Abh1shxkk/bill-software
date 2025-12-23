@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Transactions')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i> Breakage/Expiry to Supplier - Issued Transactions</h4>
            <div class="text-muted small">View and manage issued transactions</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.breakage-supplier.issued-transaction') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Transaction
            </a>
            <a href="{{ route('admin.breakage-supplier.issued-modification') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i> Modification
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.breakage-supplier.issued-index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small mb-1">Filter By</label>
                    <select name="filter_by" class="form-select form-select-sm">
                        <option value="trn_no" {{ request('filter_by') == 'trn_no' ? 'selected' : '' }}>Trn No</option>
                        <option value="supplier" {{ request('filter_by') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                        <option value="narration" {{ request('filter_by') == 'narration' ? 'selected' : '' }}>Narration</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Search</button>
                    <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle me-1"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" style="font-size: 12px;">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">Trn No</th>
                            <th style="width: 100px;">Date</th>
                            <th>Supplier</th>
                            <th style="width: 60px;">Type</th>
                            <th style="width: 60px;">Brk</th>
                            <th style="width: 60px;">Exp</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td><strong>{{ $transaction->trn_no }}</strong></td>
                            <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $transaction->supplier_name ?? '-' }}</td>
                            <td>
                                @if($transaction->note_type == 'R')
                                    <span class="badge bg-info">Repl</span>
                                @else
                                    <span class="badge bg-warning text-dark">Credit</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $transaction->brk_count ?? 0 }}</td>
                            <td class="text-center">{{ $transaction->exp_count ?? 0 }}</td>
                            <td class="text-end">₹{{ number_format($transaction->total_inv_amt ?? 0, 2) }}</td>
                            <td>
                                @if($transaction->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($transaction->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.breakage-supplier.show-issued', $transaction->id) }}" class="btn btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="cancelTransaction({{ $transaction->id }})" title="Cancel">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No transactions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function cancelTransaction(id) {
    if (confirm('Are you sure you want to cancel this transaction?')) {
        fetch(`{{ url('admin/breakage-supplier/issued') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling transaction');
        });
    }
}
</script>
@endsection
