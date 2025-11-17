@extends('layouts.admin')

@section('title', 'General Reminders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-bell me-2"></i> General Reminders</h4>
    <div class="text-muted small">Manage your reminders</div>
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-general-reminders-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteGeneralReminders()">
      <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-general-reminders-count">0</span>)
    </button>
    <a href="{{ route('admin.general-reminders.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Reminder
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive" id="reminder-table-wrapper" style="position: relative;">
    <table class="table align-middle mb-0" id="reminder-table">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-general-reminders">
              <label class="form-check-label" for="select-all-general-reminders">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th style="width: 5%">#</th>
          <th style="width: 25%">Name</th>
          <th style="width: 15%">Code</th>
          <th style="width: 20%">Due Date</th>
          <th style="width: 20%">Status</th>
          <th style="width: 15%" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="reminder-table-body">
        @forelse($reminders as $reminder)
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input general-reminders-checkbox" type="checkbox" value="{{ $reminder->id }}" id="general-reminder-{{ $reminder->id }}">
                <label class="form-check-label" for="general-reminder-{{ $reminder->id }}">
                  <span class="visually-hidden">Select reminder</span>
                </label>
              </div>
            </td>
            <td>{{ ($reminders->currentPage() - 1) * $reminders->perPage() + $loop->iteration }}</td>
            <td>{{ $reminder->name ?? '-' }}</td>
            <td>{{ $reminder->code ?? '-' }}</td>
            <td>{{ $reminder->due_date ? $reminder->due_date->format('d M Y') : '-' }}</td>
            <td>{{ $reminder->status ?? '-' }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.general-reminders.edit', $reminder) }}" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.general-reminders.destroy', $reminder) }}" method="POST" class="d-inline ajax-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                        data-delete-url="{{ route('admin.general-reminders.destroy', $reminder) }}"
                        data-delete-message="Delete this reminder?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">No reminders found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="align-self-start">
      Showing {{ $reminders->firstItem() ?? 0 }}-{{ $reminders->lastItem() ?? 0 }} of {{ $reminders->total() }}
    </div>
    @if($reminders->hasMorePages())
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="reminder-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="reminder-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="reminder-sentinel" data-next-url="{{ $reminders->appends(request()->query())->nextPageUrl() }}" style="height: 20px;"></div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('reminder-table-body');
  let isLoading = false;
  let observer = null;

  // Infinite scroll implementation
  function initInfiniteScroll() {
    const sentinel = document.getElementById('reminder-sentinel');

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

    const spinner = document.getElementById('reminder-spinner');
    const loadText = document.getElementById('reminder-load-text');

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
      const newRows = tempDiv.querySelectorAll('#reminder-table-body tr');
      
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });

      realRows.forEach(tr => {
        tbody.appendChild(tr.cloneNode(true));
      });

      // Reattach events after infinite append
      if (typeof window.reattachGeneralRemindersEventListeners === 'function') {
        window.reattachGeneralRemindersEventListeners();
        window.updateGeneralRemindersSelectedCount && window.updateGeneralRemindersSelectedCount();
      }

      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
        
        // Reinitialize observer after footer update with a small delay
        setTimeout(() => {
          initInfiniteScroll();
        }, 100);
      }

      const newSentinel = tempDiv.querySelector('#reminder-sentinel');
      const currentSentinel = document.getElementById('reminder-sentinel');
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

  window.performGeneralRemindersSearch = function() { window.location.reload(); };

  let generalRemindersPageElements = {
    selectAllCheckbox: document.getElementById('select-all-general-reminders'),
    deleteSelectedBtn: document.getElementById('delete-selected-general-reminders-btn'),
    selectedCountSpan: document.getElementById('selected-general-reminders-count')
  };

  window.updateGeneralRemindersSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.general-reminders-checkbox:checked');
    const count = checkedBoxes.length;
    if (generalRemindersPageElements.selectedCountSpan) generalRemindersPageElements.selectedCountSpan.textContent = count;
    if (generalRemindersPageElements.deleteSelectedBtn) {
      if (count > 0) generalRemindersPageElements.deleteSelectedBtn.classList.remove('d-none');
      else generalRemindersPageElements.deleteSelectedBtn.classList.add('d-none');
    }
    if (generalRemindersPageElements.selectAllCheckbox) {
      const allBoxes = document.querySelectorAll('.general-reminders-checkbox');
      if (count === 0) { generalRemindersPageElements.selectAllCheckbox.indeterminate = false; generalRemindersPageElements.selectAllCheckbox.checked = false; }
      else if (count === allBoxes.length) { generalRemindersPageElements.selectAllCheckbox.indeterminate = false; generalRemindersPageElements.selectAllCheckbox.checked = true; }
      else { generalRemindersPageElements.selectAllCheckbox.indeterminate = true; generalRemindersPageElements.selectAllCheckbox.checked = false; }
    }
  };

  window.reattachGeneralRemindersEventListeners = function() {
    setTimeout(() => {
      document.querySelectorAll('.general-reminders-checkbox').forEach(cb => {
        cb.removeEventListener('change', window.updateGeneralRemindersSelectedCount);
        cb.addEventListener('change', function(){ window.updateGeneralRemindersSelectedCount(); });
      });
      const selectAll = document.getElementById('select-all-general-reminders');
      if (selectAll) {
        const newSelectAll = selectAll.cloneNode(true);
        selectAll.parentNode.replaceChild(newSelectAll, selectAll);
        newSelectAll.addEventListener('change', function(){
          const boxes = document.querySelectorAll('.general-reminders-checkbox');
          boxes.forEach(b => b.checked = this.checked);
          window.updateGeneralRemindersSelectedCount();
        });
        generalRemindersPageElements.selectAllCheckbox = newSelectAll;
      }
    }, 50);
  };

  window.reattachGeneralRemindersEventListeners();
  window.updateGeneralRemindersSelectedCount();
});

function confirmMultipleDeleteGeneralReminders() {
  const checked = document.querySelectorAll('.general-reminders-checkbox:checked');
  if (checked.length === 0) return;
  const selectedItems = [];
  checked.forEach(cb => {
    const row = cb.closest('tr');
    const name = row.querySelector('td:nth-child(3)').textContent.trim();
    selectedItems.push({ id: cb.value, name: name });
  });
  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.general-reminders.multiple-delete') }}',
    itemType: 'general reminders',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(){ window.performGeneralRemindersSearch(); },
    onError: function(err){ console.error('Error:', err); if (window.crudNotification) crudNotification.showToast('error', 'Error', 'Failed to delete selected reminders.'); }
  });
}
</script>
@endpush
