<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Thêm cột order_id kiểu uuid, cho phép null
            $table->uuid('order_id')->after('book_id')->nullable();
            
            // Thêm khóa ngoại tham chiếu đến bảng orders
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
                
            // Thêm index cho cột order_id để tối ưu truy vấn
            $table->index('order_id');
            
            // Xóa ràng buộc unique cũ (nếu có)
            $table->dropUnique(['user_id', 'book_id']);
            
            // Thêm lại ràng buộc unique mới bao gồm cả order_id
            $table->unique(['user_id', 'book_id', 'order_id']);
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Xóa khóa ngoại trước
            $table->dropForeign(['order_id']);
            
            // Xóa index
            $table->dropIndex(['order_id']);
            
            // Xóa cột order_id
            $table->dropColumn('order_id');
            
            // Khôi phục lại ràng buộc unique ban đầu
            $table->dropUnique(['user_id', 'book_id', 'order_id']);
            $table->unique(['user_id', 'book_id']);
        });
    }
};