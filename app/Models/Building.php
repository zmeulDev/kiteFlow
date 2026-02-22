<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Building extends Model
{
    use HasFactory, SoftDeletes, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'code',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'floors',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'floors' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $building): void {
            if (empty($building->uuid)) {
                $building->uuid = Str::uuid();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function accessPoints(): HasMany
    {
        return $this->hasMany(AccessPoint::class);
    }

    public function parkingSpots(): HasMany
    {
        return $this->hasMany(ParkingSpot::class);
    }

    public function meetingRooms(): HasMany
    {
        return $this->hasManyThrough(MeetingRoom::class, Zone::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        return implode(', ', $parts);
    }

    public function getFloorList(): array
    {
        return range(1, $this->floors);
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