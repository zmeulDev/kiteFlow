<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class AccessLog extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'access_point_id',
        'subject_type',
        'subject_id',
        'direction',
        'accessed_at',
        'result',
        'denial_reason',
        'metadata',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if (empty($log->uuid)) {
                $log->uuid = Str::uuid();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function accessPoint(): BelongsTo
    {
        return $this->belongsTo(AccessPoint::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function isEntry(): bool
    {
        return $this->direction === 'entry';
    }

    public function isExit(): bool
    {
        return $this->direction === 'exit';
    }

    public function isGranted(): bool
    {
        return $this->result === 'granted';
    }

    public function isDenied(): bool
    {
        return $this->result === 'denied';
    }

    public function scopeToday($query)
    {
        return $query->whereDate('accessed_at', today());
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeGranted($query)
    {
        return $query->where('result', 'granted');
    }

    public function scopeDenied($query)
    {
        return $query->where('result', 'denied');
    }

    public function scopeEntries($query)
    {
        return $query->where('direction', 'entry');
    }

    public function scopeExits($query)
    {
        return $query->where('direction', 'exit');
    }
}