<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\RefundRequest;
use App\Models\Order;
use App\Models\RefundRequest as ModelsRefundRequest;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Show the refund request form for the specified order.
     */
    public function create(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if order is eligible for refund - CẬP NHẬT: Đơn hàng "Thành công" + "Đã Thanh Toán"
        if (!$this->isEligibleForRefund($order)) {
            return redirect()->route('account.orders.show', $order->id)
                ->with('error', 'Đơn hàng này không đủ điều kiện để yêu cầu hoàn tiền.');
        }

        // Check if there's already any refund request (ngăn chặn tạo yêu cầu hoàn tiền thứ 2)
        if ($order->refundRequests()->exists()) {
            return redirect()->route('account.orders.show', $order->id)
                ->with('error', 'Đơn hàng này đã có yêu cầu hoàn tiền. Không thể tạo yêu cầu hoàn tiền mới.');
        }

        return view('clients.account.refund-request', compact('order'));
    }

    /**
     * Check if order is eligible for refund
     */
    private function isEligibleForRefund(Order $order): bool
    {
        // Đơn hàng phải có trạng thái "Thành công"
        if ($order->orderStatus->name !== 'Thành công') {
            return false;
        }

        // Đơn hàng phải đã thanh toán và không được trong trạng thái hoàn tiền
        if (!in_array($order->paymentStatus->name, ['Đã Thanh Toán'])) {
            return false;
        }

        // Đơn hàng không được trong trạng thái hoàn tiền
        if (in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
            return false;
        }

        return true;
    }

    /**
     * Store a newly created refund request in storage.
     */
    public function store(Request $request, Order $order)
    {
        // Validate input
        $request->validate([
            'reason' => 'required|string|in:wrong_item,quality_issue,shipping_delay,wrong_qty,other',
            'details' => 'required|string|min:20|max:1000',
            'refund_method' => 'required|string|in:wallet,vnpay',
        ], [
            'reason.required' => 'Vui lòng chọn lý do hoàn tiền.',
            'details.required' => 'Vui lòng mô tả chi tiết lý do hoàn tiền.',
            'details.min' => 'Chi tiết lý do phải có ít nhất 20 ký tự.',
            'details.max' => 'Chi tiết lý do không được vượt quá 1000 ký tự.',
            'refund_method.required' => 'Vui lòng chọn phương thức hoàn tiền.',
            'refund_method.in' => 'Phương thức hoàn tiền không hợp lệ.',
        ]);

        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if order is eligible for refund
        if (!$this->isEligibleForRefund($order)) {
            toastr()->error('Đơn hàng này không đủ điều kiện để yêu cầu hoàn tiền.');
            return redirect()->route('account.orders.show', $order->id);
        }

        // Check if there's already any refund request (ngăn chặn tạo yêu cầu hoàn tiền thứ 2)
        if ($order->refundRequests()->exists()) {
            toastr()->error('Đơn hàng này đã có yêu cầu hoàn tiền. Không thể tạo yêu cầu hoàn tiền mới.');
            return redirect()->route('account.orders.show', $order->id);
        }

        // Validate refund method based on payment method
        $paymentMethod = strtolower($order->paymentMethod->name ?? '');
        if ($request->refund_method === 'vnpay' && !str_contains($paymentMethod, 'vnpay')) {
            toastr()->error('Phương thức hoàn tiền VNPay chỉ áp dụng cho đơn hàng thanh toán qua VNPay.');
            return redirect()->back()->withInput();
        }

        try {
            DB::beginTransaction();

            // Create refund request
            $refundRequest = ModelsRefundRequest::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'details' => $request->details,
                'amount' => $order->total_amount,
                'status' => 'pending',
                'refund_method' => $request->refund_method,
            ]);

            // Cập nhật trạng thái thanh toán đơn hàng thành "Đang Hoàn Tiền"
            $refundingStatus = \App\Models\PaymentStatus::where('name', 'Đang Hoàn Tiền')->first();
            if ($refundingStatus) {
                $order->update(['payment_status_id' => $refundingStatus->id]);
            }

            DB::commit();

            toastr()->success('Yêu cầu hoàn tiền đã được gửi thành công. Chúng tôi sẽ xử lý trong thời gian sớm nhất.');
            return redirect()->route('account.orders.show', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund request failed', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token'])
            ]);
            toastr()->error('Đã xảy ra lỗi khi gửi yêu cầu hoàn tiền. Vui lòng thử lại sau.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the status of a refund request for a specific order.
     */
    public function status(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get the latest refund request for this order
        $refund = $order->refundRequests()
            ->with(['order.user', 'order.paymentMethod'])
            ->latest()
            ->first();

        if (!$refund) {
            return redirect()->route('account.orders.show', $order->id)
                ->with('error', 'Không tìm thấy yêu cầu hoàn tiền cho đơn hàng này.');
        }

        return view('clients.account.refund-status', compact('refund'));
    }
}
