<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tour_packages');
    }

    public function down(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('place_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('duration_days')->default(1);
            $table->integer('duration_nights')->default(0);
            $table->decimal('price_start', 12, 0)->default(0);
            $table->json('inclusions')->nullable();
            $table->json('itinerary')->nullable();
            $table->timestamps();
        });
    }
};
