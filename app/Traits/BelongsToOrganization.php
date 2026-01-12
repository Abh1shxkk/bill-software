<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to an organization (multi-tenancy)
 * 
 * This trait automatically:
 * 1. Filters queries to only return records belonging to the current user's organization
 * 2. Sets the organization_id when creating new records
 * 3. Provides relationship to Organization model
 */
trait BelongsToOrganization
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToOrganization(): void
    {
        // Auto-set organization_id when creating a new record
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->organization_id && !$model->organization_id) {
                $model->organization_id = auth()->user()->organization_id;
            }
        });

        // Add global scope to filter by organization_id
        static::addGlobalScope('organization', function (Builder $query) {
            // Skip scope for super admin or when no user is authenticated
            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();
            
            // Super admin can see all data
            if ($user->isSuperAdmin()) {
                return;
            }

            // Filter by user's organization
            if ($user->organization_id) {
                $query->where($query->getModel()->getTable() . '.organization_id', $user->organization_id);
            }
        });
    }

    /**
     * Get the organization this model belongs to
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Scope to filter by specific organization
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where($this->getTable() . '.organization_id', $organizationId);
    }

    /**
     * Scope to include all organizations (bypass global scope)
     * Only works for super admin
     */
    public function scopeWithAllOrganizations(Builder $query): Builder
    {
        return $query->withoutGlobalScope('organization');
    }

    /**
     * Check if model belongs to specified organization
     */
    public function belongsToOrganization(int $organizationId): bool
    {
        return $this->organization_id === $organizationId;
    }

    /**
     * Check if model belongs to current user's organization
     */
    public function belongsToCurrentOrganization(): bool
    {
        if (!auth()->check() || !auth()->user()->organization_id) {
            return false;
        }
        
        return $this->organization_id === auth()->user()->organization_id;
    }
}
