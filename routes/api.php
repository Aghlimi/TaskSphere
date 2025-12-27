<?php
use Illuminate\Support\Facades\Route;
Route::group([],function () {
        require __DIR__.'/Project/route.php';
        require __DIR__.'/User/route.php';
        require __DIR__.'/Feature/route.php';
    });
