<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visit;

class SecurityAlert extends Notification
{
    use Queueable;

    public function __construct(public Visit $visit)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $visitorName = $this->visit->visitor->full_name ?? 'A flagged person';
        return (new MailMessage)
                    ->error()
                    ->subject('⚠️ SECURITY ALERT: Flagged Visitor Detected')
                    ->line("A flagged visitor, **{$visitorName}**, has just attempted to check in.")
                    ->line("Purpose: {$this->visit->purpose}")
                    ->line("Internal Notes: " . ($this->visit->visitor->internal_notes ?? 'No notes provided'))
                    ->action('View Details', url('/dashboard'))
                    ->line('Please take appropriate action immediately.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Security Alert',
            'message' => 'A flagged visitor (' . ($this->visit->visitor->full_name ?? 'A flagged person') . ') has just attempted to check in.',
            'visitor_name' => $this->visit->visitor->full_name ?? 'A flagged person',
            'purpose' => $this->visit->purpose,
            'visit_id' => $this->visit->id,
            'alert_type' => 'security',
        ];
    }
}
