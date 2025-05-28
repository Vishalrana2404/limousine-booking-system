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
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->text('message');
            $table->bigInteger('booking_id')->unsigned()->nullable();;
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_logs');
    }
};
