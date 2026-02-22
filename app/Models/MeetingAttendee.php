<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MeetingAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'attendee_type',
        'attendee_id',
        'type',
        'status',
        'responded_at',
        'notes',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function attendee(): MorphTo
    {
        return $this->morphTo();
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    public function decline(string $notes = null): void
    {
        $this->update([
            'status' => 'declined',
            'responded_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function setTentative(): void
    {
        $this->update([
            'status' => 'tentative',
            'responded_at' => now(),
        ]);
    }

    public function isRequired(): bool
    {
        return $this->type === 'required';
    }

    public function hasResponded(): bool
    {
        return $this->status !== 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }
}