<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Thêm cột order_column vào bảng book_collections nếu chưa có
        if (Schema::hasTable('book_collections') && !Schema::hasColumn('book_collections', 'order_column')) {
            Schema::table('book_collections', function (Blueprint $table) {
                $table->integer('order_column')->nullable()->after('collection_id');
            });
        }

        // Thêm cột is_series vào bảng books nếu chưa có
        if (Schema::hasTable('books') && !Schema::hasColumn('books', 'is_series')) {
            Schema::table('books', function (Blueprint $table) {
                $table->boolean('is_series')->default(false)->after('page_count');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('book_collections') && Schema::hasColumn('book_collections', 'order_column')) {
            Schema::table('book_collections', function (Blueprint $table) {
                $table->dropColumn('order_column');
            });
        }
        
        if (Schema::hasTable('books') && Schema::hasColumn('books', 'is_series')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('is_series');
            });
        }
    }
};
