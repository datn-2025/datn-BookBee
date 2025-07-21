<?php

namespace App\Livewire;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConversationList extends Component
{
    public $conversations = [];
    public $selectedConversationId = null;

    protected $listeners = ['conversationUpdated' => 'refreshConversations'];

    public function mount()
    {
        $this->loadConversations();
        
        // Lấy conversation ID hiện tại từ route
        if (request()->route('id')) {
            $this->selectedConversationId = request()->route('id');
        }
    }

    public function loadConversations()
    {
        $this->conversations = Conversation::with(['customer', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->where('admin_id', Auth::id())
        ->orderBy('last_message_at', 'desc')
        ->get();
    }

    public function switchConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        
        // Emit event để parent component biết conversation nào được chọn
        $this->emit('conversationSelected', $conversationId);
        
        // Cập nhật URL mà không reload trang
        $this->dispatch('updateUrl', [
            'url' => route('admin.chat.show', $conversationId)
        ]);
    }


    public function refreshConversations()
    {
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
} 