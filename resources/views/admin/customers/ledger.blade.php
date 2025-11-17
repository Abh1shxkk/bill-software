@extends('layouts.admin')
@section('title', 'Ledger - ' . $customer->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-journal-text me-2"></i> Customer Ledger - {{ $customer->name }}
    </h4>
    <div class="text-muted small">Party: {{ $customer->name }} | Code: {{ $customer->code ?? 'N/A' }}</div>
  </div>
  <div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
  </div>
</div>

@php
  $currentBalance = $openingBalance + $totalDebit - $totalCredit;
@endphp

<!-- Summary Cards -->
<div class="row mb-3 g-2">
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Opening Balance</small>
            <h5 class="mb-0 text-info">{{ number_format($openingBalance, 2) }}</h5>
          </div>
          <div class="text-info opacity-50">
            <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Debit</small>
            <h5 class="mb-0 text-danger">{{ number_format($totalDebit, 2) }}</h5>
          </div>
          <div class="text-danger opacity-50">
            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Credit</small>
            <h5 class="mb-0 text-success">{{ number_format($totalCredit, 2) }}</h5>
          </div>
          <div class="text-success opacity-50">
            <i class="bi bi-arrow-down-circle" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Current Balance</small>
            <h5 class="mb-0 text-primary">{{ number_format($currentBalance, 2) }}</h5>
          </div>
          <div class="text-primary opacity-50">
            <i class="bi bi-calculator" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Transactions</small>
            <h5 class="mb-0 text-warning">{{ $ledgers->total() }}</h5>
          </div>
          <div class="text-warning opacity-50">
            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Closing Balance</small>
            @php
              $closingBalance = $openingBalance + $totalDebit - $totalCredit;
            @endphp
            <h5 class="mb-0 text-{{ $closingBalance >= 0 ? 'success' : 'danger' }}">{{ number_format(abs($closingBalance), 2) }} {{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}</h5>
          </div>
          <div class="text-{{ $closingBalance >= 0 ? 'success' : 'danger' }} opacity-50">
            <i class="bi bi-bar-chart-fill" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Card -->
<div class="card shadow-sm border-0 rounded mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.customers.ledger', $customer) }}" class="row g-3" id="ledger-filter-form">
      <div class="col-md-3">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
      </div>
      <div class="col-md-3">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
      </div>
      <div class="col-md-4">
        <label for="search" class="form-label">Search by Vou. No / Invoice No</label>
        <div class="input-group">
          <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search...">
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

<!-- Ledger Table -->
<div class="card shadow-sm border-0 rounded">

  <div class="table-responsive" id="ledger-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">#</th>
          <th style="width: 100px;">Date</th>
          <th style="width: 130px;">Vou. No.</th>
          <th style="width: 180px;">Account Name</th>
          <th class="text-end" style="width: 110px;">Debit</th>
          <th class="text-end" style="width: 110px;">Credit</th>
          <th class="text-end" style="width: 130px;">Balance</th>
          <th class="text-end" style="width: 80px;">Actions</th>
        </tr>
      </thead>
      <tbody id="ledger-table-body">
        @if($ledgers->count() > 0)
          @foreach($ledgers as $ledger)
            <tr>
              <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $loop->iteration }}</td>
              <td>{{ \Carbon\Carbon::parse($ledger->transaction_date)->format('d-m-Y') }}</td>
              <td style="white-space: nowrap;">{{ $ledger->trans_no ?? '-' }}</td>
              <td>{{ $ledger->account_name }}</td>
              <td class="text-end">
                @if($ledger->debit_credit === 'debit')
                  <span class="text-danger fw-semibold">{{ number_format($ledger->amount, 2) }}</span>
                @endif
              </td>
              <td class="text-end">
                @if($ledger->debit_credit === 'credit')
                  <span class="text-success fw-semibold">{{ number_format($ledger->amount, 2) }}</span>
                @endif
              </td>
              <td class="text-end">
                {{ number_format(abs($ledger->running_balance), 2) }} {{ $ledger->running_balance >= 0 ? 'Dr' : 'Cr' }}
              </td>
              <td class="text-end">
                @if(isset($ledger->sale_transaction_id))
                  <button type="button" class="btn btn-sm btn-outline-primary view-transaction-btn" 
                          data-type="sale"
                          data-id="{{ $ledger->sale_transaction_id }}" 
                          title="View Sale">
                    <i class="bi bi-eye"></i>
                  </button>
                @elseif(isset($ledger->sale_return_transaction_id))
                  <button type="button" class="btn btn-sm btn-outline-success view-transaction-btn" 
                          data-type="sale_return"
                          data-id="{{ $ledger->sale_return_transaction_id }}" 
                          title="View Sale Return">
                    <i class="bi bi-eye"></i>
                  </button>
                @elseif(isset($ledger->breakage_expiry_transaction_id))
                  <button type="button" class="btn btn-sm btn-outline-warning view-transaction-btn" 
                          data-type="breakage_expiry"
                          data-id="{{ $ledger->breakage_expiry_transaction_id }}" 
                          title="View Breakage/Expiry">
                    <i class="bi bi-eye"></i>
                  </button>
                @elseif(isset($ledger->credit_note_id))
                  <a href="{{ route('admin.credit-note.modification') }}?credit_note_no={{ str_replace('CN / ', '', $ledger->trans_no) }}" 
                     class="btn btn-sm btn-outline-info" 
                     title="View Credit Note">
                    <i class="bi bi-eye"></i>
                  </a>
                @elseif(isset($ledger->debit_note_id))
                  <a href="{{ route('admin.debit-note.modification') }}?debit_note_no={{ str_replace('DN / ', '', $ledger->trans_no) }}" 
                     class="btn btn-sm btn-outline-danger" 
                     title="View Debit Note">
                    <i class="bi bi-eye"></i>
                  </a>
                @endif
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No ledger entries found for this date range
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $ledgers->firstItem() ?? 0 }}-{{ $ledgers->lastItem() ?? 0 }} of {{ $ledgers->total() }}</div>
      <div class="text-muted">Page {{ $ledgers->currentPage() }} of {{ $ledgers->lastPage() }}</div>
    </div>
    @if($ledgers->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="ledger-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="ledger-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="ledger-sentinel" data-next-url="{{ $ledgers->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection

<!-- Custom Transaction Detail Modal -->
<div class="custom-modal-overlay"></div>
<div id="transactionDetailModal" class="custom-modal">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5><i class="bi bi-receipt me-2"></i>Transaction Details</h5>
      <button type="button" class="custom-modal-close" onclick="closeTransactionModal()">&times;</button>
    </div>
    <div class="custom-modal-body" id="transactionDetailContent">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading transaction details...</p>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeTransactionModal()">Close</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Global variables for ledger page
let ledgerPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Global performSearch function for ledger
window.performLedgerSearch = function() {
  if(isSearching) return;
  isSearching = true;
  
  // Ensure elements are initialized
  if (!ledgerPageElements.filterForm) {
    ledgerPageElements.filterForm = document.getElementById('ledger-filter-form');
  }
  if (!ledgerPageElements.tbody) {
    ledgerPageElements.tbody = document.getElementById('ledger-table-body');
  }
  if (!ledgerPageElements.searchInput) {
    ledgerPageElements.searchInput = document.getElementById('search');
  }
  
  const formData = new FormData(ledgerPageElements.filterForm);
  const params = new URLSearchParams(formData);
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('search-loading');
  if(loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Add visual feedback
  if(ledgerPageElements.searchInput) {
    ledgerPageElements.searchInput.style.opacity = '0.6';
  }
  
  fetch(`{{ route('admin.customers.ledger', $customer) }}?${params.toString()}`, {
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
    const newRows = doc.querySelectorAll('#ledger-table-body tr');
    const realRows = Array.from(newRows).filter(tr => {
      const tds = tr.querySelectorAll('td');
      return !(tds.length === 1 && tr.querySelector('td[colspan]'));
    });
    
    // Clear and update table
    ledgerPageElements.tbody.innerHTML = '';
    if(realRows.length) {
      realRows.forEach(tr => ledgerPageElements.tbody.appendChild(tr.cloneNode(true)));
    } else {
      ledgerPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No ledger entries found for this date range</td></tr>';
    }
    
    // Update summary cards
    const newCards = doc.querySelectorAll('.row.mb-3.g-2 .col-md-2');
    const currentCards = document.querySelectorAll('.row.mb-3.g-2 .col-md-2');
    newCards.forEach((newCard, index) => {
      if (currentCards[index]) {
        currentCards[index].innerHTML = newCard.innerHTML;
      }
    });
    
    // Update pagination info and reinitialize infinite scroll
    const newFooter = doc.querySelector('.card-footer');
    const currentFooter = document.querySelector('.card-footer');
    if(newFooter && currentFooter) {
      currentFooter.innerHTML = newFooter.innerHTML;
      // Reinitialize infinite scroll after updating footer
      if (typeof window.initLedgerInfiniteScroll === 'function') {
        window.initLedgerInfiniteScroll();
      }
    }
  })
  .catch(error => {
    console.error('Error in performLedgerSearch:', error);
    if (ledgerPageElements.tbody) {
      ledgerPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
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
    if(ledgerPageElements.searchInput) {
      ledgerPageElements.searchInput.style.opacity = '1';
    }
    
    const s = document.getElementById('ledger-spinner');
    const t = document.getElementById('ledger-load-text');
    s && s.classList.add('d-none');
    t && (t.textContent = 'Scroll for more');
  });
};

// Global infinite scroll function for ledger
window.initLedgerInfiniteScroll = function() {
  // Disconnect previous observer if exists
  if(observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('ledger-sentinel');
  const spinner = document.getElementById('ledger-spinner');
  const loadText = document.getElementById('ledger-load-text');
  
  // Ensure elements are initialized
  if (!ledgerPageElements.tbody) {
    ledgerPageElements.tbody = document.getElementById('ledger-table-body');
  }
  if (!ledgerPageElements.filterForm) {
    ledgerPageElements.filterForm = document.getElementById('ledger-filter-form');
  }
  
  if(!sentinel || !ledgerPageElements.tbody) return;
  
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
      const formData = new FormData(ledgerPageElements.filterForm);
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
      const newRows = doc.querySelectorAll('#ledger-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => ledgerPageElements.tbody.appendChild(tr.cloneNode(true)));
      
      const newSentinel = doc.querySelector('#ledger-sentinel');
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
  ledgerPageElements = {
    tbody: document.getElementById('ledger-table-body'),
    searchInput: document.getElementById('search'),
    clearSearchBtn: document.getElementById('clear-search'),
    filterForm: document.getElementById('ledger-filter-form'),
    tableWrapper: document.getElementById('ledger-table-wrapper'),
    searchLoading: document.getElementById('search-loading'),
    cardFooter: document.querySelector('.card-footer')
  };
  
  // Clear search functionality
  if (ledgerPageElements.clearSearchBtn) {
    ledgerPageElements.clearSearchBtn.addEventListener('click', function() {
      if (ledgerPageElements.searchInput) {
        ledgerPageElements.searchInput.value = '';
        ledgerPageElements.searchInput.focus();
        window.performLedgerSearch();
      }
    });
  }

  // Search input with debounce
  if(ledgerPageElements.searchInput) {
    ledgerPageElements.searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performLedgerSearch, 300);
    });
  }

  // View Transaction Detail Modal
  document.addEventListener('click', function(e) {
    if (e.target.closest('.view-transaction-btn')) {
      const btn = e.target.closest('.view-transaction-btn');
      const transactionId = btn.getAttribute('data-id');
      const transactionType = btn.getAttribute('data-type');
      
      // Show modal with slide-in animation
      const modal = document.getElementById('transactionDetailModal');
      const overlay = document.querySelector('.custom-modal-overlay');
      
      overlay.style.display = 'block';
      modal.style.display = 'block';
      
      // Trigger animation
      setTimeout(() => {
        modal.classList.add('show');
        overlay.classList.add('show');
      }, 10);
      
      // Reset content
      document.getElementById('transactionDetailContent').innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading transaction details...</p>
        </div>
      `;
      
      // Fetch transaction details based on type
      let url = '';
      if (transactionType === 'sale') {
        url = `{{ url('admin/customers') }}/{{ $customer->id }}/ledger/sale/${transactionId}`;
      } else if (transactionType === 'sale_return') {
        url = `{{ url('admin/customers') }}/{{ $customer->id }}/ledger/sale-return/${transactionId}`;
      } else if (transactionType === 'breakage_expiry') {
        url = `{{ url('admin/customers') }}/{{ $customer->id }}/ledger/breakage-expiry/${transactionId}`;
      }
      
      console.log('Fetching URL:', url);
      
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        const content = document.getElementById('transactionDetailContent');
        
        let headerHtml = '';
        if (transactionType === 'sale') {
          headerHtml = `
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Customer:</strong> <span class="text-primary">${data.customer_name || 'N/A'}</span></p>
                <p class="mb-1"><strong>Invoice No:</strong> ${data.invoice_no || 'N/A'}</p>
                <p class="mb-1"><strong>Date:</strong> ${data.sale_date || 'N/A'}</p>
              </div>
              <div class="col-md-6 text-end">
                <p class="mb-1"><strong>Net Amount:</strong> <span class="text-success fs-5">${parseFloat(data.net_amount || 0).toFixed(2)}</span></p>
              </div>
            </div>
          `;
        } else if (transactionType === 'sale_return') {
          headerHtml = `
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Customer:</strong> <span class="text-primary">${data.customer_name || 'N/A'}</span></p>
                <p class="mb-1"><strong>SR No:</strong> ${data.sr_no || 'N/A'}</p>
                <p class="mb-1"><strong>Return Date:</strong> ${data.return_date || 'N/A'}</p>
                <p class="mb-1"><strong>Original Invoice:</strong> ${data.original_invoice_no || 'N/A'}</p>
              </div>
              <div class="col-md-6 text-end">
                <p class="mb-1"><strong>Net Amount:</strong> <span class="text-success fs-5">${parseFloat(data.net_amount || 0).toFixed(2)}</span></p>
              </div>
            </div>
          `;
        } else {
          headerHtml = `
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Customer:</strong> <span class="text-primary">${data.customer_name || 'N/A'}</span></p>
                <p class="mb-1"><strong>SR No:</strong> ${data.sr_no || 'N/A'}</p>
                <p class="mb-1"><strong>Date:</strong> ${data.transaction_date || 'N/A'}</p>
              </div>
              <div class="col-md-6 text-end">
                <p class="mb-1"><strong>Net Amount:</strong> <span class="text-success fs-5">${parseFloat(data.net_amount || 0).toFixed(2)}</span></p>
              </div>
            </div>
          `;
        }
        
        content.innerHTML = `
          ${headerHtml}
          
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead class="table-light">
                <tr>
                  <th>Item Name</th>
                  <th>Pack</th>
                  <th>Batch</th>
                  <th>Exp.</th>
                  <th class="text-end">Qty.</th>
                  ${transactionType !== 'breakage_expiry' ? '<th class="text-end">F.Qty.</th>' : ''}
                  <th class="text-end">Rate</th>
                  ${transactionType !== 'breakage_expiry' ? '<th class="text-end">Dis.%</th>' : ''}
                  ${transactionType !== 'breakage_expiry' ? '<th class="text-end">MRP</th>' : ''}
                  <th class="text-end">Amount</th>
                </tr>
              </thead>
              <tbody>
                ${data.items && data.items.length > 0 ? data.items.map(item => `
                  <tr>
                    <td>${item.item_name || 'N/A'}</td>
                    <td>${item.pack || ''}</td>
                    <td>${item.batch || ''}</td>
                    <td>${item.expiry || ''}</td>
                    <td class="text-end">${item.qty || 0}</td>
                    ${transactionType !== 'breakage_expiry' ? `<td class="text-end">${item.free_qty || 0}</td>` : ''}
                    <td class="text-end">${parseFloat(item.rate || 0).toFixed(2)}</td>
                    ${transactionType !== 'breakage_expiry' ? `<td class="text-end">${parseFloat(item.discount || 0).toFixed(2)}</td>` : ''}
                    ${transactionType !== 'breakage_expiry' ? `<td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>` : ''}
                    <td class="text-end">${parseFloat(item.amount || 0).toFixed(2)}</td>
                  </tr>
                `).join('') : '<tr><td colspan="10" class="text-center">No items found</td></tr>'}
              </tbody>
            </table>
          </div>
        `;
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('transactionDetailContent').innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Error loading transaction details. Please try again.
          </div>
        `;
      });
    }
  });

  // AJAX Filter Form Submission
  if (ledgerPageElements.filterForm) {
    ledgerPageElements.filterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      window.performLedgerSearch();
    });
  }

  // Initialize infinite scroll on page load
  const initialSentinel = document.getElementById('ledger-sentinel');
  if (initialSentinel) {
    window.initLedgerInfiniteScroll();
  }
});
</script>

