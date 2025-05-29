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
        Schema::table('vehicle_classes', function (Blueprint $table) {
            $table->bigInteger('sequence_no')->nullable()->after('meet_and_greet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_classes', function (Blueprint $table) {
            $table->dropColumn(['sequence_no']);
        });
    }
};
