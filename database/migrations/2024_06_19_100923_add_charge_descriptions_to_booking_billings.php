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
        Schema::table('booking_billings', function (Blueprint $table) {
            $table->text('arrival_charge_description')->nullable();
            $table->text('transfer_charge_description')->nullable();
            $table->text('departure_charge_description')->nullable();
            $table->text('disposal_charge_description')->nullable();
            $table->text('delivery_charge_description')->nullable();
            $table->text('peak_period_charge_description')->nullable();
            $table->text('mid_night_charge_description')->nullable();
            $table->text('arrivel_waiting_charge_description')->nullable();
            $table->text('outside_city_charge_description')->nullable();
            $table->text('last_minute_charge_description')->nullable();
            $table->text('additional_charge_description')->nullable();
            $table->text('misc_charge_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_billings', function (Blueprint $table) {
            $table->dropColumn([
                'arrival_charge_description',
                'transfer_charge_description',
                'departure_charge_description',
                'disposal_charge_description',
                'delivery_charge_description',
                'peak_period_charge_description',
                'mid_night_charge_description',
                'arrivel_waiting_charge_description',
                'outside_city_charge_description',
                'last_minute_charge_description',
                'additional_charge_description',
                'misc_charge_description',
            ]);
        });
    }
};
