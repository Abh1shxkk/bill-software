@extends('layouts.admin')
@section('title', 'Dues - ' . $customer->name)
@section('content')

@php
  $openingBalance = $customer->opening_balance ?? 0;
  $closingBalance = $openingBalance + ($totalDebit ?? 0) - ($totalCredit ?? 0);
  $transactionAmount = ($totalDebit ?? 0) + ($totalCredit ?? 0);
  $difference = $transactionAmount - abs($closingBalance);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-cash-coin me-2"></i> Customer Dues - {{ $customer->name }}
    </h4>
    <div class="text-muted small">Party: {{ $customer->name }} | Code: {{ $customer->code ?? 'N/A' }}</div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div class="text-end">
      <div>
        <span class="text-muted small">Trn.Amt:</span> <span class="fw-bold">{{ number_format($transactionAmount, 2) }}</span>
        <span class="mx-1">|</span>
        <span class="text-muted small">Debit:</span> <span class="fw-bold text-danger">{{ number_format($totalDebit ?? 0, 2) }}</span>
        <span class="mx-1">|</span>
        <span class="text-muted small">Credit:</span> <span class="fw-bold text-success">{{ number_format($totalCredit ?? 0, 2) }}</span>
      </div>
      <div>
        <span class="text-muted small">Difference:</span> <span class="fw-bold text-info">{{ number_format(abs($difference), 2) }}</span>
        <span class="mx-1">|</span>
        <span class="text-muted small">Closing Bal:</span> <span class="fw-bold text-{{ $closingBalance >= 0 ? 'danger' : 'success' }}">{{ number_format(abs($closingBalance), 2) }} {{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}</span>
      </div>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-3 g-2">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Debit (Sales)</small>
            <h5 class="mb-0 text-danger">{{ number_format($totalDebit ?? 0, 2) }}</h5>
          </div>
          <div class="text-danger opacity-50">
            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Credit (Returns/Breakage/Expiry)</small>
            <h5 class="mb-0 text-success">{{ number_format($totalCredit ?? 0, 2) }}</h5>
          </div>
          <div class="text-success opacity-50">
            <i class="bi bi-arrow-down-circle" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Net Balance</small>
            @php
              $netBalance = ($totalDebit ?? 0) - ($totalCredit ?? 0);
            @endphp
            <h5 class="mb-0 text-{{ $netBalance >= 0 ? 'primary' : 'success' }}">{{ number_format(abs($netBalance), 2) }} {{ $netBalance >= 0 ? 'Dr' : 'Cr' }}</h5>
          </div>
          <div class="text-primary opacity-50">
            <i class="bi bi-calculator" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Transactions</small>
            <h5 class="mb-0 text-warning">{{ $transactions->total() ?? 0 }}</h5>
          </div>
          <div class="text-warning opacity-50">
            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Card -->
<div class="card shadow-sm border-0 rounded mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.customers.dues', $customer) }}" class="row g-3" id="dues-filter-form">
      <div class="col-md-2">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
      </div>
      <div class="col-md-2">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
      </div>
      <div class="col-md-2">
        <label for="type" class="form-label">Transaction Type</label>
        <select class="form-select" id="type" name="type">
          <option value="">All Types</option>
          <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sales</option>
          <option value="sale_return" {{ request('type') == 'sale_return' ? 'selected' : '' }}>Sale Returns</option>
          <option value="breakage_expiry" {{ request('type') == 'breakage_expiry' ? 'selected' : '' }}>Breakage/Expiry</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="search" class="form-label">Search by Trans No.</label>
        <div class="input-group">
          <input type="text" class="form-control" id="dues-search" name="search" value="{{ request('search') }}" placeholder="Search transaction no..." autocomplete="off">
          <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Dues Table -->
<div class="card shadow-sm border-0 rounded">
  <div class="table-responsive" id="dues-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">Sr.No</th>
          <th style="width: 130px;">Trans No.</th>
          <th style="width: 100px;">Date</th>
          <th style="width: 80px;">Days</th>
          <th style="width: 100px;">Due Date</th>
          <th style="width: 80px;">Days</th>
          <th class="text-end" style="width: 120px;">Trn.Amt.</th>
          <th class="text-end" style="width: 110px;">Debit</th>
          <th class="text-end" style="width: 110px;">Credit</th>
          <th style="width: 80px;">Hold</th>
        </tr>
      </thead>
      <tbody id="dues-table-body">
        @if(isset($transactions) && $transactions->count() > 0)
          @foreach($transactions as $transaction)
            @php
              $transDate = $transaction->transaction_date ?? $transaction->sale_date ?? $transaction->return_date;
              $dueDate = $transaction->due_date ?? $transaction->end_date ?? $transDate;
              $transNo = $transaction->trans_no ?? $transaction->invoice_no ?? $transaction->sr_no ?? '-';
              $series = $transaction->series ?? 'S2';
              $fullTransNo = $series . '/' . $transNo;
              
              // Calculate days from transaction date (rounded to integer)
              $daysSinceTrans = $transDate ? round(\Carbon\Carbon::parse($transDate)->diffInDays(now())) : 0;
              
              // Calculate days from due date (rounded to integer)
              $daysSinceDue = $dueDate ? round(\Carbon\Carbon::parse($dueDate)->diffInDays(now())) : 0;
              
              // Determine if debit or credit
              $isDebit = $transaction->transaction_type === 'sale';
              $amount = $transaction->net_amount ?? 0;
              
              // Row color based on days overdue
              $rowClass = '';
              if ($isDebit && $daysSinceDue > 30) {
                $rowClass = 'table-danger';
              }
            @endphp
            <tr class="{{ $rowClass }}">
              <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
              <td style="white-space: nowrap;">
                <span class="badge bg-{{ $isDebit ? 'primary' : 'success' }}">{{ $fullTransNo }}</span>
              </td>
              <td>{{ $transDate ? \Carbon\Carbon::parse($transDate)->format('d-M-y') : '-' }}</td>
              <td class="text-center">{{ (int)$daysSinceTrans }}</td>
              <td>{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d-M-y') : '-' }}</td>
              <td class="text-center">{{ (int)$daysSinceDue }}</td>
              <td class="text-end">{{ number_format($amount, 2) }}</td>
              <td class="text-end">
                @if($isDebit)
                  <span class="text-danger fw-semibold">{{ number_format($amount, 2) }}</span>
                @endif
              </td>
              <td class="text-end">
                @if(!$isDebit)
                  <span class="text-success fw-semibold">{{ number_format($amount, 2) }}</span>
                @endif
              </td>
              <td class="text-center">
                @if(isset($transaction->hold) && $transaction->hold)
                  <i class="bi bi-check-circle-fill text-warning" title="On Hold"></i>
                @endif
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="10" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No dues found for this customer
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() ?? 0 }}</div>
      <div class="text-muted">Page {{ $transactions->currentPage() ?? 1 }} of {{ $transactions->lastPage() ?? 1 }}</div>
    </div>
    @if(isset($transactions) && $transactions->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="dues-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="dues-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="dues-sentinel" data-next-url="{{ $transactions->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables for dues page
let duesPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Global performSearch function for dues
window.performDuesSearch = function() {
  if(isSearching) return;
  isSearching = true;
  
  // Ensure elements are initialized
  if (!duesPageElements.filterForm) {
    duesPageElements.filterForm = document.getElementById('dues-filter-form');
  }
  if (!duesPageElements.tbody) {
    duesPageElements.tbody = document.getElementById('dues-table-body');
  }
  if (!duesPageElements.searchInput) {
    duesPageElements.searchInput = document.getElementById('dues-search');
  }
  
  const formData = new FormData(duesPageElements.filterForm);
  const params = new URLSearchParams(formData);
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('search-loading');
  if(loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Add visual feedback
  if(duesPageElements.searchInput) {
    duesPageElements.searchInput.style.opacity = '0.6';
  }
  
  fetch(`{{ route('admin.customers.dues', $customer) }}?${params.toString()}`, {
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
    const newRows = doc.querySelectorAll('#dues-table-body tr');
    const realRows = Array.from(newRows).filter(tr => {
      const tds = tr.querySelectorAll('td');
      return !(tds.length === 1 && tr.querySelector('td[colspan]'));
    });
    
    // Clear and update table
    duesPageElements.tbody.innerHTML = '';
    if(realRows.length) {
      realRows.forEach(tr => duesPageElements.tbody.appendChild(tr.cloneNode(true)));
    } else {
      duesPageElements.tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No dues found for this customer</td></tr>';
    }
    
    // Update summary cards
    const newCards = doc.querySelectorAll('.row.mb-3.g-2 .col-md-3');
    const currentCards = document.querySelectorAll('.row.mb-3.g-2 .col-md-3');
    newCards.forEach((newCard, index) => {
      if (currentCards[index]) {
        currentCards[index].innerHTML = newCard.innerHTML;
      }
    });
    
    // Update header totals
    const newHeaderTotals = doc.querySelector('.d-flex.align-items-center.gap-3 .text-end');
    const currentHeaderTotals = document.querySelector('.d-flex.align-items-center.gap-3 .text-end');
    if (newHeaderTotals && currentHeaderTotals) {
      currentHeaderTotals.innerHTML = newHeaderTotals.innerHTML;
    }
    
    // Update pagination info and reinitialize infinite scroll
    const newFooter = doc.querySelector('.card-footer');
    const currentFooter = document.querySelector('.card-footer');
    if(newFooter && currentFooter) {
      currentFooter.innerHTML = newFooter.innerHTML;
      // Reinitialize infinite scroll after updating footer
      if (typeof window.initDuesInfiniteScroll === 'function') {
        window.initDuesInfiniteScroll();
      }
    }
  })
  .catch(error => {
    console.error('Error in performDuesSearch:', error);
    if (duesPageElements.tbody) {
      duesPageElements.tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
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
    if(duesPageElements.searchInput) {
      duesPageElements.searchInput.style.opacity = '1';
    }
    
    const s = document.getElementById('dues-spinner');
    const t = document.getElementById('dues-load-text');
    s && s.classList.add('d-none');
    t && (t.textContent = 'Scroll for more');
  });
};

// Global infinite scroll function for dues
window.initDuesInfiniteScroll = function() {
  // Disconnect previous observer if exists
  if(observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('dues-sentinel');
  const spinner = document.getElementById('dues-spinner');
  const loadText = document.getElementById('dues-load-text');
  
  // Ensure elements are initialized
  if (!duesPageElements.tbody) {
    duesPageElements.tbody = document.getElementById('dues-table-body');
  }
  if (!duesPageElements.filterForm) {
    duesPageElements.filterForm = document.getElementById('dues-filter-form');
  }
  
  if(!sentinel || !duesPageElements.tbody) return;
  
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
      const formData = new FormData(duesPageElements.filterForm);
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
      const newRows = doc.querySelectorAll('#dues-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => duesPageElements.tbody.appendChild(tr.cloneNode(true)));
      
      const newSentinel = doc.querySelector('#dues-sentinel');
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
      console.error('Load more error:', e);
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

document.addEventListener('DOMContentLoaded', function() {
  // Initialize page elements
  duesPageElements = {
    tbody: document.getElementById('dues-table-body'),
    searchInput: document.getElementById('dues-search'),
    clearSearchBtn: document.getElementById('clear-search'),
    filterForm: document.getElementById('dues-filter-form'),
    tableWrapper: document.getElementById('dues-table-wrapper'),
    searchLoading: document.getElementById('search-loading'),
    cardFooter: document.querySelector('.card-footer')
  };
  
  // Clear search functionality
  if (duesPageElements.clearSearchBtn) {
    duesPageElements.clearSearchBtn.addEventListener('click', function() {
      if (duesPageElements.searchInput) {
        duesPageElements.searchInput.value = '';
        duesPageElements.searchInput.focus();
        window.performDuesSearch();
      }
    });
  }

  // Search input with debounce
  if(duesPageElements.searchInput) {
    duesPageElements.searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performDuesSearch, 300);
    });
  }

  // Type dropdown change trigger search
  const typeSelect = document.getElementById('type');
  if (typeSelect) {
    typeSelect.addEventListener('change', function() {
      window.performDuesSearch();
    });
  }

  // AJAX Filter Form Submission
  if (duesPageElements.filterForm) {
    duesPageElements.filterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      window.performDuesSearch();
    });
  }

  // Initialize infinite scroll on page load
  const initialSentinel = document.getElementById('dues-sentinel');
  if (initialSentinel) {
    window.initDuesInfiniteScroll();
  }
});
</script>

<style>
  .table-danger {
    background-color: rgba(220, 53, 69, 0.15) !important;
  }
  
  .table-danger:hover {
    background-color: rgba(220, 53, 69, 0.25) !important;
  }
  
  @media print {
    .card-footer, .btn, form, .text-muted {
      display: none !important;
    }
  }
</style>
@endpush
