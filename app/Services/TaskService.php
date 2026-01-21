<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\Task;
use App\Events\TaskCreated;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskService
{
    use AuthorizesRequests;

    public function all(Feature $feature): Collection|null
    {
        $this->authorize('viewAny', [Task::class, $feature]);

        return $feature->tasks()
            ->select('created_at', 'status', 'id', 'title', 'description')
            ->get();
    }

    public function find(int $id): ?Task
    {
        $task = Task::find($id)->first();
        if (!$task)
            return null;

        $this->authorize('view', $task);

        return $task;
    }

    public function create(array $data, Feature $feature): Task
    {
        $this->authorize('create', [Task::class, $feature]);

        $task = Task::create([
            ...$data,
            'feature_id' => $feature->first('id')->id
        ]);

        event(new TaskCreated($task));

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $this->authorize('update', $task);

        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);
        $task->delete();
    }

    public function getTasksForUser(User $user): Collection
    {
        return $user->tasks()
            ->select('id', 'title', 'status', 'created_at')
            ->get();
    }
}
