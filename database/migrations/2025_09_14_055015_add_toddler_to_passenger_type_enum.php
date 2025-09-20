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
        // MySQL doesn't support adding enum values directly, so we need to recreate the column
        DB::statement("ALTER TABLE tickets MODIFY COLUMN passenger_type ENUM('adult', 'child', 'toddler')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE tickets MODIFY COLUMN passenger_type ENUM('adult', 'child')");
    }
};
