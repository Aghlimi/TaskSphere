<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::put('/users', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::patch('/users', [UserController::class, 'edit'])->middleware('auth:sanctum');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/login', [UserController::class, 'showLoginForm']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout'])->name('logout');
