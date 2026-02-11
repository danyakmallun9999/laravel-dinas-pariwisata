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
        Schema::dropIfExists('boundaries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('boundaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('geojson_data');
            $table->string('color')->default('#3388ff');
            $table->timestamps();
        });
    }
};
