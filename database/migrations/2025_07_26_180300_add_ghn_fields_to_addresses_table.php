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
        Schema::table('addresses', function (Blueprint $table) {
            // Thêm các trường cho GHN
            $table->integer('province_id')->nullable()->after('city')->comment('ID tỉnh/thành phố GHN');
            $table->integer('district_id')->nullable()->after('district')->comment('ID quận/huyện GHN');
            $table->string('ward_code')->nullable()->after('ward')->comment('Mã phường/xã GHN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn([
                'province_id',
                'district_id',
                'ward_code'
            ]);
        });
    }
};
