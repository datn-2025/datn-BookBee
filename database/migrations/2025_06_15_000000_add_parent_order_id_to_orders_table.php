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
            $table->uuid('parent_order_id')->nullable()->after('id');
            $table->foreign('parent_order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('set null');
            $table->index('parent_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['parent_order_id']);
            $table->dropIndex(['parent_order_id']);
            $table->dropColumn('parent_order_id');
        });
    }
};