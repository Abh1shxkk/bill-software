# Automated Daily Backup Feature Plan (Login-Triggered Rolling Weekly Backup)

## Overview

Implement an automated backup system for the multi-tenant billing software that:
1. **Triggers automatically** when an admin logs into the dashboard
2. **Creates daily backups** with a **rolling 7-day retention** (Monday to Sunday)
3. **Replaces same-day backups** from the previous week (e.g., today's Monday backup replaces last Monday's)
4. **Organization-scoped** for multi-tenant isolation

## Current System Analysis

### Existing Backup Infrastructure
The system already has:
- `DatabaseBackupController` - Handles manual backup/restore operations
- `DatabaseBackupService` - Core backup logic (export, import, validation)
- `CodeBackupService` - Code/files backup functionality
- `BackupSchedule` model - Stores scheduled backup configurations
- `RunScheduledBackup` command - Artisan command for scheduled backups
- Storage location: `storage/app/backups/`

### Multi-Tenant Architecture
- **Organizations** are the tenant entities (`organizations` table)
- **Users** belong to organizations via `organization_id`
- Admin users have `role === 'admin'` or `role === 'super_admin'`
- Organization owner identified by `is_organization_owner === true`

### Authentication Flow
- Login handled by `AuthController::login()`
- Dashboard redirect: `/admin/dashboard` for org admins, `/superadmin/dashboard` for super admins
- Dashboard loaded by `DashboardController::index()`

---

## Feature Specifications

### 1. Backup Trigger Logic
| Event | Action |
|-------|--------|
| Admin/Org Owner logs in | Check if daily backup exists for today |
| No backup for today | Create new backup for current day of week |
| Backup already exists | Skip backup creation |
| Super Admin login | Skip auto-backup (super admin manages all orgs) |

### 2. Rolling 7-Day Retention
| Day | Backup File Pattern | Behavior |
|-----|---------------------|----------|
| Monday | `org_{id}_monday_backup.zip` | Replace previous Monday backup |
| Tuesday | `org_{id}_tuesday_backup.zip` | Replace previous Tuesday backup |
| Wednesday | `org_{id}_wednesday_backup.zip` | Replace previous Wednesday backup |
| Thursday | `org_{id}_thursday_backup.zip` | Replace previous Thursday backup |
| Friday | `org_{id}_friday_backup.zip` | Replace previous Friday backup |
| Saturday | `org_{id}_saturday_backup.zip` | Replace previous Saturday backup |
| Sunday | `org_{id}_sunday_backup.zip` | Replace previous Sunday backup |

### 3. Backup Content (Per Organization)
For multi-tenant isolation, backup only organization-specific data:
- Tables with `organization_id` column
- Associated child records (foreign key relationships)

---

## Technical Implementation

### Phase 1: Database Schema Updates

#### 1.1 Create `auto_backup_logs` Table Migration

```php
// database/migrations/2026_01_12_170000_create_auto_backup_logs_table.php

Schema::create('auto_backup_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('organization_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
    $table->string('day_of_week'); // monday, tuesday, etc.
    $table->string('backup_filename');
    $table->string('backup_path');
    $table->bigInteger('backup_size')->default(0); // in bytes
    $table->enum('status', ['success', 'failed', 'in_progress'])->default('in_progress');
    $table->text('error_message')->nullable();
    $table->timestamp('backup_date'); // The date this backup represents
    $table->timestamps();
    
    // Unique constraint: one backup per org per day of week
    $table->unique(['organization_id', 'day_of_week'], 'unique_org_day_backup');
});
```

#### 1.2 Update `organizations` Table (Optional Settings)

```php
// database/migrations/2026_01_12_170100_add_auto_backup_settings_to_organizations.php

Schema::table('organizations', function (Blueprint $table) {
    $table->boolean('auto_backup_enabled')->default(true);
    $table->json('auto_backup_tables')->nullable(); // Optional: specific tables to backup
});
```

