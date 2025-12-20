@extends('layouts.admin')

@section('title', 'General Notebook')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h2 class="mb-0">
                    <i class="bi bi-notebook me-2"></i>General Notebook
                </h2>
                @include('layouts.partials.module-shortcuts', [
                    'createRoute' => route('admin.general-notebook.create'),
                    'tableBodyId' => 'notebook-table-body',
                    'checkboxClass' => 'general-notebook-checkbox'
                ])
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="delete-selected-general-notebook-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteGeneralNotebook()">
                    <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-general-notebook-count">0</span>)
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.general-notebook.index') }}" class="row g-3" id="general-notebook-filter-form">
          <div class="col-md-3">
            <label for="gn_search_field" class="form-label">Search By</label>
            <select class="form-select" id="gn_search_field" name="search_field">
              <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
              <option value="title" {{ request('search_field') == 'title' ? 'selected' : '' }}>Title</option>
              <option value="content" {{ request('search_field') == 'content' ? 'selected' : '' }}>Content</option>
            </select>
          </div>
          <div class="col-md-7">
            <label for="gn_search" class="form-label">Search</label>
            <div class="input-group">
              <input type="text" class="form-control" id="gn_search" name="search" value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="gn-clear-search" title="Clear search">
                <i class="bi bi-x-circle"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div id="gn-search-loading" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    @if ($notebooks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-general-notebook">
                                <label class="form-check-label" for="select-all-general-notebook">
                                    <span class="visually-hidden">Select All</span>
                                </label>
                            </div>
                        </th>
                        <th style="width: 5%">#</th>
                        <th style="width: 35%">Title</th>
                        <th style="width: 40%">Content</th>
                        <th style="width: 10%">Date</th>
                        <th style="width: 10%">Actions</th>
                    </tr>
                </thead>
                <tbody id="notebook-table-body">
                    @foreach ($notebooks as $notebook)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input general-notebook-checkbox" type="checkbox" value="{{ $notebook->id }}" id="general-notebook-{{ $notebook->id }}">
                                    <label class="form-check-label" for="general-notebook-{{ $notebook->id }}">
                                        <span class="visually-hidden">Select note</span>
                                    </label>
                                </div>
                            </td>
                            <td>{{ ($notebooks->currentPage() - 1) * $notebooks->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $notebook->title ?? '-' }}</strong></td>
                            <td>{{ Str::limit($notebook->content ?? '-', 60) }}</td>
                            <td>{{ $notebook->created_at?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.general-notebook.edit', $notebook) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" data-delete-url="{{ route('admin.general-notebook.destroy', $notebook) }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $notebooks->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>No notes found. <a href="{{ route('admin.general-notebook.create') }}">Create one now</a>
        </div>
    @endif
</div>

<button id="scrollToTop" type="button" title="Scroll to top" onclick="scrollToTopNow()">
    <i class="bi bi-arrow-up"></i>
</button>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ========== SEARCH FUNCTIONALITY ==========
  let searchTimeout;
  const filterForm = document.getElementById('general-notebook-filter-form');
  const searchInput = document.getElementById('gn_search');
  const searchFieldSelect = document.getElementById('gn_search_field');
  const clearSearchBtn = document.getElementById('gn-clear-search');
  const tbody = document.getElementById('notebook-table-body');

  // AJAX search function
  window.performGeneralNotebookSearch = function() {
    if (!filterForm) return;
    
    // Show loading spinner
    const loadingSpinner = document.getElementById('gn-search-loading');
    if (loadingSpinner) loadingSpinner.style.display = 'flex';
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    
    fetch(`{{ route('admin.general-notebook.index') }}?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newRows = doc.querySelectorAll('#notebook-table-body tr');
      const realRows = Array.from(newRows).filter(tr => !tr.querySelector('td[colspan]'));
      
      if (tbody) {
        tbody.innerHTML = '';
        if (realRows.length) {
          realRows.forEach(tr => tbody.appendChild(tr));
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No notes found</td></tr>';
        }
      }
      
      // Reattach events
      document.querySelectorAll('.general-notebook-checkbox').forEach(cb => cb.addEventListener('change', window.updateGeneralNotebookSelectedCount));
      window.updateGeneralNotebookSelectedCount();
    })
    .catch(error => console.error('Search error:', error))
    .finally(() => {
      // Hide loading spinner
      if (loadingSpinner) loadingSpinner.style.display = 'none';
    });
  };

  // Search input with debounce
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(window.performGeneralNotebookSearch, 300);
    });
  }

  // Clear search button
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        window.performGeneralNotebookSearch();
      }
    });
  }

  // Trigger search on field change
  if (searchFieldSelect) {
    searchFieldSelect.addEventListener('change', window.performGeneralNotebookSearch);
  }

  let generalNotebookPageElements = {
    selectAllCheckbox: document.getElementById('select-all-general-notebook'),
    deleteSelectedBtn: document.getElementById('delete-selected-general-notebook-btn'),
    selectedCountSpan: document.getElementById('selected-general-notebook-count')
  };
  window.updateGeneralNotebookSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.general-notebook-checkbox:checked');
    const count = checkedBoxes.length;
    if (generalNotebookPageElements.selectedCountSpan) generalNotebookPageElements.selectedCountSpan.textContent = count;
    if (generalNotebookPageElements.deleteSelectedBtn) {
      if (count > 0) generalNotebookPageElements.deleteSelectedBtn.classList.remove('d-none');
      else generalNotebookPageElements.deleteSelectedBtn.classList.add('d-none');
    }
    if (generalNotebookPageElements.selectAllCheckbox) {
      const allBoxes = document.querySelectorAll('.general-notebook-checkbox');
      if (count === 0) { generalNotebookPageElements.selectAllCheckbox.indeterminate = false; generalNotebookPageElements.selectAllCheckbox.checked = false; }
      else if (count === allBoxes.length) { generalNotebookPageElements.selectAllCheckbox.indeterminate = false; generalNotebookPageElements.selectAllCheckbox.checked = true; }
      else { generalNotebookPageElements.selectAllCheckbox.indeterminate = true; generalNotebookPageElements.selectAllCheckbox.checked = false; }
    }
  };
  document.querySelectorAll('.general-notebook-checkbox').forEach(cb => cb.addEventListener('change', window.updateGeneralNotebookSelectedCount));
  const selectAll = document.getElementById('select-all-general-notebook');
  if (selectAll) {
    selectAll.addEventListener('change', function(){
      document.querySelectorAll('.general-notebook-checkbox').forEach(b => b.checked = this.checked);
      window.updateGeneralNotebookSelectedCount();
    });
  }
  window.updateGeneralNotebookSelectedCount();
});
function confirmMultipleDeleteGeneralNotebook() {
  const checked = document.querySelectorAll('.general-notebook-checkbox:checked');
  if (checked.length === 0) return;
  const selectedItems = [];
  checked.forEach(cb => {
    const row = cb.closest('tr');
    const name = row.querySelector('td:nth-child(3)').textContent.trim();
    selectedItems.push({ id: cb.value, name: name });
  });
  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '{{ route('admin.general-notebook.multiple-delete') }}',
    itemType: 'general notebook entries',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(){ window.performGeneralNotebookSearch(); },
    onError: function(err){ console.error('Error:', err); if (window.crudNotification) crudNotification.showToast('error', 'Error', 'Failed to delete selected notes.'); }
  });
}
</script>
@endpush
