@extends('layouts.admin')
@section('title', 'Expiry Ledger - ' . $customer->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-receipt-cutoff me-2"></i> Expiry Ledger - {{ $customer->name }}
    </h4>
    <div class="text-muted small">Party: {{ $customer->name }} | Code: {{ $customer->code ?? 'N/A' }}@if($customer->address) | Address: {{ $customer->address }}@endif</div>
  </div>
  <div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
  </div>
</div>

@php
  // Precompute totals and adjustment mapping for the current page of transactions
  $transactionIds = $transactions->pluck('id')->toArray();
  $adjustedIds = [];
  if (count($transactionIds) > 0) {
    $adjustedIds = \App\Models\BreakageExpiryAdjustment::whereIn('breakage_expiry_transaction_id', $transactionIds)
      ->pluck('breakage_expiry_transaction_id')
      ->unique()
      ->toArray();
  }

  $totalCredit = $transactions->sum(function($t) { return $t->net_amount ?? 0; });
  $totalDueReference = 0;
  foreach ($transactions as $t) {
    if (!in_array($t->id, $adjustedIds)) {
      $totalDueReference += $t->mrp_value ?? 0;
    }
  }
@endphp

<!-- Summary Cards -->
<div class="row mb-3 g-2">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block">Total Transactions</small>
            <h5 class="mb-0 text-primary">{{ $transactions->total() }}</h5>
          </div>
          <div class="text-primary opacity-50">
            <i class="bi bi-receipt-cutoff" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Credit Total</small>
            <h5 class="mb-0 text-success">{{ number_format($totalCredit, 2) }}</h5>
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
            <small class="text-muted d-block">Due/Reference Total</small>
            <h5 class="mb-0 text-warning">{{ number_format($totalDueReference, 2) }}</h5>
          </div>
          <div class="text-warning opacity-50">
            <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Balance</small>
            <h5 class="mb-0 text-info">{{ number_format($totalCredit - $totalDueReference, 2) }}</h5>
          </div>
          <div class="text-info opacity-50">
            <i class="bi bi-calculator" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Card -->
<div class="card shadow-sm border-0 rounded mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.customers.expiry-ledger', $customer) }}" class="row g-3" id="ledger-filter-form">
      <div class="col-md-3">
        <label for="date_from" class="form-label">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
      </div>
      <div class="col-md-3">
        <label for="date_to" class="form-label">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
      </div>
      <div class="col-md-4">
        <label for="search" class="form-label">Search by SR No / Transaction No</label>
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
          <th style="width: 110px;">Date</th>
          <th style="width: 110px;">Trn. No.</th>
          <th class="text-end" style="width: 110px;">Debit</th>
          <th class="text-end" style="width: 110px;">Credit</th>
          <th class="text-end" style="width: 130px;">Due/Reference</th>
          <th style="width: 110px;" class="text-center">Type</th>
          <th class="text-end" style="width: 110px;" >Actions</th>
        </tr>
      </thead>
      <tbody id="ledger-table-body">
        @php
          $runningBalance = 0;
        @endphp
        
        @forelse($transactions as $transaction)
          @php
            // Calculate credit (breakage/expiry always increases customer credit)
            $credit = $transaction->net_amount ?? 0; // Net amount with tax
            $debit = 0;
            
            // Calculate Due/Reference
            // Use precomputed adjusted ids to avoid per-row DB queries
            $hasAdjustments = in_array($transaction->id, $adjustedIds);

            // If credit note is adjusted (has adjustment records), then due/reference is 0
            // If not adjusted, due/reference = MRP value (without tax)
            $dueReference = $hasAdjustments ? 0 : ($transaction->mrp_value ?? 0);
            
            // Update running balance with credit amount
            $runningBalance += $credit;
            
            // Determine type badge
            $typeBadge = 'C'; // Credit note
            $typeBadgeClass = 'bg-success';
          @endphp
          
          <tr data-transaction-id="{{ $transaction->id }}">
            <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
            <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('m/d/Y') : '-' }}</td>
            <td>
              <span class="fw-semibold">{{ $transaction->sr_no }}</span>
            </td>
            <td class="text-end">
              @if($debit > 0)
                <span class="text-danger fw-semibold">{{ number_format($debit, 2) }}</span>
              @endif
            </td>
            <td class="text-end">
              @if($credit > 0)
                <span class="text-success fw-semibold">{{ number_format($credit, 2) }}</span>
              @endif
            </td>
            <td class="text-end">
              @if($dueReference > 0)
                <span class="badge bg-warning text-dark">{{ number_format($dueReference, 2) }}</span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td class="text-center">
              <span class="badge {{ $typeBadgeClass }}">{{ $typeBadge }}</span>
            </td>
            <td class="text-end">
              <button type="button" class="btn btn-sm btn-outline-info view-details-btn" 
                      data-transaction-id="{{ $transaction->id }}"
                      data-sr-no="{{ $transaction->sr_no }}"
                      title="View Adjustment Details">
                <i class="bi bi-eye"></i>
              </button>
              <a href="{{ route('admin.breakage-expiry.modification') }}?transaction_id={{ $transaction->id }}" 
                 class="btn btn-sm btn-outline-warning" 
                 title="Edit in Modification">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button" 
                      class="btn btn-sm btn-outline-danger delete-transaction-btn" 
                      data-transaction-id="{{ $transaction->id }}"
                      data-sr-no="{{ $transaction->sr_no }}"
                      title="Delete Transaction">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No breakage/expiry transactions found for this customer
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- Pagination Footer -->
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</div>
      <div class="text-muted">Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</div>
    </div>
    @if($transactions->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="expiry-ledger-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="expiry-ledger-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="expiry-ledger-sentinel" data-next-url="{{ $transactions->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

<!-- Custom Adjustment Details Modal -->
<div id="adjustmentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; color: white; display: flex; justify-content: space-between; align-items: center;">
      <h5 style="margin: 0; font-weight: 600;">
        <i class="bi bi-receipt-cutoff me-2"></i>Credit Note Adjustment Details
      </h5>
      <button type="button" class="btn-close btn-close-white" onclick="closeAdjustmentModal()" style="cursor: pointer;"></button>
    </div>
    <div id="adjustment-details-content" style="padding: 25px; max-height: 60vh; overflow-y: auto;">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
    <div style="background: #f8f9fa; padding: 15px; border-top: 1px solid #dee2e6; text-align: right;">
      <button type="button" class="btn btn-secondary" onclick="closeAdjustmentModal()">Close</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables for expiry ledger page
let expiryLedgerPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Global function to close modal
function closeAdjustmentModal() {
  const modal = document.getElementById('adjustmentDetailsModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Global performSearch function for expiry ledger
window.performExpiryLedgerSearch = function() {
  if(isSearching) return;
  isSearching = true;
  
  // Ensure elements are initialized
  if (!expiryLedgerPageElements.filterForm) {
    expiryLedgerPageElements.filterForm = document.getElementById('ledger-filter-form');
  }
  if (!expiryLedgerPageElements.tbody) {
    expiryLedgerPageElements.tbody = document.getElementById('ledger-table-body');
  }
  if (!expiryLedgerPageElements.searchInput) {
    expiryLedgerPageElements.searchInput = document.getElementById('search');
  }
  
  const formData = new FormData(expiryLedgerPageElements.filterForm);
  const params = new URLSearchParams(formData);
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('search-loading');
  if(loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Add visual feedback
  if(expiryLedgerPageElements.searchInput) {
    expiryLedgerPageElements.searchInput.style.opacity = '0.6';
  }
  
  fetch(`{{ route('admin.customers.expiry-ledger', $customer) }}?${params.toString()}`, {
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
    expiryLedgerPageElements.tbody.innerHTML = '';
    if(realRows.length) {
      realRows.forEach(tr => expiryLedgerPageElements.tbody.appendChild(tr.cloneNode(true)));
    } else {
      expiryLedgerPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No breakage/expiry transactions found for this customer</td></tr>';
    }
    
    // Update summary cards
    const newCards = doc.querySelectorAll('.row.mb-3.g-2 .col-md-3');
    const currentCards = document.querySelectorAll('.row.mb-3.g-2 .col-md-3');
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
      if (typeof window.initExpiryLedgerInfiniteScroll === 'function') {
        window.initExpiryLedgerInfiniteScroll();
      }
    }
    
    // Reattach event listeners to new buttons
    window.reattachExpiryLedgerEventListeners();
  })
  .catch(error => {
    console.error('Error in performExpiryLedgerSearch:', error);
    if (expiryLedgerPageElements.tbody) {
      expiryLedgerPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
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
    if(expiryLedgerPageElements.searchInput) {
      expiryLedgerPageElements.searchInput.style.opacity = '1';
    }
    
    const s = document.getElementById('expiry-ledger-spinner');
    const t = document.getElementById('expiry-ledger-load-text');
    s && s.classList.add('d-none');
    t && (t.textContent = 'Scroll for more');
  });
};

// Global infinite scroll function for expiry ledger
window.initExpiryLedgerInfiniteScroll = function() {
  // Disconnect previous observer if exists
  if(observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('expiry-ledger-sentinel');
  const spinner = document.getElementById('expiry-ledger-spinner');
  const loadText = document.getElementById('expiry-ledger-load-text');
  
  // Ensure elements are initialized
  if (!expiryLedgerPageElements.tbody) {
    expiryLedgerPageElements.tbody = document.getElementById('ledger-table-body');
  }
  if (!expiryLedgerPageElements.filterForm) {
    expiryLedgerPageElements.filterForm = document.getElementById('ledger-filter-form');
  }
  
  if(!sentinel || !expiryLedgerPageElements.tbody) return;
  
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
      const formData = new FormData(expiryLedgerPageElements.filterForm);
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
      realRows.forEach(tr => expiryLedgerPageElements.tbody.appendChild(tr.cloneNode(true)));
      
      // Reattach event listeners to newly loaded rows
      window.reattachExpiryLedgerEventListeners();
      
      const newSentinel = doc.querySelector('#expiry-ledger-sentinel');
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

// Global function to reattach event listeners after AJAX content updates
window.reattachExpiryLedgerEventListeners = function() {
  // View transaction adjustment details
  document.querySelectorAll('.view-details-btn').forEach(btn => {
    // Remove existing listeners by cloning
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.addEventListener('click', function() {
      const transactionId = this.dataset.transactionId;
      const srNo = this.dataset.srNo || 'N/A';
      loadAdjustmentDetails(transactionId, srNo);
    });
  });
  
  // Delete transaction functionality
  document.querySelectorAll('.delete-transaction-btn').forEach(btn => {
    // Remove existing listeners by cloning
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.addEventListener('click', function() {
      const transactionId = this.dataset.transactionId;
      const srNo = this.dataset.srNo;
      
      if (confirm(`Are you sure you want to delete transaction ${srNo}?\n\nThis action cannot be undone.`)) {
        deleteTransaction(transactionId);
      }
    });
  });
};

/**
 * Load adjustment details via AJAX
 */
function loadAdjustmentDetails(transactionId, srNo) {
  const modal = document.getElementById('adjustmentDetailsModal');
  const contentDiv = document.getElementById('adjustment-details-content');
  
  // Show loading spinner
  contentDiv.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading adjustment details...</p>
    </div>
  `;
  
  // Show modal
  modal.style.display = 'flex';
  
  // Fetch adjustment details
  fetch(`{{ url('admin/breakage-expiry/transaction') }}/${transactionId}/adjustments`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load adjustment details');
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        displayAdjustmentDetails(data.adjustments, srNo);
      } else {
        throw new Error(data.message || 'Failed to load adjustment details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      contentDiv.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          ${error.message}
        </div>
      `;
    });
}

/**
 * Display adjustment details in modal
 */
function displayAdjustmentDetails(adjustments, srNo) {
  const contentDiv = document.getElementById('adjustment-details-content');
  
  let totalAdjusted = 0;
  let adjustmentHtml = '';
  
  if (adjustments && adjustments.length > 0) {
    adjustmentHtml = `
      <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196F3; border-radius: 4px;">
        <h6 style="margin: 0 0 5px 0; color: #1976D2;">
          <i class="bi bi-info-circle me-2"></i>Transaction: <strong>${escapeHtml(srNo)}</strong>
        </h6>
        <p style="margin: 0; color: #666; font-size: 14px;">Credit note adjusted against the following sale transactions</p>
      </div>
      
      <div class="table-responsive">
        <table class="table table-hover table-bordered mb-0">
          <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <tr>
              <th style="width: 60px;">S.No</th>
              <th>Trn. No.</th>
              <th style="width: 130px;">Date</th>
              <th class="text-end" style="width: 150px;">Amount</th>
            </tr>
          </thead>
          <tbody>
            ${adjustments.map((adj, idx) => {
              totalAdjusted += parseFloat(adj.adjusted_amount || 0);
              return `
                <tr>
                  <td>${idx + 1}</td>
                  <td><strong>${escapeHtml(adj.sale_invoice_no || 'S/' + adj.sale_transaction_id)}</strong></td>
                  <td>${formatDate(adj.adjustment_date)}</td>
                  <td class="text-end" style="color: #10b981; font-weight: 600;">₹ ${parseFloat(adj.adjusted_amount || 0).toFixed(2)}</td>
                </tr>
              `;
            }).join('')}
          </tbody>
          <tfoot style="background: #f8f9fa; font-weight: bold;">
            <tr>
              <td colspan="3" class="text-end" style="padding: 12px;">Total Adjusted Amount:</td>
              <td class="text-end" style="color: #10b981; font-size: 16px; padding: 12px;">₹ ${totalAdjusted.toFixed(2)}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    `;
  } else {
    adjustmentHtml = `
      <div style="text-align: center; padding: 40px 20px;">
        <i class="bi bi-exclamation-triangle" style="font-size: 48px; color: #ff9800; display: block; margin-bottom: 15px;"></i>
        <h6 style="color: #666; margin: 0 0 8px 0;">No Credit Note Adjustments</h6>
        <p style="color: #999; margin: 0; font-size: 14px;">Transaction <strong>${escapeHtml(srNo)}</strong> has not been adjusted against any sale invoices.</p>
      </div>
    `;
  }
  
  contentDiv.innerHTML = adjustmentHtml;
}

/**
 * Delete transaction via AJAX
 */
function deleteTransaction(transactionId) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  
  if (!csrfToken) {
    alert('CSRF token not found. Please refresh the page and try again.');
    return;
  }
  
  // Show loading state
  const btn = document.querySelector(`[data-transaction-id="${transactionId}"].delete-transaction-btn`);
  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
  
  fetch(`{{ url('admin/breakage-expiry') }}/${transactionId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
      _method: 'DELETE'
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Remove row from table
      const row = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
      if (row) {
        row.style.transition = 'opacity 0.3s';
        row.style.opacity = '0';
        setTimeout(() => {
          row.remove();
          
          // Check if table is empty
          const tbody = document.getElementById('ledger-table-body');
          if (tbody.children.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No breakage/expiry transactions found for this customer
                </td>
              </tr>
            `;
          }
        }, 300);
      }
      
      // Show success message
      showToast('success', 'Transaction deleted successfully');
    } else {
      throw new Error(data.message || 'Failed to delete transaction');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('danger', error.message);
    
    // Restore button
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  });
}

