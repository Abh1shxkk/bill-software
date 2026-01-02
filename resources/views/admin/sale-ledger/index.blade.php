@extends('layouts.admin')
@section('title','Sale Ledger')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div>
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cart-check me-2"></i> Sale Ledger</h4>
      <div class="text-muted small">Manage sales transactions</div>
    </div>
    @include('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.sale-ledger.create'),
        'tableBodyId' => 'sale-ledger-table-body',
        'checkboxClass' => 'sale-ledger-checkbox'
    ])
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-sale-ledger-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteSaleLedger()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-sale-ledger-count">0</span>)
    </button>
  </div>
</div>


<div class="card shadow-sm">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.sale-ledger.index') }}" class="row g-3" id="sale-ledger-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="ledger_name" {{ request('search_field') == 'ledger_name' ? 'selected' : '' }}>Ledger Name</option>
            <option value="alter_code" {{ request('search_field') == 'alter_code' ? 'selected' : '' }}>Alter Code</option>
            <option value="type" {{ request('search_field') == 'type' ? 'selected' : '' }}>Type</option>
            <option value="status" {{ request('search_field') == 'status' ? 'selected' : '' }}>Status</option>
            <option value="contact_1" {{ request('search_field') == 'contact_1' ? 'selected' : '' }}>Contact</option>
            <option value="email" {{ request('search_field') == 'email' ? 'selected' : '' }}>Email</option>
            <option value="telephone" {{ request('search_field') == 'telephone' ? 'selected' : '' }}>Telephone</option>
          </select>
        </div>
        <div class="col-md-9">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="sale-ledger-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive" id="sale-ledger-table-wrapper" style="position: relative;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-sale-ledger">
              <label class="form-check-label" for="select-all-sale-ledger">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Ledger Name</th>
          <th>Type</th>
          <th>Opening Balance</th>
          <th>Form Required</th>
          <th>Contact</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="sale-ledger-table-body">
        @forelse($ledgers as $index => $ledger)
        <tr>
          <td>
            <div class="form-check">
              <input class="form-check-input sale-ledger-checkbox" type="checkbox" value="{{ $ledger->id }}" id="sale-ledger-{{ $ledger->id }}">
              <label class="form-check-label" for="sale-ledger-{{ $ledger->id }}">
                <span class="visually-hidden">Select ledger</span>
              </label>
            </div>
          </td>
          <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $loop->iteration }}</td>
          <td>
            <div>{{ $ledger->ledger_name ?? '-' }}</div>
          </td>
          <td><span class="badge bg-light text-dark">{{ $ledger->type ?? '-' }}</span></td>
          <td>₹{{ number_format($ledger->opening_balance ?? 0, 2) }}</td>
          <td>{{ $ledger->form_required ?? '-' }}</td>
          <td>
            <div>{{ $ledger->contact_1 ?? '-' }}</div>
          </td>
          <td class="text-end">
            <div class="d-flex gap-2 justify-content-end">
              <a href="{{ route('admin.sale-ledger.show', $ledger) }}" class="btn btn-sm btn-outline-info" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.sale-ledger.edit', $ledger) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.sale-ledger.destroy', $ledger) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.sale-ledger.destroy', $ledger) }}" data-delete-message="Delete this sale ledger entry?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No sale ledger entries found
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex flex-column gap-2">
    <div class="align-self-start">Showing {{ $ledgers->firstItem() ?? 0 }}-{{ $ledgers->lastItem() ?? 0 }} of {{ $ledgers->total() }}</div>
    @if($ledgers->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="sale-ledger-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="sale-ledger-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="sale-ledger-sentinel" data-next-url="{{ $ledgers->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const searchInput = document.getElementById('sale-ledger-search');
  const clearSearchBtn = document.getElementById('clear-search');
  const searchFieldSelect = document.getElementById('search_field');
  const filterForm = document.getElementById('sale-ledger-filter-form');
  const sentinel = document.getElementById('sale-ledger-sentinel');
  const spinner = document.getElementById('sale-ledger-spinner');
  const loadText = document.getElementById('sale-ledger-load-text');
  const tbody = document.getElementById('sale-ledger-table-body');
  let searchTimeout;
  let isSearching = false;

  function performSearch() {
    if(isSearching) return;
    isSearching = true;
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    
    // Show loading spinner
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }
    
    // Add visual feedback
    if(searchInput) {
      searchInput.style.opacity = '0.6';
    }
    
    fetch(`{{ route('admin.sale-ledger.index') }}?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      if(!response.ok) throw new Error('Network response was not ok');
      return response.text();
    })
    .then(html => {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const tempTbody = tempDiv.querySelector('#sale-ledger-table-body');
      
      if(!tempTbody) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
        return;
      }
      
      const tempRows = tempTbody.querySelectorAll('tr');
      
      const realRows = Array.from(tempRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });
      
      tbody.innerHTML = '';
      if(realRows.length) {
        realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));
      // Reattach events after infinite append
      if (typeof window.reattachSaleLedgerEventListeners === 'function') {
        window.reattachSaleLedgerEventListeners();
        window.updateSaleLedgerSelectedCount && window.updateSaleLedgerSelectedCount();
      }
      } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No sale ledger entries found</td></tr>';
      }
      
      // Update footer with pagination info and sentinel
      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if(newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }
      
      // Re-setup infinite scroll observer
      setupInfiniteScroll();

      // Reattach events after search refresh
      if (typeof window.reattachSaleLedgerEventListeners === 'function') {
        window.reattachSaleLedgerEventListeners();
        window.updateSaleLedgerSelectedCount && window.updateSaleLedgerSelectedCount();
      }
    })
    .catch(error => {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
    })
    .finally(() => {
      isSearching = false;
      
      // Hide loading spinner
      const loadingSpinner = document.getElementById('search-loading');
      if(loadingSpinner) {
        loadingSpinner.style.display = 'none';
      }
      
      if(searchInput) {
        searchInput.style.opacity = '1';
      }
      const s = document.getElementById('sale-ledger-spinner');
      const t = document.getElementById('sale-ledger-load-text');
      s && s.classList.add('d-none');
      t && (t.textContent = 'Scroll for more');
    });
  }

  // Attach debounced keyup on search input
  if(searchInput) {
    searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(performSearch, 300);
    });
  }

  // Clear search button
  if(clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if(searchInput) {
        searchInput.value = '';
        searchInput.focus();
        performSearch();
      }
    });
  }

  // Trigger search when search field dropdown changes
  if(searchFieldSelect) {
    searchFieldSelect.addEventListener('change', function() {
      performSearch();
    });
  }
  
  let isLoading = false;
  let observer = null;
  
  // Function to setup infinite scroll observer
  function setupInfiniteScroll() {
    const currentSentinel = document.getElementById('sale-ledger-sentinel');
    if(!currentSentinel || !tbody) return;
    
    // Disconnect previous observer if exists
    if(observer) {
      observer.disconnect();
    }
    
    // Get the scrolling container
    const contentDiv = document.querySelector('.content');
    
    // Create new observer
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting && !isLoading) {
          loadMore();
        }
      });
    }, { 
      root: contentDiv,
      threshold: 0.1,
      rootMargin: '100px'
    });
    
    observer.observe(currentSentinel);
  }
  
  async function loadMore(){
    if(isLoading) return;
    const currentSentinel = document.getElementById('sale-ledger-sentinel');
    if(!currentSentinel) return;
    
    const nextUrl = currentSentinel.getAttribute('data-next-url');
    if(!nextUrl) return;
    
    isLoading = true;
    const spinner = document.getElementById('sale-ledger-spinner');
    const loadText = document.getElementById('sale-ledger-load-text');
    spinner && spinner.classList.remove('d-none');
    loadText && (loadText.textContent = 'Loading...');
    
    try{
      // Add current search/filter params to nextUrl
      const formData = new FormData(filterForm);
      const params = new URLSearchParams(formData);
      const url = new URL(nextUrl, window.location.origin);
      params.forEach((value, key) => {
        if(value) url.searchParams.set(key, value);
      });
      const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const newRows = tempDiv.querySelectorAll('#sale-ledger-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));
      
      const newSentinel = tempDiv.querySelector('#sale-ledger-sentinel');
      if(newSentinel){
        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
        spinner && spinner.classList.add('d-none');
        loadText && (loadText.textContent = 'Scroll for more');
        isLoading = false;
      } else {
        observer && observer.disconnect();
        currentSentinel.remove();
        spinner && spinner.remove();
        loadText && loadText.remove();
      }
    }catch(e){
      spinner && spinner.classList.add('d-none');
      loadText && (loadText.textContent = 'Error loading');
      isLoading = false;
    }
  }
  
  // Initial setup of infinite scroll
  setupInfiniteScroll();

  // Handle successful delete - refresh the page to show updated list
  window.addEventListener('deleteSuccess', function() {
    // Refresh the current page to show updated data
    performSearch();
  });

  // Make performSearch globally accessible for modal callbacks
  window.performSaleLedgerSearch = performSearch;

  // Multiple delete functionality for sale ledger
  let saleLedgerPageElements = {
    selectAllCheckbox: document.getElementById('select-all-sale-ledger'),
    deleteSelectedBtn: document.getElementById('delete-selected-sale-ledger-btn'),
    selectedCountSpan: document.getElementById('selected-sale-ledger-count')
  };

  // Global function to update selected count for sale ledger
  window.updateSaleLedgerSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.sale-ledger-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (saleLedgerPageElements.selectedCountSpan) {
      saleLedgerPageElements.selectedCountSpan.textContent = count;
    }
    
    if (saleLedgerPageElements.deleteSelectedBtn) {
      if (count > 0) {
        saleLedgerPageElements.deleteSelectedBtn.classList.remove('d-none');
      } else {
        saleLedgerPageElements.deleteSelectedBtn.classList.add('d-none');
      }
    }
    
    // Update select all checkbox state
    if (saleLedgerPageElements.selectAllCheckbox) {
      const allCheckboxes = document.querySelectorAll('.sale-ledger-checkbox');
      if (count === 0) {
        saleLedgerPageElements.selectAllCheckbox.indeterminate = false;
        saleLedgerPageElements.selectAllCheckbox.checked = false;
      } else if (count === allCheckboxes.length) {
        saleLedgerPageElements.selectAllCheckbox.indeterminate = false;
        saleLedgerPageElements.selectAllCheckbox.checked = true;
      } else {
        saleLedgerPageElements.selectAllCheckbox.indeterminate = true;
        saleLedgerPageElements.selectAllCheckbox.checked = false;
      }
    }
  };

  // Global function to reattach event listeners for sale ledger
  window.reattachSaleLedgerEventListeners = function() {
    setTimeout(() => {
      // Reattach individual checkbox change listeners
      document.querySelectorAll('.sale-ledger-checkbox').forEach(checkbox => {
        checkbox.removeEventListener('change', window.updateSaleLedgerSelectedCount);
        checkbox.addEventListener('change', function() {
          window.updateSaleLedgerSelectedCount();
        });
      });
      
      // Handle select-all checkbox
      const selectAllCheckbox = document.getElementById('select-all-sale-ledger');
      if (selectAllCheckbox) {
        const newSelectAll = selectAllCheckbox.cloneNode(true);
        selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
        
        newSelectAll.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('.sale-ledger-checkbox');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
          window.updateSaleLedgerSelectedCount();
        });
        
        saleLedgerPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  // Initial attachment of event listeners
  window.reattachSaleLedgerEventListeners();
  window.updateSaleLedgerSelectedCount();
});

// Multiple delete confirmation function for sale ledger (global scope for onclick)
function confirmMultipleDeleteSaleLedger() {
  const checkedBoxes = document.querySelectorAll('.sale-ledger-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const ledgerName = row.querySelector('td:nth-child(3) div').textContent.trim(); // Ledger Name column
    selectedItems.push({
      id: checkbox.value,
      name: ledgerName
    });
  });

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.sale-ledger.multiple-delete') }}',
    itemType: 'sale-ledger',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      window.performSaleLedgerSearch();
    },
    onError: function(error) {
      console.error('Error deleting sale ledger entries:', error);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected entries. Please try again.');
      }
    }
  });
}
</script>
@endpush
