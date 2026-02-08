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
        Schema::table('tickets', function (Blueprint $table) {
            $table->decimal('price_weekend', 10, 2)->nullable()->after('price');
            $table->string('type')->default('general')->after('name'); // 'adult', 'child', 'foreigner', 'vehicle', 'general'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['price_weekend', 'type']);
        });
    }
};
