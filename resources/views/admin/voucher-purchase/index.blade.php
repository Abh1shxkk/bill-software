@extends('layouts.admin')

@section('title', 'Voucher Purchase (Input GST) - List')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Voucher Purchase (Input GST)</h5>
        <a href="{{ route('admin.voucher-purchase.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Voucher
        </a>
    </div>
    <div class="card-body">
        <!-- Search & Filter -->
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                <a href="{{ route('admin.voucher-purchase.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>V.No</th>
                        <th>Date</th>
                        <th>Bill No</th>
                        <th>Supplier</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">GST</th>
                        <th class="text-end">Net Amt</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->voucher_no }}</td>
                        <td>{{ $voucher->voucher_date?->format('d-M-y') }}</td>
                        <td>{{ $voucher->bill_no }}</td>
                        <td>{{ $voucher->supplier_name }}</td>
                        <td class="text-end">{{ number_format($voucher->amount, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->total_gst, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->net_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $voucher->status == 'active' ? 'success' : ($voucher->status == 'reversed' ? 'warning' : 'danger') }}">
                                {{ ucfirst($voucher->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.voucher-purchase.show', $voucher->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">No vouchers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $vouchers->links() }}
    </div>
</div>
@endsection
