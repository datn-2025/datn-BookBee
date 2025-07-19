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
        Schema::table('carts', function (Blueprint $table) {
            // Add combo support columns
            $table->uuid('collection_id')->nullable()->after('book_format_id');
            $table->boolean('is_combo')->default(false)->after('collection_id');
            
            // Make book-related columns nullable to support combos
            $table->uuid('book_id')->nullable()->change();
            $table->uuid('book_format_id')->nullable()->change();
            
            // Add foreign key for collection
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            
            // Add index for combo queries
            $table->index(['user_id', 'is_combo']);
            $table->index('collection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Drop foreign key and indexes first
            $table->dropForeign(['collection_id']);
            $table->dropIndex(['user_id', 'is_combo']);
            $table->dropIndex(['collection_id']);
            
            // Drop columns
            $table->dropColumn(['collection_id', 'is_combo']);
            
            // Restore book columns to not nullable (if needed)
            $table->uuid('book_id')->nullable(false)->change();
            $table->uuid('book_format_id')->nullable(false)->change();
        });
    }
};
