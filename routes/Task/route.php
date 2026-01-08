<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index']);
Route::post('/', [TaskController::class, 'store']);
Route::get('/{task}', [TaskController::class, 'show']);
Route::put('/{task}', [TaskController::class, 'update']);
Route::patch('/{task}', [TaskController::class, 'edit']);
Route::delete('/{task}', [TaskController::class, 'destroy']);
