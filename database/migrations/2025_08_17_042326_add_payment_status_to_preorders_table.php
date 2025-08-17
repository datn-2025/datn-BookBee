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
        $table->enum('payment_status', ['pending', 'paid', 'failed'])
              ->default('pending')
              ->after('selected_attributes'); 
    });
}

public function down(): void
{
    Schema::table('preorders', function (Blueprint $table) {
        $table->dropColumn('payment_status');
    });
}

};
