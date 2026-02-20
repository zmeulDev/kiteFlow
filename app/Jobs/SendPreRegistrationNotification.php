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

class SendPreRegistrationNotification implements ShouldQueue
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
            $host = $this->visit->hostUser;

            // Send email to visitor
            if ($visitor->email) {
                Mail::send('emails.visit-pre-registered', [
                    'visit' => $this->visit,
                    'visitor' => $visitor,
                    'tenant' => $tenant,
                    'host' => $host,
                ], function ($message) use ($visitor, $tenant) {
                    $message->to($visitor->email)
                        ->subject("Your visit to {$tenant->name} is confirmed");
                });
            }

            // Send WhatsApp notification if phone available
            if ($visitor->phone) {
                // Implement WhatsApp notification here
                // This would typically use Twilio or similar
                Log::info("WhatsApp notification would be sent to {$visitor->phone}");
            }

            Log::info("Pre-registration notification sent for visit {$this->visit->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send pre-registration notification: " . $e->getMessage());
        }
    }
}
