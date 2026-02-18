<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
class Booking extends Model
{
    protected $fillable = [
        'tenant_id', 
        'meeting_room_id', 
        'visit_id', 
        'starts_at', 
        'ends_at', 
        'notes'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
