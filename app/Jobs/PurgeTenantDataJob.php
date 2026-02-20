<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PurgeTenantDataJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Tenant::chunk(50, function ($tenants) {
            foreach ($tenants as $tenant) {
                $days = $tenant->data_retention_days ?? 180;
                $cutoff = now()->subDays($days);
                
                // Purge visits
                $tenant->visits()
                       ->where('check_out_time', '<', $cutoff)
                       ->where('status', 'completed')
                       ->delete();

                // Purge visitors without recent visits
                $tenant->visitors()->whereDoesntHave('visits', function ($query) use ($cutoff) {
                    $query->where('check_out_time', '>=', $cutoff)
                          ->orWhereNull('check_out_time');
                })->delete();

                Log::info("Purged data for tenant {$tenant->id} older than {$cutoff->toDateString()}");
            }
        });
    }
}
