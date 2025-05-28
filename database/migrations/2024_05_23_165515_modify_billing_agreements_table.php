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
            // Rename column from client_id to hotel_id
            // $table->renameColumn('client_id', 'hotel_id');

            // Drop foreign key constraint for client_id
            // $table->dropForeign(['client_id']);

            // Add foreign key constraint for hotel_id, with nullable
            // $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_agreements', function (Blueprint $table) {
            // Drop foreign key constraint for hotel_id
            $table->dropForeign(['hotel_id']);

            // Rename column from hotel_id to client_id
            $table->renameColumn('hotel_id', 'client_id');

            // Add foreign key constraint for client_id, onDelete cascade
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
