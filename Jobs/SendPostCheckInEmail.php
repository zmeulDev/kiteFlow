<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPostCheckInEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Visit $visit
    ) {}

    public function handle(): void
    {
        try {
            $visitor = $this->visit->visitor;
            $tenant = $this->visit->tenant;

            if (!$visitor->email) {
                Log::warning("No visitor email for post-check-in email: visit {$this->visit->id}");
                return;
            }

            // Send GDPR/NDA details email
            Mail::send('emails.post-checkin', [
                'visit' => $this->visit,
                'visitor' => $visitor,
                'tenant' => $tenant,
            ], function ($message) use ($visitor, $tenant) {
                $message->to($visitor->email)
                    ->subject("Thank you for visiting {$tenant->name}");
            });

            Log::info("Post check-in email sent for visit {$this->visit->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send post check-in email: " . $e->getMessage());
        }
    }
}