---

### Phase 2: Models

#### 2.1 Create `AutoBackupLog` Model

```php
// app/Models/AutoBackupLog.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoBackupLog extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'day_of_week',
        'backup_filename',
        'backup_path',
        'backup_size',
        'status',
        'error_message',
        'backup_date',
    ];

    protected $casts = [
        'backup_date' => 'date',
        'backup_size' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Check if a backup exists for today for the given organization
     */
    public static function hasBackupForToday(int $organizationId): bool
    {
        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        
        return self::where('organization_id', $organizationId)
            ->where('day_of_week', $today)
            ->where('status', 'success')
            ->whereDate('backup_date', today())
            ->exists();
    }

    /**
     * Get or create backup record for today
     */
    public static function getOrCreateForToday(int $organizationId, int $userId): self
    {
        $today = strtolower(now()->format('l'));
        
        // Delete previous same-day backup record (from last week)
        self::where('organization_id', $organizationId)
            ->where('day_of_week', $today)
            ->delete();
        
        return self::create([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'day_of_week' => $today,
            'backup_date' => today(),
            'status' => 'in_progress',
            'backup_filename' => '',
            'backup_path' => '',
        ]);
    }
}
```

#### 2.2 Update `Organization` Model

```php
// Add to app/Models/Organization.php

public function autoBackupLogs(): HasMany
{
    return $this->hasMany(AutoBackupLog::class, 'organization_id');
}

/**
 * Check if auto backup is enabled for this organization
 */
public function isAutoBackupEnabled(): bool
{
    return $this->auto_backup_enabled ?? true;
}

/**
 * Get the latest backup for each day of the week
 */
public function getWeeklyBackupStatus(): array
{
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $status = [];
    
    foreach ($days as $day) {
        $backup = $this->autoBackupLogs()
            ->where('day_of_week', $day)
            ->where('status', 'success')
            ->first();
        
        $status[$day] = $backup ? [
            'exists' => true,
            'date' => $backup->backup_date->format('Y-m-d'),
            'size' => $backup->backup_size,
            'filename' => $backup->backup_filename,
        ] : [
            'exists' => false,
        ];
    }
    
    return $status;
}
```

---

### Phase 3: Services

#### 3.1 Create `OrganizationBackupService`

