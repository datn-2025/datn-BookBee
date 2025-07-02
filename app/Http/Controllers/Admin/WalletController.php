<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $totalDeposits = WalletTransaction::where('type', 'deposit')->sum('amount');
        $totalWithdrawals = abs(WalletTransaction::whereIn('type', ['withdrawal', 'payment'])->sum('amount'));

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
            // - Nếu là rút tiền (Rut) và đã duyệt: không ảnh hưởng số dư (vì đã trừ khi tạo yêu cầu)
            // - Nếu là thanh toán (payment): trừ khỏi số dư
            if ($transaction->status === 'success') {
                if ($transaction->type === 'Nap') {
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
            if ($transaction->type === 'Nap') {
                $transaction->wallet->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'Rut') {
                $user = $transaction->wallet->user;
                $user->wallet_lock = max(0, ($user->wallet_lock ?? 0) - $transaction->amount);
                $user->save();
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
