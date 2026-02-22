<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VisitorVisit extends Model
{
    use HasFactory, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'visitor_id',
        'host_id',
        'meeting_id',
        'purpose',
        'check_in_method',
        'check_in_at',
        'check_out_at',
        'checked_in_by',
        'checked_out_by',
        'badge_number',
        'badge_type',
        'status',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $visit): void {
            if (empty($visit->uuid)) {
                $visit->uuid = Str::uuid();
            }
            if (empty($visit->check_in_at)) {
                $visit->check_in_at = now();
            }
            if (empty($visit->badge_number)) {
                $visit->badge_number = self::generateBadgeNumber();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function checkedInByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function checkOut(?int $checkedOutBy = null): void
    {
        $this->update([
            'check_out_at' => now(),
            'checked_out_by' => $checkedOutBy,
            'status' => 'checked_out',
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? $this->notes . "\nCancelled: " . $reason : $this->notes,
        ]);
    }

    public function markAsNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }

    public function getDurationInMinutes(): int
    {
        if (!$this->check_out_at) {
            return 0;
        }
        return (int) $this->check_in_at->diffInMinutes($this->check_out_at);
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

    public function isActive(): bool
    {
        return $this->status === 'checked_in' && $this->check_out_at === null;
    }

    public static function generateBadgeNumber(): string
    {
        return 'V' . now()->format('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'checked_in')->whereNull('check_out_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_at', today());
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}