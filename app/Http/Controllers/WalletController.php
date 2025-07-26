<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->latest()->paginate(10);
        return view('wallets.index', compact('wallet', 'transactions'));
    }

    public function showDepositForm()
    {
        $wallet = Auth::user()->wallet;
        // Lấy thông tin ngân hàng từ settings
        $bankSetting = \App\Models\Setting::first()?->bank_setting;
        $bankInfo = $bankSetting ? json_decode($bankSetting, true) : null;
        return view('wallets.deposit', compact('wallet', 'bankInfo'));
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|string'
        ]);
        $user = Auth::user();
        // dd($user);
        $wallet = Wallet::where('user_id', $user->id)->first();
        // dd($wallet);
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }
        $data = [
            'wallet_id' => $wallet->id,
            'type' => 'Nap',
            'amount' => $request->amount,
            'description' => 'NAP - ' . (Auth::user()->phone ?? Auth::user()->name) . ' - ' . time(),
            'status' => 'pending',
            'payment_method' => $request->payment_method
        ];
        if ($request->payment_method === 'bank_transfer') {
            $bankSetting = \App\Models\Setting::first()?->bank_setting;
            $bankInfo = $bankSetting ? json_decode($bankSetting, true) : null;
            // Lưu giao dịch nạp chờ duyệt (pending)
            $pendingTransaction = WalletTransaction::create($data);
            // Tạo URL QR tại controller
            if ($bankInfo) {
                $qrUrl = 'https://img.vietqr.io/image/'
                    . $bankInfo['bank_name'] . '-' . $bankInfo['bank_number'] . '-uOxo31M.jpg'
                    . '?amount=' . $data['amount']
                    . '&addInfo=' . $data['description']
                    . '&accountName=' . urlencode($bankInfo['customer_name']);
            } else {
                $qrUrl = null;
            }
            return view('wallets.qr', [
                'amount' => $data['amount'],
                'bankInfo' => $bankInfo,
                'description' => $data['description'],
                'qrUrl' => $qrUrl,
                'pendingTransaction' => $pendingTransaction,
            ]);
        }

        return $this->depositVnpay($data);
        
    }

    public function depositVnpay($data)
    {
        // dd($data);
        $pendingTransaction = WalletTransaction::create($data);
        // Chuẩn bị dữ liệu cho VNPay
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = route('wallet.vnpayReturn');
        $vnp_TxnRef = $pendingTransaction->id;
        $vnp_OrderInfo = $data['description'];
        $vnp_Amount = (int)($data['amount'] * 100);
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_OrderType" => "other",
        );
        ksort($inputData);
        $query = http_build_query($inputData);
        $hashdata = $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_" && $key != "vnp_SecureHash") {
                $inputData[$key] = $value;
            }
        }
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash !== $vnp_SecureHash) {
            return redirect()->route('wallet.index')->with('error', 'Chữ ký không hợp lệ!');
        }
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef; // transaction id
        $vnp_Amount = $request->vnp_Amount / 100;
        if ($vnp_ResponseCode == '00') {
            $transaction = WalletTransaction::find($vnp_TxnRef);
            if ($transaction && $transaction->status == 'pending') {
                DB::transaction(function () use ($transaction, $vnp_Amount) {
                    $transaction->status = 'success';
                    $transaction->save();
                    $transaction->wallet->increment('balance', $vnp_Amount);
                });
                return redirect()->route('wallet.index')->with('success', 'Nạp ví thành công qua VNPay!');
            }
        }
        return redirect()->route('wallet.index')->with('error', 'Thanh toán không thành công hoặc giao dịch không hợp lệ!');
    }

    public function showWithdrawForm()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        $userBankList = $user->bank_info ? json_decode($user->bank_info, true) : [];
        return view('wallets.withdraw', compact('wallet', 'userBankList'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255'
        ]);
        // dd($request->all());
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $request->amount) {
            toastr()->error('Số dư không đủ để rút tiền!');
            return back()->withInput();
        }
        DB::beginTransaction();
        try {
            // Trừ số dư ví, cộng vào wallet_lock
            $wallet->decrement('balance', $request->amount);
            $user->wallet_lock = ($user->wallet_lock ?? 0) + $request->amount;
            $user->save();
            // Chỉ tạo yêu cầu rút, không trừ tiền thật sự khỏi hệ thống
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'Rut',
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'pending',
                'bank_name' => $request->bank_name,
                'bank_number' => $request->bank_number,
                'customer_name' => $request->customer_name,
                'payment_method' => 'bank_transfer'
            ]);
            DB::commit();
            toastr()->success('Yêu cầu rút tiền đã được gửi, vui lòng đợi admin duyệt!');
            return redirect()->route('wallet.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra, vui lòng thử lại!');
            return back()->withInput();
        }
    }
}
