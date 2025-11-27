<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'company_id',
        'is_super_admin',
        'preferences',
        'last_login_at',
    ];

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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the company this user belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get user role assignments
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get roles for this user in a specific company
     */
    public function rolesInCompany($companyId): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->wherePivot('company_id', $companyId)
            ->wherePivot('is_active', true)
            ->withPivot(['assigned_at', 'is_active']);
    }

    /**
     * Get packages created by this user
     */
    public function createdPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'created_by');
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    /**
     * Check if user has permission in their company
     */
    public function hasPermission($permission, $companyId = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $companyId = $companyId ?? $this->company_id;

        $roles = $this->rolesInCompany($companyId)->get();

        foreach ($roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions, int $companyId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $companyId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions, int $companyId = null): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $companyId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for user in a company
     */
    public function getPermissions(int $companyId = null): array
    {
        // Super admins have all permissions
        if ($this->is_super_admin) {
            return \App\Models\Role::SYSTEM_PERMISSIONS;
        }

        $companyId = $companyId ?? $this->company_id;
        $permissions = [];

        $roles = $this->rolesInCompany($companyId)->get();
        foreach ($roles as $role) {
            $permissions = array_merge($permissions, $role->permissions);
        }

        return array_unique($permissions);
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers(int $companyId = null): bool
    {
        return $this->hasAnyPermission(['users.create', 'users.edit', 'users.delete'], $companyId);
    }

    /**
     * Check if user is company admin
     */
    public function isCompanyAdmin(int $companyId = null): bool
    {
        $companyId = $companyId ?? $this->company_id;
        $roles = $this->rolesInCompany($companyId)->get();

        return $roles->contains(function($role) {
            return $role->name === 'Company Admin' || $role->hasPermission('company.manage');
        });
    }

    /**
     * Check if user has role in company
     */
    public function hasRole($roleSlug, $companyId = null): bool
    {
        $companyId = $companyId ?? $this->company_id;

        return $this->rolesInCompany($companyId)
            ->where('slug', $roleSlug)
            ->exists();
    }

    /**
     * Assign role to user in company
     */
    public function assignRole($role, $companyId = null)
    {
        $companyId = $companyId ?? $this->company_id;
        $roleId = is_object($role) ? $role->id : $role;

        return UserRole::firstOrCreate([
            'user_id' => $this->id,
            'role_id' => $roleId,
            'company_id' => $companyId,
        ], [
            'is_active' => true,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Remove role from user in company
     */
    public function removeRole($role, $companyId = null)
    {
        $companyId = $companyId ?? $this->company_id;
        $roleId = is_object($role) ? $role->id : $role;

        return UserRole::where([
            'user_id' => $this->id,
            'role_id' => $roleId,
            'company_id' => $companyId,
        ])->delete();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}
