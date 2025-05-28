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
        Schema::create('billing_agreements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->decimal('per_trip_arr', 10, 2)->nullable();
            $table->decimal('per_trip_dep', 10, 2)->nullable();
            $table->decimal('per_trip_transfer', 10, 2)->nullable();
            $table->decimal('per_trip_delivery', 10, 2)->nullable();
            $table->decimal('per_hour_rate', 10, 2)->nullable();
            $table->decimal('peak_period_surcharge', 10, 2)->nullable();
            $table->enum('fixed_multiplier_midnight_surcharge_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('mid_night_surcharge_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_midnight_surcharge_less_then_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('midnight_surcharge_less_then_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_arrivel_waiting_time', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('arrivel_waiting_time', 10, 2)->nullable();
            $table->enum('fixed_multiplier_departure_and_transfer_waiting', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('departure_and_transfer_waiting', 10, 2)->nullable();
            $table->enum('fixed_multiplier_last_min_request_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('last_min_request_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_last_min_request_less_then_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('last_min_request_less_then_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_outside_city_surcharge_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('outside_city_surcharge_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_outside_city_surcharge_less_then_23_seats', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('outside_city_surcharge_less_then_23_seats', 10, 2)->nullable();
            $table->enum('fixed_multiplier_additional_stop', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('additional_stop', 10, 2)->nullable();
            $table->enum('fixed_multiplier_misc_charges', ['FIXED', 'MULTIPLIER'])->nullable();
            $table->decimal('misc_charges', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_agreements');
    }
};
