<?php

namespace App\Http\Controllers;

use App\Models\Preorder;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PreorderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Preorder::with(['user', 'book', 'bookFormat', 'paymentMethod']);

        // Tìm kiếm theo tên, email, số điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $preorders = $query->orderBy('created_at', 'desc')->paginate(20);
            
        return view('admin.preorders.index', compact('preorders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.preorders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:books,id',
                'book_format_id' => 'nullable|exists:book_formats,id',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'customer_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'province_code' => 'required|string|max:10',
                'province_name' => 'required|string|max:255',
                'district_code' => 'required|string|max:10',
                'district_name' => 'required|string|max:255',
                'ward_code' => 'required|string|max:10',
                'ward_name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1|max:5',
                'selected_attributes' => 'nullable|array',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get book and format info
            $book = Book::findOrFail($request->book_id);
            $bookFormat = $request->book_format_id ? BookFormat::find($request->book_format_id) : null;

            // Calculate prices
            $unitPrice = $bookFormat ? $bookFormat->price : $book->price;
            
            // Filter selected attributes based on book format (ebook vs physical)
            $filteredAttributes = [];
            if ($request->selected_attributes && is_array($request->selected_attributes)) {
                $isEbook = $bookFormat && stripos($bookFormat->format_name, 'ebook') !== false;
                
                if ($isEbook) {
                    // For ebook: only keep language attributes
                    $languageAttributeIds = \App\Models\AttributeValue::whereIn('id', $request->selected_attributes)
                        ->with('attribute')
                        ->get()
                        ->filter(function($attrValue) {
                            $attributeName = strtolower($attrValue->attribute->name);
                            return strpos($attributeName, 'ngôn ngữ') !== false || 
                                   strpos($attributeName, 'language') !== false;
                        })
                        ->pluck('id')
                        ->toArray();
                    
                    $filteredAttributes = $languageAttributeIds;
                } else {
                    // For physical books: keep all attributes
                    $filteredAttributes = $request->selected_attributes;
                }
            }
            
            // Add attribute costs if any
            $attributeCost = 0;
            if (!empty($filteredAttributes)) {
                foreach ($filteredAttributes as $attributeValueId) {
                    // Here you can add logic to get attribute value and add its price
                    // For now, we'll just store the attribute IDs
                }
            }

            // Calculate total with shipping fee and attribute costs
            $shippingFee = 30000; // 30k VND shipping fee
            $totalAmount = (($unitPrice + $attributeCost) * $request->quantity) + $shippingFee;

            // Create preorder
            $preorder = Preorder::create([
                'user_id' => Auth::id(),
                'book_id' => $request->book_id,
                'book_format_id' => $request->book_format_id,
                'payment_method_id' => $request->payment_method_id,
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'province_code' => $request->province_code,
                'province_name' => $request->province_name,
                'district_code' => $request->district_code,
                'district_name' => $request->district_name,
                'ward_code' => $request->ward_code,
                'ward_name' => $request->ward_name,
                'quantity' => $request->quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'selected_attributes' => $filteredAttributes, // Use filtered attributes
                'status' => Preorder::STATUS_PENDING,
                'notes' => $request->notes,
                'expected_delivery_date' => $book->publication_date
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt trước sách thành công! Chúng tôi sẽ liên hệ với bạn khi sách được phát hành.',
                'preorder_id' => $preorder->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt trước sách: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $preorder = Preorder::with(['user', 'book', 'bookFormat', 'paymentMethod'])->findOrFail($id);
        return view('admin.preorders.show', compact('preorder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $preorder = Preorder::with(['user', 'book', 'bookFormat', 'paymentMethod'])->findOrFail($id);
        return view('admin.preorders.edit', compact('preorder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $preorder = Preorder::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', [
                Preorder::STATUS_PENDING,
                Preorder::STATUS_CONFIRMED,
                Preorder::STATUS_PROCESSING,
                Preorder::STATUS_SHIPPED,
                Preorder::STATUS_DELIVERED,
                Preorder::STATUS_CANCELLED
            ]),
            'notes' => 'nullable|string',
            'expected_delivery_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update timestamps based on status
        $updateData = $request->only(['status', 'notes', 'expected_delivery_date']);
        
        if ($request->status === Preorder::STATUS_CONFIRMED && !$preorder->confirmed_at) {
            $updateData['confirmed_at'] = now();
        }
        
        if ($request->status === Preorder::STATUS_SHIPPED && !$preorder->shipped_at) {
            $updateData['shipped_at'] = now();
        }
        
        if ($request->status === Preorder::STATUS_DELIVERED && !$preorder->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        $preorder->update($updateData);

        return redirect()->route('admin.preorders.show', $preorder->id)
            ->with('success', 'Cập nhật đơn đặt trước thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $preorder = Preorder::findOrFail($id);
        $preorder->delete();
        
        return redirect()->route('admin.preorders.index')
            ->with('success', 'Xóa đơn đặt trước thành công!');
    }

    /**
     * Get user's preorders
     */
    public function userPreorders()
    {
        $preorders = Preorder::with(['book', 'bookFormat'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('clients.preorders.index', compact('preorders'));
    }

    /**
     * Cancel user's preorder
     */
    public function cancel(string $id)
    {
        $preorder = Preorder::where('user_id', Auth::id())->findOrFail($id);
        
        if ($preorder->status !== Preorder::STATUS_PENDING) {
            return back()->with('error', 'Chỉ có thể hủy đơn đặt trước đang chờ xác nhận!');
        }
        
        $preorder->update(['status' => Preorder::STATUS_CANCELLED]);
        
        return back()->with('success', 'Hủy đơn đặt trước thành công!');
    }

    /**
     * Confirm preorder and create order
     */
    public function confirmAndCreateOrder(string $id)
    {
        try {
            DB::beginTransaction();

            $preorder = Preorder::with(['user', 'book', 'bookFormat', 'paymentMethod'])->findOrFail($id);
            
            // Kiểm tra trạng thái preorder
            if ($preorder->status !== Preorder::STATUS_PENDING) {
                DB::rollback();
                return back()->with('error', 'Chỉ có thể xác nhận đơn đặt trước đang chờ xác nhận!');
            }

            // Tạo hoặc tìm địa chỉ giao hàng (đơn giản hóa)
            $address = Address::where('user_id', $preorder->user_id)->first();
            
            if (!$address) {
                $address = Address::create([
                    'user_id' => $preorder->user_id,
                    'province_code' => $preorder->province_code ?? '01',
                    'province_name' => $preorder->province_name ?? 'Hà Nội',
                    'district_code' => $preorder->district_code ?? '001',
                    'district_name' => $preorder->district_name ?? 'Ba Đình',
                    'ward_code' => $preorder->ward_code ?? '00001',
                    'ward_name' => $preorder->ward_name ?? 'Phúc Xá',
                    'address_detail' => $preorder->address ?? 'Địa chỉ mặc định',
                    'recipient_name' => $preorder->customer_name,
                    'recipient_phone' => $preorder->phone,
                    'is_default' => true,
                ]);
            }

            // Lấy hoặc tạo order status mặc định
            $pendingOrderStatus = OrderStatus::where('name', 'LIKE', '%chờ%')
                ->orWhere('name', 'LIKE', '%pending%')
                ->first();
            
            if (!$pendingOrderStatus) {
                $pendingOrderStatus = OrderStatus::create(['name' => 'Chờ xác nhận']);
            }

            // Lấy hoặc tạo payment status - Đã thanh toán (vì preorder đã xác nhận)
            $paidPaymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')
                ->orWhere('name', 'Đã thanh toán')
                ->orWhere('name', 'LIKE', '%Đã%Thanh%Toán%')
                ->orWhere('name', 'LIKE', '%paid%')
                ->orWhere('name', 'LIKE', '%completed%')
                ->first();
            
            if (!$paidPaymentStatus) {
                $paidPaymentStatus = PaymentStatus::create(['name' => 'Đã Thanh Toán']);
            }
            
            // Debug: Log payment status để kiểm tra
            \Log::info('Preorder confirmAndCreateOrder - Payment Status:', [
                'payment_status_id' => $paidPaymentStatus->id,
                'payment_status_name' => $paidPaymentStatus->name
            ]);

            // Tạo mã đơn hàng
            $orderCode = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);

            // Tạo đơn hàng mới với đầy đủ field
            $order = Order::create([
                'user_id' => $preorder->user_id,
                'address_id' => $address->id,
                'order_code' => $orderCode,
                'total_amount' => $preorder->total_amount,
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'order_status_id' => $pendingOrderStatus->id,
                'payment_method_id' => $preorder->payment_method_id,
                'payment_status_id' => $paidPaymentStatus->id,
                'recipient_name' => $preorder->customer_name,
                'recipient_phone' => $preorder->phone,
                'recipient_email' => $preorder->email,
                'note' => $preorder->notes,
            ]);

            // Tạo order item
            $orderItem = OrderItem::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'order_id' => $order->id,
                'book_id' => $preorder->book_id,
                'book_format_id' => $preorder->book_format_id,
                'quantity' => $preorder->quantity,
                'price' => $preorder->unit_price,
                'total' => $preorder->unit_price * $preorder->quantity,
            ]);

            // Sync attributes nếu có
            if ($preorder->selected_attributes && is_array($preorder->selected_attributes) && !empty($preorder->selected_attributes)) {
                try {
                    $orderItem->attributeValues()->sync($preorder->selected_attributes);
                } catch (\Exception $e) {
                    // Ignore attribute sync errors for now
                }
            }

            // Cập nhật trạng thái preorder
            $preorder->update([
                'status' => Preorder::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Xác nhận đơn đặt trước thành công! Đơn hàng #' . $orderCode . ' đã được tạo.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log lỗi chi tiết
            \Log::error('Error creating order from preorder: ' . $e->getMessage(), [
                'preorder_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Có lỗi xảy ra khi xác nhận đơn: ' . $e->getMessage());
        }
    }
}
