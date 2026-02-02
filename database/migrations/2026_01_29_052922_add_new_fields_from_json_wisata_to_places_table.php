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
            $table->string('ownership_status')->nullable()->after('address');
            $table->string('manager')->nullable()->after('ownership_status');
            $table->text('rides')->nullable()->after('description');
            $table->text('facilities')->nullable()->after('rides');
            $table->text('social_media')->nullable()->after('contact_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn(['ownership_status', 'manager', 'rides', 'facilities', 'social_media']);
        });
    }
};
