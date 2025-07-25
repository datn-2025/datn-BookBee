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
        Schema::table('collections', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('cover_image');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('combo_price', 10, 2)->nullable()->after('end_date');

            // Index để tối ưu truy vấn theo thời gian
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'combo_price']);
        });
    }
};
