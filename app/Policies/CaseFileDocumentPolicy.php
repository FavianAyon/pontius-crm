<?php

namespace App\Policies;

use App\Models\CaseFileDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CaseFileDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_case_file_document');
    }

    public function view(User $user, CaseFileDocument $document): bool
    {
        return $user->can('view_case_file_document');
    }

    public function create(User $user): bool
    {
        return $user->can('create_case_file_document');
    }

    public function update(User $user, CaseFileDocument $document): bool
    {
        return $user->can('update_case_file_document');
    }

    public function delete(User $user, CaseFileDocument $document): bool
    {
        return $user->can('delete_case_file_document');
    }

    public function approve(User $user, CaseFileDocument $document): bool
    {
        return $user->can('approve_case_file_document');
    }

    public function reject(User $user, CaseFileDocument $document): bool
    {
        return $user->can('reject_case_file_document');
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseFileDocument $caseFileDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseFileDocument $caseFileDocument): bool
    {
        return false;
    }
}
