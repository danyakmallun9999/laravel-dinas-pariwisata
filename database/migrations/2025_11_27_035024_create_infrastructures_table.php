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
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // road, river, irrigation, electricity
            $table->json('geometry'); // GeoJSON format for linestring/polyline
            $table->decimal('length_meters', 12, 2)->nullable();
            $table->decimal('width_meters', 8, 2)->nullable();
            $table->string('condition')->nullable(); // good, fair, poor
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructures');
    }
};
