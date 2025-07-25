<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật enum payment_method để thêm giá trị 'wallet'
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN payment_method ENUM('bank_transfer', 'vnpay', 'wallet') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum payment_method về trạng thái ban đầu
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN payment_method ENUM('bank_transfer', 'vnpay') NULL");
    }
};
