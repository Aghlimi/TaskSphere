<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\AssignService;

class AssignController extends Controller
{

    public function __construct(public AssignService $assignService)
    {
    }

    public function index(Project $project,$feature, Task $task)
    {
        $assignees = $this->assignService->getTaskAssignees($project, $task);
        return response()->json($assignees, 200);
    }

    public function assign(Project $project,$feature, Task $task, User $user)
    {
        $this->assignService->assign($project, $task, $user);
        return response()->json(['message' => 'Assignment invitation sent successfully.'], 200);
    }

    public function accept(Project $project, $feature, Task $task, Invitation $invitation)
    {
        $this->assignService->accept($invitation);
        return response()->json(['message' => 'Assignment accepted successfully.'], 200);
    }

    public function reject(Project $project, $feature, Task $task , Invitation $invitation)
    {
        $this->assignService->reject($invitation);
        return response()->noContent();
    }

    public function delete(Project $project,$feature, $task, User $user)
    {
        $this->assignService->delete($project, $user);
        return response()->noContent();
    }
}
