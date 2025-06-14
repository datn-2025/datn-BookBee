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
        if ($search = $request->input('search')) {
            $query->whereHas('wallet.user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Lọc theo loại giao dịch
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Lọc theo khoảng thời gian
        if ($dateRange = $request->input('date_range')) {
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
        $transactions = $query->latest()->paginate(10);

        return view('admin.wallets.index', compact(
            'transactions',
            'totalWallets',
            'totalTransactions',
            'totalDeposits',
            'totalWithdrawals'
        ));
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
