<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Tenantable
{
    /**
     * Boot the tenantable trait
     */
    public static function bootTenantable(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = $model->tenant_id ?? Auth::user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = Auth::user();
            
            if ($user && !$user->isSuperAdmin()) {
                $builder->where('tenant_id', $user->tenant_id);
            }
        });
    }

    /**
     * Get the tenant that owns this model
     */
    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope to bypass tenant scope for super admins
     */
    public function scopeWithoutTenant(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('tenant');
    }

    /**
     * Force set tenant ID
     */
    public function forceTenant(int $tenantId): self
    {
        $this->tenant_id = $tenantId;
        return $this;
    }
}
