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
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->string('department', 100)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('profile_image')->nullable();

            $table->foreign('user_type_id')->references('id')->on('user_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('user_type_id');
            $table->dropColumn('department');
            $table->dropColumn('status');
            $table->dropColumn('phone');
            $table->dropColumn('profile_image');
        });
    }
};
