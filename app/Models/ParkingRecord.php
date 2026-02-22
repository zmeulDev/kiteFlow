<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ParkingRecord extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'parking_spot_id',
        'vehicle_type',
        'vehicle_id',
        'license_plate',
        'vehicle_make',
        'vehicle_model',
        'vehicle_color',
        'entry_at',
        'exit_at',
        'checked_in_by',
        'checked_out_by',
        'fee',
        'is_paid',
        'notes',
    ];

    protected $casts = [
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
        'fee' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            if (empty($record->uuid)) {
                $record->uuid = Str::uuid();
            }
            if (empty($record->entry_at)) {
                $record->entry_at = now();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parkingSpot(): BelongsTo
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    public function vehicle(): MorphTo
    {
        return $this->morphTo();
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function isActive(): bool
    {
        return $this->exit_at === null;
    }

    public function getDurationInMinutes(): int
    {
        $endTime = $this->exit_at ?? now();
        return (int) $this->entry_at->diffInMinutes($endTime);
    }

    public function getDurationFormatted(): string
    {
        $minutes = $this->getDurationInMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    public function checkOut(int $checkedOutBy, ?float $fee = null): void
    {
        $this->update([
            'exit_at' => now(),
            'checked_out_by' => $checkedOutBy,
            'fee' => $fee,
        ]);

        if ($this->parkingSpot) {
            $this->pingSpot->release();
        }
    }

    public function markAsPaid(): void
    {
        $this->update(['is_paid' => true]);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('exit_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('entry_at', today());
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}