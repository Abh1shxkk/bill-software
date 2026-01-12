<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'license_id',
        'action',
        'performed_by',
        'ip_address',
        'user_agent',
        'metadata',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_at = $model->created_at ?? now();
        });
    }

    /**
     * Get the license this log belongs to
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Get the user who performed the action
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }

    /**
     * Get human-readable action description
     */
    public function getActionDescriptionAttribute(): string
    {
        $descriptions = [
            'created' => 'License was created',
            'activated' => 'License was activated',
            'renewed' => 'License was renewed',
            'extended' => 'License expiry was extended',
            'suspended' => 'License was suspended',
            'reactivated' => 'License was reactivated',
            'expired' => 'License has expired',
            'revoked' => 'License was revoked',
            'updated' => 'License was updated',
        ];

        return $descriptions[$this->action] ?? 'Unknown action';
    }

    /**
     * Get badge class for action
     */
    public function getActionBadgeClassAttribute(): string
    {
        $classes = [
            'created' => 'bg-primary',
            'activated' => 'bg-success',
            'renewed' => 'bg-info',
            'extended' => 'bg-info',
            'suspended' => 'bg-warning',
            'reactivated' => 'bg-success',
            'expired' => 'bg-danger',
            'revoked' => 'bg-danger',
            'updated' => 'bg-secondary',
        ];

        return $classes[$this->action] ?? 'bg-secondary';
    }
}
