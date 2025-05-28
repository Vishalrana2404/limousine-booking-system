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
            $table->enum('to_be_advised_status', ['yes', 'no'])->default('no')->after('pickup_time');
            $table->time('to_be_advised_time')->nullable()->after('to_be_advised_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['to_be_advised_status', 'to_be_advised_time']);
        });
    }
};
