@extends('layouts.admin')

@section('title', 'Country Managers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h2 class="mb-0">Country Managers Management</h2>
                    @include('layouts.partials.module-shortcuts', [
                        'createRoute' => route('admin.country-managers.create'),
                        'tableBodyId' => 'countryManagersTableBody',
                        'checkboxClass' => 'country-managers-checkbox'
                    ])
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="delete-selected-country-managers-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteCountryManagers()">
                        <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-country-managers-count">0</span>)
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
                        <form method="GET" action="{{ route('admin.country-managers.index') }}" class="row g-3" id="country-managers-filter-form">
                            <div class="col-md-3">
                                <label for="search_field" class="form-label">Search By</label>
                                <select class="form-select" id="search_field" name="search_field">
                                    <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
                                    <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="code" {{ request('search_field') == 'code' ? 'selected' : '' }}>Code</option>
                                    <option value="mobile" {{ request('search_field') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                    <option value="email" {{ request('search_field') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="status" {{ request('search_field') == 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="country-managers-search" name="search" value="{{ request('search') }}" 
                                           placeholder="Type to search..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" id="country-managers-table-wrapper" style="position: relative;">
                    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <table class="table align-middle mb-0" id="country-managers-table">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-country-managers">
                                        <label class="form-check-label" for="select-all-country-managers">
                                            <span class="visually-hidden">Select All</span>
                                        </label>
                                    </div>
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Mobile</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="countryManagersTableBody">
                            @forelse($countryManagers as $countryManager)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input country-managers-checkbox" type="checkbox" value="{{ $countryManager->id }}" id="country-manager-{{ $countryManager->id }}">
                                            <label class="form-check-label" for="country-manager-{{ $countryManager->id }}">
                                                <span class="visually-hidden">Select country manager</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ ($countryManagers->currentPage() - 1) * $countryManagers->perPage() + $loop->iteration }}</td>
                                    <td>{{ $countryManager->name }}</td>
                                    <td>{{ $countryManager->code ?: '-' }}</td>
                                    <td>{{ $countryManager->mobile ?: '-' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewCountryManagerDetails({{ $countryManager->id }})" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.country-managers.edit', $countryManager) }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.country-managers.destroy', $countryManager) }}" method="POST" class="d-inline ajax-delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                                                    data-delete-url="{{ route('admin.country-managers.destroy', $countryManager) }}"
                                                    data-delete-message="Delete this country manager?" title="Delete">
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
                        Showing {{ $countryManagers->firstItem() ?? 0 }}-{{ $countryManagers->lastItem() ?? 0 }} of {{ $countryManagers->total() }}
                        <small class="text-muted">(Page {{ $countryManagers->currentPage() }} of {{ $countryManagers->lastPage() }})</small>
                    </div>
                    @if($countryManagers->hasMorePages())
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div id="country-managers-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span id="country-managers-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
                        </div>
                        <div id="country-managers-sentinel" data-next-url="{{ $countryManagers->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Country Manager Details Modal -->
<div id="countryManagerDetailsModal" class="country-manager-modal">
  <div class="country-manager-modal-content">
    <div class="country-manager-modal-header">
      <h5 class="country-manager-modal-title">
        <i class="bi bi-globe me-2"></i>Country Manager Details
      </h5>
      <button type="button" class="btn-close-modal" onclick="closeCountryManagerModal()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="country-manager-modal-body" id="countryManagerModalBody">
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading details...</div>
      </div>
    </div>
  </div>
</div>

<div id="countryManagerModalBackdrop" class="country-manager-modal-backdrop"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('country-managers-search');
    const searchField = document.getElementById('search_field');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchLoading = document.getElementById('search-loading');
    const tableBody = document.getElementById('countryManagersTableBody');
    const form = document.getElementById('country-managers-filter-form');

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

        fetch(`{{ route('admin.country-managers.index') }}?${params.toString()}`, {
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
            const tempTbody = tempDiv.querySelector('#countryManagersTableBody');

            if (!tempTbody) {
                console.error('Could not find countryManagersTableBody in response');
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
            if (typeof window.reattachCountryManagersEventListeners === 'function') {
                window.reattachCountryManagersEventListeners();
                window.updateCountryManagersSelectedCount && window.updateCountryManagersSelectedCount();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Search failed. Please try again.</td></tr>';
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
        const sentinel = document.getElementById('country-managers-sentinel');
        const spinner = document.getElementById('country-managers-spinner');
        const loadText = document.getElementById('country-managers-load-text');

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

        const spinner = document.getElementById('country-managers-spinner');
        const loadText = document.getElementById('country-managers-load-text');

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
            const newRows = tempDiv.querySelectorAll('#countryManagersTableBody tr');
            
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
                if (typeof window.reattachCountryManagersEventListeners === 'function') {
                    window.reattachCountryManagersEventListeners();
                    window.updateCountryManagersSelectedCount && window.updateCountryManagersSelectedCount();
                }
            } else {
                // No more pages, remove sentinel
                const currentSentinel = document.getElementById('country-managers-sentinel');
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
    window.performCountryManagersSearch = performSearch;

    // Multiple delete selection management for country managers
    let countryManagersPageElements = {
        selectAllCheckbox: document.getElementById('select-all-country-managers'),
        deleteSelectedBtn: document.getElementById('delete-selected-country-managers-btn'),
        selectedCountSpan: document.getElementById('selected-country-managers-count')
    };

    window.updateCountryManagersSelectedCount = function() {
        const checkedBoxes = document.querySelectorAll('.country-managers-checkbox:checked');
        const count = checkedBoxes.length;
        if (countryManagersPageElements.selectedCountSpan) {
            countryManagersPageElements.selectedCountSpan.textContent = count;
        }
        if (countryManagersPageElements.deleteSelectedBtn) {
            if (count > 0) countryManagersPageElements.deleteSelectedBtn.classList.remove('d-none');
            else countryManagersPageElements.deleteSelectedBtn.classList.add('d-none');
        }
        if (countryManagersPageElements.selectAllCheckbox) {
            const allBoxes = document.querySelectorAll('.country-managers-checkbox');
            if (count === 0) {
                countryManagersPageElements.selectAllCheckbox.indeterminate = false;
                countryManagersPageElements.selectAllCheckbox.checked = false;
            } else if (count === allBoxes.length) {
                countryManagersPageElements.selectAllCheckbox.indeterminate = false;
                countryManagersPageElements.selectAllCheckbox.checked = true;
            } else {
                countryManagersPageElements.selectAllCheckbox.indeterminate = true;
                countryManagersPageElements.selectAllCheckbox.checked = false;
            }
        }
    };

    window.reattachCountryManagersEventListeners = function() {
        setTimeout(() => {
            document.querySelectorAll('.country-managers-checkbox').forEach(cb => {
                cb.removeEventListener('change', window.updateCountryManagersSelectedCount);
                cb.addEventListener('change', function(){
                    window.updateCountryManagersSelectedCount();
                });
            });
            const selectAll = document.getElementById('select-all-country-managers');
            if (selectAll) {
                const newSelectAll = selectAll.cloneNode(true);
                selectAll.parentNode.replaceChild(newSelectAll, selectAll);
                newSelectAll.addEventListener('change', function(){
                    const boxes = document.querySelectorAll('.country-managers-checkbox');
                    boxes.forEach(b => b.checked = this.checked);
                    window.updateCountryManagersSelectedCount();
                });
                countryManagersPageElements.selectAllCheckbox = newSelectAll;
            }
        }, 50);
    };

    // Initial attach
    window.reattachCountryManagersEventListeners();
    window.updateCountryManagersSelectedCount();
});

// Confirm multiple delete for Country Managers
function confirmMultipleDeleteCountryManagers() {
    const checked = document.querySelectorAll('.country-managers-checkbox:checked');
    if (checked.length === 0) return;

    const selectedItems = [];
    checked.forEach(cb => {
        const row = cb.closest('tr');
        const name = row.querySelector('td:nth-child(3)').textContent.trim();
        selectedItems.push({ id: cb.value, name: name });
    });

    window.GlobalMultipleDelete.show({
        selectedItems: selectedItems,
        deleteUrl: '{{ route('admin.country-managers.multiple-delete') }}',
        itemType: 'country managers',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        onSuccess: function(){ window.performCountryManagersSearch(); },
        onError: function(err){
            console.error('Error deleting country managers:', err);
            if (window.crudNotification) {
                crudNotification.showToast('error', 'Error', 'Failed to delete selected country managers. Please try again.');
            }
        }
    });
}

// Country Manager Modal Functions
function viewCountryManagerDetails(id) {
  const modal = document.getElementById('countryManagerDetailsModal');
  const backdrop = document.getElementById('countryManagerModalBackdrop');
  const modalBody = document.getElementById('countryManagerModalBody');

  // Show modal
  modal.style.display = 'block';
  backdrop.style.display = 'block';
  
  setTimeout(() => {
    modal.classList.add('show');
    backdrop.classList.add('show');
  }, 10);

  // Reset modal body to loading state
  modalBody.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="mt-2">Loading details...</div>
    </div>
  `;

  // Fetch country manager details
  const url = `{{ url('/admin/country-managers') }}/${id}`;
  fetch(url, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Failed to fetch country manager details');
    }
    return response.json();
  })
  .then(data => {
    displayCountryManagerDetails(data);
  })
  .catch(error => {
    console.error('Error:', error);
    showErrorInModal('Failed to load country manager details. Please try again.');
  });
}

function displayCountryManagerDetails(data) {
  const modalBody = document.getElementById('countryManagerModalBody');
  
  let html = '<div class="row g-3">';

  // Basic Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-person me-2"></i>Basic Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Name:</small>
              <div class="fw-semibold">${data.name || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Code:</small>
              <div class="fw-semibold">${data.code || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Contact Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white py-2">
          <h6 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Mobile:</small>
              <div class="fw-semibold">${data.mobile || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Telephone:</small>
              <div class="fw-semibold">${data.telephone || '-'}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">Email:</small>
              <div class="fw-semibold">${data.email || '-'}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">Address:</small>
              <div class="fw-semibold">${data.address || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Status Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white py-2">
          <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-12">
              <small class="text-muted">Status:</small>
              <div class="fw-semibold">${data.status || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Timestamps Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-secondary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Timestamps</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Created At:</small>
              <div class="fw-semibold">${data.created_at || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Last Updated:</small>
              <div class="fw-semibold">${data.updated_at || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  html += '</div>';
  modalBody.innerHTML = html;
}

function showErrorInModal(message) {
  const modalBody = document.getElementById('countryManagerModalBody');
  modalBody.innerHTML = `
    <div class="text-center py-4">
      <div class="text-danger mb-3">
        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
      </div>
      <h6 class="text-danger">Error</h6>
      <p class="text-muted">${message}</p>
      <button class="btn btn-outline-secondary btn-sm" onclick="closeCountryManagerModal()">Close</button>
    </div>
  `;
}

function closeCountryManagerModal() {
  const modal = document.getElementById('countryManagerDetailsModal');
  const backdrop = document.getElementById('countryManagerModalBackdrop');

  modal.classList.remove('show');
  backdrop.classList.remove('show');

  setTimeout(() => {
    modal.style.display = 'none';
    backdrop.style.display = 'none';
  }, 300);
}

// Close modal when clicking backdrop
document.addEventListener('click', function (e) {
  if (e.target && e.target.id === 'countryManagerModalBackdrop') {
    closeCountryManagerModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('countryManagerDetailsModal');
    if (modal && modal.classList.contains('show')) {
      closeCountryManagerModal();
    }
  }
});
</script>

<style>
/* Slide-in Modal Styles */
.country-manager-modal {
  display: none;
  position: fixed;
  top: 70px;
  right: 0;
  width: 450px;
  height: calc(100vh - 100px);
  max-height: calc(100vh - 140px);
  z-index: 999999 !important;
  transform: translateX(100%);
  transition: transform 0.3s ease-in-out;
}

.country-manager-modal.show {
  transform: translateX(0);
}

.country-manager-modal-content {
  background: white;
  height: 100%;
  box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
}

.country-manager-modal-header {
  padding: 1rem 1.25rem;
  border-bottom: 2px solid #dee2e6;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.country-manager-modal-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #ffffff;
}

.btn-close-modal {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
}

.btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.country-manager-modal-body {
  padding: 1rem;
  overflow-y: auto;
  flex: 1;
  background: #f8f9fa;
}

.country-manager-modal-backdrop {
  display: none;
  position: fixed;
  top: 0;
  left: 260px; /* Start after sidebar width */
  width: calc(100vw - 260px); /* Exclude sidebar width */
  height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
  z-index: 999998 !important;
  opacity: 0;
  transition: all 0.3s ease;
}

.country-manager-modal-backdrop.show {
  opacity: 0.7;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .country-manager-modal {
    width: 100%;
  }
  
  .country-manager-modal-backdrop {
    left: 0; /* Full width on mobile */
    width: 100vw;
  }
}

@media (max-width: 576px) {
  .country-manager-modal-body {
    padding: 0.75rem;
  }

  .country-manager-modal-header {
    padding: 0.75rem 1rem;
  }
}

/* Card styling in modal */
.country-manager-modal .card {
  margin-bottom: 1rem;
  border-radius: 0.5rem;
  overflow: hidden;
}

.country-manager-modal .card:last-child {
  margin-bottom: 0;
}

.country-manager-modal .card-header {
  font-size: 0.9rem;
  padding: 0.75rem 1rem;
  font-weight: 600;
}

.country-manager-modal .card-body {
  padding: 1rem;
  background: white;
}

.country-manager-modal .card-body small {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.country-manager-modal .fw-semibold {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

/* Smooth scrollbar for modal */
.country-manager-modal-body::-webkit-scrollbar {
  width: 8px;
}

.country-manager-modal-body::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.country-manager-modal-body::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.country-manager-modal-body::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
@endsection
