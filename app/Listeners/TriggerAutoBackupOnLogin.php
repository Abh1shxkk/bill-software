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

    /**
     * Handle the event.
     * 
     * This listener triggers an automatic backup when an admin or organization
     * owner logs into the system. The backup is performed asynchronously after
     * the response is sent to avoid delaying the login process.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Skip for super admins - they manage all organizations
        if ($user->isSuperAdmin()) {
            Log::debug('Auto backup skipped: Super admin login', [
                'user_id' => $user->user_id,
            ]);
            return;
        }

        // Skip for users without organization
        if (!$user->organization_id || !$user->organization) {
            Log::debug('Auto backup skipped: No organization assigned', [
                'user_id' => $user->user_id,
            ]);
            return;
        }

        // Skip if not an admin or organization owner
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            Log::debug('Auto backup skipped: User is not admin or owner', [
                'user_id' => $user->user_id,
                'role' => $user->role,
            ]);
            return;
        }

        // Check if auto backup is enabled for organization
        if (!$user->organization->isAutoBackupEnabled()) {
            Log::info('Auto backup disabled for organization', [
                'organization_id' => $user->organization_id,
                'organization_name' => $user->organization->name,
            ]);
            return;
        }

        // Check if backup already exists for today
        if (!$this->backupService->isBackupNeededToday($user->organization_id)) {
            Log::debug('Auto backup already exists for today', [
                'organization_id' => $user->organization_id,
                'day_of_week' => strtolower(now()->format('l')),
            ]);
            return;
        }

        Log::info('Auto backup triggered on login', [
            'organization_id' => $user->organization_id,
            'organization_name' => $user->organization->name,
            'user_id' => $user->user_id,
            'user_name' => $user->full_name,
            'day_of_week' => strtolower(now()->format('l')),
        ]);

        // Perform backup after response is sent (async-like behavior)
        // This ensures the login is not delayed by backup process
        $organization = $user->organization;
        $userId = $user->user_id;
        $backupService = $this->backupService;

        // Use Laravel's dispatch helper with afterResponse for non-blocking execution
        dispatch(function () use ($backupService, $organization, $user) {
            try {
                $result = $backupService->createBackup($organization, $user);
                
                if ($result['success']) {
                    Log::info('Auto backup completed after login', [
                        'organization_id' => $organization->id,
                        'filename' => $result['filename'],
                        'size' => $result['size_formatted'] ?? $result['size'],
                    ]);
                } else {
                    Log::error('Auto backup failed after login', [
                        'organization_id' => $organization->id,
                        'error' => $result['message'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Auto backup exception after login', [
                    'organization_id' => $organization->id,
                    'error' => $e->getMessage(),
                ]);
            }
        })->afterResponse();
    }
}
