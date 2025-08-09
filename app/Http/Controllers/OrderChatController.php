<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;
use Carbon\Carbon;

class OrderChatController extends Controller
{
    /**
     * Kiểm tra xem đơn hàng có thể chat không
     */
    public function canChat(Request $request, $orderId): JsonResponse
    {
        try {
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'can_chat' => false,
                    'reason' => 'Đơn hàng không tồn tại'
                ], 404);
            }

            // Kiểm tra quyền sở hữu đơn hàng
            if (!Auth::check()) {
                return response()->json([
                    'can_chat' => false,
                    'reason' => 'Vui lòng đăng nhập'
                ], 401);
            }

            $user = Auth::user();
            
            // Admin có thể chat với tất cả đơn hàng
            if ($user->isAdmin()) {
                return response()->json([
                    'can_chat' => true,
                    'reason' => null
                ]);
            }

            // Kiểm tra đơn hàng có thuộc về user không
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'can_chat' => false,
                    'reason' => 'Bạn không có quyền truy cập đơn hàng này'
                ], 403);
            }

            // Kiểm tra trạng thái đơn hàng
            $validStatuses = [
                'Chờ xác nhận',
                'Đã xác nhận', 
                'Đang chuẩn bị',
                'Đang giao',
                'Thành công'
            ];

            $orderStatusName = $order->orderStatus->name ?? '';
            
            if (!in_array($orderStatusName, $validStatuses)) {
                return response()->json([
                    'can_chat' => false,
                    'reason' => 'Đơn hàng không trong trạng thái cho phép chat'
                ]);
            }

            // Kiểm tra thời gian nếu đơn hàng đã thành công
            if ($orderStatusName === 'Thành công') {
                $completedAt = $order->updated_at; // Hoặc có thể dùng completed_at nếu có
                $daysSinceCompleted = Carbon::parse($completedAt)->diffInDays(now());
                
                if ($daysSinceCompleted > 7) {
                    return response()->json([
                        'can_chat' => false,
                        'reason' => 'Đơn hàng đã hoàn tất quá 7 ngày'
                    ]);
                }
            }

            return response()->json([
                'can_chat' => true,
                'reason' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Error in canChat: ' . $e->getMessage());
            return response()->json([
                'can_chat' => false,
                'reason' => 'Có lỗi xảy ra, vui lòng thử lại'
            ], 500);
        }
    }

    /**
     * Bắt đầu chat cho đơn hàng hoặc lấy conversation hiện tại
     */
    public function startChat(Request $request, $orderId): JsonResponse
    {
        try {
            // Kiểm tra điều kiện chat trước
            $canChatResponse = $this->canChat($request, $orderId);
            $canChatData = $canChatResponse->getData(true);
            
            if (!$canChatData['can_chat']) {
                return $canChatResponse;
            }

            $order = Order::with(['orderStatus', 'orderItems.book'])->find($orderId);
            $user = Auth::user();

            // Tìm admin theo email cụ thể thay vì role
            $admin = User::where('email', 'admin1@example.com')->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy admin với email admin1@example.com'
                ], 500);
            }

            DB::beginTransaction();

            // Tìm hoặc tạo conversation giữa customer và admin (không phụ thuộc order_id)
            $conversation = Conversation::where('customer_id', $user->id)
                ->where('admin_id', $admin->id)
                ->first();

            Log::info('OrderChat: Looking for conversation', [
                'customer_id' => $user->id,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'existing_conversation' => $conversation ? $conversation->id : 'not found'
            ]);

            $isNewConversation = false;
            if (!$conversation) {
                // Tạo conversation mới nếu chưa có
                $conversation = Conversation::create([
                    'customer_id' => $user->id,
                    'admin_id' => $admin->id,
                    'order_id' => null, // Không gắn order_id vào conversation
                    'last_message_at' => now(),
                ]);
                $isNewConversation = true;
            } else {
                // Cập nhật thời gian tin nhắn cuối
                $conversation->update(['last_message_at' => now()]);
            }

            // Kiểm tra xem đã có tin nhắn system cho order này chưa
            $existingOrderMessage = Message::where('conversation_id', $conversation->id)
                ->where('type', 'system_order_info')
                ->where('content', 'like', '%#' . $order->order_code . '%')
                ->first();

            $systemMessage = null;
            if (!$existingOrderMessage) {
                // Tạo tin nhắn hệ thống với thông tin đơn hàng từ phía customer
                $orderInfo = $this->formatOrderInfo($order);
                
                $systemMessage = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id, // Tin nhắn từ customer về đơn hàng của họ
                    'content' => $orderInfo,
                    'type' => 'system_order_info',
                    'is_auto_reply' => false, // Đây là tin nhắn thật của customer, không phải auto reply
                ]);

                Log::info('Created order info message in existing conversation', [
                    'conversation_id' => $conversation->id,
                    'order_id' => $orderId,
                    'customer_id' => $user->id,
                    'message_id' => $systemMessage->id
                ]);
            }

            // Lấy tất cả tin nhắn của conversation
            $messages = Message::where('conversation_id', $conversation->id)
                ->with(['sender', 'replyToMessage.sender'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Format messages cho frontend
            $formattedMessages = $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name ?? 'Unknown',
                    'is_admin' => $message->sender ? $message->sender->isAdmin() : false,
                    'created_at' => $message->created_at->toISOString(),
                    'file_path' => $message->file_path,
                    'is_auto_reply' => $message->is_auto_reply,
                    'reply_to_message' => $message->replyToMessage ? [
                        'id' => $message->replyToMessage->id,
                        'content' => $message->replyToMessage->content,
                        'sender_name' => $message->replyToMessage->sender->name ?? 'Unknown',
                    ] : null,
                ];
            });

            DB::commit();

            // Trigger event để thông báo cho admin (chỉ khi có tin nhắn mới được tạo)
            if ($systemMessage) {
                broadcast(new MessageSent($systemMessage))->toOthers();
            }

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'messages' => $formattedMessages,
                'new_message_only' => $systemMessage ? [
                    'id' => $systemMessage->id,
                    'content' => $systemMessage->content,
                    'type' => $systemMessage->type,
                    'sender_id' => $systemMessage->sender_id,
                    'sender_name' => $user->name, // Tên customer gửi tin nhắn về đơn hàng
                    'is_admin' => false, // Customer gửi, không phải admin
                    'created_at' => $systemMessage->created_at->toISOString(),
                    'file_path' => null,
                    'is_auto_reply' => $systemMessage->is_auto_reply,
                    'reply_to_message' => null,
                ] : null,
                'order_info' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'status' => $order->orderStatus->name ?? '',
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->format('d/m/Y'),
                    'items_count' => $order->orderItems->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in startChat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo cuộc hội thoại'
            ], 500);
        }
    }

    /**
     * Lấy messages của đơn hàng
     */
    public function getMessages(Request $request, $orderId): JsonResponse
    {
        try {
            // Kiểm tra điều kiện chat trước
            $canChatResponse = $this->canChat($request, $orderId);
            $canChatData = $canChatResponse->getData(true);
            
            if (!$canChatData['can_chat']) {
                return $canChatResponse;
            }

            $user = Auth::user();
            $order = Order::with(['orderStatus', 'orderItems'])->find($orderId);

            // Tìm admin theo email cụ thể
            $admin = User::where('email', 'admin1@example.com')->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy admin với email admin1@example.com'
                ], 500);
            }

            // Tìm conversation giữa customer và admin
            $conversation = Conversation::where('customer_id', $user->id)
                ->where('admin_id', $admin->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có cuộc hội thoại nào'
                ], 404);
            }

            // Lấy tất cả tin nhắn của conversation
            $messages = Message::where('conversation_id', $conversation->id)
                ->with(['sender', 'replyToMessage.sender'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Format messages cho frontend
            $formattedMessages = $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name ?? 'Unknown',
                    'is_admin' => $message->sender ? $message->sender->isAdmin() : false,
                    'created_at' => $message->created_at->toISOString(),
                    'file_path' => $message->file_path,
                    'is_auto_reply' => $message->is_auto_reply,
                    'reply_to_message' => $message->replyToMessage ? [
                        'id' => $message->replyToMessage->id,
                        'content' => $message->replyToMessage->content,
                        'sender_name' => $message->replyToMessage->sender->name ?? 'Unknown',
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'messages' => $formattedMessages,
                'order_info' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'status' => $order->orderStatus->name ?? '',
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->format('d/m/Y'),
                    'items_count' => $order->orderItems->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy tin nhắn'
            ], 500);
        }
    }

    /**
     * Format thông tin đơn hàng thành tin nhắn
     */
    private function formatOrderInfo(Order $order): string
    {
        $orderCode = $order->order_code ?? 'N/A';
        $status = $order->orderStatus->name ?? 'N/A';
        $totalAmount = number_format($order->total_amount, 0, ',', '.') . 'đ';
        $createdDate = $order->created_at->format('d/m/Y');
        $itemsCount = $order->orderItems->count();

        // Format có cấu trúc rõ ràng cho frontend xử lý
        return "Xin chào! Tôi cần hỗ trợ về đơn hàng sau:\n\n" .
               "🛒 Đơn hàng #{$orderCode}\n\n" .
               "📋 Thông tin chi tiết:\n" .
               "• Mã đơn hàng: {$orderCode}\n" .
               "• Ngày đặt: {$createdDate}\n" .
               "• Trạng thái: {$status}\n" .
               "• Tổng tiền: {$totalAmount}\n" .
               "• Số sản phẩm: {$itemsCount} items\n\n" .
               " Vui lòng hỗ trợ tôi!";
    }
}
