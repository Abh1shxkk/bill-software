@extends('layouts.admin')

@section('title', 'Customer Receipts')

@section('content')
<style>
    .table-compact {
        font-size: 12px;
    }
    .table-compact th, .table-compact td {
        padding: 8px 10px;
        vertical-align: middle;
    }
    .badge-cash {
        background-color: #28a745;
        color: white;
    }
    .badge-cheque {
        background-color: #17a2b8;
        color: white;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-receipt me-2"></i> Customer Receipts</h4>
        <small class="text-muted">Manage receipt transactions from customers</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customer-receipt.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Receipt
        </a>
        <a href="{{ route('admin.customer-receipt.modification') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-square"></i> Modification
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.customer-receipt.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Trn No, Bank, Salesman..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Receipts Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-compact mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Trn No</th>
                        <th style="width: 100px;">Date</th>
                        <th>Bank</th>
                        <th>Salesman</th>
                        <th class="text-end">Cash</th>
                        <th class="text-end">Cheque</th>
                        <th class="text-end">TDS</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td><strong>{{ $receipt->trn_no }}</strong></td>
                        <td>{{ $receipt->receipt_date->format('d/m/Y') }}</td>
                        <td>{{ $receipt->bank_name ?? '-' }}</td>
                        <td>{{ $receipt->salesman_name ?? '-' }}</td>
                        <td class="text-end">
                            @if($receipt->total_cash > 0)
                                <span class="badge badge-cash">₹{{ number_format($receipt->total_cash, 2) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($receipt->total_cheque > 0)
                                <span class="badge badge-cheque">₹{{ number_format($receipt->total_cheque, 2) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">₹{{ number_format($receipt->tds_amount, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.customer-receipt.show', $receipt->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteReceipt({{ $receipt->id }}, {{ $receipt->trn_no }})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No receipts found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($receipts->hasPages())
    <div class="card-footer bg-white">
        {{ $receipts->links() }}
    </div>
    @endif
</div>

<script>
function deleteReceipt(id, trnNo) {
    if (confirm(`Are you sure you want to delete Receipt #${trnNo}?`)) {
        fetch(`{{ url('admin/customer-receipt') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Failed to delete receipt');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting');
        });
    }
}
</script>
@endsection
