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
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELED'])->change();
            $table->text('attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['ACCEPTED', 'PENDING'])->change();
            $table->dropColumn('attachment');
        });
    }
};
