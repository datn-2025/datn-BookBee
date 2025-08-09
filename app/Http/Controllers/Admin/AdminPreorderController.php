<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreorderStatusUpdate;

class AdminPreorderController extends Controller
{
    /**
     * Danh sách đơn đặt trước
     */
    public function index(Request $request)
    {
        $query = Preorder::with(['user', 'book', 'bookFormat']);

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo sách
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        // Tìm kiếm theo tên khách hàng hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $preorders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Thống kê
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
        $preorderBooks = Book::where('pre_order', true)
            ->with('bookFormats')
            ->get(['id', 'title', 'cover_image']);
            
        $users = User::select('id', 'name', 'email', 'phone')
            ->orderBy('name')
            ->get();
            
        $provinces = Province::orderBy('name')->get(['id', 'name']);
        
        return view('admin.preorders.create', compact('preorderBooks', 'users', 'provinces'));
    }

    /**
     * Lưu đơn đặt trước mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_format_id' => 'required|exists:book_formats,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'province_id' => 'nullable|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
            'ward_id' => 'nullable|exists:wards,id',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed',
            'expected_delivery_date' => 'nullable|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            // Lấy thông tin sách và format
            $book = Book::findOrFail($validated['book_id']);
            $bookFormat = BookFormat::findOrFail($validated['book_format_id']);

            // Kiểm tra sách có phải pre-order không
            if (!$book->pre_order) {
                return back()->with('error', 'Sách này không hỗ trợ đặt trước.');
            }

            // Tính toán giá
            $unitPrice = $bookFormat->price;
            $totalAmount = $unitPrice * $validated['quantity'];

            // Lấy thông tin địa chỉ nếu không phải ebook
            $provinceId = null;
            $districtId = null;
            $wardId = null;
            $provinceName = null;
            $districtName = null;
            $wardName = null;

            if (!$bookFormat->format_name || !str_contains(strtolower($bookFormat->format_name), 'ebook')) {
                if ($validated['province_id']) {
                    $province = Province::find($validated['province_id']);
                    $provinceId = $province->id;
                    $provinceName = $province->name;
                }
                if ($validated['district_id']) {
                    $district = District::find($validated['district_id']);
                    $districtId = $district->id;
                    $districtName = $district->name;
                }
                if ($validated['ward_id']) {
                    $ward = Ward::find($validated['ward_id']);
                    $wardId = $ward->id;
                    $wardName = $ward->name;
                }
            }

            // Tạo preorder
            $preorder = Preorder::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'book_format_id' => $validated['book_format_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'ward_id' => $wardId,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'ward_name' => $wardName,
                'address' => $validated['address'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?: $book->release_date,
                'confirmed_at' => $validated['status'] === 'confirmed' ? now() : null
            ]);

            // Cập nhật preorder_count của sách
            $book->increment('preorder_count', $validated['quantity']);

            DB::commit();

            return redirect()->route('admin.preorders.show', $preorder)
                ->with('success', 'Đã tạo đơn đặt trước thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi tạo preorder từ admin: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo đơn đặt trước.');
        }
    }

    /**
     * Chi tiết đơn đặt trước
     */
    public function show(Preorder $preorder)
    {
        $preorder->load(['user', 'book', 'bookFormat']);
        
        return view('admin.preorders.show', compact('preorder'));
    }

