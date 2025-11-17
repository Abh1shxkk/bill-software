@extends('layouts.admin')

@section('title', 'Personal Directory')

@section('content')
<style>
  /* Slide-in Modal Styles */
  .personal-directory-modal {
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

  .personal-directory-modal.show {
    transform: translateX(0);
  }

  .personal-directory-modal-content {
    background: white;
    height: 100%;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
  }

  .personal-directory-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #dee2e6;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
  }

  .personal-directory-modal-title {
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

  .personal-directory-modal-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
    background: #f8f9fa;
  }

  .personal-directory-modal-backdrop {
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

  .personal-directory-modal-backdrop.show {
    opacity: 0.7;
  }

  @media (max-width: 768px) {
    .personal-directory-modal {
      width: 100%;
    }
    
    .personal-directory-modal-backdrop {
      left: 0;
      width: 100vw;
    }
  }

  .personal-directory-modal .card {
    margin-bottom: 1rem;
    border-radius: 0.5rem;
    overflow: hidden;
  }

  .personal-directory-modal .card:last-child {
    margin-bottom: 0;
  }

  .personal-directory-modal .card-header {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    font-weight: 600;
  }

  .personal-directory-modal .card-body {
    padding: 1rem;
    background: white;
  }

  .personal-directory-modal .card-body small {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  .personal-directory-modal .fw-semibold {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
  }

  .personal-directory-modal-body::-webkit-scrollbar {
    width: 8px;
  }

  .personal-directory-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .personal-directory-modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .personal-directory-modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-person-lines-fill me-2"></i> Personal Directory</h4>
    <div class="text-muted small">Manage your personal directory entries</div>
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-personal-directory-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeletePersonalDirectory()">
      <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-personal-directory-count">0</span>)
    </button>
    <a href="{{ route('admin.personal-directory.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Entry
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive" id="personal-directory-table-wrapper" style="position: relative;">
    <table class="table align-middle mb-0" id="personal-directory-table">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-personal-directory">
              <label class="form-check-label" for="select-all-personal-directory">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Name</th>
          <th>Mobile</th>
          <th>Email</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="personal-directory-table-body">
        @forelse($entries as $entry)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input personal-directory-checkbox" type="checkbox" value="{{ $entry->id }}" id="personal-directory-{{ $entry->id }}">
                <label class="form-check-label" for="personal-directory-{{ $entry->id }}">
                  <span class="visually-hidden">Select entry</span>
                </label>
              </div>
            </td>
            <td>{{ ($entries->currentPage() - 1) * $entries->perPage() + $loop->iteration }}</td>
            <td>{{ $entry->name ?? '-' }}</td>
            <td>{{ $entry->mobile ?? '-' }}</td>
            <td>{{ $entry->email ?? '-' }}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary" onclick="viewPersonalDirectoryDetails({{ $entry->id }})" title="View">
                <i class="bi bi-eye"></i>
              </button>
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.personal-directory.edit', $entry) }}" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.personal-directory.destroy', $entry) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                        data-delete-url="{{ route('admin.personal-directory.destroy', $entry) }}"
                        data-delete-message="Delete this entry?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No entries found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="align-self-start">
      Showing {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }}
    </div>
    @if($entries->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="personal-directory-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="personal-directory-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="personal-directory-sentinel" data-next-url="{{ $entries->appends(request()->query())->nextPageUrl() }}" style="height: 20px;"></div>
    @endif
  </div>
</div>

<!-- Personal Directory Details Modal -->
<div id="personalDirectoryDetailsModal" class="personal-directory-modal">
  <div class="personal-directory-modal-content">
    <div class="personal-directory-modal-header">
      <h5 class="personal-directory-modal-title">
        <i class="bi bi-person-lines-fill me-2"></i>Personal Directory Details
      </h5>
      <button type="button" class="btn-close-modal" onclick="closePersonalDirectoryModal()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="personal-directory-modal-body" id="personalDirectoryModalBody">
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading details...</div>
      </div>
    </div>
  </div>
</div>

<div id="personalDirectoryModalBackdrop" class="personal-directory-modal-backdrop"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('personal-directory-table-body');
  let isLoading = false;
  let observer = null;

  // Infinite scroll implementation
  function initInfiniteScroll() {
    const sentinel = document.getElementById('personal-directory-sentinel');
    const spinner = document.getElementById('personal-directory-spinner');
    const loadText = document.getElementById('personal-directory-load-text');

    if (!sentinel) {
      console.log('No sentinel found, stopping infinite scroll');
      return;
    }

    console.log('Initializing infinite scroll, sentinel found');

    if (observer) {
      observer.disconnect();
    }

    observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          const nextUrl = sentinel.getAttribute('data-next-url');
          console.log('Sentinel intersecting, next URL:', nextUrl);
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

    console.log('Loading more from:', url);

    const spinner = document.getElementById('personal-directory-spinner');
    const loadText = document.getElementById('personal-directory-load-text');

    if (spinner) spinner.classList.remove('d-none');
    if (loadText) loadText.textContent = 'Loading...';

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      console.log('Response received, length:', html.length);
      
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const newRows = tempDiv.querySelectorAll('#personal-directory-table-body tr');
      
      console.log('New rows found:', newRows.length);
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });

      console.log('Real rows to append:', realRows.length);

      realRows.forEach(tr => {
        tbody.appendChild(tr.cloneNode(true));
      });

      // Reattach events after infinite append
      if (typeof window.reattachPersonalDirectoryEventListeners === 'function') {
        window.reattachPersonalDirectoryEventListeners();
        window.updatePersonalDirectorySelectedCount && window.updatePersonalDirectorySelectedCount();
      }

      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        
        console.log('Footer updated, will reinitialize observer');
        
        // Reinitialize observer after footer update with a small delay
        setTimeout(() => {
          initInfiniteScroll();
        }, 100);
      }

      const newSentinel = tempDiv.querySelector('#personal-directory-sentinel');
      const currentSentinel = document.getElementById('personal-directory-sentinel');
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

  // Expose globally for callbacks
  window.performPersonalDirectorySearch = function() {
    // Reload page for now
    window.location.reload();
  };

  // Multiple delete selection management
  let personalDirectoryPageElements = {
    selectAllCheckbox: document.getElementById('select-all-personal-directory'),
    deleteSelectedBtn: document.getElementById('delete-selected-personal-directory-btn'),
    selectedCountSpan: document.getElementById('selected-personal-directory-count')
  };

  window.updatePersonalDirectorySelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.personal-directory-checkbox:checked');
    const count = checkedBoxes.length;
    if (personalDirectoryPageElements.selectedCountSpan) {
      personalDirectoryPageElements.selectedCountSpan.textContent = count;
    }
    if (personalDirectoryPageElements.deleteSelectedBtn) {
      if (count > 0) personalDirectoryPageElements.deleteSelectedBtn.classList.remove('d-none');
      else personalDirectoryPageElements.deleteSelectedBtn.classList.add('d-none');
    }
    if (personalDirectoryPageElements.selectAllCheckbox) {
      const allBoxes = document.querySelectorAll('.personal-directory-checkbox');
      if (count === 0) {
        personalDirectoryPageElements.selectAllCheckbox.indeterminate = false;
        personalDirectoryPageElements.selectAllCheckbox.checked = false;
      } else if (count === allBoxes.length) {
        personalDirectoryPageElements.selectAllCheckbox.indeterminate = false;
        personalDirectoryPageElements.selectAllCheckbox.checked = true;
      } else {
        personalDirectoryPageElements.selectAllCheckbox.indeterminate = true;
        personalDirectoryPageElements.selectAllCheckbox.checked = false;
      }
    }
  };

  window.reattachPersonalDirectoryEventListeners = function() {
    setTimeout(() => {
      document.querySelectorAll('.personal-directory-checkbox').forEach(cb => {
        cb.removeEventListener('change', window.updatePersonalDirectorySelectedCount);
        cb.addEventListener('change', function(){
          window.updatePersonalDirectorySelectedCount();
        });
      });
      const selectAll = document.getElementById('select-all-personal-directory');
      if (selectAll) {
        const newSelectAll = selectAll.cloneNode(true);
        selectAll.parentNode.replaceChild(newSelectAll, selectAll);
        newSelectAll.addEventListener('change', function(){
          const boxes = document.querySelectorAll('.personal-directory-checkbox');
          boxes.forEach(b => b.checked = this.checked);
          window.updatePersonalDirectorySelectedCount();
        });
        personalDirectoryPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  // Initial attach
  window.reattachPersonalDirectoryEventListeners();
  window.updatePersonalDirectorySelectedCount();
});

// Confirm multiple delete
function confirmMultipleDeletePersonalDirectory() {
  const checked = document.querySelectorAll('.personal-directory-checkbox:checked');
  if (checked.length === 0) return;

  const selectedItems = [];
  checked.forEach(cb => {
    const row = cb.closest('tr');
    const name = row.querySelector('td:nth-child(3)').textContent.trim();
    selectedItems.push({ id: cb.value, name: name });
  });

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.personal-directory.multiple-delete') }}',
    itemType: 'personal directory entries',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(){ window.performPersonalDirectorySearch(); },
    onError: function(err){
      console.error('Error deleting personal directory entries:', err);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected entries. Please try again.');
      }
    }
  });
}

