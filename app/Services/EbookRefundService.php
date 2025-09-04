<?php

namespace App\Services;

use App\Models\Order;
use App\Models\EbookDownload;
use App\Models\OrderStatus;
use App\Models\RefundRequest;
use App\Models\PaymentStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EbookRefundService
{
    /**
     * Tính toán số tiền hoàn trả cho ebook dựa trên số lần tải
     * - Chưa tải: 100% hoàn tiền
     * - Tải 1 lần: 40% hoàn tiền
     * - Tải trên 1 lần: Không được hoàn tiền
     */
    public function calculateRefundAmount(Order $order, User $user)
    {
        $totalRefundAmount = 0;
        $refundDetails = [];
        // dd($order->orderItems);
        foreach ($order->orderItems as $item) {
            // Chỉ xử lý ebook
            // dd($item);
            if ($item->bookFormat && $item->bookFormat->format_name === 'Ebook') {
                // Đếm lượt tải theo user + book_format (không phụ thuộc order_item)
                // $downloadCount = EbookDownload::where('user_id', $user->id)
                //     ->where('book_format_id', $item->book_format_id)
                //     ->count();

                $downloadCount = EbookDownload::where('user_id', $user->id)
                    ->where('book_format_id', $item->book_format_id)
                    ->where('order_item_id', $item->id) // thêm điều kiện theo order_item
                    ->count();
                
                $itemTotal = $item->total;
                $refundPercentage = 0;
                $refundAmount = 0;
                $canRefund = false;
                // dd($downloadCount);
                // Logic hoàn tiền mới: đã tải >= 1 lần -> KHÔNG được hoàn tiền
                if ($downloadCount === 0) {
                    // Chưa tải: 100% hoàn tiền
                    $refundPercentage = 100;
                    $refundAmount = $itemTotal;
                    $canRefund = true;
                } else {
                    // Đã tải ít nhất 1 lần: Không được hoàn tiền
                    $refundPercentage = 0;
                    $refundAmount = 0;
                    $canRefund = false;
                }

                // Chỉ cộng vào tổng nếu có thể hoàn tiền
                if ($canRefund) {
                    $totalRefundAmount += $refundAmount;
                }
                
                $refundDetails[] = [
                    'book_title' => $item->book->title ?? 'Không xác định',
                    'original_amount' => $itemTotal,
                    'refund_percentage' => $refundPercentage,
                    'refund_amount' => $refundAmount,
                    'download_count' => $downloadCount,
                    'can_refund' => $canRefund,
                    'refund_status' => $downloadCount === 0
                        ? 'Chưa tải'
                        : ('Đã tải ' . $downloadCount . ' lần - Không thể hoàn tiền')
                ];
            }
        }

        return [
            'total_refund_amount' => $totalRefundAmount,
            'details' => $refundDetails
        ];
    }

    /**
     * Tạo yêu cầu hoàn tiền cho ebook
     */
    public function createEbookRefundRequest(Order $order, User $user, $reason, $details = null)
    {
        DB::beginTransaction();
        // dd($order);
        try {
            // Kiểm tra đơn hàng có ebook không
            $hasEbook = $order->orderItems()->whereHas('bookFormat', function($query) {
                $query->where('format_name', 'Ebook');
            })->exists();

            if (!$hasEbook) {
                toastr()->error('Đơn hàng này không chứa ebook.');
                throw new \Exception('Đơn hàng này không chứa ebook.');
            }

            // Kiểm tra đã có yêu cầu hoàn tiền chưa
            $existingRefund = RefundRequest::where('order_id', $order->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['Chờ xử lý', 'Đang xử lý'])
                ->exists();

            if ($existingRefund) {
                toastr()->error('Đã có yêu cầu hoàn tiền cho đơn hàng này.');
                throw new \Exception('Đã có yêu cầu hoàn tiền cho đơn hàng này.');
            }

            // Tính toán số tiền hoàn trả
            $refundCalculation = $this->calculateRefundAmount($order, $user);
            // dd($refundCalculation);
            if ($refundCalculation['total_refund_amount'] <= 0) {
                toastr()->error('Không có ebook nào đủ điều kiện hoàn tiền. Ebook đã tải ít nhất 1 lần không thể hoàn tiền.');
                throw new \Exception('Không có ebook nào đủ điều kiện hoàn tiền. Ebook đã tải ít nhất 1 lần không thể hoàn tiền.');
            }

            // Tạo yêu cầu hoàn tiền
            $refundRequest = RefundRequest::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'reason' => $reason,
                'details' => $details . "\n\nChi tiết hoàn tiền ebook:\n" . $this->formatRefundDetails($refundCalculation['details']),
                'amount' => $refundCalculation['total_refund_amount'],
                'status' => 'pending',
                'refund_method' => 'wallet' // Mặc định hoàn về ví
            ]);

            // Cập nhật trạng thái thanh toán đơn hàng thành "Đang Hoàn Tiền"
            $refundPaymentStatus = PaymentStatus::where('name', 'Đang Hoàn Tiền')->first();
            $refundOrderStatus = OrderStatus::where('name', 'Đang Hoàn Tiền')->first();
            if ($refundPaymentStatus || $refundOrderStatus) {
                $order->update(['payment_status_id' => $refundPaymentStatus->id, 'order_status_id' => $refundOrderStatus->id]);
            }
            


            DB::commit();

            Log::info('Ebook refund request created', [
                'refund_request_id' => $refundRequest->id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'refund_amount' => $refundCalculation['total_refund_amount'],
                'details' => $refundCalculation['details']
            ]);

            return [
                'success' => true,
                'refund_request' => $refundRequest,
                'refund_calculation' => $refundCalculation
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create ebook refund request', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Định dạng chi tiết hoàn tiền để hiển thị
     */
    private function formatRefundDetails($details)
    {
        $formatted = "";
        foreach ($details as $detail) {
            $status = $detail['refund_status'] ? 'Đã tải' : 'Chưa tải';
            $formatted .= "- {$detail['book_title']}: {$status} - Hoàn {$detail['refund_percentage']}% = " . number_format($detail['refund_amount']) . "đ\n";
        }
        return $formatted;
    }

    /**
     * Kiểm tra đơn hàng có đủ điều kiện hoàn tiền ebook không
     */
    public function canRefundEbook(Order $order, User $user)
    {
        // dd(1);
        // Kiểm tra đơn hàng thuộc về user
        if ($order->user_id !== $user->id) {
            return ['can_refund' => false, 'reason' => 'Đơn hàng không thuộc về bạn.'];
        }

        // Kiểm tra đơn hàng đã thanh toán
        if (!$order->paymentStatus || $order->paymentStatus->name !== 'Đã Thanh Toán') {
            return ['can_refund' => false, 'reason' => 'Đơn hàng chưa được thanh toán.'];
        }

        // Kiểm tra đơn hàng có ebook
        $hasEbook = $order->orderItems()->whereHas('bookFormat', function($query) {
            $query->where('format_name', 'Ebook');
        })->exists();

        if (!$hasEbook) {
            return ['can_refund' => false, 'reason' => 'Đơn hàng không chứa ebook.'];
        }

        // Kiểm tra đã có yêu cầu hoàn tiền chưa
        $existingRefund = RefundRequest::where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->exists();

        if ($existingRefund) {
            return ['can_refund' => false, 'reason' => 'Đã có yêu cầu hoàn tiền cho đơn hàng này.'];
        }

        // Kiểm tra thời hạn hoàn tiền (7 ngày)
        $daysSincePurchase = now()->diffInDays($order->created_at);
        if ($daysSincePurchase > 7) {
            return ['can_refund' => false, 'reason' => 'Đã quá thời hạn hoàn tiền (7 ngày).'];
        }

        // Kiểm tra điều kiện tải xuống cho từng ebook trong đơn hàng
        $hasRefundableEbook = false;
        // dd($order);
        foreach ($order->orderItems as $item) {
            if ($item->bookFormat && $item->bookFormat->format_name === 'Ebook') {
                // Đếm lượt tải theo user + book_format (không phụ thuộc order_item)
                $downloadCount = EbookDownload::where('user_id', $user->id)
                ->where('book_format_id', $item->book_format_id)
                ->where('order_item_id', $item->id) // thêm điều kiện theo order_item
                ->count();
                // Chỉ cho phép hoàn tiền nếu CHƯA tải lần nào
                if ($downloadCount === 0) {
                    $hasRefundableEbook = true;
                    break;
                }
            }
        }
        // dd($order . '1');

        if (!$hasRefundableEbook) {
            return ['can_refund' => false, 'reason' => 'Tất cả ebook trong đơn hàng đã được tải ít nhất 1 lần. Không thể hoàn tiền.'];
        }

        return ['can_refund' => true, 'reason' => null];
    }
}