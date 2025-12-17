<?php

namespace App\Services;

use App\Models\Task;
use App\Events\TaskCreated;
use App\Events\CompleteTask;

class TaskService
{
    /**
     * Get all tasks.
     */
    public function all()
    {
        return Task::all();
    }

    /**
     * Get a task by ID.
     */
    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Create a new task.
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);

        event(new TaskCreated($task));

        return $task;
    }

    /**
     * Update an existing task.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    /**
     * Delete a task.
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Mark a task as complete.
     */
    public function complete(Task $task): Task
    {
        $task->update(['completed' => true, 'completed_at' => now()]);

        event(new CompleteTask($task));

        return $task;
    }

    /**
     * Get tasks for a specific feature.
     */
    public function getTasksForFeature(int $featureId)
    {
        return Task::where('feature_id', $featureId)->get();
    }

    /**
     * Get tasks assigned to a specific user.
     */
    public function getTasksForUser(int $userId)
    {
        return Task::where('assigned_to', $userId)->get();
    }
}
