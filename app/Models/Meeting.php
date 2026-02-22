<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasFactory, SoftDeletes, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'meeting_room_id',
        'host_id',
        'visitor_id',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'title',
        'description',
        'purpose',
        'start_at',
        'end_at',
        'timezone',
        'is_all_day',
        'is_recurring',
        'recurrence_rule',
        'status',
        'cancellation_reason',
        'check_in_code',
        'checklist',
        'meeting_url',
        'meeting_type',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'recurrence_rule' => 'array',
        'checklist' => 'array',
        'custom_fields' => 'array',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $meeting): void {
            if (empty($meeting->uuid)) {
                $meeting->uuid = Str::uuid();
            }
            if (empty($meeting->check_in_code)) {
                $meeting->check_in_code = strtoupper(Str::random(8));
            }
        });
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function meetingRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function visitorAttendees(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class, 'meeting_attendees', 'meeting_id', 'attendee_id')
            ->where('attendee_type', Visitor::class)
            ->withPivot('type', 'status', 'responded_at', 'notes')
            ->withTimestamps();
    }

    public function userAttendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_attendees', 'meeting_id', 'attendee_id')
            ->where('attendee_type', User::class)
            ->withPivot('type', 'status', 'responded_at', 'notes')
            ->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(MeetingNotification::class);
    }

    public function visitorVisits(): HasMany
    {
        return $this->hasMany(VisitorVisit::class);
    }

    public function getDurationInMinutes(): int
    {
        return (int) $this->start_at->diffInMinutes($this->end_at);
    }

    public function getDurationFormatted(): string
    {
        $minutes = $this->getDurationInMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    public function isPast(): bool
    {
        return $this->end_at->isPast();
    }

    public function isOngoing(): bool
    {
        return $this->start_at->isPast() && $this->end_at->isFuture();
    }

    public function isUpcoming(): bool
    {
        return $this->start_at->isFuture();
    }

    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }

    public function addAttendee(Model $attendee, string $type = 'required'): MeetingAttendee
    {
        return $this->attendees()->create([
            'attendee_type' => get_class($attendee),
            'attendee_id' => $attendee->id,
            'type' => $type,
            'status' => 'pending',
        ]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now())
            ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_at', today());
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }
}