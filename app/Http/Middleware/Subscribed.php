<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class Subscribed
{
    /**
     * Handle an incoming request.
     * Check if the Tenant is actively subscribed via Stripe Cashier.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->route('tenant');

        // Allow 'demo' and 'system' tenants to bypass for testing/super-admin purposes
        if ($tenant && in_array($tenant->domain, ['demo', 'system'])) {
            return $next($request);
        }

        if ($tenant && !$tenant->subscribed('default')) {
            // If they are not subscribed, redirect to billing portal route (or abort if API)
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Subscription Required.'], 402);
            }
            
            return redirect()->route('tenant.billing', ['tenant' => $tenant->id]);
        }

        return $next($request);
    }
}
