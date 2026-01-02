@extends('layouts.admin')

@section('title', 'States')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h2 class="mb-0">States Management</h2>
                    @include('layouts.partials.module-shortcuts', [
                        'createRoute' => route('admin.states.create'),
                        'tableBodyId' => 'statesTableBody',
                        'checkboxClass' => 'states-checkbox'
                    ])
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="delete-selected-states-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteStates()">
                        <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-states-count">0</span>)
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.states.index') }}" class="row g-3" id="states-filter-form">
                            <div class="col-md-3">
                                <label for="search_field" class="form-label">Search By</label>
                                <select class="form-select" id="search_field" name="search_field">
                                    <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
                                    <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="alter_code" {{ request('search_field') == 'alter_code' ? 'selected' : '' }}>Alter Code</option>
                                    <option value="status" {{ request('search_field') == 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="states-search" name="search" value="{{ request('search') }}" 
                                           placeholder="Type to search..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" id="states-table-wrapper" style="position: relative;">
                    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <table class="table align-middle mb-0" id="states-table">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-states">
                                        <label class="form-check-label" for="select-all-states">
                                            <span class="visually-hidden">Select All</span>
                                        </label>
                                    </div>
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Alter Code</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="statesTableBody">
                            @forelse($states as $state)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input states-checkbox" type="checkbox" value="{{ $state->id }}" id="states-{{ $state->id }}">
                                            <label class="form-check-label" for="states-{{ $state->id }}">
                                                <span class="visually-hidden">Select state</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ ($states->currentPage() - 1) * $states->perPage() + $loop->iteration }}</td>
                                    <td>{{ $state->name }}</td>
                                    <td>{{ $state->alter_code ?: '-' }}</td>
                                    <td>{{ $state->status ?: '-' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.states.edit', $state) }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.states.destroy', $state) }}" method="POST" class="d-inline ajax-delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                                                    data-delete-url="{{ route('admin.states.destroy', $state) }}"
                                                    data-delete-message="Delete this state?" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-light d-flex flex-column gap-2">
                    <div class="align-self-start">
                        Showing {{ $states->firstItem() ?? 0 }}-{{ $states->lastItem() ?? 0 }} of {{ $states->total() }}
                        <small class="text-muted">(Page {{ $states->currentPage() }} of {{ $states->lastPage() }})</small>
                    </div>
                    @if($states->hasMorePages())
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div id="states-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span id="states-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
                        </div>
                        <div id="states-sentinel" data-next-url="{{ $states->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('states-search');
    const searchField = document.getElementById('search_field');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchLoading = document.getElementById('search-loading');
    const tableBody = document.getElementById('statesTableBody');
    const form = document.getElementById('states-filter-form');

    let isLoading = false;
    let isSearching = false;
    let observer = null;

    // Real-time search implementation
    function performSearch() {
        if (isSearching) return;
        isSearching = true;

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Show loading overlay
        if (searchLoading) {
            searchLoading.style.display = 'flex';
        }

        // Add visual feedback to search input
        if (searchInput) {
            searchInput.style.opacity = '0.6';
        }

        fetch(`{{ route('admin.states.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary container to parse HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Find the tbody in the temporary container
            const tempTbody = tempDiv.querySelector('#statesTableBody');

            if (!tempTbody) {
                console.error('Could not find statesTableBody in response');
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error: Table not found in response</td></tr>';
                return;
            }

            // Clear tbody FIRST before adding anything
            tableBody.innerHTML = '';

            // Get all rows from the temporary tbody
            const tempRows = tempTbody.querySelectorAll('tr');

            // Add all rows (including "no data" message if any)
            if (tempRows.length > 0) {
                tempRows.forEach(tr => {
                    tableBody.appendChild(tr.cloneNode(true));
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No data</td></tr>';
            }

            // Update footer with pagination info
            const newFooter = tempDiv.querySelector('.card-footer');
            const currentFooter = document.querySelector('.card-footer');
            if (newFooter && currentFooter) {
                currentFooter.innerHTML = newFooter.innerHTML;
            }

            // Reset infinite scroll observer completely
            isLoading = false;
            if (observer) {
                observer.disconnect();
                observer = null;
            }

            // Reinitialize infinite scroll after DOM update
            setTimeout(() => {
                initInfiniteScroll();
            }, 50);

            // Reattach events after search refresh
            if (typeof window.reattachStatesEventListeners === 'function') {
                window.reattachStatesEventListeners();
                window.updateStatesSelectedCount && window.updateStatesSelectedCount();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Search failed. Please try again.</td></tr>';
        })
        .finally(() => {
            isSearching = false;
            
            // Hide loading spinner
            if (searchLoading) {
                searchLoading.style.display = 'none';
            }
            
            // Restore search input opacity
            if (searchInput) {
                searchInput.style.opacity = '1';
            }
        });
    }

    // Search input event listener with debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });
    }

    // Search field change event listener
    if (searchField) {
        searchField.addEventListener('change', function() {
            if (searchInput) {
                searchInput.value = '';
            }
            performSearch();
        });
    }

    // Clear search button
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchField) {
                searchField.value = 'all';
            }
            performSearch();
        });
    }

    // Infinite scroll implementation
    function initInfiniteScroll() {
        const sentinel = document.getElementById('states-sentinel');
        const spinner = document.getElementById('states-spinner');
        const loadText = document.getElementById('states-load-text');

        if (!sentinel || isLoading) return;

        observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading) {
                    const nextUrl = sentinel.getAttribute('data-next-url');
                    if (nextUrl && nextUrl !== 'null' && nextUrl !== '') {
                        loadMore(nextUrl);
                    }
                }
            });
        }, { rootMargin: '50px' });

        observer.observe(sentinel);
    }

    function loadMore(url) {
        if (isLoading) return;
        isLoading = true;

        const spinner = document.getElementById('states-spinner');
        const loadText = document.getElementById('states-load-text');

        if (spinner) spinner.classList.remove('d-none');
        if (loadText) loadText.textContent = 'Loading...';

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const urlWithParams = `${url}&${params.toString()}`;

        fetch(urlWithParams, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newRows = tempDiv.querySelectorAll('#statesTableBody tr');
            
            // Filter out empty message rows
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                const hasColspan = tr.querySelector('td[colspan]');
                return !(tds.length === 1 && hasColspan);
            });

            // Append new rows to existing table
            if (realRows.length > 0) {
                realRows.forEach(tr => {
                    tableBody.appendChild(tr.cloneNode(true));
                });
            }

            // Update footer pagination info
            const newFooter = tempDiv.querySelector('.card-footer');
            const currentFooter = document.querySelector('.card-footer');
            if (newFooter && currentFooter) {
                currentFooter.innerHTML = newFooter.innerHTML;
                
                // Reinitialize infinite scroll after footer update
                setTimeout(() => {
                    if (observer) {
                        observer.disconnect();
                        observer = null;
                    }
                    initInfiniteScroll();
                }, 100);
                
                // Reattach events after infinite append
                if (typeof window.reattachStatesEventListeners === 'function') {
                    window.reattachStatesEventListeners();
                    window.updateStatesSelectedCount && window.updateStatesSelectedCount();
                }
            } else {
                // No more pages, remove sentinel
                const currentSentinel = document.getElementById('states-sentinel');
                if (currentSentinel) {
                    currentSentinel.remove();
                }
            }
        })
        .catch(error => {
            console.error('Load more error:', error);
            if (loadText) loadText.textContent = 'Error loading more';
        })
        .finally(() => {
            isLoading = false;
            if (spinner) spinner.classList.add('d-none');
            if (loadText && loadText.textContent !== 'Error loading more') {
                loadText.textContent = 'Scroll for more';
            }
        });
    }

    // Initialize infinite scroll on page load
    initInfiniteScroll();

    // Note: Delete functionality is handled by global delete modal in admin layout
    
    // Debug: Add event listener to monitor delete clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('ajax-delete') || e.target.closest('.ajax-delete')) {
            console.log('Delete button clicked in states index');
            const button = e.target.classList.contains('ajax-delete') ? e.target : e.target.closest('.ajax-delete');
            console.log('Delete URL:', button.getAttribute('data-delete-url'));
            console.log('Delete Message:', button.getAttribute('data-delete-message'));
        }
    });
    
    // Debug: Check if global delete modal exists
    setTimeout(() => {
        const globalModal = document.getElementById('globalDeleteModal');
        const globalConfirm = document.getElementById('globalDeleteConfirm');
        console.log('Global delete modal exists:', !!globalModal);
        console.log('Global delete confirm button exists:', !!globalConfirm);
    }, 1000);

    // Expose perform function for modal success callback
    window.performStatesSearch = performSearch;

    // Multiple delete: elements
    let statesPageElements = {
        selectAllCheckbox: document.getElementById('select-all-states'),
        deleteSelectedBtn: document.getElementById('delete-selected-states-btn'),
        selectedCountSpan: document.getElementById('selected-states-count')
    };

    // Update selected count UI
    window.updateStatesSelectedCount = function() {
        const checked = document.querySelectorAll('.states-checkbox:checked');
        const count = checked.length;
        if (statesPageElements.selectedCountSpan) {
            statesPageElements.selectedCountSpan.textContent = count;
        }
        if (statesPageElements.deleteSelectedBtn) {
            if (count > 0) statesPageElements.deleteSelectedBtn.classList.remove('d-none');
            else statesPageElements.deleteSelectedBtn.classList.add('d-none');
        }
        if (statesPageElements.selectAllCheckbox) {
            const allBoxes = document.querySelectorAll('.states-checkbox');
            if (count === 0) {
                statesPageElements.selectAllCheckbox.indeterminate = false;
                statesPageElements.selectAllCheckbox.checked = false;
            } else if (count === allBoxes.length) {
                statesPageElements.selectAllCheckbox.indeterminate = false;
                statesPageElements.selectAllCheckbox.checked = true;
            } else {
                statesPageElements.selectAllCheckbox.indeterminate = true;
                statesPageElements.selectAllCheckbox.checked = false;
            }
        }
    };

    // Reattach listeners after DOM changes
    window.reattachStatesEventListeners = function() {
        setTimeout(() => {
            document.querySelectorAll('.states-checkbox').forEach(cb => {
                cb.removeEventListener('change', window.updateStatesSelectedCount);
                cb.addEventListener('change', function(){
                    window.updateStatesSelectedCount();
                });
            });
            const selectAll = document.getElementById('select-all-states');
            if (selectAll) {
                const newSelectAll = selectAll.cloneNode(true);
                selectAll.parentNode.replaceChild(newSelectAll, selectAll);
                newSelectAll.addEventListener('change', function(){
                    const boxes = document.querySelectorAll('.states-checkbox');
                    boxes.forEach(b => b.checked = this.checked);
                    window.updateStatesSelectedCount();
                });
                statesPageElements.selectAllCheckbox = newSelectAll;
            }
        }, 50);
    };

    // Initial attach
    window.reattachStatesEventListeners();
    window.updateStatesSelectedCount();
});

// Confirm multiple delete for States
function confirmMultipleDeleteStates() {
    const checked = document.querySelectorAll('.states-checkbox:checked');
    if (checked.length === 0) return;

    const selectedItems = [];
    checked.forEach(cb => {
        const row = cb.closest('tr');
        const name = row.querySelector('td:nth-child(3)').textContent.trim(); // after checkbox + #
        selectedItems.push({ id: cb.value, name: name });
    });

    window.GlobalMultipleDelete.show({
        selectedItems: selectedItems,
        deleteUrl: '{{ route('admin.states.multiple-delete') }}',
        itemType: 'states',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        onSuccess: function(){ window.performStatesSearch(); },
        onError: function(err){
            console.error('Error deleting states:', err);
            if (window.crudNotification) {
                crudNotification.showToast('error', 'Error', 'Failed to delete selected states. Please try again.');
            }
        }
    });
}
</script>
@endsection
