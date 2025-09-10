<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTableForTicketSelling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add who created the transaction (kasir user)
            $table->unsignedBigInteger('created_by')->nullable()->after('is_synced');
            $table->foreign('created_by')->references('id')->on('users');
            
            // Add notes for transaction
            $table->text('notes')->nullable()->after('created_by');
            
            // Add confirmation details
            $table->timestamp('paid_at')->nullable()->after('notes');
            $table->string('payment_reference')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'created_by',
                'notes',
                'paid_at',
                'payment_reference'
            ]);
        });
    }
}
