<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if current user can access a module.
     */
    public static function canAccess(string $module, string $action = 'view'): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermission($module, $action);
    }

    /**
     * Check if current user can view a module.
     */
    public static function canView(string $module): bool
    {
        return self::canAccess($module, 'view');
    }

    /**
     * Check if current user can create in a module.
     */
    public static function canCreate(string $module): bool
    {
        return self::canAccess($module, 'create');
    }

    /**
     * Check if current user can edit in a module.
     */
    public static function canEdit(string $module): bool
    {
        return self::canAccess($module, 'edit');
    }

    /**
     * Check if current user can delete in a module.
     */
    public static function canDelete(string $module): bool
    {
        return self::canAccess($module, 'delete');
    }

    /**
     * Check if current user is admin.
     */
    public static function isAdmin(): bool
    {
        $user = Auth::user();
        return $user && $user->isAdmin();
    }
}
