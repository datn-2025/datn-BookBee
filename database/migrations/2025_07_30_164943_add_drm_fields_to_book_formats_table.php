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
        Schema::table('book_formats', function (Blueprint $table) {
            $table->integer('max_downloads')->default(5)->comment('Số lần tải tối đa cho ebook');
            $table->boolean('drm_enabled')->default(true)->comment('Bật/tắt DRM cho ebook');
            $table->integer('download_expiry_days')->default(365)->comment('Số ngày hết hạn tải ebook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_formats', function (Blueprint $table) {
            $table->dropColumn(['max_downloads', 'drm_enabled', 'download_expiry_days']);
        });
    }
};
