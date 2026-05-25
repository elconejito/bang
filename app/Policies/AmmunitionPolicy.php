<?php

namespace App\Policies;

use App\Models\Ammunition;
use App\Models\User;

class AmmunitionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ammunition $ammunition): bool
    {
        return $user->id === $ammunition->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ammunition $ammunition): bool
    {
        return $user->id === $ammunition->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ammunition $ammunition): bool
    {
        return $user->id === $ammunition->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ammunition $ammunition): bool
    {
        return $user->id === $ammunition->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ammunition $ammunition): bool
    {
        return $user->id === $ammunition->user_id;
    }
}
