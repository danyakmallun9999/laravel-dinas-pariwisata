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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('ticket_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Actor (Admin or Customer)
            $table->enum('type', ['payment', 'refund', 'adjustment', 'fee'])->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending')->index();
            $table->string('payment_method')->nullable();
            $table->string('external_reference')->nullable()->index(); // Xendit ID
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('transacted_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
