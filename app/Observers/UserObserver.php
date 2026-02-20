<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->role && class_exists(\Spatie\Permission\Models\Role::class)) {
            $user->assignRole($user->role);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('role') && class_exists(\Spatie\Permission\Models\Role::class)) {
            $user->syncRoles([$user->role]);
        }
    }
}
