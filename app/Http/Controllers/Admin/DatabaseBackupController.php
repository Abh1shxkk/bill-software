<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupSchedule;
use App\Services\DatabaseBackupService;
use App\Services\CodeBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DatabaseBackupController extends Controller
{
    protected DatabaseBackupService $backupService;
    protected CodeBackupService $codeBackupService;

    public function __construct(DatabaseBackupService $backupService, CodeBackupService $codeBackupService)
    {
        $this->backupService = $backupService;
        $this->codeBackupService = $codeBackupService;
    }

    /**
     * Display backup/restore interface
     */
    public function index()
    {
        // Admin only check
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $backups = $this->backupService->getBackupHistory();
        $codeBackups = $this->codeBackupService->getCodeBackupHistory();
        $schedule = BackupSchedule::first();
        $tableStats = $this->backupService->getTableStats();
        $codeBackupInfo = $this->codeBackupService->getBackupInfo();
        
        // Merge and sort all backups by date
        $allBackups = array_merge(
            array_map(fn($b) => array_merge($b, ['type' => $b['type'] ?? 'database']), $backups),
            $codeBackups
        );
        usort($allBackups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        
        return view('admin.database-backup.index', compact('backups', 'codeBackups', 'allBackups', 'schedule', 'tableStats', 'codeBackupInfo'));
    }

    /**
     * Export database backup
     */
    public function export(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $compress = $request->boolean('compress', false);
        
        try {
            $result = $this->backupService->exportDatabase($compress);

            if ($result['success']) {
                return response()->download(
                    $result['path'],
                    $result['filename'],
                    ['Content-Type' => $compress ? 'application/zip' : 'application/json']
                );
            }

            return back()->with('error', 'Failed to create backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Import database from backup
     */
    public function import(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $request->validate([
            'backup_file' => 'required|file|max:102400',
        ]);

        $file = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        
        \Log::info('Database Import Started', [
            'original_name' => $originalName,
            'extension' => $extension,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
        
        // Validate file extension
        if (!in_array($extension, ['json', 'zip'])) {
            \Log::error('Invalid file extension', ['extension' => $extension]);
            return back()->with('error', 'Invalid file format. Only JSON and ZIP files are allowed.');
        }

        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
            \Log::info('Created temp directory', ['path' => $tempDir]);
        }

        // Store with original extension
        $filename = 'temp_backup_' . time() . '.' . $extension;
        $fullPath = $tempDir . '/' . $filename;
        
        // Move file directly instead of using store
        $file->move($tempDir, $filename);
        
        \Log::info('File saved', [
            'path' => $fullPath,
            'exists' => File::exists($fullPath),
            'size' => File::exists($fullPath) ? File::size($fullPath) : 0,
        ]);

        if (!File::exists($fullPath)) {
            \Log::error('File not found after save', ['path' => $fullPath]);
            return back()->with('error', 'Failed to save uploaded file. Path: ' . $fullPath);
        }

        try {
            // Validate the backup file
            $validation = $this->backupService->validateBackupFile($fullPath);
            
            \Log::info('Validation result', $validation);
            
            if (!$validation['valid']) {
                File::delete($fullPath);
                return back()->with('error', $validation['message']);
            }

            // Import the database
            $result = $this->backupService->importDatabase($fullPath);
            
            \Log::info('Import result', $result);
            
            // Cleanup temp file
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            if ($result['success']) {
                return back()->with('success', $result['message'] . ' (' . $result['tables_restored'] . ' tables restored)');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            \Log::error('Import exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Save scheduled backup settings
     */
    public function saveSchedule(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $validated = $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'day_of_week' => 'required_if:frequency,weekly|nullable|integer|min:0|max:6',
            'day_of_month' => 'required_if:frequency,monthly|nullable|integer|min:1|max:28',
            'compress' => 'boolean',
            'is_active' => 'boolean',
            'retention_days' => 'required|integer|min:1|max:365',
        ]);

        $schedule = BackupSchedule::first();
        
        if (!$schedule) {
            $schedule = new BackupSchedule();
            $schedule->created_by = auth()->user()->user_id;
        }

        $schedule->fill($validated);
        $schedule->save();
        
        if ($schedule->is_active) {
            $schedule->calculateNextRun();
        } else {
            $schedule->next_run_at = null;
            $schedule->save();
        }

        return back()->with('success', 'Backup schedule saved successfully.');
    }

    /**
     * Download a stored backup
     */
    public function download(string $filename)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $path = $this->backupService->getBackupPath($filename);

        if (!$path) {
            return back()->with('error', 'Backup file not found.');
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $contentType = $extension === 'zip' ? 'application/zip' : 'application/json';

        return response()->download($path, $filename, ['Content-Type' => $contentType]);
    }

    /**
     * Delete a backup file
     */
    public function destroy(string $filename)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Backup deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete backup.'], 500);
    }

    /**
     * Export selective tables backup
     */
    public function exportSelective(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $request->validate([
            'tables' => 'required|array|min:1',
            'tables.*' => 'string',
            'compress' => 'boolean',
        ]);

        $selectedTables = $request->input('tables');
        $compress = $request->boolean('compress', false);

        try {
            $result = $this->backupService->exportSelectiveTables($selectedTables, $compress);

            if ($result['success']) {
                return response()->download(
                    $result['path'],
                    $result['filename'],
                    ['Content-Type' => $compress ? 'application/zip' : 'application/json']
                );
            }

            return back()->with('error', $result['message'] ?? 'Failed to create selective backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Selective backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Export code/files backup
     */
    public function exportCode(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $directories = $request->input('directories');
        $options = [];
        
        if ($directories && is_array($directories)) {
            $options['directories'] = $directories;
        }

        try {
            $result = $this->codeBackupService->exportCode($options);

            if ($result['success']) {
                return response()->download(
                    $result['path'],
                    $result['filename'],
                    ['Content-Type' => 'application/zip']
                );
            }

            return back()->with('error', $result['message'] ?? 'Failed to create code backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Code backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Export full backup (Database + Code)
     */
    public function exportFull(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        try {
            $result = $this->codeBackupService->exportFullBackup($this->backupService);

            if ($result['success']) {
                return response()->download(
                    $result['path'],
                    $result['filename'],
                    ['Content-Type' => 'application/zip']
                );
            }

            return back()->with('error', $result['message'] ?? 'Failed to create full backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Full backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Import/Restore - handles all backup types (database, code, or full)
     */
    public function importFull(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        $request->validate([
            'backup_file' => 'required|file|max:512000', // 500MB max
            'restore_database' => 'nullable',
            'restore_code' => 'nullable',
        ]);

        $file = $request->file('backup_file');
        $tempDir = storage_path('app/temp');
        
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $originalExtension = strtolower($file->getClientOriginalExtension());
        $filename = 'restore_' . time() . '.' . $originalExtension;
        $filepath = $tempDir . '/' . $filename;
        
        // Move file directly
        $file->move($tempDir, $filename);

        \Log::info('Import file uploaded', [
            'original_name' => $file->getClientOriginalName(),
            'path' => $filepath,
            'exists' => File::exists($filepath),
        ]);

        if (!File::exists($filepath)) {
            return back()->with('error', 'Failed to upload backup file.');
        }

        try {
            // Identify backup type
            $validation = $this->codeBackupService->validateAndIdentifyBackup($filepath);

            \Log::info('Backup validation result', $validation);

            if (!$validation['valid']) {
                File::delete($filepath);
                return back()->with('error', $validation['message']);
            }

            $type = $validation['type'];
            $result = ['success' => false, 'message' => 'Unknown error'];

            switch ($type) {
                case 'database':
                    \Log::info('Restoring database backup');
                    $result = $this->backupService->importDatabase($filepath, true);
                    break;

                case 'code':
                    \Log::info('Restoring code backup');
                    $result = $this->codeBackupService->restoreCode($filepath, true);
                    break;

                case 'full':
                    \Log::info('Restoring full backup');
                    $restoreOptions = [
                        'restore_database' => $request->has('restore_database') ? $request->boolean('restore_database') : true,
                        'restore_code' => $request->has('restore_code') ? $request->boolean('restore_code') : true,
                    ];
                    \Log::info('Restore options', $restoreOptions);
                    $result = $this->codeBackupService->restoreFullBackup(
                        $filepath, 
                        $this->backupService, 
                        $restoreOptions
                    );
                    break;

                default:
                    File::delete($filepath);
                    return back()->with('error', 'Unknown backup type: ' . $type);
            }

            // Cleanup temp file
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            \Log::info('Restore result', $result);

            if ($result['success']) {
                $message = $result['message'] ?? 'Restore completed successfully.';
                
                // Add post-restore instructions for code restore
                if ($type === 'code' || $type === 'full') {
                    $message .= ' Please clear browser cache and refresh the page.';
                }
                
                return back()->with('success', $message);
            }

            return back()->with('error', $result['message'] ?? 'Restore failed.');

        } catch (\Exception $e) {
            \Log::error('Restore exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (File::exists($filepath)) {
                File::delete($filepath);
            }
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}

