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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name', 50)->nullable();
            $table->string('event', 250)->nullable();
            $table->string('hotel',250)->nullable();
            $table->string('invoice', 250)->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->bigInteger('updated_by_id')->unsigned()->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
