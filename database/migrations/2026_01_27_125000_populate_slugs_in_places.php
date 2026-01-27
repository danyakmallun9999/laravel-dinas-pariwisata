<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Place;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $places = Place::whereNull('slug')->orWhere('slug', '')->get();

        foreach ($places as $place) {
            $slug = Str::slug($place->name);
            
            // Ensure uniqueness
            $count = Place::where('slug', $slug)->where('id', '!=', $place->id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $place->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse data population
    }
};
