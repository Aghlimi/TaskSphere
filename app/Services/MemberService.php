<?php

namespace App\Services;

use App\Events\InvitedToTeamEvent;
use App\Events\MemberAdded;
use App\Events\MemberRejectedEvent;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class MemberService
{
    use AuthorizesRequests;

    public function __construct(private MemberRepositoryInterface $members)
    {
    }

    public function showMembers(Project $project)
    {
        $this->authorize('showMembers', [Member::class, $project]);

        return $this->members->showMembers($project);
    }

    public function invite(Project $project, User $user)
    {
        $this->authorize('invite', [Member::class, $project]);

        $inv = $this->members->invite($project, $user, auth()->id());

        event(new InvitedToTeamEvent($inv));
    }

    public function accept(Invitation $inv)
    {
        $this->authorize('accept', [Member::class, $inv]);

        DB::transaction(function () use ($inv) {
            $member = $this->members->accept($inv, auth()->id());

            event(new MemberAdded($member));
        });
    }

    public function reject(Invitation $inv)
    {
        $this->authorize('reject', [Member::class, $inv]);

        event(new MemberRejectedEvent($inv));

        $this->members->reject($inv);
    }

    public function delete(Project $project, User $user)
    {
        $this->authorize('delete', [Member::class, $project, $user]);
        $this->members->delete($project, $user);
    }

    public function setAdmin(Project $project, User $user)
    {
        $this->authorize('setAdmin', [Member::class, $project, $user]);
        $this->members->setAdmin($project, $user);
    }

    public function removeAdmin(Project $project, User $user)
    {
        $this->authorize('removeAdmin', [Member::class, $project]);
        if($user->id == auth()->id()) 
            return false;
        $this->members->removeAdmin($user);
        return true;
    }
    public function userRole(Project $project, User $user)
    {
        $this->authorize('userRole', [Member::class, $project]);
        return $this->members->getRole($user, $project);
    }
}
