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
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->timestamp('check_in_time')->nullable()->after('status');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity'); // Price at time of booking
            $table->string('customer_city')->nullable()->after('customer_phone');
            $table->string('payment_gateway_ref')->nullable()->after('payment_method');
            $table->enum('refund_status', ['requested', 'processed', 'rejected'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_time',
                'unit_price',
                'customer_city',
                'payment_gateway_ref',
                'refund_status'
            ]);
        });
    }
};
