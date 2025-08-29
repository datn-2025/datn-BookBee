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
use App\Services\InvoiceService;
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
        if ($request->filled('status')) { //
            $query->where('status', $request->status); //
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
        // Thống kê nhanh theo trạng thái mới
        $stats = [
            'total' => Preorder::count(),
            'cho_duyet' => Preorder::where('status', Preorder::STATUS_CHO_DUYET)->count(),
            'da_duyet' => Preorder::where('status', Preorder::STATUS_DA_DUYET)->count(),
            'san_sang_chuyen_doi' => Preorder::where('status', Preorder::STATUS_SAN_SANG_CHUYEN_DOI)->count(),
            'da_chuyen_doi' => Preorder::where('status', Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG)->count(),
            'da_huy' => Preorder::where('status', Preorder::STATUS_DA_HUY)->count(),
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
            ->with('formats')
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
            'status' => 'required|in:' . implode(',', [
                Preorder::STATUS_CHO_DUYET,
                Preorder::STATUS_DA_DUYET
            ]),
            'shipping_fee' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
            'selected_attributes' => 'nullable|array',
            'selected_attributes.*' => 'exists:attribute_values,id'
        ]);
        
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
                'confirmed_at' => $validated['status'] === Preorder::STATUS_DA_DUYET ? now() : null,
                'selected_attributes' => !empty($selectedAttributesData) ? json_encode($selectedAttributesData) : null
            ]);
            
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
        $preorder->load(['user', 'book', 'bookFormat', 'paymentMethod']);
        
        return view('admin.preorders.show', compact('preorder'));
    }

    /**
     * Cập nhật trạng thái đơn đặt trước
     */
    public function updateStatus(Request $request, Preorder $preorder)
    {
        // Kiểm tra nếu đơn đã chuyển thành đơn hàng
        if ($preorder->isConverted()) {
            toastr()->error('Không thể cập nhật trạng thái cho đơn đã chuyển thành đơn hàng.');
            return back();
        }

        // Validate trạng thái với constants mới
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                 Preorder::STATUS_CHO_DUYET,
                 Preorder::STATUS_DA_DUYET, 
                 Preorder::STATUS_SAN_SANG_CHUYEN_DOI,
                 Preorder::STATUS_DA_HUY,
                 // Tương thích ngược với UI cũ
                 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled',
                 'Chờ xử lý', 'Đã xác nhận', 'Đang xử lý', 'Đã gửi', 'Đã giao', 'Đã hủy'
             ]),
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $preorder->status;
        
        // Chuẩn hóa trạng thái từ UI cũ sang mới
        $newStatus = Preorder::STATUS_MAPPING[$validated['status']] ?? $validated['status'];
        
        try {
            DB::beginTransaction();

            // Kiểm tra xem có thể chuyển đổi trạng thái không
            if (!$preorder->canTransitionTo($newStatus)) {
                toastr()->error("Không thể chuyển từ trạng thái '{$preorder->status}' sang '{$newStatus}'.");
                return back();
            }

            // Sử dụng State Machine để chuyển đổi trạng thái
            $preorder->transitionTo($newStatus, [
                'notes' => $validated['notes']
            ]);

            // Xử lý logic đặc biệt cho từng trạng thái
            switch ($newStatus) {
                case Preorder::STATUS_SAN_SANG_CHUYEN_DOI:
                    // Kiểm tra nếu là sách chưa phát hành
                    if ($preorder->book && $preorder->book->release_date > now()) {
                        Log::info('Preorder ready to convert but book not released yet', [
                            'preorder_id' => $preorder->id,
                            'book_release_date' => $preorder->book->release_date
                        ]);
                    }
                    break;
                case Preorder::STATUS_DA_HUY:
                    // Logic hủy đơn đã được xử lý trong markAsCancelled()
                    break;
            }

            DB::commit();

            // Gửi email thông báo nếu trạng thái thay đổi
            if ($oldStatus !== $newStatus) {
                try {
                    // Mail::to($preorder->email)->send(new PreorderStatusUpdate($preorder, $oldStatus));
                    Log::info('Status changed, email notification should be sent', [
                        'preorder_id' => $preorder->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus
                    ]);
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email cập nhật trạng thái preorder: ' . $e->getMessage());
                }
            }

            toastr()->success('Đã cập nhật trạng thái thành công.');
            return back();

        } catch (\InvalidArgumentException $e) {
            DB::rollback();
            toastr()->error($e->getMessage());
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lỗi cập nhật trạng thái preorder: ' . $e->getMessage());
            toastr()->error('Có lỗi xảy ra khi cập nhật trạng thái.');
            return back();
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

        // Kiểm tra điều kiện duyệt đơn
        if (!$preorder->canBeApproved()) {
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
            // Sử dụng State Machine để chuyển đổi trạng thái
            $preorder->transitionTo(Preorder::STATUS_DA_DUYET);
            
            Log::info('Preorder approved successfully', [
                 'preorder_id' => $preorder->id,
                 'new_status' => $preorder->status
             ]);

            toastr()->success('Đã duyệt đơn đặt trước thành công!');
            return back();

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
        Log::info('Convert to order called', [
            'preorder_id' => $preorder->id,
            'current_status' => $preorder->status,
            'request_data' => $request->all(),
            'user' => Auth::user()->email ?? 'system'
        ]);
        // dd(!$preorder->canBeConverted());
        // B1: Kiểm tra điều kiện chuyển đổi
        if (!$preorder->canBeConverted()) {
            // dd(10);
            Log::warning('Cannot convert preorder - invalid status', [
                'preorder_id' => $preorder->id,
                'current_status' => $preorder->status
            ]);
            return back()->with('error', 'Chỉ có thể chuyển đổi đơn đã được duyệt và chưa chuyển đổi.');
        }
        // dd(1);
        
        Log::info('Preorder can be converted', ['preorder_id' => $preorder->id]);
        
        // Kiểm tra đã được chuyển đổi chưa
        // dd($preorder->isConverted());
        if ($preorder->isConverted()) {
            // dd(1);
            Log::warning('Preorder already converted', ['preorder_id' => $preorder->id]);
            return back()->with('error', 'Đơn đặt trước này đã được chuyển đổi thành đơn hàng.');
        }
        // dd(10);
        
        Log::info('Preorder not yet converted, proceeding', ['preorder_id' => $preorder->id]);

        // Thêm cảnh báo xác nhận cho admin trước khi chuyển đổi
        if (!$request->has('force_convert')) {
            // dd(3);
            $bookTitle = $preorder->book->title;
            $customerName = $preorder->customer_name;
            $totalAmount = number_format($preorder->total_amount, 0, ',', '.') . ' VNĐ';
            // dd($bookTitle, $customerName, $totalAmount);
            return back()->with('warning', [
                'message' => "Bạn có chắc chắn muốn chuyển đổi đơn đặt trước của khách hàng '{$customerName}' cho sách '{$bookTitle}' (Tổng tiền: {$totalAmount}) thành đơn hàng chính thức không? Hành động này không thể hoàn tác.",
                'confirm_url' => route('admin.preorders.convert-to-order', $preorder) . '?force_convert=1',
                'preorder_id' => $preorder->id
            ]);
        }
        // dd(1);
        // Allow conversion even if book is not released yet, with a warning
        // Cho phép convert kể cả khi sách chưa phát hành (ghi log cảnh báo)
        if (!$preorder->book->isReleased()) {
            Log::info("Converting preorder to order before release date", [
                'preorder_id' => $preorder->id,
                'book_title' => $preorder->book->title,
                'release_date' => $preorder->book->release_date,
                'converted_by' => Auth::user()->email ?? 'system'
            ]);
            
            // Thêm thông báo cảnh báo cho admin
            toastr()->warning(
                'Cảnh báo: Sách "' . $preorder->book->title . '" chưa đến ngày phát hành (' . 
                $preorder->book->release_date->format('d/m/Y') . '). Đơn hàng vẫn được tạo thành công.'
            );
        }

        try {
            Log::info('Starting database transaction for conversion', ['preorder_id' => $preorder->id]);
            DB::beginTransaction();

            // B2: Kiểm tra xem có phải ebook không và tạo địa chỉ giao hàng nếu cần
            $bookFormat = $preorder->bookFormat;
            $isEbook = !empty($bookFormat->file_url); // Ebook có file_url
            $addressId = null;
            
            if (!$isEbook && ($preorder->province_name || $preorder->address)) {
                // Chỉ tạo địa chỉ cho sách vật lý và có thông tin địa chỉ
                Log::info('Creating shipping address for physical book', ['preorder_id' => $preorder->id]);
                $addressId = \Illuminate\Support\Str::uuid();
                DB::table('addresses')->insert([
                    'id' => $addressId,
                    'user_id' => $preorder->user_id,
                    'recipient_name' => $preorder->customer_name,
                    'phone' => $preorder->phone,
                    'address_detail' => $preorder->address ?? 'Địa chỉ từ đơn đặt trước',
                    'city' => $preorder->province_name ?? 'Không xác định',
                    'district' => $preorder->district_name ?? 'Không xác định',
                    'ward' => $preorder->ward_name ?? 'Không xác định',
                    'province_id' => $preorder->province_code,
                    'district_id' => $preorder->district_code,
                    'ward_code' => $preorder->ward_code,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info('Address created successfully', ['preorder_id' => $preorder->id, 'address_id' => $addressId]);
            } else {
                Log::info('Skipping address creation for ebook or missing address info', [
                    'preorder_id' => $preorder->id, 
                    'is_ebook' => $isEbook,
                    'has_address' => !empty($preorder->province_name) || !empty($preorder->address)
                ]);
            }
            
            // B3: Xác định trạng thái đơn hàng dựa trên loại sản phẩm
            Log::info('Determining order status', ['preorder_id' => $preorder->id, 'is_ebook' => $isEbook]);
            
            if ($isEbook) {
                // Ebook: trạng thái "Đã giao thành công" ngay lập tức
                $orderStatusName = 'Đã giao thành công';
            } else {
                // Sách vật lý: trạng thái "Chờ xác nhận"
                $orderStatusName = 'Chờ xác nhận';
            }
            
            $orderStatusId = DB::table('order_statuses')->where('name', $orderStatusName)->value('id');
            if (!$orderStatusId) {
                $orderStatusId = \Illuminate\Support\Str::uuid();
                DB::table('order_statuses')->insert([
                    'id' => $orderStatusId,
                    'name' => $orderStatusName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            Log::info('Order status determined', ['preorder_id' => $preorder->id, 'status' => $orderStatusName]);
            
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
            Log::info('Generating order ID and code', ['preorder_id' => $preorder->id]);
            $orderId = \Illuminate\Support\Str::uuid();
            $orderCode = 'BBE' . '-' . rand(1000, 9999);
            Log::info('Order ID and code generated', ['preorder_id' => $preorder->id, 'order_id' => $orderId, 'order_code' => $orderCode]);
            
            // B6: Xác định phương thức thanh toán cho ORDER
            Log::info('Determining payment method', ['preorder_id' => $preorder->id]);
            $paymentMethodId = $this->determinePaymentMethodForOrder($preorder);
            Log::info('Payment method determined', ['preorder_id' => $preorder->id, 'payment_method_id' => $paymentMethodId]);

            // B7: Tạo Order chính thức dựa trên dữ liệu từ preorder
            // B7: Ghi Order chính thức vào DB
            Log::info('Creating order record', ['preorder_id' => $preorder->id, 'order_id' => $orderId]);
            DB::table('orders')->insert([
                'id' => $orderId,
                'user_id' => $preorder->user_id,
                'order_code' => $orderCode,
                'total_amount' => $preorder->total_amount,
                'shipping_fee' => $isEbook ? 0 : ($preorder->shipping_fee ?? 0),
                'recipient_name' => $preorder->customer_name,
                'recipient_email' => $preorder->email,
                'address_id' => $addressId, // Null cho ebook
                'order_status_id' => $orderStatusId,
                'payment_status_id' => $preorder->payment_status === 'paid' ? $paidStatusId : $unpaidStatusId,
                'payment_method_id' => $paymentMethodId,
                'note' => 'Chuyển đổi từ đơn đặt trước #' . $preorder->id . ($isEbook ? ' (Ebook)' : ''),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('Order record created successfully', ['preorder_id' => $preorder->id, 'order_id' => $orderId]);
            
            // Tạo object Order để sử dụng cho phần còn lại
            // Nạp model Order để dùng tiếp (ví dụ hiển thị, tạo items)
            Log::info('Loading order model', ['preorder_id' => $preorder->id, 'order_id' => $orderId]);
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::error('Failed to load order model', ['preorder_id' => $preorder->id, 'order_id' => $orderId]);
                throw new \Exception('Không thể tải thông tin đơn hàng vừa tạo');
            }
            
            Log::info('Order model loaded successfully', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);

            // Tạo hóa đơn tự động nếu đơn đã thanh toán
            if ($preorder->payment_status === 'paid') {
                try {
                    $invoiceService = new InvoiceService();
                    $invoice = $invoiceService->createInvoiceForOrder($order);
                    Log::info('Invoice created for converted preorder', [
                        'preorder_id' => $preorder->id,
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create invoice for converted preorder: ' . $e->getMessage(), [
                        'preorder_id' => $preorder->id,
                        'order_id' => $order->id
                    ]);
                    // Không throw exception để không làm gián đoạn quá trình chuyển đổi
                }
            }

            // B8: Tạo OrderItem tương ứng (1 item duy nhất từ preorder)
            // B8: Tạo OrderItem một-một từ preorder
            Log::info('Creating order item', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);
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

            Log::info('Order item created successfully', ['preorder_id' => $preorder->id, 'order_item_id' => $orderItemId]);

            // B8.1: Xử lý selected_attributes nếu có (cho sách vật lý)
            Log::info('Processing selected attributes', ['preorder_id' => $preorder->id, 'selected_attributes' => $preorder->selected_attributes]);
            if (!empty($preorder->selected_attributes)) {
                $selectedAttributes = is_string($preorder->selected_attributes) 
                    ? json_decode($preorder->selected_attributes, true) 
                    : $preorder->selected_attributes;
                
                if (is_array($selectedAttributes)) {
                    foreach ($selectedAttributes as $attributeName => $attributeValue) {
                        // Tìm attribute_value_id từ attribute name và value
                        $attributeValueId = DB::table('attribute_values')
                            ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                            ->where('attributes.name', $attributeName)
                            ->where('attribute_values.value', $attributeValue)
                            ->value('attribute_values.id');
                        
                        if ($attributeValueId) {
                            DB::table('order_item_attribute_values')->insert([
                                'id' => \Illuminate\Support\Str::uuid(),
                                'order_item_id' => $orderItemId,
                                'attribute_value_id' => $attributeValueId,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } else {
                            \Log::warning("Không tìm thấy attribute_value_id cho {$attributeName}: {$attributeValue}");
                        }
                    }
                }
            }

            // B9: Cập nhật trạng thái preorder sau khi đã tạo order
            // Sử dụng State Machine để chuyển đổi trạng thái theo đúng luồng
            Log::info('Updating preorder status', ['preorder_id' => $preorder->id, 'current_status' => $preorder->status]);
            
            // Nếu đang ở trạng thái 'Đã duyệt', cần chuyển qua 'Sẵn sàng chuyển đổi' trước
            if ($preorder->status === Preorder::STATUS_DA_DUYET) {
                Log::info('Transitioning to ready to convert status', ['preorder_id' => $preorder->id]);
                $preorder->transitionTo(Preorder::STATUS_SAN_SANG_CHUYEN_DOI);
                Log::info('Transitioned to ready to convert', ['preorder_id' => $preorder->id, 'new_status' => $preorder->status]);
            }
            
            // Sau đó chuyển sang 'Đã chuyển thành đơn hàng'
            Log::info('Transitioning to converted status', ['preorder_id' => $preorder->id]); // Bắt đầu chuyển trạng thái
            $preorder->transitionTo(Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG, [  // 
                'converted_order_id' => $order->id
            ]);
            Log::info('Preorder status updated to converted', ['preorder_id' => $preorder->id, 'final_status' => $preorder->status]);

            Log::info('Committing transaction', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);
            DB::commit();
            Log::info('Transaction committed successfully', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);

            // B10: Gửi email ebook nếu là ebook và đã giao thành công
            if ($isEbook && $orderStatusName === 'Đã giao thành công') {
                try {
                    Log::info('Sending ebook delivery email', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);
                    
                    // Sử dụng email từ preorder (ebook_delivery_email hoặc email thường)
                    $deliveryEmail = $preorder->ebook_delivery_email ?: $preorder->email;
                    
                    // Gửi email với file ebook đến địa chỉ email tùy chỉnh
                    $order->load([
                        'user', 
                        'orderItems.book.authors', 
                        'orderItems.book.formats',
                        'orderItems.bookFormat',
                        'orderItems.collection'
                    ]);
                    
                    Mail::to($deliveryEmail)->send(new \App\Mail\EbookPurchaseConfirmation($order));
                    
                    Log::info('Ebook delivery email sent successfully', [
                        'preorder_id' => $preorder->id, 
                        'order_id' => $order->id,
                        'delivery_email' => $deliveryEmail
                    ]);
                    
                    toastr()->success('Đã gửi email file ebook đến: ' . $deliveryEmail);
                } catch (\Exception $e) {
                    Log::error('Failed to send ebook delivery email', [
                        'preorder_id' => $preorder->id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    toastr()->warning('Đơn hàng đã tạo thành công nhưng không thể gửi email ebook: ' . $e->getMessage());
                }
            }

            // Điều hướng sang trang chi tiết đơn hàng vừa tạo
            Log::info('Conversion completed successfully', ['preorder_id' => $preorder->id, 'order_id' => $order->id]);
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đã chuyển đổi thành đơn hàng #' . $order->id . ' thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lỗi chuyển đổi preorder thành order', [
                'preorder_id' => $preorder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi chuyển đổi đơn hàng: ' . $e->getMessage());
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
            'status' => 'required|in:' . implode(',', [
                Preorder::STATUS_CHO_DUYET,
                Preorder::STATUS_DA_DUYET,
                Preorder::STATUS_SAN_SANG_CHUYEN_DOI,
                Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG,
                Preorder::STATUS_DA_HUY
            ])
        ]);

        try {
            DB::beginTransaction();
            
            $preorders = Preorder::whereIn('id', $validated['preorder_ids'])->get();
            $updated = 0;
            
            foreach ($preorders as $preorder) {
                // Xử lý đặc biệt cho trạng thái hủy để hoàn trả stock
                if ($validated['status'] === Preorder::STATUS_DA_HUY && $preorder->status !== Preorder::STATUS_DA_HUY) {
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

    /**
     * Xác định phương thức thanh toán cho đơn hàng dựa trên preorder
     */
    private function determinePaymentMethodForOrder(Preorder $preorder)
    {
        // Ưu tiên sử dụng payment method đã chọn trong preorder
        if ($preorder->payment_method_id) {
            return $preorder->payment_method_id;
        }

        // Nếu không có, xác định dựa trên trạng thái thanh toán
        if ($preorder->payment_status === 'paid') {
            // Đã thanh toán: kiểm tra có VNPay transaction không
            if ($preorder->vnpay_transaction_id) {
                return $this->findPaymentMethodByType('vnpay');
            } else {
                return $this->findPaymentMethodByType('wallet');
            }
        } else {
            // Chưa thanh toán: mặc định COD
            return $this->findPaymentMethodByType('cod');
        }
    }

    /**
     * Tìm payment method theo loại
     */
    private function findPaymentMethodByType($type)
    {
        $searchTerms = [
            'vnpay' => ['vnpay', 'vn pay', 'vn-pay'],
            'wallet' => ['ví điện tử', 'vi dien tu', 'wallet', 'e-wallet', 'momo'],
            'cod' => ['thanh toán khi nhận hàng', 'khi nhận hàng', 'cash on delivery', 'COD', 'cod']
        ];

        $terms = $searchTerms[$type] ?? [];
        
        $paymentMethod = DB::table('payment_methods')
            ->where('is_active', true)
            ->where(function($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('name', 'like', "%{$term}%");
                }
            })
            ->first();

        Log::info("Payment method selected for type: {$type}", [
            'type' => $type,
            'method' => $paymentMethod
        ]);

        return $paymentMethod->id ?? null;
    }
}
