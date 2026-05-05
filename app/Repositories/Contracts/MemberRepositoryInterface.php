<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface MemberRepositoryInterface
{
    public function showMembers(Project $project): Collection;

    public function invite(Project $project, User $user, int $senderId): Invitation;

    public function accept(Invitation $inv, int $userId): Member;

    public function reject(Invitation $inv): void;

    public function delete(Project $project, User $user): void;

    public function setAdmin(Project $project, User $user): void;

    public function removeAdmin(User $user): void;

    public function hasMembership(User $user, Project $project): bool;

    public function hasRole(User $user, Project $project, array $roles): bool;

    public function getRole(User $user, Project $project): ?string;
}