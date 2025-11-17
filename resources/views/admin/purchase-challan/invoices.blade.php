@extends('layouts.admin')

@section('title', 'Purchase Challan Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-receipt me-2"></i> Purchase Challan List</h4>
        <div class="text-muted small">View and manage all purchase challans</div>
    </div>
    <div>
        <a href="{{ route('admin.purchase-challan.transaction') }}" class="btn btn-warning">
            <i class="bi bi-plus-circle me-1"></i> New Challan
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.purchase-challan.invoices') }}" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label for="filter_by" class="form-label">Filter By</label>
                    <select class="form-select" id="filter_by" name="filter_by">
                        <option value="supplier_name" {{ request('filter_by', 'supplier_name') == 'supplier_name' ? 'selected' : '' }}>Supplier Name</option>
                        <option value="challan_no" {{ request('filter_by') == 'challan_no' ? 'selected' : '' }}>Challan No.</option>
                        <option value="supplier_invoice_no" {{ request('filter_by') == 'supplier_invoice_no' ? 'selected' : '' }}>Supplier Invoice No.</option>
                        <option value="challan_amount" {{ request('filter_by') == 'challan_amount' ? 'selected' : '' }}>Challan Amount</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Enter search term..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label for="invoiced_status" class="form-label">Status</label>
                    <select class="form-select" id="invoiced_status" name="invoiced_status">
                        <option value="">All Status</option>
                        <option value="no" {{ request('invoiced_status') == 'no' ? 'selected' : '' }}>Pending</option>
                        <option value="yes" {{ request('invoiced_status') == 'yes' ? 'selected' : '' }}>Invoiced</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100" title="Clear All Filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive" id="challan-table-wrapper" style="position: relative; min-height: 400px; max-height: 600px; overflow-y: auto;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th>#</th>
                    <th>Challan Date</th>
                    <th>Challan No.</th>
                    <th>Supplier Name</th>
                    <th>Supplier Invoice No.</th>
                    <th class="text-end">Net Amount</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="challan-table-body">
                @forelse($challans ?? [] as $challan)
                    <tr>
                        <td>{{ ($challans->currentPage() - 1) * $challans->perPage() + $loop->iteration }}</td>
                        <td>{{ $challan->challan_date ? $challan->challan_date->format('d/m/Y') : '-' }}</td>
                        <td><strong>{{ $challan->challan_no ?? '-' }}</strong></td>
                        <td>{{ $challan->supplier->name ?? '-' }}</td>
                        <td>{{ $challan->supplier_invoice_no ?? '-' }}</td>
                        <td class="text-end">
                            <span class="badge bg-success">₹{{ number_format($challan->net_amount ?? 0, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @if($challan->is_invoiced)
                                <span class="badge bg-success">Invoiced</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.purchase-challan.show', $challan->id) }}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$challan->is_invoiced)
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.purchase-challan.modification') }}?challan_no={{ $challan->challan_no }}" title="Edit Challan">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-challan" 
                                    data-challan-id="{{ $challan->id }}" 
                                    data-challan-no="{{ $challan->challan_no }}" 
                                    data-supplier="{{ $challan->supplier->name ?? 'Unknown' }}"
                                    title="Delete Challan">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No challans found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Infinite Scroll Footer -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing {{ $challans->firstItem() ?? 0 }}-{{ $challans->lastItem() ?? 0 }} of {{ $challans->total() ?? 0 }}</div>
            <div class="text-muted">Page {{ $challans->currentPage() }} of {{ $challans->lastPage() }}</div>
        </div>
        @if($challans->hasMorePages())
            <div class="d-flex align-items-center justify-content-center gap-2">
                <div id="challan-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span id="challan-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
            </div>
            <div id="challan-sentinel" data-next-url="{{ $challans->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteChallanModal" tabindex="-1" aria-labelledby="deleteChallanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteChallanModalLabel"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this purchase challan?</p>
                <div class="alert alert-warning">
                    <strong>Challan No:</strong> <span id="delete-challan-no"></span><br>
                    <strong>Supplier:</strong> <span id="delete-supplier"></span><br>
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Stock will be restored after deletion.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete Challan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const filterSelect = document.getElementById('filter_by');
    const searchInput = document.getElementById('search');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const invoicedStatusSelect = document.getElementById('invoiced_status');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('challan-table-body');
    const tableWrapper = document.getElementById('challan-table-wrapper');
    const overlay = document.getElementById('search-loading');
    
    let observer = null;
    let isLoading = false;
    
    function updatePlaceholder() {
        const filterValue = filterSelect.value;
        const placeholders = {
            'supplier_name': 'Enter supplier name...',
            'challan_no': 'Enter challan number...',
            'supplier_invoice_no': 'Enter supplier invoice no...',
            'challan_amount': 'Enter minimum amount...'
        };
        
        searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
        
        if (filterValue === 'challan_amount') {
            searchInput.type = 'number';
            searchInput.step = '0.01';
        } else {
            searchInput.type = 'text';
            searchInput.removeAttribute('step');
        }
    }
    
    filterSelect.addEventListener('change', updatePlaceholder);
    updatePlaceholder();

    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    function setLoading(loading) {
        if (!overlay) return;
        overlay.style.display = loading ? 'flex' : 'none';
    }

    function getFormParams() {
        return new URLSearchParams(new FormData(filterForm));
    }

    async function fetchChallans(urlOrParams, pushState = true) {
        try {
            setLoading(true);
            let url;
            if (typeof urlOrParams === 'string') {
                url = new URL(urlOrParams, window.location.origin);
            } else {
                url = new URL(filterForm.getAttribute('action'), window.location.origin);
                url.search = urlOrParams.toString();
            }

            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('#challan-table-body');
            const newFooter = doc.querySelector('.card-footer');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            if (newFooter) {
                const currentFooter = document.querySelector('.card-footer');
                if (currentFooter) {
                    currentFooter.innerHTML = newFooter.innerHTML;
                }
            }
            
            if (tableWrapper) {
                tableWrapper.scrollTo({ top: 0, behavior: 'smooth' });
            }

            initInfiniteScroll();
            
            if (pushState) {
                window.history.pushState({}, '', url.toString());
            }
        } catch (e) {
            console.error(e);
            alert('Failed to load challans.');
        } finally {
            setLoading(false);
        }
    }

    const debouncedSearch = debounce(() => fetchChallans(getFormParams()), 300);
    searchInput.addEventListener('input', debouncedSearch);
    filterSelect.addEventListener('change', () => {
        updatePlaceholder();
        fetchChallans(getFormParams());
    });
    dateFromInput && dateFromInput.addEventListener('change', () => fetchChallans(getFormParams()));
    dateToInput && dateToInput.addEventListener('change', () => fetchChallans(getFormParams()));
    invoicedStatusSelect && invoicedStatusSelect.addEventListener('change', () => fetchChallans(getFormParams()));
    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        fetchChallans(getFormParams());
    });

    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){
            if (filterSelect) filterSelect.value = 'supplier_name';
            if (searchInput) searchInput.value = '';
            if (dateFromInput) dateFromInput.value = '';
            if (dateToInput) dateToInput.value = '';
            if (invoicedStatusSelect) invoicedStatusSelect.value = '';
            updatePlaceholder();
            fetchChallans(new URLSearchParams());
        });
    }

    // Delete functionality
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.delete-challan');
        if (!button) return;

        const challanId = button.getAttribute('data-challan-id');
        const challanNo = button.getAttribute('data-challan-no');
        const supplier = button.getAttribute('data-supplier');
        
        document.getElementById('delete-challan-no').textContent = challanNo;
        document.getElementById('delete-supplier').textContent = supplier;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteChallanModal'));
        modal.show();
        
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.onclick = function() {
            deleteChallan(challanId, modal);
        };
    });

    function deleteChallan(challanId, modal) {
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(`{{ url('admin/purchase-challan') }}/${challanId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                modal.hide();
                fetchChallans(window.location.href, false);
            } else {
                alert('Error deleting challan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error deleting challan');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Delete Challan';
        });
    }

    // Infinite scroll functionality
    function initInfiniteScroll() {
        // Disconnect previous observer if exists
        if (observer) {
            observer.disconnect();
        }

        const sentinel = document.getElementById('challan-sentinel');
        const spinner = document.getElementById('challan-spinner');
        const loadText = document.getElementById('challan-load-text');

        if (!sentinel || !tableBody) return;

        isLoading = false;

        async function loadMore() {
            if (isLoading) return;
            const nextUrl = sentinel.getAttribute('data-next-url');
            if (!nextUrl) return;

            isLoading = true;
            spinner && spinner.classList.remove('d-none');
            loadText && (loadText.textContent = 'Loading...');

            try {
                // Merge current filter params with next page URL
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const url = new URL(nextUrl, window.location.origin);
                params.forEach((value, key) => {
                    if (value) url.searchParams.set(key, value);
                });

                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#challan-table-body tr');

                const realRows = Array.from(newRows).filter(tr => {
                    const tds = tr.querySelectorAll('td');
                    return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                });

                realRows.forEach(tr => tableBody.appendChild(tr));

                // Update footer info
                const newFooter = doc.querySelector('.card-footer');
                if (newFooter) {
                    const currentFooter = document.querySelector('.card-footer');
                    if (currentFooter) {
                        currentFooter.innerHTML = newFooter.innerHTML;
                    }
                }

                const newSentinel = doc.querySelector('#challan-sentinel');
                if (newSentinel) {
                    sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                    spinner && spinner.classList.add('d-none');
                    loadText && (loadText.textContent = 'Scroll for more');
                    isLoading = false;
                } else {
                    observer.disconnect();
                    sentinel.remove();
                    spinner && spinner.remove();
                    loadText && (loadText.textContent = 'All records loaded');
                }
            } catch (e) {
                console.error(e);
                spinner && spinner.classList.add('d-none');
                loadText && (loadText.textContent = 'Error loading');
                isLoading = false;
            }
        }

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading) {
                    loadMore();
                }
            });
        }, { rootMargin: '100px' });

        observer.observe(sentinel);
    }

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function() {
        fetchChallans(window.location.href, false);
    });

    // Initialize on page load
    initInfiniteScroll();
});
</script>
@endpush
