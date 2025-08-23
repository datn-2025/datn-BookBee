<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Events\RefundRequested;
use App\Events\OrderCancelled;

class NotificationService
{
    /**
     * Tạo thông báo thanh toán thành công cho user
     */
    public function createPaymentSuccessNotification(Order $order, User $user)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'payment_success',
                'type_id' => $order->id,
                'title' => 'Thanh toán thành công',
                'message' => "Thanh toán đơn hàng #{$order->order_code} đã thành công với số tiền " . number_format((float)$order->total_amount, 0, ',', '.') . 'đ',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'amount' => $order->total_amount,
                    'payment_method' => $order->paymentMethod->name ?? 'Không xác định'
                ]
            ]);

            Log::info('Payment success notification created', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_code' => $order->order_code
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create payment success notification', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Tạo thông báo yêu cầu hoàn tiền cho admin
     */
    public function createRefundRequestNotificationForAdmin(Order $order, $reason = null, $refundAmount = null)
    {
        try {
            $amount = $refundAmount ?? $order->total_amount;

            // Dispatch event để broadcast cho admin realtime và tự động tạo thông báo
            event(new RefundRequested($order, $reason ?? 'Yêu cầu hoàn tiền', (float)$amount));

            Log::info('Refund request event dispatched', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'refund_amount' => $amount
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to create refund request notifications for admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Tạo thông báo hủy đơn hàng cho admin
     */
    public function createOrderCancellationNotificationForAdmin(Order $order, $reason = null, $refundAmount = null)
    {
        try {
            // Dispatch event để broadcast cho admin realtime và tự động tạo thông báo
            event(new OrderCancelled($order, $reason ?? 'Hủy đơn hàng', (float)($refundAmount ?? 0)));

            Log::info('Order cancellation event dispatched', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'cancellation_reason' => $reason,
                'refund_amount' => $refundAmount
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to create order cancellation notifications for admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
}