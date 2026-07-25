<?php

namespace App\Policies;

use App\Models\Mount;
use App\Models\User;

class MountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Mount $mount): bool
    {
        return $user->id === $mount->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Mount $mount): bool
    {
        return $user->id === $mount->user_id;
    }

    public function delete(User $user, Mount $mount): bool
    {
        return $user->id === $mount->user_id;
    }
}
