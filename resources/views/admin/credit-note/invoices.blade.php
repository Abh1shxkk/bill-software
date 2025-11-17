@extends('layouts.admin')

@section('title', 'Credit Note Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-minus me-2"></i> Credit Note List</h4>
        <div class="text-muted small">View and manage all credit notes</div>
    </div>
    <div>
        <a href="{{ route('admin.credit-note.transaction') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Credit Note
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.credit-note.invoices') }}" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label for="filter_by" class="form-label">Filter By</label>
                    <select class="form-select" id="filter_by" name="filter_by">
                        <option value="party_name" {{ request('filter_by', 'party_name') == 'party_name' ? 'selected' : '' }}>Party Name</option>
                        <option value="credit_note_no" {{ request('filter_by') == 'credit_note_no' ? 'selected' : '' }}>CN No.</option>
                        <option value="amount" {{ request('filter_by') == 'amount' ? 'selected' : '' }}>Amount</option>
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
                    <label for="party_type" class="form-label">Party Type</label>
                    <select class="form-select" id="party_type" name="party_type">
                        <option value="">All</option>
                        <option value="S" {{ request('party_type') == 'S' ? 'selected' : '' }}>Supplier</option>
                        <option value="C" {{ request('party_type') == 'C' ? 'selected' : '' }}>Customer</option>
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
    <div class="table-responsive" id="cn-table-wrapper" style="position: relative; min-height: 400px; max-height: 600px; overflow-y: auto;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>CN No.</th>
                    <th>Party Type</th>
                    <th>Party Name</th>
                    <th class="text-end">CN Amount</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="cn-table-body">
                @forelse($creditNotes ?? [] as $cn)
                    <tr>
                        <td>{{ ($creditNotes->currentPage() - 1) * $creditNotes->perPage() + $loop->iteration }}</td>
                        <td>{{ $cn->credit_note_date ? $cn->credit_note_date->format('d/m/Y') : '-' }}</td>
                        <td><strong>{{ $cn->credit_note_no ?? '-' }}</strong></td>
                        <td>
                            @if($cn->credit_party_type == 'S')
                                <span class="badge bg-info">Supplier</span>
                            @else
                                <span class="badge bg-warning text-dark">Customer</span>
                            @endif
                        </td>
                        <td>{{ $cn->credit_party_name ?? '-' }}</td>
                        <td class="text-end">
                            <span class="badge bg-success">₹{{ number_format($cn->cn_amount ?? 0, 2) }}</span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.credit-note.show', $cn->id) }}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.credit-note.modification') }}?credit_note_no={{ $cn->credit_note_no }}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-cn" 
                                    data-cn-id="{{ $cn->id }}" 
                                    data-cn-no="{{ $cn->credit_note_no }}" 
                                    data-party="{{ $cn->credit_party_name ?? 'Unknown' }}"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No credit notes found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Infinite Scroll Footer -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing {{ $creditNotes->firstItem() ?? 0 }}-{{ $creditNotes->lastItem() ?? 0 }} of {{ $creditNotes->total() ?? 0 }}</div>
            <div class="text-muted">Page {{ $creditNotes->currentPage() }} of {{ $creditNotes->lastPage() }}</div>
        </div>
        @if($creditNotes->hasMorePages())
            <div class="d-flex align-items-center justify-content-center gap-2">
                <div id="cn-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span id="cn-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
            </div>
            <div id="cn-sentinel" data-next-url="{{ $creditNotes->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCnModal" tabindex="-1" aria-labelledby="deleteCnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCnModalLabel"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this credit note?</p>
                <div class="alert alert-warning">
                    <strong>CN No:</strong> <span id="delete-cn-no"></span><br>
                    <strong>Party:</strong> <span id="delete-party"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
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
    const partyTypeSelect = document.getElementById('party_type');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('cn-table-body');
    const tableWrapper = document.getElementById('cn-table-wrapper');
    const overlay = document.getElementById('search-loading');
    
    let observer = null;
    let isLoading = false;
    
    function updatePlaceholder() {
        const filterValue = filterSelect.value;
        const placeholders = {
            'party_name': 'Enter party name...',
            'credit_note_no': 'Enter CN number...',
            'inv_ref_no': 'Enter invoice ref no...',
            'amount': 'Enter minimum amount...'
        };
        
        searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
        
        if (filterValue === 'amount') {
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

    async function fetchCreditNotes(urlOrParams, pushState = true) {
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
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('#cn-table-body');
            const newFooter = doc.querySelector('.card-footer');
            
            if (newTableBody) tableBody.innerHTML = newTableBody.innerHTML;
            if (newFooter) {
                const currentFooter = document.querySelector('.card-footer');
                if (currentFooter) currentFooter.innerHTML = newFooter.innerHTML;
            }
            
            if (tableWrapper) tableWrapper.scrollTo({ top: 0, behavior: 'smooth' });
            initInfiniteScroll();
            
            if (pushState) window.history.pushState({}, '', url.toString());
        } catch (e) {
            console.error(e);
            alert('Failed to load credit notes.');
        } finally {
            setLoading(false);
        }
    }

    const debouncedSearch = debounce(() => fetchCreditNotes(getFormParams()), 300);
    searchInput.addEventListener('input', debouncedSearch);
    filterSelect.addEventListener('change', () => { updatePlaceholder(); fetchCreditNotes(getFormParams()); });
    dateFromInput && dateFromInput.addEventListener('change', () => fetchCreditNotes(getFormParams()));
    dateToInput && dateToInput.addEventListener('change', () => fetchCreditNotes(getFormParams()));
    partyTypeSelect && partyTypeSelect.addEventListener('change', () => fetchCreditNotes(getFormParams()));
    filterForm.addEventListener('submit', (e) => { e.preventDefault(); fetchCreditNotes(getFormParams()); });

    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){
            if (filterSelect) filterSelect.value = 'party_name';
            if (searchInput) searchInput.value = '';
            if (dateFromInput) dateFromInput.value = '';
            if (dateToInput) dateToInput.value = '';
            if (partyTypeSelect) partyTypeSelect.value = '';
            updatePlaceholder();
            fetchCreditNotes(new URLSearchParams());
        });
    }

    // Delete functionality
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.delete-cn');
        if (!button) return;

        const cnId = button.getAttribute('data-cn-id');
        const cnNo = button.getAttribute('data-cn-no');
        const party = button.getAttribute('data-party');
        
        document.getElementById('delete-cn-no').textContent = cnNo;
        document.getElementById('delete-party').textContent = party;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCnModal'));
        modal.show();
        
        document.getElementById('confirm-delete').onclick = function() {
            deleteCreditNote(cnId, modal);
        };
    });

    function deleteCreditNote(cnId, modal) {
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(`{{ url('admin/credit-note') }}/${cnId}`, {
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
                fetchCreditNotes(window.location.href, false);
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => alert('Error deleting credit note'))
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Delete';
        });
    }

    // Infinite scroll
    function initInfiniteScroll() {
        if (observer) observer.disconnect();

        const sentinel = document.getElementById('cn-sentinel');
        const spinner = document.getElementById('cn-spinner');
        const loadText = document.getElementById('cn-load-text');

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
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const url = new URL(nextUrl, window.location.origin);
                params.forEach((value, key) => { if (value) url.searchParams.set(key, value); });

                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#cn-table-body tr');

                const realRows = Array.from(newRows).filter(tr => {
                    const tds = tr.querySelectorAll('td');
                    return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                });

                realRows.forEach(tr => tableBody.appendChild(tr));

                const newFooter = doc.querySelector('.card-footer');
                if (newFooter) {
                    const currentFooter = document.querySelector('.card-footer');
                    if (currentFooter) currentFooter.innerHTML = newFooter.innerHTML;
                }

                const newSentinel = doc.querySelector('#cn-sentinel');
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
            entries.forEach(entry => { if (entry.isIntersecting && !isLoading) loadMore(); });
        }, { rootMargin: '100px' });

        observer.observe(sentinel);
    }

    window.addEventListener('popstate', function() { fetchCreditNotes(window.location.href, false); });
    initInfiniteScroll();
});
</script>
@endpush
