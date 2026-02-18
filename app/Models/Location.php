<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
class Location extends Model
{
    protected $fillable = ['tenant_id', 'name', 'slug', 'address'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function meetingRooms(): HasMany
    {
        return $this->hasMany(MeetingRoom::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($location) {
            $location->meetingRooms->each->delete();
            $location->visits->each->delete();
        });
    }
}
