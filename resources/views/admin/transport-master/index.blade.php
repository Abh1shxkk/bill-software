@extends('layouts.admin')

@section('title', 'Transport Master')

@section('content')
<style>
  /* Slide-in Modal Styles */
  .transport-modal {
    display: none;
    position: fixed;
    top: 70px;
    right: 0;
    width: 450px;
    height: calc(100vh - 100px);
    max-height: calc(100vh - 140px);
    z-index: 999999 !important;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
  }

  .transport-modal.show {
    transform: translateX(0);
  }

  .transport-modal-content {
    background: white;
    height: 100%;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
  }

  .transport-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #dee2e6;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
  }

  .transport-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
  }

  .btn-close-modal {
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

  .btn-close-modal:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
  }

  .transport-modal-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
    background: #f8f9fa;
  }

  .transport-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 260px;
    width: calc(100vw - 260px);
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 999998 !important;
    opacity: 0;
    transition: all 0.3s ease;
  }

  .transport-modal-backdrop.show {
    opacity: 0.7;
  }

  @media (max-width: 768px) {
    .transport-modal {
      width: 100%;
    }
    
    .transport-modal-backdrop {
      left: 0;
      width: 100vw;
    }
  }

  .transport-modal .card {
    margin-bottom: 1rem;
    border-radius: 0.5rem;
    overflow: hidden;
  }

  .transport-modal .card:last-child {
    margin-bottom: 0;
  }

  .transport-modal .card-header {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    font-weight: 600;
  }

  .transport-modal .card-body {
    padding: 1rem;
    background: white;
  }

  .transport-modal .card-body small {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  .transport-modal .fw-semibold {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
  }

  .transport-modal-body::-webkit-scrollbar {
    width: 8px;
  }

  .transport-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .transport-modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .transport-modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-truck me-2"></i> Transport Master</h4>
    <div class="text-muted small">Manage transport details</div>
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-transport-master-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteTransportMaster()">
      <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-transport-master-count">0</span>)
    </button>
    <a href="{{ route('admin.transport-master.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Transport
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive" id="transport-table-wrapper" style="position: relative;">
    <table class="table align-middle mb-0" id="transport-table">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-transport-master">
              <label class="form-check-label" for="select-all-transport-master">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th style="width: 5%">#</th>
          <th style="width: 30%">Name</th>
          <th style="width: 20%">Mobile</th>
          <th style="width: 15%">Trans Mode</th>
          <th style="width: 15%">Status</th>
          <th style="width: 15%" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="transport-table-body">
        @forelse($transports as $transport)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input transport-master-checkbox" type="checkbox" value="{{ $transport->id }}" id="transport-master-{{ $transport->id }}">
                <label class="form-check-label" for="transport-master-{{ $transport->id }}">
                  <span class="visually-hidden">Select transport</span>
                </label>
              </div>
            </td>
            <td>{{ ($transports->currentPage() - 1) * $transports->perPage() + $loop->iteration }}</td>
            <td>{{ $transport->name ?? '-' }}</td>
            <td>{{ $transport->mobile ?? '-' }}</td>
            <td>{{ $transport->trans_mode ?? '-' }}</td>
            <td>{{ $transport->status ?? '-' }}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary" onclick="viewTransportDetails({{ $transport->id }})" title="View">
                <i class="bi bi-eye"></i>
              </button>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.transport-master.edit', $transport) }}" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.transport-master.destroy', $transport) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                        data-delete-url="{{ route('admin.transport-master.destroy', $transport) }}"
                        data-delete-message="Delete this transport?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">No transports found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="align-self-start">
      Showing {{ $transports->firstItem() ?? 0 }}-{{ $transports->lastItem() ?? 0 }} of {{ $transports->total() }}
    </div>
    @if($transports->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="transport-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="transport-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="transport-sentinel" data-next-url="{{ $transports->appends(request()->query())->nextPageUrl() }}" style="height: 20px;"></div>
    @endif
  </div>
</div>

<!-- Transport Details Modal -->
<div id="transportDetailsModal" class="transport-modal">
  <div class="transport-modal-content">
    <div class="transport-modal-header">
      <h5 class="transport-modal-title">
        <i class="bi bi-truck me-2"></i>Transport Details
      </h5>
      <button type="button" class="btn-close-modal" onclick="closeTransportModal()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="transport-modal-body" id="transportModalBody">
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading details...</div>
      </div>
    </div>
  </div>
</div>

<div id="transportModalBackdrop" class="transport-modal-backdrop"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('transport-table-body');
  let isLoading = false;
  let observer = null;

  // Infinite scroll implementation
  function initInfiniteScroll() {
    const sentinel = document.getElementById('transport-sentinel');

    if (!sentinel) {
      return;
    }

    if (observer) {
      observer.disconnect();
    }

    observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          const nextUrl = sentinel.getAttribute('data-next-url');
          if (nextUrl) {
            loadMore(nextUrl);
          }
        }
      });
    }, { rootMargin: '300px' });

    observer.observe(sentinel);
  }

  function loadMore(url) {
    if (isLoading) return;
    isLoading = true;

    const spinner = document.getElementById('transport-spinner');
    const loadText = document.getElementById('transport-load-text');

    if (spinner) spinner.classList.remove('d-none');
    if (loadText) loadText.textContent = 'Loading...';

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const newRows = tempDiv.querySelectorAll('#transport-table-body tr');
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });

      realRows.forEach(tr => {
        tbody.appendChild(tr.cloneNode(true));
      });

      if (typeof window.reattachTransportMasterEventListeners === 'function') {
        window.reattachTransportMasterEventListeners();
        window.updateTransportMasterSelectedCount && window.updateTransportMasterSelectedCount();
      }

      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        
        setTimeout(() => {
          initInfiniteScroll();
        }, 100);
      }

      const newSentinel = tempDiv.querySelector('#transport-sentinel');
      const currentSentinel = document.getElementById('transport-sentinel');
      if (newSentinel && currentSentinel) {
        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url') || '');
      } else if (currentSentinel && !newSentinel) {
        currentSentinel.remove();
        if (observer) {
          observer.disconnect();
        }
      }
    })
    .catch(error => {
      console.error('Load more error:', error);
    })
    .finally(() => {
      isLoading = false;
      if (spinner) spinner.classList.add('d-none');
      if (loadText) loadText.textContent = 'Scroll for more';
    });
  }

  initInfiniteScroll();
  window.performTransportMasterSearch = function() { window.location.reload(); };
  let transportMasterPageElements = { selectAllCheckbox: document.getElementById('select-all-transport-master'), deleteSelectedBtn: document.getElementById('delete-selected-transport-master-btn'), selectedCountSpan: document.getElementById('selected-transport-master-count') };
  window.updateTransportMasterSelectedCount = function() { const checkedBoxes = document.querySelectorAll('.transport-master-checkbox:checked'); const count = checkedBoxes.length; if (transportMasterPageElements.selectedCountSpan) transportMasterPageElements.selectedCountSpan.textContent = count; if (transportMasterPageElements.deleteSelectedBtn) { if (count > 0) transportMasterPageElements.deleteSelectedBtn.classList.remove('d-none'); else transportMasterPageElements.deleteSelectedBtn.classList.add('d-none'); } if (transportMasterPageElements.selectAllCheckbox) { const allBoxes = document.querySelectorAll('.transport-master-checkbox'); if (count === 0) { transportMasterPageElements.selectAllCheckbox.indeterminate = false; transportMasterPageElements.selectAllCheckbox.checked = false; } else if (count === allBoxes.length) { transportMasterPageElements.selectAllCheckbox.indeterminate = false; transportMasterPageElements.selectAllCheckbox.checked = true; } else { transportMasterPageElements.selectAllCheckbox.indeterminate = true; transportMasterPageElements.selectAllCheckbox.checked = false; } } };
  window.reattachTransportMasterEventListeners = function() { setTimeout(() => { document.querySelectorAll('.transport-master-checkbox').forEach(cb => { cb.removeEventListener('change', window.updateTransportMasterSelectedCount); cb.addEventListener('change', function(){ window.updateTransportMasterSelectedCount(); }); }); const selectAll = document.getElementById('select-all-transport-master'); if (selectAll) { const newSelectAll = selectAll.cloneNode(true); selectAll.parentNode.replaceChild(newSelectAll, selectAll); newSelectAll.addEventListener('change', function(){ document.querySelectorAll('.transport-master-checkbox').forEach(b => b.checked = this.checked); window.updateTransportMasterSelectedCount(); }); transportMasterPageElements.selectAllCheckbox = newSelectAll; } }, 50); };
  window.reattachTransportMasterEventListeners(); window.updateTransportMasterSelectedCount();
});
function confirmMultipleDeleteTransportMaster() { const checked = document.querySelectorAll('.transport-master-checkbox:checked'); if (checked.length === 0) return; const selectedItems = []; checked.forEach(cb => { const row = cb.closest('tr'); const name = row.querySelector('td:nth-child(3)').textContent.trim(); selectedItems.push({ id: cb.value, name: name }); }); window.GlobalMultipleDelete.show({ selectedItems: selectedItems, deleteUrl: '{{ route('admin.transport-master.multiple-delete') }}', itemType: 'transport masters', csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'), onSuccess: function(){ window.performTransportMasterSearch(); }, onError: function(err){ console.error('Error:', err); if (window.crudNotification) crudNotification.showToast('error', 'Error', 'Failed to delete selected transport masters.'); } }); }

// Transport Modal Functions
function viewTransportDetails(id) {
  const modal = document.getElementById('transportDetailsModal');
  const backdrop = document.getElementById('transportModalBackdrop');
  const modalBody = document.getElementById('transportModalBody');

  backdrop.style.display = 'block';
  modal.style.display = 'block';

  setTimeout(() => {
    backdrop.classList.add('show');
    modal.classList.add('show');
  }, 10);

  modalBody.innerHTML = `
    <div class="text-center py-3">
      <div class="spinner-border spinner-border-sm text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="mt-2 small">Loading details...</div>
    </div>
  `;

  fetch(`/admin/transport-master/${id}`, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    populateTransportData(data);
  })
  .catch(error => {
    console.error('Error:', error);
    showErrorInModal('Failed to load transport details. Please try again.');
  });
}

function populateTransportData(data) {
  const modalBody = document.getElementById('transportModalBody');
  
  let html = '<div class="row g-3">';

  // Basic Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Name:</small>
              <div class="fw-semibold">${data.name || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Alter. Code:</small>
              <div class="fw-semibold"><span class="badge bg-secondary">${data.alter_code || '-'}</span></div>
            </div>
            <div class="col-12">
              <small class="text-muted">Address:</small>
              <div class="fw-semibold" style="white-space: pre-wrap; font-size: 0.85rem;">${(data.address || '-').replace(/\n/g, '<br>')}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Contact Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white py-2">
          <h6 class="mb-0"><i class="bi bi-telephone-fill me-2"></i>Contact Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Telephone:</small>
              <div class="fw-semibold">${data.telephone || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Mobile:</small>
              <div class="fw-semibold">${data.mobile || '-'}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">E-Mail:</small>
              <div class="fw-semibold">${data.email || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Transport Details Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white py-2">
          <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Transport Details</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Vehicle No.:</small>
              <div class="fw-semibold">${data.vehicle_no || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Trans Mode:</small>
              <div class="fw-semibold"><span class="badge bg-primary">${data.trans_mode || '-'}</span></div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">GST No.:</small>
              <div class="fw-semibold">${data.gst_no || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Status:</small>
              <div class="fw-semibold">${data.status || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Timestamps Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-secondary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Timestamps</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Created At:</small>
              <div class="fw-semibold">${data.created_at || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Last Updated:</small>
              <div class="fw-semibold">${data.updated_at || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  html += '</div>';
  modalBody.innerHTML = html;
}

function showErrorInModal(message) {
  const modalBody = document.getElementById('transportModalBody');
  modalBody.innerHTML = `
    <div class="text-center py-4">
      <div class="text-danger mb-3">
        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
      </div>
      <h6 class="text-danger">Error</h6>
      <p class="text-muted">${message}</p>
      <button class="btn btn-outline-secondary btn-sm" onclick="closeTransportModal()">Close</button>
    </div>
  `;
}

function closeTransportModal() {
  const modal = document.getElementById('transportDetailsModal');
  const backdrop = document.getElementById('transportModalBackdrop');

  modal.classList.remove('show');
  backdrop.classList.remove('show');

  setTimeout(() => {
    modal.style.display = 'none';
    backdrop.style.display = 'none';
  }, 300);
}

// Close modal when clicking backdrop
document.addEventListener('click', function (e) {
  if (e.target && e.target.id === 'transportModalBackdrop') {
    closeTransportModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('transportDetailsModal');
    if (modal && modal.classList.contains('show')) {
      closeTransportModal();
    }
  }
});
</script>
@endpush
