<?php

namespace App\Services;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageWorkflowService
{
    /**
     * Package workflow statuses
     */
    const STATUS_INCOMING = 'Incoming';
    const STATUS_READY_FOR_PICKUP = 'Ready for Pickup';
    const STATUS_PICKED_UP = 'Picked Up';
    const STATUS_ARCHIVED = 'Archived';

    /**
     * Valid status transitions
     */
    const VALID_TRANSITIONS = [
        self::STATUS_INCOMING => [self::STATUS_READY_FOR_PICKUP],
        self::STATUS_READY_FOR_PICKUP => [self::STATUS_PICKED_UP, self::STATUS_INCOMING],
        self::STATUS_PICKED_UP => [self::STATUS_ARCHIVED],
        self::STATUS_ARCHIVED => [], // No transitions from archived
    ];

    /**
     * Transition a package to a new status
     */
    public function transitionStatus(Package $package, string $newStatus, array $options = []): bool
    {
        $currentStatus = $package->status;

        // Validate transition
        if (!$this->isValidTransition($currentStatus, $newStatus)) {
            Log::warning("Invalid status transition for package {$package->id}: {$currentStatus} -> {$newStatus}");
            return false;
        }

        DB::beginTransaction();
        try {
            // Store previous status
            $package->previous_status = $currentStatus;
            $package->status = $newStatus;

            // Set appropriate timestamp
            $this->setStatusTimestamp($package, $newStatus, $options);

            // Add status notes if provided
            if (isset($options['notes'])) {
                $package->status_notes = $options['notes'];
            }

            $package->save();

            DB::commit();

            Log::info("Package {$package->id} status changed: {$currentStatus} -> {$newStatus}");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to transition package {$package->id} status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a status transition is valid
     */
    public function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        return in_array($newStatus, self::VALID_TRANSITIONS[$currentStatus] ?? []);
    }

    /**
     * Auto-transition packages that are ready
     */
    public function processAutoTransitions(): int
    {
        $processed = 0;

        // Get packages that should auto-transition to "Ready for Pickup"
        $packages = Package::where('status', self::STATUS_INCOMING)
            ->where('auto_ready', true)
            ->where(function($query) {
                // Auto-transition immediately (days_to_ready = 0)
                $query->where('days_to_ready', 0)
                    ->whereNull('ready_at');
            })
            ->orWhere(function($query) {
                // Auto-transition after specified days
                $query->where('days_to_ready', '>', 0)
                    ->whereRaw('DATE_ADD(created_at, INTERVAL days_to_ready DAY) <= NOW()')
                    ->whereNull('ready_at');
            })
            ->get();

        foreach ($packages as $package) {
            if ($this->transitionStatus($package, self::STATUS_READY_FOR_PICKUP, [
                'notes' => 'Auto-transitioned to ready for pickup'
            ])) {
                $processed++;
            }
        }

        if ($processed > 0) {
            Log::info("Auto-transitioned {$processed} packages to ready for pickup");
        }

        return $processed;
    }

    /**
     * Get packages that need attention (aging packages)
     */
    public function getAgingPackages(int $daysOld = 7, ?int $companyId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Package::where('status', self::STATUS_READY_FOR_PICKUP)
            ->where('ready_at', '<=', Carbon::now()->subDays($daysOld));

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    /**
     * Bulk transition multiple packages
     */
    public function bulkTransition(array $packageIds, string $newStatus, array $options = []): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($packageIds as $packageId) {
            $package = Package::find($packageId);
            if (!$package) {
                $results['failed'][] = ['id' => $packageId, 'reason' => 'Package not found'];
                continue;
            }

            if ($this->transitionStatus($package, $newStatus, $options)) {
                $results['success'][] = $packageId;
            } else {
                $results['failed'][] = ['id' => $packageId, 'reason' => 'Invalid transition'];
            }
        }

        return $results;
    }

    /**
     * Get workflow statistics
     */
    public function getWorkflowStats(?int $companyId = null): array
    {
        $baseQuery = function($status) use ($companyId) {
            $query = Package::where('status', $status);
            if ($companyId !== null) {
                $query->where('company_id', $companyId);
            }
            return $query;
        };

        $pickedUpQuery = Package::where('status', self::STATUS_PICKED_UP)
            ->whereDate('picked_up_at', Carbon::today());
        if ($companyId !== null) {
            $pickedUpQuery->where('company_id', $companyId);
        }

        return [
            'incoming' => $baseQuery(self::STATUS_INCOMING)->count(),
            'ready_for_pickup' => $baseQuery(self::STATUS_READY_FOR_PICKUP)->count(),
            'picked_up_today' => $pickedUpQuery->count(),
            'aging_packages' => $this->getAgingPackages(7, $companyId)->count(),
            'average_processing_time' => $this->getAverageProcessingTime($companyId),
        ];
    }

    /**
     * Get average processing time (incoming -> picked up)
     */
    private function getAverageProcessingTime(?int $companyId = null): float
    {
        $query = Package::whereNotNull('picked_up_at')
            ->whereNotNull('received_at')
            ->where('picked_up_at', '>=', Carbon::now()->subDays(30));

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        $packages = $query->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, received_at, picked_up_at)) as avg_hours'))
            ->first();

        return $packages->avg_hours ?? 0;
    }

    /**
     * Set the appropriate timestamp for status changes
     */
    private function setStatusTimestamp(Package $package, string $status, array $options = []): void
    {
        $timestamp = $options['timestamp'] ?? Carbon::now();

        switch ($status) {
            case self::STATUS_INCOMING:
                $package->received_at = $timestamp;
                break;
            case self::STATUS_READY_FOR_PICKUP:
                $package->ready_at = $timestamp;
                break;
            case self::STATUS_PICKED_UP:
                $package->picked_up_at = $timestamp;
                break;
        }
    }

    /**
     * Get all available statuses
     */
    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_INCOMING,
            self::STATUS_READY_FOR_PICKUP,
            self::STATUS_PICKED_UP,
            self::STATUS_ARCHIVED
        ];
    }

    /**
     * Get next possible statuses for current status
     */
    public function getNextStatuses(string $currentStatus): array
    {
        return self::VALID_TRANSITIONS[$currentStatus] ?? [];
    }
}
