<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_lead');
    }

    public function view(User $user, Lead $lead): bool
    {
        if ($user->can('view_all_leads')) {
            return true;
        }

        return $user->can('view_lead')
            && $lead->assigned_to_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_lead');
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->can('view_all_leads')) {
            return $user->can('update_lead');
        }

        return $user->can('update_lead')
            && $lead->assigned_to_user_id === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('delete_lead');
    }

    public function assign(User $user): bool
    {
        return $user->can('assign_lead');
    }
}
