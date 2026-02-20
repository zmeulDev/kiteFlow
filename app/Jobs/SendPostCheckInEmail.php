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

            // Send GDPR/NDA details email to visitor
            if ($visitor->email) {
                Mail::send('emails.visit-post-checkin', [
                    'visit' => $this->visit,
                    'visitor' => $visitor,
                    'tenant' => $tenant,
                    'gdpr_text' => $tenant->nda_text ?? config('kiteflow.default_gdpr_text'),
                    'terms_text' => $tenant->terms_text ?? config('kiteflow.default_terms_text'),
                ], function ($message) use ($visitor, $tenant) {
                    $message->to($visitor->email)
                        ->subject("Thank you for visiting {$tenant->name}");
                });
            }

            Log::info("Post check-in email sent for visit {$this->visit->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send post check-in email: " . $e->getMessage());
        }
    }
}
