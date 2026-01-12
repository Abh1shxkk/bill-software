@extends('layouts.admin')

@section('title', 'Backup & Restore')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-shield-check me-2"></i>Backup & Restore Center
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Backup Type Cards Row -->
    <div class="row">
        <!-- Full Backup (Database + Code) -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-box-seam me-2"></i>Full System Backup
                    <span class="badge bg-light text-success float-end">Recommended</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">Complete backup of your entire system including:</p>
                    <ul class="list-unstyled mb-3">
                        <li><i class="bi bi-check-circle text-success me-2"></i>All database tables & data</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Application code files</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Configuration files</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Views & resources</li>
                    </ul>
                    
                    <form action="{{ route('admin.database-backup.export-full') }}" method="POST" id="fullExportForm">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" id="fullExportBtn">
                            <i class="bi bi-download me-1"></i>Download Full Backup
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Estimated size: {{ $codeBackupInfo['total_estimated_size_formatted'] ?? 'Unknown' }} (code only)
                    </small>
                </div>
            </div>
        </div>

        <!-- Database Only Backup -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-database me-2"></i>Database Backup
                </div>
                <div class="card-body">
                    <p class="text-muted">Backup all database tables and records. Useful for:</p>
                    <ul class="list-unstyled mb-3">
                        <li><i class="bi bi-database text-primary me-2"></i>Daily data backup</li>
                        <li><i class="bi bi-arrow-repeat text-primary me-2"></i>Before major updates</li>
                        <li><i class="bi bi-cloud-upload text-primary me-2"></i>Data migration</li>
                    </ul>
                    
                    <form action="{{ route('admin.database-backup.export') }}" method="POST" id="exportForm">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="compress" id="compressBackup" value="1" checked>
                                <label class="form-check-label" for="compressBackup">
                                    Compress backup (.zip)
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="exportBtn">
                            <i class="bi bi-download me-1"></i>Download Database Backup
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-table me-1"></i>
                        {{ count($tableStats ?? []) }} tables available
                    </small>
                </div>
            </div>
        </div>

        <!-- Code Only Backup -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-code-slash me-2"></i>Code Files Backup
                </div>
                <div class="card-body">
                    <p class="text-muted">Backup application source code and assets:</p>
                    <ul class="list-unstyled mb-3">
                        <li><i class="bi bi-folder text-info me-2"></i>Controllers & Models</li>
                        <li><i class="bi bi-file-earmark-code text-info me-2"></i>Views & Resources</li>
                        <li><i class="bi bi-gear text-info me-2"></i>Configuration files</li>
                        <li><i class="bi bi-signpost-2 text-info me-2"></i>Routes & Migrations</li>
                    </ul>
                    
                    <form action="{{ route('admin.database-backup.export-code') }}" method="POST" id="codeExportForm">
                        @csrf
                        <button type="submit" class="btn btn-info w-100 text-white" id="codeExportBtn">
                            <i class="bi bi-download me-1"></i>Download Code Backup
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-folder2 me-1"></i>
                        {{ count($codeBackupInfo['directories'] ?? []) }} directories included
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Enhanced Import Section -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-upload me-2"></i>Import / Restore System
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>⚠️ Critical Warning:</strong> Restoring will replace existing files/data. This action cannot be easily undone.
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-shield-check me-1"></i>
                        An automatic backup of your current state will be created before restore.
                    </div>
                    
                    <form action="{{ route('admin.database-backup.import-full') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Select Backup File</label>
                            <input type="file" class="form-control" id="backupFile" name="backup_file" 
                                   accept=".json,.zip" required>
                            <div class="form-text">Supports: Database (.json/.zip), Code (.zip), Full System (.zip) - Max 500MB</div>
                        </div>

                        <!-- Backup Type Detection Display -->
                        <div id="backupTypeInfo" class="mb-3" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-2">Detected Type:</strong> 
                                        <span id="detectedType"></span>
                                    </div>
                                    <div id="fullBackupOptions" style="display: none;" class="mt-2 pt-2 border-top">
                                        <small class="text-muted d-block mb-2">Select what to restore:</small>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="restore_database" 
                                                   id="restoreDatabase" value="1" checked>
                                            <label class="form-check-label" for="restoreDatabase">
                                                <i class="bi bi-database me-1 text-primary"></i>Restore Database
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="restore_code" 
                                                   id="restoreCode" value="1" checked>
                                            <label class="form-check-label" for="restoreCode">
                                                <i class="bi bi-code-slash me-1 text-info"></i>Restore Code Files
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-warning w-100" id="restoreBtn">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore System
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Protected: .env, storage/, vendor/, node_modules/
                    </small>
                </div>
            </div>
        </div>

        <!-- Code Backup Details -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <i class="bi bi-folder-check me-2"></i>Code Backup Contents
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Directory</th>
                                    <th class="text-end">Size</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($codeBackupInfo['directories'] ?? [] as $dir => $info)
                                    <tr>
                                        <td>
                                            <i class="bi bi-folder text-warning me-2"></i>
                                            <code>{{ $dir }}/</code>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-secondary">{{ $info['size_formatted'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($info['exists'])
                                                <i class="bi bi-check-circle text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-folder me-1"></i>
                            Excludes: vendor, node_modules, storage, .git
                        </small>
                        <span class="badge bg-primary">
                            Total: {{ $codeBackupInfo['total_estimated_size_formatted'] ?? '0 B' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Backup Section -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
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
                    <button type="submit" class="btn btn-secondary">
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
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history me-2"></i>Backup History</span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary active" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-primary" data-filter="database">Database</button>
                <button type="button" class="btn btn-outline-info" data-filter="code">Code</button>
                <button type="button" class="btn btn-outline-success" data-filter="full">Full</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="backupTable">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allBackups ?? [] as $backup)
                            @php
                                $type = 'database';
                                $typeClass = 'bg-primary';
                                $typeIcon = 'bi-database';
                                
                                if (str_starts_with($backup['filename'], 'code_backup_')) {
                                    $type = 'code';
                                    $typeClass = 'bg-info';
                                    $typeIcon = 'bi-code-slash';
                                } elseif (str_starts_with($backup['filename'], 'full_backup_')) {
                                    $type = 'full';
                                    $typeClass = 'bg-success';
                                    $typeIcon = 'bi-box-seam';
                                } elseif (str_starts_with($backup['filename'], 'pre_restore_')) {
                                    $type = 'pre-restore';
                                    $typeClass = 'bg-secondary';
                                    $typeIcon = 'bi-arrow-counterclockwise';
                                } elseif (str_starts_with($backup['filename'], 'selective_')) {
                                    $type = 'selective';
                                    $typeClass = 'bg-warning text-dark';
                                    $typeIcon = 'bi-table';
                                }
                            @endphp
                            <tr data-type="{{ $type }}">
                                <td>
                                    <span class="badge {{ $typeClass }}">
                                        <i class="bi {{ $typeIcon }} me-1"></i>{{ ucfirst($type) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="bi {{ str_ends_with($backup['filename'], '.zip') ? 'bi-file-zip' : 'bi-file-code' }} me-2"></i>
                                    <span class="text-truncate" style="max-width: 300px; display: inline-block; vertical-align: middle;">
                                        {{ $backup['filename'] }}
                                    </span>
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
                            <tr id="noBackupsRow">
                                <td colspan="5" class="text-center py-4 text-muted">
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
    const confirmRestoreModal = document.getElementById('confirmRestoreModal');
    const backupFile = document.getElementById('backupFile');
    const frequencySelect = document.getElementById('frequency');
    const dayOfWeekGroup = document.getElementById('dayOfWeekGroup');
    const dayOfMonthGroup = document.getElementById('dayOfMonthGroup');

    // Initialize Bootstrap modal
    let confirmModal = null;
    if (confirmRestoreModal) {
        confirmModal = new bootstrap.Modal(confirmRestoreModal);
    }

    // Handle frequency change for schedule
    function updateScheduleFields() {
        if (!frequencySelect) return;
        const freq = frequencySelect.value;
        if (dayOfWeekGroup) dayOfWeekGroup.style.display = freq === 'weekly' ? 'block' : 'none';
        if (dayOfMonthGroup) dayOfMonthGroup.style.display = freq === 'monthly' ? 'block' : 'none';
    }
    
    if (frequencySelect) {
        frequencySelect.addEventListener('change', updateScheduleFields);
        updateScheduleFields();
    }

    // Backup type detection when file is selected
    if (backupFile) {
        backupFile.addEventListener('change', function() {
            const file = this.files[0];
            const infoDiv = document.getElementById('backupTypeInfo');
            const typeSpan = document.getElementById('detectedType');
            const fullOptions = document.getElementById('fullBackupOptions');

            if (!file) {
                if (infoDiv) infoDiv.style.display = 'none';
                return;
            }

            // Show the info div
            if (infoDiv) infoDiv.style.display = 'block';
            
            // Detect type based on filename
            const filename = file.name.toLowerCase();
            let type = 'unknown';
            let typeBadge = '';

            if (filename.startsWith('full_backup_') || filename.includes('full_backup')) {
                type = 'full';
                typeBadge = '<span class="badge bg-success"><i class="bi bi-box-seam me-1"></i>Full System Backup</span>';
                if (fullOptions) fullOptions.style.display = 'block';
            } else if (filename.startsWith('code_backup_') || filename.includes('code_backup')) {
                type = 'code';
                typeBadge = '<span class="badge bg-info"><i class="bi bi-code-slash me-1"></i>Code Backup</span>';
                if (fullOptions) fullOptions.style.display = 'none';
            } else if (filename.endsWith('.json') || filename.startsWith('backup_') || filename.startsWith('selective_') || filename.startsWith('pre_restore_backup_')) {
                type = 'database';
                typeBadge = '<span class="badge bg-primary"><i class="bi bi-database me-1"></i>Database Backup</span>';
                if (fullOptions) fullOptions.style.display = 'none';
            } else if (filename.endsWith('.zip')) {
                // Could be any type, will auto-detect on server
                typeBadge = '<span class="badge bg-secondary"><i class="bi bi-file-zip me-1"></i>ZIP Archive (auto-detect)</span>';
                if (fullOptions) fullOptions.style.display = 'none';
            } else {
                typeBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-question-circle me-1"></i>Unknown Format</span>';
                if (fullOptions) fullOptions.style.display = 'none';
            }

            if (typeSpan) typeSpan.innerHTML = typeBadge;
            
            console.log('Backup file selected:', { filename, type, size: file.size });
        });
    }

    // Show confirmation modal before restore
    if (restoreBtn) {
        restoreBtn.addEventListener('click', function() {
            if (!backupFile || !backupFile.files.length) {
                alert('Please select a backup file first.');
                return;
            }
            if (confirmModal) {
                confirmModal.show();
            }
        });
    }

    // Submit form on confirm
    if (confirmRestoreBtn) {
        confirmRestoreBtn.addEventListener('click', function() {
            if (confirmModal) {
                confirmModal.hide();
            }
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Restoring...';
            
            if (restoreBtn) {
                restoreBtn.disabled = true;
                restoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Restoring...';
            }
            
            if (importForm) {
                importForm.submit();
            }
        });
    }

    // Delete backup
    document.querySelectorAll('.delete-backup-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.dataset.filename;
            
            if (confirm(`Are you sure you want to delete backup "${filename}"?`)) {
                fetch(`{{ url('admin/database-backup') }}/${encodeURIComponent(filename)}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
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

    // Show loading state on export forms
    function setupExportForm(formId, btnId, loadingText) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById(btnId);
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${loadingText}`;
                    // Re-enable after 30 seconds in case of error
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalHtml || loadingText.replace('Creating...', 'Download');
                    }, 30000);
                }
            });
            
            // Store original HTML
            const btn = document.getElementById(btnId);
            if (btn) {
                btn.dataset.originalHtml = btn.innerHTML;
            }
        }
    }
    
    setupExportForm('exportForm', 'exportBtn', 'Creating backup...');
    setupExportForm('codeExportForm', 'codeExportBtn', 'Creating backup...');
    setupExportForm('fullExportForm', 'fullExportBtn', 'Creating backup...');

    // Filter backup history by type
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter table rows
            const rows = document.querySelectorAll('#backupTable tbody tr[data-type]');
            rows.forEach(row => {
                if (filter === 'all' || row.dataset.type === filter || 
                    (filter === 'database' && ['database', 'pre-restore', 'selective'].includes(row.dataset.type))) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show "no backups" message if all filtered out
            const visibleRows = document.querySelectorAll('#backupTable tbody tr[data-type]:not([style*="display: none"])');
            const noBackupsRow = document.getElementById('noBackupsRow');
            if (visibleRows.length === 0 && !noBackupsRow) {
                // Could add a dynamic "no results" row here
            }
        });
    });
});
</script>
@endpush
