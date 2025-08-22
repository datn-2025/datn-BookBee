<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Preorder;
use App\Models\OrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapping từ status cũ sang OrderStatus ID
        $statusMapping = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang chuẩn bị',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
            'cancelled' => 'Đã hủy'
        ];

        // Lấy tất cả OrderStatus
        $orderStatuses = OrderStatus::all()->keyBy('name');

        // Cập nhật từng preorder
        foreach (Preorder::all() as $preorder) {
            $statusName = $statusMapping[$preorder->status] ?? null;
            
            if ($statusName && isset($orderStatuses[$statusName])) {
                $preorder->update([
                    'order_status_id' => $orderStatuses[$statusName]->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa order_status_id khỏi tất cả preorders
        Preorder::query()->update(['order_status_id' => null]);
    }
};
