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
            $table->integer('rows')->default(5)->comment('Jumlah baris kursi ke belakang');
            $table->integer('columns')->default(4)->comment('Jumlah kursi per baris (menyamping)');
            $table->json('seat_numbers')->nullable()->comment('Custom nomor kursi untuk setiap posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['rows', 'columns', 'seat_numbers']);
        });
    }
};
