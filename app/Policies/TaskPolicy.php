<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;

class TaskPolicy
{
    public function __construct(private MemberRepositoryInterface $members)
    {
    }

    public function viewAny(User $user, Feature $feature): bool
    {
        return $this->members->hasMembership($user, $feature->project);
    }

    public function view(User $user, Task $task): bool
    {
        return $this->members->hasMembership($user, $task->feature->project);
    }

    public function create(User $user, Feature $feature): bool
    {
        return $this->members->hasRole($user, $feature->project, ['admin', 'owner']);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->members->hasRole($user, $task->feature->project, ['admin', 'owner']);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->members->hasRole($user, $task->feature->project, ['admin', 'owner']);
    }
}
