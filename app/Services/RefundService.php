<?php

namespace App\Services;

use App\Models\Order;

class RefundService
{
    /**
     * Check if an order is eligible for refund
     *
     * @param Order $order
     * @return bool
     */
    public function isEligibleForRefund(Order $order): bool
    {
        // Order must be cancelled
        if ($order->order_status_id !== 7) { // 7 is the ID for "Đã hủy" status
            return false;
        }

        // Order must be paid
        if ($order->payment_status_id !== 2) { // 2 is the ID for "Đã Thanh Toán" status
            return false;
        }

        // No existing pending or processing refund requests
        if ($order->refundRequests()->whereIn('status', ['pending', 'processing'])->exists()) {
            return false;
        }

        // Order must not be already refunded
        if ($order->refundRequests()->where('status', 'completed')->exists()) {
            return false;
        }

        // Order must be within refund period (e.g., 30 days)
        $refundPeriod = config('app.refund_period', 30); // Get from config or default to 30 days
        if ($order->created_at->addDays($refundPeriod)->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Process the refund request
     * This will be called by the admin when approving a refund
     *
     * @param Order $order
     * @return bool
     */
    public function processRefund(Order $order): bool
    {
        try {
            // Find the latest pending refund request
            $refundRequest = $order->refundRequests()->where('status', 'pending')->latest()->first();
            
            if (!$refundRequest) {
                return false;
            }

            // Update refund request status to processing
            $refundRequest->update(['status' => 'processing']);

            // Based on payment method, initiate refund
            switch ($order->payment_method_id) {
                case 1: // COD
                    // Should not happen as COD orders don't need refund
                    return false;

                case 2: // VNPay
                    // Implement VNPay refund logic here
                    // You'll need to call VNPay's refund API
                    break;

                case 3: // Wallet
                    // Add money back to user's wallet
                    $order->user->wallet->increment('balance', $refundRequest->amount);
                    break;

                default:
                    return false;
            }

            // Mark refund request as completed
            $refundRequest->update(['status' => 'completed']);

            // Send notification to user
            // TODO: Implement notification logic

            return true;
        } catch (\Exception $e) {
            // Log error
            \Log::error('Refund processing failed: ' . $e->getMessage());
            return false;
        }
    }
}
