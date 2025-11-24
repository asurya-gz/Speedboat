<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing balita records to have null seat_number
        DB::statement('UPDATE seat_bookings SET seat_number = NULL WHERE passenger_type = "toddler"');
        DB::statement('UPDATE tickets SET seat_number = NULL WHERE passenger_type = "toddler"');

        // Then alter the column to allow null
        DB::statement('ALTER TABLE seat_bookings MODIFY seat_number VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot safely reverse this as we can't restore seat numbers for toddlers
        DB::statement('ALTER TABLE seat_bookings MODIFY seat_number VARCHAR(255) NOT NULL');
    }
};
