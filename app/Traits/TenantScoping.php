<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait for models that belong to a tenant and should be scoped by tenant
 */
trait TenantScoping
{
    /**
     * Boot the trait
     */
    protected static function bootTenantScoping(): void
    {
        // Add global scope to automatically filter by current tenant
        static::addGlobalScope('tenant', function (Builder $query) {
            if (self::shouldApplyTenantScope()) {
                $tenantId = self::getCurrentTenantId();
                if ($tenantId) {
                    $query->whereHasAccessibleTenant($tenantId);
                }
            }
        });
    }

    /**
     * Determine if tenant scope should be applied
     */
    protected static function shouldApplyTenantScope(): bool
    {
        // Don't apply scope if explicitly disabled
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return false; // Allow seeder/migration to run without scope
        }

        // Don't apply for super-admin
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return false;
        }

        // Check if scope is explicitly disabled via request
        if (request()->has('disable_tenant_scope')) {
            return false;
        }

        return true;
    }

    /**
     * Get the current tenant ID from context
     */
    protected static function getCurrentTenantId(): ?int
    {
        // Try to get from tenant context set by middleware (from route parameter)
        if (request()->attributes->has('tenant_id')) {
            return request()->attributes->get('tenant_id');
        }

        // Try to get from authenticated user
        if (auth()->check()) {
            $tenant = auth()->user()->getCurrentTenant();
            return $tenant?->id;
        }

        // Try to get from tenant header (for mobile app/API)
        $tenantDomain = request()->header('X-Tenant-Domain');
        if ($tenantDomain) {
            $tenant = \App\Models\Tenant::where('domain', $tenantDomain)->first();
            return $tenant?->id;
        }

        $tenantId = request()->header('X-Tenant-ID');
        if ($tenantId) {
            return (int) $tenantId;
        }

        return null;
    }

    /**
     * Scope a query to only include records accessible by the given tenant
     * Includes the tenant's own data and all sub-tenant data
     */
    public function scopeWhereHasAccessibleTenant(Builder $query, int $tenantId): Builder
    {
        // Get the tenant and all its descendants
        $tenantIds = self::getAccessibleTenantIds($tenantId);

        return $query->whereIn('tenant_id', $tenantIds);
    }

    /**
     * Scope a query to a specific tenant (no hierarchy)
     */
    public function scopeByTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get all accessible tenant IDs (self + all descendants)
     */
    protected static function getAccessibleTenantIds(int $tenantId): array
    {
        $tenant = \App\Models\Tenant::with('descendants')->find($tenantId);

        if (!$tenant) {
            return [$tenantId];
        }

        // Get self + all descendant IDs
        $ids = [$tenant->id];
        if ($tenant->descendants) {
            $ids = array_merge($ids, $tenant->descendants->pluck('id')->toArray());
        }

        return $ids;
    }
}