```php
// app/Services/OrganizationBackupService.php

<?php

namespace App\Services;

use App\Models\AutoBackupLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class OrganizationBackupService
{
    protected string $backupPath;
    
    /**
     * Tables that should be included in organization backup
     * These are tables that have organization_id column
     */
    protected array $organizationTables = [
        'users',
        'customers',
        'suppliers',
        'items',
        'batches',
        'sale_transactions',
        'sale_transaction_items',
        'purchase_transactions',
        'purchase_transaction_items',
        'sale_return_transactions',
        'sale_return_transaction_items',
        'purchase_return_transactions',
        'purchase_return_transaction_items',
        'customer_ledgers',
        'supplier_ledgers',
        'stock_transfers',
        'stock_transfer_items',
        'sales_men',
        // Add more tables as needed
    ];
    
    public function __construct()
    {
        $this->backupPath = storage_path('app/backups/organizations');
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Create organization-specific backup
     */
    public function createBackup(Organization $organization, User $triggeredBy): array
    {
        $dayOfWeek = strtolower(now()->format('l'));
        $orgCode = $organization->code ?? $organization->id;
        
        Log::info('Starting auto backup', [
            'organization_id' => $organization->id,
            'day_of_week' => $dayOfWeek,
            'triggered_by' => $triggeredBy->user_id,
        ]);

        // Create or update backup log
        $backupLog = AutoBackupLog::getOrCreateForToday(
            $organization->id, 
            $triggeredBy->user_id
        );

        try {
            // Generate backup filename
            $filename = "org_{$orgCode}_{$dayOfWeek}_backup.zip";
            $filepath = $this->backupPath . '/' . $filename;
            
            // Delete old backup file if exists
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            // Create backup data
            $backupData = $this->exportOrganizationData($organization);
            
            // Save as JSON first
            $jsonFilename = "org_{$orgCode}_{$dayOfWeek}_backup.json";
            $jsonPath = $this->backupPath . '/' . $jsonFilename;
            File::put($jsonPath, json_encode($backupData, JSON_PRETTY_PRINT));
            
            // Compress to ZIP
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE) === true) {
                $zip->addFile($jsonPath, $jsonFilename);
                $zip->close();
                File::delete($jsonPath); // Remove JSON after zipping
            }

            // Update backup log
            $backupLog->update([
                'backup_filename' => $filename,
                'backup_path' => $filepath,
                'backup_size' => File::size($filepath),
                'status' => 'success',
                'error_message' => null,
            ]);

            Log::info('Auto backup completed successfully', [
                'organization_id' => $organization->id,
                'filename' => $filename,
                'size' => File::size($filepath),
            ]);

            return [
                'success' => true,
                'message' => 'Backup created successfully',
                'filename' => $filename,
                'size' => File::size($filepath),
                'backup_log_id' => $backupLog->id,
            ];

        } catch (\Exception $e) {
            Log::error('Auto backup failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            $backupLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Export organization-specific data
     */
    protected function exportOrganizationData(Organization $organization): array
    {
        $data = [
            'metadata' => [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'organization_code' => $organization->code,
                'created_at' => now()->toIso8601String(),
                'day_of_week' => strtolower(now()->format('l')),
                'backup_type' => 'auto_daily',
            ],
            'tables' => [],
        ];

        foreach ($this->organizationTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            // Check if table has organization_id column
            if (!Schema::hasColumn($table, 'organization_id')) {
                continue;
            }

            $records = DB::table($table)
                ->where('organization_id', $organization->id)
                ->get()
                ->toArray();

            $data['tables'][$table] = [
                'count' => count($records),
                'data' => $records,
            ];
        }

        // Include organization record itself
        $data['organization'] = $organization->toArray();

        return $data;
    }

    /**
     * Check if backup is needed for today
     */
    public function isBackupNeededToday(int $organizationId): bool
    {
        return !AutoBackupLog::hasBackupForToday($organizationId);
    }

    /**
     * Get backup history for organization
     */
    public function getBackupHistory(int $organizationId): array
    {
        return AutoBackupLog::where('organization_id', $organizationId)
            ->where('status', 'success')
            ->orderBy('backup_date', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Restore organization backup
     */
    public function restoreBackup(string $filename, Organization $organization): array
    {
        $filepath = $this->backupPath . '/' . $filename;
        
        if (!File::exists($filepath)) {
            return ['success' => false, 'message' => 'Backup file not found'];
        }

        try {
            // Extract ZIP
            $extractPath = $this->backupPath . '/temp_' . time();
            $zip = new ZipArchive();
            
            if ($zip->open($filepath) !== true) {
                return ['success' => false, 'message' => 'Could not open backup file'];
            }
            
            $zip->extractTo($extractPath);
            $zip->close();

            // Find JSON file
            $jsonFiles = glob($extractPath . '/*.json');
            if (empty($jsonFiles)) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => 'No backup data found in archive'];
            }

            $content = File::get($jsonFiles[0]);
            $data = json_decode($content, true);

            if (!$data || !isset($data['tables'])) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => 'Invalid backup format'];
            }

            // Verify organization match
            if (($data['metadata']['organization_id'] ?? null) !== $organization->id) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => 'Backup does not belong to this organization'];
            }

            // Restore tables
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            $tablesRestored = 0;

            foreach ($data['tables'] as $table => $tableData) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                // Delete existing organization data
                DB::table($table)->where('organization_id', $organization->id)->delete();

                // Insert backup data
                if (!empty($tableData['data'])) {
                    foreach (array_chunk($tableData['data'], 100) as $chunk) {
                        $insertData = array_map(fn($row) => (array) $row, $chunk);
                        DB::table($table)->insert($insertData);
                    }
                }
                
                $tablesRestored++;
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            
            // Cleanup
            File::deleteDirectory($extractPath);

            Log::info('Organization backup restored', [
                'organization_id' => $organization->id,
                'filename' => $filename,
                'tables_restored' => $tablesRestored,
            ]);

            return [
                'success' => true,
                'message' => 'Backup restored successfully',
                'tables_restored' => $tablesRestored,
            ];

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            Log::error('Backup restore failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()];
        }
    }

    /**
     * Download backup file
     */
    public function getBackupPath(string $filename): ?string
    {
        $path = $this->backupPath . '/' . basename($filename);
        return File::exists($path) ? $path : null;
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(string $filename, int $organizationId): bool
    {
        $path = $this->backupPath . '/' . basename($filename);
        
        // Remove from database
        AutoBackupLog::where('organization_id', $organizationId)
            ->where('backup_filename', $filename)
            ->delete();
        
        // Remove file
        if (File::exists($path)) {
            return File::delete($path);
        }

        return true;
    }
}
```

