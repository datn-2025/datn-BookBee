<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); 
            $table->string('image', 500)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Add indexes for frequently accessed columns
            $table->index('name');
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
