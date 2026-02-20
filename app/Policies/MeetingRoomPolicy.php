<?php

namespace App\Policies;

use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MeetingRoomPolicy
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
        return $user->hasPermissionTo('view meeting_rooms');
    }

    public function view(User $user, MeetingRoom $meetingRoom): bool
    {
        if (!$user->hasPermissionTo('view meeting_rooms')) return false;
        return $user->tenant_id === $meetingRoom->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage meeting_rooms');
    }

    public function update(User $user, MeetingRoom $meetingRoom): bool
    {
        if (!$user->hasPermissionTo('manage meeting_rooms')) return false;
        return $user->tenant_id === $meetingRoom->tenant_id;
    }

    public function delete(User $user, MeetingRoom $meetingRoom): bool
    {
        if (!$user->hasPermissionTo('manage meeting_rooms')) return false;
        return $user->tenant_id === $meetingRoom->tenant_id;
    }
}
