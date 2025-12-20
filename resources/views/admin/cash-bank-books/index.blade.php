@extends('layouts.admin')
@section('title','Cash / Bank Books')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div>
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cash-stack me-2"></i> Cash / Bank Books</h4>
      <div class="text-muted small">Manage cash and bank book entries</div>
    </div>
    @include('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.cash-bank-books.create'),
        'tableBodyId' => 'cashbook-table-body',
        'checkboxClass' => 'cash-bank-books-checkbox'
    ])
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-cash-bank-books-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteCashBankBooks()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-cash-bank-books-count">0</span>)
    </button>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.cash-bank-books.index') }}" class="row g-3" id="cashbook-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
            <option value="name" {{ request('search_field') == 'name' ? 'selected' : '' }}>Name</option>
            <option value="under" {{ request('search_field') == 'under' ? 'selected' : '' }}>Under</option>
            <option value="account_no" {{ request('search_field') == 'account_no' ? 'selected' : '' }}>Account No</option>
          </select>
        </div>
        <div class="col-md-9">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="cashbook-search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive" id="cashbook-table-wrapper" style="position: relative;">
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
              <input class="form-check-input" type="checkbox" id="select-all-cash-bank-books">
              <label class="form-check-label" for="select-all-cash-bank-books">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Name</th>
          <th>Alter Code</th>
          <th>Under</th>
          <th>Opening Balance</th>
          <th>Credit Card</th>
          <th>Account No</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="cashbook-table-body">
        @forelse($books as $index => $book)
        <tr>
          <td>
            <div class="form-check">
              <input class="form-check-input cash-bank-books-checkbox" type="checkbox" value="{{ $book->id }}" id="cash-bank-books-{{ $book->id }}">
              <label class="form-check-label" for="cash-bank-books-{{ $book->id }}">
                <span class="visually-hidden">Select entry</span>
              </label>
            </div>
          </td>
          <td>{{ ($books->currentPage() - 1) * $books->perPage() + $loop->iteration }}</td>
          <td>{{ $book->name ?? '-' }}</td>
          <td>{{ $book->alter_code ?? '-' }}</td>
          <td>{{ $book->under ?? '-' }}</td>
          <td>₹{{ number_format($book->opening_balance ?? 0, 2) }} <span class="badge bg-info">{{ $book->opening_balance_type ?? 'D' }}</span></td>
          <td>{{ $book->credit_card ?? '-' }}</td>
          <td>{{ $book->account_no ?? '-' }}</td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.cash-bank-books.show', $book) }}" title="View"><i class="bi bi-eye"></i></a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.cash-bank-books.edit', $book) }}" title="Edit"><i class="bi bi-pencil"></i></a>
            <form action="{{ route('admin.cash-bank-books.destroy', $book) }}" method="POST" class="d-inline ajax-delete-form">
              @csrf @method('DELETE')
              <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="{{ route('admin.cash-bank-books.destroy', $book) }}" data-delete-message="Delete this cash book entry?" title="Delete"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted">No entries found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex flex-column gap-2">
    <div class="align-self-start">Showing {{ $books->firstItem() ?? 0 }}-{{ $books->lastItem() ?? 0 }} of {{ $books->total() }}</div>
    @if($books->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="cashbook-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="cashbook-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="cashbook-sentinel" data-next-url="{{ $books->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const searchInput = document.getElementById('cashbook-search');
  const clearSearchBtn = document.getElementById('clear-search');
  const searchFieldSelect = document.getElementById('search_field');
  const filterForm = document.getElementById('cashbook-filter-form');
  const sentinel = document.getElementById('cashbook-sentinel');
  const spinner = document.getElementById('cashbook-spinner');
  const loadText = document.getElementById('cashbook-load-text');
  const tbody = document.getElementById('cashbook-table-body');
  let searchTimeout;
  let isSearching = false;

  function performSearch() {
    if(isSearching) return;
    isSearching = true;
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }
    
    if(searchInput) {
      searchInput.style.opacity = '0.6';
    }
    
    fetch(`{{ route('admin.cash-bank-books.index') }}?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#cashbook-table-body tr');
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });
      
      tbody.innerHTML = '';
      if(realRows.length) {
        realRows.forEach(tr => tbody.appendChild(tr));
      } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No entries found</td></tr>';
      }
      
      const newFooter = doc.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if(newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }
      
      setupInfiniteScroll();

      // Reattach events after search refresh
      if (typeof window.reattachCashBankBooksEventListeners === 'function') {
        window.reattachCashBankBooksEventListeners();
        window.updateCashBankBooksSelectedCount && window.updateCashBankBooksSelectedCount();
      }
    })
    .catch(error => {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>';
    })
    .finally(() => {
      isSearching = false;
      
      const loadingSpinner = document.getElementById('search-loading');
      if(loadingSpinner) {
        loadingSpinner.style.display = 'none';
      }
      
      if(searchInput) {
        searchInput.style.opacity = '1';
      }
      const s = document.getElementById('cashbook-spinner');
      const t = document.getElementById('cashbook-load-text');
      s && s.classList.add('d-none');
      t && (t.textContent = 'Scroll for more');
    });
  }

  if(searchInput) {
    searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(performSearch, 300);
    });
  }

  if(clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if(searchInput) {
        searchInput.value = '';
        searchInput.focus();
        performSearch();
      }
    });
  }

  if(searchFieldSelect) {
    searchFieldSelect.addEventListener('change', function() {
      performSearch();
    });
  }
  
  let isLoading = false;
  let observer = null;
  
  function setupInfiniteScroll() {
    const currentSentinel = document.getElementById('cashbook-sentinel');
    if(!currentSentinel || !tbody) return;
    
    if(observer) {
      observer.disconnect();
    }
    
    const contentDiv = document.querySelector('.content');
    
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
    const currentSentinel = document.getElementById('cashbook-sentinel');
    if(!currentSentinel) return;
    
    const nextUrl = currentSentinel.getAttribute('data-next-url');
    if(!nextUrl) return;
    
    isLoading = true;
    const spinner = document.getElementById('cashbook-spinner');
    const loadText = document.getElementById('cashbook-load-text');
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
      const newRows = doc.querySelectorAll('#cashbook-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => tbody.appendChild(tr));
      
      const newSentinel = doc.querySelector('#cashbook-sentinel');
      if(newSentinel){
        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
        spinner && spinner.classList.add('d-none');
        loadText && (loadText.textContent = 'Scroll for more');
        isLoading = false;
        // Reattach events after infinite append
        if (typeof window.reattachCashBankBooksEventListeners === 'function') {
          window.reattachCashBankBooksEventListeners();
          window.updateCashBankBooksSelectedCount && window.updateCashBankBooksSelectedCount();
        }
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
  
  setupInfiniteScroll();

  // Make performSearch globally accessible for modal callbacks
  window.performCashBankBooksSearch = performSearch;

  // Multiple delete functionality for cash bank books
  let cashBankBooksPageElements = {
    selectAllCheckbox: document.getElementById('select-all-cash-bank-books'),
    deleteSelectedBtn: document.getElementById('delete-selected-cash-bank-books-btn'),
    selectedCountSpan: document.getElementById('selected-cash-bank-books-count')
  };

  // Global function to update selected count for cash bank books
  window.updateCashBankBooksSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.cash-bank-books-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (cashBankBooksPageElements.selectedCountSpan) {
      cashBankBooksPageElements.selectedCountSpan.textContent = count;
    }
    
    if (cashBankBooksPageElements.deleteSelectedBtn) {
      if (count > 0) {
        cashBankBooksPageElements.deleteSelectedBtn.classList.remove('d-none');
      } else {
        cashBankBooksPageElements.deleteSelectedBtn.classList.add('d-none');
      }
    }
    
    // Update select all checkbox state
    if (cashBankBooksPageElements.selectAllCheckbox) {
      const allCheckboxes = document.querySelectorAll('.cash-bank-books-checkbox');
      if (count === 0) {
        cashBankBooksPageElements.selectAllCheckbox.indeterminate = false;
        cashBankBooksPageElements.selectAllCheckbox.checked = false;
      } else if (count === allCheckboxes.length) {
        cashBankBooksPageElements.selectAllCheckbox.indeterminate = false;
        cashBankBooksPageElements.selectAllCheckbox.checked = true;
      } else {
        cashBankBooksPageElements.selectAllCheckbox.indeterminate = true;
        cashBankBooksPageElements.selectAllCheckbox.checked = false;
      }
    }
  };

  // Global function to reattach event listeners for cash bank books
  window.reattachCashBankBooksEventListeners = function() {
    setTimeout(() => {
      // Reattach individual checkbox change listeners
      document.querySelectorAll('.cash-bank-books-checkbox').forEach(checkbox => {
        checkbox.removeEventListener('change', window.updateCashBankBooksSelectedCount);
        checkbox.addEventListener('change', function() {
          window.updateCashBankBooksSelectedCount();
        });
      });
      
      // Handle select-all checkbox
      const selectAllCheckbox = document.getElementById('select-all-cash-bank-books');
      if (selectAllCheckbox) {
        const newSelectAll = selectAllCheckbox.cloneNode(true);
        selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
        
        newSelectAll.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('.cash-bank-books-checkbox');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
          window.updateCashBankBooksSelectedCount();
        });
        
        cashBankBooksPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  // Initial attachment of event listeners
  window.reattachCashBankBooksEventListeners();
  window.updateCashBankBooksSelectedCount();
});

// Multiple delete confirmation function for cash bank books (global scope for onclick)
function confirmMultipleDeleteCashBankBooks() {
  const checkedBoxes = document.querySelectorAll('.cash-bank-books-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const bookName = row.querySelector('td:nth-child(3)').textContent.trim(); // Name column
    selectedItems.push({
      id: checkbox.value,
      name: bookName
    });
  });

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.cash-bank-books.multiple-delete') }}',
    itemType: 'cash-bank-books',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      window.performCashBankBooksSearch();
    },
    onError: function(error) {
      console.error('Error deleting cash bank books:', error);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected entries. Please try again.');
      }
    }
  });
}
</script>
@endpush
