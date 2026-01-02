@extends('layouts.admin')

@section('title', 'Voucher Entry - List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Voucher Entry</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.voucher-entry.transaction') }}?type=receipt" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Voucher
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label small">Voucher Type</label>
                <select name="voucher_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="receipt" {{ request('voucher_type') == 'receipt' ? 'selected' : '' }}>Receipt</option>
                    <option value="payment" {{ request('voucher_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                    <option value="contra" {{ request('voucher_type') == 'contra' ? 'selected' : '' }}>Contra</option>
                    <option value="journal" {{ request('voucher_type') == 'journal' ? 'selected' : '' }}>Journal</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Voucher No, Narration..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.voucher-entry.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Voucher No</th>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 100px;">Type</th>
                        <th>Narration</th>
                        <th class="text-end" style="width: 120px;">Debit</th>
                        <th class="text-end" style="width: 120px;">Credit</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->voucher_no }}</td>
                        <td>{{ $voucher->voucher_date ? $voucher->voucher_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                                $typeBadges = [
                                    'receipt' => 'bg-success',
                                    'payment' => 'bg-danger',
                                    'contra' => 'bg-info',
                                    'journal' => 'bg-warning text-dark',
                                ];
                            @endphp
                            <span class="badge {{ $typeBadges[$voucher->voucher_type] ?? 'bg-secondary' }}">
                                {{ ucfirst($voucher->voucher_type) }}
                            </span>
                        </td>
                        <td>{{ Str::limit($voucher->narration, 50) }}</td>
                        <td class="text-end">{{ number_format($voucher->total_debit, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->total_credit, 2) }}</td>
                        <td>
                            @if($voucher->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.voucher-entry.show', $voucher->id) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.voucher-entry.modification') }}?voucher_no={{ $voucher->voucher_no }}&type={{ $voucher->voucher_type }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteVoucher({{ $voucher->id }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No vouchers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing {{ $vouchers->firstItem() ?? 0 }} to {{ $vouchers->lastItem() ?? 0 }} of {{ $vouchers->total() }} entries
            </div>
            {{ $vouchers->withQueryString()->links() }}
        </div>
    </div>
</div>

<script>
function deleteVoucher(id) {
    if (confirm('Are you sure you want to delete this voucher?')) {
        fetch(`{{ url('admin/voucher-entry') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(e => {
            console.error('Error:', e);
            alert('Failed to delete voucher');
        });
    }
}
</script>
@endsection
