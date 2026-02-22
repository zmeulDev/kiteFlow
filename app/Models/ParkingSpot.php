<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ParkingSpot extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'building_id',
        'number',
        'zone',
        'type',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $spot): void {
            if (empty($spot->uuid)) {
                $spot->uuid = Str::uuid();
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

    public function parkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class);
    }

    public function currentRecord(): ?ParkingRecord
    {
        return $this->parkingRecords()
            ->whereNull('exit_at')
            ->first();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function occupy(): void
    {
        $this->update(['status' => 'occupied']);
    }

    public function release(): void
    {
        $this->update(['status' => 'available']);
    }

    public function reserve(): void
    {
        $this->update(['status' => 'reserved']);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}