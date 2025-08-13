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
        // Kiểm tra xem cột collection_id đã tồn tại chưa
        if (!Schema::hasColumn('reviews', 'collection_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                // Cho phép book_id nullable để hỗ trợ đánh giá combo
                $table->uuid('book_id')->nullable()->change();
                
                // Thêm cột collection_id để hỗ trợ đánh giá combo
                $table->uuid('collection_id')->nullable()->after('book_id');
                
                // Thêm foreign key cho collection
                $table->foreign('collection_id')
                    ->references('id')
                    ->on('collections')
                    ->onDelete('cascade');
                
                // Thêm index cho collection_id
                $table->index('collection_id');
            });
        }
        
        // Xóa unique constraint cũ nếu tồn tại
        try {
            DB::statement('ALTER TABLE reviews DROP INDEX reviews_user_id_book_id_unique');
        } catch (\Exception $e) {
            // Constraint có thể không tồn tại, bỏ qua lỗi
        }
        
        // Thêm unique constraint mới để ngăn đánh giá trùng lặp
        try {
            DB::statement('ALTER TABLE reviews ADD UNIQUE KEY unique_user_product_order_review (user_id, book_id, collection_id, order_id)');
        } catch (\Exception $e) {
            // Constraint có thể đã tồn tại, bỏ qua lỗi
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Xóa unique constraint mới
            $table->dropUnique('unique_user_product_order_review');
            
            // Khôi phục unique constraint cũ
            $table->unique(['user_id', 'book_id']);
            
            // Xóa foreign key và index
            $table->dropForeign(['collection_id']);
            $table->dropIndex(['collection_id']);
            
            // Xóa cột collection_id
            $table->dropColumn('collection_id');
            
            // Khôi phục book_id về NOT NULL
            $table->uuid('book_id')->nullable(false)->change();
        });
    }
};
