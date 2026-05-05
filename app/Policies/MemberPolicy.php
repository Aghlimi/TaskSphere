<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;

class MemberPolicy
{
    public function __construct(private MemberRepositoryInterface $members)
    {
    }

    public function showMembers(User $user, Project $project)
    {
        return $this->members->hasMembership($user, $project);
    }

    public function userRole(User $user, Project $project)
    {
        return $this->members->hasMembership($user, $project);
    }

    public function invite(User $user, Project $project)
    {
        return $this->members->hasRole($user, $project, ['admin', 'owner']);
    }

    public function accept(User $user, Invitation $inv)
    {
        return $inv->user_id == $user->id;
    }

    public function reject(User $user, Invitation $inv)
    {
        return $this->accept($user, $inv);
    }

    public function delete(User $user, Project $project, User $target)
    {
        if ($target->id == $user->id)
            return true;

        return $this->members->hasRole($user, $project, ['admin', 'owner'])
            && $this->members->hasMembership($target, $project);
    }

    public function setAdmin(User $user, Project $project)
    {
        return $this->members->hasRole($user, $project, ['owner']);
    }

    public function removeAdmin(User $user, Project $project)
    {
        return $this->members->hasRole($user, $project, ['owner']);
    }
}
