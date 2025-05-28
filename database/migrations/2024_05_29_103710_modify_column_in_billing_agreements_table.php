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
        Schema::table('billing_agreements', function (Blueprint $table) {
            $table->renameColumn('fixed_multiplier_midnight_surcharge_less_then_23_seats', 'fixed_multiplier_midnight_surcharge_greater_then_23_seats');
            $table->renameColumn('midnight_surcharge_less_then_23_seats', 'midnight_surcharge_greater_then_23_seats');
            $table->renameColumn('fixed_multiplier_last_min_request_less_then_23_seats', 'fixed_multiplier_last_min_request_greater_then_23_seats');
            $table->renameColumn('last_min_request_less_then_23_seats', 'last_min_request_greater_then_23_seats');
            $table->renameColumn('fixed_multiplier_outside_city_surcharge_less_then_23_seats', 'fixed_multiplier_outside_city_surcharge_greater_then_23_seats');
            $table->renameColumn('outside_city_surcharge_less_then_23_seats', 'outside_city_surcharge_greater_then_23_seats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_agreements', function (Blueprint $table) {
           $table->renameColumn('fixed_multiplier_midnight_surcharge_greater_then_23_seats', 'fixed_multiplier_midnight_surcharge_less_then_23_seats');
            $table->renameColumn('midnight_surcharge_greater_then_23_seats', 'midnight_surcharge_less_then_23_seats');
            $table->renameColumn('fixed_multiplier_last_min_request_greater_then_23_seats', 'fixed_multiplier_last_min_request_less_then_23_seats');
            $table->renameColumn('last_min_request_greater_then_23_seats', 'last_min_request_less_then_23_seats');
            $table->renameColumn('fixed_multiplier_outside_city_surcharge_greater_then_23_seats', 'fixed_multiplier_outside_city_surcharge_less_then_23_seats');
            $table->renameColumn('outside_city_surcharge_greater_then_23_seats', 'outside_city_surcharge_less_then_23_seats');
         });
    }
};
