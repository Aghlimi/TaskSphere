<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    public function allForUser(User $user): Collection;

    public function create(array $data, int $userId): Project;

    public function update(Project $project, array $data): Project;

    public function delete(Project $project): void;
}