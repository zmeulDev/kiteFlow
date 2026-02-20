<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TenantPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view tenants');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('view tenants')) return true;
        return $user->tenant_id === $tenant->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage tenants');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        if ($user->hasPermissionTo('manage tenants')) return true;
        return $user->tenant_id === $tenant->id && $user->hasRole('tenant_admin');
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->hasPermissionTo('manage tenants'); // only SA
    }
}
