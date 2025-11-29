<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system_role',
        'company_id',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system_role' => 'boolean',
    ];

    /**
     * Default system permissions
     */
    const SYSTEM_PERMISSIONS = [
        // User Management
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',

        // Package Management
        'packages.view',
        'packages.create',
        'packages.edit',
        'packages.delete',
        'packages.view_all', // View all packages vs own only

        // Company Management
        'company.view',
        'company.edit',
        'company.settings',

        // Role Management
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',

        // Reports & Analytics
        'reports.view',
        'reports.export',

        // Communication
        'messages.send',
        'messages.view',

        // System Admin (for super admins only)
        'system.manage_companies',
        'system.manage_users',
        'system.view_logs',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    /**
     * Get the company this role belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get user roles for this role
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Add permission to role
     */
    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);
        $this->permissions = array_values($permissions);
        $this->save();
    }

    /**
     * Get system role by name
     */
    public static function getSystemRole($name)
    {
        return static::where('slug', Str::slug($name))
            ->where('is_system_role', true)
            ->first();
    }
}
