<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('discount_type')->default('percent')->after('description');
            $table->decimal('fixed_discount', 15, 2)->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'fixed_discount']);
        });
    }
};
