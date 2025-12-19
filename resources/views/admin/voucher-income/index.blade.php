@extends('layouts.admin')

@section('title', 'Voucher Income (Output GST) - List')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Voucher Income (Output GST)</h5>
        <div>
            <a href="{{ route('admin.voucher-income.transaction') }}" class="btn btn-light btn-sm">+ New Voucher</a>
            <a href="{{ route('admin.voucher-income.modification') }}" class="btn btn-warning btn-sm">Modify</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}"></div>
            <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}"></div>
            <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Filter</button><a href="{{ route('admin.voucher-income.index') }}" class="btn btn-secondary btn-sm ms-1">Reset</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead class="table-dark">
                    <tr><th>V.No</th><th>Date</th><th>Customer</th><th>Amount</th><th>GST</th><th>Net Amount</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->voucher_no }}</td>
                        <td>{{ $voucher->voucher_date?->format('d-M-Y') }}</td>
                        <td>{{ $voucher->customer_name }}</td>
                        <td class="text-end">{{ number_format($voucher->amount, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->total_gst, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->net_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $voucher->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($voucher->status) }}</span></td>
                        <td>
                            <a href="{{ route('admin.voucher-income.show', $voucher->id) }}" class="btn btn-info btn-sm">View</a>
                            <button class="btn btn-danger btn-sm" onclick="deleteVoucher({{ $voucher->id }})">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No vouchers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $vouchers->links() }}
    </div>
</div>
<script>
function deleteVoucher(id) {
    if (!confirm('Delete this voucher?')) return;
    fetch(`{{ url('admin/voucher-income') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(r => r.json()).then(data => { alert(data.message); if (data.success) location.reload(); });
}
</script>
@endsection
