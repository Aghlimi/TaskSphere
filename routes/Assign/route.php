<?php

use App\Http\Controllers\AssignController;
use App\Models\Assign;

Route::get("/",[AssignController::class,"index"]);
Route::get("/{user}",[AssignController::class,"assign"]);
Route::post("/accept/{invitation}",[AssignController::class,"accept"]);
Route::post("/reject/{invitation}",[AssignController::class,"reject"]);
Route::delete("/{user}",[AssignController::class,"delete"]);