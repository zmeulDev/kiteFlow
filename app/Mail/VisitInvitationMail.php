<?php

namespace App\Mail;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Visit $visit
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Visit Invitation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visits.invitation',
            with: [
                'visit' => $this->visit,
                'visitor' => $this->visit->visitor,
                'host' => $this->visit->host,
                'checkInCode' => $this->visit->check_in_code,
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