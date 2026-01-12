@extends('layouts.admin')

@section('title', 'Automated Daily Backup')

@section('styles')
<style>
    .backup-day-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .backup-day-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .backup-day-card.today {
        border-color: var(--bs-primary);
    }
    .backup-day-card.has-backup {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
    }
    .backup-day-card.no-backup {
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(108, 117, 125, 0.05) 100%);
    }
    .status-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .backup-toggle {
        position: relative;
        width: 60px;
        height: 30px;
    }
    .backup-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: var(--bs-success);
    }
    input:checked + .toggle-slider:before {
        transform: translateX(30px);
    }
    .backup-history-table {
        font-size: 0.9rem;
    }
    .backup-history-table .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
    .info-card {
        background: linear-gradient(135deg, var(--bs-primary) 0%, #4f46e5 100%);
        color: white;
        border-radius: 12px;
    }
    .info-card .icon-wrapper {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clock-rotate-left text-primary me-2"></i>
                        Automated Daily Backup
                    </h4>
                    <p class="text-muted mb-0">
                        <small>Full backup (Database + Code) created automatically on admin login. Rolling 7-day retention.</small>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    {{-- Auto Backup Toggle --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Auto Backup:</span>
                        <label class="backup-toggle mb-0">
                            <input type="checkbox" id="autoBackupToggle" {{ $autoBackupEnabled ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span id="toggleStatusText" class="badge {{ $autoBackupEnabled ? 'bg-success' : 'bg-secondary' }}">
                            {{ $autoBackupEnabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    
                    {{-- Manual Backup Button --}}
                    <form action="{{ route('admin.auto-backup.trigger') }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Create a full backup for today ({{ ucfirst($todayDay) }})? This includes Database + Code files.');">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i> Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Today</div>
                        <div class="h5 mb-0">{{ ucfirst($todayDay) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Database Tables</div>
                        <div class="h5 mb-0">{{ count($backupTables) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper">
                        <i class="fas fa-code"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Code Directories</div>
                        <div class="h5 mb-0">7</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Retention</div>
                        <div class="h5 mb-0">7 Days</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- What's Backed Up Info --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>What's Included in Backup
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-database me-2"></i>Database ({{ count($backupTables) }} Tables)</h6>
                            <p class="small text-muted mb-2">All organization-specific data including:</p>
                            <ul class="small text-muted mb-0" style="columns: 2;">
                                <li>Customers & Suppliers</li>
                                <li>Items & Batches</li>
                                <li>Sales & Purchases</li>
                                <li>Returns & Challans</li>
                                <li>Ledgers & Payments</li>
                                <li>Stock Transfers</li>
                                <li>Breakage/Expiry</li>
                                <li>And {{ count($backupTables) - 7 }}+ more...</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-code me-2"></i>Code (7 Directories)</h6>
                            <p class="small text-muted mb-2">Application source code:</p>
                            <ul class="small text-muted mb-0" style="columns: 2;">
                                <li><code>app/</code> - Controllers, Models</li>
                                <li><code>config/</code> - Configuration</li>
                                <li><code>database/</code> - Migrations</li>
                                <li><code>resources/</code> - Views, Assets</li>
                                <li><code>routes/</code> - Route definitions</li>
                                <li><code>public/</code> - Public files</li>
                                <li><code>bootstrap/</code> - Bootstrap files</li>
                                <li>+ Root config files</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Status Grid --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-week text-primary me-2"></i>Weekly Backup Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $isToday = $todayDay === $day;
                                $backup = $weeklyStatus[$day];
                                $hasBackup = $backup['exists'];
                            @endphp
                            <div class="col-6 col-md-4 col-lg">
                                <div class="card h-100 backup-day-card {{ $isToday ? 'today' : '' }} {{ $hasBackup ? 'has-backup' : 'no-backup' }}">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title text-capitalize fw-bold mb-2">
                                            {{ $day }}
                                        </h6>
                                        @if($isToday)
                                            <span class="badge bg-primary mb-2">Today</span>
                                        @endif
                                        
                                        @if($hasBackup)
                                            <div class="status-icon text-success">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <p class="small mb-1 text-muted">{{ $backup['date'] }}</p>
                                            <p class="small mb-0">
                                                <span class="badge bg-light text-dark">{{ $backup['size_formatted'] }}</span>
                                            </p>
                                        @else
                                            <div class="status-icon text-secondary">
                                                <i class="fas fa-times-circle"></i>
                                            </div>
                                            <p class="small text-muted mb-0">No backup</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup History --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-list text-primary me-2"></i>Backup History
                    </h6>
                    <span class="badge bg-secondary">{{ $backupHistory->count() }} backups</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 backup-history-table" style="min-width: 1000px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 100px;">Day</th>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 200px;">Filename</th>
                                    <th style="width: 80px;">Size</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;">Triggered By</th>
                                    <th style="width: 80px;">Time</th>
                                    <th style="width: 150px; min-width: 150px;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backupHistory as $backup)
                                    <tr>
                                        <td class="text-capitalize fw-medium">
                                            <i class="fas fa-calendar-day text-muted me-1"></i>
                                            {{ $backup->day_of_week }}
                                        </td>
                                        <td class="text-nowrap">{{ $backup->backup_date->format('Y-m-d') }}</td>
                                        <td>
                                            <code class="small text-truncate d-inline-block" style="max-width: 180px;" title="{{ $backup->backup_filename }}">
                                                {{ $backup->backup_filename ?: '-' }}
                                            </code>
                                        </td>
                                        <td class="text-nowrap">{{ $backup->formatted_size }}</td>
                                        <td>
                                            <span class="badge {{ $backup->status_badge_class }}">
                                                @if($backup->status === 'success')
                                                    <i class="fas fa-check me-1"></i>
                                                @elseif($backup->status === 'failed')
                                                    <i class="fas fa-times me-1"></i>
                                                @else
                                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                                @endif
                                                {{ ucfirst($backup->status) }}
                                            </span>
                                            @if($backup->status === 'failed' && $backup->error_message)
                                                <i class="fas fa-info-circle text-danger ms-1" 
                                                   data-bs-toggle="tooltip" 
                                                   title="{{ $backup->error_message }}"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $backup->user->full_name ?? 'System' }}</small>
                                        </td>
                                        <td class="text-nowrap">
                                            <small class="text-muted">{{ $backup->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;">
                                            @if($backup->status === 'success' && $backup->backup_filename)
                                                <a href="{{ route('admin.auto-backup.download', $backup->backup_filename) }}" 
                                                   class="btn btn-sm btn-outline-primary me-1" 
                                                   title="Download"
                                                   style="width: 32px; height: 32px; padding: 6px; line-height: 1;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning me-1 restore-backup-btn" 
                                                        data-filename="{{ $backup->backup_filename }}"
                                                        data-day="{{ $backup->day_of_week }}"
                                                        data-date="{{ $backup->backup_date->format('Y-m-d') }}"
                                                        title="Restore"
                                                        style="width: 32px; height: 32px; padding: 6px; line-height: 1;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-filename="{{ $backup->backup_filename }}"
                                                        title="Delete"
                                                        style="width: 32px; height: 32px; padding: 6px; line-height: 1;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No backup history found.</p>
                                            <small class="text-muted">Backups will be created automatically when you log in.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Restore Confirmation Modal --}}
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Restore
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This will replace all current data for your organization with the backup data.
                </div>
                <p>You are about to restore the backup from:</p>
                <ul>
                    <li><strong>Day:</strong> <span id="restoreDay"></span></li>
                    <li><strong>Date:</strong> <span id="restoreDate"></span></li>
                    <li><strong>File:</strong> <code id="restoreFilename"></code></li>
                </ul>
                <p class="text-danger mb-0">
                    <strong>This action cannot be undone!</strong> A pre-restore backup will be created automatically.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="restoreForm" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="filename" id="restoreFilenameInput" value="">
                    <button type="submit" class="btn btn-warning" id="restoreSubmitBtn">
                        <i class="fas fa-undo me-1"></i> Restore Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Restore Button Click Handler
    const restoreButtons = document.querySelectorAll('.restore-backup-btn');
    console.log('Found restore buttons:', restoreButtons.length);
    
    restoreButtons.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            console.log('Button clicked:', index);
            e.preventDefault();
            e.stopPropagation();
            
            const filename = this.dataset.filename;
            const day = this.dataset.day;
            const date = this.dataset.date;
            
            console.log('Backup details:', {filename, day, date});
            
            // Update modal content
            document.getElementById('restoreDay').textContent = day.charAt(0).toUpperCase() + day.slice(1);
            document.getElementById('restoreDate').textContent = date;
            document.getElementById('restoreFilename').textContent = filename;
            
            // Set form action with the correct URL
            const restoreForm = document.getElementById('restoreForm');
            const actionUrl = '{{ route("admin.auto-backup.restore", ":filename") }}'.replace(':filename', encodeURIComponent(filename));
            restoreForm.action = actionUrl;
            
            console.log('Form action set to:', actionUrl);
            
            // Show the modal
            try {
                const restoreModal = new bootstrap.Modal(document.getElementById('restoreModal'));
                restoreModal.show();
                console.log('Modal shown');
            } catch (error) {
                console.error('Error showing modal:', error);
                alert('Error: ' + error.message);
            }
            
            return false;
        });
    });
    
    // Handle form submission
    const restoreForm = document.getElementById('restoreForm');
    if (restoreForm) {
        restoreForm.addEventListener('submit', function(e) {
            console.log('Form submitting to:', this.action);
            const submitBtn = document.getElementById('restoreSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Restoring...';
        });
    }

    // Auto Backup Toggle
    const toggleCheckbox = document.getElementById('autoBackupToggle');
    const toggleStatusText = document.getElementById('toggleStatusText');
    
    if (toggleCheckbox) {
        toggleCheckbox.addEventListener('change', function() {
            const enabled = this.checked;
            
            fetch('{{ route("admin.auto-backup.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ enabled: enabled })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toggleStatusText.textContent = data.enabled ? 'Enabled' : 'Disabled';
                    toggleStatusText.className = 'badge ' + (data.enabled ? 'bg-success' : 'bg-secondary');
                    
                    // Show toast notification
                    showToast('Auto backup ' + (data.enabled ? 'enabled' : 'disabled') + ' successfully', 'success');
                } else {
                    // Revert checkbox
                    toggleCheckbox.checked = !enabled;
                    showToast(data.message || 'Failed to update setting', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggleCheckbox.checked = !enabled;
                showToast('An error occurred', 'danger');
            });
        });
    }

    // Delete Button Handlers
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.dataset.filename;
            
            if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
                fetch('{{ url("admin/auto-backup") }}/' + filename, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Backup deleted successfully', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Failed to delete backup', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while deleting', 'danger');
                });
            }
        });
    });

    // Toast notification function
    function showToast(message, type) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    }
});
</script>
@endsection
