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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('bank_number')->nullable()->after('bank_name');
            $table->string('customer_name')->nullable()->after('bank_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_number', 'customer_name']);
        });
    }
};
