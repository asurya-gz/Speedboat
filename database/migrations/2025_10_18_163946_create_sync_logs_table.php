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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type'); // 'sync_from' or 'sync_to'
            $table->string('entity_type'); // 'transaction', 'order', 'product', 'speedboat'
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of transaction/speedboat
            $table->string('status'); // 'success', 'failed', 'pending'
            $table->integer('woocommerce_id')->nullable(); // WooCommerce Order/Product ID
            $table->text('request_data')->nullable(); // JSON data sent to WooCommerce
            $table->text('response_data')->nullable(); // JSON response from WooCommerce
            $table->text('error_message')->nullable(); // Error message if failed
            $table->integer('http_status_code')->nullable(); // HTTP response code
            $table->float('duration_seconds', 8, 3)->nullable(); // How long the sync took
            $table->unsignedBigInteger('triggered_by')->nullable(); // User ID who triggered
            $table->string('trigger_source')->default('auto'); // 'auto', 'manual', 'api'
            $table->timestamps();

            // Indexes for better query performance
            $table->index('sync_type');
            $table->index('entity_type');
            $table->index('status');
            $table->index('created_at');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
