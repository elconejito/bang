<?php

namespace App\Policies;

use App\Models\Optic;
use App\Models\User;

class OpticPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Optic $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Optic $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function delete(User $user, Optic $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function restore(User $user, Optic $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function forceDelete(User $user, Optic $model): bool
    {
        return $user->id === $model->user_id;
    }
}
