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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('hotel');
            $table->dropColumn('name');
            $table->bigInteger('hotel_id')->unsigned()->nullable()->after('user_id');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropColumn('hotel_id');
            $table->string('hotel', 250)->nullable()->after('event');
            $table->string('name', 50)->nullable()->after('user_id');
        });
    }
};
