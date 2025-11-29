<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'email',
        'phone',
        'address',
        'logo',
        'settings',
        'status',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_expires_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    /**
     * Get all users for this company
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all roles for this company
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get all packages for this company
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Get user roles through the pivot table
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Check if company is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is valid
     */
    public function hasValidSubscription(): bool
    {
        if (!$this->subscription_expires_at) {
            return true; // No expiration set
        }

        return $this->subscription_expires_at->isFuture();
    }

    /**
     * Get company setting
     */
    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set company setting
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }
}
