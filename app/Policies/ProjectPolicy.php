<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, User $target): bool
    {
        return $target->id === $user->id || $user->role === "admin";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        $access = $project->members()->wherePivot('user_id', '=', $user->id)->exists();
        return $access || $user->role === "admin";
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
    public function update(User $user, Project $project): bool
    {
        $access = $project->members()
            ->wherePivot('user_id', '=', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
        return $access || $user->role === "admin";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === "admin" || $project->members()
            ->wherePivot('user_id', '=', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }
}
