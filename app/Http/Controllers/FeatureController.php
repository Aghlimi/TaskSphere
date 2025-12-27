<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponceException;
use App\Models\Feature;
use App\Models\Project;
use App\Services\FeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $featureService;
    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function index($projectId)
    {
        try {
            $features = $this->featureService->all($projectId);
            return response()->json($features, 200);
        } catch (ResponceException $e) {

            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$projectId)
    {
        $project = Project::findOrFail($projectId);
        try {
            $feature = $this->featureService->create($request,$project);
            return response()->json($feature, 201);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature)
    {
        return response()->json($this->featureService->find($feature), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Feature $feature)
    {
        try {
            return response()->json($this->featureService->update($request, $feature), 200);
        } catch (ResponceException $e) {
            return response()->json(
                ["message" => $e->getMessage()],
                $e->statusCode ?: 500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feature $feature)
    {
        return $this->edit($request, $feature);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        $this->featureService->delete($feature);
        return response()->json(null, 204);
    }
}
