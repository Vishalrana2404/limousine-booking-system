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
        Schema::table('users', function (Blueprint $table) {
            // Change the existing column type
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change the existing column type back to string
            $table->string('status', 20)->collation('utf8mb4_unicode_ci')->change();
        });
    }
};
