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

    /**
     * Get the organization this backup belongs to
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who triggered this backup
     */
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
     * This will delete the previous same-day backup (from last week)
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

    /**
     * Get formatted backup size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->backup_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            'in_progress' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    /**
     * Scope: Only successful backups
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: Filter by organization
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
