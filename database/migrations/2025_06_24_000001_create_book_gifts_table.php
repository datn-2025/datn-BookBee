<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_gifts', function (Blueprint $table) {
            $table->id();
            $table->uuid('book_id');
            $table->string('gift_name');
            $table->text('gift_description')->nullable();
            $table->string('gift_image')->nullable();
            $table->integer('quantity')->default(0); // Số lượng quà tặng
            $table->date('start_date')->nullable(); // Ngày bắt đầu
            $table->date('end_date')->nullable();   // Ngày kết thúc
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_gifts');
    }
};
