<?php

namespace App\Traits;

/**
 * Trait for Report Controllers
 * Provides helper methods for organization-scoped queries
 */
trait ReportHelperTrait
{
    /**
     * Get the current user's organization ID
     * 
     * @return int
     */
    protected function getOrganizationId(): int
    {
        return auth()->user()->organization_id ?? 1;
    }

    /**
     * Apply organization filter to a query builder
     * Use this for DB::table() queries that bypass Eloquent global scopes
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string|null $table Table name/alias if needed for joins
     * @return \Illuminate\Database\Query\Builder
     */
    protected function applyOrganizationFilter($query, ?string $table = null)
    {
        $column = $table ? "{$table}.organization_id" : 'organization_id';
        return $query->where($column, $this->getOrganizationId());
    }

    /**
     * Check if current user is super admin (can see all organizations)
     * 
     * @return bool
     */
    protected function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Apply organization filter only for non-super-admin users
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string|null $table Table name/alias if needed for joins
     * @return \Illuminate\Database\Query\Builder
     */
    protected function applyOrganizationFilterIfNotSuperAdmin($query, ?string $table = null)
    {
        if ($this->isSuperAdmin()) {
            return $query;
        }
        return $this->applyOrganizationFilter($query, $table);
    }
}
