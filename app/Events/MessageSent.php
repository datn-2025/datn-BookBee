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
use Illuminate\Support\Facades\Log;

class MessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender.role']);
        
        // Debug log để xác nhận event được tạo
        Log::info('MessageSent event created', [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->message->conversation_id,
            'content' => substr($this->message->content, 0, 50) . '...'
        ]);
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
    public function broadcastOn(): array
    {
        Log::info('Broadcasting MessageSent on public channels', [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_key' => config('broadcasting.connections.pusher.key'),
            'channels' => [
                'conversations.' . $this->message->conversation_id,
                'bookbee.' . $this->message->conversation_id,
                'user.' . $this->message->conversation->customer_id, // Channel cho customer
                'user.' . $this->message->conversation->admin_id,    // Channel cho admin
                'bookbee.global'
            ]
        ]);
        
        // Test với manual pusher trigger
        try {
            $config = config('broadcasting.connections.pusher');
            $options = [
                'cluster' => $config['options']['cluster'],
                'encrypted' => true,
                'host' => $config['options']['host'],
                'port' => $config['options']['port'],
                'scheme' => $config['options']['scheme'],
            ];
            
            $pusher = new \Pusher\Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $options
            );
            
            $data = [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'sender_id' => $this->message->sender_id,
                'conversation_id' => $this->message->conversation_id,
                'created_at' => $this->message->created_at->toDateTimeString()
            ];
            
            $manualResult = $pusher->trigger('conversations.' . $this->message->conversation_id, 'MessageSent', $data);
            Log::info('Manual pusher trigger result', ['result' => $manualResult]);
            
        } catch (\Exception $e) {
            Log::error('Manual pusher trigger failed', ['error' => $e->getMessage()]);
        }
        
        // Sử dụng public channel để đơn giản hóa
        return [
            new Channel('conversations.' . $this->message->conversation_id), // Public channel cho conversation
            new Channel('bookbee.' . $this->message->conversation_id), // Channel cho admin panel
            new Channel('user.' . $this->message->conversation->customer_id), // Channel cho customer
            new Channel('user.' . $this->message->conversation->admin_id),    // Channel cho admin  
            new Channel('bookbee.global') // Channel global cho tất cả conversation list updates
        ];
    }
    public function broadcastWith()
    {
        $data = [
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
            'reads' => $this->message->reads->map(function ($read) {
                return [
                    'user_id' => $read->user_id,
                    'read_at' => $read->read_at->toDateTimeString(),
                ];
            })
        ];
        
        // Log data được broadcast
        Log::info('Broadcasting data for MessageSent', [
            'channels' => [
                'conversations.' . $this->message->conversation_id,
                'bookbee.' . $this->message->conversation_id,
                'user.' . $this->message->conversation->customer_id,
                'user.' . $this->message->conversation->admin_id,
                'bookbee.global'
            ],
            'event_name' => 'MessageSent',
            'data' => $data
        ]);
        
        return $data;
    }
    public function broadcastAs()
    {
        return 'MessageSent';
    }
}
