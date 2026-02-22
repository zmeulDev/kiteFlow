<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

/**
 * Trait for controllers to easily access current tenant context
 */
trait HasTenantContext
{
    /**
     * Get the current tenant from the request
     */
    protected function getCurrentTenant(): ?\App\Models\Tenant
    {
        return request()->attributes->get('tenant');
    }

    /**
     * Get the current tenant ID from the request
     */
    protected function getCurrentTenantId(): ?int
    {
        return request()->attributes->get('tenant_id');
    }

    /**
     * Validate that the current tenant is active
     */
    protected function requireActiveTenant(): ?\App\Models\Tenant
    {
        $tenant = $this->getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        if (!$tenant->isActive() && !$tenant->isOnTrial()) {
            abort(403, 'Tenant is not active');
        }

        return $tenant;
    }

    /**
     * Get all accessible tenant IDs (current + all descendants)
     */
    protected function getAccessibleTenantIds(): array
    {
        $tenantId = $this->getCurrentTenantId();

        if (!$tenantId) {
            return [];
        }

        return \App\Models\Tenant::getAccessibleTenantIds($tenantId);
    }

    /**
     * Scope a query to include only accessible tenant data
     */
    protected function scopeAccessibleByTenant($query, string $tenantColumn = 'tenant_id')
    {
        $tenantIds = $this->getAccessibleTenantIds();

        if (empty($tenantIds)) {
            return $query->whereRaw('1 = 0'); // Return no results if no tenant context
        }

        return $query->whereIn($tenantColumn, $tenantIds);
    }
}