    /**
     * Cập nhật trạng thái đơn đặt trước
     */
    public function updateStatus(Request $request, Preorder $preorder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $preorder->status;
        
        try {
            DB::beginTransaction();

            $preorder->update([
                'status' => $validated['status'],
                'notes' => $validated['notes']
            ]);

            // Cập nhật timestamp tương ứng
            switch ($validated['status']) {
                case 'confirmed':
                    $preorder->update(['confirmed_at' => now()]);
                    break;
                case 'shipped':
                    $preorder->update(['shipped_at' => now()]);
                    break;
                case 'delivered':
                    $preorder->update(['delivered_at' => now()]);
                    break;
            }

            DB::commit();

            // Gửi email thông báo nếu trạng thái thay đổi
            if ($oldStatus !== $validated['status']) {
                try {
                    Mail::to($preorder->email)->send(new PreorderStatusUpdate($preorder, $oldStatus));
                } catch (\Exception $e) {
                    \Log::error('Lỗi gửi email cập nhật trạng thái preorder: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Đã cập nhật trạng thái thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi cập nhật trạng thái preorder: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái.');
        }
    }

    /**
     * Duyệt đơn đặt trước
     */
    public function approvePreorder(Request $request, Preorder $preorder)
    {
        // Kiểm tra trạng thái đơn hàng - chỉ cho phép duyệt đơn đang chờ xử lý
        if ($preorder->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt đơn đang chờ xử lý.');
        }

        // Kiểm tra ngày phát hành và hiển thị cảnh báo nếu chưa phát hành
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
            $preorder->update(['status' => 'confirmed']);

            return back()->with('success', 'Đã duyệt đơn đặt trước thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi duyệt đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển đổi preorder thành order chính thức
     */
    public function convertToOrder(Request $request, Preorder $preorder)
    {
        // Kiểm tra trạng thái đơn hàng - chỉ cho phép chuyển đổi đơn đã xác nhận
        if ($preorder->status !== 'confirmed') {
            return back()->with('error', 'Chỉ có thể chuyển đổi đơn đã được xác nhận.');
        }

        // Kiểm tra sách đã phát hành
        if (!$preorder->book->isReleased()) {
            return back()->with('error', 'Không thể chuyển đổi đơn hàng khi sách chưa được phát hành.');
        }

        try {
            DB::beginTransaction();

            // Tạo Address trước
            $addressId = \Illuminate\Support\Str::uuid();
            \DB::table('addresses')->insert([
                'id' => $addressId,
                'user_id' => $preorder->user_id,
                'recipient_name' => $preorder->customer_name,
                'phone' => $preorder->phone,
                'address_detail' => $preorder->address ?? 'Địa chỉ từ đơn đặt trước',
                'city' => 'Hà Nội',
                'district' => 'Quận 1', 
                'ward' => 'Phường 1',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Tạo hoặc lấy order status và payment status
            $orderStatusId = \DB::table('order_statuses')->where('name', 'Đã Thanh Toán')->value('id');
            if (!$orderStatusId) {
                $orderStatusId = \Illuminate\Support\Str::uuid();
                \DB::table('order_statuses')->insert([
                    'id' => $orderStatusId,
                    'name' => 'Đã Thanh Toán',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $paymentStatusId = \DB::table('payment_statuses')->where('name', 'Đã Thanh Toán')->value('id');
            if (!$paymentStatusId) {
                $paymentStatusId = \Illuminate\Support\Str::uuid();
                \DB::table('payment_statuses')->insert([
                    'id' => $paymentStatusId,
                    'name' => 'Đã Thanh Toán',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Tạo Order bằng raw SQL
            $orderId = \Illuminate\Support\Str::uuid();
            $orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);
            
            \DB::table('orders')->insert([
                'id' => $orderId,
                'user_id' => $preorder->user_id,
                'order_code' => $orderCode,
                'total_amount' => $preorder->total_amount,
                'address_id' => $addressId,
                'order_status_id' => $orderStatusId,
                'payment_status_id' => $paymentStatusId,
                'note' => 'Chuyển đổi từ đơn đặt trước #' . $preorder->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Tạo object Order để sử dụng cho phần còn lại
            $order = Order::find($orderId);

            // Tạo OrderItem bằng raw SQL
            $orderItemId = \Illuminate\Support\Str::uuid();
            \DB::table('order_items')->insert([
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

            // Cập nhật trạng thái preorder
            $preorder->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'notes' => ($preorder->notes ? $preorder->notes . "\n\n" : '') . 
                          'Đã chuyển đổi thành đơn hàng #' . $order->id
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đã chuyển đổi thành đơn hàng #' . $order->id . ' thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi chuyển đổi preorder thành order: ' . $e->getMessage());
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

        $updated = Preorder::whereIn('id', $validated['preorder_ids'])
            ->update(['status' => $validated['status']]);

        return back()->with('success', "Đã cập nhật {$updated} đơn đặt trước.");
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
