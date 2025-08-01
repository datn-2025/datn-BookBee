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
        // Thêm column order_item_id trước (nullable)
        Schema::table('ebook_downloads', function (Blueprint $table) {
            $table->uuid('order_item_id')->nullable()->after('order_id');
        });
        
        // Cập nhật dữ liệu hiện có
        DB::statement("
            UPDATE ebook_downloads ed 
            SET order_item_id = (
                SELECT oi.id 
                FROM order_items oi 
                WHERE oi.order_id = ed.order_id 
                AND oi.book_format_id = ed.book_format_id 
                LIMIT 1
            )
        ");
        
        // Thêm foreign key constraint và index
        Schema::table('ebook_downloads', function (Blueprint $table) {
            $table->uuid('order_item_id')->nullable(false)->change();
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->index(['user_id', 'order_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebook_downloads', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropIndex(['user_id', 'order_item_id']);
            $table->dropColumn('order_item_id');
        });
    }
};
