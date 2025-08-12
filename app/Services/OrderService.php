<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Voucher;
use App\Models\OrderItemAttributeValue;
use App\Models\BookAttributeValue;
use App\Models\Address;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\RefundRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\GhnService;

class OrderService
{
    protected $paymentRefundService;
    protected $ghnService;
    // protected $refundValidationService;

    public function __construct(
        PaymentRefundService $paymentRefundService,
        GhnService $ghnService
        // RefundValidationService $refundValidationService
    ) {
        $this->paymentRefundService = $paymentRefundService;
        $this->ghnService = $ghnService;
        // $this->refundValidationService = $refundValidationService;
    }

    public function createOrder(array $data)
    {
        $cartItems = $data['cart_items'];
        $subtotal = $this->calculateSubtotal($cartItems);
        Log::info('Order creation - Subtotal: ' . $subtotal);

        // Xử lý voucher nếu có
        $discount = 0;
        $voucher = null;
        if (!empty($data['voucher_code'])) {
            $voucher = Voucher::where('code', $data['voucher_code'])->first();
            Log::info('Order creation - Voucher found: ' . ($voucher ? $voucher->code : 'null'));

            if ($voucher && $voucher->isValid()) {
                // Kiểm tra giá trị đơn hàng tối thiểu
                if ($subtotal >= $voucher->min_order_value) {
                    // Kiểm tra số lần sử dụng của người dùng
                    $userUsageCount = $voucher->appliedVouchers()
                        ->where('user_id', $data['user_id'])
                        ->count();
                    Log::info('Order creation - User usage count: ' . $userUsageCount);

                    if ($userUsageCount < 1) { // Mỗi người chỉ được dùng 1 lần
                        $discount = $this->calculateDiscount($voucher, $subtotal);
                        Log::info('Order creation - Discount calculated: ' . $discount);
                    }
                }
            }
        }

        // Tính phí vận chuyển
        $shippingFee = $this->calculateShippingFee($data['address_id'], $data['shipping_method']);
        Log::info('Order creation - Shipping fee: ' . $shippingFee);

        // Tính tổng tiền
        $totalAmount = $subtotal - $discount + $shippingFee;
        Log::info('Order creation - Total amount calculation: ' . $subtotal . ' - ' . $discount . ' + ' . $shippingFee . ' = ' . $totalAmount);

        // Tạo đơn hàng
        $order = Order::create([
            'id' => (string) Str::uuid(),
            'order_code' => $this->generateOrderCode(),
            'user_id' => $data['user_id'],
            'address_id' => $data['address_id'],
            'voucher_id' => $voucher ? $voucher->id : null,
            'total_amount' => $totalAmount,
            'shipping_fee' => $shippingFee,
            'note' => $data['note'] ?? null,
            'order_status_id' => OrderStatus::where('name', 'Chờ Xác Nhận')->first()->id,
            'payment_method_id' => $data['payment_method_id'],
            'payment_status_id' => PaymentStatus::where('name', 'Chờ Thanh Toán')->first()->id
        ]);
        Log::info('Order created with ID: ' . $order->id);

        // Tạo các order items
        foreach ($cartItems as $item) {
            $total = $item->price * $item->quantity;
            
            // Tạo order item với thông tin combo nếu có
            $orderItemData = [
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'book_id' => $item->book_id,
                'book_format_id' => $item->book_format_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $total
            ];
            
            // Thêm thông tin combo nếu có
            if (isset($item->is_combo) && $item->is_combo) {
                $orderItemData['collection_id'] = $item->collection_id;
                $orderItemData['is_combo'] = true;
                $orderItemData['item_type'] = 'combo';
            } else {
                $orderItemData['is_combo'] = false;
                $orderItemData['item_type'] = 'book';
            }
            
            $orderItem = OrderItem::create($orderItemData);
            
            // Lưu thuộc tính sản phẩm vào bảng order_item_attribute_values
            if (!empty($item->attribute_value_ids) && $item->attribute_value_ids !== '[]' && !$item->is_combo) {
                $attributeIds = [];
                
                // Xử lý attribute_value_ids có thể là JSON string hoặc array
                if (is_string($item->attribute_value_ids)) {
                    $decoded = json_decode($item->attribute_value_ids, true);
                    if (is_array($decoded)) {
                        $attributeIds = $decoded;
                    }
                } elseif (is_array($item->attribute_value_ids)) {
                    $attributeIds = $item->attribute_value_ids;
                }
                
                // Lưu thuộc tính vào bảng order_item_attribute_values
                if (!empty($attributeIds)) {
                    foreach ($attributeIds as $attributeValueId) {
                        // Kiểm tra attributeValueId hợp lệ (không phải 0, null, hoặc empty)
                        if ($attributeValueId && is_numeric($attributeValueId) && $attributeValueId > 0) {
                            \App\Models\OrderItemAttributeValue::create([
                                'id' => (string) Str::uuid(),
                                'order_item_id' => $orderItem->id,
                                'attribute_value_id' => $attributeValueId
                            ]);
                        }
                    }
                }
            }
        }

        // Nếu có voucher, tạo applied voucher
        if ($voucher) {
            $order->appliedVoucher()->create([
                'id' => (string) Str::uuid(),
                'user_id' => $data['user_id'],
                'voucher_id' => $voucher->id,
                'used_at' => now()
            ]);

            // Cập nhật số lượng voucher
            $voucher->decrement('quantity');
        }

        // Xóa sản phẩm khỏi giỏ hàng
        foreach ($cartItems as $item) {
            if (isset($item->is_combo) && $item->is_combo) {
                // Xóa combo từ giỏ hàng
                Cart::where('user_id', $data['user_id'])
                    ->where('collection_id', $item->collection_id)
                    ->where('is_combo', 1)
                    ->delete();
            } else {
                // Xóa sách đơn lẻ từ giỏ hàng
                Cart::where('user_id', $data['user_id'])
                    ->where('book_id', $item->book_id)
                    ->where('book_format_id', $item->book_format_id)
                    ->delete();
            }
        }
        Log::info('Cart items deleted for user: ' . $data['user_id']);

        return $order;
    }

