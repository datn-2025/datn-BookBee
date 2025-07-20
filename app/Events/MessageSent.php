<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender.role']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    /**
     * Get the channels the event should broadcast on.
     * 
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Sử dụng public channel thay vì private
        return new Channel('bookbee.' . $this->message->conversation_id);
    }
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->message->conversation_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'avatar' => $this->message->sender->avatar,
                'role' => $this->message->sender->role ? [
                    'id' => $this->message->sender->role->id,
                    'name' => $this->message->sender->role->name,
                ] : null,
            ],
            'reads' => $this->message->reads->map(function($read) {
                return [
                    'user_id' => $read->user_id,
                    'read_at' => $read->read_at->toDateTimeString(),
                ];
            })
        ];
    }
}
