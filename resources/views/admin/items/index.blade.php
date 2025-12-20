@extends('layouts.admin')
@section('title','Items')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
  <div class="d-flex align-items-start gap-3">
    <div style="min-width: 100px;">
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-seam me-2"></i> Items</h4>
      <div class="text-muted small">Manage your item/master list</div>
    </div>
    @include('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.items.create'),
        'tableBodyId' => 'item-table-body',
        'checkboxClass' => 'item-checkbox',
        'extraShortcuts' => [
            ['key' => 'F5', 'label' => 'Batches', 'action' => 'batches'],
            ['key' => 'F10', 'label' => 'Stock Ledger', 'action' => 'stock-ledger'],
            ['key' => 'F7', 'label' => 'Pending Orders', 'action' => 'pending-orders'],
        ]
    ])
  </div>
  <div>
    <button type="button" id="delete-selected-btn" class="btn btn-danger d-none" onclick="confirmMultipleDelete()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-count">0</span>)
    </button>
  </div>
</div>
<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
<form method="GET" action="{{ route('admin.items.index') }}" class="row g-3" id="item-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>1. Split Name</option>
            <option value="bar_code" {{ request('search_field') == 'bar_code' ? 'selected' : '' }}>2. BarCode</option>
            <option value="location" {{ request('search_field') == 'location' ? 'selected' : '' }}>3. Location</option>
            <option value="packing" {{ request('search_field') == 'packing' ? 'selected' : '' }}>4. Pack</option>
            <option value="mrp" {{ request('search_field') == 'mrp' ? 'selected' : '' }}>5. Mrp</option>
            <option value="code" {{ request('search_field') == 'code' ? 'selected' : '' }}>6. BtCode</option>
            <option value="hsn_code" {{ request('search_field') == 'hsn_code' ? 'selected' : '' }}>7. HSN</option>
          </select>
        </div>
        <div class="col-md-7">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="item-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive" id="item-table-wrapper" style="position: relative; min-height: 400px;">
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
              <input class="form-check-input" type="checkbox" id="select-all-items">
              <label class="form-check-label" for="select-all-items">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Name</th>
          <th>HSN Code</th>
          <th>Pack</th>
          <th>Company</th>
          <th>Qty</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="item-table-body">
        @forelse($items as $item)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}" id="item-{{ $item->id }}">
                <label class="form-check-label" for="item-{{ $item->id }}">
                  <span class="visually-hidden">Select item</span>
                </label>
              </div>
            </td>
            <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->hsn_code ?? '-' }}</td>
            <td>{{ $item->packing ?? '-' }}</td>
            <td>{{ $item->company->short_name ?? '-' }}</td>
            <td>
              <span class="badge bg-info text-white">{{ number_format($item->total_units ?? 0, 0) }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="{{ route('admin.batches.index', ['item_id' => $item->id]) }}" title="Available Batches (Non-Zero Qty)"><i class="bi bi-boxes"></i></a>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.batches.all', ['item_id' => $item->id]) }}" title="All Batches (Including Zero Qty)"><i class="bi bi-archive"></i></a>
              <a class="btn btn-sm btn-outline-warning" href="{{ route('admin.items.stock-ledger-complete', $item->id) }}" title="Stock Ledger (F10)"><i class="bi bi-graph-up"></i></a>
              <a class="btn btn-sm btn-outline-danger" href="{{ route('admin.items.expiry-ledger', $item->id) }}" title="Expiry Ledger"><i class="bi bi-clock-history"></i></a>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.items.show',$item) }}" title="View"><i class="bi bi-eye"></i></a>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.items.edit',$item) }}" title="Edit"><i class="bi bi-pencil"></i></a>
              <form action="{{ route('admin.items.destroy',$item) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.items.destroy',$item) }}" data-delete-message="Delete this item?" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">No items found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}</div>
      <div class="text-muted">Page {{ $items->currentPage() }} of {{ $items->lastPage() }}</div>
    </div>
    @if($items->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="item-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="item-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="item-sentinel" data-next-url="{{ $items->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables and functions for items page
let itemsPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Global performSearch function that can be accessed by the global modal
window.performItemsSearch = function() {
  if(isSearching) return;
  isSearching = true;
  
  // Ensure elements are initialized
  if (!itemsPageElements.filterForm) {
    itemsPageElements.filterForm = document.getElementById('item-filter-form');
  }
  if (!itemsPageElements.tbody) {
    itemsPageElements.tbody = document.getElementById('item-table-body');
  }
  if (!itemsPageElements.searchInput) {
    itemsPageElements.searchInput = document.getElementById('item-search');
  }
  
  const formData = new FormData(itemsPageElements.filterForm);
  const params = new URLSearchParams(formData);
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('search-loading');
  if(loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Add visual feedback
  if(itemsPageElements.searchInput) {
    itemsPageElements.searchInput.style.opacity = '0.6';
  }
  
  fetch(`{{ route('admin.items.index') }}?${params.toString()}`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.text();
  })
  .then(html => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newRows = doc.querySelectorAll('#item-table-body tr');
    const realRows = Array.from(newRows).filter(tr => {
      const tds = tr.querySelectorAll('td');
      return !(tds.length === 1 && tr.querySelector('td[colspan]'));
    });
    
    // Clear and update table
    itemsPageElements.tbody.innerHTML = '';
    if(realRows.length) {
      realRows.forEach(tr => itemsPageElements.tbody.appendChild(tr));
    } else {
      itemsPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No items found</td></tr>';
    }
    
    // Update pagination info and reinitialize infinite scroll
    const newFooter = doc.querySelector('.card-footer');
    const currentFooter = document.querySelector('.card-footer');
    if(newFooter && currentFooter) {
      currentFooter.innerHTML = newFooter.innerHTML;
      // Reinitialize infinite scroll after updating footer
      if (typeof window.initItemsInfiniteScroll === 'function') {
        window.initItemsInfiniteScroll();
      }
    }
    
    // Reset checkboxes and update count after search
    document.querySelectorAll('.item-checkbox').forEach(cb => {
      cb.checked = false;
      cb.indeterminate = false;
    });
    if (itemsPageElements.selectAllCheckbox) {
      itemsPageElements.selectAllCheckbox.checked = false;
      itemsPageElements.selectAllCheckbox.indeterminate = false;
    }
    
    // Reattach event listeners to new checkboxes first
    window.reattachItemsEventListeners();
    
    // Then update count (after listeners are attached)
    window.updateItemsSelectedCount();
  })
  .catch(error => {
    console.error('Error in performItemsSearch:', error);
    if (itemsPageElements.tbody) {
      itemsPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
    }
  })
  .finally(() => {
    isSearching = false;
    
    // Hide loading spinner
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) {
      loadingSpinner.style.display = 'none';
    }
    
    // Restore search input opacity
    if(itemsPageElements.searchInput) {
      itemsPageElements.searchInput.style.opacity = '1';
    }
    
    const s = document.getElementById('item-spinner');
    const t = document.getElementById('item-load-text');
    s && s.classList.add('d-none');
    t && (t.textContent = 'Scroll for more');
  });
};