// Personal Directory Modal Functions
function viewPersonalDirectoryDetails(id) {
  const modal = document.getElementById('personalDirectoryDetailsModal');
  const backdrop = document.getElementById('personalDirectoryModalBackdrop');
  const modalBody = document.getElementById('personalDirectoryModalBody');

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

  fetch(`/admin/personal-directory/${id}`, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    populatePersonalDirectoryData(data);
  })
  .catch(error => {
    console.error('Error:', error);
    showErrorInModal('Failed to load personal directory details. Please try again.');
  });
}

function populatePersonalDirectoryData(data) {
  const modalBody = document.getElementById('personalDirectoryModalBody');
  
  let html = '<div class="row g-3">';

  // Basic Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-person-fill me-2"></i>Basic Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Name:</small>
              <div class="fw-semibold">${data.name || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Alt. Code:</small>
              <div class="fw-semibold"><span class="badge bg-secondary">${data.alt_code || '-'}</span></div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Status:</small>
              <div class="fw-semibold">${data.status || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Contact Person:</small>
              <div class="fw-semibold">${data.contact_person || '-'}</div>
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
              <small class="text-muted">Mobile:</small>
              <div class="fw-semibold">${data.mobile || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Tel (O):</small>
              <div class="fw-semibold">${data.tel_office || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Tel (R):</small>
              <div class="fw-semibold">${data.tel_residence || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Fax:</small>
              <div class="fw-semibold">${data.fax || '-'}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">Email:</small>
              <div class="fw-semibold">${data.email || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Address Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white py-2">
          <h6 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Address Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-12">
              <small class="text-muted">Office Address:</small>
              <div class="fw-semibold" style="white-space: pre-wrap; font-size: 0.85rem;">${(data.address_office || '-').replace(/\n/g, '<br>')}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">Residence Address:</small>
              <div class="fw-semibold" style="white-space: pre-wrap; font-size: 0.85rem;">${(data.address_residence || '-').replace(/\n/g, '<br>')}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Personal & Family Details Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-dark py-2">
          <h6 class="mb-0"><i class="bi bi-heart-fill me-2"></i>Personal & Family Details</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Birthday:</small>
              <div class="fw-semibold">${data.birthday ? new Date(data.birthday).toLocaleDateString() : '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Anniversary:</small>
              <div class="fw-semibold">${data.anniversary ? new Date(data.anniversary).toLocaleDateString() : '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Spouse:</small>
              <div class="fw-semibold">${data.spouse || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Spouse DOB:</small>
              <div class="fw-semibold">${data.spouse_dob ? new Date(data.spouse_dob).toLocaleDateString() : '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Child I:</small>
              <div class="fw-semibold">${data.child_1 || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Child I DOB:</small>
              <div class="fw-semibold">${data.child_1_dob ? new Date(data.child_1_dob).toLocaleDateString() : '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Child II:</small>
              <div class="fw-semibold">${data.child_2 || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Child II DOB:</small>
              <div class="fw-semibold">${data.child_2_dob ? new Date(data.child_2_dob).toLocaleDateString() : '-'}</div>
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
  const modalBody = document.getElementById('personalDirectoryModalBody');
  modalBody.innerHTML = `
    <div class="text-center py-4">
      <div class="text-danger mb-3">
        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
      </div>
      <h6 class="text-danger">Error</h6>
      <p class="text-muted">${message}</p>
      <button class="btn btn-outline-secondary btn-sm" onclick="closePersonalDirectoryModal()">Close</button>
    </div>
  `;
}

function closePersonalDirectoryModal() {
  const modal = document.getElementById('personalDirectoryDetailsModal');
  const backdrop = document.getElementById('personalDirectoryModalBackdrop');

  modal.classList.remove('show');
  backdrop.classList.remove('show');

  setTimeout(() => {
    modal.style.display = 'none';
    backdrop.style.display = 'none';
  }, 300);
}

// Close modal when clicking backdrop
document.addEventListener('click', function (e) {
  if (e.target && e.target.id === 'personalDirectoryModalBackdrop') {
    closePersonalDirectoryModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('personalDirectoryDetailsModal');
    if (modal && modal.classList.contains('show')) {
      closePersonalDirectoryModal();
    }
  }
});
</script>
@endpush
