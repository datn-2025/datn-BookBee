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
        Schema::table('order_items', function (Blueprint $table) {
            // Kiểm tra và thêm collection_id nếu chưa tồn tại
            if (!Schema::hasColumn('order_items', 'collection_id')) {
                $table->char('collection_id', 36)->nullable()->after('book_format_id');
                $table->foreign('collection_id')->references('id')->on('collections')->onDelete('set null');
                $table->index('collection_id');
            }
            
            // Kiểm tra và thêm is_combo nếu chưa tồn tại
            if (!Schema::hasColumn('order_items', 'is_combo')) {
                $table->boolean('is_combo')->default(false)->after('collection_id');
            }
            
            // Kiểm tra và thêm item_type nếu chưa tồn tại
            if (!Schema::hasColumn('order_items', 'item_type')) {
                $table->string('item_type', 20)->default('book')->after('is_combo');
                $table->index('item_type');
            }
        });
        
        // Thay đổi book_id thành nullable nếu chưa nullable
        Schema::table('order_items', function (Blueprint $table) {
            $table->char('book_id', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Xóa các cột đã thêm
            if (Schema::hasColumn('order_items', 'collection_id')) {
                $table->dropForeign(['collection_id']);
                $table->dropIndex(['collection_id']);
                $table->dropColumn('collection_id');
            }
            
            if (Schema::hasColumn('order_items', 'is_combo')) {
                $table->dropColumn('is_combo');
            }
            
            if (Schema::hasColumn('order_items', 'item_type')) {
                $table->dropIndex(['item_type']);
                $table->dropColumn('item_type');
            }
        });
        
        // Khôi phục book_id về not null
        Schema::table('order_items', function (Blueprint $table) {
            $table->char('book_id', 36)->nullable(false)->change();
        });
    }
};