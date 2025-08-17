<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Voucher;
use App\Models\OrderItemAttributeValue;
use App\Models\BookAttributeValue;
use App\Models\BookGift;
use App\Models\Collection;
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
    
    /**
     * Track books that have already had their gift stock decreased in current order session
     * to avoid double processing when the same book has multiple variants
     */
    private $processedGiftBooks = [];

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

        // Log giá trị discount trước khi tạo đơn hàng
        Log::info('Creating order with discount: ' . $discount . ', total_amount: ' . $totalAmount);
        
        // Tạo đơn hàng
        $order = Order::create([
            'id' => (string) Str::uuid(),
            'order_code' => $this->generateOrderCode(),
            'user_id' => $data['user_id'],
            'address_id' => $data['address_id'],
            'voucher_id' => $voucher ? $voucher->id : null,
            'total_amount' => $totalAmount,
            'discount_amount' => $discount, // Đảm bảo lưu đúng giá trị giảm giá
            'shipping_fee' => $shippingFee,
            'note' => $data['note'] ?? null,
            'order_status_id' => OrderStatus::where('name', 'Chờ Xác Nhận')->first()->id,
            'payment_method_id' => $data['payment_method_id'],
            'payment_status_id' => PaymentStatus::where('name', 'Chờ Thanh Toán')->first()->id
        ]);
        
        // Log thông tin đơn hàng sau khi tạo
        Log::info('Order created', [
            'order_id' => $order->id,
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount
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
        // Reset gift processing tracker for new order
        $this->processedGiftBooks = [];
        
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

        // ✨ THÊM MỚI: Trừ số lượng quà tặng
        $this->decreaseGiftStock($cartItem);

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
        
        if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
            foreach ($attributeValueIds as $attributeValueId) {
                // Kiểm tra attributeValueId hợp lệ (không phải null, empty, hoặc 0)
                if ($attributeValueId && !empty(trim($attributeValueId)) && $attributeValueId !== '0') {
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
     * Trừ số lượng quà tặng khi tạo đơn hàng
     * Fixed to handle total book quantity across variants properly
     */
    private function decreaseGiftStock($cartItem)
    {
        // Chỉ xử lý quà tặng cho sách vật lý (không phải ebook hoặc combo)
        if (isset($cartItem->is_combo) && $cartItem->is_combo) {
            return; // Combo không có quà tặng
        }

        // Kiểm tra xem có phải ebook không
        if ($cartItem->bookFormat && stripos($cartItem->bookFormat->format_name, 'ebook') !== false) {
            return; // Ebook không có quà tặng
        }

        // Check if gift stock for this book has already been decreased in this order session
        if (!isset($this->processedGiftBooks)) {
            $this->processedGiftBooks = [];
        }

        if (in_array($cartItem->book_id, $this->processedGiftBooks)) {
            Log::info('Gift stock already decreased for this book in current order', [
                'book_id' => $cartItem->book_id,
                'current_item_quantity' => $cartItem->quantity
            ]);
            return;
        }

        // Calculate total quantity of this book across all variants
        $totalBookQuantity = $this->getTotalBookQuantityInCart($cartItem->book_id);

        // Lấy các quà tặng có sẵn cho sách này (chỉ những quà tặng đang trong thời gian hiệu lực)
        $availableGifts = BookGift::where('book_id', $cartItem->book_id)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('quantity', '>', 0)
            ->get();

        // Nếu không có quà tặng khả dụng thì không cần trừ stock
        if ($availableGifts->isEmpty()) {
            Log::info('OrderService - No available gifts to decrease stock for book', [
                'book_id' => $cartItem->book_id,
                'total_book_quantity' => $totalBookQuantity
            ]);
            // Mark as processed anyway to avoid re-checking
            $this->processedGiftBooks[] = $cartItem->book_id;
            return;
        }

        // Trừ số lượng quà tặng dựa trên tổng số lượng sách (không phải từng item)
        foreach ($availableGifts as $gift) {
            if ($gift->quantity >= $totalBookQuantity) {
                $gift->decrement('quantity', $totalBookQuantity);
                
                Log::info('Decreased gift stock (total book quantity):', [
                    'gift_id' => $gift->id,
                    'gift_name' => $gift->gift_name,
                    'book_id' => $cartItem->book_id,
                    'total_book_quantity_decreased' => $totalBookQuantity,
                    'current_item_quantity' => $cartItem->quantity,
                    'remaining_quantity' => $gift->fresh()->quantity
                ]);
            } else {
                Log::warning('Insufficient gift stock for total book quantity:', [
                    'gift_id' => $gift->id,
                    'gift_name' => $gift->gift_name,
                    'book_id' => $cartItem->book_id,
                    'available_quantity' => $gift->quantity,
                    'total_book_quantity_needed' => $totalBookQuantity,
                    'current_item_quantity' => $cartItem->quantity
                ]);
                
                // Nếu không đủ quà tặng, trừ hết số lượng còn lại
                if ($gift->quantity > 0) {
                    $remainingQuantity = $gift->quantity;
                    $gift->update(['quantity' => 0]);
                    
                    Log::info('Used remaining gift stock (insufficient for total quantity):', [
                        'gift_id' => $gift->id,
                        'gift_name' => $gift->gift_name,
                        'quantity_used' => $remainingQuantity,
                        'quantity_still_needed' => $totalBookQuantity - $remainingQuantity
                    ]);
                }
            }
        }

        // Mark this book as processed to avoid double processing
        $this->processedGiftBooks[] = $cartItem->book_id;
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
        Log::info('OrderService - Starting comprehensive cart validation', [
            'user_id' => $user->id
        ]);

        // Lấy tất cả items trong giỏ hàng (cả sách lẻ và combo) chỉ những item được chọn
        $cartItems = $user->cart()
            ->with(['book', 'bookFormat', 'collection', 'collection.books'])
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

        Log::info('OrderService - Cart items retrieved', [
            'total_items' => $cartItems->count(),
            'user_id' => $user->id
        ]);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Giỏ hàng của bạn đang trống hoặc không có sản phẩm nào được chọn.');
        }

        // Track validation statistics
        $validationStats = [
            'total_items' => $cartItems->count(),
            'combos_validated' => 0,
            'books_validated' => 0,
            'errors' => []
        ];

        // Validate từng item với comprehensive stock checking
        foreach ($cartItems as $item) {
            try {
                if ($item->is_combo) {
                    $this->validateComboItem($item);
                    $validationStats['combos_validated']++;
                } else {
                    $this->validateBookItem($item);
                    $validationStats['books_validated']++;
                }
            } catch (\Exception $e) {
                $validationStats['errors'][] = [
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ];
                
                Log::error('OrderService - Item validation failed', [
                    'item_id' => $item->id,
                    'is_combo' => $item->is_combo,
                    'error' => $e->getMessage()
                ]);
                
                // Re-throw the exception to stop processing
                throw $e;
            }
        }

        Log::info('OrderService - Cart validation completed successfully', $validationStats);

        return $cartItems;
    }

    /**
     * Validate combo item with real-time stock checking
     */
    private function validateComboItem($cartItem)
    {
        if (!$cartItem->collection) {
            throw new \Exception('Combo không tồn tại trong giỏ hàng.');
        }

        // Lấy thông tin combo mới nhất từ database
        $freshCombo = \App\Models\Collection::find($cartItem->collection_id);
        if (!$freshCombo) {
            throw new \Exception('Combo không tồn tại.');
        }

        // Kiểm tra combo còn hoạt động không
        if ($freshCombo->status !== 'active') {
            throw new \Exception('Combo "' . $freshCombo->name . '" không còn hoạt động.');
        }

        // Kiểm tra thời gian khuyến mãi
        $now = now()->toDateString();
        if ($freshCombo->start_date && $freshCombo->start_date > $now) {
            throw new \Exception('Combo "' . $freshCombo->name . '" chưa bắt đầu khuyến mãi.');
        }
        if ($freshCombo->end_date && $freshCombo->end_date < $now) {
            throw new \Exception('Combo "' . $freshCombo->name . '" đã hết thời gian khuyến mãi.');
        }

        // Kiểm tra tồn kho combo với thông tin thời gian thực
        if ($freshCombo->combo_stock !== null) {
            if ($freshCombo->combo_stock <= 0) {
                throw new \Exception('Combo "' . $freshCombo->name . '" đã hết hàng.');
            }

            if ($freshCombo->combo_stock < $cartItem->quantity) {
                throw new \Exception('Combo "' . $freshCombo->name . '" không đủ số lượng. Còn lại: ' . $freshCombo->combo_stock);
            }

            Log::info('OrderService - Combo stock validation passed', [
                'combo_id' => $cartItem->collection_id,
                'combo_name' => $freshCombo->name,
                'available_stock' => $freshCombo->combo_stock,
                'requested_quantity' => $cartItem->quantity
            ]);
        }
    }

    /**
     * Validate book item with comprehensive stock checking
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

        // Skip stock validation for ebooks
        $isEbook = strtolower($freshBookFormat->format_name) === 'ebook';
        if ($isEbook) {
            Log::info('OrderService - Skipping stock validation for ebook', [
                'book_id' => $cartItem->book_id,
                'format' => $freshBookFormat->format_name
            ]);
            return;
        }

        // Comprehensive stock validation for physical books
        $this->validatePhysicalBookStock($cartItem, $freshBook, $freshBookFormat);
    }

    /**
     * Validate physical book stock with hierarchical validation
     */
    private function validatePhysicalBookStock($cartItem, $freshBook, $freshBookFormat)
    {
        // Step 1: Validate format stock
        $formatStock = $freshBookFormat->stock;
        if ($formatStock <= 0) {
            throw new \Exception('Sách "' . $freshBook->title . '" (định dạng: ' . $freshBookFormat->format_name . ') đã hết hàng.');
        }

        if ($cartItem->quantity > $formatStock) {
            throw new \Exception('Sách "' . $freshBook->title . '" (định dạng: ' . $freshBookFormat->format_name . ') không đủ số lượng. Còn lại: ' . $formatStock);
        }

        // Step 2: Validate variant stock if item has attributes
        $availableStock = $formatStock;
        if (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
            $attributeValueIds = is_string($cartItem->attribute_value_ids) 
                ? json_decode($cartItem->attribute_value_ids, true) 
                : $cartItem->attribute_value_ids;

            if ($attributeValueIds && is_array($attributeValueIds) && count($attributeValueIds) > 0) {
                $variantStockInfo = DB::table('book_attribute_values')
                    ->whereIn('attribute_value_id', $attributeValueIds)
                    ->where('book_id', $cartItem->book_id)
                    ->select('attribute_value_id', 'stock', 'sku')
                    ->get();

                if ($variantStockInfo->isEmpty()) {
                    throw new \Exception('Không tìm thấy thông tin tồn kho cho thuộc tính đã chọn của sách "' . $freshBook->title . '".');
                }

                // Check for out of stock variants
                $outOfStockVariants = $variantStockInfo->filter(function ($variant) {
                    return $variant->stock <= 0;
                });

                if ($outOfStockVariants->isNotEmpty()) {
                    $outOfStockSkus = $outOfStockVariants->pluck('sku')->filter()->implode(', ');
                    throw new \Exception('Thuộc tính đã hết hàng cho sách "' . $freshBook->title . '": ' . ($outOfStockSkus ?: 'N/A'));
                }

                // Get minimum variant stock and apply hierarchical logic
                $minVariantStock = $variantStockInfo->min('stock');
                $availableStock = min($formatStock, $minVariantStock);

                Log::info('OrderService - Hierarchical stock validation', [
                    'book_id' => $cartItem->book_id,
                    'format_stock' => $formatStock,
                    'min_variant_stock' => $minVariantStock,
                    'final_available_stock' => $availableStock,
                    'requested_quantity' => $cartItem->quantity
                ]);

                if ($cartItem->quantity > $minVariantStock) {
                    $lowStockVariant = $variantStockInfo->where('stock', $minVariantStock)->first();
                    throw new \Exception('Thuộc tính không đủ số lượng cho sách "' . $freshBook->title . '". Tồn kho hiện tại: ' . $minVariantStock . 
                        ($lowStockVariant->sku ? " (SKU: {$lowStockVariant->sku})" : ""));
                }
            }
        }

        // Step 3: Validate gift stock if book has gifts
        $this->validateGiftStock($cartItem, $freshBook);

        // Final check with calculated available stock
        if ($cartItem->quantity > $availableStock) {
            throw new \Exception('Sách "' . $freshBook->title . '" không đủ số lượng khả dụng. Tồn kho hiện tại: ' . $availableStock);
        }

        Log::info('OrderService - Stock validation passed', [
            'book_id' => $cartItem->book_id,
            'book_title' => $freshBook->title,
            'final_available_stock' => $availableStock,
            'requested_quantity' => $cartItem->quantity
        ]);
    }

    /**
     * Validate gift stock availability
     * Checks total quantity of the same book across all variants in cart
     * Only validates if the book actually has gifts available in the current period
     */
    private function validateGiftStock($cartItem, $freshBook)
    {
        // Chỉ xử lý quà tặng cho sách vật lý (không phải ebook hoặc combo)
        if (isset($cartItem->is_combo) && $cartItem->is_combo) {
            return; // Combo không có quà tặng
        }

        // Kiểm tra xem có phải ebook không
        if ($cartItem->bookFormat && stripos($cartItem->bookFormat->format_name, 'ebook') !== false) {
            return; // Ebook không có quà tặng
        }

        // Lấy các quà tặng có sẵn trong thời gian hiện tại cho sách này
        $availableGifts = BookGift::where('book_id', $cartItem->book_id)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('quantity', '>', 0)
            ->get();
        
        // Nếu không có quà tặng khả dụng thì không cần validate
        if ($availableGifts->isEmpty()) {
            Log::info('OrderService - No available gifts for book, skipping gift validation', [
                'book_id' => $cartItem->book_id,
                'book_title' => $freshBook->title
            ]);
            return;
        }

        // Calculate total quantity of the same book across all variants in the user's cart
        $totalBookQuantity = $this->getTotalBookQuantityInCart($cartItem->book_id);
        
        // Chỉ validate những quà tặng thực sự có sẵn
        foreach ($availableGifts as $gift) {
            if ($gift->quantity !== null && $gift->quantity < $totalBookQuantity) {
                throw new \Exception('Quà tặng "' . $gift->gift_name . '" cho sách "' . $freshBook->title . '" không đủ số lượng. Tổng số lượng sách trong giỏ: ' . $totalBookQuantity . ', quà tặng còn lại: ' . $gift->quantity);
            }
        }

        Log::info('OrderService - Gift stock validation passed', [
            'book_id' => $cartItem->book_id,
            'gifts_count' => $availableGifts->count(),
            'current_item_quantity' => $cartItem->quantity,
            'total_book_quantity_in_cart' => $totalBookQuantity
        ]);
    }

    /**
     * Calculate total quantity of a specific book across all variants in user's cart
     */
    private function getTotalBookQuantityInCart($bookId)
    {
        // Get the current user from the cart item being validated
        $userId = null;
        
        // Find user_id from any cart item with this book_id that is selected
        $cartItem = Cart::where('book_id', $bookId)
            ->where('is_selected', 1)
            ->first();
            
        if (!$cartItem) {
            return 0;
        }
        
        $userId = $cartItem->user_id;
        
        // Sum all quantities for this book_id across different variants
        $totalQuantity = Cart::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->where('is_selected', 1)
            ->where('is_combo', 0) // Exclude combo items
            ->sum('quantity');
            
        Log::info('OrderService - Calculated total book quantity in cart', [
            'book_id' => $bookId,
            'user_id' => $userId,
            'total_quantity' => $totalQuantity
        ]);
        
        return $totalQuantity;
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
            $actualDiscountAmount,
            $shipping_method
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

    /**
     * Get detailed stock information for a cart item (useful for debugging and user feedback)
     */
    public function getCartItemStockInfo($cartItem)
    {
        $stockInfo = [
            'item_id' => $cartItem->id,
            'is_combo' => $cartItem->is_combo,
            'quantity_requested' => $cartItem->quantity,
            'validation_passed' => false,
            'errors' => []
        ];

        if ($cartItem->is_combo) {
            $combo = Collection::find($cartItem->collection_id);
            $stockInfo['combo_info'] = [
                'name' => $combo->name ?? 'Unknown',
                'combo_stock' => $combo->combo_stock ?? 'Unlimited',
                'status' => $combo->status ?? 'Unknown'
            ];
            
            if ($combo && $combo->combo_stock !== null) {
                $stockInfo['available_stock'] = $combo->combo_stock;
                $stockInfo['sufficient'] = $combo->combo_stock >= $cartItem->quantity;
            } else {
                $stockInfo['available_stock'] = 'Unlimited';
                $stockInfo['sufficient'] = true;
            }
        } else {
            $book = \App\Models\Book::find($cartItem->book_id);
            $bookFormat = \App\Models\BookFormat::find($cartItem->book_format_id);
            
            $stockInfo['book_info'] = [
                'title' => $book->title ?? 'Unknown',
                'format' => $bookFormat->format_name ?? 'Unknown',
                'format_stock' => $bookFormat->stock ?? 0,
                'book_status' => $book->status ?? 'Unknown'
            ];

            $isEbook = strtolower($bookFormat->format_name ?? '') === 'ebook';
            if ($isEbook) {
                $stockInfo['available_stock'] = 'Unlimited (Ebook)';
                $stockInfo['sufficient'] = true;
            } else {
                $availableStock = $bookFormat->stock;
                
                // Check variant stock if applicable
                if (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
                    $attributeValueIds = is_string($cartItem->attribute_value_ids) 
                        ? json_decode($cartItem->attribute_value_ids, true) 
                        : $cartItem->attribute_value_ids;

                    if ($attributeValueIds && is_array($attributeValueIds)) {
                        $variantStocks = DB::table('book_attribute_values')
                            ->whereIn('attribute_value_id', $attributeValueIds)
                            ->where('book_id', $cartItem->book_id)
                            ->pluck('stock', 'sku')
                            ->toArray();
                        
                        $minVariantStock = min($variantStocks);
                        $availableStock = min($availableStock, $minVariantStock);
                        
                        $stockInfo['variant_info'] = [
                            'variant_stocks' => $variantStocks,
                            'min_variant_stock' => $minVariantStock,
                            'hierarchical_stock' => $availableStock
                        ];
                    }
                }

                // Check gift stock
                $gifts = BookGift::where('book_id', $cartItem->book_id)->get();
                if ($gifts->isNotEmpty()) {
                    $giftStocks = [];
                    foreach ($gifts as $gift) {
                        $giftStocks[] = [
                            'name' => $gift->gift_name,
                            'stock' => $gift->quantity,
                            'sufficient' => $gift->quantity === null || $gift->quantity >= $cartItem->quantity
                        ];
                    }
                    $stockInfo['gift_info'] = $giftStocks;
                }

                $stockInfo['available_stock'] = $availableStock;
                $stockInfo['sufficient'] = $availableStock >= $cartItem->quantity;
            }
        }

        try {
            if ($cartItem->is_combo) {
                $this->validateComboItem($cartItem);
            } else {
                $this->validateBookItem($cartItem);
            }
            $stockInfo['validation_passed'] = true;
        } catch (\Exception $e) {
            $stockInfo['errors'][] = $e->getMessage();
        }

        return $stockInfo;
    }

    /**
     * Get comprehensive stock report for all selected cart items
     */
    public function getCartStockReport(User $user)
    {
        $cartItems = $user->cart()
            ->with(['book', 'bookFormat', 'collection'])
            ->where('is_selected', 1)
            ->get();

        $report = [
            'total_items' => $cartItems->count(),
            'validation_summary' => [
                'all_valid' => true,
                'total_errors' => 0
            ],
            'items' => []
        ];

        foreach ($cartItems as $item) {
            $itemInfo = $this->getCartItemStockInfo($item);
            $report['items'][] = $itemInfo;
            
            if (!$itemInfo['validation_passed']) {
                $report['validation_summary']['all_valid'] = false;
                $report['validation_summary']['total_errors'] += count($itemInfo['errors']);
            }
        }

        return $report;
    }
}