---

### Phase 4: Event Listener for Login

#### 4.1 Create Event Listener

```php
// app/Listeners/TriggerAutoBackupOnLogin.php

<?php

namespace App\Listeners;

use App\Models\AutoBackupLog;
use App\Services\OrganizationBackupService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class TriggerAutoBackupOnLogin
{
    protected OrganizationBackupService $backupService;

    public function __construct(OrganizationBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function handle(Login $event): void
    {
        $user = $event->user;

        // Skip for super admins - they manage all organizations
        if ($user->isSuperAdmin()) {
            return;
        }

        // Skip for users without organization
        if (!$user->organization_id || !$user->organization) {
            return;
        }

        // Skip if not an admin or organization owner
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return;
        }

        // Check if auto backup is enabled for organization
        if (!$user->organization->isAutoBackupEnabled()) {
            Log::info('Auto backup disabled for organization', [
                'organization_id' => $user->organization_id,
            ]);
            return;
        }

        // Check if backup already exists for today
        if (!$this->backupService->isBackupNeededToday($user->organization_id)) {
            Log::debug('Auto backup already exists for today', [
                'organization_id' => $user->organization_id,
            ]);
            return;
        }

        // Dispatch backup to queue for async processing
        dispatch(function () use ($user) {
            $this->backupService->createBackup(
                $user->organization,
                $user
            );
        })->afterResponse();

        Log::info('Auto backup triggered on login', [
            'organization_id' => $user->organization_id,
            'user_id' => $user->user_id,
        ]);
    }
}
```

#### 4.2 Register Event Listener

```php
// app/Providers/EventServiceProvider.php (or bootstrap/app.php for Laravel 11)

use App\Listeners\TriggerAutoBackupOnLogin;
use Illuminate\Auth\Events\Login;

// Add to $listen array:
protected $listen = [
    Login::class => [
        TriggerAutoBackupOnLogin::class,
    ],
];
```

---

### Phase 5: Controller Updates

#### 5.1 Create `AutoBackupController`

