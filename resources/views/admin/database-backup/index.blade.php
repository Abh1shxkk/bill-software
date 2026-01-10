@extends('layouts.admin')

@section('title', 'Database Backup & Restore')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-database me-2"></i>Database Backup & Restore
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Export Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-download me-2"></i>Full Database Backup
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a full backup of your database. The backup will include all tables and data.</p>
                    
                    <form action="{{ route('admin.database-backup.export') }}" method="POST" id="exportForm">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="compress" id="compressBackup" value="1">
                                <label class="form-check-label" for="compressBackup">
                                    Compress backup (.zip)
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="exportBtn">
                            <i class="bi bi-download me-1"></i>Download Full Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-upload me-2"></i>Import / Restore
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Warning:</strong> Restoring a backup will replace ALL existing data. This action cannot be undone.
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        An automatic backup of your current data will be created before restore.
                    </div>
                    
                    <form action="{{ route('admin.database-backup.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Select Backup File</label>
                            <input type="file" class="form-control" id="backupFile" name="backup_file" accept=".json,.zip" required>
                            <div class="form-text">Accepted formats: .json, .zip (max 100MB)</div>
                        </div>
                        
                        <button type="button" class="btn btn-warning" id="restoreBtn">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Selective Table Backup Section -->
    <!-- <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="bi bi-table me-2"></i>Selective Table Backup
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Select specific tables to backup. Useful for backing up only the data you need.</p>
            
            <form action="{{ route('admin.database-backup.export-selective') }}" method="POST" id="selectiveExportForm">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search tables...">
                        </div>
                        <div class="btn-group btn-group-sm mb-2">
                            <button type="button" class="btn btn-outline-secondary" id="selectAllTables">Select All</button>
                            <button type="button" class="btn btn-outline-secondary" id="deselectAllTables">Deselect All</button>
                        </div>
                        <span class="ms-2 text-muted" id="selectedCount">0 tables selected</span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="compress" id="compressSelectiveBackup" value="1">
                            <label class="form-check-label" for="compressSelectiveBackup">Compress (.zip)</label>
                        </div>
                        <button type="submit" class="btn btn-info" id="selectiveExportBtn">
                            <i class="bi bi-download me-1"></i>Download Selected Tables
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0" id="tablesTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="checkAllVisible">
                                </th>
                                <th>Table Name</th>
                                <th width="120" class="text-end">Records</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tableStats as $table => $count)
                                <tr class="table-row" data-table="{{ strtolower($table) }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input table-checkbox" name="tables[]" value="{{ $table }}">
                                    </td>
                                    <td>
                                        <i class="bi bi-table text-muted me-1"></i>{{ $table }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary">{{ number_format($count) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div> -->

    <!-- Scheduled Backup Section -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <i class="bi bi-clock me-2"></i>Scheduled Backup
        </div>
        <div class="card-body">
            <form action="{{ route('admin.database-backup.schedule') }}" method="POST" id="scheduleForm">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="frequency" class="form-label">Frequency</label>
                        <select class="form-select" id="frequency" name="frequency">
                            <option value="daily" {{ ($schedule->frequency ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ ($schedule->frequency ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ ($schedule->frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" value="{{ $schedule->time ?? '02:00' }}">
                    </div>
                    <div class="col-md-2 mb-3" id="dayOfWeekGroup" style="display: none;">
                        <label for="day_of_week" class="form-label">Day of Week</label>
                        <select class="form-select" id="day_of_week" name="day_of_week">
                            <option value="0" {{ ($schedule->day_of_week ?? '') == 0 ? 'selected' : '' }}>Sunday</option>
                            <option value="1" {{ ($schedule->day_of_week ?? '') == 1 ? 'selected' : '' }}>Monday</option>
                            <option value="2" {{ ($schedule->day_of_week ?? '') == 2 ? 'selected' : '' }}>Tuesday</option>
                            <option value="3" {{ ($schedule->day_of_week ?? '') == 3 ? 'selected' : '' }}>Wednesday</option>
                            <option value="4" {{ ($schedule->day_of_week ?? '') == 4 ? 'selected' : '' }}>Thursday</option>
                            <option value="5" {{ ($schedule->day_of_week ?? '') == 5 ? 'selected' : '' }}>Friday</option>
                            <option value="6" {{ ($schedule->day_of_week ?? '') == 6 ? 'selected' : '' }}>Saturday</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3" id="dayOfMonthGroup" style="display: none;">
                        <label for="day_of_month" class="form-label">Day of Month</label>
                        <select class="form-select" id="day_of_month" name="day_of_month">
                            @for($i = 1; $i <= 28; $i++)
                                <option value="{{ $i }}" {{ ($schedule->day_of_month ?? 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="retention_days" class="form-label">Keep Backups (days)</label>
                        <input type="number" class="form-control" id="retention_days" name="retention_days" 
                               value="{{ $schedule->retention_days ?? 30 }}" min="1" max="365">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="compress" name="compress" value="1" 
                                   {{ ($schedule->compress ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="compress">Compress</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ ($schedule->is_active ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i>Save Schedule
                    </button>
                    @if($schedule && $schedule->is_active)
                        <span class="ms-3 text-muted">
                            <i class="bi bi-clock me-1"></i>
                            Next run: {{ $schedule->next_run_at ? $schedule->next_run_at->format('M d, Y H:i') : 'Not scheduled' }}
                        </span>
                    @endif
                    @if($schedule && $schedule->last_run_at)
                        <span class="ms-3 text-muted">
                            <i class="bi bi-check-circle me-1"></i>
                            Last run: {{ $schedule->last_run_at->format('M d, Y H:i') }}
                        </span>
                    @endif
                </div>
            </form>
            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Note:</strong> Scheduled backups require a cron job to run <code>php artisan schedule:run</code> every minute.
                Add this to your server's crontab: <code>* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1</code>
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <div class="card">
        <div class="card-header bg-white">
            <i class="bi bi-clock-history me-2"></i>Backup History
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td>
                                    <i class="bi {{ str_ends_with($backup['filename'], '.zip') ? 'bi-file-zip' : 'bi-file-code' }} me-2"></i>
                                    {{ $backup['filename'] }}
                                    @if(str_starts_with($backup['filename'], 'pre_restore_'))
                                        <span class="badge bg-secondary">Pre-restore</span>
                                    @elseif(str_starts_with($backup['filename'], 'selective_'))
                                        <span class="badge bg-info">Selective</span>
                                    @endif
                                </td>
                                <td>{{ $backup['size_formatted'] }}</td>
                                <td>{{ $backup['created_at'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.database-backup.download', $backup['filename']) }}" 
                                           class="btn btn-outline-primary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger delete-backup-btn"
                                                data-filename="{{ $backup['filename'] }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No backups found. Create your first backup above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Restore Modal -->
<div class="modal fade" id="confirmRestoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Restore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore the database from this backup?</p>
                <p class="text-danger"><strong>This will replace ALL existing data!</strong></p>
                <p class="text-info"><i class="bi bi-info-circle me-1"></i>An automatic backup of your current data will be created before restore.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Yes, Restore
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('importForm');
    const restoreBtn = document.getElementById('restoreBtn');
    const confirmRestoreBtn = document.getElementById('confirmRestoreBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmRestoreModal'));
    const backupFile = document.getElementById('backupFile');
    const frequencySelect = document.getElementById('frequency');
    const dayOfWeekGroup = document.getElementById('dayOfWeekGroup');
    const dayOfMonthGroup = document.getElementById('dayOfMonthGroup');

    // Selective Backup Elements
    const tableSearch = document.getElementById('tableSearch');
    const tableCheckboxes = document.querySelectorAll('.table-checkbox');
    const selectAllBtn = document.getElementById('selectAllTables');
    const deselectAllBtn = document.getElementById('deselectAllTables');
    const checkAllVisible = document.getElementById('checkAllVisible');
    const selectedCountEl = document.getElementById('selectedCount');
    const selectiveExportForm = document.getElementById('selectiveExportForm');
    const selectiveExportBtn = document.getElementById('selectiveExportBtn');

    // Update selected count
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.table-checkbox:checked').length;
        selectedCountEl.textContent = checked + ' table' + (checked !== 1 ? 's' : '') + ' selected';
    }

    // Table search filter
    tableSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.table-row').forEach(row => {
            const tableName = row.dataset.table;
            row.style.display = tableName.includes(searchTerm) ? '' : 'none';
        });
    });

    // Select all tables
    selectAllBtn.addEventListener('click', function() {
        tableCheckboxes.forEach(cb => {
            cb.checked = true;
        });
        checkAllVisible.checked = true;
        updateSelectedCount();
    });

    // Deselect all tables
    deselectAllBtn.addEventListener('click', function() {
        tableCheckboxes.forEach(cb => {
            cb.checked = false;
        });
        checkAllVisible.checked = false;
        updateSelectedCount();
    });

    // Check all visible (header checkbox)
    checkAllVisible.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.table-row').forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('.table-checkbox').checked = isChecked;
            }
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    tableCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Selective export form submit
    selectiveExportForm.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.table-checkbox:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one table to backup.');
            return;
        }
        selectiveExportBtn.disabled = true;
        selectiveExportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating backup...';
    });

    // Handle frequency change for schedule
    function updateScheduleFields() {
        const freq = frequencySelect.value;
        dayOfWeekGroup.style.display = freq === 'weekly' ? 'block' : 'none';
        dayOfMonthGroup.style.display = freq === 'monthly' ? 'block' : 'none';
    }
    
    frequencySelect.addEventListener('change', updateScheduleFields);
    updateScheduleFields(); // Initial call

    // Log file selection
    backupFile.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            console.log('File selected:', {
                name: file.name,
                size: file.size,
                type: file.type,
                lastModified: new Date(file.lastModified).toISOString()
            });
        }
    });

    // Show confirmation modal before restore
    restoreBtn.addEventListener('click', function() {
        if (!backupFile.files.length) {
            console.warn('No file selected');
            alert('Please select a backup file first.');
            return;
        }
        console.log('Opening restore confirmation modal');
        confirmModal.show();
    });

    // Submit form on confirm
    confirmRestoreBtn.addEventListener('click', function() {
        console.log('Restore confirmed, submitting form...');
        confirmModal.hide();
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Restoring...';
        
        importForm.submit();
    });

    // Delete backup
    document.querySelectorAll('.delete-backup-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.dataset.filename;
            console.log('Delete requested for:', filename);
            
            if (confirm(`Are you sure you want to delete backup "${filename}"?`)) {
                fetch(`{{ url('admin/database-backup') }}/${encodeURIComponent(filename)}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => {
                    console.log('Delete response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Delete response:', data);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete backup');
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('An error occurred');
                });
            }
        });
    });

    // Show loading state on export
    document.getElementById('exportForm').addEventListener('submit', function() {
        console.log('Export form submitted');
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating backup...';
    });
});
</script>
@endpush
