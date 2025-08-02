<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật enum delivery_method để thêm giá trị 'mixed'
        DB::statement("ALTER TABLE orders MODIFY COLUMN delivery_method ENUM('delivery', 'pickup', 'ebook', 'mixed') DEFAULT 'delivery'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kiểm tra xem có đơn hàng nào sử dụng 'mixed' không trước khi rollback
        $mixedOrdersCount = DB::table('orders')->where('delivery_method', 'mixed')->count();
        
        if ($mixedOrdersCount > 0) {
            throw new Exception("Cannot rollback: There are {$mixedOrdersCount} orders with delivery_method = 'mixed'. Please handle these orders first.");
        }
        
        // Rollback về enum cũ
        DB::statement("ALTER TABLE orders MODIFY COLUMN delivery_method ENUM('delivery', 'pickup', 'ebook') DEFAULT 'delivery'");
    }
};