<?php

namespace App\Services;

use App\Models\Feature;
use App\Events\FeatureCreated;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeatureService
{
    use AuthorizesRequests;
    /**
     * Get all features.
     */
    public function all(Project $project)
    {
        $this->authorize('viewAny', [Feature::class, $project]);
        return $project->features()->get();
    }

    /**
     * Get a feature by ID.
     */
    public function find(Feature $feature): ?Feature
    {
        $this->authorize('view', $feature);
        return $feature;
    }

    /**
     * Create a new feature.
     */
    public function create(array $data, Project $project): Feature
    {
        $this->authorize('create', [Feature::class, $project]);

        $feature = new Feature($data);
        $feature->project_id = $project->id;
        $feature->save();

        event(new FeatureCreated($feature));

        return $feature;
    }

    /**
     * Update an existing feature.
     */
    public function update(array $data, Feature $feature): Feature
    {
        $this->authorize('update', $feature);

        $feature->update($data);

        return $feature;
    }

    /**
     * Delete a feature.
     */
    public function delete(Feature $feature): void
    {
        $this->authorize('delete', $feature);
        $feature->delete();
    }
}
