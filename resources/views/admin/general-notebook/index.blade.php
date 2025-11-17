@extends('layouts.admin')

@section('title', 'General Notebook')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-notebook me-2"></i>General Notebook
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" id="delete-selected-general-notebook-btn" class="btn btn-danger d-none me-2" onclick="confirmMultipleDeleteGeneralNotebook()">
                <i class="bi bi-trash me-2"></i>Delete Selected (<span id="selected-general-notebook-count">0</span>)
            </button>
            <a href="{{ route('admin.general-notebook.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Note
            </a>
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
                <tbody>
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
  window.performGeneralNotebookSearch = function() { window.location.reload(); };
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
