<?php

namespace App\Policies;

use App\Models\SessionLine;
use App\Models\User;

class SessionLinePolicy
{
    public function view(User $user, SessionLine $sessionLine): bool
    {
        return $user->id === $sessionLine->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SessionLine $sessionLine): bool
    {
        return $user->id === $sessionLine->user_id;
    }

    public function delete(User $user, SessionLine $sessionLine): bool
    {
        return $user->id === $sessionLine->user_id;
    }
}
