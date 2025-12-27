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
    public function viewAny(User $user): bool
    {
        return $user->role === "admin";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        $access = Member::where("user_id", $user->id)
            ->where("project_id", $project->id)
            ->first();
        return !!$access || $user->role === "admin";
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
        $access = Member::where("user_id", $user->id)
            ->where("project_id", $project->id)
            ->where("role", "owner")
            ->first();
        return !!$access || $user->role === "admin";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        $access = Member::where("user_id", $user->id)
            ->where("project_id", $project->id)
            ->where("role", "owner")
            ->first();
        return !!$access || $user->role === "admin";
    }
    
    public function seeUserProjects(User $user,User $target): bool
    {
        return $target->id === $user->id || $user->role === "admin";
    }
}
