<?php
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    throw new \Exception("hegsdfikerlidsgu");
    return response()->json(["message" => "Welcome to TaskSphere API"]);
});

require __DIR__ . '/User/route.php';

Route::prefix('/projects')->middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/Project/route.php';
    Route::prefix('{project}/members')->group(function () {
        require __DIR__ . '/Member/route.php';
    });
    Route::prefix('/{project}/features')->group(function () {
        require __DIR__ . '/Feature/route.php';
        Route::prefix('/{feature}/tasks')->group(function () {
            require __DIR__ . '/Task/route.php';
            Route::prefix('/{task}/assign')->group(function () {
                require __DIR__ . '/Assign/route.php';
            });
        });
    });
});
