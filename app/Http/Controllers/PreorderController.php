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
     * OVERVIEW: Luồng Đặt Trước (Preorder)
     * - Route vào file `routes/web.php` nhóm prefix `preorders.*` (xem các comment tại đó)
     * - Người dùng mở form `create()` → gửi form tới `store()`
     * - Nếu chọn "Ví điện tử": trừ tiền ví ngay và đánh dấu preorder `payment_status = 'paid'`
     * - Nếu chọn "VNPay": redirect sang VNPay; khi quay lại sẽ vào `vnpayReturn()` để chốt trạng thái thanh toán
     * - Người dùng xem chi tiết bằng `show()`; có thể xem danh sách tại `index()` hoặc hủy bằng `cancel()` nếu còn hợp lệ
     */

    /**
     * Hiển thị form đặt trước sách
     * - Chỉ hiển thị phương thức thanh toán phù hợp: VNPay, Ví điện tử
     * - Trả thêm thông tin ví của user để UI hiển thị số dư
     */
    public function create(Book $book)
    {
        // Kiểm tra điều kiện cho phép đặt trước (ví dụ: cờ pre_order, thời gian mở preorder, tồn kho dự kiến...)
        if (!$book->canPreorder()) {
            return redirect()->back()->with('error', 'Sách này không thể đặt trước.');
        }  //Đoạn code kiểm tra nếu sách không thể đặt trước ($book->canPreorder() trả về false) thì sẽ chuyển người dùng về trang trước và hiển thị thông báo lỗi "Sách này không thể đặt trước.".

        // Danh sách định dạng sách (bìa cứng, bìa mềm, ebook, ...)
        $formats = $book->formats()->get();
        // Thuộc tính/biến thể hiển thị cho người dùng lựa chọn (màu, chữ ký, tặng kèm...)
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
            
        // Lấy ví hiện tại của người dùng đang đăng nhập để hiện số dư
        $wallet = Wallet::where('user_id', Auth::id())->first();
        
        // Truyền thêm preorder_discount_percent để tính toán giá
        // Phần trăm giảm giá dành riêng cho giai đoạn preorder (nếu có cấu hình trên sách)
        $preorderDiscountPercent = $book->preorder_discount_percent ?? 0;
        
        return view('preorders.create', compact('book', 'formats', 'attributes', 'paymentMethods', 'wallet', 'preorderDiscountPercent'));
    } //Đoạn code trên lấy phần trăm giảm giá đặt trước của sách (mặc định 0 nếu không có), sau đó truyền toàn bộ dữ liệu (book, formats, attributes, paymentMethods, wallet, preorderDiscountPercent) sang view preorders.create.

    /**
     * Lưu đơn đặt trước
     * STEPS:
     * 1) Validate input và tính đơn giá/tổng tiền theo định dạng sách + thuộc tính
     * 2) Tạo bản ghi `preorders`
     * 3) Xử lý thanh toán theo phương thức:
     *    - Ví điện tử: kiểm tra số dư → trừ tiền → ghi `WalletTransaction` → cập nhật preorder.payment_status='paid' → commit, gửi mail
     *    - VNPay: commit tạm preorder → build URL thanh toán → redirect sang VNPay
     * 4) Trường hợp không phải 2 phương thức trên: commit và gửi mail xác nhận đặt trước (chưa thanh toán)
     */
    public function store(Request $request)
    {
        // 1) Validate các trường đầu vào từ form — đảm bảo dữ liệu hợp lệ trước khi xử lý
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
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_fee' => 'nullable|numeric|min:0'
        ]);

        // 2) Tải đối tượng cần thiết để tính giá và xử lý thanh toán
        $book = Book::findOrFail($validated['book_id']); //Lấy ra sách từ bảng books theo book_id đã validate
        $bookFormat = $validated['book_format_id'] ? BookFormat::findOrFail($validated['book_format_id']) : null; //Nếu người dùng có chọn định dạng sách (ví dụ: bìa mềm, bìa cứng, ebook) thì lấy bản ghi BookFormat. Nếu không có thì để null.
        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);//Lấy phương thức thanh toán đã chọn từ bảng payment_methods.
        // Kiểm tra xem sách có thể đặt trước không
        if (!$book->canPreorder()) {
            return back()->with('error', 'Sách này không thể đặt trước.');
        }

        // Kiểm tra định dạng có phải ebook không (ebook: không cần địa chỉ giao hàng)
        $isEbook = $bookFormat && strtolower($bookFormat->format_name) === 'ebook';

        // Nếu là sách vật lý, bắt buộc phải có địa chỉ — tránh đơn thiếu thông tin giao hàng
        if (!$isEbook && (!$validated['address'] || !$validated['province_code'])) {
            return back()->with('error', 'Vui lòng nhập đầy đủ địa chỉ giao hàng cho sách vật lý.');
        }
        //  dd($request->all());
        try {
            DB::beginTransaction();

            // Tính giá cơ bản (đơn giá preorder có thể khác giá bán chính thức)
            $basePrice = $book->getPreorderPrice($bookFormat);
            
            // Làm sạch thuộc tính được chọn: loại bỏ giá trị rỗng/null để tránh cộng giá sai
            $selectedAttributes = []; // Mảng lưu trữ các thuộc tính đã chọn
            // Nếu có thuộc tính được chọn, lọc ra các giá trị hợp lệ
            // Chỉ giữ lại các thuộc tính có giá trị không rỗng
            if (!empty($validated['selected_attributes']) && is_array($validated['selected_attributes'])) { // Kiểm tra nếu selected_attributes không rỗng và là mảng
                // Duyệt qua từng thuộc tính đã chọn và loại bỏ giá trị rỗng
                // Chỉ giữ lại các thuộc tính có giá trị không rỗng
                // Điều này giúp tránh việc cộng giá trị rỗng vào tổng tiền
                foreach ($validated['selected_attributes'] as $k => $v) { // Duyệt qua các thuộc tính được chọn
                    if ($v !== null && $v !== '') { // Kiểm tra nếu giá trị không phải null hoặc chuỗi rỗng
                        // Chỉ giữ lại các thuộc tính có giá trị hợp lệ
                        $selectedAttributes[$k] = $v; // Lưu trữ thuộc tính đã chọn
                    }
                }
            }

            // Tính giá thêm từ thuộc tính (chỉ áp dụng cho sách vật lý)
            $attributeExtraPrice = 0; // Tổng giá trị từ các thuộc tính
            if (!empty($selectedAttributes) && !$isEbook) { // Kiểm tra nếu có thuộc tính được chọn và sách không phải ebook
                foreach ($selectedAttributes as $attributeName => $attributeValue) {   // Duyệt qua từng thuộc tính đã chọn 
                     // Tìm BookAttributeValue tương ứng
                     $bookAttributeValue = $book->bookAttributeValues() // Tìm kiếm BookAttributeValue tương ứng với sách và thuộc tính
                         ->whereHas('attributeValue', function($query) use ($attributeValue) { // Kiểm tra xem thuộc tính có giá trị tương ứng
                             $query->where('value', $attributeValue); // Lọc ra các giá trị có trong thuộc tính
                         }) 
                         ->whereHas('attributeValue.attribute', function($query) use ($attributeName) { // Kiểm tra xem thuộc tính có tên tương ứng
                             $query->where('name', $attributeName); // Lọc ra các thuộc tính có tên trong thuộc tính
                         })
                         ->first(); // Lấy giá trị đầu tiên phù hợp
                        // Nếu tìm thấy giá trị thuộc tính và nó có phụ thu, cộng vào tổng phụ thu
                     if ($bookAttributeValue && $bookAttributeValue->extra_price > 0) { // Kiểm tra nếu giá trị thuộc tính có phụ thu
                         $attributeExtraPrice += $bookAttributeValue->extra_price; // Cộng giá trị phụ thu vào tổng phụ thu
                     } 
                 }
             }
            
            // Đơn giá cuối cùng = giá cơ bản + phụ thu từ thuộc tính
            $unitPrice = $basePrice + $attributeExtraPrice; // Tính đơn giá cuối cùng
            // Thành tiền = đơn giá cuối cùng * số lượng
            $subtotal = $unitPrice * $validated['quantity']; // Tính tổng tiền sản phẩm
            
            // Tính phí vận chuyển (chỉ áp dụng cho sách vật lý)
            $shippingFee = 0;
            if (!$isEbook && isset($validated['shipping_fee'])) {
                $shippingFee = $validated['shipping_fee'];
            }
            
            // Tổng tiền cuối cùng = tiền sản phẩm + phí vận chuyển
            $totalAmount = $subtotal + $shippingFee;

            // Lấy trạng thái thanh toán mặc định (hiển thị "Chờ Xử Lý" cho các giao dịch đang diễn ra)
            $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->first();
            if (!$paymentStatus) {
                $paymentStatus = PaymentStatus::first(); // Fallback
            }

            // Dữ liệu sẽ ghi vào bảng preorders — lưu ý: payment_status mặc định 'pending'
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
                'shipping_fee' => $shippingFee,
                'selected_attributes' => $selectedAttributes,
                'status' => Preorder::STATUS_CHO_DUYET,
                'notes' => $validated['notes'],
                'expected_delivery_date' => $book->release_date,
                'payment_method_id' => $validated['payment_method_id'],
                'payment_status' => 'pending'
            ];

            // Chỉ lưu địa chỉ nếu không phải ebook (ebook không cần địa chỉ giao hàng)
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

            // Trừ số lượng tồn kho khi tạo preorder
            $this->decreaseStockForPreorder($book, $bookFormat, $validated);

            // Xử lý thanh toán ví điện tử
            // Lưu ý: Đây là luồng "thu tiền ngay" cho preorder. Sau bước này
            // `preorder.payment_status` sẽ là 'paid'. Điều này rất quan trọng cho bước convertToOrder phía Admin.
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
                // Trừ tiền từ ví — đảm bảo thao tác trong transaction để an toàn dữ liệu
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
                // Cập nhật trạng thái thanh toán preorder — đánh dấu đã trả tiền
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
            // Ghi chú: KHÔNG tạo bản ghi Payment ở đây (tránh lỗi ràng buộc order_id not null).
            // Thay vào đó lưu tạm thông tin vào preorder và hoàn tất tại `vnpayReturn()`
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

        $preorder->load(['book', 'bookFormat', 'paymentMethod']);
        
        return view('preorders.show', compact('preorder'));
    }

    /**
     * Danh sách đơn đặt trước của user
     * - Dựa theo Auth::id()
     * - Phân trang và trả view `preorders.index`
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
     * Trừ số lượng tồn kho khi tạo preorder
     */
    private function decreaseStockForPreorder($book, $bookFormat, $validated)
    {
        $quantity = $validated['quantity'];
        
        // Trừ preorder_count của sách
        $book->increment('preorder_count', $quantity);
        
        // Trừ stock của book format nếu có
        if ($bookFormat && $bookFormat->stock > 0) {
            $newStock = max(0, $bookFormat->stock - $quantity);
            $bookFormat->update(['stock' => $newStock]);
        }
        
        // Trừ stock của các thuộc tính được chọn
        if (!empty($validated['selected_attributes'])) {
            foreach ($validated['selected_attributes'] as $attributeName => $attributeValue) {
                // Bỏ qua giá trị rỗng
                if ($attributeValue === null || $attributeValue === '') {
                    continue;
                }
                
                // Tìm BookAttributeValue tương ứng
                $bookAttributeValue = $book->bookAttributeValues()
                    ->whereHas('attributeValue', function($query) use ($attributeValue) {
                        $query->where('value', $attributeValue);
                    })
                    ->whereHas('attributeValue.attribute', function($query) use ($attributeName) {
                        $query->where('name', $attributeName);
                    })
                    ->first();
                    
                if ($bookAttributeValue && $bookAttributeValue->stock > 0) {
                    $newStock = max(0, $bookAttributeValue->stock - $quantity);
                    $bookAttributeValue->update(['stock' => $newStock]);
                }
            }
        }
    }

    /**
     * Hủy đơn đặt trước
     * - Chỉ cho phép nếu `Preorder::canBeCancelled()` trả true.
     * - Cập nhật trạng thái phù hợp thông qua `markAsCancelled()`.
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
     * - Dùng cho UI: trả về book, các format và các thuộc tính có thể chọn.
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
     * - Tạo tham số, ký hash và redirect sang cổng VNPay
     * - Trước khi redirect, cập nhật `preorders.vnpay_transaction_id` = mã tham chiếu và `payment_status = 'pending'`
     * - Khi VNPay redirect về, `vnpayReturn()` sẽ xác nhận thành công/thất bại
     */
    public function vnpay_payment($data)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = route("preorder.vnpay.return");
        $vnp_TxnRef = $data['order_code'];
        $vnp_OrderInfo = $data['order_info'];
        // VNPay yêu cầu số tiền theo đơn vị VND x 100
        $vnp_Amount = (int)($data['amount'] * 100);
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        // Địa chỉ IP của client (VNPay ghi nhận)
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

        // Sắp xếp tham số theo key để tạo chuỗi ký đúng chuẩn VNPay
        ksort($inputData);

        // Chuỗi query dùng để redirect và cũng là dữ liệu để băm chữ ký
        $query = http_build_query($inputData);
        $hashdata = $query;

        // Tạo chữ ký an toàn theo thuật toán HMAC-SHA512
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        // Lưu thông tin thanh toán vào preorder record thay vì tạo payment record
        // (theo memory về lỗi constraint violation)
        $preorder = Preorder::find($data['preorder_id']);
        if ($preorder) {
            $preorder->update([
                'vnpay_transaction_id' => $data['order_code'],
                // Sử dụng giá trị hợp lệ theo enum: pending | paid | failed
                'payment_status' => 'paid'       
            ]);
        }
        
        
        return redirect($vnp_Url);
        
    }
    
    /**
     * Xử lý callback từ VNPay cho preorder
     * STEPS:
     * 1) Xác thực chữ ký VNPay
     * 2) Tìm preorder theo `vnp_TxnRef`
     * 3) Nếu ResponseCode === '00' → set `payment_status = 'paid'`, cập nhật transaction id thực tế, gửi mail
     * 4) Nếu thất bại → set `payment_status = 'failed'`
     * 5) Redirect về trang chi tiết preorder với thông báo
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $request->vnp_SecureHash;

        // Lấy tất cả tham số trừ vnp_SecureHash (không dùng tham số này khi tạo chữ ký kiểm chứng)
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if ($key !== 'vnp_SecureHash') {
                $inputData[$key] = $value;
            }
        }

        // Sắp xếp theo key để đảm bảo cùng thứ tự khi tính hash
        ksort($inputData);

        // Tạo hash string đúng format như lúc gửi đi
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của chữ ký — nếu sai, dừng xử lý và báo lỗi
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

            // Tìm preorder theo transaction reference (mã đã đặt ở vnpay_payment)
            $preorder = Preorder::where('vnpay_transaction_id', $vnp_TxnRef)->first();

            if (!$preorder) {
                DB::rollBack();
                Log::error('Preorder not found for VNPay return', ['transaction_ref' => $vnp_TxnRef]);
                return redirect()->route('preorders.index')->with('error', 'Không tìm thấy đơn đặt trước');
            }

            if ($vnp_ResponseCode === '00') {
                // Thanh toán thành công — đánh dấu paid và cập nhật transaction id thực tế từ VNPay
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
