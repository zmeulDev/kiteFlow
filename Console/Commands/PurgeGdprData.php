<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\Visitor;
use App\Models\CheckIn;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PurgeGdprData extends Command
{
    protected $signature = 'visitors:purge-gdpr {--tenant= : Specific tenant ID to purge}';
    protected $description = 'Purges visitor data older than the retention period for GDPR compliance';

    public function handle(): int
    {
        $this->info('Starting GDPR data purge...');

        $purgedVisits = 0;
        $purgedVisitors = 0;
        $purgedCheckIns = 0;

        $tenants = \App\Models\Tenant::query()
            ->when($this->option('tenant'), fn($q, $id) => $q->where('id', $id))
            ->get();

        foreach ($tenants as $tenant) {
            $retentionMonths = $tenant->gdpr_retention_months ?? 6;
            $cutoffDate = Carbon::now()->subMonths($retentionMonths);

            $this->info("Processing tenant: {$tenant->name} (retention: {$retentionMonths} months, cutoff: {$cutoffDate->format('Y-m-d')})");

            // Find visits older than retention period and already checked out
            $oldVisits = Visit::where('tenant_id', $tenant->id)
                ->where('status', 'checked_out')
                ->where('checked_out_at', '<', $cutoffDate)
                ->get();

            foreach ($oldVisits as $visit) {
                // Delete related check-ins first
                $checkInsDeleted = $visit->checkIns()->delete();
                $purgedCheckIns += $checkInsDeleted;

                // Delete the visit
                $visit->delete();
                $purgedVisits++;
            }

            // Delete visitors with no more visits and older than cutoff
            $oldVisitors = Visitor::where('tenant_id', $tenant->id)
                ->whereDoesntHave('visits')
                ->where('created_at', '<', $cutoffDate)
                ->get();

            foreach ($oldVisitors as $visitor) {
                $visitor->delete();
                $purgedVisitors++;
            }

            $this->info("  Purged: {$purgedVisits} visits, {$purgedCheckIns} check-ins, {$purgedVisitors} visitors");
        }

        $this->info("GDPR purge completed: {$purgedVisits} visits, {$purgedCheckIns} check-ins, {$purgedVisitors} visitors");
        Log::info("GDPR purge completed: {$purgedVisits} visits, {$purgedCheckIns} check-ins, {$purgedVisitors} visitors");

        return Command::SUCCESS;
    }
}
