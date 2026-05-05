<?php

namespace App\Repositories;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MemberRepository implements MemberRepositoryInterface
{
    public function showMembers(Project $project): Collection
    {
        return $project->members()
            ->select('name', 'email')
            ->addSelect('members.role')
            ->get();
    }

    public function invite(Project $project, User $user, int $senderId): Invitation
    {
        return $project->invitable()->create([
            'user_id' => $user->id,
            'sender_id' => $senderId,
        ]);
    }

    public function accept(Invitation $inv, int $userId): Member
    {
        $project = $inv->invitable;
        $inv->delete();

        return Member::create([
            'user_id' => $userId,
            'project_id' => $project->id,
        ]);
    }

    public function reject(Invitation $inv): void
    {
        $inv->delete();
    }

    public function delete(Project $project, User $user): void
    {
        $project->members()
            ->where('users.id', '=', $user->id)
            ->delete();
    }

    public function setAdmin(Project $project, User $user): void
    {
        $project->members()
            ->where('users.id', '=', $user->id)
            ->update(['role' => 'admin']);
    }

    public function removeAdmin(User $user): void
    {
        Member::where('user_id', $user->id)->delete();
    }

    public function hasMembership(User $user, Project $project): bool
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->exists();
    }

    public function hasRole(User $user, Project $project, array $roles): bool
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->whereIn('role', $roles)
            ->exists();
    }

    public function getRole(User $user, Project $project): ?string
    {
        return Member::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->value('role');
    }
}