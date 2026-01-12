<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the organization
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by model
     */
    public function scopeForModel($query, string $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'created' => 'bg-success',
            'updated' => 'bg-info',
            'deleted' => 'bg-danger',
            'restored' => 'bg-warning',
            'viewed' => 'bg-secondary',
            'exported' => 'bg-primary',
            'login' => 'bg-success',
            'logout' => 'bg-secondary',
            'failed_login' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Log an action
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $notes = null
    ): self {
        $user = auth()->user();
        $request = request();

        $changedFields = null;
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        return self::create([
            'organization_id' => $user?->organization_id,
            'user_id' => $user?->user_id,
            'user_name' => $user?->full_name ?? 'System',
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'model_name' => $model ? self::getModelName($model) : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'notes' => $notes,
        ]);
    }

    /**
     * Get human-readable model name
     */
    protected static function getModelName(Model $model): string
    {
        // Try common name attributes
        foreach (['name', 'full_name', 'title', 'invoice_no', 'code', 'email'] as $attr) {
            if ($model->$attr) {
                return $model->$attr;
            }
        }
        return class_basename($model) . ' #' . $model->getKey();
    }
}
