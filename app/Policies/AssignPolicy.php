<?php

namespace App\Policies;

use App\Models\Assign;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Project;

class AssignPolicy
{
    public function viewAssignees(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->exists();
    }

    public function assign(User $user, Project $project)
    {
        return $project->members()
            ->where('users.id', '=', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
    }

    public function unassign(User $user, Project $project)
    {
        return $this->assign($user, $project);
    }

    public function viewAssignments(User $user, Project $project)
    {
        return $project->members()
            ->where('id', '=', $user->id)
            ->exists();
    }

    public function accept(User $user, Invitation $inv)
    {
        return $user->id === $inv->user_id;
    }

    public function reject(User $user, Invitation $inv)
    {
        return $this->accept($user, $inv);
    }

    public function delete(User $user, Project $project, User $target)
    {
        return $user->id === $target->id || $project->members()
            ->where('users.id', '=', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
    }
}
