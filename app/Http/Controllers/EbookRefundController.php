<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\EbookRefundService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class EbookRefundController extends Controller
{
    protected $ebookRefundService;
    public $notificationService;

    public function __construct(EbookRefundService $ebookRefundService, NotificationService $notificationService)
    {
        $this->ebookRefundService = $ebookRefundService;
        $this->notificationService = $notificationService;
    }

    /**
     * Hiển thị form yêu cầu hoàn tiền ebook
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if ($order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        // Kiểm tra điều kiện hoàn tiền
        $canRefund = $this->ebookRefundService->canRefundEbook($order, $user);
        // dd($canRefund);
        if (!$canRefund['can_refund']) {
            Toastr::error($canRefund['reason']);
            return redirect()->route('orders.show', $order->id);
        }

        // Tính toán số tiền hoàn trả
        $refundCalculation = $this->ebookRefundService->calculateRefundAmount($order, $user);
        // dd($refundCalculation);
        return view('ebook-refund.show', compact('order', 'refundCalculation'));
    }

    /**
     * Tạo yêu cầu hoàn tiền ebook
     */
    public function store(Request $request, Order $order)
    {
        // dd($request->all());
        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000'
        ], [
            'reason.required' => 'Vui lòng nhập lý do hoàn tiền',
            'reason.max' => 'Lý do hoàn tiền không được quá 255 ký tự',
            'details.max' => 'Chi tiết không được quá 1000 ký tự'
        ]);

        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if ($order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        // Tạo yêu cầu hoàn tiền
        $result = $this->ebookRefundService->createEbookRefundRequest(
            $order, 
            $user, 
            $request->reason, 
            $request->details
        );

        $this->notificationService->createRefundRequestNotificationForAdmin(
            $order, $request->reason,
            $order->paymentStatus->name === 'Đã Thanh Toán' ? $order->total_amount : 0
        );

        if ($result['success']) {
            Toastr::success('Yêu cầu hoàn tiền đã được gửi thành công. Chúng tôi sẽ xem xét và phản hồi trong vòng 24-48 giờ.');
            return redirect()->route('orders.show', $order->id);
        } else {
            Toastr::error($result['message']);
            return back()->withInput();
        }
    }

    /**
     * Xem trước số tiền hoàn trả (AJAX)
     */
    public function preview(Order $order)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Kiểm tra điều kiện hoàn tiền
        $canRefund = $this->ebookRefundService->canRefundEbook($order, $user);
        
        if (!$canRefund['can_refund']) {
            return response()->json([
                'error' => $canRefund['reason']
            ], 400);
        }

        // Tính toán số tiền hoàn trả
        $refundCalculation = $this->ebookRefundService->calculateRefundAmount($order, $user);

        return response()->json([
            'success' => true,
            'refund_calculation' => $refundCalculation
        ]);
    }
}
