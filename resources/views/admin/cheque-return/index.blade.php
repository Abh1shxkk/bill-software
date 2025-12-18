@extends('layouts.admin')

@section('title', 'Cheque Returned Unpaid')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-credit-card-2-back me-2"></i> Cheque Returned Unpaid</h4>
        <div class="text-muted small">Manage cheques returned by bank</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="filter_by" class="form-label">Filter By</label>
                    <select class="form-select" id="filter_by" name="filter_by">
                        <option value="customer_name">Customer Name</option>
                        <option value="cheque_no">Cheque No.</option>
                        <option value="bank_name">Bank Name</option>
                        <option value="trn_no">Trans No.</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Enter customer name..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter" name="status">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="returned">Returned</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Clear All
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive" id="cheque-table-wrapper" style="position: relative; min-height: 400px;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Trans No.</th>
                    <th>Customer</th>
                    <th>Bank Name</th>
                    <th>Cheque No.</th>
                    <th class="text-end">Amount</th>
                    <th>Status</th>
                    <th>Status Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="cheque-table-body">
                @forelse($chequeData as $index => $cheque)
                <tr data-id="{{ $cheque['id'] }}" data-cheque='@json($cheque)'>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $cheque['date'] }}</td>
                    <td>{{ $cheque['trn_no'] }}</td>
                    <td>{{ $cheque['customer_name'] ?? '-' }}</td>
                    <td>{{ $cheque['bank_name'] }}</td>
                    <td>{{ $cheque['cheque_no'] }}</td>
                    <td class="text-end">
                        <span class="badge bg-success">₹{{ number_format($cheque['amount'], 2) }}</span>
                    </td>
                    <td>
                        @if($cheque['status'] === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($cheque['status'] === 'returned')
                            <span class="badge bg-danger">Returned</span>
                        @else
                            <span class="badge bg-secondary">Cancelled</span>
                        @endif
                    </td>
                    <td>{{ $cheque['status_date'] }}</td>
                    <td class="text-end">
                        @if($cheque['status'] === 'pending')
                            <button type="button" class="btn btn-sm btn-outline-danger btn-return-single" 
                                    data-id="{{ $cheque['id'] }}" 
                                    data-cheque-no="{{ $cheque['cheque_no'] }}"
                                    data-customer="{{ $cheque['customer_name'] ?? '-' }}"
                                    title="Mark as Returned Unpaid">
                                <i class="bi bi-arrow-return-left"></i>
                            </button>
                        @elseif($cheque['status'] === 'returned')
                            <button type="button" class="btn btn-sm btn-outline-danger btn-return-single" 
                                    data-id="{{ $cheque['id'] }}" 
                                    data-cheque-no="{{ $cheque['cheque_no'] }}"
                                    data-customer="{{ $cheque['customer_name'] ?? '-' }}"
                                    title="Mark as Returned Unpaid"
                                    disabled>
                                <i class="bi bi-arrow-return-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning btn-cancel-single" 
                                    data-id="{{ $cheque['cheque_return_id'] }}" 
                                    data-cheque-no="{{ $cheque['cheque_no'] }}"
                                    data-customer="{{ $cheque['customer_name'] ?? '-' }}"
                                    title="Cancel Return">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-danger" disabled title="Return">
                                <i class="bi bi-arrow-return-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" disabled title="Cancel Return">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">No cheques found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>Showing {{ count($chequeData) }} cheques</div>
        </div>
    </div>
</div>

<!-- Return Confirmation Modal -->
<div class="modal fade" id="returnChequeModal" tabindex="-1" aria-labelledby="returnChequeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnChequeModalLabel">Confirm Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this cheque as returned unpaid?</p>
                <div class="alert alert-warning">
                    <strong>Cheque No:</strong> <span id="return-cheque-no"></span><br>
                    <strong>Customer:</strong> <span id="return-customer"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-return">
                    <i class="bi bi-arrow-return-left me-1"></i> Mark as Returned
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Return Confirmation Modal -->
<div class="modal fade" id="cancelReturnModal" tabindex="-1" aria-labelledby="cancelReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelReturnModalLabel">Confirm Cancel Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this cheque return?</p>
                <div class="alert alert-info">
                    <strong>Cheque No:</strong> <span id="cancel-cheque-no"></span><br>
                    <strong>Customer:</strong> <span id="cancel-customer"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="confirm-cancel">
                    <i class="bi bi-x-circle me-1"></i> Cancel Return
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filter_by');
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status_filter');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('cheque-table-body');

    // Update placeholder based on filter selection
    function updatePlaceholder() {
        const filterValue = filterSelect.value;
        const placeholders = {
            'customer_name': 'Enter customer name...',
            'cheque_no': 'Enter cheque number...',
            'bank_name': 'Enter bank name...',
            'trn_no': 'Enter transaction number...'
        };
        searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
    }

    filterSelect.addEventListener('change', updatePlaceholder);
    updatePlaceholder();

    // Filter table
    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const filterBy = filterSelect.value;
        const status = statusFilter.value;

        document.querySelectorAll('#cheque-table-body tr').forEach(function(row) {
            const chequeData = row.dataset.cheque;
            if (!chequeData) return;

            const data = JSON.parse(chequeData);
            let show = true;

            // Text search based on filter
            if (searchText) {
                let fieldValue = '';
                switch (filterBy) {
                    case 'customer_name':
                        fieldValue = (data.customer_name || '').toLowerCase();
                        break;
                    case 'cheque_no':
                        fieldValue = (data.cheque_no || '').toLowerCase();
                        break;
                    case 'bank_name':
                        fieldValue = (data.bank_name || '').toLowerCase();
                        break;
                    case 'trn_no':
                        fieldValue = String(data.trn_no || '').toLowerCase();
                        break;
                }
                if (!fieldValue.includes(searchText)) {
                    show = false;
                }
            }

            // Status filter
            if (status && data.status !== status) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
        });
    }

    // Event listeners for filtering
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        filterTable();
    });

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // Clear filters
    document.getElementById('clear-filters').addEventListener('click', function() {
        filterSelect.value = 'customer_name';
        searchInput.value = '';
        statusFilter.value = '';
        updatePlaceholder();
        document.querySelectorAll('#cheque-table-body tr').forEach(row => row.style.display = '');
    });

    // Return cheque functionality
    let currentReturnId = null;
    let currentCancelId = null;

    document.addEventListener('click', function(e) {
        // Return button
        const returnBtn = e.target.closest('.btn-return-single');
        if (returnBtn) {
            currentReturnId = returnBtn.getAttribute('data-id');
            document.getElementById('return-cheque-no').textContent = returnBtn.getAttribute('data-cheque-no');
            document.getElementById('return-customer').textContent = returnBtn.getAttribute('data-customer');
            
            const modal = new bootstrap.Modal(document.getElementById('returnChequeModal'));
            modal.show();
        }

        // Cancel return button
        const cancelBtn = e.target.closest('.btn-cancel-single');
        if (cancelBtn) {
            currentCancelId = cancelBtn.getAttribute('data-id');
            document.getElementById('cancel-cheque-no').textContent = cancelBtn.getAttribute('data-cheque-no');
            document.getElementById('cancel-customer').textContent = cancelBtn.getAttribute('data-customer');
            
            const modal = new bootstrap.Modal(document.getElementById('cancelReturnModal'));
            modal.show();
        }
    });

    // Confirm return
    document.getElementById('confirm-return').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        fetch('{{ route("admin.cheque-return.return") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                customer_receipt_item_id: currentReturnId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-return-left me-1"></i> Mark as Returned';
            }
        })
        .catch(error => {
            alert('Error processing request');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-return-left me-1"></i> Mark as Returned';
        });
    });

    // Confirm cancel return
    document.getElementById('confirm-cancel').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        fetch('{{ route("admin.cheque-return.cancel") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cheque_return_id: currentCancelId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Cancel Return';
            }
        })
        .catch(error => {
            alert('Error processing request');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Cancel Return';
        });
    });
});
</script>
@endpush
