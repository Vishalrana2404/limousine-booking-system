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
        Schema::table('hotels', function (Blueprint $table) {
            $table->enum('is_head_office', ['1', '0'])->default('0')->after('name');
            $table->unsignedBigInteger('linked_head_office')->nullable()->after('is_head_office');
            $table->foreign('linked_head_office')->references('id')->on('hotels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['linked_head_office']);
            $table->dropColumn(['is_head_office', 'linked_head_office']);
        });
    }
};
