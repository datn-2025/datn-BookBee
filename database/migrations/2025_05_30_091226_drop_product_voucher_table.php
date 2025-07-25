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
        Schema::dropIfExists('product_voucher');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('product_voucher', function (Blueprint $table) {
            $table->uuid('voucher_id');
            $table->uuid('book_id');

            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }
};
