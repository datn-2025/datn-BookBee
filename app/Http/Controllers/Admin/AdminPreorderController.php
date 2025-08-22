<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Preorder;
use App\Models\User;
use App\Services\GHNService;
use App\Mail\PreorderStatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminPreorderController extends Controller
{
    /**
     * Danh sách đơn đặt trước
     */
    public function index(Request $request)
    {
        // Eager-load để giảm N+1 query trên danh sách
        $query = Preorder::with(['user', 'book', 'bookFormat']);

        // Filter theo status
        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo sách
        // Lọc theo sách (book_id)
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        // Tìm kiếm theo tên khách hàng hoặc email
        // Tìm kiếm theo tên/email/điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter theo ngày
        // Lọc theo ngày tạo từ ... đến ...
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $preorders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Thống kê
        // Thống kê nhanh theo scope trên model Preorder
        $stats = [
            'total' => Preorder::count(),
            'pending' => Preorder::pending()->count(),
            'confirmed' => Preorder::confirmed()->count(),
            'processing' => Preorder::processing()->count(),
            'shipped' => Preorder::shipped()->count(),
            'delivered' => Preorder::delivered()->count(),
            'cancelled' => Preorder::cancelled()->count(),
        ];

        $books = Book::where('pre_order', true)->get(['id', 'title']);

        return view('admin.preorders.index', compact('preorders', 'stats', 'books'));
    }

    /**
     * Hiển thị form tạo đơn đặt trước mới
     */
    public function create()
    {
        // Lấy danh sách sách đang mở preorder kèm các định dạng
        $preorderBooks = Book::where('pre_order', true)
            ->with('bookFormats')
            ->get(['id', 'title', 'cover_image', 'pre_order_price']);
            
        $users = User::select('id', 'name', 'email', 'phone')
            ->orderBy('name')
            ->get();
            
        $ghnService = new GHNService();
        $provinces = collect($ghnService->getProvinces())->map(function($province) {
            return [
                'id' => $province['ProvinceID'],
                'name' => $province['ProvinceName']
            ];
        });
        
        return view('admin.preorders.create', compact('preorderBooks', 'users', 'provinces'));
    }

    /**
     * Lưu đơn đặt trước mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu form tạo đơn đặt trước từ admin
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_format_id' => 'required|exists:book_formats,id',
            'quantity' => 'required|integer|min:1|max:10', // Giới hạn số lượng đặt trước
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
            'user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed',
            'shipping_fee' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
            'selected_attributes' => 'nullable|array',
            'selected_attributes.*' => 'exists:attribute_values,id'
        ];
        
        // Chỉ yêu cầu địa chỉ cho sách vật lý và không phải preorder
        // Với preorder, địa chỉ có thể được thêm sau khi sách sẵn sàng giao
        if (!$isEbook && !$isPreorderBook) {
            $rules = array_merge($rules, [
                'province_id' => 'required|exists:provinces,id',
                'district_id' => 'required|exists:districts,id', 
                'ward_id' => 'required|exists:wards,id',
                'address' => 'required|string|max:500'
            ]);
        }
        
        $validated = $request->validate($rules, [
            'quantity.max' => 'Số lượng đặt trước không được vượt quá 10.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'expected_delivery_date.after_or_equal' => 'Ngày giao hàng dự kiến không được là ngày trong quá khứ.',
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố cho sách vật lý thông thường.',
            'district_id.required' => 'Vui lòng chọn quận/huyện cho sách vật lý thông thường.',
            'ward_id.required' => 'Vui lòng chọn phường/xã cho sách vật lý thông thường.',
            'address.required' => 'Vui lòng nhập địa chỉ cụ thể cho sách vật lý thông thường.'
        ]);

        try {
            DB::beginTransaction(); // Bắt đầu giao dịch để đảm bảo toàn vẹn dữ liệu

            // Lấy thông tin sách và format
            // Tải sách và định dạng sách đã chọn
            $book = Book::findOrFail($validated['book_id']);
            $bookFormat = BookFormat::findOrFail($validated['book_format_id']);

            // Kiểm tra sách có phải pre-order không
            if (!$book->pre_order) {
                return back()->with('error', 'Sách này không hỗ trợ đặt trước.');
            }

            

            // Tính toán giá
            // Tính giá dựa vào book format (ở phía user có thể dùng công thức preorder khác)
            $unitPrice = $bookFormat->price;
            $totalAmount = $unitPrice * $validated['quantity'];

            // Lấy thông tin địa chỉ nếu không phải ebook và không phải preorder
            $provinceId = null;
            $districtId = null;
            $wardId = null;
            $provinceName = null;
            $districtName = null;
            $wardName = null;

            // Nếu không phải ebook, tải thêm tên Tỉnh/Quận/Phường để lưu kèm
            if (!$bookFormat->format_name || !str_contains(strtolower($bookFormat->format_name), 'ebook')) {
                $ghnService = new GHNService();

                if ($validated['province_id']) {
                    $provinces = $ghnService->getProvinces();
                    $province = collect($provinces)->firstWhere('ProvinceID', $validated['province_id']);
                    $provinceId = $province['ProvinceID'];
                    $provinceName = $province['ProvinceName'];
                }
                if ($validated['district_id']) {
                    $districts = $ghnService->getDistricts($validated['province_id']);
                    $district = collect($districts)->firstWhere('DistrictID', $validated['district_id']);
                    $districtId = $district['DistrictID'];
                    $districtName = $district['DistrictName'];
                }
                if ($validated['ward_code']) {
                    $wards = $ghnService->getWards($validated['district_id']);
                    $ward = collect($wards)->firstWhere('WardCode', $validated['ward_code']);
                    $wardId = $ward['WardCode'];
                    $wardName = $ward['WardName'];
                }
            }

            // Tạo preorder
            // Tạo bản ghi preorder trong DB
            $preorder = Preorder::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'book_format_id' => $validated['book_format_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?: $book->release_date,
                'confirmed_at' => $validated['status'] === 'confirmed' ? now() : null,
                'selected_attributes' => !empty($selectedAttributesData) ? json_encode($selectedAttributesData) : null
            ];
            
            // Thêm thông tin địa chỉ nếu không phải ebook và không phải preorder
            if (!$isEbook && !$isPreorderBook) {
                $preorderData = array_merge($preorderData, [
                    'province_code' => $provinceId,
                    'district_code' => $districtId,
                    'ward_code' => $wardId,
                    'province_name' => $provinceName,
                    'district_name' => $districtName,
                    'ward_name' => $wardName,
                    'address' => $validated['address']
                ]);
            }
            
            $preorder = Preorder::create($preorderData);

            // Trừ số lượng tồn kho cho preorder
            $this->decreaseStockForPreorder($book, $bookFormat, $validated);

            // Cập nhật preorder_count của sách
            // Tăng số lượng preorder của sách để thống kê
            $book->increment('preorder_count', $validated['quantity']);

            DB::commit();

            return redirect()->route('admin.preorders.show', $preorder)
                ->with('success', 'Đã tạo đơn đặt trước thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lỗi tạo preorder từ admin: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo đơn đặt trước.');
        }
    }

    /**
     * Chi tiết đơn đặt trước
     */
    public function show(Preorder $preorder)
    {
        // Load thêm quan hệ để hiển thị đầy đủ trên view
        $preorder->load(['user', 'book', 'bookFormat']);
        
        return view('admin.preorders.show', compact('preorder'));
    }

    /**
     * Cập nhật trạng thái đơn đặt trước
     */
    public function updateStatus(Request $request, Preorder $preorder)
    {
        // Kiểm tra nếu đơn đã chuyển thành đơn hàng
        if ($preorder->status === 'delivered' || $preorder->status === 'Đã chuyển thành đơn hàng') {
            toastr()->error('Không thể cập nhật trạng thái cho đơn đã chuyển thành đơn hàng.');
            return back();
        }

        // Cho phép nhập cả bộ trạng thái tiếng Việt/tiếng Anh nhằm tương thích UI
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,Chờ xử lý,Đã xác nhận,Đang xử lý,Đã gửi,Đã giao,Đã hủy',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $preorder->status;
        
        try {
            DB::beginTransaction();

            // Cập nhật trạng thái chung và ghi chú
            $preorder->update([
                'status' => $validated['status'],
                'notes' => $validated['notes']
            ]);

            // Cập nhật timestamp tương ứng
            // Đồng bộ các mốc thời gian tương ứng với trạng thái
            switch ($validated['status']) {
                case 'confirmed':
                case 'Đã xác nhận':
                    $preorder->update(['confirmed_at' => now()]);
                    break;
                case 'shipped':
                case 'Đã gửi':
                    $preorder->update(['shipped_at' => now()]);
                    break;
                case 'delivered':
                case 'Đã giao':
                    $preorder->update(['delivered_at' => now()]);
                    break;
            }

            DB::commit();

            // Gửi email thông báo nếu trạng thái thay đổi
            if ($oldStatus !== $validated['status']) {
                try {
                    Mail::to($preorder->email)->send(new PreorderStatusUpdate($preorder, $oldStatus));
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email cập nhật trạng thái preorder: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Đã cập nhật trạng thái thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lỗi cập nhật trạng thái preorder: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái.');
        }
    }

    /**
     * Duyệt đơn đặt trước
     */
    public function approvePreorder(Request $request, Preorder $preorder)
    {
        Log::info('Approve preorder called', [
            'preorder_id' => $preorder->id,
            'current_status' => $preorder->status,
            'request_data' => $request->all()
        ]);

        // Kiểm tra trạng thái đơn hàng - chỉ cho phép duyệt đơn đang chờ xử lý
        // Chỉ cho phép duyệt nếu đang ở trạng thái chờ xác nhận
        if ($preorder->status !== 'pending' && $preorder->status !== 'Chờ xác nhận') {
            Log::warning('Cannot approve preorder - invalid status', [
                'preorder_id' => $preorder->id,
                'current_status' => $preorder->status
            ]);
            return back()->with('error', 'Chỉ có thể duyệt đơn đang chờ xử lý.');
        }

        // Kiểm tra ngày phát hành và hiển thị cảnh báo nếu chưa phát hành
        // Nếu sách chưa phát hành, yêu cầu xác nhận lại (có thể convert trước ngày phát hành)
        if (!$preorder->book->isReleased()) {
            if (!$request->has('force_approve')) {
                $releaseDate = $preorder->book->release_date->format('d/m/Y');
                return back()->with('warning', [
                    'message' => "Sách chưa đến ngày phát hành ({$releaseDate}). Bạn có chắc chắn muốn duyệt đơn này không?",
                    'confirm_url' => route('admin.preorders.approve', $preorder) . '?force_approve=1',
                    'preorder_id' => $preorder->id
                ]);
            }
        }

        try {
            // Cập nhật trạng thái preorder từ pending sang confirmed
            // Chuyển từ pending sang Đã xác nhận và set mốc thời gian
            $preorder->update([
                'status' => 'Đã xác nhận',
                'confirmed_at' => now()
            ]);

            Log::info('Preorder approved successfully', [
                'preorder_id' => $preorder->id,
                'new_status' => 'Đã xác nhận'
            ]);

            return back()->with('success', 'Đã duyệt đơn đặt trước thành công!');

        } catch (\Exception $e) {
            Log::error('Error approving preorder: ' . $e->getMessage(), [
                'preorder_id' => $preorder->id
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi duyệt đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển đổi preorder thành order chính thức
     */
    public function convertToOrder(Request $request, Preorder $preorder)
    {
        // B1: Chỉ cho phép convert khi preorder đã được admin xác nhận (an toàn nghiệp vụ)
        if ($preorder->status !== 'Đã xác nhận' && $preorder->status !== 'confirmed') {
            return back()->with('error', 'Chỉ có thể chuyển đổi đơn đã được xác nhận.');
        }

        // Allow conversion even if book is not released yet, with a warning
        // Cho phép convert kể cả khi sách chưa phát hành (ghi log cảnh báo)
        if (!$preorder->book->isReleased()) {
            Log::info("Converting preorder to order before release date", [
                'preorder_id' => $preorder->id,
                'book_title' => $preorder->book->title,
                'release_date' => $preorder->book->release_date,
                'converted_by' => Auth::user()->email ?? 'system'
            ]);
        }

        try {
            DB::beginTransaction();

            // B2: Tạo địa chỉ giao hàng đơn giản dựa theo thông tin trong preorder
            $addressId = \Illuminate\Support\Str::uuid();
            DB::table('addresses')->insert([
                'id' => $addressId,
                'user_id' => $preorder->user_id,
                'recipient_name' => $preorder->customer_name,
                'phone' => $preorder->phone,
                'address_detail' => $preorder->address ?? 'Địa chỉ từ đơn đặt trước',
                'city' => $preorder->province_name,
                'district' => $preorder->district_name,
                'ward' => $preorder->ward_name,
                'province_id' => $preorder->province_code,
                'district_id' => $preorder->district_code,
                'ward_code' => $preorder->ward_code,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // B3: Lấy/khởi tạo trạng thái đơn hàng mặc định "Chờ xác nhận"
            // B3: Đảm bảo có trạng thái đơn hàng mặc định "Chờ xác nhận"
            $orderStatusId = DB::table('order_statuses')->where('name', 'Chờ xác nhận')->value('id');
            if (!$orderStatusId) {
                $orderStatusId = \Illuminate\Support\Str::uuid();
                DB::table('order_statuses')->insert([
                    'id' => $orderStatusId,
                    'name' => 'Chờ xác nhận',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // B4: Đảm bảo có trạng thái thanh toán Paid/Unpaid cho bảng orders
            $paidStatusId = DB::table('payment_statuses')->where('name', 'Đã Thanh Toán')->value('id');
            if (!$paidStatusId) {
                $paidStatusId = \Illuminate\Support\Str::uuid();
                DB::table('payment_statuses')->insert([
                    'id' => $paidStatusId,
                    'name' => 'Đã Thanh Toán',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $unpaidStatusId = DB::table('payment_statuses')->where('name', 'Chưa Thanh Toán')->value('id');
            if (!$unpaidStatusId) {
                $unpaidStatusId = \Illuminate\Support\Str::uuid();
                DB::table('payment_statuses')->insert([
                    'id' => $unpaidStatusId,
                    'name' => 'Chưa Thanh Toán',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // B5: Chuẩn bị ID và mã đơn
            // B5: Sinh ID và mã đơn độc nhất
            $orderId = \Illuminate\Support\Str::uuid();
            $orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);
            
            // B6: Xác định phương thức thanh toán cho ORDER
            //    - Mặc định dùng phương thức mà người dùng đã chọn khi tạo preorder
            //    - Nếu preorder đã PAID: phân nhánh VNPay/Wallet dựa trên `vnpay_transaction_id`
            //    - Nếu chưa xác định được: có fallback (đã paid → ưu tiên Ví; chưa paid → COD)
            // Mặc định: dùng phương thức đã lưu trên preorder (lựa chọn ban đầu của KH)
            $paymentMethodId = $preorder->payment_method_id;

            // Nếu preorder đã thanh toán, suy luận PM dựa theo transaction id VNPay
            if ($preorder->payment_status === 'paid') {
                // Nếu đã thanh toán: phân biệt VNPay vs Ví điện tử theo dấu hiệu transaction
                if ($preorder->vnpay_transaction_id) {
                    $pm = DB::table('payment_methods')
                        ->where('is_active', true)
                        ->where(function($q){
                            $q->where('name', 'like', '%vnpay%')
                              ->orWhere('name', 'like', '%vn pay%')
                              ->orWhere('name', 'like', '%vn-pay%');
                        })
                        ->first();
                    // Ưu tiên áp dụng PM tìm được, nếu không có thì giữ nguyên
                    $paymentMethodId = ($pm->id ?? null) ?: $paymentMethodId;
                    Log::info('convertToOrder chose payment method (VNPay path)', ['pm' => $pm]);
                } else {
                    $pm = DB::table('payment_methods')
                        ->where('is_active', true)
                        ->where(function($q){
                            $q->where('name', 'like', '%ví điện tử%')
                              ->orWhere('name', 'like', '%vi dien tu%')
                              ->orWhere('name', 'like', '%wallet%')
                              ->orWhere('name', 'like', '%e-wallet%')
                              ->orWhere('name', 'like', '%vi%')
                              ->orWhere('name', 'like', '%momo%');
                        })
                        ->first();
                    // Không có vnpay_transaction_id → hiểu là Ví điện tử
                    $paymentMethodId = ($pm->id ?? null) ?: $paymentMethodId;
                    Log::info('convertToOrder chose payment method (Wallet path)', ['pm' => $pm]);
                }
            }

            // Fallback lần cuối để tránh hiển thị "Không xác định"
            if (!$paymentMethodId) {
                if ($preorder->payment_status === 'paid') {
                    // Nếu đã thanh toán mà vẫn chưa xác định PM -> ưu tiên Ví điện tử
                    $pm = DB::table('payment_methods')
                        ->where('is_active', true)
                        ->where(function($q){
                            $q->where('name', 'like', '%ví điện tử%')
                              ->orWhere('name', 'like', '%vi dien tu%')
                              ->orWhere('name', 'like', '%wallet%')
                              ->orWhere('name', 'like', '%e-wallet%')
                              ->orWhere('name', 'like', '%momo%');
                        })
                        ->first();
                    $paymentMethodId = ($pm->id ?? null) ?: $paymentMethodId;
                    Log::info('convertToOrder fallback chose Wallet', ['pm' => $pm]);
                } else {
                    // Chưa thanh toán -> fallback COD
                    $pm = DB::table('payment_methods')
                        ->where('is_active', true)
                        ->where(function($q){
                            $q->where('name', 'like', '%thanh toán khi nhận hàng%')
                              ->orWhere('name', 'like', '%khi nhận hàng%')
                              ->orWhere('name', 'like', '%cash on delivery%')
                              ->orWhere('name', 'like', '%COD%')
                              ->orWhere('name', 'like', '%cod%');
                        })
                        ->first();
                    $paymentMethodId = ($pm->id ?? null) ?: $paymentMethodId;
                    Log::info('convertToOrder fallback chose COD', ['pm' => $pm]);
                }
            }

            // B7: Tạo Order chính thức dựa trên dữ liệu từ preorder
            // B7: Ghi Order chính thức vào DB
            DB::table('orders')->insert([
                'id' => $orderId,
                'user_id' => $preorder->user_id,
                'order_code' => $orderCode,
                'total_amount' => $preorder->total_amount,
                'address_id' => $addressId,
                'order_status_id' => $orderStatusId,
                'payment_status_id' => $preorder->payment_status === 'paid' ? $paidStatusId : $unpaidStatusId,
                'payment_method_id' => $paymentMethodId,
                'note' => 'Chuyển đổi từ đơn đặt trước #' . $preorder->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Tạo object Order để sử dụng cho phần còn lại
            // Nạp model Order để dùng tiếp (ví dụ hiển thị, tạo items)
            $order = Order::find($orderId);

            // B8: Tạo OrderItem tương ứng (1 item duy nhất từ preorder)
            // B8: Tạo OrderItem một-một từ preorder
            $orderItemId = \Illuminate\Support\Str::uuid();
            DB::table('order_items')->insert([
                 'id' => $orderItemId,
                 'order_id' => $order->id,
                 'book_id' => $preorder->book_id,
                 'book_format_id' => $preorder->book_format_id,
                 'quantity' => $preorder->quantity,
                 'price' => $preorder->unit_price,
                 'total' => $preorder->total_amount,
                 'is_combo' => false,
                 'created_at' => now(),
                 'updated_at' => now()
             ]);

            // B9: Cập nhật trạng thái preorder sau khi đã tạo order
            // B9: Cập nhật trạng thái preorder sau khi đã tạo order (theo thiết kế hiện tại)
            $preorder->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);

            DB::commit();

            // Điều hướng sang trang chi tiết đơn hàng vừa tạo
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đã chuyển đổi thành đơn hàng #' . $order->id . ' thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lỗi chuyển đổi preorder thành order: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi chuyển đổi đơn hàng.');
        }
    }

    /**
     * Xóa đơn đặt trước
     */
    public function destroy(Preorder $preorder)
    {
        if (!in_array($preorder->status, ['cancelled'])) {
            return back()->with('error', 'Chỉ có thể xóa đơn đã hủy.');
        }

        $preorder->delete();

        return redirect()->route('admin.preorders.index')
            ->with('success', 'Đã xóa đơn đặt trước thành công.');
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'preorder_ids' => 'required|array',
            'preorder_ids.*' => 'exists:preorders,id',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        try {
            DB::beginTransaction();
            
            $preorders = Preorder::whereIn('id', $validated['preorder_ids'])->get();
            $updated = 0;
            
            foreach ($preorders as $preorder) {
                // Xử lý đặc biệt cho trạng thái cancelled để hoàn trả stock
                if ($validated['status'] === 'cancelled' && $preorder->status !== 'cancelled') {
                    $preorder->markAsCancelled();
                } else {
                    $preorder->update(['status' => $validated['status']]);
                }
                $updated++;
            }
            
            DB::commit();
            return back()->with('success', "Đã cập nhật {$updated} đơn đặt trước.");
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi bulk update preorder status: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật hàng loạt.');
        }
    }

    /**
     * Export preorders
     */
    public function export(Request $request)
    {
        $query = Preorder::with(['user', 'book', 'bookFormat']);

        // Áp dụng các filter giống như index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $preorders = $query->orderBy('created_at', 'desc')->get();

        // Tạo CSV
        $filename = 'preorders_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'ID', 'Khách hàng', 'Email', 'Điện thoại', 'Sách', 'Định dạng',
            'Số lượng', 'Đơn giá', 'Tổng tiền', 'Trạng thái', 'Ngày đặt', 'Ghi chú'
        ]);

        // Data
        foreach ($preorders as $preorder) {
            fputcsv($handle, [
                $preorder->id,
                $preorder->customer_name,
                $preorder->email,
                $preorder->phone,
                $preorder->book->title,
                $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A',
                $preorder->quantity,
                number_format($preorder->unit_price, 0, ',', '.'),
                number_format($preorder->total_amount, 0, ',', '.'),
                $preorder->status_text,
                $preorder->created_at->format('d/m/Y H:i'),
                $preorder->notes
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}

