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
        Schema::create('corporate_fair_billings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->unsignedBigInteger('vehicle_class_id')->nullable();
            $table->foreign('vehicle_class_id')->references('id')->on('vehicle_classes');
            $table->enum('billing_type', ['Arrival', 'Departure', 'Transfer', 'Hour'])->default('Arrival');
            $table->bigInteger('amount')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
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
        Schema::dropIfExists('corporate_fair_billings');
    }
};
