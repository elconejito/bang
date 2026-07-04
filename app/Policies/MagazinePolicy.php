<?php

namespace App\Policies;

use App\Models\Magazine;
use App\Models\User;

class MagazinePolicy
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
    public function view(User $user, Magazine $magazine): bool
    {
        return $user->id === $magazine->user_id;
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
    public function update(User $user, Magazine $magazine): bool
    {
        return $user->id === $magazine->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Magazine $magazine): bool
    {
        return $user->id === $magazine->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Magazine $magazine): bool
    {
        return $user->id === $magazine->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Magazine $magazine): bool
    {
        return $user->id === $magazine->user_id;
    }
}
