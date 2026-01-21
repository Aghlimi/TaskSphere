<?php

namespace App\Http\Controllers;

use App\Events\ProjectCreated;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $user = auth()->user();

        $projects = $this->projectService->all($user);

        return response()->json($projects, 200);
    }

    public function store(ProjectRequest $request)
    {

        $data = $request->validated();

        $project = $this->projectService->create($data, auth()->id());

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $project = $this->projectService->find($project);
        return response()->json($project, 200);
    }

    public function edit(ProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $this->projectService->update($project, $data);

        return response()->json($project, 200);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $project = $this->projectService->update($project, $data);

        return response()
            ->json($project, 200);
    }

    public function destroy(Project $project)
    {
        $this->projectService->delete($project);
        return response()->json(null, 204);
    }
}