```php
// app/Http/Controllers/Admin/AutoBackupController.php

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoBackupLog;
use App\Services\OrganizationBackupService;
use Illuminate\Http\Request;

class AutoBackupController extends Controller
{
    protected OrganizationBackupService $backupService;

    public function __construct(OrganizationBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Show auto backup status dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->organization_id) {
            abort(403, 'No organization assigned');
        }

        $weeklyStatus = $user->organization->getWeeklyBackupStatus();
        $backupHistory = AutoBackupLog::where('organization_id', $user->organization_id)
            ->orderBy('backup_date', 'desc')
            ->limit(30)
            ->get();
        
        $autoBackupEnabled = $user->organization->auto_backup_enabled ?? true;

        return view('admin.auto-backup.index', compact(
            'weeklyStatus',
            'backupHistory',
            'autoBackupEnabled'
        ));
    }

    /**
     * Toggle auto backup setting
     */
    public function toggleAutoBackup(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user->organization->update([
            'auto_backup_enabled' => $request->boolean('enabled'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Auto backup setting updated',
            'enabled' => $user->organization->auto_backup_enabled,
        ]);
    }

    /**
     * Manually trigger backup
     */
    public function triggerManualBackup()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return back()->with('error', 'Unauthorized');
        }

        $result = $this->backupService->createBackup($user->organization, $user);

        if ($result['success']) {
            return back()->with('success', 'Backup created successfully: ' . $result['filename']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Download a backup file
     */
    public function download(string $filename)
    {
        $user = auth()->user();
        
        // Verify the backup belongs to user's organization
        $backup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('backup_filename', $filename)
            ->firstOrFail();

        $path = $this->backupService->getBackupPath($filename);
        
        if (!$path) {
            return back()->with('error', 'Backup file not found');
        }

        return response()->download($path, $filename);
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request, string $filename)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return back()->with('error', 'Unauthorized');
        }

        // Verify the backup belongs to user's organization
        $backup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('backup_filename', $filename)
            ->firstOrFail();

        $result = $this->backupService->restoreBackup($filename, $user->organization);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Delete a backup
     */
    public function destroy(string $filename)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $deleted = $this->backupService->deleteBackup($filename, $user->organization_id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Backup deleted' : 'Failed to delete backup',
        ]);
    }
}
```

#### 5.2 Add Routes

```php
// routes/web.php

// Auto Backup Routes (Admin section)
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    // ... existing routes ...
    
    // Auto Backup
    Route::get('auto-backup', [AutoBackupController::class, 'index'])
        ->name('auto-backup.index');
    Route::post('auto-backup/toggle', [AutoBackupController::class, 'toggleAutoBackup'])
        ->name('auto-backup.toggle');
    Route::post('auto-backup/trigger', [AutoBackupController::class, 'triggerManualBackup'])
        ->name('auto-backup.trigger');
    Route::get('auto-backup/download/{filename}', [AutoBackupController::class, 'download'])
        ->name('auto-backup.download');
    Route::post('auto-backup/restore/{filename}', [AutoBackupController::class, 'restore'])
        ->name('auto-backup.restore');
    Route::delete('auto-backup/{filename}', [AutoBackupController::class, 'destroy'])
        ->name('auto-backup.destroy');
});
```

---

### Phase 6: Views

#### 6.1 Create Auto Backup Dashboard View

