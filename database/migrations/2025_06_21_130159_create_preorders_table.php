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
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->uuid('book_id');
            $table->string('book_title');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(30000);
            $table->decimal('book_total', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->uuid('book_format_id')->nullable();
            $table->string('book_format_name')->nullable();
            $table->json('attributes')->nullable();
            $table->json('attributes_display')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('book_format_id')->references('id')->on('book_formats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preorders');
    }
};
