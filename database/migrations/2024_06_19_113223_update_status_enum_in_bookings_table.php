<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Change the enum definition to include 'CANCELLED' instead of 'CANCELED'
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELLED','CANCELED', 'SCHEDULED'])
                ->default('PENDING')
                ->change();
        });
        // Update existing rows to the new spelling
        DB::table('bookings')->where('status', 'CANCELED')->update(['status' => 'CANCELLED']);

        Schema::table('bookings', function (Blueprint $table) {
            // Change the enum definition to include 'CANCELLED' instead of 'CANCELED'
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELLED', 'SCHEDULED'])
                ->default('PENDING')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert the enum definition back to the original state
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED', 'CANCELED', 'SCHEDULED'])
                ->default('PENDING')
                ->change();
        });

        // Revert the updated rows back to the old spelling
        DB::table('bookings')->where('status', 'CANCELLED')->update(['status' => 'CANCELED']);
    }
};
