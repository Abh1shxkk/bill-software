@extends('layouts.admin')
@section('title', 'List of Bills - ' . $supplier->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-receipt-cutoff me-2"></i> List Of Bills - {{ $supplier->name }}
    </h4>
    <div class="text-muted small">Code: {{ $supplier->code ?? 'N/A' }}</div>
  </div>
  <div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>
</div>

<!-- Filter Section -->
<div class="card shadow-sm border-0 rounded mb-3">
  <div class="card-body py-2">
    <form id="billFilterForm" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small mb-1">From Date</label>
        <input type="date" class="form-control form-control-sm filter-input" id="from_date" name="from_date" value="{{ $fromDate }}">
      </div>
      <div class="col-md-4">
        <label class="form-label small mb-1">To Date</label>
        <input type="date" class="form-control form-control-sm filter-input" id="to_date" name="to_date" value="{{ $toDate }}">
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-sm w-100" id="filterBtn">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="window.print()">
          <i class="bi bi-printer me-1"></i> Print (F7)
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Total Section -->
<div class="card shadow-sm border-0 rounded mb-3">
  <div class="card-body py-2">
    <div class="d-flex justify-content-end align-items-center">
      <h5 class="mb-0">
        <span class="text-muted">Total : </span>
        <span class="text-danger fw-bold" id="totalAmount">{{ number_format($totalAmount, 2) }}</span>
      </h5>
    </div>
  </div>
</div>

<!-- Bills Table -->
<div class="card shadow-sm border-0 rounded">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">Sr</th>
          <th style="width: 150px;">BillNo</th>
          <th style="width: 120px;">Date</th>
          <th>Name</th>
          <th>Address</th>
          <th class="text-end" style="width: 130px;">Amount</th>
        </tr>
      </thead>
      <tbody id="bills-table-body">
        @foreach($bills as $bill)
          <tr>
            <td>{{ ($bills->currentPage() - 1) * $bills->perPage() + $loop->iteration }}</td>
            <td>PB / {{ $bill->trn_no }}</td>
            <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d-M-y') }}</td>
            <td>{{ $bill->supplier->name ?? 'N/A' }}</td>
            <td>{{ $bill->supplier->address ?? 'N/A' }}</td>
            <td class="text-end">{{ number_format($bill->net_amount, 2) }}</td>
          </tr>
        @endforeach
        
        @if($bills->isEmpty())
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              No bills found for selected period
            </td>
          </tr>
        @endif
      </tbody>
    </table>
    
    @if($bills->hasMorePages())
    <div id="bills-sentinel" data-next-url="{{ $bills->nextPageUrl() }}" style="height: 1px;"></div>
    <div class="text-center py-3">
      <div class="d-none" id="bills-spinner">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <small class="text-muted" id="bills-load-text">Scroll for more</small>
    </div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
let observer = null;
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
  const filterBtn = document.getElementById('filterBtn');
  const filterForm = document.getElementById('billFilterForm');
  const tbody = document.getElementById('bills-table-body');
  const tableWrapper = document.querySelector('.table-responsive');
  
  // Create loading overlay
  const loadingOverlay = document.createElement('div');
  loadingOverlay.id = 'filter-loading';
  loadingOverlay.style.cssText = 'display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;';
  loadingOverlay.innerHTML = `
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  `;
  if (tableWrapper) {
    tableWrapper.style.position = 'relative';
    tableWrapper.appendChild(loadingOverlay);
  }
  
  // Filter function
  async function applyFilter() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    
    console.log('Applying filter with:', { fromDate, toDate });
    
    // Show loading overlay
    if (loadingOverlay) loadingOverlay.style.display = 'flex';
    
    try {
      const params = new URLSearchParams();
      if (fromDate) params.set('from_date', fromDate);
      if (toDate) params.set('to_date', toDate);
      
      const url = `{{ route('admin.suppliers.bills', $supplier) }}?${params.toString()}`;
      console.log('Fetching URL:', url);
      
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        }
      });
      
      console.log('Response status:', res.status);
      
      const html = await res.text();
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      
      // Update table body
      const newTbody = tempDiv.querySelector('#bills-table-body');
      if (newTbody && tbody) {
        console.log('Updating table body');
        tbody.innerHTML = newTbody.innerHTML;
      } else {
        console.error('New tbody not found!');
      }
      
      // Update total amount
      const newTotal = tempDiv.querySelector('#totalAmount');
      const currentTotal = document.getElementById('totalAmount');
      if (newTotal && currentTotal) {
        console.log('Updating total from', currentTotal.textContent, 'to', newTotal.textContent);
        currentTotal.textContent = newTotal.textContent;
      } else {
        console.error('Total element not found!');
      }
      
      // Update date inputs
      const newFromDate = tempDiv.querySelector('#from_date');
      const newToDate = tempDiv.querySelector('#to_date');
      
      if (newFromDate) document.getElementById('from_date').value = newFromDate.value;
      if (newToDate) document.getElementById('to_date').value = newToDate.value;
      
      // Remove old sentinel and pagination elements
      const oldSentinel = document.getElementById('bills-sentinel');
      const oldSpinner = document.getElementById('bills-spinner');
      
      if (oldSentinel) oldSentinel.remove();
      if (oldSpinner) oldSpinner.parentElement?.remove();
      
      // Add new sentinel if exists
      const newSentinel = tempDiv.querySelector('#bills-sentinel');
      const newPaginationDiv = tempDiv.querySelector('#bills-sentinel')?.parentElement;
      
      if (newSentinel && newPaginationDiv && tableWrapper) {
        tableWrapper.appendChild(newSentinel.cloneNode(true));
        tableWrapper.appendChild(newPaginationDiv.cloneNode(true));
      }
      
      // Reinitialize infinite scroll
      setTimeout(() => {
        if (document.getElementById('bills-sentinel')) {
          window.initBillsInfiniteScroll();
        }
      }, 100);
      
    } catch (error) {
      console.error('Filter error:', error);
      alert('Error loading filtered data. Please try again.');
    } finally {
      if (loadingOverlay) loadingOverlay.style.display = 'none';
    }
  }
  
  // Filter button click
  if (filterBtn) {
    filterBtn.addEventListener('click', function(e) {
      e.preventDefault();
      applyFilter();
    });
  }
  
  // Enter key on filter inputs
  const filterInputs = document.querySelectorAll('.filter-input');
  filterInputs.forEach(input => {
    input.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        applyFilter();
      }
    });
  });
  
  // Initialize infinite scroll
  window.initBillsInfiniteScroll();
});

