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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Rename the fields here
            $table->renameColumn('table_name', 'model_name');
            $table->renameColumn('user_agent', 'user_device');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Revert the field names back if needed
            $table->renameColumn('model_name', 'table_name');
            $table->renameColumn('user_device', 'user_agent');
        });
    }
};
