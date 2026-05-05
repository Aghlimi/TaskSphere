<?php

namespace App\Repositories\Contracts;

use App\Models\Feature;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function allForFeature(Feature $feature): Collection;

    public function findById(int $id): ?Task;

    public function create(array $data, Feature $feature): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): void;

    public function allForUser(User $user): Collection;

    public function getTaskAssignees(Task $task): Collection;
}