```blade
{{-- resources/views/admin/auto-backup/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Auto Backup Status')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-clock-rotate-left me-2"></i>
                    Automated Daily Backup
                </h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="toggleAutoBackup">
                        <i class="fas fa-power-off me-1"></i>
                        <span id="toggleLabel">{{ $autoBackupEnabled ? 'Enabled' : 'Disabled' }}</span>
                    </button>
                    <form action="{{ route('admin.auto-backup.trigger') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i> Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly Status Grid --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Weekly Backup Status</h6>
                    <small class="text-muted">Rolling 7-day backup schedule</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $isToday = strtolower(now()->format('l')) === $day;
                                $backup = $weeklyStatus[$day];
                            @endphp
                            <div class="col-md-3 col-lg">
                                <div class="card h-100 {{ $isToday ? 'border-primary' : '' }} {{ $backup['exists'] ? 'bg-success-subtle' : 'bg-secondary-subtle' }}">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title text-capitalize mb-2">
                                            {{ $day }}
                                            @if($isToday)
                                                <span class="badge bg-primary">Today</span>
                                            @endif
                                        </h6>
                                        @if($backup['exists'])
                                            <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                            <p class="small mb-1">{{ $backup['date'] }}</p>
                                            <p class="small text-muted mb-0">
                                                {{ number_format($backup['size'] / 1024, 2) }} KB
                                            </p>
                                        @else
                                            <i class="fas fa-times-circle text-secondary fs-3 mb-2"></i>
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
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Backup History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Backup Date</th>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Triggered By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backupHistory as $backup)
                                    <tr>
                                        <td class="text-capitalize">{{ $backup->day_of_week }}</td>
                                        <td>{{ $backup->backup_date->format('Y-m-d') }}</td>
                                        <td><code>{{ $backup->backup_filename }}</code></td>
                                        <td>{{ number_format($backup->backup_size / 1024, 2) }} KB</td>
                                        <td>
                                            @if($backup->status === 'success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif($backup->status === 'failed')
                                                <span class="badge bg-danger" title="{{ $backup->error_message }}">Failed</span>
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>{{ $backup->user->full_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($backup->status === 'success')
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.auto-backup.download', $backup->backup_filename) }}" 
                                                       class="btn btn-outline-primary" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-warning restore-btn" 
                                                            data-filename="{{ $backup->backup_filename }}"
                                                            title="Restore">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                                            data-filename="{{ $backup->backup_filename }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No backup history found. Backups will be created automatically when you log in.
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
@endsection

@section('scripts')
<script>
document.getElementById('toggleAutoBackup')?.addEventListener('click', function() {
    const currentState = this.querySelector('#toggleLabel').textContent === 'Enabled';
    
    fetch('{{ route("admin.auto-backup.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ enabled: !currentState })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('toggleLabel').textContent = data.enabled ? 'Enabled' : 'Disabled';
        }
    });
});

// Restore button handler
document.querySelectorAll('.restore-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filename = this.dataset.filename;
        if (confirm('Are you sure you want to restore this backup? This will replace all current data for your organization.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("admin/auto-backup/restore") }}/' + filename;
            form.innerHTML = '@csrf';
            document.body.appendChild(form);
            form.submit();
        }
    });
});

// Delete button handler
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filename = this.dataset.filename;
        if (confirm('Are you sure you want to delete this backup?')) {
            fetch('{{ url("admin/auto-backup") }}/' + filename, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    });
});
</script>
@endsection
```

---

### Phase 7: Testing Plan

#### 7.1 Unit Tests

```php
// tests/Feature/AutoBackupTest.php

<?php

namespace Tests\Feature;

use App\Models\AutoBackupLog;
use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_created_on_admin_login()
    {
        $org = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $org->id,
            'role' => 'admin',
        ]);

        // Login
        $this->post('/login', [
            'username' => $admin->username,
            'password' => 'password',
        ]);

        // Assert backup was created
        $this->assertDatabaseHas('auto_backup_logs', [
            'organization_id' => $org->id,
            'user_id' => $admin->user_id,
            'day_of_week' => strtolower(now()->format('l')),
        ]);
    }

    public function test_backup_not_created_for_non_admin()
    {
        $org = Organization::factory()->create();
        $staff = User::factory()->create([
            'organization_id' => $org->id,
            'role' => 'staff',
        ]);

        $this->post('/login', [
            'username' => $staff->username,
            'password' => 'password',
        ]);

        $this->assertDatabaseMissing('auto_backup_logs', [
            'organization_id' => $org->id,
        ]);
    }

    public function test_duplicate_backup_not_created_same_day()
    {
        $org = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $org->id,
            'role' => 'admin',
        ]);

        // First login
        $this->post('/login', ['username' => $admin->username, 'password' => 'password']);
        $this->post('/logout');

        // Second login same day
        $this->post('/login', ['username' => $admin->username, 'password' => 'password']);

        // Should only have one backup per day
        $backupCount = AutoBackupLog::where('organization_id', $org->id)
            ->where('day_of_week', strtolower(now()->format('l')))
            ->count();

        $this->assertEquals(1, $backupCount);
    }

    public function test_previous_week_backup_replaced()
    {
        $org = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $org->id,
            'role' => 'admin',
        ]);

        // Create a backup from "last week" (same day)
        $lastWeekBackup = AutoBackupLog::create([
            'organization_id' => $org->id,
            'user_id' => $admin->user_id,
            'day_of_week' => strtolower(now()->format('l')),
            'backup_filename' => 'old_backup.zip',
            'backup_path' => '/fake/path',
            'backup_date' => now()->subWeek(),
            'status' => 'success',
        ]);

        // Login now
        $this->post('/login', ['username' => $admin->username, 'password' => 'password']);

        // Old backup should be deleted
        $this->assertDatabaseMissing('auto_backup_logs', [
            'id' => $lastWeekBackup->id,
        ]);

        // New backup should exist
        $this->assertDatabaseHas('auto_backup_logs', [
            'organization_id' => $org->id,
            'backup_date' => now()->toDateString(),
        ]);
    }
}
```

