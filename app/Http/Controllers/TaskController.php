<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Feature;
use App\Models\Task;
use App\Services\TaskService;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index($project, Feature $feature)
    {
        $tasks = $this->taskService->all($feature);
        if (!$tasks)
            return response()->json(["message" => "Feature not found"], 404);

        return response()->json($tasks, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request, $project, Feature $feature)
    {
        $data = $request->validated();

        $task = $this->taskService->create($data, $feature);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($project, $feature, Task $task)
    {
        $task = $this->taskService->find($task->id);

        if (!$task)
            return response()->json(["message" => "Task not found"], 404);

        return response()->json($task, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskRequest $request, $project, $feature, Task $task)
    {
        $data = $request->validated();

        $task = $this->taskService->update($task, $data);

        return response()
            ->json($task, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, $project, $feature, Task $task)
    {
        $data = $request->validated();

        $task = $this->taskService->update($task, $data);

        return response()
            ->json($task, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project, $feature, Task $task)
    {
        $this->taskService->delete($task);
        return response()->json(null, 204);
    }
}
