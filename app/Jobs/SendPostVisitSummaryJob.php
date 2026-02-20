<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendPostVisitSummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Visit $visit) {}

    public function handle(): void
    {
        Log::info("Sending GDPR/NDA post-email to {$this->visit->visitor->email}.");
    }
}
