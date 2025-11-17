@extends('layouts.admin')
@section('title', 'HSN Codes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-upc-scan me-2"></i> HSN Codes Master</h4>
        <div class="text-muted small">Manage your HSN codes list</div>
    </div>
    <div>
        <button type="button" id="delete-selected-hsn-codes-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteHsnCodes()">
            <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-hsn-codes-count">0</span>)
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.hsn-codes.index') }}" class="row g-3" id="hsn-filter-form">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="hsn-search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, HSN code..." autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="page-jump" class="form-label">Jump to Page</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="page-jump" min="1" max="{{ $hsnCodes->lastPage() }}" 
                               placeholder="Page #" autocomplete="off">
                        <button type="button" class="btn btn-primary" id="page-jump-btn" title="Go to page">
                            <i class="bi bi-arrow-right-circle"></i>
                        </button>
                    </div>
                    <small class="text-muted">Total: {{ $hsnCodes->lastPage() }} pages</small>
                </div>
            </form>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-hsn-codes">
                            <label class="form-check-label" for="select-all-hsn-codes">
                                <span class="visually-hidden">Select All</span>
                            </label>
                        </div>
                    </th>
                    <th>#</th>
                    <th>Name</th>
                    <th>HSN Code</th>
                    <th>CGST %</th>
                    <th>SGST %</th>
                    <th>IGST %</th>
                    <th>Total GST %</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="hsn-table-body">
                @forelse($hsnCodes as $hsnCode)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input hsn-codes-checkbox" type="checkbox" value="{{ $hsnCode->id }}" id="hsn-codes-{{ $hsnCode->id }}">
                                <label class="form-check-label" for="hsn-codes-{{ $hsnCode->id }}">
                                    <span class="visually-hidden">Select HSN code</span>
                                </label>
                            </div>
                        </td>
                        <td>{{ ($hsnCodes->currentPage() - 1) * $hsnCodes->perPage() + $loop->iteration }}</td>
                        <td>
                            <strong>{{ $hsnCode->name }}</strong>
                            @if($hsnCode->is_service)
                                <span class="badge bg-info ms-1">Service</span>
                            @endif
                        </td>
                        <td>{{ $hsnCode->hsn_code ?? '-' }}</td>
                        <td>{{ number_format($hsnCode->cgst_percent, 2) }}%</td>
                        <td>{{ number_format($hsnCode->sgst_percent, 2) }}%</td>
                        <td>{{ number_format($hsnCode->igst_percent, 2) }}%</td>
                        <td><strong>{{ number_format($hsnCode->total_gst_percent, 2) }}%</strong></td>
                        <td>
                            <span class="badge {{ $hsnCode->is_inactive ? 'bg-danger' : 'bg-success' }}">
                                {{ $hsnCode->is_inactive ? 'Inactive' : 'Active' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.hsn-codes.edit', $hsnCode) }}" 
                               title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.hsn-codes.destroy', $hsnCode) }}" method="POST" class="d-inline ajax-delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                                        data-delete-url="{{ route('admin.hsn-codes.destroy', $hsnCode) }}" 
                                        data-delete-message="Delete this HSN code?" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No HSN codes found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing {{ $hsnCodes->firstItem() ?? 0 }}-{{ $hsnCodes->lastItem() ?? 0 }} of {{ $hsnCodes->total() }}</div>
            <div class="text-muted">Page {{ $hsnCodes->currentPage() }} of {{ $hsnCodes->lastPage() }}</div>
        </div>
        @if($hsnCodes->hasMorePages())
            <div class="d-flex align-items-center justify-content-center gap-2">
                <div id="hsn-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span id="hsn-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
            </div>
            <div id="hsn-sentinel" data-next-url="{{ $hsnCodes->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const tbody = document.getElementById('hsn-table-body');
    const searchInput = document.getElementById('hsn-search');
    const statusSelect = document.getElementById('status');
    const filterForm = document.getElementById('hsn-filter-form');
    
    let searchTimeout;
    let isLoading = false;
    let observer = null;

    // Real-time search implementation
    function performSearch() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        const footerSpinner = document.getElementById('hsn-spinner');
        const footerLoadText = document.getElementById('hsn-load-text');
        let spinnerTimer = setTimeout(() => {
            footerSpinner && footerSpinner.classList.remove('d-none');
            footerLoadText && (footerLoadText.textContent = 'Loading...');
        }, 250);
        
        fetch(`{{ route('admin.hsn-codes.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#hsn-table-body tr');
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            
            // Clear and update table
            tbody.innerHTML = '';
            if(realRows.length) {
                realRows.forEach(tr => tbody.appendChild(tr));
                // Reattach events after infinite append
                if (typeof window.reattachHsnCodesEventListeners === 'function') {
                    window.reattachHsnCodesEventListeners();
                    window.updateHsnCodesSelectedCount && window.updateHsnCodesSelectedCount();
                }
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No HSN codes found</td></tr>';
            }
            
            // Update pagination info and reinitialize infinite scroll
            const newFooter = doc.querySelector('.card-footer');
            const currentFooter = document.querySelector('.card-footer');
            if(newFooter && currentFooter) {
                currentFooter.innerHTML = newFooter.innerHTML;
                initInfiniteScroll();
            }

            // Reattach events after search refresh
            if (typeof window.reattachHsnCodesEventListeners === 'function') {
                window.reattachHsnCodesEventListeners();
                window.updateHsnCodesSelectedCount && window.updateHsnCodesSelectedCount();
            }
        })
        .catch(error => {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>';
        })
        .finally(() => {
            typeof spinnerTimer !== 'undefined' && clearTimeout(spinnerTimer);
            const s = document.getElementById('hsn-spinner');
            const t = document.getElementById('hsn-load-text');
            s && s.classList.add('d-none');
            t && (t.textContent = 'Scroll for more');
        });
    }

    // Search input with debounce
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }

    // Status filter real-time
    if(statusSelect) {
        statusSelect.addEventListener('change', performSearch);
    }

    // Toast notification helper
    function showToast(message, type = 'danger') {
        const toastContainer = document.getElementById('ajaxToastContainer');
        if (!toastContainer) return;
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        toastContainer.appendChild(toastEl);
        const bToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bToast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    // Page jump functionality
    function jumpToPage() {
        const pageJumpInput = document.getElementById('page-jump');
        const pageJumpBtn = document.getElementById('page-jump-btn');
        
        if(!pageJumpInput || !pageJumpBtn) return;
        
        const pageNum = parseInt(pageJumpInput.value);
        const maxPage = parseInt(pageJumpInput.getAttribute('max'));
        
        if(!pageNum || pageNum < 1) {
            showToast('Please enter a valid page number', 'warning');
            return;
        }
        
        if(pageNum > maxPage) {
            showToast(`Page number cannot exceed ${maxPage}`, 'warning');
            return;
        }
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.set('page', pageNum);
        
        pageJumpBtn.disabled = true;
        pageJumpBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch(`{{ route('admin.hsn-codes.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#hsn-table-body tr');
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            
            tbody.innerHTML = '';
            if(realRows.length) {
                realRows.forEach(tr => tbody.appendChild(tr));
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No HSN codes found</td></tr>';
            }
            
            const newFooter = doc.querySelector('.card-footer');
            const currentFooter = document.querySelector('.card-footer');
            if(newFooter && currentFooter) {
                currentFooter.innerHTML = newFooter.innerHTML;
                initInfiniteScroll();
                initPageJump();
            }
            
            const contentDiv = document.querySelector('.content');
            if(contentDiv) {
                contentDiv.scrollTo({ top: 0, behavior: 'smooth' });
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            showToast('Error loading page. Please try again.', 'danger');
            console.error(error);
            const btn = document.getElementById('page-jump-btn');
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-right-circle"></i>';
            }
        })
        .finally(() => {
            const btn = document.getElementById('page-jump-btn');
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-right-circle"></i>';
            }
        });
    }
    
    function initPageJump() {
        const jumpInput = document.getElementById('page-jump');
        const jumpBtn = document.getElementById('page-jump-btn');
        
        if(jumpBtn) {
            const newBtn = jumpBtn.cloneNode(true);
            jumpBtn.parentNode.replaceChild(newBtn, jumpBtn);
            newBtn.addEventListener('click', jumpToPage);
        }
        
        if(jumpInput) {
            const newInput = jumpInput.cloneNode(true);
            jumpInput.parentNode.replaceChild(newInput, jumpInput);
            newInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') {
                    e.preventDefault();
                    jumpToPage();
                }
            });
        }
    }
    
    initPageJump();

    // Infinite scroll functionality
    function initInfiniteScroll() {
        if(observer) {
            observer.disconnect();
        }

        const sentinel = document.getElementById('hsn-sentinel');
        const spinner = document.getElementById('hsn-spinner');
        const loadText = document.getElementById('hsn-load-text');
        
        if(!sentinel || !tbody) return;
        
        isLoading = false;
        
        async function loadMore(){
            if(isLoading) return;
            const nextUrl = sentinel.getAttribute('data-next-url');
            if(!nextUrl) return;
            
            isLoading = true;
            spinner && spinner.classList.remove('d-none');
            loadText && (loadText.textContent = 'Loading...');
            
            try{
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const url = new URL(nextUrl, window.location.origin);
                
                params.forEach((value, key) => {
                    if(value) url.searchParams.set(key, value);
                });
                
                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#hsn-table-body tr');
                const realRows = Array.from(newRows).filter(tr => {
                    const tds = tr.querySelectorAll('td');
                    return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                });
                realRows.forEach(tr => tbody.appendChild(tr));
                
                const newSentinel = doc.querySelector('#hsn-sentinel');
                if(newSentinel){
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
            }catch(e){
                spinner && spinner.classList.add('d-none');
                loadText && (loadText.textContent = 'Error loading');
                isLoading = false;
            }
        }
        
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting && !isLoading){
                    loadMore();
                }
            });
        }, { rootMargin: '100px' });
        
        observer.observe(sentinel);
    }

    initInfiniteScroll();

    // Make performSearch globally accessible for modal callbacks
    window.performHsnCodesSearch = performSearch;

    // Multiple delete functionality for HSN codes
    let hsnCodesPageElements = {
        selectAllCheckbox: document.getElementById('select-all-hsn-codes'),
        deleteSelectedBtn: document.getElementById('delete-selected-hsn-codes-btn'),
        selectedCountSpan: document.getElementById('selected-hsn-codes-count')
    };

    // Global function to update selected count for HSN codes
    window.updateHsnCodesSelectedCount = function() {
        const checkedBoxes = document.querySelectorAll('.hsn-codes-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (hsnCodesPageElements.selectedCountSpan) {
            hsnCodesPageElements.selectedCountSpan.textContent = count;
        }
        
        if (hsnCodesPageElements.deleteSelectedBtn) {
            if (count > 0) {
                hsnCodesPageElements.deleteSelectedBtn.classList.remove('d-none');
            } else {
                hsnCodesPageElements.deleteSelectedBtn.classList.add('d-none');
            }
        }
        
        // Update select all checkbox state
        if (hsnCodesPageElements.selectAllCheckbox) {
            const allCheckboxes = document.querySelectorAll('.hsn-codes-checkbox');
            if (count === 0) {
                hsnCodesPageElements.selectAllCheckbox.indeterminate = false;
                hsnCodesPageElements.selectAllCheckbox.checked = false;
            } else if (count === allCheckboxes.length) {
                hsnCodesPageElements.selectAllCheckbox.indeterminate = false;
                hsnCodesPageElements.selectAllCheckbox.checked = true;
            } else {
                hsnCodesPageElements.selectAllCheckbox.indeterminate = true;
                hsnCodesPageElements.selectAllCheckbox.checked = false;
            }
        }
    };

    // Global function to reattach event listeners for HSN codes
    window.reattachHsnCodesEventListeners = function() {
        setTimeout(() => {
            // Reattach individual checkbox change listeners
            document.querySelectorAll('.hsn-codes-checkbox').forEach(checkbox => {
                checkbox.removeEventListener('change', window.updateHsnCodesSelectedCount);
                checkbox.addEventListener('change', function() {
                    window.updateHsnCodesSelectedCount();
                });
            });
            
            // Handle select-all checkbox
            const selectAllCheckbox = document.getElementById('select-all-hsn-codes');
            if (selectAllCheckbox) {
                const newSelectAll = selectAllCheckbox.cloneNode(true);
                selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
                
                newSelectAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.hsn-codes-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    window.updateHsnCodesSelectedCount();
                });
                
                hsnCodesPageElements.selectAllCheckbox = newSelectAll;
            }
        }, 50);
    };

    // Initial attachment of event listeners
    window.reattachHsnCodesEventListeners();
    window.updateHsnCodesSelectedCount();
});

// Multiple delete confirmation function for HSN codes (global scope for onclick)
function confirmMultipleDeleteHsnCodes() {
    const checkedBoxes = document.querySelectorAll('.hsn-codes-checkbox:checked');
    if (checkedBoxes.length === 0) {
        return;
    }

    const selectedItems = [];
    checkedBoxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const hsnName = row.querySelector('td:nth-child(3) strong').textContent.trim(); // Name column
        selectedItems.push({
            id: checkbox.value,
            name: hsnName
        });
    });

    window.GlobalMultipleDelete.show({
        selectedItems: selectedItems,
        deleteUrl: '{{ route('admin.hsn-codes.multiple-delete') }}',
        itemType: 'hsn-codes',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        onSuccess: function(data) {
            window.performHsnCodesSearch();
        },
        onError: function(error) {
            console.error('Error deleting HSN codes:', error);
            if (window.crudNotification) {
                crudNotification.showToast('error', 'Error', 'Failed to delete selected HSN codes. Please try again.');
            }
        }
    });
}
</script>
@endpush
