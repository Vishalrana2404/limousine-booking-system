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
        Schema::table('outside_city_surcharges', function (Blueprint $table) {
            $table->string('region', 255)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->decimal('latitude', 10, 7)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outside_city_surcharges', function (Blueprint $table) {
            $table->string('region', 100);
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
        });
    }
};
