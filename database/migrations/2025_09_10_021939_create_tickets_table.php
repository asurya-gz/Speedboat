<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->unsignedBigInteger('transaction_id');
            $table->string('passenger_name');
            $table->enum('passenger_type', ['adult', 'child']);
            $table->decimal('price', 10, 2);
            $table->string('qr_code')->nullable();
            $table->enum('status', ['active', 'used', 'cancelled'])->default('active');
            $table->timestamp('boarding_time')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
