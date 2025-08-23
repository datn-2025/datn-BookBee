<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // Load relationships including reply message and its sender
        $this->message = $message->load(['sender.role', 'replyToMessage.sender']);
        
        // Debug log để xác nhận event được tạo
        Log::info('MessageSent event created', [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->message->conversation_id,
            'content' => substr($this->message->content, 0, 50) . '...',
            'reply_to_message_id' => $this->message->reply_to_message_id,
            'has_reply_data' => $this->message->replyToMessage ? true : false
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
            'customer_id' => $this->message->conversation->customer_id,
            'admin_id' => $this->message->conversation->admin_id,
        ]);
        
        // Sử dụng public channel để đơn giản hóa
        return [
            new Channel('conversations.' . $this->message->conversation_id), // Public channel cho conversation
            new Channel('user.' . $this->message->conversation->customer_id), // Channel cho customer
            new Channel('user.' . $this->message->conversation->admin_id),    // Channel cho admin  
        ];
    }
    public function broadcastWith()
    {
        $data = [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'type' => $this->message->type ?? 'text',
            'file_path' => $this->message->file_path,
            'sender_id' => $this->message->sender_id,
            'conversation_id' => $this->message->conversation_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'avatar' => $this->message->sender->avatar ?? null,
            ],
            // Thêm thông tin reply
            'reply_to_message_id' => $this->message->reply_to_message_id,
            'reply_to_message' => null,
            'replyToMessage' => null
        ];
        
        // Nếu có reply, load thông tin tin nhắn gốc
        if ($this->message->reply_to_message_id) {
            $originalMessage = $this->message->replyToMessage;
            if ($originalMessage) {
                $data['reply_to_message'] = [
                    'id' => $originalMessage->id,
                    'content' => $originalMessage->content,
                    'sender_id' => $originalMessage->sender_id,
                    'sender' => [
                        'id' => $originalMessage->sender->id,
                        'name' => $originalMessage->sender->name,
                    ]
                ];
                // Đồng bộ cả 2 field để tương thích
                $data['replyToMessage'] = $data['reply_to_message'];
            }
        }
        
        // Log data được broadcast với thông tin reply
        Log::info('Broadcasting data for MessageSent', [
            'channels' => [
                'conversations.' . $this->message->conversation_id,
                'user.' . $this->message->conversation->customer_id,
                'user.' . $this->message->conversation->admin_id,
            ],
            'event_name' => 'MessageSent',
            'data' => $data,
            'has_reply' => !empty($data['reply_to_message_id']),
            'reply_data' => $data['reply_to_message'] ?? null
        ]);
        
        return $data;
    }
    public function broadcastAs()
    {
        return 'MessageSent';
    }
}
