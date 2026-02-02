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
        Schema::table('places', function (Blueprint $table) {
            $table->text('ticket_price')->nullable()->change();
            $table->text('opening_hours')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to not null might fail if there are nulls, but we'll try to revert state if needed.
        // Usually we don't strictly revert nullability in down unless critical.
        // We will just leave it as is or try to set nullable false.
        Schema::table('places', function (Blueprint $table) {
            $table->text('ticket_price')->nullable(false)->change();
            $table->text('opening_hours')->nullable(false)->change();
        });
    }
};
