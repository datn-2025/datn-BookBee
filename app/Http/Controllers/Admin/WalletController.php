<?php

namespace App\Http\Controllers\Admin;

use App\Events\WalletDeposited;
use App\Events\WalletWithdrawn;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        // Truy vấn giao dịch ví thay vì ví
        $query = WalletTransaction::with(['wallet.user']);

        // Lọc theo tìm kiếm
        $search = $request->input('search');
        if ($search) {
            $query->whereHas('wallet.user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Lọc theo loại giao dịch
        $type = $request->input('type');
        if ($type) {
            $query->where('type', $type);
        }

        // Lọc theo trạng thái
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Lọc theo khoảng thời gian
        $dateRange = $request->input('date_range');
        if ($dateRange) {
            $dates = explode(' đến ', $dateRange);
            if (count($dates) == 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Thống kê tổng quan
        $totalWallets = Wallet::count();
        $totalTransactions = WalletTransaction::count();
        // Tổng nạp: chỉ tính các giao dịch nạp đã duyệt ("Nap"), hỗ trợ phân biệt hoa thường
        $totalDeposits = WalletTransaction::whereIn('type', ['Nap', 'NAP'])
            ->where('status', 'success')
            ->sum('amount');
        // Tổng chi tiêu: thanh toán đơn hàng (payment) + rút tiền (Rut) đã duyệt
        $totalWithdrawals = WalletTransaction::where(function ($q) {
                $q->whereIn('type', ['payment', 'PAYMENT'])
                  ->orWhereIn('type', ['Rut', 'RUT']);
            })
            ->where('status', 'success')
            ->sum('amount');

        // Phân trang các giao dịch
        $transactions = $query->where('status' , '!=', 'pending')->latest()->paginate(10)->appends($request->all());

        // Tính số dư sau giao dịch cho từng transaction
        $previousBalances = [];
        $afterBalances = [];
        foreach ($transactions as $transaction) {
            // Tổng các giao dịch đã duyệt trước thời điểm hiện tại (không tính giao dịch hiện tại)
            $sumBefore = WalletTransaction::where('wallet_id', $transaction->wallet_id)
                ->where('status', 'success')
                ->where('created_at', '<', $transaction->created_at)
                ->sum('amount');
            $previousBalances[$transaction->id] = $sumBefore;
            
            // Số dư sau giao dịch: 
            // - Nếu là nạp tiền (Nap) và đã duyệt: cộng vào số dư
            // - Nếu là hoàn tiền (HoanTien) và đã duyệt: cộng vào số dư  
            // - Nếu là rút tiền (Rut) và đã duyệt: không ảnh hưởng số dư (vì đã trừ khi tạo yêu cầu)
            // - Nếu là thanh toán (payment): trừ khỏi số dư
            if ($transaction->status === 'success') {
                if ($transaction->type === 'Nap' || $transaction->type === 'HOANTIEN') {
                    // Nạp tiền và hoàn tiền: cộng vào số dư
                    $afterBalances[$transaction->id] = $sumBefore + $transaction->amount;
                } elseif ($transaction->type === 'Rut') {
                    // Rút tiền: số dư không thay đổi vì đã trừ khi tạo yêu cầu
                    $afterBalances[$transaction->id] = $sumBefore;
                } else {
                    // Payment hoặc các loại khác: trừ từ số dư
                    $afterBalances[$transaction->id] = $sumBefore - abs($transaction->amount);
                }
            } else {
                // Giao dịch chưa duyệt hoặc thất bại: không ảnh hưởng số dư
                $afterBalances[$transaction->id] = $sumBefore;
            }
        }

        // Truyền lại các filter để giữ trạng thái
        return view('admin.wallets.index', compact(
            'transactions',
            'totalWallets',
            'totalTransactions',
            'totalDeposits',
            'totalWithdrawals',
            'previousBalances',
            'afterBalances',
            'search',
            'type',
            'status',
            'dateRange',
        ));
    }

    public function depositHistory(Request $request)
    {
        $query = WalletTransaction::with(['wallet.user'])
            ->where('type', 'Nap');

        // Lọc theo user
        if ($userId = $request->input('user_id')) {
            $query->whereHas('wallet', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        // Lọc theo thời gian
        if ($dateRange = $request->input('date_range')) {
            $dates = explode(' đến ', $dateRange);
            if (count($dates) == 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }
        $depositTransactions = $query->latest()->paginate(10);
        return view('admin.wallets.deposit', compact('depositTransactions'));
    }

    public function withdrawHistory(Request $request)
    {
        $query = WalletTransaction::with(['wallet.user'])
            ->where('type', 'Rut');
        // Lọc theo tên/email người dùng
        if ($user = $request->input('user')) {
            $query->whereHas('wallet.user', function ($q) use ($user) {
                $q->where('name', 'like', "%$user%")
                  ->orWhere('email', 'like', "%$user%");
            });
        }
        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        // Lọc theo thời gian
        if ($dateRange = $request->input('date_range')) {
            $dates = explode(' đến ', $dateRange);
            if (count($dates) == 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }
        $withdrawTransactions = $query->latest()->paginate(10)->appends($request->all());
        return view('admin.wallets.withdraw', compact('withdrawTransactions'));
    }

    public function approveTransaction($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt giao dịch đang chờ duyệt!');
        }
        
        DB::transaction(function () use ($transaction) {
            $transaction->status = 'success';
            $transaction->save();
            
            // Nếu là nạp thì cộng tiền, nếu là rút thì trừ wallet_lock
            if ($transaction->type === 'NAP') {
                $transaction->wallet->increment('balance', $transaction->amount);
                // dd(Auth::user());
                event(new WalletDeposited($transaction->wallet->user, $transaction->amount ,$transaction->id, 'customer'));
            } elseif ($transaction->type === 'RUT') {
                // dd($transaction->wallet->user);
                $user = $transaction->wallet->user;
                $user->wallet_lock = max(0, ($user->wallet_lock ?? 0) - $transaction->amount);
                $user->save();
                event(new WalletWithdrawn($user, $transaction->amount ,$transaction->id, 'customer'));
                // Tạo và gửi hóa đơn rút tiền
                try {
                    $walletInvoiceService = new WalletInvoiceService();
                    $walletInvoiceService->createAndSendWithdrawInvoice($transaction);
                    
                    Log::info('Withdraw invoice created and sent after admin approval', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount' => $transaction->amount
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create withdraw invoice after admin approval', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                    // Không throw exception để không ảnh hưởng đến việc duyệt giao dịch
                }
            }
        });
        
        return back()->with('success', 'Duyệt giao dịch thành công!');
    }

    public function rejectTransaction($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối giao dịch đang chờ duyệt!');
        }
        
        DB::transaction(function () use ($transaction) {
            $transaction->status = 'failed';
            $transaction->save();
            
            if ($transaction->type === 'Rut') {
                $user = $transaction->wallet->user;
                $user->wallet_lock = max(0, ($user->wallet_lock ?? 0) - $transaction->amount);
                $user->save();
                
                $transaction->wallet->increment('balance', $transaction->amount);
            }
        });
        
        return back()->with('success', 'Từ chối giao dịch thành công!');
    }

    public function generateTransactionPdf($id)
    {
        $transaction = WalletTransaction::with(['wallet.user'])->findOrFail($id);
        
        $pdf = PDF::loadView('admin.wallets.transaction-pdf', compact('transaction'));
        
        return $pdf->download('wallet-transaction-' . $transaction->id . '.pdf');
    }

//    public function show(Wallet $wallet)
//    {
//        $wallet->load('user');
//
//        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
//            ->latest()
//            ->paginate(5);
//
//        return view('admin.wallets.show', compact('wallet','transactions'));
//    }
}
