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
        Schema::table('preorders', function (Blueprint $table) {
            // Sửa kiểu dữ liệu của converted_order_id thành varchar(36) để tương thích với UUID
            $table->string('converted_order_id', 36)->nullable()->change();
            
            // Thêm foreign key constraint
            $table->foreign('converted_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropForeign(['converted_order_id']);
        });
    }
};
