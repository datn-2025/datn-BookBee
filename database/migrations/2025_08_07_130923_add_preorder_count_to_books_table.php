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
        Schema::table('books', function (Blueprint $table) {
            // Thêm cột preorder_count để đếm số lượng đặt trước
            $table->integer('preorder_count')->default(0)->after('pre_order_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Xóa cột preorder_count
            $table->dropColumn('preorder_count');
        });
    }
};
