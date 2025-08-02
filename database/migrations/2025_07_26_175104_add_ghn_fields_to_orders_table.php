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
        Schema::table('orders', function (Blueprint $table) {
            // Thêm các trường cho GHN
            $table->string('ghn_order_code')->nullable()->after('order_code')->comment('Mã vận đơn GHN');
            $table->integer('ghn_service_type_id')->default(2)->after('ghn_order_code')->comment('Loại dịch vụ GHN (2: tiêu chuẩn)');
            $table->timestamp('expected_delivery_date')->nullable()->after('ghn_service_type_id')->comment('Ngày giao hàng dự kiến');
            $table->json('ghn_tracking_data')->nullable()->after('expected_delivery_date')->comment('Dữ liệu theo dõi từ GHN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ghn_order_code',
                'ghn_service_type_id', 
                'expected_delivery_date',
                'ghn_tracking_data'
            ]);
        });
    }
};
