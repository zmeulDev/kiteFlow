<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
class Visit extends Model
{
    protected $fillable = [
        'tenant_id', 
        'location_id',
        'check_in_token',
        'visitor_id', 
        'user_id', 
        'purpose', 
        'signature_data', 
        'photo_path',
        'scheduled_at',
        'signed_at', 
        'checked_in_at', 
        'checked_out_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'signed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Booking::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($visit) {
            $visit->booking?->delete();
        });
    }
}
