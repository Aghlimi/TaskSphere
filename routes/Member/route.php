<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get("/",[MemberController::class,'listMembers']);
Route::post('/{user}',[MemberController::class,'invite']);
Route::get('/accept/{inv}',[MemberController::class,'accept']);
Route::get('/reject/{inv}',[MemberController::class,'reject']);
Route::delete('/delete/{user}',[MemberController::class,'delete']);
Route::post('/setadmin/{user}',[MemberController::class,'setAdmin']);
Route::delete('/removeadmin/{user}',[MemberController::class,'removeAdmin']);
Route::get('/role/{user}',[MemberController::class,'UserRole']);