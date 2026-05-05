<?php

namespace App\Repositories\Contracts;

use App\Models\Assign;
use App\Models\Invitation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AssignRepositoryInterface
{
    public function getTaskAssignees(Task $task): Collection;

    public function invite(Task $task, User $user, int $senderId): Invitation;

    public function accept(Invitation $inv, int $userId): Assign;

    public function reject(Invitation $inv): void;
}