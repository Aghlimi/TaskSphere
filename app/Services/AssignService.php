<?php

namespace App\Services;

use App\Events\InvitedToTeamEvent;
use App\Events\MemberAdded;
use App\Events\MemberRejectedEvent;
use App\Models\Assign;
use App\Models\Invitation;
use App\Models\Member;
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
            ->addSelect('assigns.role')
            ->get();
    }

    public function assign(Project $project, User $user)
    {
        $this->authorize('assign', [Assign::class, $project]);

        $inv = $project->invitable()->create(['user_id' => $user->id]);

        event(new InvitedToTeamEvent($inv));
    }

    public function accept(Invitation $inv)
    {
        $this->authorize('accept', [Assign::class, $inv]);

        DB::transaction(function () use ($inv) {
            $project = $inv->invitable;
            $inv->delete();

            $member = Member::create([
                'user_id' => auth()->id(),
                'project_id' => $project->id
            ]);

            event(new MemberAdded($member));
        });
    }

    public function reject(Invitation $inv)
    {
        $this->authorize('reject', [Assign::class, $inv]);

        event(new MemberRejectedEvent($inv->invitable, auth()->id()));

        $inv->delete();
    }

    public function delete(Project $project, User $user)
    {
        $this->authorize('delete', [Assign::class, $project, $user]);
        $project->members()
            ->where('id', '=', $user->id)
            ->delete();
    }
}
