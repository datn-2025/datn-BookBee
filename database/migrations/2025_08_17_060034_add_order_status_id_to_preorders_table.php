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
            // Thêm cột order_status_id
            $table->uuid('order_status_id')->nullable()->after('selected_attributes');
            
            // Thêm foreign key
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('set null');
            
            // Thêm index
            $table->index('order_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropForeign(['order_status_id']);
            $table->dropIndex(['order_status_id']);
            $table->dropColumn('order_status_id');
        });
    }
};
