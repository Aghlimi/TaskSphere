<?php

namespace App\Services;

use App\Events\ErrorLogs;
use App\Exceptions\ResponceException;
use App\Models\Feature;
use App\Events\FeatureCreated;
use App\Models\Member;
use App\Models\Project;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeatureService
{
    use AuthorizesRequests;
    /**
     * Get all features.
     */
    public function all($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('viewAny', [Feature::class, $project]);
        try {
            $features = $project->features()->get();
        } catch (Exception $e) {
            event(new ErrorLogs($e));
            throw $e;
        }
        return $features;
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
    public function create(Request $request, Project $project): Feature
    {
        $this->authorize('create', [Feature::class,$project]);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string'
        ]);

        $feature = new Feature($validatedData);
        $feature->project_id = $project->id;
        $feature->save();

        event(new FeatureCreated($feature));

        return $feature;
    }

    /**
     * Update an existing feature.
     */
    public function update(Request $request, Feature $feature): Feature
    {
        $this->authorize('update', $feature);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'details' => 'sometimes|string',
            'status' => 'sometimes|in:completed',
        ]);

        if (empty($validatedData))
            throw new ResponceException("No data provided for update", 400);

        $feature->update($validatedData);
        return $feature;
    }

    /**
     * Delete a feature.
     */
    public function delete(Feature $feature): void
    {
        $this->authorize('delete', $feature);
        try {
            $feature->delete();
        } catch (Exception $e) {
            event(new ErrorLogs($e));
            throw new ResponceException(
                "unknown problem when deleting feature",
                500,
            );
        }
    }
}
