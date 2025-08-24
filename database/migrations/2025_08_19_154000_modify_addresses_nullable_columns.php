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
            $table->string('city')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('ward')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('city')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('ward')->nullable(false)->change();
        });
    }
};