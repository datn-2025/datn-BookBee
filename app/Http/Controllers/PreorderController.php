<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Preorder;
use App\Models\BookFormat;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreorderConfirmation;
use App\Services\GhnService;
use App\Services\PaymentService;

class PreorderController extends Controller
{
    protected $ghnService;
    protected $paymentService;

    public function __construct(GhnService $ghnService, PaymentService $paymentService)
    {
        $this->ghnService = $ghnService;
        $this->paymentService = $paymentService;
    }

    /**
     * Hiển thị form đặt trước sách
     */
    public function create(Book $book)
    {
        if (!$book->canPreorder()) {
            return redirect()->back()->with('error', 'Sách này không thể đặt trước.');
        }

        $formats = $book->formats()->get();
        $attributes = $book->bookAttributeValues()->with('attributeValue.attribute')->get();
        
        // Chỉ hiển thị VNPay và Ví điện tử cho preorder
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->where(function($query) {
                $query->where('name', 'like', '%vnpay%')
                      ->orWhere('name', 'like', '%ví điện tử%')
                      ->orWhere('name', 'like', '%Ví điện tử%')
                      ->orWhere('name', 'like', '%wallet%');
            })
            ->get();
            
        $wallet = Wallet::where('user_id', Auth::id())->first();
        
        // Truyền thêm preorder_discount_percent để tính toán giá
        $preorderDiscountPercent = $book->preorder_discount_percent ?? 0;
        
        return view('preorders.create', compact('book', 'formats', 'attributes', 'paymentMethods', 'wallet', 'preorderDiscountPercent'));
    }

