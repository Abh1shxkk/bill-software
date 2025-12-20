@extends('layouts.admin')
@section('title','Companies')
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
    opacity: 1;
    visibility: visible;
  }
  
  #scrollToTop.hide {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
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
</style>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div>
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Companies</h4>
      <div class="text-muted small">Manage your company database</div>
    </div>
    @include('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.companies.create'),
        'tableBodyId' => 'company-table-body',
        'checkboxClass' => 'company-checkbox'
    ])
  </div>
  <div>
    <button type="button" id="delete-selected-companies-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteCompanies()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-companies-count">0</span>)
    </button>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card mb-4">
    <div class="card-body">
<form method="GET" action="{{ route('admin.companies.index') }}" class="row g-3" id="company-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="alter_code" {{ request('search_field') == 'alter_code' ? 'selected' : '' }}>Alter Code</option>
            <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>Name</option>
            <option value="mobile" {{ request('search_field') == 'mobile' ? 'selected' : '' }}>Mobile</option>
            <option value="telephone" {{ request('search_field') == 'telephone' ? 'selected' : '' }}>Telephone</option>
            <option value="address" {{ request('search_field') == 'address' ? 'selected' : '' }}>Address</option>
          </select>
        </div>
        <div class="col-md-9">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="company-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive" id="company-table-wrapper" style="position: relative;">
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
              <input class="form-check-input" type="checkbox" id="select-all-companies">
              <label class="form-check-label" for="select-all-companies">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Alter Code</th>
          <th>Name</th>
          <th>Address</th>
          <th>Email</th>
          <th>Mobile 1</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="company-table-body">
        @forelse($companies as $company)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input company-checkbox" type="checkbox" value="{{ $company->id }}" id="company-{{ $company->id }}">
                <label class="form-check-label" for="company-{{ $company->id }}">
                  <span class="visually-hidden">Select company</span>
                </label>
              </div>
            </td>
            <td>{{ ($companies->currentPage() - 1) * $companies->perPage() + $loop->iteration }}</td>
            <td>{{ $company->alter_code }}</td>
            <td>{{ $company->name }}</td>
            <td>{{ $company->address }}</td>
            <td>{{ $company->email }}</td>
            <td>{{ $company->mobile_1 }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.companies.show',$company) }}" title="View"><i class="bi bi-eye"></i></a>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.companies.edit',$company) }}"><i class="bi bi-pencil"></i></a>
              <form action="{{ route('admin.companies.destroy',$company) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.companies.destroy',$company) }}" data-delete-message="Delete this company?" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center text-muted">No data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex flex-column gap-2">
    <div class="align-self-start">Showing {{ $companies->firstItem() ?? 0 }}-{{ $companies->lastItem() ?? 0 }} of {{ $companies->total() }}</div>
    @if($companies->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="company-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="company-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="company-sentinel" data-next-url="{{ $companies->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function(){
  // REST OF YOUR CODE (search, infinite scroll, etc.)
  const searchInput = document.getElementById('company-search');
  const clearSearchBtn = document.getElementById('clear-search');
  const searchFieldSelect = document.getElementById('search_field');
  const filterForm = document.getElementById('company-filter-form');
  const sentinel = document.getElementById('company-sentinel');
  const spinner = document.getElementById('company-spinner');
  const loadText = document.getElementById('company-load-text');
  const tbody = document.getElementById('company-table-body');
  let searchTimeout;
  let isSearching = false;

  function performSearch() {
    if(isSearching) return;
    isSearching = true;
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    
    // Debug logging
    console.log('Search params:', {
      search: formData.get('search'),
      search_field: formData.get('search_field')
    });
    
    // Show loading spinner
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }
    
    // Add visual feedback
    if(searchInput) {
      searchInput.style.opacity = '0.6';
    }
    
    fetch(`{{ route('admin.companies.index') }}?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      console.log('Response received, parsing...');
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#company-table-body tr');
      console.log('Total rows found:', newRows.length);
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        const isRealRow = !(tds.length === 1 && hasColspan);
        console.log('Row check:', { tdCount: tds.length, hasColspan: !!hasColspan, isRealRow });
        return isRealRow;
      });
      
      console.log('Real rows after filter:', realRows.length);
      
      tbody.innerHTML = '';
      if(realRows.length) {
        realRows.forEach(tr => tbody.appendChild(tr));
        console.log('Appended', realRows.length, 'rows to tbody');
      } else {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No companies found</td></tr>';
        console.log('No real rows found, showing empty message');
      }
      
      // Update footer with pagination info and sentinel
      const newFooter = doc.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if(newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }
      
      // Re-query sentinel after footer update (it may have been recreated)
      const updatedSentinel = document.getElementById('company-sentinel');
      if(updatedSentinel) {
        const newSentinel = doc.querySelector('#company-sentinel');
        if(newSentinel && newSentinel.getAttribute('data-next-url')) {
          updatedSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
        }
      }
      
      // Reset infinite scroll state
      isLoading = false;
      
      // Re-setup infinite scroll observer for the updated/new sentinel
      setupInfiniteScroll();
      
      // Reattach event listeners for checkboxes after AJAX update
      window.reattachCompaniesEventListeners();
      window.updateCompaniesSelectedCount();
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
      const s = document.getElementById('company-spinner');
      const t = document.getElementById('company-load-text');
      s && s.classList.add('d-none');
      t && (t.textContent = 'Scroll for more');
    });
  }

  // Make performSearch globally accessible for modal callbacks
  window.performCompaniesSearch = performSearch;

  // Attach debounced keyup on search input (reduced to 300ms for faster response)
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
    const currentSentinel = document.getElementById('company-sentinel');
    if(!currentSentinel || !tbody) return;
    
    // Disconnect previous observer if exists
    if(observer) {
      observer.disconnect();
    }
    
    // Get the scrolling container (.content div)
    const contentDiv = document.querySelector('.content');
    
    // Create new observer with correct root (scrolling container)
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting && !isLoading) {
          console.log('Sentinel visible, loading more...');
          loadMore();
        }
      });
    }, { 
      root: contentDiv, // Watch scrolling within .content div
      threshold: 0.1,
      rootMargin: '100px' // Trigger 100px before sentinel is visible
    });
    
    observer.observe(currentSentinel);
    console.log('Infinite scroll observer setup complete');
  }
  
  async function loadMore(){
    if(isLoading) return;
    const currentSentinel = document.getElementById('company-sentinel');
    if(!currentSentinel) return;
    
    const nextUrl = currentSentinel.getAttribute('data-next-url');
    if(!nextUrl) return;
    
    isLoading = true;
    const spinner = document.getElementById('company-spinner');
    const loadText = document.getElementById('company-load-text');
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
      const newRows = doc.querySelectorAll('#company-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => tbody.appendChild(tr));
      
      // Reattach event listeners to newly loaded checkboxes
      window.reattachCompaniesEventListeners();
      
      const newSentinel = doc.querySelector('#company-sentinel');
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

  // Multiple delete functionality for companies
  let companiesPageElements = {
    selectAllCheckbox: document.getElementById('select-all-companies'),
    deleteSelectedBtn: document.getElementById('delete-selected-companies-btn'),
    selectedCountSpan: document.getElementById('selected-companies-count')
  };

  // Global function to update selected count for companies
  window.updateCompaniesSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.company-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (companiesPageElements.selectedCountSpan) {
      companiesPageElements.selectedCountSpan.textContent = count;
    }
    
    if (companiesPageElements.deleteSelectedBtn) {
      if (count > 0) {
        companiesPageElements.deleteSelectedBtn.classList.remove('d-none');
      } else {
        companiesPageElements.deleteSelectedBtn.classList.add('d-none');
      }
    }
    
    // Update select all checkbox state
    if (companiesPageElements.selectAllCheckbox) {
      const allCheckboxes = document.querySelectorAll('.company-checkbox');
      if (count === 0) {
        companiesPageElements.selectAllCheckbox.indeterminate = false;
        companiesPageElements.selectAllCheckbox.checked = false;
      } else if (count === allCheckboxes.length) {
        companiesPageElements.selectAllCheckbox.indeterminate = false;
        companiesPageElements.selectAllCheckbox.checked = true;
      } else {
        companiesPageElements.selectAllCheckbox.indeterminate = true;
        companiesPageElements.selectAllCheckbox.checked = false;
      }
    }
  };

  // Global function to reattach event listeners for companies
  window.reattachCompaniesEventListeners = function() {
    setTimeout(() => {
      // Remove existing event listeners on individual checkboxes
      document.querySelectorAll('.company-checkbox').forEach(checkbox => {
        const newCheckbox = checkbox.cloneNode(true);
        checkbox.parentNode.replaceChild(newCheckbox, checkbox);
      });
      
      // Reattach individual checkbox change listeners
      document.querySelectorAll('.company-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          window.updateCompaniesSelectedCount();
        });
      });
      
      // Handle select-all checkbox
      const selectAllCheckbox = document.getElementById('select-all-companies');
      if (selectAllCheckbox) {
        const newSelectAll = selectAllCheckbox.cloneNode(true);
        selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
        
        newSelectAll.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('.company-checkbox');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
          window.updateCompaniesSelectedCount();
        });
        
        companiesPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  // Initial attachment of event listeners
  window.reattachCompaniesEventListeners();
  window.updateCompaniesSelectedCount();
});

// Multiple delete confirmation function for companies (global scope for onclick)
function confirmMultipleDeleteCompanies() {
  const checkedBoxes = document.querySelectorAll('.company-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const companyName = row.querySelector('td:nth-child(4)').textContent.trim(); // Name column
    selectedItems.push({
      id: checkbox.value,
      name: companyName
    });
  });

  // Use the global multiple delete modal
  if (typeof window.GlobalMultipleDelete === 'undefined' || !document.getElementById('globalMultipleDeleteModal')) {
    // Fallback to simple confirmation dialog
    const itemNames = selectedItems.map(item => item.name).slice(0, 3).join(', ');
    const displayText = selectedItems.length <= 3 ? itemNames : `${itemNames} and ${selectedItems.length - 3} more companies`;
    
    if (confirm(`Are you sure you want to delete ${selectedItems.length} selected companies?\n\n${displayText}\n\nThis action cannot be undone.`)) {
      // Perform AJAX delete directly
      const itemIds = selectedItems.map(item => item.id);
      
      const formData = new FormData();
      itemIds.forEach(id => {
        formData.append('company_ids[]', id);
      });
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      fetch('{{ route('admin.companies.multiple-delete') }}', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (window.crudNotification) {
            crudNotification.showToast('success', 'Deleted', data.message || `${itemIds.length} companies deleted successfully`);
          }
          // Refresh the table
          window.performCompaniesSearch();
        } else {
          if (window.crudNotification) {
            crudNotification.showToast('error', 'Error', data.message || 'Error deleting companies');
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (window.crudNotification) {
          crudNotification.showToast('error', 'Error', 'Error deleting companies. Please try again.');
        }
      });
    }
    return;
  }

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.companies.multiple-delete') }}',
    itemType: 'companies',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      // Refresh the table via AJAX
      window.performCompaniesSearch();
    },
    onError: function(error) {
      console.error('Error deleting companies:', error);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected companies. Please try again.');
      }
    }
  });
}
</script>


@endpush


