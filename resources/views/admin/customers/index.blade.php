@extends('layouts.admin')
@section('title','Customers')
@section('content')
<style>
  /* Scroll to Top Button Styles */
  #scrollToTop {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    background: #0d6efd;
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
  }
  
  #scrollToTop.show {
    opacity: 1;
    visibility: visible;
  }
  
  #scrollToTop:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    background: #0b5ed7;
  }
  
  #scrollToTop i {
    font-size: 22px;
  }
  
  /* Smooth scroll */
  .content {
    scroll-behavior: smooth !important;
  }

  /* Action buttons - single line */
  .action-btns {
    display: flex;
    flex-wrap: nowrap;
    gap: 3px;
    justify-content: flex-end;
    align-items: center;
  }
  .action-btns .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
  }
  .action-btns form {
    margin: 0;
  }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-people me-2"></i> Customers</h4>
    <div class="text-muted small">Manage your customer database</div>
  </div>
  <div>
    <button type="button" id="delete-selected-customers-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteCustomers()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-customers-count">0</span>)
    </button>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card mb-4">
    <div class="card-body">
<form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3" id="customer-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>1. Split Name</option>
            <option value="code" {{ request('search_field') == 'code' ? 'selected' : '' }}>2. Alter Code</option>
            <option value="mobile" {{ request('search_field') == 'mobile' ? 'selected' : '' }}>3. Mobile</option>
            <option value="telephone_office" {{ request('search_field') == 'telephone_office' ? 'selected' : '' }}>4. Tel.</option>
            <option value="address" {{ request('search_field') == 'address' ? 'selected' : '' }}>5. Address</option>
            <option value="dl_number" {{ request('search_field') == 'dl_number' ? 'selected' : '' }}>6. DL No.</option>
            <option value="gst_name" {{ request('search_field') == 'gst_name' ? 'selected' : '' }}>7. GSTIN</option>
          </select>
        </div>
        <div class="col-md-9">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="customer-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive" id="customer-table-wrapper" style="position: relative;">
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
              <input class="form-check-input" type="checkbox" id="select-all-customers">
              <label class="form-check-label" for="select-all-customers">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th style="width: 5%;">#</th>
          <th style="width: 20%;">Name</th>
          <th style="width: 10%;">Code</th>
          <th style="width: 10%;">Status</th>
          <th style="width: 8%;">Flag</th>
          <th style="width: 15%;">City</th>
          <th style="width: 12%;">Mobile</th>
          <th style="width: 20%;" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="customer-table-body">
        @forelse($customers as $customer)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input customer-checkbox" type="checkbox" value="{{ $customer->id }}" id="customer-{{ $customer->id }}">
                <label class="form-check-label" for="customer-{{ $customer->id }}">
                  <span class="visually-hidden">Select customer</span>
                </label>
              </div>
            </td>
            <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->code ?? '-' }}</td>
            <td>{{ $customer->status ?? '-' }}</td>
            <td>{{ $customer->flag ?? '-' }}</td>
            <td>{{ $customer->city ?? '-' }}</td>
            <td>{{ $customer->mobile ?? '-' }}</td>
            <td>
              <div class="action-btns">
                <a class="btn btn-sm btn-outline-warning" href="{{ route('admin.customers.ledger',$customer) }}" title="Ledger">
                  <i class="bi bi-journal-text"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger" href="{{ route('admin.customers.dues',$customer) }}" title="Due List">
                  <i class="bi bi-cash-coin"></i>
                </a>
                <a class="btn btn-sm btn-outline-success" href="{{ route('admin.customers.bills',$customer) }}" title="List of Bills">
                  <i class="bi bi-list-ul"></i>
                </a>
                <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.customers.challans',$customer) }}" title="Pending Challans">
                  <i class="bi bi-file-earmark-text"></i>
                </a>
                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.customers.expiry-ledger',$customer) }}" title="Expiry Ledger">
                  <i class="bi bi-receipt-cutoff"></i>
                </a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.customers.show',$customer) }}" title="View">
                  <i class="bi bi-eye"></i>
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.edit',$customer) }}" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.customers.destroy',$customer) }}" method="POST" class="d-inline ajax-delete-form">
                  @csrf @method('DELETE')
                  <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.customers.destroy',$customer) }}" data-delete-message="Delete this customer?" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>
              No customers yet
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex flex-column gap-2">
    <div class="align-self-start">Showing {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }}</div>
    @if($customers->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="customer-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="customer-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="customer-sentinel" data-next-url="{{ $customers->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>
<!-- Scroll to Top Button -->
<button id="scrollToTop" type="button" title="Scroll to top" onclick="scrollToTopNow()">
  <i class="bi bi-arrow-up"></i>
</button>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const tbody = document.getElementById('customer-table-body');
  const searchInput = document.getElementById('customer-search');
  const clearSearchBtn = document.getElementById('clear-search');
  const searchFieldSelect = document.getElementById('search_field');
  const filterForm = document.getElementById('customer-filter-form');
  
  let searchTimeout;
  let isLoading = false;
  let isSearching = false;
  let observer = null;

  // Real-time search implementation
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
    
    fetch(`{{ route('admin.customers.index') }}?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#customer-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      
      // Clear and update table
      tbody.innerHTML = '';
      if(realRows.length) {
        realRows.forEach(tr => tbody.appendChild(tr));
      } else {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No customers found</td></tr>';
      }
      
      // Update pagination info and reinitialize infinite scroll
      const newFooter = doc.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if(newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }
      
      // Re-query sentinel after footer update
      const updatedSentinel = document.getElementById('customer-sentinel');
      if(updatedSentinel) {
        const newSentinel = doc.querySelector('#customer-sentinel');
        if(newSentinel && newSentinel.getAttribute('data-next-url')) {
          updatedSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
        }
      }
      
      // Reset infinite scroll state
      isLoading = false;
      
      // Re-setup infinite scroll observer
      setupInfiniteScroll();
      
      // Reattach event listeners for checkboxes after AJAX update
      window.reattachCustomersEventListeners();
      window.updateCustomersSelectedCount();
    })
    .catch(error => {
      tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>';
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
    });
  }
// GLOBAL FUNCTION for smooth scroll to top
function scrollToTopNow() {
  const contentDiv = document.querySelector('.content');
  if(contentDiv) {
    contentDiv.scrollTo({ top: 0, behavior: 'smooth' });
  }
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
// expose globally for inline onclick handler
window.scrollToTopNow = scrollToTopNow;

  const scrollBtn = document.getElementById('scrollToTop');
  const contentDiv = document.querySelector('.content');
  
  if(scrollBtn && contentDiv) {
    contentDiv.addEventListener('scroll', function() {
      const scrollPos = contentDiv.scrollTop;
      if (scrollPos > 200) {
        scrollBtn.style.opacity = '1';
        scrollBtn.style.visibility = 'visible';
      } else {
        scrollBtn.style.opacity = '0';
        scrollBtn.style.visibility = 'hidden';
      }
    });
  }
  // also react to window scroll as a fallback
  if(scrollBtn) {
    window.addEventListener('scroll', function(){
      const y = window.scrollY || document.documentElement.scrollTop;
      if (y > 200) {
        scrollBtn.style.opacity = '1';
        scrollBtn.style.visibility = 'visible';
      } else {
        scrollBtn.style.opacity = '0';
        scrollBtn.style.visibility = 'hidden';
      }
    });
  }
  // Search input with debounce (reduced to 300ms for faster response)
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

  // Function to setup infinite scroll observer
  function setupInfiniteScroll() {
    const currentSentinel = document.getElementById('customer-sentinel');
    if(!currentSentinel || !tbody) return;
    
    // Disconnect previous observer if exists
    if(observer) {
      observer.disconnect();
    }
    
    // Get the scrolling container (.content div)
    const contentDiv = document.querySelector('.content');
    
    isLoading = false;
    
    async function loadMore(){
      if(isLoading) return;
      const currentSentinel = document.getElementById('customer-sentinel');
      if(!currentSentinel) return;
      
      const nextUrl = currentSentinel.getAttribute('data-next-url');
      if(!nextUrl) return;
      
      isLoading = true;
      const spinner = document.getElementById('customer-spinner');
      const loadText = document.getElementById('customer-load-text');
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
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newRows = doc.querySelectorAll('#customer-table-body tr');
        const realRows = Array.from(newRows).filter(tr => {
          const tds = tr.querySelectorAll('td');
          return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });
        realRows.forEach(tr => tbody.appendChild(tr));
        
        // Reattach event listeners to newly loaded rows
        window.reattachCustomersEventListeners();
        
        const newSentinel = doc.querySelector('#customer-sentinel');
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
    
    // Create new observer with correct root (scrolling container)
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting && !isLoading){
          loadMore();
        }
      });
    }, { 
      root: contentDiv, // Watch scrolling within .content div
      threshold: 0.1,
      rootMargin: '100px' // Trigger 100px before sentinel is visible
    });
    
    observer.observe(currentSentinel);
  }

  // Initial setup of infinite scroll
  setupInfiniteScroll();

  // Auto-submit on filter change (REMOVE THIS if you want only real-time)
  const filterInputs = document.querySelectorAll('input[name="date_from"], input[name="date_to"]');
  filterInputs.forEach(function(el){ 
    el.addEventListener('change', function(){ 
      this.form.submit(); 
    }); 
  });

  // Make performSearch globally accessible for modal callbacks
  window.performCustomersSearch = performSearch;

  // Multiple delete functionality for customers
  let customersPageElements = {
    selectAllCheckbox: document.getElementById('select-all-customers'),
    deleteSelectedBtn: document.getElementById('delete-selected-customers-btn'),
    selectedCountSpan: document.getElementById('selected-customers-count')
  };

  // Global function to update selected count for customers
  window.updateCustomersSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.customer-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (customersPageElements.selectedCountSpan) {
      customersPageElements.selectedCountSpan.textContent = count;
    }
    
    if (customersPageElements.deleteSelectedBtn) {
      if (count > 0) {
        customersPageElements.deleteSelectedBtn.classList.remove('d-none');
      } else {
        customersPageElements.deleteSelectedBtn.classList.add('d-none');
      }
    }
    
    // Update select all checkbox state
    if (customersPageElements.selectAllCheckbox) {
      const allCheckboxes = document.querySelectorAll('.customer-checkbox');
      if (count === 0) {
        customersPageElements.selectAllCheckbox.indeterminate = false;
        customersPageElements.selectAllCheckbox.checked = false;
      } else if (count === allCheckboxes.length) {
        customersPageElements.selectAllCheckbox.indeterminate = false;
        customersPageElements.selectAllCheckbox.checked = true;
      } else {
        customersPageElements.selectAllCheckbox.indeterminate = true;
        customersPageElements.selectAllCheckbox.checked = false;
      }
    }
  };

  // Global function to reattach event listeners for customers
  window.reattachCustomersEventListeners = function() {
    setTimeout(() => {
      // Reattach individual checkbox change listeners (without cloning)
      document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
        // Remove existing listeners by removing and re-adding the event
        checkbox.removeEventListener('change', window.updateCustomersSelectedCount);
        checkbox.addEventListener('change', function() {
          window.updateCustomersSelectedCount();
        });
      });
      
      // Handle select-all checkbox
      const selectAllCheckbox = document.getElementById('select-all-customers');
      if (selectAllCheckbox) {
        const newSelectAll = selectAllCheckbox.cloneNode(true);
        selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
        
        newSelectAll.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('.customer-checkbox');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
          window.updateCustomersSelectedCount();
        });
        
        customersPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  // Initial attachment of event listeners
  window.reattachCustomersEventListeners();
  window.updateCustomersSelectedCount();
});

// Multiple delete confirmation function for customers (global scope for onclick)
function confirmMultipleDeleteCustomers() {
  const checkedBoxes = document.querySelectorAll('.customer-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const customerName = row.querySelector('td:nth-child(3)').textContent.trim(); // Name column
    
    // Try to extract ID from checkbox, or from the row's edit/delete buttons as fallback
    let customerId = checkbox.value || checkbox.getAttribute('value');
    
    if (!customerId || customerId === '') {
      // Fallback: try to extract ID from edit or delete button URLs
      const editBtn = row.querySelector('a[href*="/customers/"][href*="/edit"]');
      const deleteBtn = row.querySelector('button[data-delete-url*="/customers/"]');
      
      if (editBtn) {
        const match = editBtn.href.match(/\/customers\/(\d+)\/edit/);
        if (match) customerId = match[1];
      } else if (deleteBtn) {
        const match = deleteBtn.getAttribute('data-delete-url').match(/\/customers\/(\d+)$/);
        if (match) customerId = match[1];
      }
    }
    
    selectedItems.push({
      id: customerId || 'NO_ID',
      name: customerName
    });
  });

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.customers.multiple-delete') }}',
    itemType: 'customers',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      // Refresh the table via AJAX
      window.performCustomersSearch();
    },
    onError: function(error) {
      console.error('Error deleting customers:', error);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected customers. Please try again.');
      }
    }
  });
}
</script>
@endpush