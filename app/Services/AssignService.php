<?php

namespace App\Services;

use App\Events\AssignmentAcceptedEvent;
use App\Events\AssignmentRejectedEvent;
use App\Events\AssignmentSendedEvent;
use App\Models\Assign;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class AssignService
{
    use AuthorizesRequests;

    public function getTaskAssignees(Project $project, Task $task)
    {
        $this->authorize('viewAssignees', [Assign::class, $project]);
        return $task->assignees()->select('name', 'email')
            ->get();
    }

    public function assign(Project $project,Task $task, User $user)
    {
        $this->authorize('assign', [Assign::class, $project]);

        $inv = $task->invitable()->create(['user_id' => $user->id, 'sender_id' => auth()->id()]);

        event(new AssignmentSendedEvent($inv));
    }

    public function accept(Invitation $inv)
    {
        $this->authorize('accept', [Assign::class, $inv]);

        DB::transaction(function () use ($inv) {
            $task = $inv->invitable;

            $inv->delete();

            $assignment = Assign::create([
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);
            
            event(new AssignmentAcceptedEvent($assignment));
        });
    }

    public function reject(Invitation $inv)
    {
        $this->authorize('reject', [Assign::class, $inv]);

        event(new AssignmentRejectedEvent($inv));
        
        $inv->delete();
    }

    public function delete(Project $project, User $user)
    {
        $this->authorize('delete', [Assign::class, $project, $user]);
        $project->members()
            ->where('users.id', '=', $user->id)
            ->delete();
    }
}
