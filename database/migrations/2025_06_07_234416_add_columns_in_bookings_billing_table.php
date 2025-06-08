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
            $table->enum('is_discount', ['0', '1'])->default('0')->after('misc_charge_description');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->after('is_discount');
            $table->float('discount_value', 8, 2)->nullable()->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_billings', function (Blueprint $table) {
            $table->dropColumn(['is_discount', 'discount_type', 'discount_value']);
        });
    }
};
