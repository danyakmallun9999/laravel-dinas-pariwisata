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
        Schema::create('land_uses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // settlement, rice_field, plantation, forest, etc
            $table->json('geometry'); // GeoJSON format for polygon
            $table->decimal('area_hectares', 10, 4)->nullable();
            $table->string('owner')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_uses');
    }
};
