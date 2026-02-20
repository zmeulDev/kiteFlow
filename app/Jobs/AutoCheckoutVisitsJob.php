<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AutoCheckoutVisitsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Auto-checkout any checked_in visitors that forgot to manually check out at End-of-Day
        // It's possible we might want to schedule this per tenant timezone, but for now we do system daily.
        Visit::where('status', 'checked_in')
            ->whereDate('scheduled_at', '<=', today())
            ->update([
                'status' => 'completed',
                'check_out_time' => now()
            ]);
    }
}
