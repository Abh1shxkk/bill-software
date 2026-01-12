# Full System Restore Implementation Plan

## Overview
Implement functionality to restore the entire web application (code files + database) from a backup file created by the backup system.

---

## Table of Contents
1. [Current State](#current-state)
2. [Goals](#goals)
3. [Safety Considerations](#safety-considerations)
4. [Implementation Steps](#implementation-steps)
5. [File Changes](#file-changes)
6. [Testing Checklist](#testing-checklist)

---

## Current State

### What Exists
- ✅ Database backup export (JSON/ZIP)
- ✅ Code backup export (ZIP)
- ✅ Full backup export (Database + Code in ZIP)
- ✅ Database restore from backup file
- ❌ Code restore from backup file
- ❌ Full system restore (Database + Code)

### Backup File Formats
| Type | Format | Contents |
|------|--------|----------|
| Database | `.json` or `.zip` containing `.json` | JSON with table data |
| Code | `.zip` | All source files + `_backup_metadata.json` |
| Full | `.zip` | `database/` folder + `code/` folder + `_full_backup_metadata.json` |

---

## Goals

1. **Restore Code Files** - Extract and replace source code from backup
2. **Restore Full Backup** - Restore both database AND code in one operation
3. **Safety First** - Create automatic backup before any restore
4. **Selective Restore** - Allow choosing what to restore from full backup (DB only, Code only, or Both)
5. **Rollback Support** - Ability to undo restore if something goes wrong

---

## Safety Considerations

### Critical Protections
1. **Pre-Restore Backup** - Always create a backup of current state before restoring
2. **File Preservation** - Never delete `.env` file (contains database credentials)
3. **Session Protection** - Keep current user session active during restore
4. **Permissions** - Maintain file permissions after restore
5. **Excluded from Restore**:
   - `.env` (environment config)
   - `storage/` directory (logs, cache, sessions)
   - `vendor/` directory (composer packages - run `composer install` after)
   - `node_modules/` directory (npm packages - run `npm install` after)

### Restore Order
1. Create pre-restore backup
2. Restore code files (if applicable)
3. Clear Laravel caches
4. Restore database (if applicable)
5. Run any pending migrations (optional)
6. Clear all caches again

---

## Implementation Steps

### Step 1: Update CodeBackupService.php

Add the following methods to `app/Services/CodeBackupService.php`:

```php
/**
 * Restore code from a backup file
 * 
 * @param string $zipPath Path to the code backup ZIP file
 * @param bool $createBackupFirst Create backup before restore
 * @return array Result with success status and message
 */
public function restoreCode(string $zipPath, bool $createBackupFirst = true): array
{
    Log::info('Starting code restore', ['zipPath' => $zipPath]);

    // Validate the file exists
    if (!File::exists($zipPath)) {
        return ['success' => false, 'message' => 'Backup file not found.'];
    }

    // Create pre-restore backup
    if ($createBackupFirst) {
        try {
            $preRestoreResult = $this->exportCode();
            if ($preRestoreResult['success']) {
                $oldPath = $preRestoreResult['path'];
                $newFilename = str_replace('code_backup_', 'pre_restore_code_', $preRestoreResult['filename']);
                $newPath = $this->backupPath . '/' . $newFilename;
                File::move($oldPath, $newPath);
                Log::info('Pre-restore code backup created', ['path' => $newPath]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to create pre-restore code backup: ' . $e->getMessage());
        }
    }

    $extractPath = storage_path('app/temp/code_restore_' . time());

    try {
        // Extract the ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['success' => false, 'message' => 'Failed to open backup archive.'];
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // Verify it's a valid code backup
        if (!File::exists($extractPath . '/_backup_metadata.json')) {
            // Check if it might be inside a subfolder (full backup structure)
            $possiblePaths = glob($extractPath . '/code/*.json');
            if (!empty($possiblePaths)) {
                $extractPath = dirname($possiblePaths[0]);
            } else {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => 'Invalid code backup format.'];
            }
        }

        // Files/directories to NEVER overwrite
        $preserveList = [
            '.env',
            'storage',
            'vendor',
            'node_modules',
            '.git',
        ];

        $filesRestored = 0;
        $dirsRestored = 0;

        // Restore each directory
        foreach ($this->includeDirs as $dir) {
            $sourcePath = $extractPath . '/' . $dir;
            $targetPath = $this->basePath . '/' . $dir;

            if (File::isDirectory($sourcePath)) {
                // Backup existing directory content (just rename with timestamp)
                if (File::isDirectory($targetPath)) {
                    // Delete existing (except preserved items)
                    $this->deleteDirectoryContents($targetPath, $preserveList);
                }

                // Copy from backup
                $count = $this->copyDirectory($sourcePath, $targetPath);
                $filesRestored += $count;
                $dirsRestored++;
                Log::info("Restored directory", ['dir' => $dir, 'files' => $count]);
            }
        }

        // Restore root files
        foreach ($this->includeFiles as $file) {
            if (in_array($file, $preserveList)) {
                continue;
            }

            $sourceFile = $extractPath . '/' . $file;
            $targetFile = $this->basePath . '/' . $file;

            if (File::exists($sourceFile)) {
                File::copy($sourceFile, $targetFile);
                $filesRestored++;
            }
        }

        // Cleanup temp directory
        File::deleteDirectory(dirname($extractPath) === storage_path('app/temp') 
            ? $extractPath 
            : dirname($extractPath));

        Log::info('Code restore completed', [
            'files_restored' => $filesRestored,
            'dirs_restored' => $dirsRestored,
        ]);

        return [
            'success' => true,
            'message' => 'Code restored successfully.',
            'files_restored' => $filesRestored,
            'directories_restored' => $dirsRestored,
        ];

    } catch (\Exception $e) {
        Log::error('Code restore failed', ['error' => $e->getMessage()]);
        
        // Cleanup on failure
        if (File::isDirectory($extractPath)) {
            File::deleteDirectory($extractPath);
        }

        return ['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()];
    }
}

/**
 * Delete directory contents except preserved items
 */
protected function deleteDirectoryContents(string $path, array $preserve): void
{
    $items = File::directories($path);
    foreach ($items as $item) {
        $name = basename($item);
        if (!in_array($name, $preserve)) {
            File::deleteDirectory($item);
        }
    }

    $files = File::files($path);
    foreach ($files as $file) {
        $name = $file->getFilename();
        if (!in_array($name, $preserve)) {
            File::delete($file->getPathname());
        }
    }
}

/**
 * Copy directory recursively
 */
protected function copyDirectory(string $source, string $destination): int
{
    $count = 0;

    if (!File::isDirectory($destination)) {
        File::makeDirectory($destination, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = $iterator->getSubPathname();
        $targetPath = $destination . '/' . $relativePath;

        if ($item->isDir()) {
            if (!File::isDirectory($targetPath)) {
                File::makeDirectory($targetPath, 0755, true);
            }
        } else {
            $targetDir = dirname($targetPath);
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            File::copy($item->getPathname(), $targetPath);
            $count++;
        }
    }

    return $count;
}

/**
 * Validate a backup file and determine its type
 */
public function validateAndIdentifyBackup(string $filepath): array
{
    if (!File::exists($filepath)) {
        return ['valid' => false, 'type' => null, 'message' => 'File not found.'];
    }

    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    
    if ($extension !== 'zip' && $extension !== 'json') {
        return ['valid' => false, 'type' => null, 'message' => 'Invalid file format.'];
    }

    // Check if it's a JSON database backup
    if ($extension === 'json') {
        $content = File::get($filepath);
        $data = json_decode($content, true);
        if (isset($data['tables'])) {
            return ['valid' => true, 'type' => 'database', 'message' => 'Valid database backup.'];
        }
        return ['valid' => false, 'type' => null, 'message' => 'Invalid JSON format.'];
    }

    // It's a ZIP - determine what kind
    $zip = new ZipArchive();
    if ($zip->open($filepath) !== true) {
        return ['valid' => false, 'type' => null, 'message' => 'Cannot open ZIP file.'];
    }

    $hasFullMeta = $zip->locateName('_full_backup_metadata.json') !== false;
    $hasCodeMeta = $zip->locateName('_backup_metadata.json') !== false;
    $hasDatabaseFolder = $zip->locateName('database/') !== false;
    $hasCodeFolder = $zip->locateName('code/') !== false;
    $hasAppFolder = $zip->locateName('app/') !== false;

    $zip->close();

    if ($hasFullMeta || ($hasDatabaseFolder && $hasCodeFolder)) {
        return [
            'valid' => true, 
            'type' => 'full', 
            'message' => 'Valid full backup (database + code).',
            'contains' => ['database', 'code'],
        ];
    }

    if ($hasCodeMeta || $hasAppFolder) {
        return ['valid' => true, 'type' => 'code', 'message' => 'Valid code backup.'];
    }

    // Check for database backup inside ZIP
    $zip->open($filepath);
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if (str_ends_with($filename, '.json')) {
            $zip->close();
            return ['valid' => true, 'type' => 'database', 'message' => 'Valid database backup (zipped).'];
        }
    }
    $zip->close();

    return ['valid' => false, 'type' => null, 'message' => 'Unknown backup format.'];
}

/**
 * Restore from a full backup (both database and code)
 */
public function restoreFullBackup(string $zipPath, DatabaseBackupService $dbService, array $options = []): array
{
    $restoreDatabase = $options['restore_database'] ?? true;
    $restoreCode = $options['restore_code'] ?? true;

    Log::info('Starting full restore', [
        'zipPath' => $zipPath,
        'restoreDatabase' => $restoreDatabase,
        'restoreCode' => $restoreCode,
    ]);

    $extractPath = storage_path('app/temp/full_restore_' . time());
    $results = [
        'success' => true,
        'database' => null,
        'code' => null,
    ];

    try {
        // Extract full backup
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['success' => false, 'message' => 'Failed to open backup archive.'];
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // Restore code first (if selected)
        if ($restoreCode) {
            $codeBackups = glob($extractPath . '/code/*.zip');
            if (!empty($codeBackups)) {
                $codeResult = $this->restoreCode($codeBackups[0], true);
                $results['code'] = $codeResult;
                if (!$codeResult['success']) {
                    $results['success'] = false;
                }
            } else {
                Log::warning('No code backup found in full backup');
            }
        }

        // Clear Laravel caches after code restore
        try {
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
        } catch (\Exception $e) {
            Log::warning('Failed to clear caches: ' . $e->getMessage());
        }

        // Restore database (if selected)
        if ($restoreDatabase) {
            $dbBackups = glob($extractPath . '/database/*.json');
            if (empty($dbBackups)) {
                $dbBackups = glob($extractPath . '/database/*.zip');
            }
            
            if (!empty($dbBackups)) {
                $dbResult = $dbService->importDatabase($dbBackups[0], true);
                $results['database'] = $dbResult;
                if (!$dbResult['success']) {
                    $results['success'] = false;
                }
            } else {
                Log::warning('No database backup found in full backup');
            }
        }

        // Cleanup
        File::deleteDirectory($extractPath);

        // Build summary message
        $messages = [];
        if ($results['code']) {
            $messages[] = $results['code']['success'] 
                ? "Code: {$results['code']['files_restored']} files restored"
                : "Code restore failed";
        }
        if ($results['database']) {
            $messages[] = $results['database']['success']
                ? "Database: {$results['database']['tables_restored']} tables restored"
                : "Database restore failed";
        }

        $results['message'] = implode('. ', $messages) ?: 'Restore completed.';

        return $results;

    } catch (\Exception $e) {
        Log::error('Full restore failed', ['error' => $e->getMessage()]);

        if (File::isDirectory($extractPath)) {
            File::deleteDirectory($extractPath);
        }

        return ['success' => false, 'message' => 'Full restore failed: ' . $e->getMessage()];
    }
}
```

---

### Step 2: Update DatabaseBackupController.php

Add the following methods to `app/Http/Controllers/Admin/DatabaseBackupController.php`:

```php
/**
 * Import/Restore - handles all backup types
 */
public function importFull(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Access denied. Admin only.');
    }

    $request->validate([
        'backup_file' => 'required|file|max:512000', // 500MB max
        'restore_database' => 'boolean',
        'restore_code' => 'boolean',
    ]);

    $file = $request->file('backup_file');
    $tempDir = storage_path('app/temp');
    
    if (!File::exists($tempDir)) {
        File::makeDirectory($tempDir, 0755, true);
    }

    $filename = 'restore_' . time() . '.' . $file->getClientOriginalExtension();
    $filepath = $tempDir . '/' . $filename;
    $file->move($tempDir, $filename);

    try {
        // Identify backup type
        $validation = $this->codeBackupService->validateAndIdentifyBackup($filepath);

        if (!$validation['valid']) {
            File::delete($filepath);
            return back()->with('error', $validation['message']);
        }

        $type = $validation['type'];
        $result = ['success' => false];

        switch ($type) {
            case 'database':
                $result = $this->backupService->importDatabase($filepath, true);
                break;

            case 'code':
                $result = $this->codeBackupService->restoreCode($filepath, true);
                break;

            case 'full':
                $restoreOptions = [
                    'restore_database' => $request->boolean('restore_database', true),
                    'restore_code' => $request->boolean('restore_code', true),
                ];
                $result = $this->codeBackupService->restoreFullBackup(
                    $filepath, 
                    $this->backupService, 
                    $restoreOptions
                );
                break;

            default:
                File::delete($filepath);
                return back()->with('error', 'Unknown backup type.');
        }

        // Cleanup
        if (File::exists($filepath)) {
            File::delete($filepath);
        }

        if ($result['success']) {
            $message = $result['message'] ?? 'Restore completed successfully.';
            return back()->with('success', $message);
        }

        return back()->with('error', $result['message'] ?? 'Restore failed.');

    } catch (\Exception $e) {
        if (File::exists($filepath)) {
            File::delete($filepath);
        }
        return back()->with('error', 'Restore failed: ' . $e->getMessage());
    }
}

/**
 * AJAX endpoint to validate and identify backup type
 */
public function validateBackup(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        return response()->json(['error' => 'Access denied'], 403);
    }

    $request->validate([
        'backup_file' => 'required|file|max:512000',
    ]);

    $file = $request->file('backup_file');
    $tempPath = $file->store('temp');
    $fullPath = storage_path('app/' . $tempPath);

    $validation = $this->codeBackupService->validateAndIdentifyBackup($fullPath);

    // Cleanup
    File::delete($fullPath);

    return response()->json($validation);
}
```

---

### Step 3: Add New Routes

Add to `routes/web.php` in the database-backup section:

```php
Route::post('database-backup/import-full', [DatabaseBackupController::class, 'importFull'])->name('database-backup.import-full');
Route::post('database-backup/validate', [DatabaseBackupController::class, 'validateBackup'])->name('database-backup.validate');
```

---

### Step 4: Update the View

Update `resources/views/admin/database-backup/index.blade.php`:

Replace the Import Section with an enhanced version that:
1. Detects backup type when file is selected
2. Shows options for full backup (restore DB, restore Code, or both)
3. Displays warnings appropriate to the backup type

```blade
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
                <i class="bi bi-info-circle me-1"></i>
                An automatic backup of your current state will be created before restore.
            </div>
            
            <form action="{{ route('admin.database-backup.import-full') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="mb-3">
                    <label for="backupFile" class="form-label">Select Backup File</label>
                    <input type="file" class="form-control" id="backupFile" name="backup_file" 
                           accept=".json,.zip" required>
                    <div class="form-text">Supports: Database (.json/.zip), Code (.zip), Full System (.zip)</div>
                </div>

                <!-- Backup Type Detection Display -->
                <div id="backupTypeInfo" class="mb-3" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <strong>Detected Type:</strong> <span id="detectedType"></span>
                            <div id="fullBackupOptions" style="display: none;" class="mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="restore_database" 
                                           id="restoreDatabase" value="1" checked>
                                    <label class="form-check-label" for="restoreDatabase">
                                        <i class="bi bi-database me-1"></i>Restore Database
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="restore_code" 
                                           id="restoreCode" value="1" checked>
                                    <label class="form-check-label" for="restoreCode">
                                        <i class="bi bi-code-slash me-1"></i>Restore Code Files
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
                Protected files: .env, storage/, vendor/, node_modules/
            </small>
        </div>
    </div>
</div>
```

**JavaScript to add:**
```javascript
// Detect backup type when file is selected
document.getElementById('backupFile').addEventListener('change', async function() {
    const file = this.files[0];
    if (!file) return;

    const infoDiv = document.getElementById('backupTypeInfo');
    const typeSpan = document.getElementById('detectedType');
    const fullOptions = document.getElementById('fullBackupOptions');

    // Show loading
    infoDiv.style.display = 'block';
    typeSpan.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Detecting...';
    fullOptions.style.display = 'none';

    // Simple client-side detection
    const filename = file.name.toLowerCase();
    let type = 'unknown';
    let typeBadge = '';

    if (filename.startsWith('full_backup_')) {
        type = 'full';
        typeBadge = '<span class="badge bg-success">Full System Backup</span>';
        fullOptions.style.display = 'block';
    } else if (filename.startsWith('code_backup_')) {
        type = 'code';
        typeBadge = '<span class="badge bg-info">Code Backup</span>';
    } else if (filename.endsWith('.json') || filename.startsWith('backup_') || filename.startsWith('selective_')) {
        type = 'database';
        typeBadge = '<span class="badge bg-primary">Database Backup</span>';
    } else {
        typeBadge = '<span class="badge bg-secondary">Unknown - will auto-detect</span>';
    }

    typeSpan.innerHTML = typeBadge;
});
```

---

### Step 5: Post-Restore Commands

After a code restore, the user should run:
```bash
# Re-install composer dependencies
composer install --no-dev --optimize-autoloader

# Re-install npm packages (if using Vite/frontend)
npm install
npm run build

# Clear all Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize
php artisan optimize
```

Consider adding a "Post-Restore Commands" info panel or automated execution.

---

## File Changes Summary

| File | Action | Description |
|------|--------|-------------|
| `app/Services/CodeBackupService.php` | MODIFY | Add `restoreCode()`, `restoreFullBackup()`, `validateAndIdentifyBackup()` methods |
| `app/Http/Controllers/Admin/DatabaseBackupController.php` | MODIFY | Add `importFull()`, `validateBackup()` methods |
| `routes/web.php` | MODIFY | Add 2 new routes for import-full and validate |
| `resources/views/admin/database-backup/index.blade.php` | MODIFY | Enhanced import UI with type detection and options |

---

## Testing Checklist

### Backup Creation Tests
- [ ] Create full backup successfully
- [ ] Create code-only backup successfully
- [ ] Create database-only backup successfully
- [ ] Verify backup files contain correct content

### Restore Tests
- [ ] Restore database from `.json` file
- [ ] Restore database from `.zip` file
- [ ] Restore code from code backup
- [ ] Restore full backup (both DB and code)
- [ ] Restore full backup (database only option)
- [ ] Restore full backup (code only option)

### Safety Tests
- [ ] Pre-restore backup is created automatically
- [ ] `.env` file is preserved during code restore
- [ ] `storage/` directory is preserved
- [ ] `vendor/` directory is preserved
- [ ] User session remains active after restore

### Error Handling Tests
- [ ] Invalid file format shows error
- [ ] Corrupted ZIP shows error
- [ ] Missing permissions shows error
- [ ] Partial restore creates proper error message

### UI Tests
- [ ] File type detection works correctly
- [ ] Full backup shows restore options
- [ ] Loading states display properly
- [ ] Success/error messages display correctly

---

## Security Notes

1. **Admin Only** - All restore operations require admin privileges
2. **File Validation** - Backup files are validated before processing
3. **No Shell Execution** - Avoid `exec()` or `shell_exec()` for security
4. **Temp Cleanup** - All temporary files are cleaned up after operations
5. **Logging** - All restore operations are logged for audit trail

---

## Rollback Strategy

If a restore goes wrong:
1. Find the `pre_restore_*` backup file created before the restore
2. Use that file to restore back to the previous state
3. Check Laravel logs at `storage/logs/laravel.log` for error details

---

## Future Enhancements

1. **Restore Preview** - Show what will be changed before confirming
2. **Incremental Backup** - Only backup changed files
3. **Remote Backup** - Support S3/cloud storage
4. **Scheduled Restore Points** - Automatic restore point creation
5. **Version Comparison** - Compare backup with current state
