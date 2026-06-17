<?php

namespace App\Policies;

use App\Models\Suppressor;
use App\Models\User;

class SuppressorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Suppressor $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Suppressor $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function delete(User $user, Suppressor $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function restore(User $user, Suppressor $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function forceDelete(User $user, Suppressor $model): bool
    {
        return $user->id === $model->user_id;
    }
}
