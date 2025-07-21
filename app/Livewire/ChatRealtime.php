<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
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
        'conversationSelected' => 'switchConversation'
    ];

    public function getListeners()
    {
        if ($this->selectedConversation) {
            return array_merge($this->listeners, [
                "echo:bookbee.{$this->selectedConversation->id},MessageSent" => 'messageReceived'
            ]);
        }
        return $this->listeners;
    }

    public function mount($selectedConversation = null)
    {
        $this->selectedConversation = $selectedConversation;
        if ($this->selectedConversation) {
            $this->loadMessages();
            $this->markMessagesAsRead();
        }
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

    public function messageReceived($payload)
    {
        // Chỉ thêm tin nhắn nếu không phải từ người dùng hiện tại
        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;
        
        if ($payload['sender_id'] !== $currentUserId) {
            $this->loadMessages(); // Reload để lấy tin nhắn mới
            $this->dispatch('scroll-to-bottom');
        }
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

        // Clear input sau khi gửi
        $this->message_content = '';
        $this->messageInput = '';

        // Load lại tin nhắn và thêm tin nhắn mới vào cuối danh sách
        $this->loadMessages();

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        // Update conversation
        $this->selectedConversation->update(['last_message_at' => now()]);

        // Dispatch event để refresh conversation list
        $this->dispatch('conversationUpdated');
        
        // Scroll to bottom
        $this->dispatch('scroll-to-bottom');
        
        // Show success feedback
        $this->dispatch('message-sent');
    }

    public function markMessagesAsRead()
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
