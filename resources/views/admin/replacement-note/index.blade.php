@extends('layouts.admin')
@section('title', 'Replacement Note Invoices')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-repeat me-2"></i> Replacement Notes</h4>
        <div class="text-muted small">Manage replacement note transactions</div>
    </div>
    <div>
        <a href="{{ route('admin.replacement-note.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
        <a href="{{ route('admin.replacement-note.modification') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-square me-1"></i> Modification
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Card -->
    <div class="card mb-0 border-0">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.replacement-note.index') }}" class="row g-3" id="rn-filter-form">
                <div class="col-md-2">
                    <label for="search_field" class="form-label small mb-1">Search By</label>
                    <select class="form-select form-select-sm" id="search_field" name="filter_by">
                        <option value="rn_no" {{ request('filter_by', 'rn_no') == 'rn_no' ? 'selected' : '' }}>RN No.</option>
                        <option value="supplier_name" {{ request('filter_by') == 'supplier_name' ? 'selected' : '' }}>Supplier Name</option>
                        <option value="net_amount" {{ request('filter_by') == 'net_amount' ? 'selected' : '' }}>Amount</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label small mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="rn-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Search</button>
                    <a href="{{ route('admin.replacement-note.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive" id="rn-table-wrapper" style="position: relative; min-height: 300px;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th style="width: 100px;">RN No.</th>
                    <th style="width: 110px;">Date</th>
                    <th>Supplier</th>
                    <th style="width: 120px;" class="text-end">Net Amount</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 160px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="rn-table-body">
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                    <td><span class="badge bg-primary">{{ $transaction->rn_no }}</span></td>
                    <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d-M-Y') : '-' }}</td>
                    <td>{{ $transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? '-') }}</td>
                    <td class="text-end fw-semibold">₹{{ number_format($transaction->net_amount ?? 0, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($transaction->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.replacement-note.show', $transaction->id) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.replacement-note.modification') }}?id={{ $transaction->id }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                                data-url="{{ route('admin.replacement-note.destroy', $transaction->id) }}"
                                data-name="RN No: {{ $transaction->rn_no }}"
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No replacement notes found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer with Pagination -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</div>
            <div class="text-muted">Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</div>
        </div>
        @if($transactions->hasMorePages())
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div id="rn-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span id="rn-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
        </div>
        <div id="rn-sentinel" data-next-url="{{ $transactions->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
let rnPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Perform search via AJAX
window.performRnSearch = function() {
    if(isSearching) return;
    isSearching = true;
    
    if (!rnPageElements.filterForm) {
        rnPageElements.filterForm = document.getElementById('rn-filter-form');
    }
    if (!rnPageElements.tbody) {
        rnPageElements.tbody = document.getElementById('rn-table-body');
    }
    if (!rnPageElements.searchInput) {
        rnPageElements.searchInput = document.getElementById('rn-search');
    }
    
    const formData = new FormData(rnPageElements.filterForm);
    const params = new URLSearchParams(formData);
    
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) loadingSpinner.style.display = 'flex';
    
    if(rnPageElements.searchInput) rnPageElements.searchInput.style.opacity = '0.6';
    
    fetch(`{{ route('admin.replacement-note.index') }}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newRows = doc.querySelectorAll('#rn-table-body tr');
        const realRows = Array.from(newRows).filter(tr => {
            const tds = tr.querySelectorAll('td');
            return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });
        
        rnPageElements.tbody.innerHTML = '';
        if(realRows.length) {
            realRows.forEach(tr => rnPageElements.tbody.appendChild(tr));
        } else {
            rnPageElements.tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No replacement notes found</td></tr>';
        }
        
        const newFooter = doc.querySelector('.card-footer');
        const currentFooter = document.querySelector('.card-footer');
        if(newFooter && currentFooter) {
            currentFooter.innerHTML = newFooter.innerHTML;
            initRnInfiniteScroll();
        }
        
        // Reattach delete handlers
        reattachDeleteHandlers();
    })
    .catch(error => {
        console.error('Search error:', error);
        if (rnPageElements.tbody) {
            rnPageElements.tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
        }
    })
    .finally(() => {
        isSearching = false;
        if(loadingSpinner) loadingSpinner.style.display = 'none';
        if(rnPageElements.searchInput) rnPageElements.searchInput.style.opacity = '1';
    });
};

// Infinite scroll
function initRnInfiniteScroll() {
    if(observer) observer.disconnect();
    
    const sentinel = document.getElementById('rn-sentinel');
    const spinner = document.getElementById('rn-spinner');
    const loadText = document.getElementById('rn-load-text');
    
    if (!rnPageElements.tbody) rnPageElements.tbody = document.getElementById('rn-table-body');
    if (!rnPageElements.filterForm) rnPageElements.filterForm = document.getElementById('rn-filter-form');
    
    if(!sentinel || !rnPageElements.tbody) return;
    
    isLoading = false;
    
    async function loadMore() {
        if(isLoading) return;
        const nextUrl = sentinel.getAttribute('data-next-url');
        if(!nextUrl) return;
        
        isLoading = true;
        spinner && spinner.classList.remove('d-none');
        loadText && (loadText.textContent = 'Loading...');
        
        try {
            const formData = new FormData(rnPageElements.filterForm);
            const params = new URLSearchParams(formData);
            const url = new URL(nextUrl, window.location.origin);
            
            params.forEach((value, key) => {
                if(value) url.searchParams.set(key, value);
            });
            
            const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#rn-table-body tr');
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            realRows.forEach(tr => rnPageElements.tbody.appendChild(tr));
            
            // Reattach delete handlers
            reattachDeleteHandlers();
            
            const newSentinel = doc.querySelector('#rn-sentinel');
            if(newSentinel) {
                sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                spinner && spinner.classList.add('d-none');
                loadText && (loadText.textContent = 'Scroll for more');
                isLoading = false;
            } else {
                observer.disconnect();
                sentinel.remove();
                spinner && spinner.remove();
                loadText && loadText.remove();
            }
        } catch(e) {
            spinner && spinner.classList.add('d-none');
            loadText && (loadText.textContent = 'Error loading');
            isLoading = false;
        }
    }
    
    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting && !isLoading) loadMore();
        });
    }, { rootMargin: '100px' });
    
    observer.observe(sentinel);
}

// Reattach delete handlers after AJAX load
function reattachDeleteHandlers() {
    document.querySelectorAll('.ajax-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const name = this.dataset.name;
            
            if(confirm(`Are you sure you want to delete ${name}?`)) {
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        performRnSearch();
                        if(window.crudNotification) {
                            crudNotification.showToast('success', 'Deleted', data.message || 'Deleted successfully');
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Delete failed'));
                    }
                })
                .catch(error => alert('Error deleting'));
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    rnPageElements = {
        tbody: document.getElementById('rn-table-body'),
        searchInput: document.getElementById('rn-search'),
        searchFieldSelect: document.getElementById('search_field'),
        clearSearchBtn: document.getElementById('clear-search'),
        filterForm: document.getElementById('rn-filter-form')
    };
    
    // Search with debounce
    if(rnPageElements.searchInput) {
        rnPageElements.searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performRnSearch, 300);
        });
    }
    
    // Clear search
    if(rnPageElements.clearSearchBtn) {
        rnPageElements.clearSearchBtn.addEventListener('click', function() {
            if(rnPageElements.searchInput) {
                rnPageElements.searchInput.value = '';
                rnPageElements.searchInput.focus();
                performRnSearch();
            }
        });
    }
    
    // Search field change
    if(rnPageElements.searchFieldSelect) {
        rnPageElements.searchFieldSelect.addEventListener('change', performRnSearch);
    }
    
    // Date filter change
    document.querySelectorAll('input[name="date_from"], input[name="date_to"]').forEach(el => {
        el.addEventListener('change', performRnSearch);
    });
    
    // Initialize infinite scroll
    initRnInfiniteScroll();
    
    // Attach delete handlers
    reattachDeleteHandlers();
});
</script>
@endpush
