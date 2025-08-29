<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Thêm khóa ngoại tới bảng book_gifts (nullable)
            $table->foreignId('book_gift_id')->nullable()->after('book_id')
                ->constrained('book_gifts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Xóa ràng buộc và cột
            $table->dropForeign(['book_gift_id']);
            $table->dropColumn('book_gift_id');
        });
    }
};
