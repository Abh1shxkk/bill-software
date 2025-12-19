@extends('layouts.admin')

@section('title', 'Purchase Vouchers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-cart-plus me-2"></i> Purchase Vouchers</h4>
        <small class="text-muted">Manage purchase vouchers (HSN based entries)</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.purchase-voucher.modification') }}" class="btn btn-warning">
            <i class="bi bi-pencil-square me-1"></i> Modification
        </a>
        <a href="{{ route('admin.purchase-voucher.transaction') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Voucher
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <form class="row g-2" method="GET">
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
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Bill No.</th>
                    <th>Transaction No.</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th class="text-end">Net Amount</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $index => $voucher)
                <tr>
                    <td>{{ $vouchers->firstItem() + $index }}</td>
                    <td><strong>{{ $voucher->bill_no }}</strong></td>
                    <td>{{ str_pad($voucher->trn_no, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $voucher->bill_date ? $voucher->bill_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $voucher->supplier?->name ?? '-' }}</td>
                    <td class="text-end">₹{{ number_format($voucher->net_amount, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.purchase-voucher.show', $voucher->id) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.purchase-voucher.modification') }}?bill_no={{ $voucher->bill_no }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteVoucher({{ $voucher->id }})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No vouchers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer">
        {{ $vouchers->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteVoucher(id) {
    if (!confirm('Are you sure you want to delete this voucher?')) return;
    
    fetch(`{{ url('admin/purchase-voucher') }}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) location.reload();
        else alert('Error: ' + result.message);
    })
    .catch(e => { console.error(e); alert('Error deleting voucher'); });
}
</script>
@endpush
