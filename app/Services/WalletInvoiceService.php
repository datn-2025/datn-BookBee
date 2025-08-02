<?php

namespace App\Services;

use App\Models\WalletTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Mail\WalletWithdrawInvoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WalletInvoiceService
{
    /**
     * Tạo và gửi hóa đơn rút tiền qua email
     */
    public function createAndSendWithdrawInvoice(WalletTransaction $transaction)
    {
        try {
            // Tạo hóa đơn rút tiền
            $invoice = $this->createWithdrawInvoice($transaction);
            
            // Gửi email hóa đơn
            $this->sendWithdrawInvoiceEmail($transaction, $invoice);
            
            Log::info('Withdraw invoice created and sent successfully', [
                'transaction_id' => $transaction->id,
                'invoice_id' => $invoice->id,
                'user_id' => $transaction->wallet->user_id
            ]);
            
            return $invoice;
            
        } catch (\Exception $e) {
            Log::error('Failed to create and send withdraw invoice', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Tạo hóa đơn rút tiền
     */
    private function createWithdrawInvoice(WalletTransaction $transaction)
    {
        $user = $transaction->wallet->user;
        
        // Tạo invoice
        $invoice = Invoice::create([
            'invoice_number' => 'WD-' . date('Ymd') . '-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'order_id' => null, // Không có order_id cho rút tiền
            'total_amount' => $transaction->amount,
            'issued_at' => now(),
            'type' => 'withdraw' // Loại hóa đơn rút tiền
        ]);
        
        // Tạo invoice item cho giao dịch rút tiền
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'book_id' => null,
            'collection_id' => null,
            'book_format_id' => null,
            'item_name' => 'Rút tiền từ ví điện tử',
            'quantity' => 1,
            'unit_price' => $transaction->amount,
            'total_price' => $transaction->amount
        ]);
        
        return $invoice;
    }
    
    /**
     * Gửi email hóa đơn rút tiền
     */
    private function sendWithdrawInvoiceEmail(WalletTransaction $transaction, Invoice $invoice)
    {
        $user = $transaction->wallet->user;
        
        // Load relationships cần thiết
        $invoice->load(['user', 'invoiceItems']);
        
        // Lấy thông tin cài đặt cửa hàng
        $storeSettings = \App\Models\Setting::first();
        
        // Gửi email
        Mail::to($user->email)->send(new WalletWithdrawInvoice($transaction, $invoice, $storeSettings));
        
        Log::info('Withdraw invoice email sent', [
            'transaction_id' => $transaction->id,
            'invoice_id' => $invoice->id,
            'user_email' => $user->email
        ]);
    }
}