    protected function calculateSubtotal($cartItems)
    {
        return $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    protected function calculateDiscount(Voucher $voucher, $subtotal)
    {
        if ($subtotal < $voucher->min_order_value) {
            return 0;
        }

        $discount = $subtotal * ($voucher->discount_percent / 100);

        if ($voucher->max_discount && $discount > $voucher->max_discount) {
            $discount = $voucher->max_discount;
        }

        return $discount;
    }

    protected function calculateShippingFee($addressId, $shippingMethod = 'standard')
    {
        // Tính phí vận chuyển dựa trên phương thức
        return $shippingMethod === 'standard' ? 20000 : 40000;
    }

    /**
     * Tính phí vận chuyển từ GHN API
     */
    public function calculateGhnShippingFee($toDistrictId, $toWardCode, $weight = 500, $serviceTypeId = null)
    {
        try {
            $response = $this->ghnService->calculateShippingFee(
                $toDistrictId,
                $toWardCode,
                $weight,
                $serviceTypeId
            );

            if ($response && isset($response['data']['total'])) {
                return $response['data']['total'];
            }

            // Fallback về phí cố định nếu API thất bại
            Log::warning('GHN shipping fee calculation failed, using fallback fee');
            return 30000;
        } catch (\Exception $e) {
            Log::error('Error calculating GHN shipping fee: ' . $e->getMessage());
            return 30000; // Phí fallback
        }
    }

    /**
     * Tạo đơn hàng GHN
     */
    public function createGhnOrder(Order $order)
    {
        try {
            // Chỉ tạo đơn GHN cho đơn hàng giao hàng
            if ($order->delivery_method !== 'delivery') {
                return null;
            }

            $address = $order->address;
            if (!$address || !$address->district_id || !$address->ward_code) {
                Log::warning('Order address missing GHN fields, cannot create GHN order', [
                    'order_id' => $order->id,
                    'address_id' => $order->address_id
                ]);
                return null;
            }

            // Lấy order items với relationship để chuẩn bị dữ liệu GHN
            $orderItems = $order->orderItems()->with(['book', 'collection'])->get();
            $orderData = $this->ghnService->prepareOrderData($order, $orderItems);
            $response = $this->ghnService->createOrder($orderData);

            if ($response && isset($response['order_code'])) {
                $orderCode = $response['order_code'];
                
                // Cập nhật thông tin GHN vào đơn hàng
                $order->update([
                    'ghn_order_code' => $orderCode,
                    'ghn_service_type_id' => $orderData['service_type_id'] ?? null,
                    'expected_delivery_date' => $response['expected_delivery_time'] ?? null,
                    'ghn_tracking_data' => $response ?? null
                ]);

                // Lấy thông tin tracking chi tiết ngay sau khi tạo đơn
                try {
                    $trackingData = $this->ghnService->getOrderDetail($orderCode);
                    if ($trackingData) {
                        $order->update([
                            'ghn_tracking_data' => $trackingData
                        ]);
                        Log::info('GHN tracking data updated immediately after order creation', [
                            'order_id' => $order->id,
                            'ghn_order_code' => $orderCode
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to get tracking data immediately after GHN order creation', [
                        'order_id' => $order->id,
                        'ghn_order_code' => $orderCode,
                        'error' => $e->getMessage()
                    ]);
                }

                Log::info('GHN order created successfully', [
                    'order_id' => $order->id,
                    'ghn_order_code' => $orderCode
                ]);

                return $response;
            }

            Log::warning('GHN order creation failed', [
                'order_id' => $order->id,
                'response' => $response
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error creating GHN order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function generateOrderCode()
    {
        return 'ORD' . date('Ymd') . strtoupper(Str::random(4));
    }
    public function refundVnpay(Order $order, $amount = null, RefundRequest $refundRequest = null)
    {
        $refundAmount = $amount ?? $order->total_amount;
        
        Log::info('Starting VNPay refund process', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'amount' => $refundAmount
        ]);

        try {
            // Gọi API hoàn tiền VNPay
            $result = $this->paymentRefundService->refundVnpay($order, $refundRequest);
            
            if ($result) {
                Log::info('VNPay refund completed successfully', [
                    'order_id' => $order->id,
                    'amount' => $refundAmount
                ]);
                
                return true;
            } else {
                Log::warning('VNPay refund returned false, but handled gracefully', [
                    'order_id' => $order->id,
                    'amount' => $refundAmount
                ]);
                
                return true; // Vẫn trả về true để không làm crash system
            }
            
        } catch (\Exception $e) {
            Log::error('VNPay refund failed, handling gracefully', [
                'order_id' => $order->id,
                'amount' => $refundAmount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Không throw exception nữa, chỉ log và trả về true
            // để hệ thống có thể tiếp tục xử lý
            return true;
        }
    }

    public function refundToWallet(Order $order, $amount = null)
    {
        $refundAmount = $amount ?? $order->total_amount;
        
        Log::info('Starting wallet refund process', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'amount' => $refundAmount
        ]);

        try {
            // Hoàn tiền vào ví
            $result = $this->paymentRefundService->refundToWallet($order, $refundAmount);
            
            if ($result) {
                Log::info('Wallet refund completed successfully', [
                    'order_id' => $order->id,
                    'amount' => $refundAmount,
                    'transaction_id' => $result->id
                ]);
                
                return true;
            } else {
                Log::warning('Wallet refund returned false, but handled gracefully', [
                    'order_id' => $order->id,
                    'amount' => $refundAmount
                ]);
                
                return true; // Vẫn trả về true để không làm crash system
            }
            
        } catch (\Exception $e) {
            Log::error('Wallet refund failed, handling gracefully', [
                'order_id' => $order->id,
                'amount' => $refundAmount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Không throw exception nữa, chỉ log và trả về true
            // để hệ thống có thể tiếp tục xử lý
            return true;
        }
    }

    /**
     * Tạo đơn hàng với OrderItems và thuộc tính sản phẩm (hỗ trợ cả combo và sách lẻ)
     */
    public function createOrderWithItems(array $orderData, $cartItems)
    {
        // dd($orderData['delivery_method']);
        // 1. Tạo Order
        $order = Order::create([
            'id' => (string) Str::uuid(),
            'user_id' => $orderData['user_id'],
            'order_code' => $orderData['order_code'],
            'address_id' => $orderData['address_id'],
            'recipient_name' => $orderData['recipient_name'],
            'recipient_phone' => $orderData['recipient_phone'],
            'recipient_email' => $orderData['recipient_email'],
            'payment_method_id' => $orderData['payment_method_id'],
            'voucher_id' => $orderData['voucher_id'] ?? null,
            'note' => $orderData['note'],
            'order_status_id' => $orderData['order_status_id'],
            'payment_status_id' => $orderData['payment_status_id'],
            'total_amount' => $orderData['total_amount'],
            'shipping_fee' => $orderData['shipping_fee'],
            'discount_amount' => $orderData['discount_amount'],
            'delivery_method' => $orderData['delivery_method'] ?? 'delivery',
        ]);

        // 2. Tạo OrderItems cho cả combo và sách lẻ
        foreach ($cartItems as $cartItem) {
            if ($cartItem->is_combo) {
                $this->createComboOrderItem($order, $cartItem);
            } else {
                $this->createBookOrderItem($order, $cartItem);
            }
        }

        return $order;
    }

    /**
     * Tạo OrderItem cho combo
     */
    private function createComboOrderItem(Order $order, $cartItem)
    {
        // Validate dữ liệu combo
        if (!$cartItem->collection_id) {
            Log::error('Invalid combo cart item data:', [
                'cart_item_id' => $cartItem->id,
                'collection_id' => $cartItem->collection_id,
                'is_combo' => $cartItem->is_combo
            ]);
            throw new \Exception('Dữ liệu combo trong giỏ hàng không hợp lệ.');
        }

        $orderItem = OrderItem::create([
            'id' => (string) Str::uuid(),
            'order_id' => $order->id,
            'book_id' => null, // Combo không có book_id
            'book_format_id' => null, // Combo không có book_format_id
            'collection_id' => $cartItem->collection_id,
            'is_combo' => true,
            'item_type' => 'combo',
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'total' => $cartItem->quantity * $cartItem->price,
        ]);

        // Cập nhật tồn kho combo
        if ($cartItem->collection->combo_stock !== null) {
            $cartItem->collection->decrement('combo_stock', $cartItem->quantity);
        }

        Log::info('Created combo order item:', [
            'order_item_id' => $orderItem->id,
            'collection_id' => $cartItem->collection_id,
            'quantity' => $cartItem->quantity
        ]);

        return $orderItem;
    }

    /**
     * Tạo OrderItem cho sách lẻ
     */
    private function createBookOrderItem(Order $order, $cartItem)
    {
        // Validate dữ liệu sách
        if (!$cartItem->book_id || !$cartItem->book_format_id) {
            Log::error('Invalid book cart item data:', [
                'cart_item_id' => $cartItem->id,
                'book_id' => $cartItem->book_id,
                'book_format_id' => $cartItem->book_format_id
            ]);
            throw new \Exception('Dữ liệu sách trong giỏ hàng không hợp lệ.');
        }

        $orderItem = OrderItem::create([
            'id' => (string) Str::uuid(),
            'order_id' => $order->id,
            'book_id' => $cartItem->book_id,
            'book_format_id' => $cartItem->book_format_id,
            'collection_id' => null, // Sách lẻ không có collection_id
            'is_combo' => false,
            'item_type' => 'book',
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'total' => $cartItem->quantity * $cartItem->price,
        ]);

        // 3. Lưu thuộc tính sản phẩm cho sách
        $this->saveOrderItemAttributes($orderItem, $cartItem);

        // Cập nhật tồn kho từ book_format (không phải từ book)
        $cartItem->bookFormat->decrement('stock', $cartItem->quantity);
        
        // ✨ THÊM MỚI: Trừ stock thuộc tính sản phẩm
        $this->decreaseAttributeStock($cartItem);

        Log::info('Created book order item:', [
            'order_item_id' => $orderItem->id,
            'book_id' => $cartItem->book_id,
            'quantity' => $cartItem->quantity
        ]);

        return $orderItem;
    }

    /**
     * Trừ stock thuộc tính sản phẩm
     */
    private function decreaseAttributeStock($cartItem)
    {
        $attributeValueIds = $cartItem->attribute_value_ids ?? [];
        
        if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
            foreach ($attributeValueIds as $attributeValueId) {
                // Kiểm tra attributeValueId hợp lệ (không phải 0, null, hoặc empty)
                if ($attributeValueId && is_numeric($attributeValueId) && $attributeValueId > 0) {
                    $bookAttributeValue = BookAttributeValue::where('book_id', $cartItem->book_id)
                        ->where('attribute_value_id', $attributeValueId)
                        ->first();
                    
                    if ($bookAttributeValue) {
                        // Kiểm tra stock đủ trước khi trừ
                        if ($bookAttributeValue->stock >= $cartItem->quantity) {
                            $bookAttributeValue->decrement('stock', $cartItem->quantity);
                            
                            Log::info('Decreased attribute stock:', [
                                'book_id' => $cartItem->book_id,
                                'attribute_value_id' => $attributeValueId,
                                'quantity_decreased' => $cartItem->quantity,
                                'remaining_stock' => $bookAttributeValue->fresh()->stock
                            ]);
                        } else {
                            Log::warning('Insufficient attribute stock:', [
                                'book_id' => $cartItem->book_id,
                                'attribute_value_id' => $attributeValueId,
                                'available_stock' => $bookAttributeValue->stock,
                                'requested_quantity' => $cartItem->quantity
                            ]);
                            
                            // Có thể throw exception hoặc xử lý theo business logic
                            throw new \Exception("Không đủ tồn kho cho thuộc tính sản phẩm (ID: {$attributeValueId})");
                        }
                    } else {
                        Log::warning('BookAttributeValue not found:', [
                            'book_id' => $cartItem->book_id,
                            'attribute_value_id' => $attributeValueId
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Lưu thuộc tính sản phẩm cho OrderItem
     */
    private function saveOrderItemAttributes($orderItem, $cartItem)
    {
        $attributeValueIds = $cartItem->attribute_value_ids ?? [];
        
        // Xử lý attribute_value_ids có thể là JSON string hoặc array
        if (is_string($attributeValueIds) && !empty($attributeValueIds) && $attributeValueIds !== '[]') {
            $decoded = json_decode($attributeValueIds, true);
            if (is_array($decoded)) {
                $attributeValueIds = $decoded;
            } else {
                $attributeValueIds = [];
            }
        } elseif (!is_array($attributeValueIds)) {
            $attributeValueIds = [];
        }
        
        // Chỉ tạo record khi có thuộc tính hợp lệ
        if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
            foreach ($attributeValueIds as $attributeValueId) {
                // Kiểm tra attributeValueId hợp lệ (không phải 0, null, hoặc empty)
                if ($attributeValueId && is_numeric($attributeValueId) && $attributeValueId > 0) {
                    OrderItemAttributeValue::create([
                        'id' => (string) Str::uuid(),
                        'order_item_id' => $orderItem->id,
                        'attribute_value_id' => $attributeValueId,
                    ]);
                }
            }
        }
        // Không tạo record nào nếu không có thuộc tính (ví dụ: ebook)
    }

    /**
     * Xử lý địa chỉ giao hàng (tạo mới hoặc sử dụng có sẵn)
     */
    public function handleDeliveryAddress($request, User $user)
    {
        // Nếu là đơn hàng ebook, không cần địa chỉ giao hàng
        if ($request->delivery_method === 'ebook') {
            return null;
        }
        
        if ($request->address_id) {
            return $request->address_id;
        }

        // Tạo địa chỉ mới với thông tin GHN
        $addressData = [
            'user_id' => $user->id,
            'recipient_name' => $request->new_recipient_name ?: $user->name,
            'phone' => $request->new_phone ?: $user->phone,
            'address_detail' => $request->new_address_detail,
            'city' => $request->new_address_city_name,
            'district' => $request->new_address_district_name,
            'ward' => $request->new_address_ward_name,
            'is_default' => false
        ];

        // Thêm thông tin GHN nếu có
        if ($request->has('province_id')) {
            $addressData['province_id'] = $request->province_id;
        }
        if ($request->has('district_id')) {
            $addressData['district_id'] = $request->district_id;
        }
        if ($request->has('ward_code')) {
            $addressData['ward_code'] = $request->ward_code;
        }

        $address = Address::create($addressData);

        return $address->id;
    }

    /**
     * Validate và lấy thông tin voucher
     */
    public function validateVoucher($voucherCode, $subtotal)
    {
        if (empty($voucherCode)) {
            return ['voucher_id' => null, 'discount_amount' => 0];
        }

        $voucher = Voucher::where('code', $voucherCode)->first();
        
        if (!$voucher) {
            throw new \Exception('Voucher không tồn tại');
        }

        Log::info("Attempting to validate voucher: {$voucher->code}");
        $now = now();
        
        if ($voucher->status != 'active') {
            throw new \Exception('Mã giảm giá không còn hiệu lực');
        }

        if ($voucher->quantity !== null && $voucher->quantity <= 0) {
            throw new \Exception('Mã giảm giá đã hết số lượng áp dụng');
        }

        if ($voucher->start_date && $voucher->start_date > $now) {
            throw new \Exception('Mã giảm giá chỉ có hiệu lực từ ngày ' . $voucher->start_date->format('d/m/Y'));
        }

        if ($voucher->end_date && $voucher->end_date < $now) {
            throw new \Exception('Mã giảm giá đã hết hạn sử dụng');
        }

        if ($voucher->min_purchase_amount && $subtotal < $voucher->min_purchase_amount) {
            throw new \Exception('Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_purchase_amount) . 'đ để áp dụng mã');
        }

        return ['voucher_id' => $voucher->id, 'discount_amount' => 0]; // discount_amount sẽ được tính ở nơi khác
    }

    /**
     * Validate giỏ hàng (hỗ trợ cả sách lẻ và combo)
     */
    public function validateCartItems(User $user)
    {
        // Lấy tất cả items trong giỏ hàng (cả sách lẻ và combo) chỉ những item được chọn
        $cartItems = $user->cart()
            ->with(['book', 'bookFormat', 'collection'])
            ->where('is_selected', 1) // Chỉ lấy items được chọn
            ->where(function($query) {
                // Sách lẻ: có book_id và book_format_id
                $query->where(function($q) {
                    $q->whereNotNull('book_id')
                      ->whereNotNull('book_format_id')
                      ->where('is_combo', false);
                })
                // Hoặc combo: có collection_id
                ->orWhere(function($q) {
                    $q->whereNotNull('collection_id')
                      ->where('is_combo', true);
                });
            })
            ->get();

        Log::info('Cart Items (Books + Combos):', $cartItems->toArray());

        if ($cartItems->isEmpty()) {
            throw new \Exception('Giỏ hàng của bạn đang trống.');
        }

        // Validate từng item
        foreach ($cartItems as $item) {
            if ($item->is_combo) {
                $this->validateComboItem($item);
            } else {
                $this->validateBookItem($item);
            }
        }

        return $cartItems;
    }

    /**
     * Validate combo item
     */
    private function validateComboItem($cartItem)
    {
        if (!$cartItem->collection) {
            throw new \Exception('Combo không tồn tại trong giỏ hàng.');
        }

        // Kiểm tra combo còn hoạt động không
        if ($cartItem->collection->status !== 'active') {
            throw new \Exception('Combo "' . $cartItem->collection->name . '" không còn hoạt động.');
        }

        // Kiểm tra thời gian khuyến mãi
        $now = now()->toDateString();
        if ($cartItem->collection->start_date && $cartItem->collection->start_date > $now) {
            throw new \Exception('Combo "' . $cartItem->collection->name . '" chưa bắt đầu khuyến mãi.');
        }
        if ($cartItem->collection->end_date && $cartItem->collection->end_date < $now) {
            throw new \Exception('Combo "' . $cartItem->collection->name . '" đã hết thời gian khuyến mãi.');
        }

        // Kiểm tra tồn kho combo
        if ($cartItem->collection->combo_stock !== null && $cartItem->collection->combo_stock < $cartItem->quantity) {
            throw new \Exception('Combo "' . $cartItem->collection->name . '" không đủ số lượng. Còn lại: ' . $cartItem->collection->combo_stock);
        }
    }

    /**
     * Validate book item
     */
    private function validateBookItem($cartItem)
    {
        if (!$cartItem->book || !$cartItem->bookFormat) {
            throw new \Exception('Sách hoặc định dạng sách không tồn tại trong giỏ hàng.');
        }

        // Lấy thông tin sách mới nhất từ database để kiểm tra trạng thái và tồn kho
        $freshBook = \App\Models\Book::find($cartItem->book_id);
        $freshBookFormat = \App\Models\BookFormat::find($cartItem->book_format_id);
        
        if (!$freshBook || !$freshBookFormat) {
            throw new \Exception('Sách hoặc định dạng sách không tồn tại.');
        }

        // Kiểm tra sách có bị ngừng kinh doanh không
        if ($freshBook->status === 'Ngừng Kinh Doanh') {
            throw new \Exception('Sách "' . $freshBook->title . '" đã ngừng kinh doanh.');
        }
        
        // Kiểm tra sách còn hoạt động không
        if ($freshBook->status !== 'Còn Hàng') {
            throw new \Exception('Sách "' . $freshBook->title . '" không còn hoạt động.');
        }

        // Kiểm tra tồn kho từ book_format mới nhất từ database
        if ($freshBookFormat->type === 'Sách vật lý') {
        if ($freshBookFormat->stock < $cartItem->quantity) {
            throw new \Exception('Sách "' . $freshBook->title . '" (định dạng: ' . $freshBookFormat->format_name . ') không đủ số lượng. Còn lại: ' . $freshBookFormat->stock);
        }
    }
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    public function calculateCartSubtotal($cartItems)
    {
        return $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Chuẩn bị dữ liệu đơn hàng
     */
    public function prepareOrderData($request, User $user, $addressId, $voucherId, $subtotal, $discountAmount, $shipper_method)
    {
        // dd($request->all());
        // dd($request->shipping_method);
        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->firstOrFail();
        $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->firstOrFail();
        
        $finalTotalAmount = $subtotal + (float) $request->shipping_fee_applied - (float) $discountAmount;
        
        // Đối với đơn hàng ebook, sử dụng thông tin user nếu không có thông tin người nhận
        $isEbookOrder = $request->delivery_method === 'ebook';
        // dd($shipper_method);
        $order =  [
            'user_id' => $user->id,
            'order_code' => 'BBE-' . time(),
            'address_id' => $addressId,
            'recipient_name' => $isEbookOrder ? ($request->new_recipient_name ?: $user->name) : $request->new_recipient_name,
            'recipient_phone' => $isEbookOrder ? ($request->new_phone ?: $user->phone) : $request->new_phone,
            'recipient_email' => $request->new_email ?: $user->email,
            'payment_method_id' => $request->payment_method_id,
            'voucher_id' => $voucherId,
            'note' => $request->note,
            'order_status_id' => $orderStatus->id,
            'payment_status_id' => $paymentStatus->id,
            'total_amount' => $finalTotalAmount,
            'shipping_fee' => ($request->shipping_method === 'pickup' || $isEbookOrder) ? 0 : $request->shipping_fee_applied,
            'discount_amount' => (int) $discountAmount,
            'delivery_method' => $request->delivery_method,
        ];
        // dd($order);
        // dd($order['delivery_method']);
        return $order;
    }

    /**
     * Xử lý toàn bộ quá trình tạo đơn hàng
     */
    public function processOrderCreation($request, User $user)
    {
        // 1. Xử lý địa chỉ giao hàng
        $addressId = $this->handleDeliveryAddress($request, $user);
        
        // Chỉ yêu cầu địa chỉ khi không phải đơn hàng ebook
        if (!$addressId && $request->delivery_method !== 'ebook') {
            throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
        }

        // 2. Validate giỏ hàng
        $cartItems = $this->validateCartItems($user);

        // 3. Tính tổng tiền
        $subtotal = $this->calculateCartSubtotal($cartItems);

        // 4. Validate voucher
        $voucherData = $this->validateVoucher($request->applied_voucher_code, $subtotal);
        $actualDiscountAmount = $request->discount_amount_applied;

        // 5. Lấy thông tin phương thức thanh toán
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // 6. Chuẩn bị dữ liệu đơn hàng
        $orderData = $this->prepareOrderData(
            $request, 
            $user, 
            $addressId, 
            $voucherData['voucher_id'], 
            $subtotal, 
            $request->shipping_method,
            $actualDiscountAmount
        );

        // 7. Tạo đơn hàng
        $order = $this->createOrderWithItems($orderData, $cartItems);

        return [
            'order' => $order,
            'payment_method' => $paymentMethod,
            'cart_items' => $cartItems
        ];
    }

    /**
     * Xóa giỏ hàng sau khi tạo đơn hàng thành công
     */
    public function clearUserCart(User $user)
    {
        $user->cart()->delete();
    }

    /**
     * Kiểm tra số dư ví của người dùng
     */
    public function checkWalletBalance(User $user, $amount)
    {
        // Tạo wallet nếu user chưa có
        $wallet = $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        if ($wallet->balance < $amount) {
            throw new \Exception('Số dư ví không đủ để thanh toán. Số dư hiện tại: ' . number_format($wallet->balance) . 'đ');
        }

        return true;
    }

    /**
     * Xử lý thanh toán bằng ví điện tử
     */
    public function processWalletPayment(Order $order, User $user)
    {
        DB::beginTransaction();
        
        try {
            // Kiểm tra số dư ví (sẽ tạo wallet nếu chưa có)
            $this->checkWalletBalance($user, $order->total_amount);
            
            // Lấy wallet (đã được tạo trong checkWalletBalance)
            $wallet = $user->wallet()->first();
            
            // Trừ tiền từ ví
            $wallet->decrement('balance', $order->total_amount);
            
            // Tạo giao dịch ví
            WalletTransaction::create([
                'id' => (string) Str::uuid(),
                'wallet_id' => $wallet->id,
                'amount' => $order->total_amount,
                'type' => 'payment',
                'description' => 'Thanh toán đơn hàng ' . $order->order_code,
                'related_order_id' => $order->id,
                'status' => 'Thành Công',
                'payment_method' => 'wallet'
            ]);
            
            // Cập nhật trạng thái thanh toán của đơn hàng
            $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
            if ($paymentStatus) {
                $order->update([
                    'payment_status_id' => $paymentStatus->id
                ]);
            }
            
            DB::commit();
            
            Log::info('Wallet payment processed successfully', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $order->total_amount,
                'remaining_balance' => $wallet->fresh()->balance
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet payment failed', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $order->total_amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Xử lý toàn bộ quá trình tạo đơn hàng với hỗ trợ thanh toán ví
     */
    public function processOrderCreationWithWallet($request, User $user)
    {
        // dd($request->all());
        $addressId = $this->handleDeliveryAddress($request, $user);
        
        // Chỉ yêu cầu địa chỉ khi không phải đơn hàng ebook
        if (!$addressId && $request->delivery_method !== 'ebook') {
            throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
        }

        // 2. Validate giỏ hàng
        $cartItems = $this->validateCartItems($user);

        // 3. Tính tổng tiền
        $subtotal = $this->calculateCartSubtotal($cartItems);

        // 4. Validate voucher
        $voucherData = $this->validateVoucher($request->applied_voucher_code, $subtotal);
        $actualDiscountAmount = $request->discount_amount_applied;

        // 5. Lấy thông tin phương thức thanh toán
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // 6. Kiểm tra nếu là thanh toán ví
        $isWalletPayment = stripos($paymentMethod->name, 'ví điện tử') !== false;

        $shipping_method = $request->shipping_method;
        // dd($shipping_method);
        // 7. Chuẩn bị dữ liệu đơn hàng
        $orderData = $this->prepareOrderData(
            $request, 
            $user, 
            $addressId, 
            $voucherData['voucher_id'], 
            $subtotal,
            $shipping_method,
            $actualDiscountAmount
        );

        // 8. Nếu là thanh toán ví, kiểm tra số dư trước khi tạo đơn hàng
        if ($isWalletPayment) {
            $this->checkWalletBalance($user, $orderData['total_amount']);
        }

        // 9. Tạo đơn hàng
        $order = $this->createOrderWithItems($orderData, $cartItems);

        return [
            'order' => $order,
            'payment_method' => $paymentMethod,
            'cart_items' => $cartItems,
            'is_wallet_payment' => $isWalletPayment
        ];
    }

    /**
     * Cập nhật trạng thái đơn hàng ebook thành 'Thành công' khi thanh toán thành công
     */
    public function updateEbookOrderStatusOnPaymentSuccess(Order $order)
    {
        // Kiểm tra nếu đơn hàng là ebook và đã thanh toán
        if ($order->delivery_method === 'ebook') {
            $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
            $successStatus = OrderStatus::where('name', 'Thành công')->first();
            
            if ($paymentStatus && $successStatus && $order->payment_status_id == $paymentStatus->id) {
                $order->update([
                    'order_status_id' => $successStatus->id
                ]);
                
                Log::info('Ebook order status updated to success', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code
                ]);
            }
        }
    }
}
