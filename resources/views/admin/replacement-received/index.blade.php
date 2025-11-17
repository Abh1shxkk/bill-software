@extends('layouts.admin')
@section('title', 'Replacement Received Invoices')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-in-down me-2"></i> Replacement Received Invoices</h4>
        <div class="text-muted small">View and manage all replacement received transactions</div>
    </div>
    <div>
        <a href="{{ route('admin.replacement-received.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card-body">
        <form method="GET" action="{{ route('admin.replacement-received.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter By</label>
                <select class="form-select" name="filter_by">
                    <option value="supplier_name" {{ request('filter_by') == 'supplier_name' ? 'selected' : '' }}>Supplier Name</option>
                    <option value="rr_no" {{ request('filter_by', 'rr_no') == 'rr_no' ? 'selected' : '' }}>RR No.</option>
                    <option value="total_amount" {{ request('filter_by') == 'total_amount' ? 'selected' : '' }}>Amount</option>
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
                    <th style="width: 50px;">#</th>
                    <th style="width: 100px;">Date</th>
                    <th style="width: 100px;">RR No.</th>
                    <th>Supplier</th>
                    <th style="width: 120px;" class="text-end">Total Amount</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions ?? [] as $transaction)
                <tr>
                    <td>{{ $loop->iteration + (($transactions->currentPage() - 1) * $transactions->perPage()) }}</td>
                    <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }}</td>
                    <td><strong>{{ $transaction->rr_no }}</strong></td>
                    <td>{{ $transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? '-') }}</td>
                    <td class="text-end">₹{{ number_format($transaction->total_amount ?? 0, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($transaction->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.replacement-received.show', $transaction->id) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.replacement-received.modification') }}?id={{ $transaction->id }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.replacement-received.destroy', $transaction->id) }}" method="POST" class="d-inline ajax-delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.replacement-received.destroy', $transaction->id) }}" data-delete-message="Delete this transaction?" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No replacement received transactions found</td>
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
