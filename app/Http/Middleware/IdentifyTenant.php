<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // If it's a normal user or impersonation is active, ensure tenant_id is in session
            if (!$user->is_super_admin || session()->has('impersonator_id')) {
                if (!session()->has('tenant_id')) {
                    session()->put('tenant_id', $user->tenant_id);
                }

                $tenantId = session()->get('tenant_id');
                
                // For Hub Owners: provide access to child tenants
                // We cache this in the session to avoid repeated queries
                if (!session()->has('accessible_tenant_ids') || session()->get('tenant_id_for_accessible') !== $tenantId) {
                    $tenant = \App\Models\Tenant::find($tenantId);
                    if ($tenant && $tenant->is_hub) {
                        $childIds = $tenant->children()->pluck('id')->toArray();
                        $accessibleIds = array_merge([$tenantId], $childIds);
                        session()->put('accessible_tenant_ids', $accessibleIds);
                    } else {
                        session()->put('accessible_tenant_ids', [$tenantId]);
                    }
                    session()->put('tenant_id_for_accessible', $tenantId);
                }
            } else {
                // Super Admin not impersonating
                session()->forget(['tenant_id', 'accessible_tenant_ids', 'tenant_id_for_accessible']);
            }
        }

        return $next($request);
    }
}
