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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('type')->default('sale')->after('order_id'); // 'sale' hoặc 'refund'
            $table->uuid('refund_request_id')->nullable()->after('type');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('total_amount');
            $table->string('refund_method')->nullable()->after('refund_amount'); // 'wallet' hoặc 'vnpay'
            $table->string('refund_reason')->nullable()->after('refund_method');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_reason');
            
            // Foreign key constraint
            $table->foreign('refund_request_id')
                ->references('id')
                ->on('refund_requests')
                ->onDelete('set null');
            
            // Add index for type column
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['refund_request_id']);
            $table->dropIndex(['type']);
            $table->dropColumn([
                'type',
                'refund_request_id', 
                'refund_amount',
                'refund_method',
                'refund_reason',
                'refund_processed_at'
            ]);
        });
    }
};
