<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function accessAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist']);
    }

    public function manageUsers(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function manageSettings(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function viewVisits(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist', 'viewer']);
    }

    public function manageCompanies(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist']);
    }

    public function manageBuildings(User $user): bool
    {
        return $user->role === 'admin';
    }
}