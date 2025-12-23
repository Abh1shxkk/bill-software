<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        ];
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
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has permission for a module.
     */
    public function hasPermission(string $module, string $action = 'view'): bool
    {
        // Admin has all permissions
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
        return $this->hasPermission($module, 'create');
    }

    /**
     * Check if user can edit in a module.
     */
    public function canEdit(string $module): bool
    {
        return $this->hasPermission($module, 'edit');
    }

    /**
     * Check if user can delete in a module.
     */
    public function canDelete(string $module): bool
    {
        return $this->hasPermission($module, 'delete');
    }

    /**
     * Get all module permissions for the user as an array.
     */
    public function getPermissionsArray(): array
    {
        if ($this->isAdmin()) {
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
}
