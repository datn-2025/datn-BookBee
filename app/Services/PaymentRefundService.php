<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\PaymentStatus;
use App\Models\Invoice;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentRefundService
{
    protected $invoiceService;
    protected $emailService;

    public function __construct(InvoiceService $invoiceService = null, EmailService $emailService = null)
    {
        $this->invoiceService = $invoiceService ?? new InvoiceService();
        $this->emailService = $emailService ?? new EmailService();
    }

    public function refundToWallet(Order $order, $amount = null, RefundRequest $refundRequest = null)
    {
        $refundAmount = $amount ?? $order->total_amount;
        
        return DB::transaction(function () use ($order, $refundAmount, $refundRequest) {
            Log::info('Starting wallet refund transaction', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'refund_amount' => $refundAmount
            ]);
            
            // Kiểm tra đơn hàng đã thanh toán chưa
            if ($order->paymentStatus->name !== 'Đã Thanh Toán') {
                throw new \Exception('Đơn hàng chưa thanh toán không thể hoàn tiền');
            }

            // Kiểm tra số tiền hoàn tiền hợp lệ
            if ($refundAmount <= 0) {
                throw new \Exception('Số tiền hoàn tiền phải lớn hơn 0');
            }

            if ($refundAmount > $order->total_amount) {
                throw new \Exception('Số tiền hoàn tiền không được vượt quá tổng giá trị đơn hàng');
            }

            // Tạo hoặc lấy ví của user
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $order->user_id],
                ['balance' => 0]
            );
            
            Log::info('Wallet found/created', [
                'wallet_id' => $wallet->id,
                'user_id' => $order->user_id,
                'current_balance' => $wallet->balance
            ]);

            // Tạo giao dịch hoàn tiền
            Log::info('Creating wallet refund transaction', [
                'wallet_id' => $wallet->id,
                'amount' => $refundAmount,
                'order_id' => $order->id
            ]);
            
            try {
                $refundTransaction = WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $refundAmount,
                    'type' => 'HOANTIEN',
                    'description' => 'Hoàn tiền đơn hàng #' . $order->order_code . ' - Admin duyệt',
                    'related_order_id' => $order->id,
                    'status' => 'Thành Công',
                    'payment_method' => 'wallet'
                ]);
                
                Log::info('Wallet transaction created successfully', [
                    'transaction_id' => $refundTransaction->id,
                    'amount' => $refundAmount,
                    'type' => 'HOANTIEN',
                    'wallet_id' => $wallet->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create wallet transaction', [
                    'error' => $e->getMessage(),
                    'wallet_id' => $wallet->id,
                    'amount' => $refundAmount,
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Không thể tạo giao dịch hoàn tiền: ' . $e->getMessage());
            }

            // Cộng tiền vào ví - LOG trước và sau
            $oldBalance = $wallet->balance;
            Log::info('Before incrementing wallet balance', [
                'wallet_id' => $wallet->id,
                'old_balance' => $oldBalance,
                'increment_amount' => $refundAmount
            ]);
            
            // Cộng tiền vào ví
            $wallet->increment('balance', $refundAmount);
            $wallet->refresh(); // Làm mới để lấy balance mới
            
            Log::info('After incrementing wallet balance', [
                'user_id' => $order->user_id,
                'wallet_id' => $wallet->id,
                'old_balance' => $oldBalance,
                'refund_amount' => $refundAmount,
                'new_balance' => $wallet->balance,
                'transaction_id' => $refundTransaction->id,
                'balance_change_successful' => ($wallet->balance == ($oldBalance + $refundAmount))
            ]);

            // Cập nhật trạng thái thanh toán đơn hàng
            $refundedStatus = PaymentStatus::where('name', 'Đã Hoàn Tiền')->first();
            if ($refundedStatus) {
                $order->update(['payment_status_id' => $refundedStatus->id]);
            }

            // Tạo hóa đơn hoàn tiền nếu có refundRequest
            // dd($refundRequest);
            if ($refundRequest) {
                // dd($order, $refundRequest);
                $invoice = $this->invoiceService->createRefundInvoice($order, $refundRequest);
                // Gửi email hóa đơn hoàn tiền
                try {
                    $this->emailService->sendRefundInvoice($invoice, $refundRequest);
                    Log::info('Refund invoice email sent successfully', [
                        'invoice_id' => $invoice->id,
                        'order_id' => $order->id,
                        'user_email' => $order->user->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send refund invoice email', [
                        'error' => $e->getMessage(),
                        'invoice_id' => $invoice->id
                    ]);
                }
            }

            Log::info('Wallet refund transaction completed successfully', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'user_id' => $order->user_id,
                'refund_amount' => $refundAmount,
                'transaction_id' => $refundTransaction->id,
                'wallet_new_balance' => $wallet->balance
            ]);

            return $refundTransaction;
        });
    }

    public function refundVnpay(Order $order, RefundRequest $refundRequest = null)
    {
        return DB::transaction(function () use ($order, $refundRequest) {
            if ($order->paymentStatus->name !== 'Đã Thanh Toán') {
                throw new \Exception('Đơn hàng chưa thanh toán không thể hoàn tiền');
            }

            $payment = $order->payments->first();
            if (!$payment || !$payment->transaction_id) {
                Log::warning('No payment transaction found for VNPay refund', [
                    'order_id' => $order->id,
                    'payment_method' => $order->paymentMethod->name ?? 'unknown'
                ]);

                $processingStatus = PaymentStatus::firstOrCreate(
                    ['name' => 'Đang Xử Lý Hoàn Tiền'],
                    ['description' => 'Đơn hàng đang được xử lý hoàn tiền thủ công']
                );

                $order->update(['payment_status_id' => $processingStatus->id]);
                return true;
            }

            Log::info('Starting VNPay refund process', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'transaction_id' => $payment->transaction_id
            ]);

            try {
                $response = $this->callVnpayRefund($order, $payment);

                if (isset($response['vnp_ResponseCode']) && $response['vnp_ResponseCode'] === '00') {
                    $refundedStatus = PaymentStatus::firstOrCreate(
                        ['name' => 'Đã Hoàn Tiền'],
                        ['description' => 'Đơn hàng đã được hoàn tiền thành công']
                    );

                    $order->update(['payment_status_id' => $refundedStatus->id]);

                    // Tạo hóa đơn hoàn tiền nếu có refundRequest
                    if ($refundRequest) {
                        $invoice = $this->invoiceService->createRefundInvoice($order, $refundRequest);
                        // Gửi email hóa đơn hoàn tiền
                        try {
                            $this->emailService->sendRefundInvoice($invoice, $refundRequest);
                            Log::info('VNPay refund invoice email sent successfully', [
                                'invoice_id' => $invoice->id,
                                'order_id' => $order->id,
                                'user_email' => $order->user->email
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send VNPay refund invoice email', [
                                'error' => $e->getMessage(),
                                'invoice_id' => $invoice->id
                            ]);
                        }
                    }

                    Log::info('VNPay refund successful', [
                        'order_id' => $order->id,
                        'response_code' => $response['vnp_ResponseCode']
                    ]);

                    return true;
                } else {
                    Log::warning('VNPay refund failed', [
                        'order_id' => $order->id,
                        'response_code' => $response['vnp_ResponseCode'] ?? 'unknown',
                        'response' => $response
                    ]);

                    $processingStatus = PaymentStatus::firstOrCreate(
                        ['name' => 'Đang Xử Lý Hoàn Tiền'],
                        ['description' => 'VNPay hoàn tiền không thành công, cần xử lý thủ công']
                    );

                    $order->update(['payment_status_id' => $processingStatus->id]);
                    return false;
                }
            } catch (\Exception $e) {
                Log::error('VNPay refund API call failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $processingStatus = PaymentStatus::firstOrCreate(
                    ['name' => 'Đang Xử Lý Hoàn Tiền'],
                    ['description' => 'Có lỗi khi gọi API VNPay, cần xử lý thủ công']
                );

                $order->update(['payment_status_id' => $processingStatus->id]);
                return false;
            }
        });
    }

    private function callVnpayRefund(Order $order, $payment)
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_ApiUrl = config('vnpay.api_url') . '/merchant_webapi/api/transaction';

        $vnp_RequestId = date('YmdHis') . rand(100000, 999999);
        $vnp_Version = '2.1.0';
        $vnp_Command = 'refund';
        $vnp_TxnRef = $payment->transaction_id;
        $vnp_Amount = $order->total_amount * 100;
        $vnp_TransactionNo = $payment->transaction_id;
        $vnp_TransactionDate = $payment->created_at->format('YmdHis');
        $vnp_CreateBy = Auth::user()->name ?? 'System';
        $vnp_IpAddr = request()->ip();

        $inputData = [
            'vnp_RequestId' => $vnp_RequestId,
            'vnp_Version' => $vnp_Version,
            'vnp_Command' => $vnp_Command,
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_TransactionType' => '02', // refund
            'vnp_TxnRef' => $vnp_TxnRef,
            'vnp_Amount' => $vnp_Amount,
            'vnp_OrderInfo' => 'Refund order #' . $order->id,
            'vnp_TransactionNo' => $vnp_TransactionNo,
            'vnp_TransactionDate' => $vnp_TransactionDate,
            'vnp_CreateBy' => $vnp_CreateBy,
            'vnp_IpAddr' => $vnp_IpAddr,
        ];

        // Tạo secure hash
        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . '=' . $value . '&';
        }
        $hashData = rtrim($hashData, '&');

        $inputData['vnp_SecureHash'] = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Gửi yêu cầu POST tới VNPay
        $response = Http::post($vnp_ApiUrl, $inputData);

        return $response->json();
    }
}
