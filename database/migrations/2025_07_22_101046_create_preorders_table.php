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
            $table->uuid('id')->primary(); // Dùng UUID thay vì bigint

            $table->uuid('user_id');
            $table->uuid('book_id');
            $table->uuid('book_format_id')->nullable();
            
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('province_code');
            $table->string('province_name');
            $table->string('district_code');
            $table->string('district_name');
            $table->string('ward_code');
            $table->string('ward_name');
            
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->json('selected_attributes')->nullable(); // Lưu thuộc tính đã chọn
            
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamp('expected_delivery_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('book_format_id')->references('id')->on('book_formats')->onDelete('set null');

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('status');
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
