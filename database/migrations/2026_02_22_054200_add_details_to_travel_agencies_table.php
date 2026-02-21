<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_agencies', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('name');
            $table->string('business_type')->nullable()->after('owner_name');
            $table->string('nib')->nullable()->after('business_type');
            $table->string('address')->nullable()->after('nib');
        });
    }

    public function down(): void
    {
        Schema::table('travel_agencies', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'business_type', 'nib', 'address']);
        });
    }
};
