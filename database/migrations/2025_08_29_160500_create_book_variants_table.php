<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('book_id');
            $table->string('sku', 100)->nullable();
            $table->decimal('extra_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->index('book_id');
            $table->index('sku');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_variants');
    }
};
