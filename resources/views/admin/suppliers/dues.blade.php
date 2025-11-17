@extends('layouts.admin')
@section('title', 'Due List - ' . $supplier->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-receipt me-2"></i> Supplier Due List - {{ $supplier->name }}
    </h4>
    <div class="text-muted small">Code: {{ $supplier->code ?? 'N/A' }}</div>
  </div>
  <div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
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
            <small class="text-muted d-block">Ledger Balance</small>
            <h5 class="mb-0 text-success">{{ number_format($totalDue, 2) }} Cr</h5>
          </div>
          <div class="text-success opacity-50">
            <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Due List Balance</small>
            <h5 class="mb-0 text-success">{{ number_format($totalDue, 2) }} Cr</h5>
          </div>
          <div class="text-success opacity-50">
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
            <small class="text-muted d-block">Total Invoices</small>
            <h5 class="mb-0 text-primary">{{ $dues->total() }}</h5>
          </div>
          <div class="text-primary opacity-50">
            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
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
            <small class="text-muted d-block">Pending Amount</small>
            <h5 class="mb-0 text-warning">{{ number_format($totalDue, 2) }}</h5>
          </div>
          <div class="text-warning opacity-50">
            <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Due List Table -->
<div class="card shadow-sm border-0 rounded">
  <div class="table-responsive" id="dues-table-wrapper" style="position: relative; min-height: 400px;">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">Sr.No</th>
          <th style="width: 130px;">Trans No.</th>
          <th style="width: 110px;">Date</th>
          <th style="width: 80px;">Days</th>
          <th style="width: 130px;">Due Date</th>
          <th style="width: 80px;">Days</th>
          <th class="text-end" style="width: 130px;">Trn.Amt.</th>
          <th class="text-end" style="width: 130px;">Debit</th>
          <th class="text-end" style="width: 130px;">Credit</th>
          <th style="width: 90px;">Hold</th>
          <th class="text-end" style="width: 110px;">Actions</th>
        </tr>
      </thead>
      <tbody id="dues-table-body">
        @if($dues->count() > 0)
          @foreach($dues as $due)
            @php
              $billDate = \Carbon\Carbon::parse($due->bill_date);
              $today = \Carbon\Carbon::today();
              $daysFromBill = $billDate->diffInDays($today);
              
              // Calculate due date (assuming 30 days credit period)
              $dueDate = $billDate->copy()->addDays(30);
              $daysFromDue = $today->diffInDays($dueDate);
              $isOverdue = $today->greaterThan($dueDate);
            @endphp
            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
              <td>{{ ($dues->currentPage() - 1) * $dues->perPage() + $loop->iteration }}</td>
              <td>PB/{{ $due->trn_no }}</td>
              <td>{{ $billDate->format('d-M-y') }}</td>
              <td>{{ $daysFromBill }}</td>
              <td>{{ $dueDate->format('d-M-y') }}</td>
              <td>{{ $isOverdue ? $daysFromDue : $daysFromDue }}</td>
              <td class="text-end">{{ number_format($due->net_amount, 2) }}</td>
              <td class="text-end"></td>
              <td class="text-end text-success fw-semibold">{{ number_format($due->net_amount, 2) }}</td>
              <td></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary view-due-btn" 
                        data-purchase-id="{{ $due->id }}" 
                        title="View Details">
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="11" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No due invoices found
            </td>
          </tr>
        @endif
        
      </tbody>
    </table>
    
    @if($dues->hasMorePages())
    <div id="dues-sentinel" data-next-url="{{ $dues->nextPageUrl() }}" style="height: 1px;"></div>
    <div class="text-center py-3">
      <div class="d-none" id="dues-spinner">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <small class="text-muted" id="dues-load-text">Scroll for more</small>
    </div>
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
      <button type="button" class="custom-modal-close" onclick="closePurchaseModal()">
        <i class="bi bi-x-lg"></i>
      </button>
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
  // View Purchase Detail Modal
  document.addEventListener('click', function(e) {
    if (e.target.closest('.view-due-btn')) {
      const btn = e.target.closest('.view-due-btn');
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
});

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

// Infinite scroll functionality for load more
let observer = null;
let isLoading = false;

window.initDuesInfiniteScroll = function() {
  if (observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('dues-sentinel');
  const spinner = document.getElementById('dues-spinner');
  const loadText = document.getElementById('dues-load-text');
  const tbody = document.getElementById('dues-table-body');

  if (!sentinel || !tbody) return;

  isLoading = false;

  async function loadMore() {
    if (isLoading) return;
    const nextUrl = sentinel.getAttribute('data-next-url');
    if (!nextUrl) return;

    isLoading = true;
    spinner && spinner.classList.remove('d-none');
    loadText && (loadText.textContent = 'Loading...');

    try {
      const res = await fetch(nextUrl, { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
      });
      const html = await res.text();

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#dues-table-body tr');
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      
      realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));

      const newSentinel = doc.querySelector('#dues-sentinel');
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
      console.error('Load more error:', e);
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
  }, { threshold: 0.1 });

  observer.observe(sentinel);
};

// Initialize on page load
if (document.getElementById('dues-sentinel')) {
  window.initDuesInfiniteScroll();
}
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
    .card-footer, .btn, .text-muted {
      display: none !important;
    }
  }
</style>
@endpush
