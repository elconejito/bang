<?php

namespace App\Policies;

use App\Models\Firearm;
use App\Models\User;

class FirearmPolicy
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
    public function view(User $user, Firearm $firearm): bool
    {
        return $user->id === $firearm->user_id;
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
    public function update(User $user, Firearm $firearm): bool
    {
        return $user->id === $firearm->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Firearm $firearm): bool
    {
        return $user->id === $firearm->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Firearm $firearm): bool
    {
        return $user->id === $firearm->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Firearm $firearm): bool
    {
        return $user->id === $firearm->user_id;
    }
}
