@extends('layouts.admin')

@section('title', 'Pending Order Items List')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i> Pending Order Items</h4>
        <a href="{{ route('admin.pending-order-item.transaction') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Entry
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Action</th>
                            <th class="text-end">Quantity</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>
                                <span class="badge bg-{{ $item->action_type === 'I' ? 'success' : 'danger' }}">
                                    {{ $item->action_type === 'I' ? 'Insert' : 'Delete' }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ $item->created_at->format('d-M-Y H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteItem(id) {
    if (!confirm('Are you sure you want to delete this item?')) return;
    
    fetch(`{{ url('admin/pending-order-item') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting item');
    });
}
</script>
@endpush
