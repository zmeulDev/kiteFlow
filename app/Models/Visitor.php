<?php

namespace App\Models;

use App\Traits\TenantScoping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Visitor extends Model
{
    use HasFactory, SoftDeletes, TenantScoping;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'id_type',
        'id_number',
        'photo',
        'notes',
        'is_blacklisted',
        'blacklist_reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_blacklisted' => 'boolean',
    ];

    protected $appends = ['full_name'];

    protected static function booted(): void
    {
        static::creating(function (self $visitor): void {
            if (empty($visitor->uuid)) {
                $visitor->uuid = Str::uuid();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(VisitorVisit::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisitorDocument::class);
    }

    public function latestVisit(): HasMany
    {
        return $this->visits()->latest('check_in_at');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isCheckedIn(): bool
    {
        return $this->visits()
            ->whereNull('check_out_at')
            ->where('status', 'checked_in')
            ->exists();
    }

    public function getCurrentVisit(): ?VisitorVisit
    {
        return $this->visits()
            ->whereNull('check_out_at')
            ->where('status', 'checked_in')
            ->first();
    }

    public function blacklist(string $reason): void
    {
        $this->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $reason,
        ]);
    }

    public function unblacklist(): void
    {
        $this->update([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
        ]);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_blacklisted', false);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}