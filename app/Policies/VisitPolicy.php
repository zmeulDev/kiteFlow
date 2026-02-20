<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Auth\Access\Response;

class VisitPolicy
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
        return $user->hasPermissionTo('view visits');
    }

    public function view(User $user, Visit $visit): bool
    {
        if (!$user->hasPermissionTo('view visits')) return false;
        if ($user->sub_tenant_id && $visit->sub_tenant_id !== $user->sub_tenant_id) return false;
        
        return $user->tenant_id === $visit->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage visits');
    }

    public function update(User $user, Visit $visit): bool
    {
        if (!$user->hasPermissionTo('manage visits')) return false;
        if ($user->sub_tenant_id && $visit->sub_tenant_id !== $user->sub_tenant_id) return false;
        
        return $user->tenant_id === $visit->tenant_id;
    }

    public function delete(User $user, Visit $visit): bool
    {
        if (!$user->hasPermissionTo('manage visits')) return false;
        if ($user->sub_tenant_id && $visit->sub_tenant_id !== $user->sub_tenant_id) return false;
        
        return $user->tenant_id === $visit->tenant_id;
    }
}
