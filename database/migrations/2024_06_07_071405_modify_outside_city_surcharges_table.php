<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOutsideCitySurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outside_city_surcharges', function (Blueprint $table) {
            // Drop latitude and longitude columns
            $table->dropColumn(['longitude', 'latitude','region']);
            // Add coordinates column to store polygon coordinates
            $table->json('coordinates')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outside_city_surcharges', function (Blueprint $table) {
            // Re-add longitude and latitude columns
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            // Drop coordinates column
            $table->dropColumn('coordinates');
        });
    }
}
