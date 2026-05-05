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
use App\Repositories\Contracts\AssignRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class AssignService
{
    use AuthorizesRequests;

    public function __construct(
        private AssignRepositoryInterface $assigns,
        private MemberRepositoryInterface $members
    ) {
    }

    public function getTaskAssignees(Project $project, Task $task)
    {
        $this->authorize('viewAssignees', [Assign::class, $project]);
        return $this->assigns->getTaskAssignees($task);
    }

    public function assign(Project $project,Task $task, User $user)
    {
        $this->authorize('assign', [Assign::class, $project]);

        $inv = $this->assigns->invite($task, $user, auth()->id());

        event(new AssignmentSendedEvent($inv));
    }

    public function accept(Invitation $inv)
    {
        $this->authorize('accept', [Assign::class, $inv]);

        DB::transaction(function () use ($inv) {
            $assignment = $this->assigns->accept($inv, auth()->id());
            
            event(new AssignmentAcceptedEvent($assignment));
        });
    }

    public function reject(Invitation $inv)
    {
        $this->authorize('reject', [Assign::class, $inv]);

        event(new AssignmentRejectedEvent($inv));
        
        $this->assigns->reject($inv);
    }

    public function delete(Project $project, User $user)
    {
        $this->authorize('delete', [Assign::class, $project, $user]);
        $this->members->delete($project, $user);
    }
}