// Global updateSelectedCount function
window.updateItemsSelectedCount = function() {
  // Ensure elements are initialized
  if (!itemsPageElements.selectedCountSpan) {
    itemsPageElements.selectedCountSpan = document.getElementById('selected-count');
  }
  if (!itemsPageElements.deleteSelectedBtn) {
    itemsPageElements.deleteSelectedBtn = document.getElementById('delete-selected-btn');
  }
  if (!itemsPageElements.selectAllCheckbox) {
    itemsPageElements.selectAllCheckbox = document.getElementById('select-all-items');
  }
  
  const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
  const count = checkedBoxes.length;
  
  if (itemsPageElements.selectedCountSpan) {
    itemsPageElements.selectedCountSpan.textContent = count;
  }
  
  if (itemsPageElements.deleteSelectedBtn) {
    if (count > 0) {
      itemsPageElements.deleteSelectedBtn.classList.remove('d-none');
    } else {
      itemsPageElements.deleteSelectedBtn.classList.add('d-none');
    }
  }
  
  // Update select all checkbox state
  if (itemsPageElements.selectAllCheckbox) {
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    if (count === 0) {
      itemsPageElements.selectAllCheckbox.indeterminate = false;
      itemsPageElements.selectAllCheckbox.checked = false;
    } else if (count === allCheckboxes.length) {
      itemsPageElements.selectAllCheckbox.indeterminate = false;
      itemsPageElements.selectAllCheckbox.checked = true;
    } else {
      itemsPageElements.selectAllCheckbox.indeterminate = true;
      itemsPageElements.selectAllCheckbox.checked = false;
    }
  }
};

