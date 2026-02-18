<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
class MeetingRoom extends Model
{
    protected $fillable = ['tenant_id', 'location_id', 'name', 'capacity', 'amenities', 'is_active'];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($meetingRoom) {
            $meetingRoom->bookings->each->delete();
        });
    }
}
