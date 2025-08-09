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
            // Xóa foreign key constraint trước
            $table->dropForeign(['user_id']);
            
            // Làm user_id nullable để hỗ trợ khách hàng chưa đăng ký
            $table->uuid('user_id')->nullable()->change();
            
            // Thêm lại foreign key với nullable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            // Xóa foreign key
            $table->dropForeign(['user_id']);
            
            // Khôi phục user_id về not nullable
            $table->uuid('user_id')->nullable(false)->change();
            
            // Thêm lại foreign key ban đầu
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
