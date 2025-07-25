<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Livewire\ConversationList;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use App\Services\AutoReplyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChatRealtime extends Component
{
    use WithFileUploads;

    // Properties
    public $selectedConversation;
    public $chatMessages = [];
    public $messageInput = '';
    public $message_content = '';
    public $fileUpload;

    // Listeners
    protected $listeners = [
        'refreshMessages' => 'loadMessages',
        'conversationSelected' => 'switchConversation',
        'handleIncomingMessage' => 'handleIncomingMessage'
    ];

    public function getListeners()
    {
        $listeners = $this->listeners;
        
        if ($this->selectedConversation) {
            // Đăng ký listener cho conversation channel
            $conversationChannel = "echo:bookbee.{$this->selectedConversation->id},MessageSent";
            $listeners[$conversationChannel] = 'messageReceived';
            
            Log::info('ChatRealtime listener registered', [
                'conversation_id' => $this->selectedConversation->id,
                'channel' => "bookbee.{$this->selectedConversation->id}",
                'listener' => $conversationChannel
            ]);
        }
        
        return $listeners;
    }

    public function mount($selectedConversation = null)
    {
        $this->selectedConversation = $selectedConversation;
        if ($this->selectedConversation) {
            $this->loadMessages();
            $this->markMessagesAsRead();
        }
    }

    public function hasMessages()
    {
        return $this->chatMessages && count($this->chatMessages) > 0;
    }

    public function isNewConversation()
    {
        return !$this->hasMessages();
    }

    public function loadMessages()
    {
        if (!$this->selectedConversation) {
            return;
        }

        $this->chatMessages = Message::where('conversation_id', $this->selectedConversation->id)
            ->with(['sender.role', 'reads'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Update last seen
        $this->selectedConversation->update(['last_message_at' => now()]);
    }

    public function handleIncomingMessage($payload)
    {
        Log::info('ChatRealtime handleIncomingMessage called', [
            'payload' => $payload,
            'conversation_id' => $this->selectedConversation?->id,
            'current_user_id' => Auth::guard('admin')->user()?->id ?: Auth::user()?->id
        ]);
        
        // Chỉ thêm tin nhắn nếu không phải từ người dùng hiện tại
        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;
        
        if (isset($payload['sender_id']) && $payload['sender_id'] !== $currentUserId) {
            Log::info('Processing new message in admin chat', [
                'sender_id' => $payload['sender_id'],
                'current_user_id' => $currentUserId,
                'conversation_id' => $payload['conversation_id'] ?? null
            ]);
            
            $this->loadMessages(); // Reload để lấy tin nhắn mới
            $this->dispatch('scroll-to-bottom');
            
            // Dispatch event để refresh conversation list
            $this->dispatch('refreshConversations')->to(ConversationList::class);
        } else {
            Log::info('Skipping message - from current user', [
                'sender_id' => $payload['sender_id'] ?? null,
                'current_user_id' => $currentUserId
            ]);
        }
    }

    // Backward compatibility
    public function messageReceived($payload)
    {
        return $this->handleIncomingMessage($payload);
    }

    public function switchConversation($conversationId)
    {
        $this->selectedConversation = Conversation::with(['customer', 'messages.sender'])->find($conversationId);
        if ($this->selectedConversation) {
            $this->loadMessages();
            $this->markMessagesAsRead();
            $this->dispatch('scroll-to-bottom');
        }
    }

    public function sendMessage($data = null)
    {
        // Lấy nội dung từ tham số truyền vào hoặc từ property
        $messageContent = $data['message_content'] ?? $this->message_content ?? '';
        
        if (empty(trim($messageContent))) {
            return;
        }

        if (!$this->selectedConversation) {
            return;
        }

        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;

        if (!$currentUserId) {
            return;
        }

        // Tạo tin nhắn mới
        $message = new Message([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => $currentUserId,
            'content' => trim($messageContent),
            'type' => 'text'
        ]);

        $message->save();

        // Đánh dấu admin đang hoạt động
        AutoReplyService::markAdminAsActive($currentUserId);

        // Clear input ngay sau khi lưu message
        $this->message_content = '';
        $this->messageInput = '';

        // Load lại tin nhắn và thêm tin nhắn mới vào cuối danh sách
        $this->loadMessages();

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        // Update conversation last_message_at
        $this->selectedConversation->update(['last_message_at' => now()]);
        
        // Dispatch event để refresh conversation list
        $this->dispatch('refreshConversations')->to(ConversationList::class);
        
        // Scroll to bottom
        $this->dispatch('scroll-to-bottom');
        
        // Show success feedback and trigger animations
        $this->dispatch('message-sent');
        $this->dispatch('messageProcessed');

        
        Log::info('ChatRealtime sendMessage completed', [
            'message_id' => $message->id,
            'cleared_input' => empty($this->message_content)
        ]);
    }    public function markMessagesAsRead()
    {
        if (!$this->selectedConversation) {
            return;
        }

        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;

        if (!$currentUserId) {
            return;
        }

        // Mark all unread messages as read
        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('sender_id', '!=', $currentUserId)
            ->whereDoesntHave('reads', function($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            })
            ->get()
            ->each(function($message) use ($currentUserId) {
                MessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $currentUserId,
                    'read_at' => now()
                ]);
            });
    }

    public function uploadFile()
    {
        $this->validate([
            'fileUpload' => 'required|file|max:10240' // 10MB max
        ]);

        if (!$this->selectedConversation) {
            return;
        }

        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;

        if (!$currentUserId) {
            return;
        }

        // Store file
        $path = $this->fileUpload->store('chat-files', 'public');
        $fileName = $this->fileUpload->getClientOriginalName();

        // Determine file type
        $mimeType = $this->fileUpload->getMimeType();
        $type = 'file';
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        }

        // Create message
        $message = new Message([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => $currentUserId,
            'content' => $fileName,
            'type' => $type,
            'file_path' => $path
        ]);

        $message->save();

        // Load messages và broadcast
        $this->loadMessages();
        broadcast(new MessageSent($message))->toOthers();

        // Update conversation
        $this->selectedConversation->update(['last_message_at' => now()]);

        // Clear file input
        $this->fileUpload = null;

        // Dispatch event
        $this->dispatch('conversationUpdated');
    }

    public function render()
    {
        return view('livewire.chat-realtime');
    }
}
