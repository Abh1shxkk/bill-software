@extends('layouts.admin')

@section('title', 'Item Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div>
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-tag me-2"></i> Item Categories</h4>
      <div class="text-muted small">Manage item categories</div>
    </div>
    @include('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.item-category.create'),
        'tableBodyId' => 'category-table-body',
        'checkboxClass' => 'item-category-checkbox'
    ])
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-item-category-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteItemCategory()">
      <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-item-category-count">0</span>)
    </button>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.item-category.index') }}" class="row g-3" id="item-category-filter-form">
        <div class="col-md-3">
          <label for="ic_search_field" class="form-label">Search By</label>
          <select class="form-select" id="ic_search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>Name</option>
            <option value="alter_code" {{ request('search_field') == 'alter_code' ? 'selected' : '' }}>Alter. Code</option>
            <option value="status" {{ request('search_field') == 'status' ? 'selected' : '' }}>Status</option>
          </select>
        </div>
        <div class="col-md-7">
          <label for="ic_search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="ic_search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="ic-clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive" id="category-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="ic-search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table align-middle mb-0" id="category-table">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-item-category">
              <label class="form-check-label" for="select-all-item-category">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th style="width: 5%">#</th>
          <th style="width: 40%">Name</th>
          <th style="width: 25%">Alter. Code</th>
          <th style="width: 15%">Status</th>
          <th style="width: 15%" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="category-table-body">
        @forelse($categories as $category)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input item-category-checkbox" type="checkbox" value="{{ $category->id }}" id="item-category-{{ $category->id }}">
                <label class="form-check-label" for="item-category-{{ $category->id }}">
                  <span class="visually-hidden">Select category</span>
                </label>
              </div>
            </td>
            <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
            <td>{{ $category->name ?? '-' }}</td>
            <td>{{ $category->alter_code ?? '-' }}</td>
            <td>{{ $category->status ?? '-' }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.item-category.edit', $category) }}" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.item-category.destroy', $category) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                        data-delete-url="{{ route('admin.item-category.destroy', $category) }}"
                        data-delete-message="Delete this category?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No categories found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="align-self-start">
      Showing {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }}
    </div>
    @if($categories->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="category-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="category-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="category-sentinel" data-next-url="{{ $categories->appends(request()->query())->nextPageUrl() }}" style="height: 20px;"></div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('category-table-body');
  let isLoading = false;
  let observer = null;

  // Infinite scroll implementation
  function initInfiniteScroll() {
    const sentinel = document.getElementById('category-sentinel');

    if (!sentinel) {
      return;
    }

    if (observer) {
      observer.disconnect();
    }

    observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          const nextUrl = sentinel.getAttribute('data-next-url');
          if (nextUrl) {
            loadMore(nextUrl);
          }
        }
      });
    }, { rootMargin: '300px' });

    observer.observe(sentinel);
  }

  function loadMore(url) {
    if (isLoading) return;
    isLoading = true;

    const spinner = document.getElementById('category-spinner');
    const loadText = document.getElementById('category-load-text');

    if (spinner) spinner.classList.remove('d-none');
    if (loadText) loadText.textContent = 'Loading...';

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const newRows = tempDiv.querySelectorAll('#category-table-body tr');
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });

      realRows.forEach(tr => {
        tbody.appendChild(tr.cloneNode(true));
      });

      if (typeof window.reattachItemCategoryEventListeners === 'function') {
        window.reattachItemCategoryEventListeners();
        window.updateItemCategorySelectedCount && window.updateItemCategorySelectedCount();
      }

      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        
        // Reinitialize observer after footer update with a small delay
        setTimeout(() => {
          initInfiniteScroll();
        }, 100);
      }

      const newSentinel = tempDiv.querySelector('#category-sentinel');
      const currentSentinel = document.getElementById('category-sentinel');
      if (newSentinel && currentSentinel) {
        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url') || '');
      } else if (currentSentinel && !newSentinel) {
        currentSentinel.remove();
        if (observer) {
          observer.disconnect();
        }
      }
    })
    .catch(error => {
      console.error('Load more error:', error);
    })
    .finally(() => {
      isLoading = false;
      if (spinner) spinner.classList.add('d-none');
      if (loadText) loadText.textContent = 'Scroll for more';
    });
  }

  initInfiniteScroll();

  // ========== SEARCH FUNCTIONALITY ==========
  let searchTimeout;
  const filterForm = document.getElementById('item-category-filter-form');
  const searchInput = document.getElementById('ic_search');
  const searchFieldSelect = document.getElementById('ic_search_field');
  const clearSearchBtn = document.getElementById('ic-clear-search');

  // AJAX search function
  window.performItemCategorySearch = function() {
    if (!filterForm) return;
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    
    // Show loading spinner
    const loadingSpinner = document.getElementById('ic-search-loading');
    if (loadingSpinner) loadingSpinner.style.display = 'flex';
    if (searchInput) searchInput.style.opacity = '0.6';
    
    fetch(`{{ route('admin.item-category.index') }}?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#category-table-body tr');
      const realRows = Array.from(newRows).filter(tr => !tr.querySelector('td[colspan]'));
      
      tbody.innerHTML = '';
      if (realRows.length) {
        realRows.forEach(tr => tbody.appendChild(tr));
      } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No categories found</td></tr>';
      }
      
      // Update footer
      const newFooter = doc.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        initInfiniteScroll();
      }
      
      // Reattach events
      window.reattachItemCategoryEventListeners();
      window.updateItemCategorySelectedCount();
    })
    .catch(error => console.error('Search error:', error))
    .finally(() => {
      // Hide loading spinner
      const loadingSpinner = document.getElementById('ic-search-loading');
      if (loadingSpinner) loadingSpinner.style.display = 'none';
      if (searchInput) searchInput.style.opacity = '1';
    });
  };

  // Search input with debounce
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performItemCategorySearch, 300);
    });
  }

  // Clear search button
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        window.performItemCategorySearch();
      }
    });
  }

  // Trigger search on field change
  if (searchFieldSelect) {
    searchFieldSelect.addEventListener('change', window.performItemCategorySearch);
  }

  window.performItemCategorySearch = window.performItemCategorySearch;
  let itemCategoryPageElements = { selectAllCheckbox: document.getElementById('select-all-item-category'), deleteSelectedBtn: document.getElementById('delete-selected-item-category-btn'), selectedCountSpan: document.getElementById('selected-item-category-count') };
  window.updateItemCategorySelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.item-category-checkbox:checked'); const count = checkedBoxes.length;
    if (itemCategoryPageElements.selectedCountSpan) itemCategoryPageElements.selectedCountSpan.textContent = count;
    if (itemCategoryPageElements.deleteSelectedBtn) { if (count > 0) itemCategoryPageElements.deleteSelectedBtn.classList.remove('d-none'); else itemCategoryPageElements.deleteSelectedBtn.classList.add('d-none'); }
    if (itemCategoryPageElements.selectAllCheckbox) { const allBoxes = document.querySelectorAll('.item-category-checkbox'); if (count === 0) { itemCategoryPageElements.selectAllCheckbox.indeterminate = false; itemCategoryPageElements.selectAllCheckbox.checked = false; } else if (count === allBoxes.length) { itemCategoryPageElements.selectAllCheckbox.indeterminate = false; itemCategoryPageElements.selectAllCheckbox.checked = true; } else { itemCategoryPageElements.selectAllCheckbox.indeterminate = true; itemCategoryPageElements.selectAllCheckbox.checked = false; } }
  };
  window.reattachItemCategoryEventListeners = function() { setTimeout(() => { document.querySelectorAll('.item-category-checkbox').forEach(cb => { cb.removeEventListener('change', window.updateItemCategorySelectedCount); cb.addEventListener('change', function(){ window.updateItemCategorySelectedCount(); }); }); const selectAll = document.getElementById('select-all-item-category'); if (selectAll) { const newSelectAll = selectAll.cloneNode(true); selectAll.parentNode.replaceChild(newSelectAll, selectAll); newSelectAll.addEventListener('change', function(){ document.querySelectorAll('.item-category-checkbox').forEach(b => b.checked = this.checked); window.updateItemCategorySelectedCount(); }); itemCategoryPageElements.selectAllCheckbox = newSelectAll; } }, 50); };
  window.reattachItemCategoryEventListeners(); window.updateItemCategorySelectedCount();
});
function confirmMultipleDeleteItemCategory() { const checked = document.querySelectorAll('.item-category-checkbox:checked'); if (checked.length === 0) return; const selectedItems = []; checked.forEach(cb => { const row = cb.closest('tr'); const name = row.querySelector('td:nth-child(3)').textContent.trim(); selectedItems.push({ id: cb.value, name: name }); }); window.GlobalMultipleDelete.show({ selectedItems: selectedItems, deleteUrl: '{{ route('admin.item-category.multiple-delete') }}', itemType: 'item categories', csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'), onSuccess: function(){ window.performItemCategorySearch(); }, onError: function(err){ console.error('Error:', err); if (window.crudNotification) crudNotification.showToast('error', 'Error', 'Failed to delete selected categories.'); } }); }
</script>
@endpush
