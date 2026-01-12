<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoBackupLog;
use App\Services\OrganizationBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization assigned to your account.');
        }

        $weeklyStatus = $user->organization->getWeeklyBackupStatus();
        $backupHistory = AutoBackupLog::where('organization_id', $user->organization_id)
            ->orderBy('backup_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();
        
        $autoBackupEnabled = $user->organization->auto_backup_enabled ?? true;
        $todayDay = strtolower(now()->format('l'));
        $backupTables = $this->backupService->getBackupTables();

        return view('admin.auto-backup.index', compact(
            'weeklyStatus',
            'backupHistory',
            'autoBackupEnabled',
            'todayDay',
            'backupTables'
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

        if (!$user->organization_id) {
            return response()->json(['success' => false, 'message' => 'No organization assigned'], 400);
        }

        $user->organization->update([
            'auto_backup_enabled' => $request->boolean('enabled'),
        ]);

        Log::info('Auto backup setting toggled', [
            'organization_id' => $user->organization_id,
            'enabled' => $request->boolean('enabled'),
            'changed_by' => $user->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Auto backup setting updated',
            'enabled' => $user->organization->fresh()->auto_backup_enabled,
        ]);
    }

    /**
     * Manually trigger backup
     */
    public function triggerManualBackup()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return back()->with('error', 'Only admins and organization owners can trigger backups.');
        }

        if (!$user->organization_id || !$user->organization) {
            return back()->with('error', 'No organization assigned to your account.');
        }

        Log::info('Manual backup triggered', [
            'organization_id' => $user->organization_id,
            'triggered_by' => $user->user_id,
        ]);

        $result = $this->backupService->createBackup($user->organization, $user);

        if ($result['success']) {
            return back()->with('success', 'Backup created successfully! File: ' . $result['filename'] . ' (' . ($result['size_formatted'] ?? '') . ')');
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Download a backup file
     */
    public function download(string $filename)
    {
        $user = auth()->user();
        
        if (!$user->organization_id) {
            abort(403, 'No organization assigned');
        }

        // Verify the backup belongs to user's organization
        $backup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('backup_filename', $filename)
            ->where('status', 'success')
            ->first();

        if (!$backup) {
            return back()->with('error', 'Backup not found or access denied.');
        }

        $path = $this->backupService->getBackupPath($filename);
        
        if (!$path) {
            return back()->with('error', 'Backup file not found on disk.');
        }

        Log::info('Backup downloaded', [
            'organization_id' => $user->organization_id,
            'filename' => $filename,
            'downloaded_by' => $user->user_id,
        ]);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request, string $filename)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isOrganizationOwner()) {
            return back()->with('error', 'Only admins and organization owners can restore backups.');
        }

        if (!$user->organization_id || !$user->organization) {
            return back()->with('error', 'No organization assigned to your account.');
        }

        // Verify the backup belongs to user's organization
        $backup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('backup_filename', $filename)
            ->where('status', 'success')
            ->first();

        if (!$backup) {
            return back()->with('error', 'Backup not found or access denied.');
        }

        Log::info('Backup restore initiated', [
            'organization_id' => $user->organization_id,
            'filename' => $filename,
            'initiated_by' => $user->user_id,
        ]);

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

        if (!$user->organization_id) {
            return response()->json(['success' => false, 'message' => 'No organization assigned'], 400);
        }

        // Verify the backup belongs to user's organization
        $backup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('backup_filename', $filename)
            ->first();

        if (!$backup) {
            return response()->json(['success' => false, 'message' => 'Backup not found'], 404);
        }

        Log::info('Backup deletion initiated', [
            'organization_id' => $user->organization_id,
            'filename' => $filename,
            'deleted_by' => $user->user_id,
        ]);

        $deleted = $this->backupService->deleteBackup($filename, $user->organization_id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Backup deleted successfully' : 'Failed to delete backup',
        ]);
    }

    /**
     * Get backup status (for AJAX refresh)
     */
    public function status()
    {
        $user = auth()->user();
        
        if (!$user->organization_id) {
            return response()->json(['success' => false, 'message' => 'No organization assigned'], 400);
        }

        $weeklyStatus = $user->organization->getWeeklyBackupStatus();
        $latestBackup = AutoBackupLog::where('organization_id', $user->organization_id)
            ->where('status', 'success')
            ->orderBy('backup_date', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'weekly_status' => $weeklyStatus,
            'today' => strtolower(now()->format('l')),
            'has_backup_today' => AutoBackupLog::hasBackupForToday($user->organization_id),
            'latest_backup' => $latestBackup ? [
                'filename' => $latestBackup->backup_filename,
                'date' => $latestBackup->backup_date->format('Y-m-d'),
                'size' => $latestBackup->formatted_size,
            ] : null,
        ]);
    }
}
