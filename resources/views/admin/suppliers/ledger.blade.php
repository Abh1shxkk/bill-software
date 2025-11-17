@extends('layouts.admin')
@section('title', 'Ledger - ' . $supplier->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-journal-text me-2"></i> Supplier Ledger - {{ $supplier->name }}
    </h4>
    <div class="text-muted small">Party: {{ $supplier->name }} | Code: {{ $supplier->code ?? 'N/A' }}</div>
  </div>
  <div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
    </a>
  </div>
</div>

@php
  $currentBalance = $openingBalance + $totalCredit - $totalDebit;
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
              $closingBalance = $openingBalance + $totalCredit - $totalDebit;
            @endphp
            <h5 class="mb-0 text-{{ $closingBalance >= 0 ? 'success' : 'danger' }}">{{ number_format(abs($closingBalance), 2) }} {{ $closingBalance >= 0 ? 'Cr' : 'Dr' }}</h5>
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
    <form method="GET" action="{{ route('admin.suppliers.ledger', $supplier) }}" class="row g-3" id="ledger-filter-form">
      <div class="col-md-3">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
      </div>
      <div class="col-md-3">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
      </div>
      <div class="col-md-4">
        <label for="search" class="form-label">Search by Vou. No / Bill No</label>
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
          <th style="width: 110px;">Vou. No.</th>
          <th>Account Name</th>
          <th class="text-end" style="width: 110px;">Debit</th>
          <th class="text-end" style="width: 110px;">Credit</th>
          <th class="text-end" style="width: 130px;">Balance</th>
          <th class="text-end" style="width: 110px;">Actions</th>
        </tr>
      </thead>
      <tbody id="ledger-table-body">
        @if($ledgers->count() > 0)
          @foreach($ledgers as $ledger)
            <tr>
              <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $loop->iteration }}</td>
              <td>{{ \Carbon\Carbon::parse($ledger->transaction_date)->format('m/d/Y') }}</td>
              <td>{{ $ledger->trans_no ?? '-' }}</td>
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
              <td class="text-end">{{ number_format($ledger->running_balance, 2) }} Cr</td>
              <td class="text-end">
                @if(isset($ledger->purchase_transaction_id))
                  <button type="button" class="btn btn-sm btn-outline-primary view-purchase-btn" 
                          data-purchase-id="{{ $ledger->purchase_transaction_id }}" 
                          title="View Purchase">
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

