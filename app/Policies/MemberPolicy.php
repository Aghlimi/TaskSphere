<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\Project;
use App\Models\User;

class MemberPolicy
{
    public function showMembers(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->exists();
    }

    public function userRole(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->exists();
    }

    public function invite(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
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
        $access = $project->members()->whereIn('users.id', [$user->id, $target->id])
            ->whereIn('members.role', ['admin', 'owner'])
            ->orWhere(function ($q) use ($target) {
                $q->where('users.id', '=', $target->id)
                    ->where('members.role', '=', 'member');
            })
            ->count();

        return $access === 2;
    }

    public function setAdmin(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function removeAdmin(User $user, Project $project)
    {
        return $project->members()->where('users.id', '=', $user->id)
            ->wherePivot('role', '=', 'owner')->exists();
    }
}
