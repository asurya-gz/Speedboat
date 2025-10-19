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
        // Add WooCommerce integration fields to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('woocommerce_order_id')->nullable()->after('id');
            $table->timestamp('synced_at')->nullable()->after('is_synced');
            $table->string('sync_error')->nullable()->after('synced_at');
            $table->index('woocommerce_order_id');
        });

        // Add WooCommerce integration fields to tickets table
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('woocommerce_line_item_id')->nullable()->after('id');
            $table->timestamp('synced_at')->nullable()->after('is_synced');
            $table->index('woocommerce_line_item_id');
        });

        // Add WooCommerce product mapping to speedboats table
        Schema::table('speedboats', function (Blueprint $table) {
            $table->unsignedBigInteger('woocommerce_product_id')->nullable()->after('code');
            $table->string('woocommerce_bus_id')->nullable()->after('woocommerce_product_id');
            $table->index('woocommerce_product_id');
        });

        // Create sync_queue table for offline transactions
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('syncable_type'); // Transaction or Ticket
            $table->unsignedBigInteger('syncable_id');
            $table->enum('direction', ['to_woocommerce', 'from_woocommerce']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamps();

            $table->index(['syncable_type', 'syncable_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['woocommerce_order_id']);
            $table->dropColumn(['woocommerce_order_id', 'synced_at', 'sync_error']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['woocommerce_line_item_id']);
            $table->dropColumn(['woocommerce_line_item_id', 'synced_at']);
        });

        Schema::table('speedboats', function (Blueprint $table) {
            $table->dropIndex(['woocommerce_product_id']);
            $table->dropColumn(['woocommerce_product_id', 'woocommerce_bus_id']);
        });

        Schema::dropIfExists('sync_queue');
    }
};
