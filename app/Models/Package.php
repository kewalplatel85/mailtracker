<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone_number',
        'mailbox_number',
        'num_packages',
        'tracking_number',
        'status',
        'company_id',
        'created_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Boot the model and add global scopes
     */
    protected static function booted()
    {
        // Auto-scope packages to user's company unless user is super admin
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->is_super_admin) {
                $companyId = session('current_company_id') ?? Auth::user()->company_id;
                if ($companyId) {
                    $builder->where('company_id', $companyId);
                }
            }
        });

        // Auto-set company_id when creating packages
        static::creating(function ($package) {
            if (Auth::check() && !$package->company_id) {
                $companyId = session('current_company_id') ?? Auth::user()->company_id;
                if ($companyId) {
                    $package->company_id = $companyId;
                }
            }

            // Set created_by
            if (Auth::check() && !$package->created_by) {
                $package->created_by = Auth::id();
            }
        });
    }

    /**
     * Get the company this package belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this package
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to specific company
     */
    public function scopeForCompany(Builder $query, $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to specific status
     */
    public function scopeWithStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to specific mailbox
     */
    public function scopeForMailbox(Builder $query, $mailboxNumber): Builder
    {
        return $query->where('mailbox_number', $mailboxNumber);
    }

    /**
     * Get metadata value
     */
    public function getMetadata($key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    /**
     * Set metadata value
     */
    public function setMetadata($key, $value)
    {
        $metadata = $this->metadata ?? [];
        data_set($metadata, $key, $value);
        $this->metadata = $metadata;
        $this->save();
    }
}
