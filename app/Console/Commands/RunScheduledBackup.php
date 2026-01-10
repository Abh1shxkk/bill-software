<?php

namespace App\Console\Commands;

use App\Models\BackupSchedule;
use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:run-scheduled';
    protected $description = 'Run scheduled database backups';

    public function handle(DatabaseBackupService $backupService): int
    {
        $schedules = BackupSchedule::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            })
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No scheduled backups to run.');
            return 0;
        }

        foreach ($schedules as $schedule) {
            $this->info("Running backup for schedule #{$schedule->id} ({$schedule->frequency_label})");
            
            try {
                $result = $backupService->exportDatabase($schedule->compress);
                
                if ($result['success']) {
                    $schedule->last_run_at = now();
                    $schedule->calculateNextRun();
                    
                    Log::info('Scheduled backup completed', [
                        'schedule_id' => $schedule->id,
                        'filename' => $result['filename'],
                        'size' => $result['size'],
                    ]);
                    
                    $this->info("Backup created: {$result['filename']}");
                    
                    // Clean up old backups based on retention policy
                    $this->cleanupOldBackups($backupService, $schedule->retention_days);
                } else {
                    Log::error('Scheduled backup failed', ['schedule_id' => $schedule->id]);
                    $this->error("Backup failed for schedule #{$schedule->id}");
                }
            } catch (\Exception $e) {
                Log::error('Scheduled backup exception', [
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Error: {$e->getMessage()}");
            }
        }

        return 0;
    }

    protected function cleanupOldBackups(DatabaseBackupService $backupService, int $retentionDays): void
    {
        $backups = $backupService->getBackupHistory();
        $cutoffDate = now()->subDays($retentionDays);

        foreach ($backups as $backup) {
            $createdAt = \Carbon\Carbon::parse($backup['created_at']);
            
            // Only delete auto-generated backups (not pre_restore backups)
            if ($createdAt->lt($cutoffDate) && !str_starts_with($backup['filename'], 'pre_restore_')) {
                $backupService->deleteBackup($backup['filename']);
                $this->info("Deleted old backup: {$backup['filename']}");
                Log::info('Old backup deleted by retention policy', ['filename' => $backup['filename']]);
            }
        }
    }
}
