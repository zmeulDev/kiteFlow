<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Support both pipe (|) and comma (,) as separators
        $roleList = preg_split('/[|,]/', $roles);

        // Use Spatie's hasAnyRole() method
        if ($request->user()->hasAnyRole($roleList)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}