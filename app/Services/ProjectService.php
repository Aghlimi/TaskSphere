<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Events\ProjectCreated;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectService
{
    use AuthorizesRequests;

    public function __construct(private ProjectRepositoryInterface $projects)
    {
    }

    /**
     * Get all projects for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function all(User $user): Collection
    {
        $this->authorize('viewAny', [Project::class, $user]);

        return $this->projects->allForUser($user);
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

        $project = $this->projects->create($data, $user_id);

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

        return $this->projects->update($project, $data);
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
        $this->projects->delete($project);
    }
}
