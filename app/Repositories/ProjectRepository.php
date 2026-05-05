<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function allForUser(User $user): Collection
    {
        return $user->projects()->addSelect('members.role')->get();
    }

    public function create(array $data, int $userId): Project
    {
        return DB::transaction(function () use ($data, $userId) {
            $project = Project::create($data);

            Member::create([
                'user_id' => $userId,
                'project_id' => $project->id,
                'role' => 'owner',
            ]);

            return $project;
        });
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}