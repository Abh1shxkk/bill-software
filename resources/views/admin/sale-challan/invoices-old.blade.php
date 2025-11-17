@extends('layouts.admin')

@section('title', 'Sale Challan List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Sale Challan List</h4>
        <div class="text-muted small">View and manage sale challans</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-challan.transaction') }}" class="btn btn-warning">
            <i class="bi bi-plus-circle me-1"></i> New Challan
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="filterBy">
                    <option value="customer_name">Customer Name</option>
                    <option value="challan_no">Challan No</option>
                    <option value="salesman_name">Salesman Name</option>
                    <option value="challan_amount">Challan Amount</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search...">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" id="dateFrom" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" id="dateTo" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="invoicedStatus">
                    <option value="">All Status</option>
                    <option value="no">Pending (Not Invoiced)</option>
                    <option value="yes">Invoiced</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Challan Date</th>
                        <th>Challan No</th>
                        <th>Customer</th>
                        <th>Salesman</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody id="challansTableBody">
                    @forelse($challans as $index => $challan)
                    <tr>
                        <td>{{ $challans->firstItem() + $index }}</td>
                        <td>{{ $challan->challan_date->format('d-m-Y') }}</td>
                        <td><strong>{{ $challan->challan_no }}</strong></td>
                        <td>{{ $challan->customer->name ?? 'N/A' }}</td>
                        <td>{{ $challan->salesman->name ?? 'N/A' }}</td>
                        <td class="text-end">₹ {{ number_format($challan->net_amount, 2) }}</td>
                        <td class="text-center">
                            @if($challan->is_invoiced)
                                <span class="badge bg-success">Invoiced</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.sale-challan.show', $challan->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$challan->is_invoiced)
                            <a href="{{ route('admin.sale-challan.modification') }}?challan_no={{ $challan->challan_no }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteChallan({{ $challan->id }}, '{{ $challan->challan_no }}')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No challans found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing {{ $challans->firstItem() ?? 0 }} to {{ $challans->lastItem() ?? 0 }} of {{ $challans->total() }} entries
            </div>
            <div>
                {{ $challans->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete challan <strong id="deleteChallanNo"></strong>?</p>
                <p class="text-warning"><i class="bi bi-info-circle me-1"></i> Stock will be restored after deletion.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteId = null;

function deleteChallan(id, challanNo) {
    deleteId = id;
    document.getElementById('deleteChallanNo').textContent = challanNo;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteId) return;
    
    fetch(`{{ url('admin/sale-challan') }}/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting challan');
    });
});

// Search functionality
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => performSearch(), 300);
});

document.getElementById('filterBy').addEventListener('change', performSearch);
document.getElementById('dateFrom').addEventListener('change', performSearch);
document.getElementById('dateTo').addEventListener('change', performSearch);
document.getElementById('invoicedStatus').addEventListener('change', performSearch);

function performSearch() {
    const params = new URLSearchParams();
    const search = document.getElementById('searchInput').value;
    const filterBy = document.getElementById('filterBy').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const invoicedStatus = document.getElementById('invoicedStatus').value;
    
    if (search) params.append('search', search);
    if (filterBy) params.append('filter_by', filterBy);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    if (invoicedStatus) params.append('invoiced_status', invoicedStatus);
    
    window.location.href = '{{ route("admin.sale-challan.invoices") }}?' + params.toString();
}
</script>
@endsection
