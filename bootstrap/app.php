<?php

use App\Exceptions\ResponceException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // $exceptions->render(function (ResponceException $e){
        //     return response()->json([
        //         'error' => $e->getMessage()
        //     ], $e->getStatusCode());
        // });
        // $exceptions->render(function (Exception $e){

        //     return view('error',[
        //         'message' => $e->getMessage(),
        //         'details' => $e->getTraceAsString()
        //     ]);
        // });
    })->create();
