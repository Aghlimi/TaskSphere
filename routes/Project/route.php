<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;


Route::apiResource('projects', ProjectController::class);
Route::get('/', [ProjectController::class, 'index']);
Route::post('/', [ProjectController::class, 'store']);
Route::get('/{project}', [ProjectController::class, 'show']);
Route::patch('/{project}', [ProjectController::class, 'edit']);
Route::put('/{project}', [ProjectController::class, 'update']);
Route::delete('/{project}', [ProjectController::class, 'destroy']);
