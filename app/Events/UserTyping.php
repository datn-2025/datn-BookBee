<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $conversationId;
    public $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct($user, $conversationId, $isTyping = true)
    {
        $this->user = $user;
        $this->conversationId = $conversationId;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('bookbee.' . $this->conversationId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return $this->isTyping ? 'UserTyping' : 'UserStoppedTyping';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'conversation_id' => $this->conversationId,
            'is_typing' => $this->isTyping,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}
