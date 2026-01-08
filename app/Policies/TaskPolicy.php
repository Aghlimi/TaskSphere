<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    public function viewAny(User $user, Feature $feature): bool
    {
        return $feature->project
            ->members()
            ->wherePivot('user_id', $user->id)
            ->exists();
    }

    public function view(User $user, Task $task): bool
    {
        return
            $task->feature
                ->project
                ->members()
                ->wherePivot('user_id', $user->id)
                ->exists();
    }

    public function create(User $user, Feature $feature): bool
    {
        return $feature->project->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
    }

    public function update(User $user, Task $task): bool
    {
        return $task->feature->project->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->feature->project->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['admin', 'owner'])
            ->exists();
    }
}
