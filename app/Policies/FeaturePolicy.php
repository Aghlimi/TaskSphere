<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;

class FeaturePolicy
{
    public function __construct(private MemberRepositoryInterface $members)
    {
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user,$project): bool
    {
        return $this->members->hasMembership($user, $project);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Feature $feature): bool
    {
        return $this->members->hasMembership($user, $feature->project);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Project $project): bool
    {
        return $this->members->hasRole($user, $project, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Feature $feature): bool
    {
        return $this->members->hasRole($user, $feature->project, ['admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Feature $feature): bool
    {
        return $this->members->hasRole($user, $feature->project, ['admin']);
    }
}
