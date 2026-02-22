<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Zone extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'building_id',
        'name',
        'code',
        'type',
        'floor',
        'description',
        'access_rules',
        'requires_authorization',
        'is_active',
    ];

    protected $casts = [
        'access_rules' => 'array',
        'requires_authorization' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $zone): void {
            if (empty($zone->uuid)) {
                $zone->uuid = Str::uuid();
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

    public function accessPoints(): HasMany
    {
        return $this->hasMany(AccessPoint::class);
    }

    public function hasAccessRule(string $rule): bool
    {
        return in_array($rule, $this->access_rules ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}