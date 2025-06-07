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
        Schema::create('bookings_additional_stops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->enum('destination_sequence', ['Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth',
                'Eleventh', 'Twelfth', 'Thirteenth', 'Fourteenth', 'Fifteenth', 'Sixteenth', 'Seventeenth', 'Eighteenth', 'Nineteenth', 'Twentieth',
                'Twenty-first', 'Twenty-second', 'Twenty-third', 'Twenty-fourth', 'Twenty-fifth', 'Twenty-sixth', 'Twenty-seventh', 'Twenty-eighth', 'Twenty-ninth', 'Thirtieth',
                'Thirty-first', 'Thirty-second', 'Thirty-third', 'Thirty-fourth', 'Thirty-fifth', 'Thirty-sixth', 'Thirty-seventh', 'Thirty-eighth', 'Thirty-ninth', 'Fortieth',
                'Forty-first', 'Forty-second', 'Forty-third', 'Forty-fourth', 'Forty-fifth', 'Forty-sixth', 'Forty-seventh', 'Forty-eighth', 'Forty-ninth', 'Fiftieth'])->default('Second');
            $table->string('additional_stop_address')->nullable();
            $table->enum('destination_type', ['pickup', 'dropoff'])->default('pickup');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->foreign('updated_by_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings_additional_stops');
    }
};
