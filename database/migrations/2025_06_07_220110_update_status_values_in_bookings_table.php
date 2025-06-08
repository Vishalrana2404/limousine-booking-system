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
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM(
                'ACCEPTED', 
                'PENDING', 
                'COMPLETED', 
                'CANCELLED', 
                'SCHEDULED', 
                'CANCELLED WITH CHARGES'
            ) DEFAULT 'PENDING'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM(
                'ACCEPTED', 
                'PENDING', 
                'COMPLETED', 
                'CANCELLED', 
                'SCHEDULED'
            ) DEFAULT 'PENDING'
        ");
    }
};
