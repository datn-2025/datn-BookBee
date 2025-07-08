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
        Schema::table('carts', function (Blueprint $table) {
            // Thêm unique constraint để ngăn chặn duplicate records trong cart
            // Một user chỉ có thể có 1 record duy nhất cho cùng book + format + attributes
            $table->unique(['user_id', 'book_id', 'book_format_id', 'attribute_value_ids'], 'unique_cart_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('unique_cart_item');
        });
    }
};