// Global function to reattach event listeners after AJAX content updates
window.reattachItemsEventListeners = function() {
  // Wait a small amount to ensure DOM is fully updated
  setTimeout(() => {
    // Remove existing event listeners on individual checkboxes (if any)
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
      // Clone and replace to remove all event listeners
      const newCheckbox = checkbox.cloneNode(true);
      checkbox.parentNode.replaceChild(newCheckbox, checkbox);
    });
    
    // Reattach individual checkbox change listeners
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        window.updateItemsSelectedCount();
      });
    });
    
    // Ensure select all checkbox is also properly handled
    const selectAllCheckbox = document.getElementById('select-all-items');
    if (selectAllCheckbox) {
      // Remove old listener by cloning
      const newSelectAll = selectAllCheckbox.cloneNode(true);
      selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
      
      // Reattach select all listener
      newSelectAll.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
          checkbox.checked = this.checked;
        });
        window.updateItemsSelectedCount();
      });
      
      // Update reference in itemsPageElements
      itemsPageElements.selectAllCheckbox = newSelectAll;
    }
  }, 50);
};

// Global infinite scroll function
window.initItemsInfiniteScroll = function() {
  // Disconnect previous observer if exists
  if(observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('item-sentinel');
  const spinner = document.getElementById('item-spinner');
  const loadText = document.getElementById('item-load-text');
  
  // Ensure elements are initialized
  if (!itemsPageElements.tbody) {
    itemsPageElements.tbody = document.getElementById('item-table-body');
  }
  if (!itemsPageElements.filterForm) {
    itemsPageElements.filterForm = document.getElementById('item-filter-form');
  }
  
  if(!sentinel || !itemsPageElements.tbody) return;
  
  isLoading = false;
  
  async function loadMore(){
    if(isLoading) return;
    const nextUrl = sentinel.getAttribute('data-next-url');
    if(!nextUrl) return;
    
    isLoading = true;
    spinner && spinner.classList.remove('d-none');
    loadText && (loadText.textContent = 'Loading...');
    
    try{
      // Add current search/filter params to nextUrl
      const formData = new FormData(itemsPageElements.filterForm);
      const params = new URLSearchParams(formData);
      const url = new URL(nextUrl, window.location.origin);
      
      // Merge current filter params with pagination URL
      params.forEach((value, key) => {
        if(value) url.searchParams.set(key, value);
      });
      
      const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#item-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => itemsPageElements.tbody.appendChild(tr));
      
      // Reattach event listeners to newly loaded checkboxes
      window.reattachItemsEventListeners();
      
      const newSentinel = doc.querySelector('#item-sentinel');
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
};

document.addEventListener('DOMContentLoaded', function(){
  // Initialize page elements
  itemsPageElements = {
    tbody: document.getElementById('item-table-body'),
    searchInput: document.getElementById('item-search'),
    searchFieldSelect: document.getElementById('search_field'),
    clearSearchBtn: document.getElementById('clear-search'),
    filterForm: document.getElementById('item-filter-form'),
    selectAllCheckbox: document.getElementById('select-all-items'),
    deleteSelectedBtn: document.getElementById('delete-selected-btn'),
    selectedCountSpan: document.getElementById('selected-count')
  };

  // Local reference to global function for backward compatibility
  function performSearch() {
    window.performItemsSearch();
  }

  // Search input with debounce
  if(itemsPageElements.searchInput) {
    itemsPageElements.searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(performSearch, 300);
    });
  }

  // Clear search button
  if(itemsPageElements.clearSearchBtn) {
    itemsPageElements.clearSearchBtn.addEventListener('click', function() {
      if(itemsPageElements.searchInput) {
        itemsPageElements.searchInput.value = '';
        itemsPageElements.searchInput.focus();
        performSearch();
      }
    });
  }

  // Trigger search when search field dropdown changes
  if(itemsPageElements.searchFieldSelect) {
    itemsPageElements.searchFieldSelect.addEventListener('change', function() {
      performSearch();
    });
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
    
    // Remove toast element after it's hidden
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  }

  // Page jump functionality (AJAX - no page refresh)
  function jumpToPage() {
    // Get fresh references each time
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
    
    // Build URL with current filters and page number
    const formData = new FormData(itemsPageElements.filterForm);
    const params = new URLSearchParams(formData);
    params.set('page', pageNum);
    
    // Show loading state
    pageJumpBtn.disabled = true;
    pageJumpBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    // Fetch page via AJAX
    fetch(`{{ route('admin.items.index') }}?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#item-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      
      // Clear and update table
      itemsPageElements.tbody.innerHTML = '';
      if(realRows.length) {
        realRows.forEach(tr => itemsPageElements.tbody.appendChild(tr));
      } else {
        itemsPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No items found</td></tr>';
      }
      
      // Update pagination info and footer
      const newFooter = doc.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if(newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        // Reinitialize infinite scroll after updating footer
        window.initItemsInfiniteScroll();
        // Reinitialize page jump after footer update
        initPageJump();
      }
      
      // Reattach event listeners to new checkboxes
      window.reattachItemsEventListeners();
      
      // Scroll to top
      const contentDiv = document.querySelector('.content');
      if(contentDiv) {
        contentDiv.scrollTo({ top: 0, behavior: 'smooth' });
      }
      window.scrollTo({ top: 0, behavior: 'smooth' });
    })
    .catch(error => {
      showToast('Error loading page. Please try again.', 'danger');
      console.error(error);
      // Re-enable button on error
      const btn = document.getElementById('page-jump-btn');
      if(btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-right-circle"></i>';
      }
    })
    .finally(() => {
      // Get fresh reference for finally block
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
      // Remove old listeners by cloning
      const newBtn = jumpBtn.cloneNode(true);
      jumpBtn.parentNode.replaceChild(newBtn, jumpBtn);
      newBtn.addEventListener('click', jumpToPage);
    }
    
    if(jumpInput) {
      // Remove old listeners by cloning
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
  
  // Initialize page jump on load
  initPageJump();

  // Infinite scroll functionality - local reference to global function
  function initInfiniteScroll() {
    window.initItemsInfiniteScroll();
  }

  // Initialize on page load
  initInfiniteScroll();

  // Auto-submit on filter change (date filters only)
  const filterInputs = document.querySelectorAll('input[name="date_from"], input[name="date_to"]');
  filterInputs.forEach(function(el){ 
    el.addEventListener('change', function(){ 
      this.form.submit(); 
    }); 
  });

  // Multiple delete functionality - local reference to global function
  function updateSelectedCount() {
    window.updateItemsSelectedCount();
  }

  // Select all functionality
  if (itemsPageElements.selectAllCheckbox) {
    itemsPageElements.selectAllCheckbox.addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.item-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateSelectedCount();
    });
  }

  // Individual checkbox change - handled globally now via reattachItemsEventListeners
  // Initial attachment for page load
  window.reattachItemsEventListeners();

  // Initialize count on page load
  updateSelectedCount();
  
  // Note: Keyboard shortcuts (F9, F3, Delete, F5, F10, F7, Arrow keys) and row selection
  // are handled globally by module-shortcuts.blade.php partial
});

// Multiple delete confirmation function (global scope for onclick)
function confirmMultipleDelete() {
  const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const itemName = row.querySelector('td:nth-child(3)').textContent.trim(); // Name column
    selectedItems.push({
      id: checkbox.value,
      name: itemName
    });
  });

  // Debug: Check what's available
  console.log('Debug - Modal availability check:', {
    GlobalMultipleDelete: typeof window.GlobalMultipleDelete,
    modalElement: !!document.getElementById('globalMultipleDeleteModal'),
    countElement: !!document.getElementById('global-delete-count'),
    typeElement: !!document.getElementById('global-delete-type'),
    footerTypeElement: !!document.getElementById('global-delete-type-footer'),
    itemsListElement: !!document.getElementById('global-selected-items-list')
  });

  // Ensure GlobalMultipleDelete is available, otherwise use fallback
  if (typeof window.GlobalMultipleDelete === 'undefined' || !document.getElementById('globalMultipleDeleteModal')) {
    console.warn('GlobalMultipleDelete not available, using fallback confirmation');
    
    // Fallback to simple confirmation dialog
    const itemNames = selectedItems.map(item => item.name).slice(0, 3).join(', ');
    const displayText = selectedItems.length <= 3 ? itemNames : `${itemNames} and ${selectedItems.length - 3} more items`;
    
    if (confirm(`Are you sure you want to delete ${selectedItems.length} selected items?\n\n${displayText}\n\nThis action cannot be undone.`)) {
      // Perform AJAX delete directly
      const itemIds = selectedItems.map(item => item.id);
      
      fetch('{{ route("admin.items.multiple-delete") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          item_ids: itemIds
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          if (window.crudNotification) {
            crudNotification.showToast('error', 'Deleted', data.message || `${itemIds.length} items deleted successfully`);
          }
          // Refresh the table
          window.performItemsSearch();
        } else {
          if (window.crudNotification) {
            crudNotification.showToast('error', 'Error', data.message || 'Error deleting items');
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (window.crudNotification) {
          crudNotification.showToast('error', 'Error', 'Error deleting items. Please try again.');
        }
      });
    }
    return;
  }

  // Use the global multiple delete modal
  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route("admin.items.multiple-delete") }}',
    itemType: 'items',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      // Refresh the table via AJAX
      window.performItemsSearch();
    },
    onError: function(error) {
      console.error('Error deleting items:', error);
      // Show error notification
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected items. Please try again.');
      }
    }
  });
}
</script>
@endpush
