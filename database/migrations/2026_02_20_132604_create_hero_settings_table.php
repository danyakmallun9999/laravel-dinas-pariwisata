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
        Schema::create('hero_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['map', 'video', 'image'])->default('map');
            $table->json('media_paths')->nullable();
            $table->text('title_id')->nullable();
            $table->text('title_en')->nullable();
            $table->text('subtitle_id')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->string('badge_id')->nullable();
            $table->string('badge_en')->nullable();
            $table->string('button_text_id')->nullable();
            $table->string('button_text_en')->nullable();
            $table->string('button_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_settings');
    }
};
