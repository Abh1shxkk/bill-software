# Database Backup Feature Plan

## Overview
Implement a database backup and restore feature that allows admin users to export and import complete database backups. This feature will be restricted to admin users only.

## Features

### 1. Export Database (Backup)
- Full database export to SQL/JSON format
- Download backup file with timestamp naming
- Option to select specific tables (optional enhancement)
- Compressed backup option (.zip)

### 2. Import Database (Restore)
- Upload and restore from backup file
- Validation before restore
- Confirmation prompt with warnings
- Transaction-based restore (rollback on failure)

### 3. Backup History
- List of previous backups (if stored on server)
- Download previous backups
- Delete old backups

## Technical Implementation

### Files to Create

```
app/
├── Http/
│   └── Controllers/
│       └── Admin/
│           └── DatabaseBackupController.php
├── Services/
│   └── DatabaseBackupService.php

resources/
└── views/
    └── admin/
        └── database-backup/
            ├── index.blade.php
            └── history.blade.php

storage/
└── app/
    └── backups/          (backup storage directory)
```

### Routes (routes/web.php)
```php
// Database Backup Routes (Admin Only)
Route::middleware(['admin.only'])->group(function () {
    Route::get('database-backup', [DatabaseBackupController::class, 'index'])->name('database-backup.index');
    Route::post('database-backup/export', [DatabaseBackupController::class, 'export'])->name('database-backup.export');
    Route::post('database-backup/import', [DatabaseBackupController::class, 'import'])->name('database-backup.import');
    Route::get('database-backup/history', [DatabaseBackupController::class, 'history'])->name('database-backup.history');
    Route::get('database-backup/download/{filename}', [DatabaseBackupController::class, 'download'])->name('database-backup.download');
    Route::delete('database-backup/{filename}', [DatabaseBackupController::class, 'destroy'])->name('database-backup.destroy');
});
```

### Controller Methods

| Method | Description |
|--------|-------------|
| `index()` | Display backup/restore interface |
| `export()` | Generate and download database backup |
| `import()` | Upload and restore database from backup |
| `history()` | List stored backups |
| `download()` | Download specific backup file |
| `destroy()` | Delete backup file |

### DatabaseBackupService Methods

| Method | Description |
|--------|-------------|
| `exportDatabase()` | Export all tables to SQL/JSON |
| `importDatabase()` | Import data from backup file |
| `validateBackupFile()` | Validate backup file structure |
| `getBackupHistory()` | Get list of stored backups |
| `deleteBackup()` | Remove backup file |

## Security Considerations

1. **Admin Only Access**
   - Create new middleware or use existing role check
   - Verify `auth()->user()->role === 'admin'`

2. **File Validation**
   - Validate file extension (.sql, .json, .zip)
   - Check file size limits
   - Sanitize file contents before import

3. **Backup Storage**
   - Store in non-public directory (`storage/app/backups/`)
   - Add to `.gitignore`

4. **Import Safety**
   - Use database transactions
   - Create automatic backup before restore
   - Confirmation with password re-entry

## Database Support

The system uses SQLite by default (configurable). The backup service should support:
- SQLite (primary)
- MySQL (if configured)

## UI Design

### Backup Page Layout
```
┌─────────────────────────────────────────────────────┐
│  Database Backup & Restore                          │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌─────────────────────┐  ┌─────────────────────┐  │
│  │   Export Backup     │  │   Import Backup     │  │
│  │                     │  │                     │  │
│  │  Format: [SQL ▼]    │  │  [Choose File...]   │  │
│  │  □ Compress (.zip)  │  │                     │  │
│  │                     │  │  ⚠ Warning: This    │  │
│  │  [Download Backup]  │  │  will replace all   │  │
│  │                     │  │  existing data      │  │
│  └─────────────────────┘  │                     │  │
│                           │  [Restore Database] │  │
│                           └─────────────────────┘  │
│                                                     │
│  Recent Backups                                     │
│  ┌─────────────────────────────────────────────┐   │
│  │ backup_2026-01-10_143022.sql    50MB  [⬇][🗑]│   │
│  │ backup_2026-01-09_091500.sql    48MB  [⬇][🗑]│   │
│  └─────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

## Implementation Steps

### Phase 1: Core Functionality ✅ COMPLETED
1. Create `DatabaseBackupController` ✅
2. Create `DatabaseBackupService` ✅
3. Implement export functionality (JSON format) ✅
4. Implement import functionality ✅
5. Create basic UI view ✅

### Phase 2: Security & Validation ✅ COMPLETED
6. Add admin-only middleware check ✅
7. Implement file validation ✅
8. Add transaction-based restore ✅
9. Add confirmation dialogs ✅
10. Auto-backup before restore ✅

### Phase 3: Enhancements
11. Add backup history management ✅
12. Add compression support ✅
13. Add scheduled backup option (future)

## Sidebar Menu Integration

Add to admin sidebar under "Settings" or "System" section:
```php
// Only visible to admin users
@if(auth()->user()->role === 'admin')
<li>
    <a href="{{ route('admin.database-backup.index') }}">
        <i class="fas fa-database"></i>
        <span>Database Backup</span>
    </a>
</li>
@endif
```

## Estimated Timeline
- Phase 1: 2-3 hours
- Phase 2: 1-2 hours
- Phase 3: 1-2 hours

## Dependencies
- No external packages required
- Uses Laravel's built-in DB facade
- Uses PHP's ZipArchive for compression (optional)
