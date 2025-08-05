<?php

namespace App\Listeners;

use App\Events\UserSessionChanged;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastUserLoginNotification
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
    public function handle(Login $event): void
    {
       // Log::info("✅ Listener chạy rồi nè! User: " . $event->user->email);
        broadcast(new UserSessionChanged("{$event->user->name} is online", 'success', $event->user));
        //  Log::info("📣 Đã gọi broadcast");
    }
}
