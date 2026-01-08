<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users', [UserController::class, 'update']);
    Route::patch('/users', [UserController::class, 'edit']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/logout', [UserController::class, 'logout']);
});
