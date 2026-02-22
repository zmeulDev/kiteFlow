<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AccessPoint extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'building_id',
        'zone_id',
        'name',
        'code',
        'type',
        'direction',
        'description',
        'device_id',
        'ip_address',
        'settings',
        'requires_badge',
        'is_kiosk_mode',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'requires_badge' => 'boolean',
        'is_kiosk_mode' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    protected static function booted(): void
    {
        static::creating(function (self $accessPoint): void {
            if (empty($accessPoint->uuid)) {
                $accessPoint->uuid = Str::uuid();
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

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function isEntryPoint(): bool
    {
        return in_array($this->direction, ['entry', 'both']);
    }

    public function isExitPoint(): bool
    {
        return in_array($this->direction, ['exit', 'both']);
    }

    public function isKiosk(): bool
    {
        return $this->is_kiosk_mode || $this->type === 'kiosk';
    }

    public function logAccess(Model $subject, string $direction, string $result = 'granted', array $metadata = []): AccessLog
    {
        return $this->accessLogs()->create([
            'tenant_id' => $this->tenant_id,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'direction' => $direction,
            'accessed_at' => now(),
            'result' => $result,
            'metadata' => $metadata,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKiosks($query)
    {
        return $query->where('is_kiosk_mode', true);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}