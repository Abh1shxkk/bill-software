@extends('layouts.admin')
@section('title','Sale Return Invoices')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Invoices</h4>
    <div class="text-muted small">View and manage all past sale return invoices</div>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.sale-return.index') }}" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="customer_name" {{ request('filter_by', 'customer_name') == 'customer_name' ? 'selected' : '' }}>Customer Name</option>
            <option value="sr_no" {{ request('filter_by') == 'sr_no' ? 'selected' : '' }}>SR No.</option>
            <option value="original_invoice_no" {{ request('filter_by') == 'original_invoice_no' ? 'selected' : '' }}>Original Invoice No.</option>
            <option value="salesman_name" {{ request('filter_by') == 'salesman_name' ? 'selected' : '' }}>Salesman</option>
            <option value="return_amount" {{ request('filter_by') == 'return_amount' ? 'selected' : '' }}>Return Amount</option>
          </select>
        </div>
        <div class="col-md-5">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" 
                   value="{{ request('search') }}" placeholder="Enter search term..." autocomplete="off">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-search"></i> Search
            </button>
          </div>
        </div>
        <div class="col-md-2">
          <label for="date_from" class="form-label">Date From</label>
          <input type="date" class="form-control" id="date_from" name="date_from" 
                 value="{{ request('date_from') }}" autocomplete="off">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">Date To</label>
          <input type="date" class="form-control" id="date_to" name="date_to" 
                 value="{{ request('date_to') }}" autocomplete="off">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-clockwise"></i> Clear All
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Table Section -->
  <div class="table-responsive" id="sale-return-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Return Date</th>
          <th>Customer Name</th>
          <th>SR No.</th>
          <th>Original Invoice</th>
          <th>Salesman</th>
          <th class="text-end">Return Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="sale-return-table-body">
        @forelse($saleReturns ?? [] as $saleReturn)
          <tr>
            <td>{{ ($saleReturns->currentPage() - 1) * $saleReturns->perPage() + $loop->iteration }}</td>
            <td>{{ $saleReturn->return_date ? $saleReturn->return_date->format('d/m/Y') : '-' }}</td>
            <td>{{ $saleReturn->customer->name ?? '-' }}</td>
            <td>{{ $saleReturn->sr_no ?? '-' }}</td>
            <td>{{ $saleReturn->original_invoice_no ?? '-' }}</td>
            <td>{{ $saleReturn->salesman->name ?? '-' }}</td>
            <td class="text-end">
              <span class="badge bg-danger">₹{{ number_format($saleReturn->net_amount ?? 0, 2) }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="{{ route('admin.sale-return.show', $saleReturn->id) }}" title="View Sale Return Details">
                <i class="bi bi-eye"></i>
              </a>
              <button type="button" class="btn btn-sm btn-outline-primary edit-sale-return" 
                      data-sale-return-id="{{ $saleReturn->id }}"
                      data-sr-no="{{ $saleReturn->sr_no }}"
                      title="Edit Sale Return">
                <i class="bi bi-pencil"></i>
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger delete-sale-return" 
                      data-sale-return-id="{{ $saleReturn->id }}" 
                      data-sr-no="{{ $saleReturn->sr_no }}" 
                      data-customer="{{ $saleReturn->customer->name ?? 'Unknown' }}"
                      title="Delete Sale Return">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">No sale return invoices found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Infinite Scroll Footer -->
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $saleReturns->firstItem() ?? 0 }}-{{ $saleReturns->lastItem() ?? 0 }} of {{ $saleReturns->total() ?? 0 }}</div>
      <div class="text-muted">Page {{ $saleReturns->currentPage() }} of {{ $saleReturns->lastPage() }}</div>
    </div>
    @if($saleReturns->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="sale-return-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="sale-return-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="sale-return-sentinel" data-next-url="{{ $saleReturns->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSaleReturnModal" tabindex="-1" aria-labelledby="deleteSaleReturnModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSaleReturnModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this sale return invoice?</p>
        <div class="alert alert-warning">
          <strong>SR No:</strong> <span id="delete-sr-no"></span><br>
          <strong>Customer:</strong> <span id="delete-customer"></span><br>
          <small class="text-muted">This action cannot be undone and will remove all associated data.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-delete">Delete Sale Return</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const filterSelect = document.getElementById('filter_by');
  const searchInput = document.getElementById('search');
  const dateFromInput = document.getElementById('date_from');
  const dateToInput = document.getElementById('date_to');
  const filterForm = document.getElementById('filterForm');
  const tableBody = document.getElementById('sale-return-table-body');
  const tableWrapper = document.getElementById('sale-return-table-wrapper');
  const overlay = document.getElementById('search-loading');
  
  let observer = null;
  let isLoading = false;
  
  function updatePlaceholder() {
    const filterValue = filterSelect.value;
    const placeholders = {
      'customer_name': 'Enter customer name...',
      'sr_no': 'Enter SR number...',
      'original_invoice_no': 'Enter original invoice number...',
      'salesman_name': 'Enter salesman name...',
      'return_amount': 'Enter minimum amount...'
    };
    
    searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
    
    if (filterValue === 'return_amount') {
      searchInput.type = 'number';
      searchInput.step = '0.01';
    } else {
      searchInput.type = 'text';
      searchInput.removeAttribute('step');
    }
  }
  
  filterSelect.addEventListener('change', updatePlaceholder);
  updatePlaceholder();

  function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  }

  function setLoading(isLoading) {
    if (!overlay) return;
    overlay.style.display = isLoading ? 'flex' : 'none';
  }

  function getFormParams() {
    return new URLSearchParams(new FormData(filterForm));
  }

  async function fetchSaleReturns(urlOrParams, pushState = true) {
    try {
      setLoading(true);
      let url;
      if (typeof urlOrParams === 'string') {
        url = new URL(urlOrParams, window.location.origin);
      } else {
        url = new URL(filterForm.getAttribute('action'), window.location.origin);
        url.search = urlOrParams.toString();
      }

      const response = await fetch(url.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!response.ok) throw new Error('Network response was not ok');
      const html = await response.text();
      
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newTableBody = doc.querySelector('#sale-return-table-body');
      const newFooter = doc.querySelector('.card-footer');
      
      if (newTableBody) {
        tableBody.innerHTML = newTableBody.innerHTML;
      }
      if (newFooter) {
        const currentFooter = document.querySelector('.card-footer');
        if (currentFooter) {
          currentFooter.innerHTML = newFooter.innerHTML;
        }
      }
      
      if (tableWrapper) {
        tableWrapper.scrollTo({ top: 0, behavior: 'smooth' });
      }

      initInfiniteScroll();
      
      if (pushState) {
        window.history.pushState({}, '', url.toString());
      }
    } catch (e) {
      console.error(e);
      alert('Failed to load sale return invoices.');
    } finally {
      setLoading(false);
    }
  }

  const debouncedSearch = debounce(() => fetchSaleReturns(getFormParams()), 300);
  searchInput.addEventListener('input', debouncedSearch);
  filterSelect.addEventListener('change', () => {
    updatePlaceholder();
    fetchSaleReturns(getFormParams());
  });
  dateFromInput && dateFromInput.addEventListener('change', () => fetchSaleReturns(getFormParams()));
  dateToInput && dateToInput.addEventListener('change', () => fetchSaleReturns(getFormParams()));
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchSaleReturns(getFormParams());
  });

  const clearBtn = document.getElementById('clear-filters');
  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      if (filterSelect) filterSelect.value = 'customer_name';
      if (searchInput) searchInput.value = '';
      if (dateFromInput) dateFromInput.value = '';
      if (dateToInput) dateToInput.value = '';
      updatePlaceholder();
      fetchSaleReturns(new URLSearchParams());
    });
  }

  // Edit functionality
  document.addEventListener('click', function(e) {
    const editButton = e.target.closest('.edit-sale-return');
    if (editButton) {
      const saleReturnId = editButton.getAttribute('data-sale-return-id');
      const srNo = editButton.getAttribute('data-sr-no');
      
      // Redirect to modification page with sale return ID
      window.location.href = `{{ route('admin.sale-return.modification') }}?id=${saleReturnId}`;
      return;
    }
  });

  // Delete functionality
  document.addEventListener('click', function(e) {
    const button = e.target.closest('.delete-sale-return');
    if (!button) return;

    const saleReturnId = button.getAttribute('data-sale-return-id');
    const srNo = button.getAttribute('data-sr-no');
    const customer = button.getAttribute('data-customer');
    
    document.getElementById('delete-sr-no').textContent = srNo;
    document.getElementById('delete-customer').textContent = customer;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteSaleReturnModal'));
    modal.show();
    
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.onclick = function() {
      deleteSaleReturn(saleReturnId, modal);
    };
  });

  function deleteSaleReturn(saleReturnId, modal) {
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    
    fetch(`/admin/sale-return/${saleReturnId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        modal.hide();
        fetchSaleReturns(window.location.href, false);
      } else {
        alert('Error deleting sale return: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Error deleting sale return invoice');
    })
    .finally(() => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = 'Delete Sale Return';
    });
  }

  // Infinite scroll functionality
  function initInfiniteScroll() {
    // Disconnect previous observer if exists
    if (observer) {
      observer.disconnect();
    }

    const sentinel = document.getElementById('sale-return-sentinel');
    const spinner = document.getElementById('sale-return-spinner');
    const loadText = document.getElementById('sale-return-load-text');

    if (!sentinel || !tableBody) return;

    isLoading = false;

    async function loadMore() {
      if (isLoading) return;
      const nextUrl = sentinel.getAttribute('data-next-url');
      if (!nextUrl) return;

      isLoading = true;
      spinner && spinner.classList.remove('d-none');
      loadText && (loadText.textContent = 'Loading...');

      try {
        // Merge current filter params with next page URL
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = new URL(nextUrl, window.location.origin);
        params.forEach((value, key) => {
          if (value) url.searchParams.set(key, value);
        });

        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newRows = doc.querySelectorAll('#sale-return-table-body tr');

        const realRows = Array.from(newRows).filter(tr => {
          const tds = tr.querySelectorAll('td');
          return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });

        realRows.forEach(tr => tableBody.appendChild(tr));

        const newSentinel = doc.querySelector('#sale-return-sentinel');
        if (newSentinel) {
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
      } catch (e) {
        console.error(e);
        spinner && spinner.classList.add('d-none');
        loadText && (loadText.textContent = 'Error loading');
        isLoading = false;
      }
    }

    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          loadMore();
        }
      });
    }, { rootMargin: '100px' });

    observer.observe(sentinel);
  }

  // Reinitialize infinite scroll after AJAX fetches
  function reinitInfiniteScroll() {
    initInfiniteScroll();
  }

  // Handle browser back/forward navigation
  window.addEventListener('popstate', function() {
    fetchSaleReturns(window.location.href, false);
    reinitInfiniteScroll();
  });

  // Initialize on page load
  initInfiniteScroll();
});
</script>
@endpush
