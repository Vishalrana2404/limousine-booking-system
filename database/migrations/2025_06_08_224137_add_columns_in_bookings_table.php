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
            $table->enum('invoice_generated', ['0', '1'])->default('0')->after('latest_admin_comment');
            $table->unsignedBigInteger('invoice_booking_id')->nullable()->after('invoice_generated');
            $table->foreign('invoice_booking_id')->references('id')->on('invoice_bookings');
            $table->unsignedBigInteger('invoice_generated_by')->nullable()->after('invoice_booking_id');
            $table->foreign('invoice_generated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
