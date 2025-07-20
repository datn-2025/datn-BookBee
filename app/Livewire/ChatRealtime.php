<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ChatRealtime extends Component
{
    use WithFileUploads;

    public $conversations;
    public $selectedConversation;
    public $chatMessages = [];
    public $messageInput = '';
    public $uploadFile;


    public function mount($selectedConversation = null)
    {
        $this->conversations = Conversation::with('customer')->get();

        if ($selectedConversation) {
            $this->selectedConversation = Conversation::with(['customer', 'messages.sender'])->findOrFail($selectedConversation->id);
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        if (!$this->selectedConversation) return;

        $chatMessages = $this->selectedConversation
            ->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        // Đánh dấu tin chưa đọc
        $chatMessages->whereNull('read_at')->each(function ($message) {
            $message->update(['read_at' => now()]);
        });

        $this->chatMessages = $chatMessages; // Gán lại sau khi xử lý
    }


    public function send()
    {
        // Kiểm tra có nội dung để gửi không
        if (empty($this->messageInput) && !$this->uploadFile) {
            return;
        }

        // Validation linh hoạt hơn
        $rules = [
            'messageInput' => 'nullable|string|max:1000',
            'uploadFile' => 'nullable|file|max:10240' // 10MB
        ];

        // Nếu không có file thì messageInput phải có
        if (!$this->uploadFile) {
            $rules['messageInput'] = 'required|string|max:1000';
        }

        $this->validate($rules);

        // Xử lý file
        $type = 'text';
        $filePath = null;

        if ($this->uploadFile) {
            try {
                $filePath = $this->uploadFile->store('messages', 'public');
                $mimeType = $this->uploadFile->getMimeType();

                if (str_starts_with($mimeType, 'image/')) {
                    $type = 'image';
                } else {
                    $type = 'file';
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Lỗi khi upload file: ' . $e->getMessage());
                return;
            }
        }

        // Tạo tin nhắn
        $newMessage = Message::create([
            'id' => (string) Str::uuid(),
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'content' => $this->messageInput ?? '',
            'type' => $type,
            'file_path' => $filePath,
        ]);

        // Cập nhật conversation
        $this->selectedConversation->update([
            'last_message_at' => now(),
        ]);

        // Đánh dấu đã đọc
        MessageRead::create([
            'id' => (string) Str::uuid(),
            'message_id' => $newMessage->id,
            'user_id' => Auth::id(),
            'read_at' => now(),
        ]);
        Log::info('Message sent', ['message_id' => $newMessage->id]);
        // Phát sự kiện
        event(new MessageSent($newMessage));


        // Thêm tin nhắn mới vào danh sách ngay lập tức
        $this->chatMessages->push($newMessage);
        
        // Reset form (chỉ cần reset một lần)
        $this->reset(['messageInput', 'uploadFile']);

       
        // Scroll xuống dưới
        $this->dispatch('scrollToBottom');

    }

    public function updatedUploadFile()
    {
        if ($this->uploadFile) {
            // Validate file ngay khi upload
            $this->validate([
                'uploadFile' => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,txt'
            ]);

            // Debug thông tin file
            Log::info('File uploaded:', [
                'name' => $this->uploadFile->getClientOriginalName(),
                'size' => $this->uploadFile->getSize(),
                'mime' => $this->uploadFile->getMimeType(),
            ]);
        }
    }



    public function removeFile()
    {
        $this->uploadFile = null;
        $this->resetValidation('uploadFile');
    }

    public function onMessageSent($payload)
    {
        Log::info('Message received', ['payload' => $payload]);
        // Payload có thể là ['message' => [...]] hoặc object
        $message = $payload['message'] ?? $payload;
        if (isset($message['conversation_id']) && $message['conversation_id'] == $this->selectedConversation->id) {
            $this->loadMessages();
            $this->emit('messageReceived');
            $this->dispatch('scrollToBottom');

        }
    }

    public function render()
    {
        return view('livewire.chat-realtime', [
            'selectedConversation' => $this->selectedConversation,
            'chatMessages' => $this->chatMessages,
        ]);
    }
}
