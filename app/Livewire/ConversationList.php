<?php 
namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class ConversationList extends Component
{
    public $conversations = [];
    public $refreshKey = 0;
    public $selectedConversationId;

    protected $listeners = [
        'refreshConversations' => 'loadConversations',
        // 'echo:bookbee.global,MessageSent' => 'handleGlobalMessage'
    ];

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $currentUserId = auth('admin')->id();
        
        $this->conversations = Conversation::with([
                'customer', 
                'messages' => fn ($q) => $q->latest()->take(1) // Chỉ lấy tin nhắn mới nhất
            ])
            ->withCount(['messages as unread_messages_count' => function ($query) use ($currentUserId) {
                // Chỉ đếm tin nhắn KHÔNG PHẢI từ admin hiện tại và chưa được admin đọc
                $query->where('sender_id', '!=', $currentUserId)
                    ->whereDoesntHave('reads', function($q) use ($currentUserId) {
                        $q->where('user_id', $currentUserId);
                    });
            }])
            ->orderByDesc('last_message_at')
            ->get();
        
        // Update refresh key để trigger re-render
        $this->refreshKey = time();
        
        // Log để debug
        Log::info('ConversationList loaded', [
            'count' => $this->conversations->count(),
            'refreshKey' => $this->refreshKey,
            'currentUserId' => $currentUserId
        ]);
    }

    public function handleGlobalMessage($payload)
    {
        Log::info('Global message received in ConversationList', ['payload' => $payload]);
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
}
