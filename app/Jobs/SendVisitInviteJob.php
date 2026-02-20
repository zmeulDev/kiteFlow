<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendVisitInviteJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Visit $visit) {}

    public function handle(): void
    {
        Log::info("Sending invite to {$this->visit->visitor->email} with code {$this->visit->invite_code}.");
    }
}
