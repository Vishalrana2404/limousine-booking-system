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
        Schema::table('bookings', function (Blueprint $table) {
            $table->bigInteger('no_of_seats_required')->nullable()->after('child_seat_required');
            $table->enum('child_1_age', ['<1 yo', '1 yo', '2 yo', '3 yo', '4 yo', '5 yo', '6 yo'])->nullable()->after('no_of_seats_required');
            $table->enum('child_2_age', ['<1 yo', '1 yo', '2 yo', '3 yo', '4 yo', '5 yo', '6 yo'])->nullable()->after('child_1_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['no_of_seats_required', 'child_1_age', 'child_2_age']);
        });
    }
};
