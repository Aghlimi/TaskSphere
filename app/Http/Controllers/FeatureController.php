<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeatureRequest;
use App\Models\Feature;
use App\Models\Project;
use App\Services\FeatureService;

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

    public function index(Project $project)
    {
        $features = $this->featureService->all($project);
        return response()->json($features, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureRequest $request, Project $project)
    {
        $data = $request->validated();
        $feature = $this->featureService->create($data, $project);
        return response()->json($feature, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project,Feature $feature)
    {
        $feature = $this->featureService->find($feature);
        return response()->json($feature, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeatureRequest $request, Feature $feature)
    {
        $data = $request->validated();

        if (empty($data))
            return response()->json(['message' => 'no data provided'], 400);

        $feature = $this->featureService->update($data, $feature);
        return response()->json($feature, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureRequest $request, Project $project, Feature $feature)
    {
        $data = $request->validated();

        if (empty($data))
            return response()->json(['message' => 'no data provided'], 400);

        $feature = $this->featureService->update($data, $feature);
        return response()->json($feature, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Feature $feature)
    {
        $this->featureService->delete($feature);
        return response()->json(null, 204);
    }
}
