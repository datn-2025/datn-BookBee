<?php

namespace App\Observers;

use App\Models\Book;
use App\Models\Preorder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookObserver
{
    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        Log::info("BookObserver: updated() được gọi cho sách {$book->title}");
        Log::info("BookObserver: isDirty('status'): " . ($book->isDirty('status') ? 'true' : 'false'));
        Log::info("BookObserver: status hiện tại: {$book->status}");
        // Kiểm tra nếu trạng thái sách thay đổi thành "Còn hàng"
        if ($book->isDirty('status') && $book->status === 'Còn hàng') {
            Log::info("BookObserver: Điều kiện thỏa mãn - bắt đầu tạo orders");
            $this->createOrdersFromPreorders($book);
        } else {
            Log::info("BookObserver: Điều kiện không thỏa mãn - không tạo orders");
        }
    }

    /**
     * Tự động tạo orders từ preorders khi sách có trạng thái "Còn hàng"
     */
    private function createOrdersFromPreorders(Book $book): void
    {
        try {
            // Lấy tất cả preorders của sách này (không phân biệt trạng thái)
            $preorders = Preorder::where('book_id', $book->id)
                ->with(['user', 'book', 'bookFormat', 'paymentMethod'])
                ->get();

            if ($preorders->isEmpty()) {
                Log::info("BookObserver: Không có preorders nào cho sách {$book->title}");
                return;
            }

            $createdOrdersCount = 0;

            foreach ($preorders as $preorder) {
                DB::transaction(function () use ($preorder, &$createdOrdersCount) {
                    try {
                        // Kiểm tra xem đã có order cho preorder này chưa
                        $existingOrder = Order::where('preorder_id', $preorder->id)->first();
                        if ($existingOrder) {
                            Log::info("BookObserver: Preorder {$preorder->id} đã có order {$existingOrder->id}");
                            return;
                        }

                        // Tạo hoặc lấy địa chỉ
                        $address = Address::firstOrCreate([
                            'user_id' => $preorder->user_id,
                            'address_detail' => $preorder->address . ', ' . $preorder->ward_name . ', ' . $preorder->district_name . ', ' . $preorder->province_name,
                            'city' => $preorder->province_name,
                            'district' => $preorder->district_name,
                            'ward' => $preorder->ward_name,
                        ], [
                            'is_default' => false,
                        ]);

                        // Lấy trạng thái đơn hàng và thanh toán mặc định
                        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->first();
                        $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();

                        if (!$orderStatus || !$paymentStatus) {
                            Log::error("BookObserver: Không tìm thấy order status hoặc payment status");
                            return;
                        }

                        // Tạo order mới
                        $order = Order::create([
                            'id' => Str::uuid(),
                            'user_id' => $preorder->user_id,
                            'order_code' => 'PRE-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                            'order_status_id' => $orderStatus->id,
                            'payment_status_id' => $paymentStatus->id,
                            'payment_method_id' => $preorder->payment_method_id,
                            'address_id' => $address->id,
                            'recipient_name' => $preorder->customer_name,
                            'recipient_phone' => $preorder->phone,
                            'recipient_email' => $preorder->email,
                            'shipping_fee' => 0,
                            'total_amount' => $preorder->total_amount,
                            'note' => ($preorder->notes ?? '') . ' (Tự động tạo từ đặt trước)',
                            'preorder_id' => $preorder->id,
                        ]);

                        // Tạo order item
                        OrderItem::create([
                            'id' => Str::uuid(),
                            'order_id' => $order->id,
                            'book_id' => $preorder->book_id,
                            'book_format_id' => $preorder->book_format_id,
                            'quantity' => $preorder->quantity,
                            'unit_price' => $preorder->unit_price,
                            'total_price' => $preorder->total_amount,
                            'selected_attributes' => $preorder->selected_attributes,
                        ]);

                        // Cập nhật trạng thái preorder thành "confirmed"
                        $preorder->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);

                        $createdOrdersCount++;
                        
                        Log::info("BookObserver: Đã tạo order {$order->order_code} từ preorder {$preorder->id}");
                        
                    } catch (\Exception $e) {
                        Log::error("BookObserver: Lỗi khi tạo order từ preorder {$preorder->id}: " . $e->getMessage());
                        throw $e;
                    }
                });
            }

            Log::info("BookObserver: Đã tạo {$createdOrdersCount} orders từ {$preorders->count()} preorders cho sách {$book->title}");
            
        } catch (\Exception $e) {
            Log::error("BookObserver: Lỗi khi xử lý preorders cho sách {$book->title}: " . $e->getMessage());
        }
    }
}
