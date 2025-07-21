<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminChatrealtimeController extends Controller
{
    public function index()
    {
        return view('admin.chat.index', [
            'selectedConversation' => null,
        ]);
    }
    
   public function show($id)
    {
        // Lấy tất cả các cuộc trò chuyện và thông tin khách hàng
        $conversations = Conversation::with('customer')->get();

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
            'messages'
        ));
    }
   


}
