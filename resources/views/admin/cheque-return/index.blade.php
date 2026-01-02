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
                        <button type="button" class="btn btn-sm btn-outline-danger btn-return-single" 
                                data-id="{{ $cheque['id'] }}" 
                                data-cheque-no="{{ $cheque['cheque_no'] }}"
                                data-customer="{{ $cheque['customer_name'] ?? '-' }}"
                                data-amount="{{ number_format($cheque['amount'], 2) }}"
                                title="Mark as Returned Unpaid"
                                {{ $cheque['status'] !== 'pending' ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-return-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning btn-cancel-single" 
                                data-id="{{ $cheque['cheque_return_id'] }}" 
                                data-cheque-no="{{ $cheque['cheque_no'] }}"
                                data-customer="{{ $cheque['customer_name'] ?? '-' }}"
                                data-amount="{{ number_format($cheque['amount'], 2) }}"
                                title="Cancel Return"
                                {{ $cheque['status'] !== 'returned' ? 'disabled' : '' }}>
                            <i class="bi bi-x-circle"></i>
                        </button>
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

<style>
/* Custom Modal Styles */
.custom-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
}
.custom-modal-backdrop.show { display: block; }

.custom-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 450px;
    background: #f0f0f0;
    border: 1px solid #999;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    z-index: 10001;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.custom-modal.show { display: block; }

.custom-modal-header {
    padding: 10px 15px;
    background: linear-gradient(to bottom, #f8f8f8, #e8e8e8);
    border-bottom: 1px solid #ccc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.custom-modal-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: #333;
}
.custom-modal-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #666;
    padding: 0;
    line-height: 1;
}
.custom-modal-close:hover { color: #c00; }

.custom-modal-body {
    padding: 20px;
    background: #f5f5f5;
}

.custom-modal-info {
    background: #fffbcc;
    border: 1px solid #e6d98c;
    padding: 12px 15px;
    margin-bottom: 15px;
    font-size: 13px;
}
.custom-modal-info strong { color: #333; }

.custom-modal-form-row {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}
.custom-modal-form-row label {
    width: 120px;
    font-weight: 600;
    font-size: 13px;
    color: #333;
}
.custom-modal-form-row input,
.custom-modal-form-row select {
    flex: 1;
    padding: 5px 8px;
    border: 1px solid #999;
    font-size: 13px;
    background: white;
}

.custom-modal-footer {
    padding: 12px 15px;
    background: #e8e8e8;
    border-top: 1px solid #ccc;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.custom-modal-btn {
    padding: 6px 20px;
    font-size: 13px;
    border: 1px solid #666;
    cursor: pointer;
    background: linear-gradient(to bottom, #f8f8f8, #e0e0e0);
}
.custom-modal-btn:hover { background: linear-gradient(to bottom, #e8e8e8, #d0d0d0); }
.custom-modal-btn-primary {
    background: linear-gradient(to bottom, #4a9cd8, #2980b9);
    color: white;
    border-color: #2573a7;
}
.custom-modal-btn-primary:hover { background: linear-gradient(to bottom, #3a8cc8, #1970a9); }
.custom-modal-btn-danger {
    background: linear-gradient(to bottom, #e74c3c, #c0392b);
    color: white;
    border-color: #a93226;
}
.custom-modal-btn-danger:hover { background: linear-gradient(to bottom, #d74c3c, #b0392b); }
</style>

<!-- Return Modal Backdrop -->
<div class="custom-modal-backdrop" id="returnModalBackdrop" onclick="closeReturnModal()"></div>

<!-- Return Cheque Modal -->
<div class="custom-modal" id="returnModal">
    <div class="custom-modal-header">
        <h5 class="custom-modal-title">Confirm Return</h5>
        <button type="button" class="custom-modal-close" onclick="closeReturnModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <p style="margin-bottom: 15px; font-size: 13px;">Are you sure you want to mark this cheque as returned unpaid?</p>
        
        <div class="custom-modal-info">
            <strong>Cheque No:</strong> <span id="return-cheque-no"></span><br>
            <strong>Customer:</strong> <span id="return-customer"></span><br>
            <strong>Amount:</strong> <span id="return-amount"></span>
        </div>
        
        <div class="custom-modal-form-row">
            <label>Return Date :</label>
            <input type="date" id="returnDate" value="{{ date('Y-m-d') }}">
        </div>
        
        <div class="custom-modal-form-row">
            <label>Bank Charges :</label>
            <input type="number" id="bankCharges" step="0.01" value="0.00" min="0">
        </div>
    </div>
    <div class="custom-modal-footer">
        <button type="button" class="custom-modal-btn" onclick="closeReturnModal()">Cancel</button>
        <button type="button" class="custom-modal-btn custom-modal-btn-danger" id="confirm-return">
            <i class="bi bi-arrow-return-left me-1"></i> Mark as Returned
        </button>
    </div>
</div>

<!-- Cancel Return Modal Backdrop -->
<div class="custom-modal-backdrop" id="cancelModalBackdrop" onclick="closeCancelModal()"></div>

<!-- Cancel Return Modal -->
<div class="custom-modal" id="cancelModal">
    <div class="custom-modal-header">
        <h5 class="custom-modal-title">Cancel Return</h5>
        <button type="button" class="custom-modal-close" onclick="closeCancelModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <div class="custom-modal-info">
            <strong>Cheque No:</strong> <span id="cancel-cheque-no"></span><br>
            <strong>Customer:</strong> <span id="cancel-customer"></span><br>
            <strong>Amount:</strong> <span id="cancel-amount"></span>
        </div>
        
        <div style="background: #ffdddd; border: 1px solid #e6a0a0; padding: 12px; margin-bottom: 15px; font-size: 12px;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Warning!</strong> Cancelling return will reverse all restored adjustments - the balance amounts will be deducted again from sale invoices.
        </div>
        
        <p style="text-align: center; font-size: 13px; margin: 0;">Are you sure you want to cancel this return?</p>
    </div>
    <div class="custom-modal-footer">
        <button type="button" class="custom-modal-btn" onclick="closeCancelModal()">No</button>
        <button type="button" class="custom-modal-btn custom-modal-btn-primary" id="confirm-cancel">
            <i class="bi bi-check-circle me-1"></i> Yes, Cancel Return
        </button>
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

    // Modal functions
    window.openReturnModal = function() {
        document.getElementById('returnModalBackdrop').classList.add('show');
        document.getElementById('returnModal').classList.add('show');
    };
    
    window.closeReturnModal = function() {
        document.getElementById('returnModalBackdrop').classList.remove('show');
        document.getElementById('returnModal').classList.remove('show');
    };
    
    window.openCancelModal = function() {
        document.getElementById('cancelModalBackdrop').classList.add('show');
        document.getElementById('cancelModal').classList.add('show');
    };
    
    window.closeCancelModal = function() {
        document.getElementById('cancelModalBackdrop').classList.remove('show');
        document.getElementById('cancelModal').classList.remove('show');
    };

    document.addEventListener('click', function(e) {
        // Return button
        const returnBtn = e.target.closest('.btn-return-single');
        if (returnBtn) {
            currentReturnId = returnBtn.getAttribute('data-id');
            document.getElementById('return-cheque-no').textContent = returnBtn.getAttribute('data-cheque-no');
            document.getElementById('return-customer').textContent = returnBtn.getAttribute('data-customer');
            document.getElementById('return-amount').textContent = '₹ ' + (returnBtn.getAttribute('data-amount') || '0.00');
            
            // Reset form fields
            document.getElementById('returnDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('bankCharges').value = '0.00';
            
            openReturnModal();
        }

        // Cancel return button
        const cancelBtn = e.target.closest('.btn-cancel-single');
        if (cancelBtn) {
            currentCancelId = cancelBtn.getAttribute('data-id');
            document.getElementById('cancel-cheque-no').textContent = cancelBtn.getAttribute('data-cheque-no');
            document.getElementById('cancel-customer').textContent = cancelBtn.getAttribute('data-customer');
            document.getElementById('cancel-amount').textContent = '₹ ' + (cancelBtn.getAttribute('data-amount') || '0.00');
            
            openCancelModal();
        }
    });
    
    // ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReturnModal();
            closeCancelModal();
        }
    });

    // Confirm return
    document.getElementById('confirm-return').addEventListener('click', function() {
        const btn = this;
        const returnDate = document.getElementById('returnDate').value;
        const bankCharges = document.getElementById('bankCharges').value;
        
        if (!returnDate) {
            alert('Please select a return date');
            return;
        }
        
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
                customer_receipt_item_id: currentReturnId,
                return_date: returnDate,
                bank_charges: bankCharges
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
