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
        Schema::create('seat_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->date('departure_date');
            $table->string('seat_number'); // Format: A1, B2, C3, D4, etc.
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->string('passenger_name');
            $table->enum('passenger_type', ['adult', 'child', 'toddler']);
            $table->enum('status', ['booked', 'checked_in', 'cancelled'])->default('booked');
            $table->timestamps();
            
            // Unique constraint: one seat per schedule per date
            $table->unique(['schedule_id', 'departure_date', 'seat_number'], 'unique_seat_booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_bookings');
    }
};
