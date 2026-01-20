@extends('layouts.admin')
@section('title','Sale Invoices')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-receipt me-2"></i> Sale Invoices</h4>
    <div class="text-muted small">View and manage all past sale invoices</div>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.sale.invoices') }}" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="customer_name" {{ request('filter_by', 'customer_name') == 'customer_name' ? 'selected' : '' }}>Customer Name</option>
            <option value="invoice_no" {{ request('filter_by') == 'invoice_no' ? 'selected' : '' }}>Invoice No.</option>
            <option value="salesman_name" {{ request('filter_by') == 'salesman_name' ? 'selected' : '' }}>Salesman</option>
            <option value="invoice_amount" {{ request('filter_by') == 'invoice_amount' ? 'selected' : '' }}>Invoice Amount</option>
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
  <div class="table-responsive" id="sale-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Sale Date</th>
          <th>Customer Name</th>
          <th>Invoice No.</th>
          <th>Salesman</th>
          <th>Due Date</th>
          <th class="text-end">Invoice Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="sale-table-body">
        @forelse($sales ?? [] as $sale)
          @php
            $isTemp = $sale->series === 'TEMP' || str_starts_with($sale->invoice_no ?? '', 'TEMP-');
          @endphp
          <tr class="{{ $isTemp ? 'table-warning' : '' }}">
            <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
            <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
            <td>{{ $sale->customer->name ?? '-' }}</td>
            <td>
              {{ $sale->invoice_no ?? '-' }}
              @if($isTemp)
                <span class="badge bg-warning text-dark ms-1" title="Temporary Transaction - Needs Finalization">
                  <i class="bi bi-clock"></i> TEMP
                </span>
              @endif
            </td>
            <td>{{ $sale->salesman->name ?? '-' }}</td>
            <td>{{ $sale->due_date ? $sale->due_date->format('d/m/Y') : '-' }}</td>
            <td class="text-end">
              <span class="badge bg-success">₹{{ number_format($sale->net_amount ?? 0, 2) }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="{{ route('admin.sale.show', $sale->id) }}" title="View Sale Details">
                <i class="bi bi-eye"></i>
              </a>
              @if($isTemp)
                {{-- TEMP Transaction: Show Finalize button --}}
                <a class="btn btn-sm btn-warning" href="{{ route('admin.sale.modification') }}?invoice_no={{ $sale->invoice_no }}&mode=finalize" title="Finalize Transaction">
                  <i class="bi bi-check-circle"></i>
                </a>
              @else
                {{-- Normal Transaction: Show Edit button --}}
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.sale.modification') }}?invoice_no={{ $sale->invoice_no }}" title="Edit Sale">
                  <i class="bi bi-pencil"></i>
                </a>
              @endif
              <button type="button" class="btn btn-sm btn-outline-danger delete-sale" 
                      data-sale-id="{{ $sale->id }}" 
                      data-invoice-no="{{ $sale->invoice_no }}" 
                      data-customer="{{ $sale->customer->name ?? 'Unknown' }}"
                      title="Delete Sale">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">No sale invoices found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Infinite Scroll Footer -->
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $sales->firstItem() ?? 0 }}-{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() ?? 0 }}</div>
      <div class="text-muted">Page {{ $sales->currentPage() }} of {{ $sales->lastPage() }}</div>
    </div>
    @if($sales->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="sale-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="sale-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="sale-sentinel" data-next-url="{{ $sales->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSaleModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this sale invoice?</p>
        <div class="alert alert-warning">
          <strong>Invoice No:</strong> <span id="delete-invoice-no"></span><br>
          <strong>Customer:</strong> <span id="delete-customer"></span><br>
          <small class="text-muted">This action cannot be undone and will remove all associated data.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-delete">Delete Sale</button>
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
  const tableBody = document.getElementById('sale-table-body');
  const tableWrapper = document.getElementById('sale-table-wrapper');
  const overlay = document.getElementById('search-loading');
  
  let observer = null;
  let isLoading = false;
  
  function updatePlaceholder() {
    const filterValue = filterSelect.value;
    const placeholders = {
      'customer_name': 'Enter customer name...',
      'invoice_no': 'Enter invoice number...',
      'salesman_name': 'Enter salesman name...',
      'invoice_amount': 'Enter minimum amount...'
    };
    
    searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
    
    if (filterValue === 'invoice_amount') {
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

  async function fetchSales(urlOrParams, pushState = true) {
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
      const newTableBody = doc.querySelector('#sale-table-body');
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
      alert('Failed to load invoices.');
    } finally {
      setLoading(false);
    }
  }

  const debouncedSearch = debounce(() => fetchSales(getFormParams()), 300);
  searchInput.addEventListener('input', debouncedSearch);
  filterSelect.addEventListener('change', () => {
    updatePlaceholder();
    fetchSales(getFormParams());
  });
  dateFromInput && dateFromInput.addEventListener('change', () => fetchSales(getFormParams()));
  dateToInput && dateToInput.addEventListener('change', () => fetchSales(getFormParams()));
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchSales(getFormParams());
  });

  const clearBtn = document.getElementById('clear-filters');
  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      if (filterSelect) filterSelect.value = 'customer_name';
      if (searchInput) searchInput.value = '';
      if (dateFromInput) dateFromInput.value = '';
      if (dateToInput) dateToInput.value = '';
      updatePlaceholder();
      fetchSales(new URLSearchParams());
    });
  }

  // Delete functionality
  document.addEventListener('click', function(e) {
    const button = e.target.closest('.delete-sale');
    if (!button) return;

    const saleId = button.getAttribute('data-sale-id');
    const invoiceNo = button.getAttribute('data-invoice-no');
    const customer = button.getAttribute('data-customer');
    
    document.getElementById('delete-invoice-no').textContent = invoiceNo;
    document.getElementById('delete-customer').textContent = customer;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));
    modal.show();
    
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.onclick = function() {
      deleteSale(saleId, modal);
    };
  });

  function deleteSale(saleId, modal) {
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    
    fetch(`/admin/sale/${saleId}`, {
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
        fetchSales(window.location.href, false);
      } else {
        alert('Error deleting sale: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Error deleting sale invoice');
    })
    .finally(() => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = 'Delete Sale';
    });
  }

  // Infinite scroll functionality
  function initInfiniteScroll() {
    // Disconnect previous observer if exists
    if (observer) {
      observer.disconnect();
    }

    const sentinel = document.getElementById('sale-sentinel');
    const spinner = document.getElementById('sale-spinner');
    const loadText = document.getElementById('sale-load-text');

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
        const newRows = doc.querySelectorAll('#sale-table-body tr');

        const realRows = Array.from(newRows).filter(tr => {
          const tds = tr.querySelectorAll('td');
          return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });

        realRows.forEach(tr => tableBody.appendChild(tr));

        const newSentinel = doc.querySelector('#sale-sentinel');
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
    fetchSales(window.location.href, false);
    reinitInfiniteScroll();
  });

  // Initialize on page load
  initInfiniteScroll();
});
</script>
@endpush