<style>
  /* Custom Modal Styles - Right Sliding Panel */
  .custom-modal {
    display: none;
    position: fixed;
    top: 70px;
    right: 0;
    width: 900px;
    height: calc(100vh - 100px);
    max-height: calc(100vh - 140px);
    z-index: 999999 !important;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
  }

  .custom-modal.show {
    transform: translateX(0);
  }

  .custom-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 999998 !important;
    opacity: 0;
    transition: all 0.3s ease;
  }

  .custom-modal-overlay.show {
    opacity: 0.7;
  }

  .custom-modal-content {
    background: white;
    height: 100%;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
  }

  .custom-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #dee2e6;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
  }

  .custom-modal-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
  }

  .custom-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
  }

  .custom-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
  }

  .custom-modal-body {
    padding: 0.75rem;
    overflow-y: auto;
    flex: 1;
    background: #f8f9fa;
  }

  .custom-modal-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    background: #ffffff;
    flex-shrink: 0;
  }

  @media (max-width: 768px) {
    .custom-modal {
      width: 100%;
    }
  }

  @media print {
    .card-footer, .btn, form, .text-muted {
      display: none !important;
    }
  }
</style>

<script>
function closeTransactionModal() {
  const modal = document.getElementById('transactionDetailModal');
  const overlay = document.querySelector('.custom-modal-overlay');
  
  // Slide out animation
  modal.classList.remove('show');
  overlay.classList.remove('show');
  
  // Hide after animation
  setTimeout(() => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
  }, 300);
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('custom-modal-overlay')) {
    closeTransactionModal();
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeTransactionModal();
  }
});
</script>
@endpush
