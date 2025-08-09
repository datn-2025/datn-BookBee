<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use App\Events\MessageSent;
use App\Services\AutoReplyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminChatrealtimeController extends Controller
{
    public function index()
    {
        // Đánh dấu admin đang hoạt động
        AutoReplyService::markAdminAsActive(Auth::id());
        
        // Lấy tất cả các cuộc trò chuyện với tin nhắn mới nhất
        $conversations = Conversation::with(['customer', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->where('admin_id', Auth::id())
        ->orderBy('last_message_at', 'desc')
        ->get();

        // Lấy danh sách users có role "User" và status "Hoạt Động"
        $activeUsers = User::with('role')
            ->whereHas('role', function($query) {
                $query->where('name', 'User');
            })
            ->where('status', 'Hoạt Động')
            ->orderBy('name')
            ->get();

        return view('admin.chat.index', [
            'selectedConversation' => null,
            'conversations' => $conversations,
            'activeUsers' => $activeUsers,
        ]);
    }
    
   public function show($id)
    {
        // Đánh dấu admin đang hoạt động
        AutoReplyService::markAdminAsActive(Auth::id());
        
        // Lấy tất cả các cuộc trò chuyện với tin nhắn mới nhất
        $conversations = Conversation::with(['customer', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->where('admin_id', Auth::id())
        ->orderBy('last_message_at', 'desc')
        ->get();

        // Lấy danh sách users có role "User" và status "Hoạt Động"
        $activeUsers = User::with('role')
            ->whereHas('role', function($query) {
                $query->where('name', 'User');
            })
            ->where('status', 'Hoạt Động')
            ->orderBy('name')
            ->get();

        // Lấy thông tin chi tiết cuộc trò chuyện đã chọn
        $selectedConversation = Conversation::with(['customer', 'messages.sender'])->findOrFail($id);

        // Lấy các tin nhắn của cuộc trò chuyện đã chọn, bao gồm thông tin người gửi
        $messages = $selectedConversation->messages()->with('sender')->orderBy('created_at')->get();

        // Đánh dấu tin nhắn là đã đọc (cập nhật trường read_at)
        $messages->whereNull('read_at')->each(function ($message) {
            $message->update(['read_at' => now()]);
        });

        // Trả về view với các dữ liệu: cuộc trò chuyện và tin nhắn
        return view('admin.chat.index', compact(
            'conversations',
            'selectedConversation',
            'messages',
            'activeUsers'
        ));
    }

    /**
     * Tạo cuộc trò chuyện mới với user
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id'
        ]);

        $customerId = $request->customer_id;
        $adminId = Auth::id();

        // Kiểm tra xem cuộc trò chuyện đã tồn tại chưa
        $existingConversation = Conversation::where('admin_id', $adminId)
            ->where('customer_id', $customerId)
            ->first();

        if ($existingConversation) {
            return response()->json([
                'success' => true,
                'conversation_id' => $existingConversation->id,
                'message' => 'Cuộc trò chuyện đã tồn tại',
                'redirect_url' => route('admin.chat.show', $existingConversation->id)
            ]);
        }

        // Tạo cuộc trò chuyện mới
        $conversation = Conversation::create([
            'admin_id' => $adminId,
            'customer_id' => $customerId,
            'status' => 'active',
            'last_message_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'message' => 'Tạo cuộc trò chuyện thành công',
            'redirect_url' => route('admin.chat.show', $conversation->id)
        ]);
    }

    /**
     * Lấy danh sách users để hiển thị trong contacts
     */
    public function getActiveUsers(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = User::with('role')
            ->whereHas('role', function($q) {
                $q->where('name', 'User');
            })
            ->where('status', 'Hoạt Động');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('name')->limit(50)->get();
        
        return response()->json([
            'success' => true,
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&size=200',
                    'is_online' => $user->isOnline(),
                    'is_active' => $user->isActiveWithin(60),
                ];
            })
        ]);
    }


}
