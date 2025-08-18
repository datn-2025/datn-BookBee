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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); // ID người nhận thông báo
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type'); // Loại thông báo: order_created, order_status_updated, etc.
            $table->string('title'); // Tiêu đề thông báo
            $table->text('message'); // Nội dung thông báo
            $table->json('data')->nullable(); // Dữ liệu bổ sung (order_id, status, etc.)
            $table->timestamp('read_at')->nullable(); // Thời gian đọc thông báo
            $table->timestamps();
            
            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
