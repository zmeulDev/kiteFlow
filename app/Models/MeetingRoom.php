<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MeetingRoom extends Model
{
    use HasFactory, SoftDeletes, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'building_id',
        'access_point_id',
        'name',
        'code',
        'location',
        'capacity',
        'description',
        'amenities',
        'image',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'amenities' => 'array',
        'settings' => 'array',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $room): void {
            if (empty($room->uuid)) {
                $room->uuid = Str::uuid();
            }
            if (empty($room->code)) {
                $room->code = strtoupper(Str::random(6));
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function accessPoint(): BelongsTo
    {
        return $this->belongsTo(AccessPoint::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function getUpcomingMeetings(): HasMany
    {
        return $this->meetings()
            ->where('start_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_at');
    }

    public function isAvailable(string $startAt, string $endAt, ?int $excludeMeetingId = null): bool
    {
        $query = $this->meetings()
            ->where('status', 'scheduled')
            ->where(function ($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                    ->orWhereBetween('end_at', [$startAt, $endAt])
                    ->orWhere(function ($q2) use ($startAt, $endAt) {
                        $q2->where('start_at', '<=', $startAt)
                            ->where('end_at', '>=', $endAt);
                    });
            });

        if ($excludeMeetingId) {
            $query->where('id', '!=', $excludeMeetingId);
        }

        return !$query->exists();
    }

    public function getAvailabilityForDate(string $date): array
    {
        $meetings = $this->meetings()
            ->whereDate('start_at', $date)
            ->where('status', 'scheduled')
            ->orderBy('start_at')
            ->get(['start_at', 'end_at']);

        return $meetings->map(function ($meeting) {
            return [
                'start' => $meeting->start_at->format('H:i'),
                'end' => $meeting->end_at->format('H:i'),
            ];
        })->toArray();
    }

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}