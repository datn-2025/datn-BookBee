<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('book_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->enum('status', ['visible', 'hidden'])->default('visible');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->onDelete('cascade');

            // Add indexes for frequently accessed columns
            $table->index('user_id');
            $table->index('book_id');
            $table->index('status');
            $table->index('rating');
            $table->index('created_at');
            
            // Prevent multiple reviews from same user for same book
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