    /**
     * Lưu đơn đặt trước
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_format_id' => 'nullable|exists:book_formats,id',
            'quantity' => 'required|integer|min:1|max:10',
            'selected_attributes' => 'nullable|array',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'district_code' => 'nullable|string', 
            'district_name' => 'nullable|string',
            'ward_code' => 'nullable|string',
            'ward_name' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $bookFormat = $validated['book_format_id'] ? BookFormat::findOrFail($validated['book_format_id']) : null;
        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);

        if (!$book->canPreorder()) {
            return back()->with('error', 'Sách này không thể đặt trước.');
        }

        // Kiểm tra định dạng có phải ebook không
        $isEbook = $bookFormat && strtolower($bookFormat->format_name) === 'ebook';

        // Nếu là sách vật lý, bắt buộc phải có địa chỉ
        if (!$isEbook && (!$validated['address'] || !$validated['province_code'])) {
            return back()->with('error', 'Vui lòng nhập đầy đủ địa chỉ giao hàng cho sách vật lý.');
        }
        //  dd($request->all());
        try {
            DB::beginTransaction();

            // Tính giá cơ bản
            $basePrice = $book->getPreorderPrice($bookFormat);
            
            // Làm sạch thuộc tính được chọn: loại bỏ giá trị rỗng/null
            $selectedAttributes = [];
            if (!empty($validated['selected_attributes']) && is_array($validated['selected_attributes'])) {
                foreach ($validated['selected_attributes'] as $k => $v) {
                    if ($v !== null && $v !== '') {
                        $selectedAttributes[$k] = $v;
                    }
                }
            }

            // Tính giá thêm từ thuộc tính (chỉ với sách vật lý)
            $attributeExtraPrice = 0;
            if (!empty($selectedAttributes) && !$isEbook) {
                foreach ($selectedAttributes as $attributeName => $attributeValue) {
                     // Tìm BookAttributeValue tương ứng
                     $bookAttributeValue = $book->bookAttributeValues()
                         ->whereHas('attributeValue', function($query) use ($attributeValue) {
                             $query->where('value', $attributeValue);
                         })
                         ->whereHas('attributeValue.attribute', function($query) use ($attributeName) {
                             $query->where('name', $attributeName);
                         })
                         ->first();
                     
                     if ($bookAttributeValue && $bookAttributeValue->extra_price > 0) {
                         $attributeExtraPrice += $bookAttributeValue->extra_price;
                     }
                 }
             }
            
            $unitPrice = $basePrice + $attributeExtraPrice;
            $totalAmount = $unitPrice * $validated['quantity'];
            
            // Thêm phí ship nếu có (miễn phí cho preorder)
            $shippingFee = 0; // Miễn phí ship cho đặt trước

            // Lấy trạng thái thanh toán mặc định
            $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->first();
            if (!$paymentStatus) {
                $paymentStatus = PaymentStatus::first(); // Fallback
            }

            $preorderData = [
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'book_format_id' => $bookFormat?->id,
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'selected_attributes' => $selectedAttributes,
                'status' => 'pending',
                'notes' => $validated['notes'],
                'expected_delivery_date' => $book->release_date,
                'payment_method_id' => $validated['payment_method_id'],
                'payment_status' => 'pending'
            ];

            // Chỉ lưu địa chỉ nếu không phải ebook
            if (!$isEbook) {
                $preorderData = array_merge($preorderData, [
                    'address' => $validated['address'],
                    'province_code' => $validated['province_code'],
                    'province_name' => $validated['province_name'],
                    'district_code' => $validated['district_code'],
                    'district_name' => $validated['district_name'],
                    'ward_code' => $validated['ward_code'],
                    'ward_name' => $validated['ward_name']
                ]);
            }
            $preorder = Preorder::create($preorderData);
            // dd($preorder);

            // Xử lý thanh toán ví điện tử
            if (str_contains(strtolower($paymentMethod->name), 'ví điện tử') || str_contains(strtolower($paymentMethod->name), 'wallet')) {
                $user = Auth::user();
                // dd(1);
                $wallet = Wallet::where('user_id', $user->id)->first();
                // dd($wallet);
                // Kiểm tra số dư ví
                if (!$wallet || $wallet->balance < $totalAmount) {
                    DB::rollback();
                    return back()->with('error', 'Số dư ví không đủ để thanh toán. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.');
                }
                // dd($totalAmount);
                // Trừ tiền từ ví
                $wallet->decrement('balance', $totalAmount);
                // dd(2);
                // Tạo bản ghi lịch sử giao dịch ví
               
                $a = WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $totalAmount,
                    'type' => 'Đặt trước sách',
                    'description' => 'Thanh toán đặt trước sách: ' . $book->title,
                    'status' => 'Thành công',
                    'payment_method' => 'wallet'
                ]);
                // dd($a);
                // Cập nhật trạng thái thanh toán preorder
                $preorder->update([
                    'payment_status' => 'paid',
                ]);
                
                DB::commit();
                
                // Gửi email xác nhận
                try {
                    Mail::to($preorder->email)->send(new PreorderConfirmation($preorder));
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email preorder: ' . $e->getMessage());
                }
                
                return redirect()->route('preorders.show', $preorder)
                    ->with('success', 'Đặt trước và thanh toán bằng ví thành công! Chúng tôi đã gửi email xác nhận đến bạn.');
            }

            // Xử lý thanh toán VNPay cho preorder
            if ($paymentMethod->name == 'Thanh toán vnpay') {
                DB::commit();
                
                $vnpayData = [
                    'preorder_id' => $preorder->id,
                    'payment_status_id' => $paymentStatus->id,
                    'payment_method_id' => $validated['payment_method_id'],
                    'order_code' => 'PRE' . $preorder->id . time(),
                    'amount' => $totalAmount,
                    'order_info' => "Thanh toán đặt trước sách " . $book->title,
                ];
                
                return $this->vnpay_payment($vnpayData);
            }

            DB::commit();

            // Gửi email xác nhận
            try {
                Mail::to($preorder->email)->send(new PreorderConfirmation($preorder));
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email preorder: ' . $e->getMessage());
            }

            return redirect()->route('preorders.show', $preorder)
                ->with('success', 'Đặt trước thành công! Chúng tôi đã gửi email xác nhận đến bạn.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi tạo preorder: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }

    /**
     * Hiển thị chi tiết đơn đặt trước
     */
    public function show(Preorder $preorder)
    {
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $preorder->user_id) {
            abort(403);
        }

        $preorder->load(['book', 'bookFormat']);
        
