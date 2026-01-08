<?php

namespace App\Listeners;

use App\Events\InvitedToTeamEvent;
use App\Notifications\InviteToTeamNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InvitedToTeamListener implements ShouldQueue
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
    public function handle(InvitedToTeamEvent $event): void
    {
        $event->inv->user->notify(new InviteToTeamNotification(
            $event->inv
        ));
    }
}
