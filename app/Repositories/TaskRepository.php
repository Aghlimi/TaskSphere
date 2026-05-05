<?php

namespace App\Repositories;

use App\Models\Feature;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function allForFeature(Feature $feature): Collection
    {
        return $feature->tasks()
            ->select('created_at', 'status', 'id', 'title', 'description')
            ->get();
    }

    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function create(array $data, Feature $feature): Task
    {
        return Task::create([
            ...$data,
            'feature_id' => $feature->id,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function allForUser(User $user): Collection
    {
        return $user->tasks()
            ->select('id', 'title', 'status', 'created_at')
            ->get();
    }

    public function getTaskAssignees(Task $task): Collection
    {
        return $task->assignees()
            ->select('name', 'email')
            ->get();
    }
}