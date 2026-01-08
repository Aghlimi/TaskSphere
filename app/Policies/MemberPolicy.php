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
            ->where('id', '=', $user->id)
            ->exists();
    }

    public function invite(User $user, Project $project)
    {
        return $project->members()
            ->where('id', '=', $user->id)
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
        return $project->members()
            ->whereIn('id', [$user->id, $target->id])
            ->where(function ($q) use ($user, $target) {
                $q->where(function ($q) use ($user) {
                    $q->where('id', $user->id)
                        ->wherePivotIn('role', ['admin', 'owner']);
                })->orWhere(function ($q) use ($target) {
                    $q->where('id', $target->id)
                        ->wherePivotIn('role', ['admin', 'member']);
                });
            })
            ->count() === 2;
    }
}
