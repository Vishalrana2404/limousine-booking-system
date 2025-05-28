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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('type', 20);
            $table->string('environment', 20);
            $table->boolean('status')->comment('true for open, false for closed');
            $table->string('level_name', 255);
            $table->unsignedSmallInteger('level');
            $table->text('message');
            $table->json('context');
            $table->json('extra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
