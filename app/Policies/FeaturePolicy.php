<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class FeaturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user,$project): bool
    {

        return Member::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Feature $feature): bool
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $feature->project_id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Project $project): bool
    {
        $member = Member::where('user_id', $user->id)
            ->where('project_id', $project->id)->first();
        return $member && ($member->role === 'admin' || $member->role === 'owner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Feature $feature): bool
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $feature->project_id)
            ->where('role', 'admin')
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Feature $feature): bool
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $feature->project_id)
            ->where('role', 'admin')
            ->exists();
    }
}
