<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_billings', function (Blueprint $table) {
            $table->bigInteger('sub_total_charge')->nullable()->after('misc_surcharge');
        });

        // Copy existing total_charge values into sub_total_charge
        DB::table('booking_billings')->update([
            'sub_total_charge' => DB::raw('total_charge')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_billings', function (Blueprint $table) {
            $table->dropColumn('sub_total_charge');
        });
    }
};
