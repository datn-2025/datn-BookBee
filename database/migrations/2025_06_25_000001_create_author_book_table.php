<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('author_books', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->uuid('book_id'); // Foreign key to books table
            $table->uuid('author_id'); // Foreign key to authors table
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['book_id', 'author_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('author_books');
    }
};
