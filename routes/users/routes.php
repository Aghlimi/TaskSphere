<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::resource('users', UserController::class);
    // ->middleware('auth:sanctum')
    // ->only(['index', 'show', 'edit', 'update', 'destroy']);
Route::get('/login', [UserController::class, 'showLoginForm']);

Route::post('/login', [UserController::class, 'login']);

Route::get('/logout', [UserController::class, 'logout'])->name('logout');
