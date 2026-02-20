<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCheckoutVisitors extends Command
{
    protected $signature = 'visitors:auto-checkout';
    protected $description = 'Automatically check out visitors who forgot to check out';

    public function handle(): int
    {
        $this->info('Running auto-checkout for visitors...');

        // Find visits that are checked in but past their scheduled end time + 30 min grace period
        $gracePeriod = Carbon::now()->subMinutes(30);
        
        $visits = Visit::where('status', 'checked_in')
            ->where('scheduled_end', '<', $gracePeriod)
            ->get();

        $count = 0;
        foreach ($visits as $visit) {
            // Auto-checkout the visit
            $visit->update([
                'status' => 'checked_out',
                'checked_out_at' => $visit->scheduled_end,
            ]);

            // Update the active check-in record
            $activeCheckIn = $visit->checkIns()->whereNull('check_out_time')->first();
            if ($activeCheckIn) {
                $activeCheckIn->update([
                    'check_out_time' => $visit->scheduled_end,
                    'check_out_method' => 'auto',
                ]);
            }

            $count++;
            $this->line("Auto-checked out visit {$visit->visit_code}");
            Log::info("Auto-checkout: Visit {$visit->visit_code} auto-checked out");
        }

        $this->info("Completed: {$count} visitors auto-checked out.");
        Log::info("Auto-checkout job completed: {$count} visitors");

        return Command::SUCCESS;
    }
}
