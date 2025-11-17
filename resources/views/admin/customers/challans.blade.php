@extends('layouts.admin')
@section('title', 'Pending Challans - ' . $customer->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Pending Challans</h4>
    <div class="text-muted small">Customer: <strong class="text-primary">{{ $customer->name }}</strong></div>
  </div>
  <div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.customers.challans', $customer->id) }}" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="search" class="form-label">Search Challan No.</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" 
                   value="{{ request('search') }}" placeholder="Enter challan number..." autocomplete="off">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-search"></i>
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
        <div class="col-md-3">
          <label for="amount_min" class="form-label">Min Amount</label>
          <input type="number" class="form-control" id="amount_min" name="amount_min" 
                 value="{{ request('amount_min') }}" placeholder="0.00" step="0.01" autocomplete="off">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100" title="Clear All Filters">
            <i class="bi bi-arrow-clockwise"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards - Only Pending Challans -->
  <div class="row g-3 mb-3 px-3">
    <div class="col-md-4">
      <div class="card bg-warning text-dark">
        <div class="card-body py-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="small">Pending Challans</div>
              <div class="h5 mb-0" id="pending-count">{{ $pendingCount ?? 0 }}</div>
            </div>
            <i class="bi bi-clock fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-success text-white">
        <div class="card-body py-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="small">Pending Amount</div>
              <div class="h5 mb-0" id="total-amount">₹{{ number_format($totalAmount ?? 0, 2) }}</div>
            </div>
            <i class="bi bi-currency-rupee fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-info text-white">
        <div class="card-body py-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="small">Already Invoiced</div>
              <div class="h5 mb-0" id="invoiced-count">{{ $invoicedCount ?? 0 }}</div>
            </div>
            <i class="bi bi-check-circle fs-3"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Section -->
  <div class="table-responsive" id="challan-table-wrapper" style="position: relative; min-height: 400px; max-height: 600px; overflow-y: auto;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
        <tr>
          <th>Sr.No</th>
          <th>DATE</th>
          <th>TRN.No</th>
          <th class="text-end">AMOUNT</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="challan-table-body">
        @forelse($challans ?? [] as $challan)
          <tr>
            <td>{{ ($challans->currentPage() - 1) * $challans->perPage() + $loop->iteration }}</td>
            <td>{{ $challan->challan_date ? $challan->challan_date->format('d/m/Y') : '-' }}</td>
            <td><strong>{{ $challan->challan_no ?? '-' }}</strong></td>
            <td class="text-end">₹{{ number_format($challan->net_amount ?? 0, 2) }}</td>
            <td class="text-center">
              <a class="btn btn-sm btn-outline-info" href="{{ route('admin.sale-challan.show', $challan->id) }}" title="View Details">
                <i class="bi bi-eye"></i>
              </a>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.sale-challan.modification') }}?challan_no={{ $challan->challan_no }}&load=true" title="Edit Challan">
                <i class="bi bi-pencil"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">No pending challans for this customer</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Infinite Scroll Footer -->
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>Showing {{ $challans->firstItem() ?? 0 }}-{{ $challans->lastItem() ?? 0 }} of {{ $challans->total() ?? 0 }}</div>
      <div class="text-muted">Page {{ $challans->currentPage() }} of {{ $challans->lastPage() }}</div>
    </div>
    @if($challans->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="challan-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="challan-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="challan-sentinel" data-next-url="{{ $challans->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
    @endif
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Fetch challans via AJAX
    function fetchChallans(url = null, append = false) {
        const loading = document.getElementById('search-loading');
        const spinner = document.getElementById('challan-spinner');
        const loadText = document.getElementById('challan-load-text');
        
        if (!append) {
            loading.style.display = 'flex';
        } else {
            if (spinner) spinner.classList.remove('d-none');
            if (loadText) loadText.textContent = 'Loading...';
        }
        
        // Build URL with filters
        if (!url) {
            const params = new URLSearchParams();
            const search = document.getElementById('search').value;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const amountMin = document.getElementById('amount_min').value;
            
            if (search) params.append('search', search);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (amountMin) params.append('amount_min', amountMin);
            
            url = '{{ route("admin.customers.challans", $customer->id) }}?' + params.toString();
        }
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newRows = doc.querySelectorAll('#challan-table-body tr');
            const newSentinel = doc.querySelector('#challan-sentinel');
            const newFooter = doc.querySelector('.card-footer');
            
            // Update summary cards
            const totalChallans = doc.querySelector('#total-challans');
            const pendingCount = doc.querySelector('#pending-count');
            const invoicedCount = doc.querySelector('#invoiced-count');
            const totalAmount = doc.querySelector('#total-amount');
            
            if (totalChallans) document.getElementById('total-challans').textContent = totalChallans.textContent;
            if (pendingCount) document.getElementById('pending-count').textContent = pendingCount.textContent;
            if (invoicedCount) document.getElementById('invoiced-count').textContent = invoicedCount.textContent;
            if (totalAmount) document.getElementById('total-amount').textContent = totalAmount.textContent;
            
            const tbody = document.getElementById('challan-table-body');
            
            if (append) {
                // Append new rows
                newRows.forEach(row => {
                    if (!row.querySelector('td[colspan]')) {
                        tbody.appendChild(row.cloneNode(true));
                    }
                });
            } else {
                // Replace all rows
                tbody.innerHTML = '';
                newRows.forEach(row => tbody.appendChild(row.cloneNode(true)));
            }
            
            // Update sentinel
            const sentinel = document.getElementById('challan-sentinel');
            if (newSentinel) {
                if (sentinel) {
                    sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                }
            } else {
                if (sentinel) sentinel.remove();
                if (loadText) loadText.textContent = 'All records loaded';
            }
            
            // Update footer
            if (newFooter) {
                document.querySelector('.card-footer').innerHTML = newFooter.innerHTML;
                initInfiniteScroll();
            }
            
            loading.style.display = 'none';
            if (spinner) spinner.classList.add('d-none');
            if (loadText) loadText.textContent = 'Scroll for more';
        })
        .catch(error => {
            console.error('Error fetching challans:', error);
            loading.style.display = 'none';
            if (spinner) spinner.classList.add('d-none');
        });
    }
    
    // Infinite scroll
    function initInfiniteScroll() {
        const sentinel = document.getElementById('challan-sentinel');
        if (!sentinel) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const nextUrl = sentinel.getAttribute('data-next-url');
                    if (nextUrl) {
                        fetchChallans(nextUrl, true);
                    }
                }
            });
        }, { rootMargin: '100px' });
        
        observer.observe(sentinel);
    }
    
    // Search input with debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            fetchChallans();
        }, 300));
    }
    
    // Filter changes
    ['date_from', 'date_to', 'amount_min'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                fetchChallans();
            });
        }
    });
    
    // Clear filters
    document.getElementById('clear-filters').addEventListener('click', function() {
        document.getElementById('search').value = '';
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        document.getElementById('amount_min').value = '';
        fetchChallans();
    });
    
    // Prevent form submission (use AJAX instead)
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchChallans();
    });
    
    // Initialize infinite scroll
    initInfiniteScroll();
});
</script>
@endpush

@endsection
