<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('book_id');
            $table->text('summary')->nullable(); // Tóm tắt ngắn
            $table->longText('detailed_summary')->nullable(); // Tóm tắt chi tiết
            $table->json('key_points')->nullable(); // Những điểm chính
            $table->json('themes')->nullable(); // Chủ đề của sách
            $table->string('ai_model')->default('openai'); // Model AI đã sử dụng
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // Lưu lỗi nếu có
            $table->timestamps();
            
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->index(['book_id', 'status']);
            $table->unique('book_id'); // Mỗi sách chỉ có 1 summary
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_summaries');
    }
};
