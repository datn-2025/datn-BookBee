<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->timestamp('invoice_date');
            $table->decimal('total_amount', 12, 2);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('restrict');

            // Add indexes for frequently accessed columns
            $table->index('order_id');
            $table->index('invoice_date');
            // $table->unique('order_id'); // Each order can only have one invoice
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
