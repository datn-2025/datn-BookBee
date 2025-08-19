<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Models\BookGift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MixedOrderService
{
    protected $orderService;
    protected $emailService;
    protected $qrCodeService;
    
    /**
     * Track books that have already had their gift stock decreased in current order session
     * to avoid double processing when the same book has multiple variants
     */
    private $processedGiftBooks = [];

    public function __construct(
        OrderService $orderService,
        EmailService $emailService,
        QrCodeService $qrCodeService
    ) {
        $this->orderService = $orderService;
        $this->emailService = $emailService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Kiểm tra xem giỏ hàng có chứa cả ebook và sách vật lý không
     */
    public function hasMixedFormats($cartItems)
    {
        $hasPhysicalBook = false;
        $hasEbook = false;

        foreach ($cartItems as $item) {
            // Kiểm tra combo - combo luôn là sách vật lý
            if (isset($item->is_combo) && $item->is_combo) {
                $hasPhysicalBook = true;
            } elseif ($item->bookFormat) {
                if (strtolower($item->bookFormat->format_name) === 'ebook') {
                    $hasEbook = true;
                } else {
                    $hasPhysicalBook = true;
                }
            }

            if ($hasPhysicalBook && $hasEbook) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tách giỏ hàng thành 2 phần: sách vật lý và ebook
     */
    public function separateCartItems($cartItems)
    {
        $physicalItems = collect();
        $ebookItems = collect();

        foreach ($cartItems as $item) {
            // Kiểm tra combo - combo luôn là sách vật lý
            if (isset($item->is_combo) && $item->is_combo) {
                $physicalItems->push($item);
            } elseif ($item->bookFormat && strtolower($item->bookFormat->format_name) === 'ebook') {
                $ebookItems->push($item);
            } else {
                $physicalItems->push($item);
            }
        }

        return [
            'physical' => $physicalItems,
            'ebook' => $ebookItems
        ];
    }

    /**
     * Tạo đơn hàng cha và 2 đơn hàng con cho trường hợp mixed format
     */
    public function createMixedFormatOrders($request, User $user)
    {
        DB::beginTransaction();
        
        try {
            // 1. Validate giỏ hàng
            $cartItems = $this->orderService->validateCartItems($user);
            
            // 2. Kiểm tra có phải mixed format không
            if (!$this->hasMixedFormats($cartItems)) {
                throw new \Exception('Giỏ hàng không chứa cả ebook và sách vật lý.');
            }

            // 3. Tách giỏ hàng
            $separatedItems = $this->separateCartItems($cartItems);
            
            // 4. Xử lý địa chỉ giao hàng (chỉ cần cho sách vật lý)
            $addressId = $this->orderService->handleDeliveryAddress($request, $user);
            if (!$addressId) {
                throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
            }

            // 5. Tính tổng tiền cho từng loại
            $physicalSubtotal = $this->calculateSubtotal($separatedItems['physical']);
            $ebookSubtotal = $this->calculateSubtotal($separatedItems['ebook']);
            $totalSubtotal = $physicalSubtotal + $ebookSubtotal;

            // 6. Validate voucher (áp dụng cho tổng đơn hàng)
            $voucherData = $this->orderService->validateVoucher($request->applied_voucher_code ?? null, $totalSubtotal);
            $totalDiscountAmount = $request->discount_amount_applied ?? 0;
            
            // Phân bổ discount theo tỷ lệ
            $physicalDiscountRatio = $physicalSubtotal / $totalSubtotal;
            $physicalDiscountAmount = $totalDiscountAmount * $physicalDiscountRatio;
            $ebookDiscountAmount = $totalDiscountAmount - $physicalDiscountAmount;

            // 7. Tạo đơn hàng cha
            $parentOrder = $this->createParentOrder($request, $user, $addressId, $voucherData['voucher_id'], $totalSubtotal, $totalDiscountAmount);

            // 8. Tạo đơn hàng con cho sách vật lý
            $physicalOrder = $this->createPhysicalChildOrder(
                $parentOrder, 
                $request, 
                $user, 
                $addressId, 
                $separatedItems['physical'], 
                $physicalSubtotal, 
                $physicalDiscountAmount
            );

            // 9. Tạo đơn hàng con cho ebook
            $ebookOrder = $this->createEbookChildOrder(
                $parentOrder, 
                $request, 
                $user, 
                $separatedItems['ebook'], 
                $ebookSubtotal, 
                $ebookDiscountAmount
            );

            DB::commit();

            return [
                'parent_order' => $parentOrder,
                'physical_order' => $physicalOrder,
                'ebook_order' => $ebookOrder,
                'cart_items' => $cartItems
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mixed format order creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Tính tổng tiền cho một nhóm sản phẩm
     */
    private function calculateSubtotal($items)
    {
        return $items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Tạo đơn hàng cha
     */
    private function createParentOrder($request, User $user, $addressId, $voucherId, $subtotal, $discountAmount)
    {
        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->firstOrFail();
        $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->firstOrFail();
        
        $finalTotalAmount = $subtotal + $request->shipping_fee_applied - $discountAmount;

        $orderData = [
            'user_id' => $user->id,
            'order_code' => 'BBE-PARENT-' . time(),
            'address_id' => $addressId,
            'recipient_name' => $request->new_recipient_name ?: $user->name,
            'recipient_phone' => $request->new_phone ?: $user->phone,
            'recipient_email' => $request->new_email ?: $user->email,
            'payment_method_id' => $request->payment_method_id,
            'voucher_id' => $voucherId,
            'note' => $request->note,
            'order_status_id' => $orderStatus->id,
            'payment_status_id' => $paymentStatus->id,
            'total_amount' => $finalTotalAmount,
            'shipping_fee' => $request->shipping_fee_applied,
            'discount_amount' => (int) $discountAmount,
            'delivery_method' => 'mixed', // Đánh dấu là đơn hàng hỗn hợp
            'parent_order_id' => null, // Đây là đơn hàng cha
        ];

        return Order::create($orderData);
    }

    /**
     * Tạo đơn hàng con cho sách vật lý
     */
    private function createPhysicalChildOrder($parentOrder, $request, User $user, $addressId, $items, $subtotal, $discountAmount)
    {
        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->firstOrFail();
        $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->firstOrFail();
        
        $finalTotalAmount = $subtotal + $request->shipping_fee_applied - $discountAmount;

        $orderData = [
            'user_id' => $user->id,
            'order_code' => 'BBE-PHYSICAL-' . time(),
            'address_id' => $addressId,
            'recipient_name' => $request->new_recipient_name ?: $user->name,
            'recipient_phone' => $request->new_phone ?: $user->phone,
            'recipient_email' => $request->new_email ?: $user->email,
            'payment_method_id' => $request->payment_method_id,
            'voucher_id' => null, // Voucher chỉ áp dụng cho đơn cha
            'note' => $request->note,
            'order_status_id' => $orderStatus->id,
            'payment_status_id' => $paymentStatus->id,
            'total_amount' => $finalTotalAmount,
            'shipping_fee' => $request->shipping_fee_applied,
            'discount_amount' => (int) $discountAmount,
            'delivery_method' => 'delivery',
            'parent_order_id' => $parentOrder->id, // Đây là đơn hàng con
        ];

        $order = Order::create($orderData);
        
        // Tạo order items cho sách vật lý
        $this->createOrderItems($order, $items);
        
        return $order;
    }

    /**
     * Tạo đơn hàng con cho ebook
     */
    private function createEbookChildOrder($parentOrder, $request, User $user, $items, $subtotal, $discountAmount)
    {
        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->firstOrFail();
        $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->firstOrFail();
        
        $finalTotalAmount = $subtotal - $discountAmount; // Không có phí ship cho ebook

        $orderData = [
            'user_id' => $user->id,
            'order_code' => 'BBE-EBOOK-' . time(),
            'address_id' => null, // Ebook không cần địa chỉ
            'recipient_name' => $request->new_recipient_name ?: $user->name,
            'recipient_phone' => $request->new_phone ?: $user->phone,
            'recipient_email' => $request->new_email ?: $user->email,
            'payment_method_id' => $request->payment_method_id,
            'voucher_id' => null, // Voucher chỉ áp dụng cho đơn cha
            'note' => $request->note,
            'order_status_id' => $orderStatus->id,
            'payment_status_id' => $paymentStatus->id,
            'total_amount' => $finalTotalAmount,
            'shipping_fee' => 0,
            'discount_amount' => (int) $discountAmount,
            'delivery_method' => 'ebook',
            'parent_order_id' => $parentOrder->id, // Đây là đơn hàng con
        ];

        $order = Order::create($orderData);
        
        // Tạo order items cho ebook
        $this->createOrderItems($order, $items);
        
        return $order;
    }

    /**
     * Tạo order items cho một đơn hàng
     */
    private function createOrderItems($order, $items)
    {
        foreach ($items as $item) {
            // Tạo order item với thông tin combo nếu có
            $orderItemData = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'order_id' => $order->id,
                'book_id' => $item->book_id,
                'book_format_id' => $item->book_format_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->price * $item->quantity,
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
            
            // ✨ THÊM MỚI: Logic trừ stock
            // Trừ stock combo
            if (isset($item->is_combo) && $item->is_combo && $item->collection) {
                if ($item->collection->combo_stock !== null) {
                    $item->collection->decrement('combo_stock', $item->quantity);
                    
                    Log::info('Decreased combo stock in mixed order:', [
                        'collection_id' => $item->collection_id,
                        'quantity_decreased' => $item->quantity,
                        'remaining_stock' => $item->collection->fresh()->combo_stock
                    ]);
                }
            }
            // Trừ stock sách vật lý (chỉ trừ khi không phải ebook)
            elseif ($item->bookFormat && stripos($item->bookFormat->format_name, 'ebook') === false) {
                $item->bookFormat->decrement('stock', $item->quantity);
                
                Log::info('Decreased book format stock in mixed order:', [
                    'book_id' => $item->book_id,
                    'book_format_id' => $item->book_format_id,
                    'quantity_decreased' => $item->quantity,
                    'remaining_stock' => $item->bookFormat->fresh()->stock
                ]);
                
                // Trừ stock thuộc tính sản phẩm
                $this->decreaseAttributeStock($item);
                
                // Trừ số lượng quà tặng
                $this->decreaseGiftStock($item);
            }
            
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
                        \App\Models\OrderItemAttributeValue::create([
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'order_item_id' => $orderItem->id,
                            'attribute_value_id' => $attributeValueId
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Xử lý thanh toán cho đơn hàng hỗn hợp
     */
    public function processMixedOrderPayment($parentOrder, $physicalOrder, $ebookOrder, User $user, $paymentMethod)
    {
        // Kiểm tra nếu là thanh toán ví
        $isWalletPayment = stripos($paymentMethod->name, 'ví điện tử') !== false;
        
        if ($isWalletPayment) {
            // Xử lý thanh toán ví cho đơn cha (tổng tiền)
            $this->orderService->processWalletPayment($parentOrder, $user);
            
            // Cập nhật trạng thái thanh toán cho các đơn con
            $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
            if ($paymentStatus) {
                $physicalOrder->update(['payment_status_id' => $paymentStatus->id]);
                $ebookOrder->update(['payment_status_id' => $paymentStatus->id]);
                
                // Cập nhật trạng thái đơn hàng ebook thành 'Thành công' ngay sau khi thanh toán
                $this->orderService->updateEbookOrderStatusOnPaymentSuccess($ebookOrder);
            }
        }
        
        return $isWalletPayment;
    }

    /**
     * Xử lý sau khi tạo đơn hàng thành công
     */
    public function handlePostOrderCreation($parentOrder, $physicalOrder, $ebookOrder, User $user)
    {
        // Tạo đơn hàng GHN cho sách vật lý
        if ($physicalOrder->delivery_method === 'delivery') {
            $this->orderService->createGhnOrder($physicalOrder);
        }
        
        // Tạo mã QR cho các đơn hàng
        $this->qrCodeService->generateOrderQrCode($parentOrder);
        $this->qrCodeService->generateOrderQrCode($physicalOrder);
        $this->qrCodeService->generateOrderQrCode($ebookOrder);
        
        // Gửi email xác nhận
        $this->emailService->sendOrderConfirmation($parentOrder);
        $this->emailService->sendOrderConfirmation($physicalOrder);
        
        // Gửi email ebook ngay lập tức
        $this->emailService->sendEbookDownloadEmail($ebookOrder);
        
        // Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
        $this->orderService->updateEbookOrderStatusOnPaymentSuccess($ebookOrder);
        
        // Xóa giỏ hàng
        $this->orderService->clearUserCart($user);
    }
    
    /**
     * Trừ stock thuộc tính sản phẩm
     */
    private function decreaseAttributeStock($cartItem)
    {
        if (empty($cartItem->attribute_value_ids) || $cartItem->attribute_value_ids === '[]') {
            return;
        }

        $attributeValueIds = [];
        if (is_string($cartItem->attribute_value_ids)) {
            $decoded = json_decode($cartItem->attribute_value_ids, true);
            if (is_array($decoded)) {
                $attributeValueIds = $decoded;
            }
        } elseif (is_array($cartItem->attribute_value_ids)) {
            $attributeValueIds = $cartItem->attribute_value_ids;
        }

        if (empty($attributeValueIds)) {
            return;
        }

        foreach ($attributeValueIds as $attributeValueId) {
            $bookAttributeValue = \App\Models\BookAttributeValue::where('book_id', $cartItem->book_id)
                ->where('attribute_value_id', $attributeValueId)
                ->first();

            if ($bookAttributeValue && $bookAttributeValue->stock >= $cartItem->quantity) {
                $bookAttributeValue->decrement('stock', $cartItem->quantity);
                
                Log::info('Decreased attribute stock in mixed order:', [
                    'book_id' => $cartItem->book_id,
                    'attribute_value_id' => $attributeValueId,
                    'quantity_decreased' => $cartItem->quantity,
                    'remaining_stock' => $bookAttributeValue->fresh()->stock
                ]);
            }
        }
    }
    
    /**
     * Trừ số lượng quà tặng khi tạo đơn hàng
     */
    private function decreaseGiftStock($cartItem)
    {
        if (!isset($cartItem->book_id) || in_array($cartItem->book_id, $this->processedGiftBooks)) {
            return;
        }

        $this->processedGiftBooks[] = $cartItem->book_id;

        $giftBooks = \App\Models\BookGift::where('book_id', $cartItem->book_id)
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

        foreach ($giftBooks as $giftBook) {
            if ($giftBook->quantity >= $cartItem->quantity) {
                $giftBook->decrement('quantity', $cartItem->quantity);
                
                Log::info('Decreased gift stock in mixed order:', [
                    'gift_book_id' => $giftBook->id,
                    'book_id' => $cartItem->book_id,
                    'quantity_decreased' => $cartItem->quantity,
                    'remaining_stock' => $giftBook->fresh()->quantity
                ]);
            } else {
                Log::warning('Insufficient gift stock in mixed order:', [
                    'gift_book_id' => $giftBook->id,
                    'book_id' => $cartItem->book_id,
                    'available_quantity' => $giftBook->quantity,
                    'quantity_needed' => $cartItem->quantity
                ]);
                
                // Nếu không đủ quà tặng, trừ hết số lượng còn lại
                if ($giftBook->quantity > 0) {
                    $remainingQuantity = $giftBook->quantity;
                    $giftBook->update(['quantity' => 0]);
                    
                    Log::info('Used remaining gift stock in mixed order:', [
                        'gift_book_id' => $giftBook->id,
                        'quantity_used' => $remainingQuantity,
                        'quantity_still_needed' => $cartItem->quantity - $remainingQuantity
                    ]);
                }
            }
        }
    }
}