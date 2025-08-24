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
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
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
    public $replyToMessage = null; // Tin nhắn được reply

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
            ->with(['sender.role', 'reads', 'replyToMessage.sender'])
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
        Log::info('SendMessage called', [
            'hasFileUpload' => !empty($this->fileUpload),
            'messageContent' => $this->message_content,
            'dataParam' => $data
        ]);

        // Kiểm tra xem có file upload không
        if ($this->fileUpload) {
            Log::info('File upload detected, calling sendFileMessage');
            $this->sendFileMessage();
            return;
        }

        // Lấy nội dung từ tham số truyền vào hoặc từ property
        $messageContent = $data['message_content'] ?? $this->message_content ?? '';
        
        // Nếu không có nội dung và không có file thì không làm gì
        if (empty(trim($messageContent)) && !$this->fileUpload) {
            Log::info('No content and no file, skipping send');
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

        // Debug log để kiểm tra
        Log::info('SendMessage Debug', [
            'replyToMessage' => $this->replyToMessage ? $this->replyToMessage->id : null,
            'messageContent' => $messageContent,
            'hasFile' => !empty($this->fileUpload)
        ]);

        // Tạo tin nhắn mới
        $messageData = [
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => $currentUserId,
            'content' => trim($messageContent),
            'type' => 'text'
        ];

        // Thêm reply reference nếu có
        if ($this->replyToMessage) {
            $messageData['reply_to_message_id'] = $this->replyToMessage->id;
            Log::info('Adding reply reference', ['reply_to' => $this->replyToMessage->id]);
        }

        $message = new Message($messageData);
        $message->save();

        // Debug sau khi save
        Log::info('Message saved', [
            'id' => $message->id,
            'reply_to_message_id' => $message->reply_to_message_id
        ]);

        // Đánh dấu admin đang hoạt động
        AutoReplyService::markAdminAsActive($currentUserId);

        // Clear input ngay sau khi lưu message
        $this->message_content = '';
        $this->messageInput = '';
        
        // Clear reply sau khi gửi tin nhắn thành công
        if ($this->replyToMessage) {
            $this->replyToMessage = null;
            $this->dispatch('hideReplyPreview');
        }

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
            'cleared_input' => empty($this->message_content),
            'reply_to_message_id' => $message->reply_to_message_id
        ]);
    }

    public function sendFileMessage()
    {
        Log::info('SendFileMessage called', [
            'hasFileUpload' => !empty($this->fileUpload),
            'fileUploadType' => $this->fileUpload ? get_class($this->fileUpload) : 'null'
        ]);

        try {
            $this->validate([
                'fileUpload' => 'required|file|max:10240' // 10MB max
            ]);
        } catch (\Exception $e) {
            Log::error('File validation failed', [
                'error' => $e->getMessage(),
                'hasFileUpload' => !empty($this->fileUpload)
            ]);
            return;
        }

        if (!$this->selectedConversation) {
            Log::error('No selected conversation');
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

        // Determine caption: prefer admin-typed text/emojis, fallback to filename
        $caption = trim($this->message_content ?? '');

        // Create message data
        $messageData = [
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => $currentUserId,
            'content' => ($caption !== '' ? $caption : $fileName),
            'type' => $type,
            'file_path' => $path
        ];

        // Thêm reply reference nếu có
        if ($this->replyToMessage) {
            $messageData['reply_to_message_id'] = $this->replyToMessage->id;
        }

        $message = new Message($messageData);
        $message->save();

        // Clear file input
        $this->fileUpload = null;
        $this->message_content = '';
        
        // Clear reply sau khi gửi tin nhắn thành công
        if ($this->replyToMessage) {
            $this->replyToMessage = null;
            $this->dispatch('hideReplyPreview');
        }

        // Load messages và broadcast
        $this->loadMessages();
        broadcast(new MessageSent($message))->toOthers();

        // Update conversation
        $this->selectedConversation->update(['last_message_at' => now()]);
        
        // Dispatch events
        $this->dispatch('refreshConversations')->to(ConversationList::class);
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('message-sent');
        $this->dispatch('messageProcessed');
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

    // Legacy uploadFile method - không sử dụng nữa, file được gửi qua sendMessage
    public function uploadFile()
    {
        // Redirect to sendMessage to handle file upload
        $this->sendMessage();
    }

    public function setReplyTo($messageId)
    {
        Log::info('SetReplyTo called', ['messageId' => $messageId]);
        
        $message = Message::find($messageId);
        if ($message && $message->conversation_id == $this->selectedConversation->id) {
            $this->replyToMessage = $message;
            
            Log::info('Reply message set', [
                'replyToMessage' => $this->replyToMessage->id,
                'replyToContent' => $this->replyToMessage->content
            ]);
            
            // Dispatch để show reply preview với dữ liệu đầy đủ
            $this->dispatch('showReplyPreview', [
                'messageId' => $messageId,
                'senderName' => $message->sender->name,
                'content' => $message->content
            ]);
            
            Log::info('Dispatched showReplyPreview event with data', [
                'messageId' => $messageId,
                'senderName' => $message->sender->name,
                'contentLength' => strlen($message->content)
            ]);
            
        } else {
            Log::error('Reply message not found or invalid conversation', [
                'messageId' => $messageId,
                'conversationId' => $this->selectedConversation?->id
            ]);
        }
    }

    public function cancelReply()
    {
        Log::info('CancelReply called', ['previousReplyTo' => $this->replyToMessage?->id]);
        
        $this->replyToMessage = null;
        $this->dispatch('hideReplyPreview');
        
        Log::info('Reply cancelled - replyToMessage reset to null');
    }

    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);
        
        if (!$message || $message->conversation_id != $this->selectedConversation->id) {
            return;
        }

        $currentUser = Auth::guard('admin')->user() ?: Auth::user();
        $currentUserId = $currentUser ? $currentUser->id : null;

        // Chỉ cho phép xóa tin nhắn của chính mình hoặc nếu là admin guard
        if ($message->sender_id != $currentUserId && !Auth::guard('admin')->check()) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Bạn không có quyền xóa tin nhắn này!'
            ]);
            return;
        }

        try {
            // Xóa file nếu có
            if ($message->file_path) {
                Storage::disk('public')->delete($message->file_path);
            }

            // Xóa message reads
            MessageRead::where('message_id', $messageId)->delete();
            
            // Xóa tin nhắn
            $message->delete();

            // Load lại messages
            $this->loadMessages();

            // Broadcast event xóa tin nhắn
            broadcast(new \App\Events\MessageDeleted($messageId, $this->selectedConversation->id));

            $this->dispatch('showAlert', [
                'type' => 'success',
                'message' => 'Đã xóa tin nhắn thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa tin nhắn!'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.chat-realtime');
    }
}