        return view('preorders.show', compact('preorder'));
    }

    /**
     * Danh sách đơn đặt trước của user
     */
    public function index()
    {
        $preorders = Preorder::where('user_id', Auth::id())
            ->with(['book', 'bookFormat'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('preorders.index', compact('preorders'));
    }

    /**
     * Hủy đơn đặt trước
     */
    public function cancel(Preorder $preorder)
    {
        // Kiểm tra quyền
        if (Auth::id() !== $preorder->user_id) {
            abort(403);
        }

        if (!$preorder->canBeCancelled()) {
            return back()->with('error', 'Không thể hủy đơn hàng này.');
        }

        $preorder->markAsCancelled();

        return back()->with('success', 'Đã hủy đơn đặt trước thành công.');
    }

    /**
     * API: Lấy thông tin sách để đặt trước
     */
    public function getBookInfo(Book $book)
    {
        if (!$book->canPreorder()) {
            return response()->json(['error' => 'Sách không thể đặt trước'], 400);
        }

        $formats = $book->formats()->get();
        $attributes = $book->bookAttributeValues()->with('attributeValue.attribute')->get();

        return response()->json([
            'book' => $book,
            'formats' => $formats,
            'attributes' => $attributes
        ]);
    }

    /**
     * Xử lý thanh toán VNPay cho preorder
     */
    public function vnpay_payment($data)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = route("preorder.vnpay.return");
        $vnp_TxnRef = $data['order_code'];
        $vnp_OrderInfo = $data['order_info'];
        $vnp_Amount = (int)($data['amount'] * 100);
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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

        // Lưu thông tin thanh toán vào preorder record thay vì tạo payment record
        // (theo memory về lỗi constraint violation)
        $preorder = Preorder::find($data['preorder_id']);
        if ($preorder) {
            $preorder->update([
                'vnpay_transaction_id' => $data['order_code'],
                'payment_status' => 'processing'
            ]);
        }

        return redirect($vnp_Url);
    }

    /**
     * Xử lý callback từ VNPay cho preorder
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $request->vnp_SecureHash;

        // Lấy tất cả tham số trừ vnp_SecureHash
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if ($key !== 'vnp_SecureHash') {
                $inputData[$key] = $value;
            }
        }

        // Sắp xếp theo key
        ksort($inputData);

        // Tạo hash string
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của chữ ký
        if ($secureHash !== $vnp_SecureHash) {
            Log::error('VNPay signature verification failed for preorder', [
                'expected' => $secureHash,
                'received' => $vnp_SecureHash
            ]);
            return redirect()->route('preorders.index')->with('error', 'Có lỗi xảy ra trong quá trình thanh toán');
        }

        // Lấy thông tin từ VNPay response
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_Amount = $request->vnp_Amount / 100;
        $vnp_TransactionNo = $request->vnp_TransactionNo;

        try {
            DB::beginTransaction();

            // Tìm preorder theo transaction reference
            $preorder = Preorder::where('vnpay_transaction_id', $vnp_TxnRef)->first();

            if (!$preorder) {
                DB::rollBack();
                Log::error('Preorder not found for VNPay return', ['transaction_ref' => $vnp_TxnRef]);
                return redirect()->route('preorders.index')->with('error', 'Không tìm thấy đơn đặt trước');
            }

            if ($vnp_ResponseCode === '00') {
                // Thanh toán thành công
                $preorder->update([
                    'payment_status' => 'paid',
                    'vnpay_transaction_id' => $vnp_TransactionNo,
                    'status' => 'Đã xác nhận'
                ]);
                
                Log::info('Preorder payment successful', [
                    'preorder_id' => $preorder->id,
                    'transaction_id' => $vnp_TransactionNo,
                    'amount' => $vnp_Amount
                ]);

                // Gửi email xác nhận thanh toán thành công
                try {
                    Mail::to($preorder->email)->send(new PreorderConfirmation($preorder));
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email xác nhận thanh toán preorder: ' . $e->getMessage());
                }

                DB::commit();
                
                return redirect()->route('preorders.show', $preorder)
                    ->with('success', 'Thanh toán đặt trước thành công! Chúng tôi đã gửi email xác nhận đến bạn.');
            } else {
                // Thanh toán thất bại
                $preorder->update([
                    'payment_status' => 'failed'
                ]);
                
                Log::warning('Preorder payment failed', [
                    'preorder_id' => $preorder->id,
                    'response_code' => $vnp_ResponseCode,
                    'transaction_ref' => $vnp_TxnRef
                ]);

                DB::commit();
                
                return redirect()->route('preorders.show', $preorder)
                    ->with('error', 'Thanh toán không thành công. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing VNPay return for preorder', [
                'error' => $e->getMessage(),
                'transaction_ref' => $vnp_TxnRef
            ]);
            
            return redirect()->route('preorders.index')
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng liên hệ hỗ trợ.');
        }
    }
}
