<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
class Visitor extends Model
{
    protected $fillable = ['tenant_id', 'first_name', 'last_name', 'email', 'phone', 'is_flagged', 'is_vip', 'internal_notes'];

    protected $casts = [
        'is_flagged' => 'boolean',
        'is_vip' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($visitor) {
            $visitor->visits->each->delete();
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
