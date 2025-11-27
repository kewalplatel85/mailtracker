<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if current user has permission
     */
    public static function can(string $permission, ?int $companyId = null): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission($permission, $companyId);
    }

    /**
     * Check if current user has any of the given permissions
     */
    public static function canAny(array $permissions, ?int $companyId = null): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyPermission($permissions, $companyId);
    }

    /**
     * Check if current user has all of the given permissions
     */
    public static function canAll(array $permissions, ?int $companyId = null): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAllPermissions($permissions, $companyId);
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->is_super_admin;
    }

    /**
     * Check if current user is company admin
     */
    public static function isCompanyAdmin(?int $companyId = null): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->isCompanyAdmin($companyId);
    }

    /**
     * Get current user's permissions
     */
    public static function getPermissions(?int $companyId = null): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getPermissions($companyId);
    }

    /**
     * Get current company context
     */
    public static function getCurrentCompany()
    {
        if (!Auth::check()) {
            return null;
        }

        $companyId = session('current_company_id') ?? Auth::user()->company_id;

        if (!$companyId) {
            return null;
        }

        return \App\Models\Company::find($companyId);
    }
}
