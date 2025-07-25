<?php

namespace App\Listeners;

use App\Events\UserSessionChanged;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastUserLogoutNotification
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
    public function handle(Logout $event): void
    {
        broadcast(new UserSessionChanged("{$event->user->name} is offline", 'danger', $event->user));
    }
}
