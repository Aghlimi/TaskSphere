<?php

namespace App\Listeners;

use App\Events\CompleteFeature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCompleteFeatureNotification
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
    public function handle(CompleteFeature $event): void
    {
        //
    }
}
