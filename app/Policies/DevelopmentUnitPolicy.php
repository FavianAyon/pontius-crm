<?php

namespace App\Policies;

use App\Models\DevelopmentUnit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DevelopmentUnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_development_unit');
    }

    public function view(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return $user->can('view_development_unit');
    }

    public function create(User $user): bool
    {
        return $user->can('create_development_unit');
    }

    public function update(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return $user->can('update_development_unit');
    }

    public function delete(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return $user->can('delete_development_unit');
    }

    public function changeStatus(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return $user->can('change_development_unit_status');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DevelopmentUnit $developmentUnit): bool
    {
        return false;
    }
}
