<?php
use Illuminate\Support\Facades\Route;

require __DIR__ . '/User/route.php';

Route::prefix('/projects')->middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/Project/route.php';
    require __DIR__ . '/Member/route.php';
    Route::prefix('/{project}/features')->group(function () {
        require __DIR__ . '/Feature/route.php';
        Route::prefix('/{feature}/tasks')->group(function () {
            require __DIR__ . '/Task/route.php';
        });
    });
});
