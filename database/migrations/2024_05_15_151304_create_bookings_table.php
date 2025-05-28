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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('service_type_id')->unsigned()->nullable();
            $table->bigInteger('pick_up_location_id')->unsigned()->nullable();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->bigInteger('vehicle_id')->unsigned()->nullable();
            $table->bigInteger('vehicle_type_id')->unsigned()->nullable();
            $table->string('pick_up_location')->nullable();
            $table->string('drop_of_location')->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->string('flight_detail')->nullable();
            $table->bigInteger('no_of_hours')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('total_pax')->nullable();
            $table->bigInteger('total_luggage')->default(0);
            $table->text('client_instructions')->nullable();

            $table->text('driver_remark')->nullable();
            $table->text('internal_remark')->nullable();
            $table->text('dispatch')->nullable();


            $table->text('guest_name')->nullable();
            $table->boolean('is_cross_border')->default(false);
            $table->enum('status', ['ACCEPTED', 'PENDING', 'COMPLETED'])->default('PENDING');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('updated_by_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Define foreign key constraints
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('set null');
            $table->foreign('pick_up_location_id')->references('id')->on('pickup_locations')->onDelete('set null');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_classes')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
