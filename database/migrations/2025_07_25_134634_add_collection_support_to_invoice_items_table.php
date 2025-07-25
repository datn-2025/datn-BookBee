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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Thêm cột collection_id để hỗ trợ combo items
            $table->uuid('collection_id')->nullable()->after('book_id');
            
            // Cho phép book_id nullable để hỗ trợ combo items
            $table->uuid('book_id')->nullable()->change();
            
            // Thêm foreign key cho collection_id
            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onDelete('restrict');
                
            // Thêm index cho collection_id
            $table->index('collection_id');
            
            // Xóa unique constraint cũ và tạo lại để hỗ trợ combo
            $table->dropUnique(['invoice_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Xóa foreign key và index
            $table->dropForeign(['collection_id']);
            $table->dropIndex(['collection_id']);
            
            // Xóa cột collection_id
            $table->dropColumn('collection_id');
            
            // Khôi phục book_id không nullable
            $table->uuid('book_id')->nullable(false)->change();
            
            // Khôi phục unique constraint
            $table->unique(['invoice_id', 'book_id']);
        });
    }
};
