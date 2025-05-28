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
        Schema::create('booking_billings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id')->unsigned()->nullable();
            $table->decimal('arrival_charge', 10, 2)->nullable();
            $table->decimal('transfer_charge', 10, 2)->nullable();
            $table->decimal('departure_charge', 10, 2)->nullable();
            $table->decimal('disposal_charge', 10, 2)->nullable();
            $table->decimal('delivery_charge', 10, 2)->nullable();
            $table->boolean('is_peak_period_surcharge')->default(0);
            $table->decimal('peak_period_surcharge', 10, 2)->nullable();
            $table->boolean('is_mid_night_surcharge')->default(0);
            $table->boolean('is_fixed_midnight_surcharge')->default(0);
            $table->decimal('mid_night_surcharge', 10, 2)->nullable();
            $table->boolean('is_fixed_arrival_waiting_surcharge')->default(0);
            $table->boolean('is_arr_waiting_time_surcharge')->default(0);
            $table->decimal('arrivel_waiting_time_surcharge', 10, 2)->nullable();
            $table->boolean('is_fixed_outside_city_surcharge')->default(0);
            $table->boolean('is_outside_city_surcharge')->default(0);
            $table->decimal('outside_city_surcharge', 10, 2)->nullable();
            $table->boolean('is_fixed_last_minute_surcharge')->default(0);
            $table->boolean('is_last_minute_surcharge')->default(0);
            $table->decimal('last_minute_surcharge', 10, 2)->nullable();
            $table->boolean('is_fixed_additional_stop_surcharge')->default(0);
            $table->boolean('is_additional_stop_surcharge')->default(0);
            $table->decimal('additional_stop_surcharge', 10, 2)->nullable();
            $table->boolean('is_fixed_misc_surcharge')->default(0);
            $table->boolean('is_misc_surcharge')->default(0);
            $table->decimal('misc_surcharge', 10, 2)->nullable();
            $table->decimal('total_charge', 10, 2)->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('updated_by_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_billings');
    }
};
