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
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELED', 'SCHEDULED'])->default('PENDING')->change();
            $table->dateTime('trip_ended')->nullable();
            $table->dateTime('departure_time')->nullable()->change();
            $table->string('driver_contact')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELED'])->change();
            $table->dropColumn('trip_ended');
            $table->time('departure_time')->nullable()->change();
            $table->dropColumn('driver_contact');
        });
    }
};
