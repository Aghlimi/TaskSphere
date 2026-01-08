<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FeatureController;

Route::get('/', [FeatureController::class, 'index']);
Route::post('/', [FeatureController::class, 'store']);
Route::get('/{feature}', [FeatureController::class, 'show']);
Route::put('/{feature}', [FeatureController::class, 'update']);
Route::patch('/{feature}', [FeatureController::class, 'update']);
Route::delete('/{feature}', [FeatureController::class, 'destroy']);
