<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;

class ProjectPolicy
{
    public function __construct(private MemberRepositoryInterface $members)
    {
    }

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
        $access = $this->members->hasMembership($user, $project);
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
        $access = $this->members->hasRole($user, $project, ['owner']);
        return $access || $user->role === "admin";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === "admin" || $this->members->hasRole($user, $project, ['owner']);
    }
}
