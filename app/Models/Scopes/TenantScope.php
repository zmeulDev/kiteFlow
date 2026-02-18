<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    private static $isApplying = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // NEVER apply scope in console to allow seeders/commands to work,
        // EXCEPT when running tests where we want to verify isolation.
        if ((app()->runningInConsole() && !app()->environment('testing')) || self::$isApplying) {
            return;
        }

        self::$isApplying = true;

        try {
            // Bypass for Super Admins ONLY if they are NOT impersonating
            if (Auth::hasUser() && Auth::user()->is_super_admin && !Session::has('impersonator_id')) {
                // If a specific tenant is selected in session (filtering), we apply the scope
                if (Session::has('tenant_id')) {
                    $builder->where($model->getTable() . '.tenant_id', Session::get('tenant_id'));
                }
                return;
            }

            // Apply tenant isolation for authenticated users
            if (Auth::hasUser()) {
                if (Session::has('accessible_tenant_ids')) {
                    $builder->whereIn($model->getTable() . '.tenant_id', Session::get('accessible_tenant_ids'));
                } elseif (Session::has('tenant_id')) {
                    $builder->where($model->getTable() . '.tenant_id', Session::get('tenant_id'));
                } else {
                    // Default to the user's own tenant if no session override exists
                    $builder->where($model->getTable() . '.tenant_id', Auth::user()->tenant_id);
                }
            } elseif (Session::has('tenant_id')) {
                // For unauthenticated sessions (like Kiosk or Tests), apply if tenant_id is set
                $builder->where($model->getTable() . '.tenant_id', Session::get('tenant_id'));
            }
        } finally {
            self::$isApplying = false;
        }
    }
}
