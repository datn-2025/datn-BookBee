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
            $table->string('preorder_code')->nullable()->after('id');
            $table->string('payment_status')->nullable()->after('status');
            $table->string('vnpay_transaction_id')->nullable()->after('payment_status');
            $table->text('cancellation_reason')->nullable()->after('vnpay_transaction_id');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropColumn([
                'preorder_code',
                'payment_status',
                'vnpay_transaction_id',
                'cancellation_reason',
                'cancelled_at'
            ]);
        });
    }
};
