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
        Schema::table('new', function (Blueprint $table) {
            Schema::rename('pickup_locations', 'locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new', function (Blueprint $table) {
            Schema::rename('locations', 'pickup_locations');
        });
    }
};
