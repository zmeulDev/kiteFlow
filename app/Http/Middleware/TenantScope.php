<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    /**
     * Handle an incoming request.
     * Ensures users can only access data belonging to their tenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Super admins can access everything
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // For tenant-scoped routes, add tenant_id to queries
        if ($user && $user->tenant_id) {
            // Add tenant_id to request for controllers to use
            $request->merge(['tenant_id' => $user->tenant_id]);
            $request->attributes->set('tenant_id', $user->tenant_id);
        }

        return $next($request);
    }
}
