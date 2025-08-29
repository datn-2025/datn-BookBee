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
        Schema::table('preorders', function (Blueprint $table) {
            // Làm các trường địa chỉ thành nullable cho ebook
            $table->text('address')->nullable()->change();
            $table->string('province_code')->nullable()->change();
            $table->string('province_name')->nullable()->change();
            $table->string('district_code')->nullable()->change();
            $table->string('district_name')->nullable()->change();
            $table->string('ward_code')->nullable()->change();
            $table->string('ward_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            // Khôi phục lại trạng thái ban đầu
            $table->text('address')->nullable(false)->change();
            $table->string('province_code')->nullable(false)->change();
            $table->string('province_name')->nullable(false)->change();
            $table->string('district_code')->nullable(false)->change();
            $table->string('district_name')->nullable(false)->change();
            $table->string('ward_code')->nullable(false)->change();
            $table->string('ward_name')->nullable(false)->change();
        });
    }
};
