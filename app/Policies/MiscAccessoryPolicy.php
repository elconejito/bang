<?php

namespace App\Policies;

use App\Models\MiscAccessory;
use App\Models\User;

class MiscAccessoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MiscAccessory $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MiscAccessory $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function delete(User $user, MiscAccessory $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function restore(User $user, MiscAccessory $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function forceDelete(User $user, MiscAccessory $model): bool
    {
        return $user->id === $model->user_id;
    }
}