---

## Implementation Checklist

### Phase 1: Database Schema ⬜
- [ ] Create migration for `auto_backup_logs` table
- [ ] Add `auto_backup_enabled` column to `organizations` table
- [ ] Run migrations

### Phase 2: Models ⬜
- [ ] Create `AutoBackupLog` model
- [ ] Update `Organization` model with relationships and methods
- [ ] Update `User` model if needed

### Phase 3: Services ⬜
- [ ] Create `OrganizationBackupService`
- [ ] Implement `createBackup()` method
- [ ] Implement `exportOrganizationData()` method
- [ ] Implement `restoreBackup()` method
- [ ] Implement helper methods

### Phase 4: Event Listener ⬜
- [ ] Create `app/Listeners` directory
- [ ] Create `TriggerAutoBackupOnLogin` listener
- [ ] Register listener in `EventServiceProvider`

### Phase 5: Controller & Routes ⬜
- [ ] Create `AutoBackupController`
- [ ] Add routes to `web.php`
- [ ] Test controller methods

### Phase 6: Views ⬜
- [ ] Create `resources/views/admin/auto-backup/index.blade.php`
- [ ] Add to sidebar navigation
- [ ] Style the weekly status grid

### Phase 7: Testing ⬜
- [ ] Write unit tests
- [ ] Write integration tests
- [ ] Manual testing with different scenarios

---

## Security Considerations

1. **Access Control**
   - Only admins and organization owners can view/manage backups
   - Backups are scoped to organization (no cross-organization access)
   - Super admins are excluded from auto-backup (they manage system-wide)

2. **File Security**
   - Backups stored in non-public directory (`storage/app/backups/organizations/`)
   - Filename includes organization identifier for isolation
   - Validation before restore to prevent cross-org restore

3. **Data Integrity**
   - Transaction-based restore with rollback on failure
   - Backup validation before restore
   - Logging of all backup/restore operations

---

## Estimated Timeline

| Phase | Description | Time Estimate |
|-------|-------------|---------------|
| 1 | Database Schema | 30 minutes |
| 2 | Models | 45 minutes |
| 3 | Services | 2 hours |
| 4 | Event Listener | 30 minutes |
| 5 | Controller & Routes | 1 hour |
| 6 | Views | 1.5 hours |
| 7 | Testing | 1.5 hours |
| **Total** | | **~8 hours** |

---

## Future Enhancements

1. **Email Notifications** - Send backup status emails to admin
2. **Cloud Storage Integration** - Option to store backups on S3/GCS
3. **Backup Encryption** - Encrypt backup files at rest
4. **Custom Retention Policy** - Allow organizations to set custom retention
5. **Scheduled Backup Time** - Let organizations choose backup time instead of login-triggered
6. **Differential Backups** - Only backup changed data after first full backup
