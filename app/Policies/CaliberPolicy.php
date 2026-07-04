<?php

namespace App\Policies;

use App\Models\Caliber;
use App\Models\User;

class CaliberPolicy
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
    public function view(User $user, Caliber $caliber): bool
    {
        return $user->id === $caliber->user_id;
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
    public function update(User $user, Caliber $caliber): bool
    {
        return $user->id === $caliber->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Caliber $caliber): bool
    {
        return $user->id === $caliber->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Caliber $caliber): bool
    {
        return $user->id === $caliber->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Caliber $caliber): bool
    {
        return $user->id === $caliber->user_id;
    }
}
