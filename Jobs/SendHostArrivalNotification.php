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

class SendHostArrivalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Visit $visit
    ) {}

    public function handle(): void
    {
        try {
            $visitor = $this->visit->visitor;
            $host = $this->visit->hostUser;
            $tenant = $this->visit->tenant;

            if (!$host || !$host->email) {
                Log::warning("No host email found for visit {$this->visit->id}");
                return;
            }

            // Send email to host
            Mail::send('emails.host-arrival', [
                'visit' => $this->visit,
                'visitor' => $visitor,
                'tenant' => $tenant,
                'host' => $host,
            ], function ($message) use ($host, $tenant) {
                $message->to($host->email)
                    ->subject("Visitor Arrived - {$tenant->name}");
            });

            // Send WhatsApp notification to host
            if ($host->phone) {
                Log::info("WhatsApp arrival alert would be sent to host {$host->phone}");
            }

            Log::info("Host arrival notification sent for visit {$this->visit->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send host arrival notification: " . $e->getMessage());
        }
    }
}
