<?php

namespace App\Repositories\Contracts;

use App\Models\Feature;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

interface FeatureRepositoryInterface
{
    public function allForProject(Project $project): Collection;

    public function create(array $data, Project $project): Feature;

    public function update(Feature $feature, array $data): Feature;

    public function delete(Feature $feature): void;
}