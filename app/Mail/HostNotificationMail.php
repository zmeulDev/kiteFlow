<?php

namespace App\Mail;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HostNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Visit $visit
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Have a Scheduled Visit',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visits.host-notification',
            with: [
                'visit' => $this->visit,
                'visitor' => $this->visit->visitor,
                'scheduledAt' => $this->visit->scheduled_at,
                'entrance' => $this->visit->entrance,
                'building' => $this->visit->entrance?->building,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}