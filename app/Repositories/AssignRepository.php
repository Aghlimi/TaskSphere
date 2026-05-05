<?php

namespace App\Repositories;

use App\Models\Assign;
use App\Models\Invitation;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\AssignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssignRepository implements AssignRepositoryInterface
{
    public function getTaskAssignees(Task $task): Collection
    {
        return $task->assignees()
            ->select('name', 'email')
            ->get();
    }

    public function invite(Task $task, User $user, int $senderId): Invitation
    {
        return $task->invitable()->create([
            'user_id' => $user->id,
            'sender_id' => $senderId,
        ]);
    }

    public function accept(Invitation $inv, int $userId): Assign
    {
        $task = $inv->invitable;
        $inv->delete();

        return Assign::create([
            'task_id' => $task->id,
            'user_id' => $userId,
        ]);
    }

    public function reject(Invitation $inv): void
    {
        $inv->delete();
    }
}