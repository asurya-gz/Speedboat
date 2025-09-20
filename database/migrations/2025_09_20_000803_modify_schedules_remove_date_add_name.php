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
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('name')->after('destination_id');
            $table->dropColumn('departure_date');
            $table->dropColumn('available_seats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->date('departure_date')->after('destination_id');
            $table->integer('available_seats')->after('capacity');
        });
    }
};
