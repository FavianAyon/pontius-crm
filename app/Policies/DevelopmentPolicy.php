<?php

namespace App\Policies;

use App\Models\Development;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DevelopmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_development');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Development $development): bool
    {
        return $user->can('view_development');    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_development');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Development $development): bool
    {
        return $user->can('update_development');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Development $development): bool
    {
        return $user->can('delete_development');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Development $development): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Development $development): bool
    {
        return false;
    }


}
