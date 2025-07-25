<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class UserSessionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $type;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $type, $user = null)
    {
        Log::info("📢 Broadcast nội dung: $message | $type");
        $this->message = $message;
        $this->type = $type; // 'info', 'success', 'warning', 'error'
        $this->user = $user;
    }
    

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('user-status');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'status' => $this->type === 'success' ? 'online' : 'offline',
                'last_seen' => $this->type === 'success' ? null : now()->toDateTimeString()
            ] : null
        ];
    }
}
