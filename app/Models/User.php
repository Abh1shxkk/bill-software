<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role',
        'organization_id',
        'is_organization_owner',
        'profile_picture',
        'address',
        'telephone',
        'tin_no',
        'gst_no',
        'dl_no',
        'dl_no_1',
        'licensed_to',
        'is_active',
    ];

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_organization_owner' => 'boolean',
        ];
    }

    /**
     * Get the organization this user belongs to
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Get the permissions for the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
            ->withPivot(['can_view', 'can_create', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }

    /**
     * Get user permissions records.
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id', 'user_id');
    }

    /**
     * Check if user is super admin (platform level).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin (organization level).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Check if user is organization admin (not super admin).
     */
    public function isOrganizationAdmin(): bool
    {
        return $this->role === 'admin' && !$this->isSuperAdmin();
    }

    /**
     * Check if user is the owner of their organization.
     */
    public function isOrganizationOwner(): bool
    {
        return $this->is_organization_owner === true;
    }

    /**
     * Check if user is manager.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff' || $this->role === 'user';
    }

    /**
     * Check if user is readonly.
     */
    public function isReadonly(): bool
    {
        return $this->role === 'readonly';
    }

    /**
     * Get current active license for user's organization
     */
    public function getActiveLicense(): ?License
    {
        if (!$this->organization_id) {
            return null;
        }

        return $this->organization?->activeLicense;
    }

    /**
     * Check if user's organization has a valid license
     */
    public function hasValidLicense(): bool
    {
        if ($this->isSuperAdmin()) {
            return true; // Super admin always has access
        }

        $license = $this->getActiveLicense();
        return $license && $license->isValid();
    }

    /**
     * Check if user has permission for a module.
     */
    public function hasPermission(string $module, string $action = 'view'): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin has all permissions for their organization
        if ($this->isAdmin()) {
            return true;
        }

        $permission = $this->permissions()
            ->where('name', $module)
            ->first();

        if (!$permission) {
            return false;
        }

        $actionField = 'can_' . $action;
        return (bool) $permission->pivot->$actionField;
    }

    /**
     * Check if user can view a module.
     */
    public function canView(string $module): bool
    {
        return $this->hasPermission($module, 'view');
    }

    /**
     * Check if user can create in a module.
     */
    public function canCreate(string $module): bool
    {
        if ($this->isReadonly()) {
            return false;
        }
        return $this->hasPermission($module, 'create');
    }

    /**
     * Check if user can edit in a module.
     */
    public function canEdit(string $module): bool
    {
        if ($this->isReadonly()) {
            return false;
        }
        return $this->hasPermission($module, 'edit');
    }

    /**
     * Check if user can delete in a module.
     */
    public function canDelete(string $module): bool
    {
        if ($this->isReadonly()) {
            return false;
        }
        return $this->hasPermission($module, 'delete');
    }

    /**
     * Get all module permissions for the user as an array.
     */
    public function getPermissionsArray(): array
    {
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return Permission::pluck('name')->mapWithKeys(function ($name) {
                return [$name => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true]];
            })->toArray();
        }

        return $this->permissions->mapWithKeys(function ($permission) {
            return [$permission->name => [
                'view' => (bool) $permission->pivot->can_view,
                'create' => (bool) $permission->pivot->can_create,
                'edit' => (bool) $permission->pivot->can_edit,
                'delete' => (bool) $permission->pivot->can_delete,
            ]];
        })->toArray();
    }

    /**
     * Get user's role display name
     */
    public function getRoleDisplayNameAttribute(): string
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'staff' => 'Staff',
            'user' => 'User',
            'readonly' => 'Read Only',
        ];

        return $roles[$this->role] ?? 'Unknown';
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeClassAttribute(): string
    {
        $classes = [
            'super_admin' => 'bg-danger',
            'admin' => 'bg-primary',
            'manager' => 'bg-info',
            'staff' => 'bg-secondary',
            'user' => 'bg-secondary',
            'readonly' => 'bg-warning',
        ];

        return $classes[$this->role] ?? 'bg-secondary';
    }

    /**
     * Scope to filter users by organization
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope to filter only super admins
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }

    /**
     * Scope to filter only organization admins
     */
    public function scopeOrganizationAdmins($query)
    {
        return $query->where('role', 'admin')->whereNotNull('organization_id');
    }
}
