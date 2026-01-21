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
        $exceptions->report(function (Throwable $e) {
            try {
                $logMessage = "\n" . str_repeat('=', 50) . "\n";
                $logMessage .= "[" . date('Y-m-d H:i:s') . "] EXCEPTION CAUGHT\n";
                $logMessage .= "Message: " . $e->getMessage() . "\n";
                $logMessage .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
                $logMessage .= "Stack Trace:\n" . $e->getTraceAsString() . "\n";
                $logMessage .= str_repeat('=', 50) . "\n";
                
                file_put_contents(storage_path('logs/my-custom-exceptions.log'), $logMessage, FILE_APPEND);
                
                // Also dispatch the event
                event(new \App\Events\ErrorLogs($e));
            } catch (\Throwable $logError) {
                // Fail silently
            }
        });
    })->create();
