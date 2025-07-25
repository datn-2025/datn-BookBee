<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('book_formats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('book_id');
            $table->string('format_name');
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->string('file_url', 500)->nullable();
            $table->string('sample_file_url', 500)->nullable();
            $table->boolean('allow_sample_read')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->onDelete('cascade');

            // Add indexes for frequently accessed columns
            $table->index('book_id');
            $table->index('price');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_formats');
    }
};
