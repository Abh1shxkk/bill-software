@extends('layouts.admin')
@section('title','Unused Dump Transactions')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
  <div class="d-flex align-items-start gap-3">
    <div style="min-width: 100px;">
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-trash me-2"></i> Unused Dump Transactions</h4>
      <div class="text-muted small">Breakage/Expiry Dump List</div>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.breakage-supplier.unused-dump-transaction') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i> New Transaction
    </a>
    <a href="{{ route('admin.breakage-supplier.unused-dump-modification') }}" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil me-1"></i> Modification
    </a>
  </div>
</div>
<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.breakage-supplier.unused-dump-index') }}" class="row g-3" id="filter-form">
        <div class="col-md-2">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="trn_no" {{ request('filter_by', 'trn_no') == 'trn_no' ? 'selected' : '' }}>Trn No</option>
            <option value="narration" {{ request('filter_by') == 'narration' ? 'selected' : '' }}>Narration</option>
          </select>
        </div>
        <div class="col-md-4">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
        <div class="col-md-2">
          <label for="from_date" class="form-label">From Date</label>
          <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-2">
          <label for="to_date" class="form-label">To Date</label>
          <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="button" class="btn btn-secondary w-100" id="clear-filters">
            <i class="bi bi-x-circle me-1"></i> Clear All
          </button>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive" id="table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Trn No</th>
          <th>Date</th>
          <th>Narration</th>
          <th>Items</th>
          <th class="text-end">Net Loss</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="table-body">
        @forelse($transactions as $transaction)
        <tr>
          <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
          <td><strong>{{ $transaction->trn_no }}</strong></td>
          <td>{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') : '-' }}</td>
          <td>{{ Str::limit($transaction->narration ?? '-', 50) }}</td>
          <td><span class="badge bg-info text-white">{{ $transaction->items_count ?? $transaction->items->count() }}</span></td>
          <td class="text-end text-danger fw-bold">₹{{ number_format($transaction->total_inv_amt ?? 0, 2) }}</td>
          <td>
            @if($transaction->status == 'completed')
              <span class="badge bg-success">Completed</span>
            @elseif($transaction->status == 'cancelled')
              <span class="badge bg-danger">Cancelled</span>
            @else
              <span class="badge bg-secondary">{{ ucfirst($transaction->status ?? 'Pending') }}</span>
            @endif
          </td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.breakage-supplier.show-unused-dump', $transaction->id) }}" title="View"><i class="bi bi-eye"></i></a>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTransaction({{ $transaction->id }})" title="Delete"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted">No dump transactions found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</div>
      <div class="text-muted">Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</div>
    </div>
    @if($transactions->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="sentinel" data-next-url="{{ $transactions->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
let pageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

function performSearch() {
    if(isSearching) return;
    isSearching = true;
    
    const formData = new FormData(pageElements.filterForm);
    const params = new URLSearchParams(formData);
    
    const loadingSpinner = document.getElementById('search-loading');
    if(loadingSpinner) loadingSpinner.style.display = 'flex';
    
    if(pageElements.searchInput) pageElements.searchInput.style.opacity = '0.6';
    
    fetch(`{{ route('admin.breakage-supplier.unused-dump-index') }}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newRows = doc.querySelectorAll('#table-body tr');
        
        pageElements.tbody.innerHTML = '';
        newRows.forEach(tr => pageElements.tbody.appendChild(tr));
        
        const newFooter = doc.querySelector('.card-footer');
        const currentFooter = document.querySelector('.card-footer');
        if(newFooter && currentFooter) {
            currentFooter.innerHTML = newFooter.innerHTML;
            initInfiniteScroll();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (pageElements.tbody) {
            pageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>';
        }
    })
    .finally(() => {
        isSearching = false;
        if(loadingSpinner) loadingSpinner.style.display = 'none';
        if(pageElements.searchInput) pageElements.searchInput.style.opacity = '1';
    });
}

function initInfiniteScroll() {
    if(observer) observer.disconnect();

    const sentinel = document.getElementById('sentinel');
    const spinner = document.getElementById('spinner');
    const loadText = document.getElementById('load-text');
    
    if(!sentinel || !pageElements.tbody) return;
    
    isLoading = false;
    
    async function loadMore(){
        if(isLoading) return;
        const nextUrl = sentinel.getAttribute('data-next-url');
        if(!nextUrl) return;
        
        isLoading = true;
        spinner && spinner.classList.remove('d-none');
        loadText && (loadText.textContent = 'Loading...');
        
        try{
            const formData = new FormData(pageElements.filterForm);
            const params = new URLSearchParams(formData);
            const url = new URL(nextUrl, window.location.origin);
            
            params.forEach((value, key) => {
                if(value) url.searchParams.set(key, value);
            });
            
            const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#table-body tr');
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            realRows.forEach(tr => pageElements.tbody.appendChild(tr));
            
            const newSentinel = doc.querySelector('#sentinel');
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
}

function deleteTransaction(id) {
    if (confirm('Are you sure you want to delete this dump transaction?')) {
        fetch(`{{ url('admin/breakage-supplier/unused-dump') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                performSearch();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting transaction');
        });
    }
}

document.addEventListener('DOMContentLoaded', function(){
    pageElements = {
        tbody: document.getElementById('table-body'),
        searchInput: document.getElementById('search'),
        filterForm: document.getElementById('filter-form'),
        clearSearchBtn: document.getElementById('clear-search'),
        clearFiltersBtn: document.getElementById('clear-filters'),
        filterBySelect: document.getElementById('filter_by'),
        fromDateInput: document.getElementById('from_date'),
        toDateInput: document.getElementById('to_date')
    };

    if(pageElements.searchInput) {
        pageElements.searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });
    }

    if(pageElements.clearSearchBtn) {
        pageElements.clearSearchBtn.addEventListener('click', function() {
            if(pageElements.searchInput) {
                pageElements.searchInput.value = '';
                pageElements.searchInput.focus();
                performSearch();
            }
        });
    }

    if(pageElements.clearFiltersBtn) {
        pageElements.clearFiltersBtn.addEventListener('click', function() {
            window.location.href = '{{ route("admin.breakage-supplier.unused-dump-index") }}';
        });
    }

    if(pageElements.filterBySelect) {
        pageElements.filterBySelect.addEventListener('change', performSearch);
    }

    if(pageElements.fromDateInput) {
        pageElements.fromDateInput.addEventListener('change', performSearch);
    }

    if(pageElements.toDateInput) {
        pageElements.toDateInput.addEventListener('change', performSearch);
    }

    initInfiniteScroll();
});
</script>
@endpush
