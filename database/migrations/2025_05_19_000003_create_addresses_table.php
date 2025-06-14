<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address_detail')->nullable();
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->boolean('is_default');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index('user_id');
            $table->index('city');
            $table->index('district');
            $table->index('is_default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
