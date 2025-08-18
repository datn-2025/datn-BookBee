<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    /**
     * Hiển thị danh sách thông báo admin
     */
    public function index(Request $request)
    {
        // Kiểm tra admin đã đăng nhập
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        
        // Lấy thông báo với phân trang
        $notifications = Notification::where('user_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        // Đếm số thông báo chưa đọc
        $unreadCount = Notification::where('user_id', $admin->id)
            ->whereNull('read_at')
            ->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Lấy tất cả thông báo cho API
     */
    public function all(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $admin = Auth::user()->id;
        
        // Lấy thông báo mới nhất (limit 10)
        $notifications = DB::table('notifications')
            // ->where('notifiable_type', 'App\\Models\\Admin')
            ->where('user_id', $admin)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                // $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'type_id' => $notification->type_id,
                    'is_read' => !is_null($notification->read_at),
                    // 'created_at' => $notification->created_at->format('d/m/Y H:i'),
                    'time_ago' => $this->getTimeAgo($notification->created_at)
                ];
            });
        // dd($notifications);
        // Đếm số thông báo chưa đọc
        $unreadCount = DB::table('notifications')
            // ->where('notifiable_type', 'App\\Models\\Admin')
            ->where('user_id', $admin)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Đánh dấu một thông báo đã đọc
     */
    public function markAsRead(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $admin = Auth::guard('admin')->user();

        $notify = Notification::where('id', $id)
            ->where('user_id', $admin->id)
            ->first();

        if (!$notify) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        $notify->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Thông báo đã được đánh dấu là đã đọc',
            'data' => $notify
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $admin = Auth::guard('admin')->user();
        
        $updated = DB::table('notifications')
            // ->where('notifiable_type', 'App\\Models\\Admin')
            ->where('user_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "Đã đánh dấu {$updated} thông báo là đã đọc",
            'updated_count' => $updated
        ]);
    }

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