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
        Schema::table('billing_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('billing_agreements', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
                $table->bigInteger('hotel_id')->unsigned()->nullable();
                $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_agreements', function (Blueprint $table) {
            //
        });
    }
};
