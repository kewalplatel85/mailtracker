<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PackageWorkflowService;

class ProcessPackageWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:process-workflow {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automated package workflow transitions';

    /**
     * Execute the console command.
     */
    public function handle(PackageWorkflowService $workflowService): int
    {
        $this->info('Processing package workflow transitions...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Process auto-transitions
        if (!$this->option('dry-run')) {
            $processed = $workflowService->processAutoTransitions();
            $this->info("Auto-transitioned {$processed} packages to 'Ready for Pickup'");
        } else {
            // Show what would be processed
            $packages = \App\Models\Package::readyForAutoTransition()->get();
            $this->info("Would auto-transition {$packages->count()} packages:");

            foreach ($packages as $package) {
                $this->line("  - Package #{$package->id} (Mailbox: {$package->mailbox_number}, Customer: {$package->customer_name})");
            }
        }

        // Show aging packages report
        $agingPackages = $workflowService->getAgingPackages();
        if ($agingPackages->count() > 0) {
            $this->warn("Found {$agingPackages->count()} aging packages (ready for pickup >7 days):");
            foreach ($agingPackages->take(5) as $package) {
                $days = $package->getAgeInDays();
                $this->line("  - Package #{$package->id} ({$days} days old, Mailbox: {$package->mailbox_number})");
            }

            if ($agingPackages->count() > 5) {
                $this->line("  ... and " . ($agingPackages->count() - 5) . " more");
            }
        }

        // Show workflow statistics
        $stats = $workflowService->getWorkflowStats();
        $this->newLine();
        $this->info('Workflow Statistics:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Incoming', $stats['incoming']],
                ['Ready for Pickup', $stats['ready_for_pickup']],
                ['Picked up Today', $stats['picked_up_today']],
                ['Aging Packages (>7 days)', $stats['aging_packages']],
            ]
        );

        $this->info("Average processing time: " . round($stats['average_processing_time'], 1) . " hours");

        return Command::SUCCESS;
    }
}
