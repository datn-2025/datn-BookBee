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
        Schema::create('combo_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('collection_id');
            $table->text('summary')->nullable();
            $table->text('detailed_summary')->nullable();
            $table->json('key_points')->nullable();
            $table->json('themes')->nullable();
            $table->json('benefits')->nullable();
            $table->string('ai_model')->default('gemini-1.5-flash');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            $table->index(['collection_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_summaries');
    }
};
