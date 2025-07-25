<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Voucher;
use App\Models\OrderItemAttributeValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;

class OrderService
{
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
            OrderItem::create([
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'book_id' => $item->book_id,
                'book_format_id' => $item->book_format_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $total
            ]);
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
            Cart::where('user_id', $data['user_id'])
                ->where('book_id', $item->book_id)
                ->where('book_format_id', $item->book_format_id)
                ->delete();
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

    protected function generateOrderCode()
    {
        return 'ORD' . date('Ymd') . strtoupper(Str::random(4));
    }

    /**
     * Tạo đơn hàng với OrderItems và thuộc tính sản phẩm
     */
    public function createOrderWithItems(array $orderData, $cartItems)
    {
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
        ]);

        // 2. Tạo OrderItems
        foreach ($cartItems as $cartItem) {
            // Validate dữ liệu cart item
            if (!$cartItem->book_id || !$cartItem->book_format_id) {
                Log::error('Invalid cart item data:', [
                    'cart_item_id' => $cartItem->id,
                    'book_id' => $cartItem->book_id,
                    'book_format_id' => $cartItem->book_format_id
                ]);
                throw new \Exception('Dữ liệu giỏ hàng không hợp lệ.');
            }

            $orderItem = OrderItem::create([
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'book_id' => $cartItem->book_id,
                'book_format_id' => $cartItem->book_format_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'total' => $cartItem->quantity * $cartItem->price,
            ]);

            // 3. Lưu thuộc tính sản phẩm
            $this->saveOrderItemAttributes($orderItem, $cartItem);
        }

        return $order;
    }

    /**
     * Lưu thuộc tính sản phẩm cho OrderItem
     */
    private function saveOrderItemAttributes($orderItem, $cartItem)
    {
        $attributeValueIds = $cartItem->attribute_value_ids ?? [];
        
        if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
            foreach ($attributeValueIds as $attributeValueId) {
                if ($attributeValueId) {
                    OrderItemAttributeValue::create([
                        'id' => (string) Str::uuid(),
                        'order_item_id' => $orderItem->id,
                        'attribute_value_id' => $attributeValueId,
                    ]);
                }
            }
        } else {
            // Tạo record với attribute_value_id = 0 nếu không có thuộc tính
            OrderItemAttributeValue::create([
                'id' => (string) Str::uuid(),
                'order_item_id' => $orderItem->id,
                'attribute_value_id' => 0,
            ]);
        }
    }
}
