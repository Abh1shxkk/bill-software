<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class CodeBackupService
{
    protected string $backupPath;
    protected string $basePath;

    /**
     * Directories to include in backup
     */
    protected array $includeDirs = [
        'app',
        'config',
        'database',
        'resources',
        'routes',
        'public',
        'bootstrap',
        'docs',
    ];

    /**
     * Individual files to include in backup
     */
    protected array $includeFiles = [
        '.env.example',
        'composer.json',
        'composer.lock',
        'package.json',
        'package-lock.json',
        'artisan',
        'vite.config.js',
        '.htaccess',
        'phpunit.xml',
        '.editorconfig',
        '.gitattributes',
        '.gitignore',
    ];

    /**
     * Directories/patterns to exclude
     */
    protected array $excludePatterns = [
        'vendor',
        'node_modules',
        'storage/app/backups',
        'storage/logs',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'public/storage',
        '.git',
        '.idea',
        '.vscode',
    ];

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->basePath = base_path();
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Export code/files backup
     */
    public function exportCode(array $options = []): array
    {
        $timestamp = now()->format('Y-m-d_His');
        $zipFilename = "code_backup_{$timestamp}.zip";
        $zipPath = $this->backupPath . '/' . $zipFilename;

        Log::info('Starting code backup', [
            'timestamp' => $timestamp,
            'target' => $zipPath,
            'options' => $options,
        ]);

        try {
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                Log::error('Failed to create zip file');
                return ['success' => false, 'message' => 'Failed to create backup archive.'];
            }

            $filesAdded = 0;
            $dirsProcessed = 0;

            // Determine which directories to backup
            $dirsToBackup = $options['directories'] ?? $this->includeDirs;
            
            // Add directories
            foreach ($dirsToBackup as $dir) {
                $fullPath = $this->basePath . '/' . $dir;
                if (File::isDirectory($fullPath)) {
                    $count = $this->addDirectoryToZip($zip, $fullPath, $dir);
                    $filesAdded += $count;
                    $dirsProcessed++;
                    Log::info("Added directory to backup", ['dir' => $dir, 'files' => $count]);
                }
            }

            // Add individual files if full backup
            if (!isset($options['directories']) || in_array('root_files', $options['directories'])) {
                foreach ($this->includeFiles as $file) {
                    $fullPath = $this->basePath . '/' . $file;
                    if (File::exists($fullPath)) {
                        $zip->addFile($fullPath, $file);
                        $filesAdded++;
                    }
                }
            }

            // Add backup metadata
            $metadata = [
                'created_at' => now()->toIso8601String(),
                'type' => 'code_backup',
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'directories_backed_up' => $dirsToBackup,
                'total_files' => $filesAdded,
                'total_directories' => $dirsProcessed,
            ];
            
            $zip->addFromString('_backup_metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));
            
            $zip->close();

            $size = File::size($zipPath);
            
            Log::info('Code backup completed', [
                'filename' => $zipFilename,
                'size' => $size,
                'files_added' => $filesAdded,
            ]);

            return [
                'success' => true,
                'filename' => $zipFilename,
                'path' => $zipPath,
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'files_count' => $filesAdded,
                'directories_count' => $dirsProcessed,
            ];

        } catch (\Exception $e) {
            Log::error('Code backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up partial file
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }

            return ['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Add a directory recursively to zip archive
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $sourcePath, string $zipPath): int
    {
        $filesAdded = 0;
        
        if (!File::isDirectory($sourcePath)) {
            return 0;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $zipPath . '/' . $iterator->getSubPathname();
            
            // Convert Windows path separators
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Check if path should be excluded
            if ($this->shouldExclude($relativePath)) {
                continue;
            }

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                // Skip very large files (> 50MB)
                if ($file->getSize() > 50 * 1024 * 1024) {
                    Log::warning('Skipping large file', ['file' => $relativePath, 'size' => $file->getSize()]);
                    continue;
                }
                
                $zip->addFile($filePath, $relativePath);
                $filesAdded++;
            }
        }

        return $filesAdded;
    }

    /**
     * Check if path should be excluded
     */
    protected function shouldExclude(string $path): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get information about what will be backed up
     */
    public function getBackupInfo(): array
    {
        $info = [
            'directories' => [],
            'root_files' => [],
            'total_estimated_size' => 0,
        ];

        foreach ($this->includeDirs as $dir) {
            $fullPath = $this->basePath . '/' . $dir;
            if (File::isDirectory($fullPath)) {
                $size = $this->getDirectorySize($fullPath);
                $info['directories'][$dir] = [
                    'exists' => true,
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                ];
                $info['total_estimated_size'] += $size;
            } else {
                $info['directories'][$dir] = [
                    'exists' => false,
                    'size' => 0,
                    'size_formatted' => '0 B',
                ];
            }
        }

        foreach ($this->includeFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (File::exists($fullPath)) {
                $size = File::size($fullPath);
                $info['root_files'][$file] = [
                    'exists' => true,
                    'size' => $size,
                ];
                $info['total_estimated_size'] += $size;
            } else {
                $info['root_files'][$file] = [
                    'exists' => false,
                    'size' => 0,
                ];
            }
        }

        $info['total_estimated_size_formatted'] = $this->formatBytes($info['total_estimated_size']);

        return $info;
    }

    /**
     * Get directory size (excluding excluded patterns)
     */
    protected function getDirectorySize(string $path): int
    {
        $size = 0;
        
        if (!File::isDirectory($path)) {
            return 0;
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $relativePath = str_replace($this->basePath . '/', '', $file->getRealPath());
                
                if ($this->shouldExclude($relativePath)) {
                    continue;
                }

                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error calculating directory size', ['path' => $path, 'error' => $e->getMessage()]);
        }

        return $size;
    }

    /**
     * Get list of code backups
     */
    public function getCodeBackupHistory(): array
    {
        if (!File::exists($this->backupPath)) {
            return [];
        }
        
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            // Only include code backups
            if (str_starts_with($filename, 'code_backup_') && $file->getExtension() === 'zip') {
                $backups[] = [
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'type' => 'code',
                ];
            }
        }

        usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return $backups;
    }

    /**
     * Export full backup (Database + Code)
     */
    public function exportFullBackup(DatabaseBackupService $dbService): array
    {
        $timestamp = now()->format('Y-m-d_His');
        $fullZipFilename = "full_backup_{$timestamp}.zip";
        $fullZipPath = $this->backupPath . '/' . $fullZipFilename;

        Log::info('Starting full backup (code + database)', ['timestamp' => $timestamp]);

        try {
            // First, export database
            $dbResult = $dbService->exportDatabase(false);
            if (!$dbResult['success']) {
                return ['success' => false, 'message' => 'Failed to create database backup.'];
            }

            // Export code
            $codeResult = $this->exportCode();
            if (!$codeResult['success']) {
                // Clean up database backup
                if (isset($dbResult['path']) && File::exists($dbResult['path'])) {
                    File::delete($dbResult['path']);
                }
                return ['success' => false, 'message' => 'Failed to create code backup.'];
            }

            // Create a combined zip
            $zip = new ZipArchive();
            if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return ['success' => false, 'message' => 'Failed to create combined backup archive.'];
            }

            // Add database backup
            $zip->addFile($dbResult['path'], 'database/' . $dbResult['filename']);
            
            // Add code backup
            $zip->addFile($codeResult['path'], 'code/' . $codeResult['filename']);

            // Add metadata
            $metadata = [
                'created_at' => now()->toIso8601String(),
                'type' => 'full_backup',
                'includes' => ['database', 'code'],
                'database_backup' => $dbResult['filename'],
                'code_backup' => $codeResult['filename'],
            ];
            $zip->addFromString('_full_backup_metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

            $zip->close();

            // Clean up individual backups
            File::delete($dbResult['path']);
            File::delete($codeResult['path']);

            $size = File::size($fullZipPath);

            Log::info('Full backup completed', [
                'filename' => $fullZipFilename,
                'size' => $size,
            ]);

            return [
                'success' => true,
                'filename' => $fullZipFilename,
                'path' => $fullZipPath,
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'type' => 'full',
            ];

        } catch (\Exception $e) {
            Log::error('Full backup failed', ['error' => $e->getMessage()]);
            
            if (File::exists($fullZipPath)) {
                File::delete($fullZipPath);
            }

            return ['success' => false, 'message' => 'Full backup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the list of directories that can be backed up
     */
    public function getAvailableDirectories(): array
    {
        return $this->includeDirs;
    }

    /**
     * Delete a code backup file
     */
    public function deleteBackup(string $filename): bool
    {
        $filepath = $this->backupPath . '/' . basename($filename);
        
        if (File::exists($filepath)) {
            return File::delete($filepath);
        }

        return false;
    }

    /**
     * Get backup file path
     */
    public function getBackupPath(string $filename): ?string
    {
        $filepath = $this->backupPath . '/' . basename($filename);
        
        return File::exists($filepath) ? $filepath : null;
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
            return ['valid' => false, 'type' => null, 'message' => 'Invalid file format. Only ZIP and JSON files are allowed.'];
        }

        // Check if it's a JSON database backup
        if ($extension === 'json') {
            try {
                $content = File::get($filepath);
                $data = json_decode($content, true);
                if (isset($data['tables'])) {
                    return ['valid' => true, 'type' => 'database', 'message' => 'Valid database backup.'];
                }
            } catch (\Exception $e) {
                return ['valid' => false, 'type' => null, 'message' => 'Invalid JSON format.'];
            }
            return ['valid' => false, 'type' => null, 'message' => 'Invalid backup format.'];
        }

        // It's a ZIP - determine what kind
        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            return ['valid' => false, 'type' => null, 'message' => 'Cannot open ZIP file.'];
        }

        $hasFullMeta = $zip->locateName('_full_backup_metadata.json') !== false;
        $hasCodeMeta = $zip->locateName('_backup_metadata.json') !== false;
        $hasDatabaseFolder = false;
        $hasCodeFolder = false;
        $hasAppFolder = $zip->locateName('app/') !== false;

        // Check for database and code folders
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (strpos($name, 'database/') === 0) {
                $hasDatabaseFolder = true;
            }
            if (strpos($name, 'code/') === 0) {
                $hasCodeFolder = true;
            }
        }

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
            if (str_ends_with($filename, '.json') && !str_starts_with($filename, '_')) {
                $zip->close();
                return ['valid' => true, 'type' => 'database', 'message' => 'Valid database backup (zipped).'];
            }
        }
        $zip->close();

        return ['valid' => false, 'type' => null, 'message' => 'Unknown backup format.'];
    }

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
        
        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        try {
            // Extract the ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                return ['success' => false, 'message' => 'Failed to open backup archive.'];
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Verify it's a valid code backup (has app folder or metadata)
            $isValidCodeBackup = File::exists($extractPath . '/_backup_metadata.json') || 
                                  File::isDirectory($extractPath . '/app');

            if (!$isValidCodeBackup) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => 'Invalid code backup format. Missing app folder or metadata.'];
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
                    // Delete existing directory content (except preserved items)
                    if (File::isDirectory($targetPath)) {
                        $this->deleteDirectoryContents($targetPath, $preserveList);
                    } else {
                        File::makeDirectory($targetPath, 0755, true);
                    }

                    // Copy from backup
                    $count = $this->copyDirectory($sourcePath, $targetPath);
                    $filesRestored += $count;
                    $dirsRestored++;
                    Log::info("Restored directory", ['dir' => $dir, 'files' => $count]);
                }
            }

            // Restore root files (except preserved ones)
            foreach ($this->includeFiles as $file) {
                if (in_array($file, $preserveList) || $file === '.env') {
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
            File::deleteDirectory($extractPath);

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
            Log::error('Code restore failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
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
        if (!File::isDirectory($path)) {
            return;
        }

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

        if (!File::isDirectory($source)) {
            return 0;
        }

        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathname();
            // Normalize path separators
            $relativePath = str_replace('\\', '/', $relativePath);
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

        if (!$restoreDatabase && !$restoreCode) {
            return ['success' => false, 'message' => 'Nothing selected to restore.'];
        }

        $extractPath = storage_path('app/temp/full_restore_' . time());
        
        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $results = [
            'success' => true,
            'database' => null,
            'code' => null,
            'message' => '',
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
                    $results['code'] = ['success' => false, 'message' => 'No code backup found in archive.'];
                }
            }

            // Clear Laravel caches after code restore
            try {
                \Artisan::call('config:clear');
                \Artisan::call('cache:clear');
                \Artisan::call('view:clear');
                \Artisan::call('route:clear');
                Log::info('Laravel caches cleared after code restore');
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
                    $results['database'] = ['success' => false, 'message' => 'No database backup found in archive.'];
                }
            }

            // Cleanup
            File::deleteDirectory($extractPath);

            // Build summary message
            $messages = [];
            if ($results['code']) {
                if ($results['code']['success']) {
                    $filesRestored = $results['code']['files_restored'] ?? 0;
                    $messages[] = "Code: {$filesRestored} files restored";
                } else {
                    $messages[] = "Code restore failed: " . ($results['code']['message'] ?? 'Unknown error');
                }
            }
            if ($results['database']) {
                if ($results['database']['success']) {
                    $tablesRestored = $results['database']['tables_restored'] ?? 0;
                    $messages[] = "Database: {$tablesRestored} tables restored";
                } else {
                    $messages[] = "Database restore failed: " . ($results['database']['message'] ?? 'Unknown error');
                }
            }

            $results['message'] = implode('. ', $messages) ?: 'Restore completed.';

            Log::info('Full restore completed', $results);

            return $results;

        } catch (\Exception $e) {
            Log::error('Full restore failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if (File::isDirectory($extractPath)) {
                File::deleteDirectory($extractPath);
            }

            return ['success' => false, 'message' => 'Full restore failed: ' . $e->getMessage()];
        }
    }
}

