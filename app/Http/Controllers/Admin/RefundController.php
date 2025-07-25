<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    protected $paymentRefundService;

    public function __construct(PaymentRefundService $paymentRefundService)
    {
        $this->paymentRefundService = $paymentRefundService;
    }

    /**
     * Display a listing of all refund requests.
     */
    public function index(Request $request)
    {
        $query = RefundRequest::with(['order.user', 'order.paymentMethod', 'order.orderStatus'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by refund method
        if ($request->filled('refund_method')) {
            $query->where('refund_method', $request->refund_method);
        }

        // Search by order code or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                               ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $refunds = $query->paginate(20);

        return view('admin.orders.refunds.index', compact('refunds'));
    }

    /**
     * Show the specified refund request.
     */
    public function show(RefundRequest $refund)
    {
        // dd(1);
        $refund->load(['order.user', 'order.paymentMethod', 'order.orderStatus', 'order.paymentStatus']);
        // dd($refund);
        return view('admin.orders.refunds.show', compact('refund'));
    }

    /**
     * Process (approve/reject) a refund request.
     */
    public function process(Request $request, RefundRequest $refund)
    {
        // dd($request->all());
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // dd($refund, $request->admin_note);
            if ($request->action === 'approve') {
                $this->approveRefund($refund, $request->admin_note);
                $message = 'Đã duyệt yêu cầu hoàn tiền thành công.';
            } else {
                $this->rejectRefund($refund, $request->admin_note);
                $message = 'Đã từ chối yêu cầu hoàn tiền.';
            }

            DB::commit();
            return redirect()->route('admin.refunds.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing refund request', [
                'refund_id' => $refund->id,
                'action' => $request->action,
                'error' => $e->getMessage()
            ]);
            toastr()->error('Đã xảy ra lỗi khi xử lý yêu cầu hoàn tiền. Vui lòng thử lại sau.');
            return back()->with('error', 'Đã xảy ra lỗi khi xử lý yêu cầu hoàn tiền.');
        }
    }

    /**
     * Approve refund request and process refund.
     */
    private function approveRefund(RefundRequest $refund, $adminNote = null)
    {
        // dd($refund->refund_method);
        $order = $refund->order;
        
        // Update refund request status
        $refund->update([
            'status' => 'processing',
            'admin_note' => $adminNote,
            'processed_at' => now(),
        ]);

        // Process refund based on method
        if ($refund->refund_method === 'wallet') {
            $this->processWalletRefund($order, $refund->amount, $refund);
        } elseif ($refund->refund_method === 'vnpay') {
            $this->processVnpayRefund($order, $refund->amount, $refund);
        }

        // Update refund status to completed
        $refund->update(['status' => 'completed']);

        // Log the action
        Log::info('Refund approved and processed', [
            'refund_id' => $refund->id,
            'order_id' => $order->id,
            'amount' => $refund->amount,
            'method' => $refund->refund_method,
            'admin_note' => $adminNote
        ]);
    }

    /**
     * Reject refund request.
     */
    private function rejectRefund(RefundRequest $refund, $adminNote = null)
    {
        $refund->update([
            'status' => 'rejected',
            'admin_note' => $adminNote,
            'processed_at' => now(),
        ]);

        Log::info('Refund rejected', [
            'refund_id' => $refund->id,
            'order_id' => $refund->order_id,
            'admin_note' => $adminNote
        ]);
    }

    /**
     * Process wallet refund.
     */
    private function processWalletRefund(Order $order, $amount, RefundRequest $refundRequest = null)
    {
        // dd(1);
        try {
            $result = $this->paymentRefundService->refundToWallet($order, $amount, $refundRequest);
            
            if ($result) {
                Log::info('Wallet refund processed successfully', [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'transaction_id' => $result->id
                ]);
                
                return true;
            } else {
                Log::warning('Wallet refund returned false', [
                    'order_id' => $order->id,
                    'amount' => $amount
                ]);
                
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Wallet refund failed', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Không throw exception để không crash UI
            // throw new \Exception('Có lỗi xảy ra khi xử lý yêu cầu hoàn tiền vào ví: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process VNPay refund.
     */
    private function processVnpayRefund(Order $order, $amount, RefundRequest $refundRequest = null)
    {
        try {
            $result = $this->paymentRefundService->refundVnpay($order, $refundRequest);
            
            if ($result) {
                Log::info('VNPay refund processed successfully', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'amount' => $amount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('VNPay refund failed', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            
            // Có thể throw lại exception hoặc xử lý theo cách khác
            throw new \Exception('Có lỗi xảy ra khi xử lý yêu cầu hoàn tiền VNPay: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for refund dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_requests' => RefundRequest::count(),
            'pending_requests' => RefundRequest::where('status', 'pending')->count(),
            'processing_requests' => RefundRequest::where('status', 'processing')->count(),
            'completed_requests' => RefundRequest::where('status', 'completed')->count(),
            'rejected_requests' => RefundRequest::where('status', 'rejected')->count(),
            'total_refund_amount' => RefundRequest::where('status', 'completed')->sum('amount'),
            'wallet_refunds' => RefundRequest::where('refund_method', 'wallet')->where('status', 'completed')->count(),
            'vnpay_refunds' => RefundRequest::where('refund_method', 'vnpay')->where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }
}
