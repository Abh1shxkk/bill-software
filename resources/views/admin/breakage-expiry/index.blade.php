@extends('layouts.admin')
@section('title', 'Breakage/Expiry Invoices')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-exclamation-triangle me-2"></i> Breakage/Expiry Invoices</h4>
        <div class="text-muted small">View and manage all breakage/expiry transactions</div>
    </div>
    <div>
        <a href="{{ route('admin.breakage-expiry.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card-body">
        <form method="GET" action="{{ route('admin.breakage-expiry.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter By</label>
                <select class="form-select" name="filter_by">
                    <option value="customer_name" {{ request('filter_by') == 'customer_name' ? 'selected' : '' }}>Customer Name</option>
                    <option value="sr_no" {{ request('filter_by') == 'sr_no' ? 'selected' : '' }}>SR No.</option>
                    <option value="salesman_name" {{ request('filter_by') == 'salesman_name' ? 'selected' : '' }}>Salesman</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Enter search term...">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>SR No.</th>
                    <th>Customer</th>
                    <th>Salesman</th>
                    <th class="text-end">Net Amount</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions ?? [] as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td><strong>{{ $transaction->sr_no }}</strong></td>
                    <td>{{ $transaction->customer_name ?? '-' }}</td>
                    <td>{{ $transaction->salesman_name ?? '-' }}</td>
                    <td class="text-end">₹{{ number_format($transaction->net_amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.breakage-expiry.show', $transaction->id) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.breakage-expiry.modification') }}?sr_no={{ $transaction->sr_no }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.breakage-expiry.destroy', $transaction) }}" method="POST" class="d-inline ajax-delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.breakage-expiry.destroy', $transaction) }}" data-delete-message="Delete this transaction?" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No transactions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($transactions) && $transactions->hasPages())
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
    @endif
</div>


@endsection
