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
        // Workflow fields
        'received_at',
        'ready_at',
        'picked_up_at',
        'notified_at',
        'auto_ready',
        'days_to_ready',
        'status_notes',
        'previous_status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'received_at' => 'datetime',
        'ready_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'notified_at' => 'datetime',
        'auto_ready' => 'boolean',
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

            // Set received_at timestamp for incoming packages
            if ($package->status === 'Incoming' && !$package->received_at) {
                $package->received_at = now();
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

    /**
     * Check if package can transition to given status
     */
    public function canTransitionTo($status): bool
    {
        $workflowService = app(\App\Services\PackageWorkflowService::class);
        return $workflowService->isValidTransition($this->status, $status);
    }

    /**
     * Get next possible statuses
     */
    public function getNextStatuses(): array
    {
        $workflowService = app(\App\Services\PackageWorkflowService::class);
        return $workflowService->getNextStatuses($this->status);
    }

    /**
     * Get package age in days
     */
    public function getAgeInDays(): int
    {
        $startDate = $this->received_at ?? $this->created_at;
        return $startDate->diffInDays(now());
    }

    /**
     * Check if package is aging (ready for pickup but not picked up)
     */
    public function isAging($daysThreshold = 7): bool
    {
        return $this->status === 'Ready for Pickup'
            && $this->ready_at
            && $this->ready_at->diffInDays(now()) >= $daysThreshold;
    }

    /**
     * Get processing time in hours (if picked up)
     */
    public function getProcessingTimeHours(): ?float
    {
        if (!$this->received_at || !$this->picked_up_at) {
            return null;
        }

        return $this->received_at->diffInHours($this->picked_up_at);
    }

    /**
     * Scope for aging packages
     */
    public function scopeAging(Builder $query, int $days = 7): Builder
    {
        return $query->where('status', 'Ready for Pickup')
            ->where('ready_at', '<=', now()->subDays($days));
    }

    /**
     * Scope for ready packages that need auto-transition
     */
    public function scopeReadyForAutoTransition(Builder $query): Builder
    {
        return $query->where('status', 'Incoming')
            ->where('auto_ready', true)
            ->where(function($q) {
                $q->where('days_to_ready', 0)->whereNull('ready_at')
                  ->orWhere(function($subQ) {
                      $subQ->where('days_to_ready', '>', 0)
                           ->whereRaw('DATE_ADD(created_at, INTERVAL days_to_ready DAY) <= NOW()')
                           ->whereNull('ready_at');
                  });
            });
    }
}
