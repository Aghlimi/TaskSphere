<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Events\ProjectCreated;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectService
{
    use AuthorizesRequests;

    /**
     * Get all projects for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function all(User $user): Collection
    {
        $this->authorize('viewAny', [Project::class, $user]);

        return $user->projects()->addSelect('members.role')->get();
    }

    /**
     * Find a project by ID.
     *
     * @param int $id
     * @return Project|null
     */
    public function find(Project $project): ?Project
    {
        $this->authorize('view', [Project::class, $project]);
        return $project;
    }

    /**
     * Create a new project and assign owner.
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data, $user_id): Project
    {
        $this->authorize('create', Project::class);

        $project = DB::transaction(function () use ($data, $user_id) {
            $project = Project::create($data);
            Member::create([
                'user_id' => $user_id,
                'project_id' => $project->id,
                'role' => 'owner',
            ]);
            return $project;
        });

        event(new ProjectCreated($project));
        return $project;
    }

    /**
     * Update a project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data): Project
    {
        $this->authorize("update", [Project::class, $project]);

        $project->update($data);
        return $project;
    }

    /**
     * Delete a project.
     *
     * @param Project $project
     * @return void
     */
    public function delete(Project $project): void
    {
        $this->authorize('delete', [$project]);
        $project->delete();
    }
}
