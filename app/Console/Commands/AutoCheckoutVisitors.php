<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCheckoutVisitors extends Command
{
    protected $signature = 'visitors:auto-checkout';
    protected $description = 'Automatically check out visitors who forgot to check out';

    public function handle(): int
    {
        $this->info('Running auto-checkout for visitors...');

        // Find visits that are checked in but past their scheduled end time
        $visits = Visit::where('status', 'checked_in')
            ->where('scheduled_end', '<', Carbon::now())
            ->get();

        $count = 0;
        foreach ($visits as $visit) {
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
        }

        $this->info("Completed: {$count} visitors auto-checked out.");

        return Command::SUCCESS;
    }
}
