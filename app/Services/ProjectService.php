<?php

namespace App\Services;

use App\Events\ErrorLogs;
use App\Exceptions\ResponceException;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use App\Events\ProjectCreated;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

// use Laravel\Telescope\AuthorizesRequests;

class ProjectService
{
    use AuthorizesRequests;
    /**
     * Get all projects.
     */
    public function all()
    {
        $this->authorize("viewAny", Project::class);

        try {
            return Project::all();
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when fetch projects",
                500,
            );
        }
    }

    /**
     * Get a project by ID.
     */
    public function find(int $id): ?Project
    {
        $project = Project::find($id);
        if (!$project) {
            return null;
        }
        $this->authorize("view", [Project::class, Project::find($id)]);

        try {
            return Project::find((int) $id);
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when fetch project",
                500,
            );
        }
    }

    /**
     * Create a new project.
     */
    public function create(Request $request): Project
    {
        $this->authorize("create", Project::class);

        $data = $request->only(
            "title",
            "description",
            "start_date",
            "completed_at",
        );

        $validatedData = validator($data, [
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start_date" => "nullable|date",
            "completed_at" => "nullable|date|after_or_equal:start_date",
        ])->validate();

        try {
            $project = Project::create([
                "title" => $validatedData["title"],
                "description" => $validatedData["description"] ?? null,
                "start_date" => $validatedData["start_date"] ?? null,
                "completed_at" => $validatedData["completed_at"] ?? null,
            ]);
            Member::create([
                "user_id" => $request->user()->id,
                "project_id" => $project->id,
                "role" => "owner",
            ]);
            event(new ProjectCreated($project));
            return $project;
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when create project",
                422,
            );
        }
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        $this->authorize("update", [Project::class, $project]);

        $validatedData = validator($data, [
            "title" => "sometimes|required|string|max:255",
            "description" => "sometimes|nullable|string",
            "start_date" => "sometimes|nullable|date",
            "completed_at" => "sometimes|nullable|date|after_or_equal:start_date",
        ])->validate();

        try {
            $project->update($validatedData);
            return $project;
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when update project",
                422,
            );
        }
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): void
    {
        $this->authorize("delete", [Project::class, $project]);
        try {
            $project->delete();
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when delete related project data",
                500,
            );
        }
    }

    /**
     * Get projects for a specific user.
     */
    public function getProjectsForUser(int $userId): array
    {
        $this->authorize("seeUserProjects", Project::class);
        try {
            $user = User::find($userId)->with("projects")->get();
            return $user->projects
                ->select(
                    "id",
                    "name",
                    "description",
                    "start_date",
                    "completed_at",
                    "created_at",
                    "updated_at",
                )
                ->toArray();
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknow problem when fetch user projects",
                500,
            );
        }
    }
    public function setAsComplete(Project $project): Project
    {
        $this->authorize("complete", [Project::class, $project]);
        try {
            $project->update([
                "status" => "completed",
                "completed_at" => now(),
            ]);
            return $project;
        } catch (\Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException("unknow problem when complete project",500);
        }
    }
}
