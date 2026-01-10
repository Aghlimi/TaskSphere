<?php

use App\Http\Controllers\AssignController;
use App\Models\Assign;

Route::get("/",[AssignController::class,"index"]);
Route::get("/{user}",[AssignController::class,"assign"]);
Route::get("/accept/{invitation}",[AssignController::class,"accept"]);
Route::get("/reject/{invitation}",[AssignController::class,"reject"]);
Route::delete("/{user}",[AssignController::class,"delete"]);