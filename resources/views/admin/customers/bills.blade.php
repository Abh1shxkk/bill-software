@extends('layouts.admin')
@section('title', 'List of Bills - ' . $customer->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-receipt me-2"></i> List of Bills - {{ $customer->name }}
    </h4>
    <div class="text-muted small">Party: {{ $customer->name }} | Code: {{ $customer->code ?? 'N/A' }}</div>
  </div>
  <div>
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
            <small class="text-muted d-block">Total Bills</small>
            <h5 class="mb-0 text-primary">{{ $bills->total() }}</h5>
          </div>
          <div class="text-primary opacity-50">
            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Sale Amount</small>
            <h5 class="mb-0 text-success">{{ number_format($totalSaleAmount, 2) }}</h5>
          </div>
          <div class="text-success opacity-50">
            <i class="bi bi-cart-check" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Return Amount</small>
            <h5 class="mb-0 text-danger">{{ number_format($totalReturnAmount, 2) }}</h5>
          </div>
          <div class="text-danger opacity-50">
            <i class="bi bi-arrow-return-left" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Net Total</small>
            <h5 class="mb-0 {{ $netTotal >= 0 ? 'text-info' : 'text-warning' }}">{{ number_format($netTotal, 2) }}</h5>
          </div>
          <div class="{{ $netTotal >= 0 ? 'text-info' : 'text-warning' }} opacity-50">
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
    <form method="GET" action="{{ route('admin.customers.bills', $customer) }}" class="row g-3" id="bills-filter-form">
      <div class="col-md-2">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
      </div>
      <div class="col-md-2">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
      </div>
      <div class="col-md-2">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
          <option value="">All</option>
          <option value="sale" {{ request('status') == 'sale' ? 'selected' : '' }}>Sale Only</option>
          <option value="return" {{ request('status') == 'return' ? 'selected' : '' }}>Return Only</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="search" class="form-label">Search Bill No</label>
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

<!-- Bills Table -->
<div class="card shadow-sm border-0 rounded">
  <div class="table-responsive" id="bills-table-wrapper" style="position: relative; min-height: 400px;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">Sr</th>
          <th style="width: 130px;">Bill No</th>
          <th style="width: 110px;">Date</th>
          <th>Name</th>
          <th>Address</th>
          <th class="text-end" style="width: 130px;">Amount</th>
          <th class="text-center" style="width: 80px;">Type</th>
          <th class="text-end" style="width: 100px;">Actions</th>
        </tr>
      </thead>
      <tbody id="bills-table-body">
        @forelse($bills as $bill)
          <tr data-bill-id="{{ $bill->id }}" data-bill-type="{{ $bill->type }}">
            <td>{{ ($bills->currentPage() - 1) * $bills->perPage() + $loop->iteration }}</td>
            <td>
              <span class="fw-semibold">{{ $bill->series ?? '' }}{{ $bill->series ? ' / ' : '' }}{{ $bill->bill_no }}</span>
            </td>
            <td>{{ \Carbon\Carbon::parse($bill->date)->format('d-M-y') }}</td>
            <td>{{ $customer->name }}</td>
            <td class="text-truncate" style="max-width: 250px;" title="{{ $customer->address ?? '' }}">
              {{ $customer->address ?? '-' }}
            </td>
            <td class="text-end">
              <span class="{{ $bill->type == 'sale' ? 'text-success' : 'text-danger' }} fw-semibold">
                {{ $bill->type == 'return' ? '-' : '' }}{{ number_format($bill->net_amount, 2) }}
              </span>
            </td>
            <td class="text-center">
              @if($bill->type == 'sale')
                <span class="badge bg-success">Sale</span>
              @else
                <span class="badge bg-danger">Return</span>
              @endif
            </td>
            <td class="text-end">
              <button type="button" class="btn btn-sm btn-outline-primary view-bill-btn" 
                      data-bill-id="{{ $bill->id }}"
                      data-bill-type="{{ $bill->type }}"
                      title="View Details">
                <i class="bi bi-eye"></i>
              </button>
              @if($bill->type == 'sale')
                <a href="{{ route('admin.sale.modification') }}?transaction_id={{ $bill->id }}" 
                   class="btn btn-sm btn-outline-warning" 
                   title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @else
                <a href="{{ route('admin.sale-return.modification') }}?transaction_id={{ $bill->id }}" 
                   class="btn btn-sm btn-outline-warning" 
                   title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No bills found for this customer
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $bills->firstItem() ?? 0 }}-{{ $bills->lastItem() ?? 0 }} of {{ $bills->total() }}</div>
      <div class="text-muted">Page {{ $bills->currentPage() }} of {{ $bills->lastPage() }}</div>
    </div>
    @if($bills->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="bills-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="bills-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="bills-sentinel" data-next-url="{{ $bills->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@endsection

<!-- Bill Detail Modal -->
<div class="custom-modal-overlay"></div>
<div id="billDetailModal" class="custom-modal">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5><i class="bi bi-receipt me-2"></i>Bill Details</h5>
      <button type="button" class="custom-modal-close" onclick="closeBillModal()">&times;</button>
    </div>
    <div class="custom-modal-body" id="billDetailContent">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading bill details...</p>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeBillModal()">Close</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Global variables for bills page
let billsPageElements = {};
let searchTimeout;
let isLoading = false;
let observer = null;
let isSearching = false;

// Global performSearch function for bills
window.performBillsSearch = function() {
  if(isSearching) return;
  isSearching = true;
  
  // Ensure elements are initialized
  if (!billsPageElements.filterForm) {
    billsPageElements.filterForm = document.getElementById('bills-filter-form');
  }
  if (!billsPageElements.tbody) {
    billsPageElements.tbody = document.getElementById('bills-table-body');
  }
  if (!billsPageElements.searchInput) {
    billsPageElements.searchInput = document.getElementById('search');
  }
  
  const formData = new FormData(billsPageElements.filterForm);
  const params = new URLSearchParams(formData);
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('search-loading');
  if(loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Add visual feedback
  if(billsPageElements.searchInput) {
    billsPageElements.searchInput.style.opacity = '0.6';
  }
  
  fetch(`{{ route('admin.customers.bills', $customer) }}?${params.toString()}`, {
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
    const newRows = doc.querySelectorAll('#bills-table-body tr');
    const realRows = Array.from(newRows).filter(tr => {
      const tds = tr.querySelectorAll('td');
      return !(tds.length === 1 && tr.querySelector('td[colspan]'));
    });
    
    // Clear and update table
    billsPageElements.tbody.innerHTML = '';
    if(realRows.length) {
      realRows.forEach(tr => billsPageElements.tbody.appendChild(tr.cloneNode(true)));
    } else {
      billsPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No bills found for this customer</td></tr>';
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
      if (typeof window.initBillsInfiniteScroll === 'function') {
        window.initBillsInfiniteScroll();
      }
    }
    
    // Reattach event listeners
    window.reattachBillsEventListeners();
  })
  .catch(error => {
    console.error('Error in performBillsSearch:', error);
    if (billsPageElements.tbody) {
      billsPageElements.tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
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
    if(billsPageElements.searchInput) {
      billsPageElements.searchInput.style.opacity = '1';
    }
    
    const s = document.getElementById('bills-spinner');
    const t = document.getElementById('bills-load-text');
    s && s.classList.add('d-none');
    t && (t.textContent = 'Scroll for more');
  });
};

// Global infinite scroll function for bills
window.initBillsInfiniteScroll = function() {
  // Disconnect previous observer if exists
  if(observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('bills-sentinel');
  const spinner = document.getElementById('bills-spinner');
  const loadText = document.getElementById('bills-load-text');
  
  // Ensure elements are initialized
  if (!billsPageElements.tbody) {
    billsPageElements.tbody = document.getElementById('bills-table-body');
  }
  if (!billsPageElements.filterForm) {
    billsPageElements.filterForm = document.getElementById('bills-filter-form');
  }
  
  if(!sentinel || !billsPageElements.tbody) return;
  
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
      const formData = new FormData(billsPageElements.filterForm);
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
      const newRows = doc.querySelectorAll('#bills-table-body tr');
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      realRows.forEach(tr => billsPageElements.tbody.appendChild(tr.cloneNode(true)));
      
      // Reattach event listeners to newly loaded rows
      window.reattachBillsEventListeners();
      
      const newSentinel = doc.querySelector('#bills-sentinel');
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
window.reattachBillsEventListeners = function() {
  // View bill details
  document.querySelectorAll('.view-bill-btn').forEach(btn => {
    // Remove existing listeners by cloning
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.addEventListener('click', function() {
      const billId = this.dataset.billId;
      const billType = this.dataset.billType;
      viewBillDetails(billId, billType);
    });
  });
};

/**
 * View bill details in modal
 */
function viewBillDetails(billId, billType) {
  const modal = document.getElementById('billDetailModal');
  const overlay = document.querySelector('.custom-modal-overlay');
  const contentDiv = document.getElementById('billDetailContent');
  
  // Show modal with animation
  overlay.style.display = 'block';
  modal.style.display = 'block';
  
  setTimeout(() => {
    modal.classList.add('show');
    overlay.classList.add('show');
  }, 10);
  
  // Show loading
  contentDiv.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading bill details...</p>
    </div>
  `;
  
  // Fetch bill details based on type
  let url = '';
  if (billType === 'sale') {
    url = `{{ url('admin/customers') }}/{{ $customer->id }}/ledger/sale/${billId}`;
  } else {
    url = `{{ url('admin/customers') }}/{{ $customer->id }}/ledger/sale-return/${billId}`;
  }
  
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
    let headerHtml = '';
    if (billType === 'sale') {
      headerHtml = `
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="mb-1"><strong>Customer:</strong> <span class="text-primary">${data.customer_name || 'N/A'}</span></p>
            <p class="mb-1"><strong>Invoice No:</strong> ${data.invoice_no || 'N/A'}</p>
            <p class="mb-1"><strong>Date:</strong> ${data.sale_date || 'N/A'}</p>
          </div>
          <div class="col-md-6 text-end">
            <p class="mb-1"><strong>Net Amount:</strong> <span class="text-success fs-5">₹ ${parseFloat(data.net_amount || 0).toFixed(2)}</span></p>
          </div>
        </div>
      `;
    } else {
      headerHtml = `
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="mb-1"><strong>Customer:</strong> <span class="text-primary">${data.customer_name || 'N/A'}</span></p>
            <p class="mb-1"><strong>SR No:</strong> ${data.sr_no || 'N/A'}</p>
            <p class="mb-1"><strong>Return Date:</strong> ${data.return_date || 'N/A'}</p>
            <p class="mb-1"><strong>Original Invoice:</strong> ${data.original_invoice_no || 'N/A'}</p>
          </div>
          <div class="col-md-6 text-end">
            <p class="mb-1"><strong>Net Amount:</strong> <span class="text-danger fs-5">₹ ${parseFloat(data.net_amount || 0).toFixed(2)}</span></p>
          </div>
        </div>
      `;
    }
    
    contentDiv.innerHTML = `
      ${headerHtml}
      
      <div class="table-responsive">
        <table class="table table-bordered table-sm">
          <thead class="table-light">
            <tr>
              <th>Item Name</th>
              <th>Pack</th>
              <th>Batch</th>
              <th>Exp.</th>
              <th class="text-end">Qty</th>
              <th class="text-end">F.Qty</th>
              <th class="text-end">Rate</th>
              <th class="text-end">Dis.%</th>
              <th class="text-end">MRP</th>
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
                <td class="text-end">${item.free_qty || 0}</td>
                <td class="text-end">${parseFloat(item.rate || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.discount || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
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
    contentDiv.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Error loading bill details. Please try again.
      </div>
    `;
  });
}

function closeBillModal() {
  const modal = document.getElementById('billDetailModal');
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

document.addEventListener('DOMContentLoaded', function() {
  // Initialize page elements
  billsPageElements = {
    tbody: document.getElementById('bills-table-body'),
    searchInput: document.getElementById('search'),
    clearSearchBtn: document.getElementById('clear-search'),
    filterForm: document.getElementById('bills-filter-form'),
    tableWrapper: document.getElementById('bills-table-wrapper'),
    searchLoading: document.getElementById('search-loading'),
    cardFooter: document.querySelector('.card-footer')
  };
  
  // Clear search functionality
  if (billsPageElements.clearSearchBtn) {
    billsPageElements.clearSearchBtn.addEventListener('click', function() {
      if (billsPageElements.searchInput) {
        billsPageElements.searchInput.value = '';
        billsPageElements.searchInput.focus();
        window.performBillsSearch();
      }
    });
  }
  
  // Search input with debounce
  if(billsPageElements.searchInput) {
    billsPageElements.searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performBillsSearch, 300);
    });
  }
  
  // AJAX Filter Form Submission
  if (billsPageElements.filterForm) {
    billsPageElements.filterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      window.performBillsSearch();
    });
  }
  
  // Initialize event listeners for buttons
  window.reattachBillsEventListeners();
  
  // Initialize infinite scroll on page load
  const initialSentinel = document.getElementById('bills-sentinel');
  if (initialSentinel) {
    window.initBillsInfiniteScroll();
  }
  
  // Close modal on overlay click
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('custom-modal-overlay')) {
      closeBillModal();
    }
  });
  
  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeBillModal();
    }
  });
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
@endpush
