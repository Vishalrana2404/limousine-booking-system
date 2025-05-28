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
        Schema::create('outside_city_surcharges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('region', 100);
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
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
        Schema::dropIfExists('outside_city_surcharges');
    }
};
