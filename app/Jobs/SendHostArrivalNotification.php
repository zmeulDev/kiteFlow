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
            $room = $this->visit->meetingRoom;

            // Notify host via email
            if ($host && $host->email) {
                Mail::send('emails.host-arrival', [
                    'visit' => $this->visit,
                    'visitor' => $visitor,
                    'host' => $host,
                    'tenant' => $tenant,
                    'room' => $room,
                ], function ($message) use ($host, $tenant) {
                    $message->to($host->email)
                        ->subject("{$tenant->name}: Your guest has arrived");
                });
            }

            // Notify host via WhatsApp
            if ($host && $host->phone) {
                Log::info("WhatsApp arrival notification would be sent to host {$host->phone}");
            }

            Log::info("Host arrival notification sent for visit {$this->visit->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send host arrival notification: " . $e->getMessage());
        }
    }
}
