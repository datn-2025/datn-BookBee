<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Thêm cột order_column vào bảng book_collections
        Schema::table('book_collections', function (Blueprint $table) {
            $table->integer('order_column')->nullable()->after('collection_id');
        });

        // Thêm cột is_series vào bảng books
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_series')->default(false)->after('page_count');
        });
    }

    public function down()
    {
        Schema::table('book_collections', function (Blueprint $table) {
            $table->dropColumn('order_column');
        });
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('is_series');
        });
    }
};
