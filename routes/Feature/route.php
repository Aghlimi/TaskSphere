<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FeatureController;

Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('features', FeatureController::class);
    Route::get('/projects/{projectId}/features', [FeatureController::class, 'index']);
    Route::post('/projects/{projectId}/features', [FeatureController::class, 'store']);
    Route::get('/features/{feature}', [FeatureController::class, 'show']);
    Route::put('/features/{feature}', [FeatureController::class, 'update']);
    Route::patch('/features/{feature}', [FeatureController::class, 'update']);
    Route::delete('/features/{feature}', [FeatureController::class, 'destroy']);

});
