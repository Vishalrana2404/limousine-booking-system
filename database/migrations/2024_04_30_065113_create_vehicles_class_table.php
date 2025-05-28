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
        Schema::create('vehicle_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->unsignedBigInteger('seating_capacity');
            $table->unsignedBigInteger('total_luggage');
            $table->unsignedBigInteger('total_pax');
            $table->text('description')->nullable();
            $table->string('status', 20)->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_classes');
    }
};
