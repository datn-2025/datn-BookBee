<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_variant_attribute_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('book_variant_id');
            $table->uuid('attribute_value_id');
            $table->timestamps();

            $table->foreign('book_variant_id')->references('id')->on('book_variants')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');

            $table->unique(['book_variant_id', 'attribute_value_id'], 'bv_attr_val_unique');
            $table->index('book_variant_id');
            $table->index('attribute_value_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_variant_attribute_values');
    }
};