<!-- Custom Purchase Detail Modal -->
<div class="custom-modal-overlay"></div>
<div id="purchaseDetailModal" class="custom-modal">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5><i class="bi bi-receipt me-2"></i>Purchase Transaction Details</h5>
      <button type="button" class="custom-modal-close" onclick="closePurchaseModal()">&times;</button>
    </div>
    <div class="custom-modal-body" id="purchaseDetailContent">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading purchase details...</p>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closePurchaseModal()">Close</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search');
  const clearSearchBtn = document.getElementById('clear-search');
  const tbody = document.getElementById('ledger-table-body');
  const sentinel = document.getElementById('ledger-sentinel');
  const spinner = document.getElementById('ledger-spinner');
  const loadText = document.getElementById('ledger-load-text');
  const filterForm = document.getElementById('ledger-filter-form');
  const tableWrapper = document.getElementById('ledger-table-wrapper');
  const searchLoading = document.getElementById('search-loading');
  const cardFooter = document.querySelector('.card-footer');
  
  let isLoading = false;
  let observer = null;
  
  // Clear search functionality
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
      }
    });
  }

  // View Purchase Detail Modal
  document.addEventListener('click', function(e) {
    if (e.target.closest('.view-purchase-btn')) {
      const btn = e.target.closest('.view-purchase-btn');
      const purchaseId = btn.getAttribute('data-purchase-id');
      
      // Show modal with slide-in animation
      const modal = document.getElementById('purchaseDetailModal');
      const overlay = document.querySelector('.custom-modal-overlay');
      
      overlay.style.display = 'block';
      modal.style.display = 'block';
      
      // Trigger animation
      setTimeout(() => {
        modal.classList.add('show');
        overlay.classList.add('show');
      }, 10);
      
      // Reset content
      document.getElementById('purchaseDetailContent').innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading purchase details...</p>
        </div>
      `;
      
      // Fetch purchase details
      const url = `{{ url('admin/purchase') }}/${purchaseId}/show`;
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
        const content = document.getElementById('purchaseDetailContent');
        content.innerHTML = `
          <div class="row mb-3">
            <div class="col-md-6">
              <p class="mb-1"><strong>Name:</strong> <span class="text-primary">${data.supplier_name || 'N/A'}</span></p>
              <p class="mb-1"><strong>Inv.No:</strong> ${data.bill_no || 'N/A'}</p>
              <p class="mb-1"><strong>Date:</strong> ${data.bill_date || 'N/A'}</p>
            </div>
          </div>
          
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead class="table-light">
                <tr>
                  <th>Item Name</th>
                  <th>Pack</th>
                  <th>Batch</th>
                  <th>Exp.</th>
                  <th class="text-end">Qty.</th>
                  <th class="text-end">F.Qty.</th>
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
        document.getElementById('purchaseDetailContent').innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Error loading purchase details. Please try again.
          </div>
        `;
      });
    }
  });

  // AJAX Filter Form Submission
  if (filterForm) {
    filterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      if (searchLoading) searchLoading.style.display = 'flex';
      
      try {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = new URL(filterForm.action, window.location.origin);
        
        params.forEach((value, key) => {
          if (value) url.searchParams.set(key, value);
        });

        const res = await fetch(url.toString(), { 
          headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        });
        const html = await res.text();

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Update table body
        const newTbody = tempDiv.querySelector('#ledger-table-body');
        if (newTbody && tbody) {
          tbody.innerHTML = newTbody.innerHTML;
        }

        // Update date fields
        const newFromDate = tempDiv.querySelector('#from_date');
        const newToDate = tempDiv.querySelector('#to_date');
        const newSearch = tempDiv.querySelector('#search');
        
        if (newFromDate) document.getElementById('from_date').value = newFromDate.value;
        if (newToDate) document.getElementById('to_date').value = newToDate.value;
        if (newSearch) document.getElementById('search').value = newSearch.value;

        // Update summary cards
        const newCards = tempDiv.querySelectorAll('.row.mb-3.g-2 .col-md-2');
        const currentCards = document.querySelectorAll('.row.mb-3.g-2 .col-md-2');
        newCards.forEach((newCard, index) => {
          if (currentCards[index]) {
            currentCards[index].innerHTML = newCard.innerHTML;
          }
        });

        // Update pagination footer
        const newFooter = tempDiv.querySelector('.card-footer');
        if (newFooter && cardFooter) {
          cardFooter.innerHTML = newFooter.innerHTML;
        }

        // Reinitialize infinite scroll
        const newSentinel = document.getElementById('ledger-sentinel');
        if (newSentinel) {
          initInfiniteScroll();
        }

      } catch (e) {
        console.error('Filter error:', e);
        alert('Error loading filtered data. Please try again.');
      } finally {
        if (searchLoading) searchLoading.style.display = 'none';
      }
    });
  }

  // Infinite scroll functionality
  function initInfiniteScroll() {
    if (observer) {
      observer.disconnect();
    }

    const currentSentinel = document.getElementById('ledger-sentinel');
    const currentSpinner = document.getElementById('ledger-spinner');
    const currentLoadText = document.getElementById('ledger-load-text');
    const currentTbody = document.getElementById('ledger-table-body');

    if (!currentSentinel || !currentTbody) return;

    isLoading = false;

    async function loadMore() {
      if (isLoading) return;
      const nextUrl = currentSentinel.getAttribute('data-next-url');
      if (!nextUrl) return;

      isLoading = true;
      currentSpinner && currentSpinner.classList.remove('d-none');
      currentLoadText && (currentLoadText.textContent = 'Loading...');

      try {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = new URL(nextUrl, window.location.origin);

        params.forEach((value, key) => {
          if (value) url.searchParams.set(key, value);
        });

        const res = await fetch(url.toString(), { 
          headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        });
        const html = await res.text();

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const tempTbody = tempDiv.querySelector('#ledger-table-body');

        if (tempTbody) {
          const newRows = tempTbody.querySelectorAll('tr');
          const realRows = Array.from(newRows).filter(tr => {
            const tds = tr.querySelectorAll('td');
            return !(tds.length === 1 && tr.querySelector('td[colspan]'));
          });
          
          realRows.forEach(tr => currentTbody.appendChild(tr.cloneNode(true)));
        }

        const newSentinel = tempDiv.querySelector('#ledger-sentinel');
        if (newSentinel) {
          currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
          currentSpinner && currentSpinner.classList.add('d-none');
          currentLoadText && (currentLoadText.textContent = 'Scroll for more');
          isLoading = false;
        } else {
          observer.disconnect();
          currentSentinel.remove();
          currentSpinner && currentSpinner.remove();
          currentLoadText && currentLoadText.remove();
        }
      } catch (e) {
        console.error('Load more error:', e);
        currentSpinner && currentSpinner.classList.add('d-none');
        currentLoadText && (currentLoadText.textContent = 'Error loading');
        isLoading = false;
      }
    }

    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          loadMore();
        }
      });
    }, { rootMargin: '300px' });

    observer.observe(currentSentinel);
  }

  // Initialize on page load
  const initialSentinel = document.getElementById('ledger-sentinel');
  if (initialSentinel) {
    initInfiniteScroll();
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
function closePurchaseModal() {
  const modal = document.getElementById('purchaseDetailModal');
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
    closePurchaseModal();
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closePurchaseModal();
  }
});
</script>
@endpush
