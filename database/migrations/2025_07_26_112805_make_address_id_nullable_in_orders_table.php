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
        Schema::table('orders', function (Blueprint $table) {
            // Xóa foreign key constraint trước
            $table->dropForeign(['address_id']);
            
            // Thay đổi cột address_id thành nullable
            $table->uuid('address_id')->nullable()->change();
            
            // Thêm lại foreign key constraint với nullable
            $table->foreign('address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa foreign key constraint
            $table->dropForeign(['address_id']);
            
            // Thay đổi cột address_id về không nullable
            $table->uuid('address_id')->nullable(false)->change();
            
            // Thêm lại foreign key constraint
            $table->foreign('address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('restrict');
        });
    }
};
