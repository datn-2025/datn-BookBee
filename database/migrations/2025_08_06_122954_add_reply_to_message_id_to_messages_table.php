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
        Schema::table('messages', function (Blueprint $table) {
            // Chỉ thêm foreign key vì cột đã tồn tại
            if (Schema::hasColumn('messages', 'reply_to_message_id')) {
                $table->foreign('reply_to_message_id')->references('id')->on('messages')->onDelete('set null');
            } else {
                // Nếu chưa có cột thì tạo cột và foreign key
                $table->uuid('reply_to_message_id')->nullable()->after('sender_id');
                $table->foreign('reply_to_message_id')->references('id')->on('messages')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['reply_to_message_id']);
            $table->dropColumn('reply_to_message_id');
        });
    }
};
