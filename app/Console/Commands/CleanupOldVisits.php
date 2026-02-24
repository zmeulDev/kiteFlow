<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;

class CleanupOldVisits extends Command
{
    protected $signature = 'visits:cleanup';

    protected $description = 'Clean up visits older than the retention period';

    public function handle(DataRetentionService $retentionService): int
    {
        $this->info('Starting cleanup of old visits...');

        $visitsDeleted = $retentionService->cleanupOldVisits();
        $this->info("Deleted {$visitsDeleted} old visits.");

        $visitorsDeleted = $retentionService->cleanupOrphanedVisitors();
        $this->info("Deleted {$visitorsDeleted} orphaned visitors.");

        $this->info('Cleanup completed successfully.');

        return self::SUCCESS;
    }
}