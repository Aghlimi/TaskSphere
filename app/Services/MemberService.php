<?php

namespace App\Services;

use App\Events\InvitedToTeamEvent;
use App\Events\MemberAdded;
use App\Events\MemberRejectedEvent;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class MemberService
{
    use AuthorizesRequests;

    public function showMembers(Project $project)
    {
        $this->authorize('showMembers', [Member::class, $project]);

        return $project->members()->select('name', 'email')
            ->addSelect('members.role')
            ->get();
    }

    public function invite(Project $project, User $user)
    {
        $this->authorize('invite', [Member::class, $project]);

        $inv = $project->invitable()->create(['user_id' => $user->id, 'sender_id' => auth()->id()]);

        event(new InvitedToTeamEvent($inv));
    }

    public function accept(Invitation $inv)
    {
        $this->authorize('accept', [Member::class, $inv]);

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
        $this->authorize('reject', [Member::class, $inv]);

        event(new MemberRejectedEvent($inv));

        $inv->delete();
    }

    public function delete(Project $project, User $user)
    {
        $this->authorize('delete', [Member::class, $project, $user]);
        $project->members()
            ->where('users.id', '=', $user->id)
            ->delete();
    }

    public function setAdmin(Project $project, User $user)
    {
        $this->authorize('setAdmin', [Member::class, $project, $user]);
        $project->members()
            ->where('users.id', '=', $user->id)
            ->update(['role' => 'admin']);
    }

    public function removeAdmin(Project $project, User $user)
    {
        $this->authorize('removeAdmin', [Member::class, $project]);
        if($user->id == auth()->id()) 
            return false;
        Member::where('user_id', $user->id)->delete();
        return true;
    }
    public function userRole(Project $project, User $user)
    {
        $this->authorize('userRole', [Member::class, $project]);
        $role = $project->members()->where('id','=', $user->id)->select('id')->addSelect('role')->get();
        return $role->role;
    }
}
