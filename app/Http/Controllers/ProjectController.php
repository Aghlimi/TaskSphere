<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponceException;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        try {
            $projects = $this->projectService->all();
            return response()->json($projects, 200);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $project = $this->projectService->create($request);
            event(new \App\Events\ProjectCreated($project));
            return response()->json($project, 201);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $project = $this->projectService->find((int) $id);
            if (!$project) {
                return response()->json(["message" => "Project not found"], 404);
            }
            return response()->json($project, 200);
        } catch (ResponceException $e) {
            return response()->json(["message" => $e->getMessage()], $e->statusCode ?: 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project)
    {
        try {
            $this->projectService->update($project, $request->all());
            return response()->json($project, 200);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        try {
            $this->projectService->update($project, $request->all());
            return response()->json($project, 200);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }
    
    public function setAsComplete($projectId)
    {
        $project = Project::findOrFail($projectId);
        try {
            $this->projectService->setAsComplete($project);
            return response()->json($project, 200);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $this->projectService->delete($project);
            return response()->json(null, 204);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }
}
