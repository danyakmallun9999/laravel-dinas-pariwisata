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
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->decimal('tax_amount', 10, 2)->default(0)->after('unit_price');
            $table->decimal('app_fee', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('app_fee');
            $table->decimal('refund_amount', 10, 2)->default(0)->after('refund_status');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'tax_amount',
                'app_fee',
                'discount_amount',
                'refund_amount',
                'refunded_at'
            ]);
        });
    }
};
