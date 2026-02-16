<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SCAN-06: Add scanned_by column to track which operator scanned the ticket.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('scanned_by')->nullable()->after('check_in_time');
            $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropForeign(['scanned_by']);
            $table->dropColumn('scanned_by');
        });
    }
};
