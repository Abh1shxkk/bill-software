@extends('layouts.admin')
@section('title', 'Stock Transfer Incoming')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-in-down me-2"></i> Stock Transfer Incoming</h4>
    <div class="text-muted small">Manage incoming stock transfers</div>
  </div>
  <div>
    <a href="{{ route('admin.stock-transfer-incoming.transaction') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i> New Transaction
    </a>
    <a href="{{ route('admin.stock-transfer-incoming.modification') }}" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil-square me-1"></i> Modification
    </a>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.stock-transfer-incoming.index') }}" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="trf_no" {{ request('filter_by', 'trf_no') == 'trf_no' ? 'selected' : '' }}>Trf No.</option>
            <option value="supplier_name" {{ request('filter_by') == 'supplier_name' ? 'selected' : '' }}>Supplier Name</option>
            <option value="total_amount" {{ request('filter_by') == 'total_amount' ? 'selected' : '' }}>Total Amount</option>
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
  <div class="table-responsive" id="sti-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Trf Date</th>
          <th>Supplier Name</th>
          <th>Trf No.</th>
          <th>GR No.</th>
          <th>GR Date</th>
          <th class="text-end">Total Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="sti-table-body">
        @forelse($transactions ?? [] as $transaction)
          <tr>
            <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
            <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }}</td>
            <td>{{ $transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? '-') }}</td>
            <td>{{ $transaction->trf_no ?? '-' }}</td>
            <td>{{ $transaction->gr_no ?? '-' }}</td>
            <td>{{ $transaction->gr_date ? (is_string($transaction->gr_date) ? $transaction->gr_date : $transaction->gr_date->format('d/m/Y')) : '-' }}</td>
            <td class="text-end">
              <span class="badge bg-success">₹{{ number_format($transaction->total_amount ?? 0, 2) }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="{{ route('admin.stock-transfer-incoming.show', $transaction->id) }}" title="View Details">
                <i class="bi bi-eye"></i>
              </a>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.stock-transfer-incoming.modification') }}?id={{ $transaction->id }}" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button" class="btn btn-sm btn-outline-danger delete-sti" 
                      data-id="{{ $transaction->id }}" 
                      data-trf-no="{{ $transaction->trf_no }}" 
                      data-supplier="{{ $transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? 'Unknown') }}"
                      title="Delete">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">No stock transfer incoming transactions found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Infinite Scroll Footer -->
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() ?? 0 }}</div>
      <div class="text-muted">Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</div>
    </div>
    @if($transactions->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="sti-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="sti-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="sti-sentinel" data-next-url="{{ $transactions->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteStiModal" tabindex="-1" aria-labelledby="deleteStiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStiModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this stock transfer incoming transaction?</p>
        <div class="alert alert-warning">
          <strong>Trf No:</strong> <span id="delete-trf-no"></span><br>
          <strong>Supplier:</strong> <span id="delete-supplier"></span><br>
          <small class="text-muted">This action cannot be undone. Stock quantities will be restored.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
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
  const tableBody = document.getElementById('sti-table-body');
  const tableWrapper = document.getElementById('sti-table-wrapper');
  const overlay = document.getElementById('search-loading');
  
  let observer = null;
  let isLoading = false;
  
  function updatePlaceholder() {
    const filterValue = filterSelect.value;
    const placeholders = {
      'trf_no': 'Enter transfer number...',
      'supplier_name': 'Enter supplier name...',
      'total_amount': 'Enter minimum amount...'
    };
    
    searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
    
    if (filterValue === 'total_amount') {
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

  async function fetchData(urlOrParams, pushState = true) {
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
      const newTableBody = doc.querySelector('#sti-table-body');
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
      alert('Failed to load data.');
    } finally {
      setLoading(false);
    }
  }

  const debouncedSearch = debounce(() => fetchData(getFormParams()), 300);
  searchInput.addEventListener('input', debouncedSearch);
  filterSelect.addEventListener('change', () => {
    updatePlaceholder();
    fetchData(getFormParams());
  });
  dateFromInput && dateFromInput.addEventListener('change', () => fetchData(getFormParams()));
  dateToInput && dateToInput.addEventListener('change', () => fetchData(getFormParams()));
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchData(getFormParams());
  });

  const clearBtn = document.getElementById('clear-filters');
  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      if (filterSelect) filterSelect.value = 'trf_no';
      if (searchInput) searchInput.value = '';
      if (dateFromInput) dateFromInput.value = '';
      if (dateToInput) dateToInput.value = '';
      updatePlaceholder();
      fetchData(new URLSearchParams());
    });
  }

  // Delete functionality
  document.addEventListener('click', function(e) {
    const button = e.target.closest('.delete-sti');
    if (!button) return;

    const id = button.getAttribute('data-id');
    const trfNo = button.getAttribute('data-trf-no');
    const supplier = button.getAttribute('data-supplier');
    
    document.getElementById('delete-trf-no').textContent = trfNo;
    document.getElementById('delete-supplier').textContent = supplier;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteStiModal'));
    modal.show();
    
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.onclick = function() {
      deleteTransaction(id, modal);
    };
  });

  function deleteTransaction(id, modal) {
    const confirmBtn = document.getElementById('confirm-delete');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    
    fetch(`{{ url('admin/stock-transfer-incoming') }}/${id}`, {
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
        fetchData(window.location.href, false);
      } else {
        alert('Error deleting: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Error deleting transaction');
    })
    .finally(() => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = 'Delete';
    });
  }

  // Infinite scroll functionality
  function initInfiniteScroll() {
    if (observer) {
      observer.disconnect();
    }

    const sentinel = document.getElementById('sti-sentinel');
    const spinner = document.getElementById('sti-spinner');
    const loadText = document.getElementById('sti-load-text');

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
        const newRows = doc.querySelectorAll('#sti-table-body tr');

        const realRows = Array.from(newRows).filter(tr => {
          const tds = tr.querySelectorAll('td');
          return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });

        realRows.forEach(tr => tableBody.appendChild(tr));

        const newSentinel = doc.querySelector('#sti-sentinel');
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

  // Handle browser back/forward navigation
  window.addEventListener('popstate', function() {
    fetchData(window.location.href, false);
  });

  // Initialize on page load
  initInfiniteScroll();
});
</script>
@endpush