// Infinite scroll functionality
window.initBillsInfiniteScroll = function() {
  if (observer) {
    observer.disconnect();
  }

  const sentinel = document.getElementById('bills-sentinel');
  const spinner = document.getElementById('bills-spinner');
  const loadText = document.getElementById('bills-load-text');
  const tbody = document.getElementById('bills-table-body');

  if (!sentinel || !tbody) {
    console.log('Bills infinite scroll: Sentinel or tbody not found');
    return;
  }

  console.log('Bills infinite scroll initialized', {
    nextUrl: sentinel.getAttribute('data-next-url'),
    sentinelExists: !!sentinel
  });

  isLoading = false;

  async function loadMore() {
    if (isLoading) {
      console.log('Already loading, skipping...');
      return;
    }
    const nextUrl = sentinel.getAttribute('data-next-url');
    if (!nextUrl) {
      console.log('No next URL found');
      return;
    }

    console.log('Loading more bills from:', nextUrl);
    isLoading = true;
    spinner && spinner.classList.remove('d-none');
    loadText && (loadText.textContent = 'Loading...');

    try {
      const fromDate = document.getElementById('from_date')?.value || '';
      const toDate = document.getElementById('to_date')?.value || '';
      
      const url = new URL(nextUrl, window.location.origin);
      if (fromDate) url.searchParams.set('from_date', fromDate);
      if (toDate) url.searchParams.set('to_date', toDate);

      const res = await fetch(url.toString(), { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
      });
      const html = await res.text();

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#bills-table-body tr');
      
      console.log(`Found ${newRows.length} new rows`);
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
      });
      
      console.log(`Adding ${realRows.length} real rows to table`);
      realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));

      const newSentinel = doc.querySelector('#bills-sentinel');
      if (newSentinel) {
        const newNextUrl = newSentinel.getAttribute('data-next-url');
        console.log('More pages available, next URL:', newNextUrl);
        sentinel.setAttribute('data-next-url', newNextUrl);
        spinner && spinner.classList.add('d-none');
        loadText && (loadText.textContent = 'Scroll for more');
        isLoading = false;
      } else {
        console.log('No more pages, removing sentinel');
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
      console.log('Sentinel intersection:', {
        isIntersecting: entry.isIntersecting,
        isLoading: isLoading,
        intersectionRatio: entry.intersectionRatio
      });
      if (entry.isIntersecting && !isLoading) {
        loadMore();
      }
    });
  }, { rootMargin: '300px' });

  observer.observe(sentinel);
  console.log('Observer attached to sentinel');
};
</script>

<style>
  @media print {
    .card-footer, .btn, .text-muted, .card-body form {
      display: none !important;
    }
    
    .card {
      box-shadow: none !important;
      border: 1px solid #000 !important;
    }
    
    table {
      border: 1px solid #000 !important;
    }
    
    th, td {
      border: 1px solid #000 !important;
    }
  }
</style>
@endpush