/**
 * Helper function to escape HTML
 */
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

/**
 * Helper function to format date
 */
function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit' 
  });
}

/**
 * Show toast notification
 */
function showToast(type, message) {
  // Try to use existing notification system if available
  if (window.crudNotification && typeof window.crudNotification.showToast === 'function') {
    window.crudNotification.showToast(type === 'success' ? 'success' : 'error', 
                                      type === 'success' ? 'Success' : 'Error', 
                                      message);
    return;
  }
  
  // Fallback to simple alert
  alert(message);
}

document.addEventListener('DOMContentLoaded', function() {
  // Initialize page elements
  expiryLedgerPageElements = {
    tbody: document.getElementById('ledger-table-body'),
    searchInput: document.getElementById('search'),
    clearSearchBtn: document.getElementById('clear-search'),
    filterForm: document.getElementById('ledger-filter-form'),
    tableWrapper: document.getElementById('ledger-table-wrapper'),
    searchLoading: document.getElementById('search-loading'),
    cardFooter: document.querySelector('.card-footer')
  };

  // Close modal when clicking outside
  const modal = document.getElementById('adjustmentDetailsModal');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeAdjustmentModal();
      }
    });
  }
  
  // Close modal on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeAdjustmentModal();
    }
  });
  
  // Clear search functionality
  if (expiryLedgerPageElements.clearSearchBtn) {
    expiryLedgerPageElements.clearSearchBtn.addEventListener('click', function() {
      if (expiryLedgerPageElements.searchInput) {
        expiryLedgerPageElements.searchInput.value = '';
        expiryLedgerPageElements.searchInput.focus();
        window.performExpiryLedgerSearch();
      }
    });
  }
  
  // Search input with debounce
  if(expiryLedgerPageElements.searchInput) {
    expiryLedgerPageElements.searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performExpiryLedgerSearch, 300);
    });
  }
  
  // AJAX Filter Form Submission
  if (expiryLedgerPageElements.filterForm) {
    expiryLedgerPageElements.filterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      window.performExpiryLedgerSearch();
    });
  }
  
  // Initialize event listeners for buttons
  window.reattachExpiryLedgerEventListeners();
  
  // Initialize infinite scroll on page load
  const initialSentinel = document.getElementById('expiry-ledger-sentinel');
  if (initialSentinel) {
    window.initExpiryLedgerInfiniteScroll();
  }
});
</script>
@endpush
