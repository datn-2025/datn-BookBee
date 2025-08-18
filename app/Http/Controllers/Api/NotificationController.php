<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của user hiện tại
     * Giới hạn 3 thông báo mới nhất
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }

            // Lấy tối đa 3 thông báo mới nhất
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                // ->limit(3)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'type_id' => $notification->type_id,
                        'is_read' => !is_null($notification->read_at),
                        'created_at' => $notification->created_at->format('d/m/Y H:i'),
                        'time_ago' => $this->getTimeAgo($notification->created_at)
                    ];
                });

            // Đếm tổng số thông báo chưa đọc
            $unreadCount = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                    'unread_count' => $unreadCount,
                    'total_count' => $notifications->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông báo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tất cả thông báo của user hiện tại với phân trang
     */
    public function all(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }

            $perPage = $request->get('per_page', 15); // Mặc định 15 thông báo mỗi trang
            $page = $request->get('page', 1);

            // Lấy tất cả thông báo với phân trang
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'type_id' => $notification->type_id, 
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->format('d/m/Y H:i'),
                    'time_ago' => $this->getTimeAgo($notification->created_at)
                ];
            });

            // Đếm tổng số thông báo chưa đọc
            $unreadCount = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $formattedNotifications,
                    'unread_count' => $unreadCount,
                    'total_count' => $notifications->total(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông báo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }

            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu thông báo đã đọc'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông báo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }

            Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu tất cả thông báo đã đọc'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông báo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tính thời gian đã trước
     */
    private function getTimeAgo($datetime)
    {
         $time = $datetime instanceof Carbon 
        ? $datetime 
        : Carbon::parse($datetime, 'Asia/Ho_Chi_Minh'); // ép timezone

        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $diffInMinutes = ceil($time->diffInMinutes($now));

        if ($diffInMinutes < 1) {
            return 'Vừa xong';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' phút trước';
        } elseif ($diffInMinutes < 1440) {
            $hours = floor($diffInMinutes / 60);
            return $hours . ' giờ trước';
        } else {
            $days = floor($diffInMinutes / 1440);
            return $days . ' ngày trước';
        }
    }
}