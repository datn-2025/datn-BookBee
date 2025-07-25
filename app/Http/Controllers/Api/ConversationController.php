<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Services\AutoReplyService;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{


    /**
     * Display a listing of the messages in a conversation.
     */
    public function index(Request $request)
    {
        $query = Conversation::with([
            'customer:id,name,email,avatar',
            'admin:id,name,email,avatar',
            'messages' => function ($query) {
                $query->with('sender:id,name,email,avatar')
                      ->orderBy('created_at', 'asc');
            }
        ]);
        
        // Filter by customer_id if provided
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by admin_id if provided
        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        
        $conversations = $query->orderByDesc('last_message_at')->get();

        return response()->json($conversations);
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'customer_id' => 'required_without:conversation_id|exists:users,id',
            'admin_id' => 'required_without:conversation_id|exists:users,id',
            'content' => 'nullable|string',
            'type' => 'nullable|string|in:text,image,file',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            'file_path' => 'nullable|string'
        ]);

        // Validate that either content or image is provided
        if (!$request->content && !$request->hasFile('image')) {
            return response()->json([
                'message' => 'Vui lòng nhập nội dung tin nhắn hoặc chọn ảnh'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // If conversation_id is provided, use existing conversation
            if ($request->conversation_id) {
                $conversation = Conversation::findOrFail($request->conversation_id);
            } else {
                // Kiểm tra hoặc tạo mới conversation
                $conversation = Conversation::firstOrCreate(
                    [
                        'customer_id' => $request->customer_id,
                        'admin_id' => $request->admin_id
                    ],
                    [
                        'last_message_at' => now()
                    ]
                );
            }

            // Handle file upload
            $filePath = null;
            $messageType = $request->type ?? 'text';
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('chat-images', $fileName, 'public');
                $messageType = 'image';
            }

            // Tạo tin nhắn mới
            $message = new Message([
                'sender_id' => $request->sender_id, // Use sender_id from request
                'content' => $request->content ?? ($messageType === 'image' ? 'Đã gửi một hình ảnh' : ''),
                'type' => $messageType,
                'file_path' => $filePath ?? $request->file_path
            ]);

            $conversation->messages()->save($message);

            // Cập nhật thời gian tin nhắn cuối
            $conversation->update(['last_message_at' => now()]);

            // Broadcast event - bỏ toOthers() để đảm bảo broadcast
            broadcast(new MessageSent($message));
            
            \Illuminate\Support\Facades\Log::info('Message sent and broadcasted', [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'conversation_id' => $message->conversation_id
            ]);

            // Cập nhật last_seen cho user
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user instanceof \App\Models\User) {
                $user->update(['last_seen' => now()]);
            }

            // Kiểm tra và gửi tin nhắn tự động nếu cần
            $autoReplyService = new AutoReplyService();
            $autoReplyService->checkAndSendAutoReply($conversation, $message);

            DB::commit();
            // Trả về dữ liệu cho frontend
            return response()->json([
                'message' => 'Gửi tin nhắn thành công',
                'conversation' => $conversation->load(['customer', 'admin']),
                'conversation_id' => $conversation->id,
                'new_message' => $message->load(['sender.role'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi khi gửi tin nhắn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new conversation between customer and admin.
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'admin_id' => 'required|exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            // Kiểm tra xem conversation đã tồn tại chưa
            $existingConversation = Conversation::where('customer_id', $request->customer_id)
                ->where('admin_id', $request->admin_id)
                ->first();

            if ($existingConversation) {
                // Nếu đã có conversation, trả về conversation đó
                return response()->json([
                    'message' => 'Cuộc trò chuyện đã tồn tại',
                    'conversation' => $existingConversation->load(['customer', 'admin'])
                ], 200);
            }

            // Tạo conversation mới
            $conversation = Conversation::create([
                'customer_id' => $request->customer_id,
                'admin_id' => $request->admin_id,
                'last_message_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo cuộc trò chuyện thành công',
                'conversation' => $conversation->load(['customer', 'admin'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi khi tạo cuộc trò chuyện',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(string $id)
    {
        try {
            $message = Message::findOrFail($id);

            // Nếu có file đính kèm thì xóa file (nếu cần)
            if ($message->file_path && Storage::exists($message->file_path)) {
                Storage::delete($message->file_path);
            }

            $message->delete();

            return response()->json([
                'message' => 'Xóa tin nhắn thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể xóa tin nhắn',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
