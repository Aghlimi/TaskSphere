<?php

use Illuminate\Support\Facades\Route;

Route::prefix('')->group(function () {
    require __DIR__ . "/users/routes.php";
});
