<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function accessAdmin(User $user): bool
    {
        // Require any role other than pure viewer without access to dashboard (if applicable).
        // For now, any user that is not a plain 'viewer' might need access, but actually our roles all have some dashboard access.
        // Let's allow all authenticated users with a defined role to access the admin base.
        return in_array($user->role, ['admin', 'administrator', 'tenant', 'receptionist', 'viewer']);
    }

    public function manageUsers(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    public function manageSettings(User $user): bool
    {
        return in_array($user->role, ['admin', 'administrator']); 
    }

    public function viewVisits(User $user): bool
    {
        return $user->hasPermission('manage_visits');
    }

    public function manageCompanies(User $user): bool
    {
        return $user->hasPermission('manage_companies');
    }

    public function manageBuildings(User $user): bool
    {
        return $user->hasPermission('manage_buildings');
    }
}