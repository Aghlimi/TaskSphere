<?php

namespace App\Services;

use App\Models\Feature;
use App\Events\FeatureCreated;
use App\Events\CompleteFeature;

class FeatureService
{
    /**
     * Get all features.
     */
    public function all()
    {
        return Feature::all();
    }

    /**
     * Get a feature by ID.
     */
    public function find(int $id): ?Feature
    {
        return Feature::find($id);
    }

    /**
     * Create a new feature.
     */
    public function create(array $data): Feature
    {
        $feature = Feature::create($data);

        event(new FeatureCreated($feature));

        return $feature;
    }

    /**
     * Update an existing feature.
     */
    public function update(Feature $feature, array $data): Feature
    {
        $feature->update($data);

        return $feature;
    }

    /**
     * Delete a feature.
     */
    public function delete(Feature $feature): bool
    {
        return $feature->delete();
    }

    /**
     * Mark a feature as complete.
     */
    public function complete(Feature $feature): Feature
    {
        $feature->update(['completed' => true, 'completed_at' => now()]);

        event(new CompleteFeature($feature));

        return $feature;
    }

    /**
     * Get features for a specific project.
     */
    public function getFeaturesForProject(int $projectId)
    {
        return Feature::where('project_id', $projectId)->get();
    }
}
