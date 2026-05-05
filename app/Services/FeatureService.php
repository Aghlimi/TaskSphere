<?php

namespace App\Services;

use App\Models\Feature;
use App\Events\FeatureCreated;
use App\Models\Project;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeatureService
{
    use AuthorizesRequests;

    public function __construct(private FeatureRepositoryInterface $features)
    {
    }
    /**
     * Get all features.
     */
    public function all(Project $project)
    {
        $this->authorize('viewAny', [Feature::class, $project]);
        return $this->features->allForProject($project);
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

        $feature = $this->features->create($data, $project);

        event(new FeatureCreated($feature));

        return $feature;
    }

    /**
     * Update an existing feature.
     */
    public function update(array $data, Feature $feature): Feature
    {
        $this->authorize('update', $feature);

        return $this->features->update($feature, $data);
    }

    /**
     * Delete a feature.
     */
    public function delete(Feature $feature): void
    {
        $this->authorize('delete', $feature);
        $this->features->delete($feature);
    }
}
