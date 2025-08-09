<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_attribute_values', function (Blueprint $table) {
            // Thêm cột extra_price trước
            $table->decimal('extra_price', 12, 2)->default(0)->after('attribute_value_id');
            
            // Thêm cột stock để quản lý số lượng tồn kho theo biến thể
            $table->integer('stock')->default(0)->after('extra_price');
            
            // Thêm cột SKU để quản lý mã biến thể
            $table->string('sku', 100)->nullable()->after('stock');
            
            // Thêm index cho SKU để tìm kiếm nhanh
            $table->index('sku');
            
            // Thêm index cho stock để báo cáo tồn kho
            $table->index('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_attribute_values', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropIndex(['stock']);
            $table->dropColumn(['extra_price', 'stock', 'sku']);
        });
    }
};