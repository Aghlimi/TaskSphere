<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\Task;
use App\Events\TaskCreated;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskService
{
    use AuthorizesRequests;

    public function __construct(private TaskRepositoryInterface $tasks)
    {
    }

    public function all(Feature $feature): Collection|null
    {
        $this->authorize('viewAny', [Task::class, $feature]);

        return $this->tasks->allForFeature($feature);
    }

    public function find(int $id): ?Task
    {
        $task = $this->tasks->findById($id);
        if (!$task)
            return null;

        $this->authorize('view', $task);

        return $task;
    }

    public function create(array $data, Feature $feature): Task
    {
        $this->authorize('create', [Task::class, $feature]);

        $task = $this->tasks->create($data, $feature);

        event(new TaskCreated($task));

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $this->authorize('update', $task);

        return $this->tasks->update($task, $data);
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);
        $this->tasks->delete($task);
    }

    public function getTasksForUser(User $user): Collection
    {
        return $this->tasks->allForUser($user);
    }
}
