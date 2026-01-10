<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class DatabaseBackupService
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Get all tables in the database (public method for UI)
     */
    public function getAllTables(): array
    {
        return $this->getTables();
    }

    /**
     * Export database to JSON format
     */
    public function exportDatabase(bool $compress = false): array
    {
        $tables = $this->getTables();
        $data = [
            'metadata' => [
                'created_at' => now()->toIso8601String(),
                'database' => config('database.connections.mysql.database'),
                'tables_count' => count($tables),
            ],
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $records = DB::table($table)->get()->toArray();
            $data['tables'][$table] = [
                'count' => count($records),
                'data' => $records,
            ];
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "backup_{$timestamp}.json";
        $filepath = $this->backupPath . '/' . $filename;

        File::put($filepath, json_encode($data, JSON_PRETTY_PRINT));

        if ($compress) {
            $zipFilename = "backup_{$timestamp}.zip";
            $zipPath = $this->backupPath . '/' . $zipFilename;
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($filepath, $filename);
                $zip->close();
                File::delete($filepath);
                
                return [
                    'success' => true,
                    'filename' => $zipFilename,
                    'path' => $zipPath,
                    'size' => File::size($zipPath),
                ];
            }
        }

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $filepath,
            'size' => File::size($filepath),
        ];
    }

    /**
     * Import database from backup file (MySQL)
     */
    public function importDatabase(string $filepath, bool $createBackupFirst = true): array
    {
        Log::info('Starting database import', ['filepath' => $filepath, 'createBackupFirst' => $createBackupFirst]);
        
        // Create automatic backup before restore
        if ($createBackupFirst) {
            try {
                Log::info('Creating pre-restore backup');
                $preRestoreBackup = $this->exportDatabase(false);
                if ($preRestoreBackup['success']) {
                    $oldPath = $preRestoreBackup['path'];
                    $newFilename = str_replace('backup_', 'pre_restore_backup_', $preRestoreBackup['filename']);
                    $newPath = $this->backupPath . '/' . $newFilename;
                    File::move($oldPath, $newPath);
                    Log::info('Pre-restore backup created', ['path' => $newPath]);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to create pre-restore backup: " . $e->getMessage());
            }
        }

        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        $extractPath = null;
        
        Log::info('Processing file', ['extension' => $extension]);
        
        // Handle zip files
        if ($extension === 'zip') {
            $extractPath = $this->backupPath . '/temp_' . time();
            Log::info('Extracting zip file', ['extractPath' => $extractPath]);
            
            $zip = new ZipArchive();
            
            if ($zip->open($filepath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
                
                $jsonFiles = glob($extractPath . '/*.json');
                Log::info('Found JSON files in zip', ['count' => count($jsonFiles), 'files' => $jsonFiles]);
                
                if (empty($jsonFiles)) {
                    File::deleteDirectory($extractPath);
                    return ['success' => false, 'message' => 'No JSON backup file found in archive.'];
                }
                
                $filepath = $jsonFiles[0];
            } else {
                Log::error('Failed to open zip archive');
                return ['success' => false, 'message' => 'Failed to open zip archive.'];
            }
        }

        Log::info('Reading JSON file', ['filepath' => $filepath]);
        $content = File::get($filepath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error', ['error' => json_last_error_msg()]);
            $this->cleanupTempDir($extractPath);
            return ['success' => false, 'message' => 'Invalid JSON format in backup file: ' . json_last_error_msg()];
        }

        if (!isset($data['tables']) || !is_array($data['tables'])) {
            Log::error('Invalid backup structure', ['keys' => array_keys($data)]);
            $this->cleanupTempDir($extractPath);
            return ['success' => false, 'message' => 'Invalid backup file structure.'];
        }

        Log::info('Backup file parsed', [
            'tables_count' => count($data['tables']),
            'tables' => array_keys($data['tables']),
        ]);

        // Tables to skip during restore:
        // - migrations: handled by Laravel
        // - sessions: can cause auth issues when using database sessions
        // - cache: can cause issues when using database cache
        // - jobs/failed_jobs: queue tables that shouldn't be restored
        $skipTables = ['migrations', 'sessions', 'cache', 'cache_locks'];
        $tablesRestored = 0;

        try {
            // Disable foreign key checks for MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Log::info('Foreign key checks disabled');

            foreach ($data['tables'] as $table => $tableData) {
                // Skip if table doesn't exist or is in skip list
                if (!Schema::hasTable($table)) {
                    Log::warning("Table does not exist, skipping", ['table' => $table]);
                    continue;
                }
                
                if (in_array($table, $skipTables)) {
                    Log::info("Skipping table", ['table' => $table]);
                    continue;
                }

                Log::info("Restoring table", ['table' => $table, 'records' => $tableData['count'] ?? count($tableData['data'] ?? [])]);

                try {
                    // Use DELETE instead of TRUNCATE to avoid implicit commit
                    DB::table($table)->delete();

                    // Insert new data in chunks
                    if (!empty($tableData['data'])) {
                        foreach (array_chunk($tableData['data'], 100) as $chunkIndex => $chunk) {
                            $insertData = array_map(function ($row) {
                                return (array) $row;
                            }, $chunk);
                            
                            DB::table($table)->insert($insertData);
                        }
                    }
                    
                    $tablesRestored++;
                } catch (\Exception $e) {
                    Log::warning("Failed to restore table {$table}", [
                        'error' => $e->getMessage()
                    ]);
                    // Continue with other tables even if one fails
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            
            Log::info('Database restore completed', ['tables_restored' => $tablesRestored]);

            $this->cleanupTempDir($extractPath);

            return [
                'success' => true,
                'message' => 'Database restored successfully.',
                'tables_restored' => $tablesRestored,
            ];
        } catch (\Exception $e) {
            // Make sure foreign key checks are re-enabled even on failure
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Exception $fkException) {
                Log::warning('Failed to re-enable foreign key checks', ['error' => $fkException->getMessage()]);
            }
            
            Log::error('Database restore failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            $this->cleanupTempDir($extractPath);

            return ['success' => false, 'message' => 'Import failed: ' . $e->getMessage()];
        }
    }

    /**
     * Cleanup temporary directory
     */
    protected function cleanupTempDir(?string $path): void
    {
        if ($path && File::exists($path)) {
            File::deleteDirectory($path);
        }
    }

    /**
     * Validate backup file
     */
    public function validateBackupFile(string $filepath): array
    {
        Log::info('Validating backup file', ['filepath' => $filepath]);
        
        if (!File::exists($filepath)) {
            Log::error('Backup file not found', ['filepath' => $filepath]);
            return ['valid' => false, 'message' => 'File not found at: ' . $filepath];
        }

        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        Log::info('File extension', ['extension' => $extension]);
        
        if (!in_array($extension, ['json', 'zip'])) {
            return ['valid' => false, 'message' => 'Invalid file format. Only JSON and ZIP files are allowed.'];
        }

        $fileSize = File::size($filepath);
        $maxSize = 100 * 1024 * 1024; // 100MB
        Log::info('File size check', ['size' => $fileSize, 'max' => $maxSize]);
        
        if ($fileSize > $maxSize) {
            return ['valid' => false, 'message' => 'File size exceeds maximum limit of 100MB.'];
        }

        return ['valid' => true, 'message' => 'File is valid.'];
    }

    /**
     * Get list of stored backups
     */
    public function getBackupHistory(): array
    {
        if (!File::exists($this->backupPath)) {
            return [];
        }
        
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $extension = $file->getExtension();
            if (in_array($extension, ['json', 'zip'])) {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return $backups;
    }

    /**
     * Delete a backup file
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
     * Get all tables in the MySQL database
     */
    protected function getTables(): array
    {
        $database = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . $database;
        
        return array_map(fn($t) => $t->$key, $tables);
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
     * Export selected tables to JSON format (Selective Backup)
     */
    public function exportSelectiveTables(array $selectedTables, bool $compress = false): array
    {
        if (empty($selectedTables)) {
            return ['success' => false, 'message' => 'No tables selected for backup.'];
        }

        $allTables = $this->getTables();
        $validTables = array_intersect($selectedTables, $allTables);

        if (empty($validTables)) {
            return ['success' => false, 'message' => 'No valid tables found for backup.'];
        }

        $data = [
            'metadata' => [
                'created_at' => now()->toIso8601String(),
                'database' => config('database.connections.mysql.database'),
                'backup_type' => 'selective',
                'tables_count' => count($validTables),
                'selected_tables' => array_values($validTables),
            ],
            'tables' => [],
        ];

        foreach ($validTables as $table) {
            $records = DB::table($table)->get()->toArray();
            $data['tables'][$table] = [
                'count' => count($records),
                'data' => $records,
            ];
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "selective_backup_{$timestamp}.json";
        $filepath = $this->backupPath . '/' . $filename;

        File::put($filepath, json_encode($data, JSON_PRETTY_PRINT));

        if ($compress) {
            $zipFilename = "selective_backup_{$timestamp}.zip";
            $zipPath = $this->backupPath . '/' . $zipFilename;
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($filepath, $filename);
                $zip->close();
                File::delete($filepath);
                
                return [
                    'success' => true,
                    'filename' => $zipFilename,
                    'path' => $zipPath,
                    'size' => File::size($zipPath),
                    'tables_exported' => count($validTables),
                ];
            }
        }

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $filepath,
            'size' => File::size($filepath),
            'tables_exported' => count($validTables),
        ];
    }

    /**
     * Get table row counts for display
     */
    public function getTableStats(): array
    {
        $tables = $this->getTables();
        $stats = [];

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $stats[$table] = $count;
        }

        return $stats;
    }
}
