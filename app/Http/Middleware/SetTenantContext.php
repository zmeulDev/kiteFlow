<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set the current tenant context for all requests.
 *
 * This middleware:
 * - Extracts tenant from authenticated user's tenant relationship
 * - Handles super-admin users who can access any tenant
 * - Handles tenant header for API requests from mobile app
 * - Sets current tenant in request context for all controllers
 */
class SetTenantContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;
        $routeTenant = null;

        // 1. Check if there's a tenant route parameter (for tenant-scoped routes)
        // We need to check this BEFORE route model binding completes
        $routeParams = $request->route()?->parameters() ?? [];
        if (isset($routeParams['tenant'])) {
            $routeTenantParam = $routeParams['tenant'];
            // It could be a string (slug) or a Tenant model
            if (is_string($routeTenantParam)) {
                // It's a slug, find the tenant
                $routeTenant = \App\Models\Tenant::where('slug', $routeTenantParam)->first();
                if ($routeTenant) {
                    $tenantId = $routeTenant->id;
                }
            } elseif ($routeTenantParam instanceof \App\Models\Tenant) {
                // Already resolved to a Tenant model
                $routeTenant = $routeTenantParam;
                $tenantId = $routeTenant->id;
            }
        }

        // 2. If no tenant from route, try to get from authenticated user
        if (!$tenantId && Auth::check()) {
            $user = Auth::user();

            // Super-admin can access any tenant (or no tenant context)
            if ($user->isSuperAdmin()) {
                // For super-admin, we allow switching tenants via header
                $tenantId = $this->getTenantFromHeader($request);
            } else {
                // Regular users are scoped to their tenant
                $tenant = $user->getCurrentTenant();
                $tenantId = $tenant?->id;
            }
        }

        // 3. If still no tenant, try to get from header (for API requests)
        if (!$tenantId) {
            $tenantId = $this->getTenantFromHeader($request);
        }

        // 4. If still no tenant, try to get from domain (for web requests)
        if (!$tenantId) {
            $tenantId = $this->getTenantFromDomain($request);
        }

        // 5. Set tenant context
        if ($tenantId) {
            // If we have a route tenant, validate access; otherwise just set context
            if ($routeTenant) {
                $this->validateAndSetTenant($request, $tenantId, $routeTenant);
            } else {
                $this->setTenantContext($request, $tenantId);
            }
        }

        return $next($request);
    }

    /**
     * Get tenant from request headers
     */
    protected function getTenantFromHeader(Request $request): ?int
    {
        // Try X-Tenant-ID header
        $tenantId = $request->header('X-Tenant-ID');
        if ($tenantId) {
            return (int) $tenantId;
        }

        // Try X-Tenant-Domain header
        $tenantDomain = $request->header('X-Tenant-Domain');
        if ($tenantDomain) {
            $tenant = \App\Models\Tenant::where('domain', $tenantDomain)->first();
            return $tenant?->id;
        }

        return null;
    }

    /**
     * Get tenant from request domain (for subdomain-based routing)
     */
    protected function getTenantFromDomain(Request $request): ?int
    {
        $host = $request->getHost();

        // Extract subdomain if using subdomain-based tenant routing
        // Example: tenant.kiteflow.com -> tenant
        if (str_contains($host, '.')) {
            $subdomain = explode('.', $host)[0];

            // Skip common subdomains
            $commonSubdomains = ['www', 'api', 'app', 'admin', 'staging'];
            if (!in_array($subdomain, $commonSubdomains)) {
                $tenant = \App\Models\Tenant::where('slug', $subdomain)->first();
                return $tenant?->id;
            }
        }

        return null;
    }

    /**
     * Set tenant context in request (without validation)
     */
    protected function setTenantContext(Request $request, int $tenantId): void
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            return;
        }

        // Set tenant in request context
        $request->attributes->set('tenant_id', $tenantId);
        $request->attributes->set('tenant', $tenant);

        // Also set in app() helper for easy access
        app()->instance('current_tenant', $tenant);
        app()->instance('current_tenant_id', $tenantId);
    }

    /**
     * Validate user has access to requested tenant and set context
     */
    protected function validateAndSetTenant(Request $request, int $tenantId, ?\App\Models\Tenant $routeTenant = null): void
    {
        $tenant = $routeTenant ?? \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Check if user has access to this tenant
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $user = Auth::user();

            // Check if user belongs to this tenant OR belongs to a parent tenant
            // (business logic: tenant can see their own data and their sub-tenant data)
            $userTenantIds = $user->tenants()->pluck('tenants.id')->toArray();
            $accessibleTenantIds = [];

            // Get all tenant IDs that the user has access to (their own tenants + descendants)
            foreach ($userTenantIds as $userTenantId) {
                $accessibleTenantIds = array_merge(
                    $accessibleTenantIds,
                    \App\Models\Tenant::getAccessibleTenantIds($userTenantId)
                );
            }
            $accessibleTenantIds = array_unique($accessibleTenantIds);

            if (!in_array($tenantId, $accessibleTenantIds)) {
                abort(403, 'You do not have access to this tenant');
            }
        }

        // Set tenant in request context
        $request->attributes->set('tenant_id', $tenantId);
        $request->attributes->set('tenant', $tenant);

        // Also set in app() helper for easy access
        app()->instance('current_tenant', $tenant);
        app()->instance('current_tenant_id', $tenantId);
    }

    /**
     * Helper to get the current tenant ID from anywhere in the app
     */
    public static function getCurrentTenantId(): ?int
    {
        if (app()->bound('current_tenant_id')) {
            return app('current_tenant_id');
        }

        if (request()->has('tenant_id')) {
            return (int) request()->get('tenant_id');
        }

        return null;
    }

    /**
     * Helper to get the current tenant from anywhere in the app
     */
    public static function getCurrentTenant(): ?\App\Models\Tenant
    {
        if (app()->bound('current_tenant')) {
            return app('current_tenant');
        }

        $tenantId = self::getCurrentTenantId();
        if ($tenantId) {
            return \App\Models\Tenant::find($tenantId);
        }

        return null;
    }
}