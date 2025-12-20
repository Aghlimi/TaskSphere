<?php

namespace App\Listeners;

use App\Events\ErrorLogs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class ErrorLogsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ErrorLogs $event): void
    {
        Log::error('ErrorLogs Event: ' . $event->message);
        file_put_contents('../../storage/logs/custom_error.log', $event->details . PHP_EOL, FILE_APPEND);
    }
}
