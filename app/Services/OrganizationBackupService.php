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
     * This will be dynamically populated with ALL tables that have organization_id
     */
    protected array $organizationTables = [];
    
    /**
     * Tables to exclude from backup (system tables, etc.)
     */
    protected array $excludedTables = [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'personal_access_tokens',
        'failed_jobs',
        'jobs',
        'sessions',
        'cache',
        'cache_locks',
        'auto_backup_logs', // Don't backup the backup logs
    ];
    
    public function __construct()
    {
        $this->backupPath = storage_path('app/backups/organizations');
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
        
        // Dynamically discover all tables with organization_id
        $this->discoverOrganizationTables();
    }
    
    /**
     * Discover all database tables that have organization_id column
     */
    protected function discoverOrganizationTables(): void
    {
        try {
            // Get all tables in the database
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Skip excluded tables
                if (in_array($tableName, $this->excludedTables)) {
                    continue;
                }
                
                // Check if table has organization_id column
                if (Schema::hasColumn($tableName, 'organization_id')) {
                    $this->organizationTables[] = $tableName;
                }
            }
            
            // Sort tables alphabetically for consistency
            sort($this->organizationTables);
            
            Log::debug('Discovered organization tables for backup', [
                'count' => count($this->organizationTables),
                'tables' => $this->organizationTables,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to discover organization tables', [
                'error' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Create organization-specific backup (Database + Code)
     */
    public function createBackup(Organization $organization, User $triggeredBy, bool $includeCode = true): array
    {
        $dayOfWeek = strtolower(now()->format('l'));
        $orgCode = $organization->code ?? $organization->id;
        
        Log::info('Starting auto backup', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'day_of_week' => $dayOfWeek,
            'triggered_by' => $triggeredBy->user_id,
            'include_code' => $includeCode,
        ]);

        // Create or update backup log
        $backupLog = AutoBackupLog::getOrCreateForToday(
            $organization->id, 
            $triggeredBy->user_id
        );

        try {
            // Generate backup filename
            $filename = "org_{$orgCode}_{$dayOfWeek}_full_backup.zip";
            $filepath = $this->backupPath . '/' . $filename;
            
            // Delete old backup file if exists
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            // Create main ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Failed to create ZIP archive');
            }

            // ============ DATABASE BACKUP ============
            $backupData = $this->exportOrganizationData($organization);
            $dbJsonFilename = "database_backup.json";
            $dbJsonContent = json_encode($backupData, JSON_PRETTY_PRINT);
            $zip->addFromString('database/' . $dbJsonFilename, $dbJsonContent);
            
            // ============ CODE BACKUP ============
            $codeFilesCount = 0;
            if ($includeCode) {
                $codeFilesCount = $this->addCodeToZip($zip);
            }

            // ============ METADATA ============
            $metadata = [
                'backup_type' => 'full_auto_backup',
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'organization_code' => $orgCode,
                'day_of_week' => $dayOfWeek,
                'created_at' => now()->toIso8601String(),
                'triggered_by_user_id' => $triggeredBy->user_id,
                'triggered_by_name' => $triggeredBy->full_name ?? $triggeredBy->name,
                'includes' => [
                    'database' => true,
                    'code' => $includeCode,
                ],
                'database_tables_count' => count($backupData['tables']),
                'database_records_count' => $backupData['metadata']['total_records'] ?? 0,
                'code_files_count' => $codeFilesCount,
                'app_version' => config('app.version', '1.0.0'),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
            ];
            $zip->addFromString('_backup_metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

            $zip->close();

            $fileSize = File::size($filepath);

            // Update backup log
            $backupLog->update([
                'backup_filename' => $filename,
                'backup_path' => $filepath,
                'backup_size' => $fileSize,
                'status' => 'success',
                'error_message' => null,
            ]);

            Log::info('Auto backup completed successfully', [
                'organization_id' => $organization->id,
                'filename' => $filename,
                'size' => $fileSize,
                'tables_backed_up' => count($backupData['tables']),
                'code_files_backed_up' => $codeFilesCount,
            ]);

            return [
                'success' => true,
                'message' => 'Full backup created successfully (Database + Code)',
                'filename' => $filename,
                'size' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize),
                'backup_log_id' => $backupLog->id,
                'tables_count' => count($backupData['tables']),
                'code_files_count' => $codeFilesCount,
            ];

        } catch (\Exception $e) {
            Log::error('Auto backup failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
     * Add code files to ZIP archive
     */
    protected function addCodeToZip(ZipArchive $zip): int
    {
        $filesAdded = 0;
        $basePath = base_path();
        
        // Directories to include
        $includeDirs = [
            'app',
            'config',
            'database',
            'resources',
            'routes',
            'public',
            'bootstrap',
        ];
        
        // Patterns to exclude
        $excludePatterns = [
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
        
        // Root files to include
        $includeFiles = [
            '.env.example',
            'composer.json',
            'composer.lock',
            'package.json',
            'artisan',
            '.htaccess',
        ];

        // Add directories
        foreach ($includeDirs as $dir) {
            $fullPath = $basePath . '/' . $dir;
            if (File::isDirectory($fullPath)) {
                $filesAdded += $this->addDirectoryToZip($zip, $fullPath, 'code/' . $dir, $excludePatterns);
            }
        }

        // Add root files
        foreach ($includeFiles as $file) {
            $fullPath = $basePath . '/' . $file;
            if (File::exists($fullPath)) {
                $zip->addFile($fullPath, 'code/' . $file);
                $filesAdded++;
            }
        }

        return $filesAdded;
    }

    /**
     * Add directory recursively to ZIP
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $sourcePath, string $zipPath, array $excludePatterns): int
    {
        $filesAdded = 0;
        
        if (!File::isDirectory($sourcePath)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $zipPath . '/' . $iterator->getSubPathname();
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Check exclusions
            $shouldExclude = false;
            foreach ($excludePatterns as $pattern) {
                if (strpos($relativePath, $pattern) !== false) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                // Skip large files (> 50MB)
                if ($file->getSize() > 50 * 1024 * 1024) {
                    continue;
                }
                $zip->addFile($filePath, $relativePath);
                $filesAdded++;
            }
        }

        return $filesAdded;
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
                'app_version' => config('app.version', '1.0.0'),
            ],
            'tables' => [],
        ];

        $totalRecords = 0;

        foreach ($this->organizationTables as $table) {
            // Skip if table doesn't exist
            if (!Schema::hasTable($table)) {
                Log::debug("Table does not exist, skipping: {$table}");
                continue;
            }

            // Check if table has organization_id column
            if (!Schema::hasColumn($table, 'organization_id')) {
                Log::debug("Table has no organization_id, skipping: {$table}");
                continue;
            }

            try {
                $records = DB::table($table)
                    ->where('organization_id', $organization->id)
                    ->get()
                    ->toArray();

                $data['tables'][$table] = [
                    'count' => count($records),
                    'data' => $records,
                ];

                $totalRecords += count($records);

                Log::debug("Backed up table", [
                    'table' => $table,
                    'records' => count($records),
                ]);

            } catch (\Exception $e) {
                Log::warning("Failed to backup table: {$table}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Include organization record itself
        $data['organization'] = $organization->toArray();
        $data['metadata']['total_records'] = $totalRecords;
        $data['metadata']['tables_count'] = count($data['tables']);

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
    public function getBackupHistory(int $organizationId, int $limit = 30): array
    {
        return AutoBackupLog::where('organization_id', $organizationId)
            ->orderBy('backup_date', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Restore organization backup
     */
    public function restoreBackup(string $filename, Organization $organization): array
    {
        $filepath = $this->backupPath . '/' . basename($filename);
        
        if (!File::exists($filepath)) {
            return ['success' => false, 'message' => 'Backup file not found'];
        }

        Log::info('Starting organization backup restore', [
            'organization_id' => $organization->id,
            'filename' => $filename,
        ]);

        try {
            // Create a pre-restore backup first
            $preRestoreResult = $this->createPreRestoreBackup($organization);
            if (!$preRestoreResult['success']) {
                Log::warning('Could not create pre-restore backup', [
                    'error' => $preRestoreResult['message'] ?? 'Unknown error',
                ]);
            }

            // Extract ZIP
            $extractPath = $this->backupPath . '/temp_restore_' . time();
            
            if (!File::exists($extractPath)) {
                File::makeDirectory($extractPath, 0755, true);
            }

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
            $recordsRestored = 0;

            foreach ($data['tables'] as $table => $tableData) {
                if (!Schema::hasTable($table)) {
                    Log::warning("Table does not exist during restore: {$table}");
                    continue;
                }

                if (!Schema::hasColumn($table, 'organization_id')) {
                    continue;
                }

                try {
                    // Delete existing organization data
                    $deletedCount = DB::table($table)->where('organization_id', $organization->id)->delete();
                    
                    Log::debug("Deleted existing data for restore", [
                        'table' => $table,
                        'deleted' => $deletedCount,
                    ]);

                    // Insert backup data
                    if (!empty($tableData['data'])) {
                        foreach (array_chunk($tableData['data'], 100) as $chunk) {
                            $insertData = array_map(fn($row) => (array) $row, $chunk);
                            DB::table($table)->insert($insertData);
                            $recordsRestored += count($chunk);
                        }
                    }
                    
                    $tablesRestored++;

                } catch (\Exception $e) {
                    Log::warning("Failed to restore table: {$table}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            
            // Cleanup
            File::deleteDirectory($extractPath);

            Log::info('Organization backup restored successfully', [
                'organization_id' => $organization->id,
                'filename' => $filename,
                'tables_restored' => $tablesRestored,
                'records_restored' => $recordsRestored,
            ]);

            return [
                'success' => true,
                'message' => "Backup restored successfully. {$tablesRestored} tables and {$recordsRestored} records restored.",
                'tables_restored' => $tablesRestored,
                'records_restored' => $recordsRestored,
            ];

        } catch (\Exception $e) {
            // Ensure foreign key checks are re-enabled
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Exception $fkException) {
                // Ignore
            }

            Log::error('Backup restore failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()];
        }
    }

    /**
     * Create a pre-restore backup before restoring
     */
    protected function createPreRestoreBackup(Organization $organization): array
    {
        try {
            $orgCode = $organization->code ?? $organization->id;
            $timestamp = now()->format('Y-m-d_His');
            $filename = "org_{$orgCode}_pre_restore_{$timestamp}.zip";
            $filepath = $this->backupPath . '/' . $filename;

            $backupData = $this->exportOrganizationData($organization);
            
            $jsonFilename = "org_{$orgCode}_pre_restore_{$timestamp}.json";
            $jsonPath = $this->backupPath . '/' . $jsonFilename;
            File::put($jsonPath, json_encode($backupData, JSON_PRETTY_PRINT));
            
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE) === true) {
                $zip->addFile($jsonPath, $jsonFilename);
                $zip->close();
                File::delete($jsonPath);
            }

            Log::info('Pre-restore backup created', [
                'organization_id' => $organization->id,
                'filename' => $filename,
            ]);

            return ['success' => true, 'filename' => $filename];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Download backup file path
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
     * Get list of all organization tables that will be backed up
     */
    public function getBackupTables(): array
    {
        return $this->organizationTables;
    }

    /**
     * Add additional tables to backup list
     */
    public function addBackupTables(array $tables): void
    {
        $this->organizationTables = array_unique(array_merge($this->organizationTables, $tables));
    }
}
