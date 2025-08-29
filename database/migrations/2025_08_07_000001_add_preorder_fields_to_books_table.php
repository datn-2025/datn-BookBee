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
            // Thêm các trường mới cho preorder
            if (!Schema::hasColumn('books', 'stock_preorder_limit')) {
                $table->integer('stock_preorder_limit')->nullable();
            }
            
            if (!Schema::hasColumn('books', 'preorder_description')) {
                $table->text('preorder_description')->nullable()->after('stock_preorder_limit');
                $table->date('release_date')->nullable()->after('preorder_description');
            }
            $table->boolean('pre_order')->default(false)->after('release_date');
            $table->integer('pre_order_price')->default(0)->after('pre_order');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Chỉ xóa những trường mình đã thêm
            if (Schema::hasColumn('books', 'stock_preorder_limit')) {
                $table->dropColumn('stock_preorder_limit');
            }
            
            if (Schema::hasColumn('books', 'preorder_description')) {
                $table->dropColumn('preorder_description');
            }
            
            // Xóa indexes nếu có
            if (Schema::hasIndex('books', 'books_release_date_index')) {
                $table->dropIndex(['release_date']);
            }
            if (Schema::hasIndex('books', 'books_pre_order_index')) {
                $table->dropIndex(['pre_order']);
            }
        });
    }
};
