<?php

namespace App\Services;

use App\Models\Project;
use App\Events\ProjectCreated;

class ProjectService
{
    /**
     * Get all projects.
     */
    public function all()
    {
        return Project::all();
    }

    /**
     * Get a project by ID.
     */
    public function find(int $id): ?Project
    {
        return Project::find($id);
    }

    /**
     * Create a new project.
     */
    public function create(array $data): Project
    {
        $project = Project::create($data);

        event(new ProjectCreated($project));

        return $project;
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    /**
     * Get projects for a specific user.
     */
    public function getProjectsForUser(int $userId)
    {
        return Project::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }
}
