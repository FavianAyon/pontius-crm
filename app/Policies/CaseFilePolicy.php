<?php

namespace App\Policies;

use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CaseFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_case_file');
    }

    public function view(User $user, CaseFile $caseFile): bool
    {
        if ($user->can('view_all_leads')) {
            return $user->can('view_case_file');
        }

        return $user->can('view_case_file')
            && $caseFile->assigned_to_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_case_file');
    }

    public function update(User $user, CaseFile $caseFile): bool
    {
        if ($user->can('view_all_leads')) {
            return $user->can('update_case_file');
        }

        return $user->can('update_case_file')
            && $caseFile->assigned_to_user_id === $user->id;
    }

    public function delete(User $user, CaseFile $caseFile): bool
    {
        return $user->can('delete_case_file');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseFile $caseFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseFile $caseFile): bool
    {
        return false;
    }
}
