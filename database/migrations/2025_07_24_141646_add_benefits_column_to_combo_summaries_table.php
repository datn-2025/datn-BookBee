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
        Schema::table('combo_summaries', function (Blueprint $table) {
            // Add the missing benefits column            
            // Modify existing columns to be JSON type to match our model expectations
            $table->json('key_points')->nullable()->change();
            $table->json('themes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combo_summaries', function (Blueprint $table) {
            // Remove the benefits colum            
            // Revert column types back to longtext
            $table->longText('key_points')->nullable()->change();
            $table->longText('themes')->nullable()->change();
        });
    }
};
