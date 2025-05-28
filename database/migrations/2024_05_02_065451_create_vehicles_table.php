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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_class_id');
            $table->string('vehicle_number', 50);
            $table->string('image', 250);
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->text('description')->nullable();
            $table->string('status', 20)->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('vehicle_class_id')->references('id')->on('vehicle_classes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
