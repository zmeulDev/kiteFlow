<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visit;

class VisitorArrived extends Notification
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
        $visitorName = $this->visit?->visitor?->full_name ?? 'A guest';
        return (new MailMessage)
                    ->greeting("Hello " . ($notifiable->name ?? 'there') . ",")
                    ->line("Your visitor, **{$visitorName}**, has just arrived at the front desk.")
                    ->line("Purpose: " . ($this->visit->purpose ?? 'Meeting'))
                    ->action('View Visitor Details', url('/dashboard'))
                    ->line('Thank you for using KiteFlow!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Visitor Arrived',
            'message' => ($this->visit->visitor->full_name ?? 'A guest') . ' has just arrived for: ' . ($this->visit->purpose ?? 'Meeting'),
            'visitor_name' => $this->visit->visitor->full_name ?? 'A guest',
            'purpose' => $this->visit->purpose,
            'visit_id' => $this->visit->id,
        ];
    }
}
