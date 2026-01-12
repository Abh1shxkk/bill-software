<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the trait
     */
    protected static function bootAuditable()
    {
        // Log creation
        static::created(function ($model) {
            if (self::shouldAudit()) {
                AuditLog::log(
                    'created',
                    $model,
                    null,
                    $model->getAttributes()
                );
            }
        });

        // Log updates
        static::updated(function ($model) {
            if (self::shouldAudit()) {
                $original = $model->getOriginal();
                $changes = $model->getChanges();
                
                // Don't log if no real changes
                if (empty($changes)) {
                    return;
                }

                // Remove timestamps from changes
                unset($changes['updated_at'], $changes['created_at']);
                
                if (!empty($changes)) {
                    $oldValues = array_intersect_key($original, $changes);
                    AuditLog::log(
                        'updated',
                        $model,
                        $oldValues,
                        $changes
                    );
                }
            }
        });

        // Log deletion
        static::deleted(function ($model) {
            if (self::shouldAudit()) {
                AuditLog::log(
                    'deleted',
                    $model,
                    $model->getAttributes(),
                    null
                );
            }
        });

        // Log restoration (for soft deletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if (self::shouldAudit()) {
                    AuditLog::log(
                        'restored',
                        $model,
                        null,
                        $model->getAttributes()
                    );
                }
            });
        }
    }

    /**
     * Check if auditing should happen
     */
    protected static function shouldAudit(): bool
    {
        // Skip during seeding or migrations
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return false;
        }

        // Skip if no authenticated user
        if (!auth()->check()) {
            return false;
        }

        return true;
    }

    /**
     * Get fields that should be hidden from audit logs
     */
    protected function getAuditExclude(): array
    {
        return property_exists($this, 'auditExclude') ? $this->auditExclude : ['password', 'remember_token'];
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return AuditLog::where('model_type', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc');
    }
}
