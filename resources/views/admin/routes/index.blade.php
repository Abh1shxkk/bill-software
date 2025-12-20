@extends('layouts.admin')

@section('title', 'Routes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h2 class="mb-0">Routes Management</h2>
                    @include('layouts.partials.module-shortcuts', [
                        'createRoute' => route('admin.routes.create'),
                        'tableBodyId' => 'routesTableBody',
                        'checkboxClass' => 'routes-checkbox'
                    ])
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="delete-selected-routes-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteRoutes()">
                        <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-routes-count">0</span>)
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
                        <form method="GET" action="{{ route('admin.routes.index') }}" class="row g-3" id="routes-filter-form">
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
                                    <input type="text" class="form-control" id="routes-search" name="search" value="{{ request('search') }}" 
                                           placeholder="Type to search..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" id="routes-table-wrapper" style="position: relative;">
                    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <table class="table align-middle mb-0" id="routes-table">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-routes">
                                        <label class="form-check-label" for="select-all-routes">
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
                        <tbody id="routesTableBody">
                            @forelse($routes as $route)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input routes-checkbox" type="checkbox" value="{{ $route->id }}" id="routes-{{ $route->id }}">
                                            <label class="form-check-label" for="routes-{{ $route->id }}">
                                                <span class="visually-hidden">Select route</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ ($routes->currentPage() - 1) * $routes->perPage() + $loop->iteration }}</td>
                                    <td>{{ $route->name }}</td>
                                    <td>{{ $route->alter_code ?: '-' }}</td>
                                    <td>{{ $route->status ?: '-' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.routes.edit', $route) }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline ajax-delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                                                    data-delete-url="{{ route('admin.routes.destroy', $route) }}"
                                                    data-delete-message="Delete this route?" title="Delete">
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
                        Showing {{ $routes->firstItem() ?? 0 }}-{{ $routes->lastItem() ?? 0 }} of {{ $routes->total() }}
                        <small class="text-muted">(Page {{ $routes->currentPage() }} of {{ $routes->lastPage() }})</small>
                    </div>
                    @if($routes->hasMorePages())
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div id="routes-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span id="routes-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
                        </div>
                        <div id="routes-sentinel" data-next-url="{{ $routes->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('routes-search');
    const searchField = document.getElementById('search_field');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchLoading = document.getElementById('search-loading');
    const tableBody = document.getElementById('routesTableBody');
    const form = document.getElementById('routes-filter-form');

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

        fetch(`{{ route('admin.routes.index') }}?${params.toString()}`, {
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
            const tempTbody = tempDiv.querySelector('#routesTableBody');

            if (!tempTbody) {
                console.error('Could not find routesTableBody in response');
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
            if (typeof window.reattachRoutesEventListeners === 'function') {
                window.reattachRoutesEventListeners();
                window.updateRoutesSelectedCount && window.updateRoutesSelectedCount();
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
        const sentinel = document.getElementById('routes-sentinel');
        const spinner = document.getElementById('routes-spinner');
        const loadText = document.getElementById('routes-load-text');

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

        const spinner = document.getElementById('routes-spinner');
        const loadText = document.getElementById('routes-load-text');

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
            const newRows = tempDiv.querySelectorAll('#routesTableBody tr');
            
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
                if (typeof window.reattachRoutesEventListeners === 'function') {
                    window.reattachRoutesEventListeners();
                    window.updateRoutesSelectedCount && window.updateRoutesSelectedCount();
                }
            } else {
                // No more pages, remove sentinel
                const currentSentinel = document.getElementById('routes-sentinel');
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

    // Expose perform function globally for modal callbacks
    window.performRoutesSearch = performSearch;

    // Multiple delete selection management for routes
    let routesPageElements = {
        selectAllCheckbox: document.getElementById('select-all-routes'),
        deleteSelectedBtn: document.getElementById('delete-selected-routes-btn'),
        selectedCountSpan: document.getElementById('selected-routes-count')
    };

    window.updateRoutesSelectedCount = function() {
        const checkedBoxes = document.querySelectorAll('.routes-checkbox:checked');
        const count = checkedBoxes.length;
        if (routesPageElements.selectedCountSpan) {
            routesPageElements.selectedCountSpan.textContent = count;
        }
        if (routesPageElements.deleteSelectedBtn) {
            if (count > 0) routesPageElements.deleteSelectedBtn.classList.remove('d-none');
            else routesPageElements.deleteSelectedBtn.classList.add('d-none');
        }
        if (routesPageElements.selectAllCheckbox) {
            const allBoxes = document.querySelectorAll('.routes-checkbox');
            if (count === 0) {
                routesPageElements.selectAllCheckbox.indeterminate = false;
                routesPageElements.selectAllCheckbox.checked = false;
            } else if (count === allBoxes.length) {
                routesPageElements.selectAllCheckbox.indeterminate = false;
                routesPageElements.selectAllCheckbox.checked = true;
            } else {
                routesPageElements.selectAllCheckbox.indeterminate = true;
                routesPageElements.selectAllCheckbox.checked = false;
            }
        }
    };

    window.reattachRoutesEventListeners = function() {
        setTimeout(() => {
            document.querySelectorAll('.routes-checkbox').forEach(cb => {
                cb.removeEventListener('change', window.updateRoutesSelectedCount);
                cb.addEventListener('change', function(){
                    window.updateRoutesSelectedCount();
                });
            });
            const selectAll = document.getElementById('select-all-routes');
            if (selectAll) {
                const newSelectAll = selectAll.cloneNode(true);
                selectAll.parentNode.replaceChild(newSelectAll, selectAll);
                newSelectAll.addEventListener('change', function(){
                    const boxes = document.querySelectorAll('.routes-checkbox');
                    boxes.forEach(b => b.checked = this.checked);
                    window.updateRoutesSelectedCount();
                });
                routesPageElements.selectAllCheckbox = newSelectAll;
            }
        }, 50);
    };

    // Initial attach
    window.reattachRoutesEventListeners();
    window.updateRoutesSelectedCount();
    
    // Debug: Check if global delete modal exists
    setTimeout(() => {
        const globalModal = document.getElementById('globalDeleteModal');
        const globalConfirm = document.getElementById('globalDeleteConfirm');
        console.log('Global delete modal exists:', !!globalModal);
        console.log('Global delete confirm button exists:', !!globalConfirm);
    }, 1000);
});

// Confirm multiple delete for Routes
function confirmMultipleDeleteRoutes() {
    const checked = document.querySelectorAll('.routes-checkbox:checked');
    if (checked.length === 0) return;

    const selectedItems = [];
    checked.forEach(cb => {
        const row = cb.closest('tr');
        const name = row.querySelector('td:nth-child(3)').textContent.trim(); // Name column after checkbox + #
        selectedItems.push({ id: cb.value, name: name });
    });

    window.GlobalMultipleDelete.show({
        selectedItems: selectedItems,
        deleteUrl: '{{ route('admin.routes.multiple-delete') }}',
        itemType: 'routes',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        onSuccess: function(){ window.performRoutesSearch(); },
        onError: function(err){
            console.error('Error deleting routes:', err);
            if (window.crudNotification) {
                crudNotification.showToast('error', 'Error', 'Failed to delete selected routes. Please try again.');
            }
        }
    });
}
</script>
@endsection
