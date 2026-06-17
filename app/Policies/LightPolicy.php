<?php

namespace App\Policies;

use App\Models\Light;
use App\Models\User;

class LightPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Light $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Light $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function delete(User $user, Light $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function restore(User $user, Light $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function forceDelete(User $user, Light $model): bool
    {
        return $user->id === $model->user_id;
    }
}
