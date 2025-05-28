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
        Schema::create('peak_periods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peak_periods');
    }
};
