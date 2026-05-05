<?php

namespace App\Repositories;

use App\Models\Feature;
use App\Models\Project;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FeatureRepository implements FeatureRepositoryInterface
{
    public function allForProject(Project $project): Collection
    {
        return $project->features()->get();
    }

    public function create(array $data, Project $project): Feature
    {
        $feature = new Feature($data);
        $feature->project_id = $project->id;
        $feature->save();

        return $feature;
    }

    public function update(Feature $feature, array $data): Feature
    {
        $feature->update($data);

        return $feature;
    }

    public function delete(Feature $feature): void
    {
        $feature->delete();
    }
}