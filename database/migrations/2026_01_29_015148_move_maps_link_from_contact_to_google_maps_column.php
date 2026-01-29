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
        $places = \App\Models\Place::all();
        foreach ($places as $place) {
            // Check if contact_info looks like a URL or map link
            if ($place->contact_info && (str_contains($place->contact_info, 'http') || str_contains($place->contact_info, 'goo.gl') || str_contains($place->contact_info, 'maps'))) {
                // If google_maps_link is empty, move it there
                if (empty($place->google_maps_link)) {
                    $place->google_maps_link = $place->contact_info;
                    $place->contact_info = null;
                    $place->save();
                } else {
                    // If google_maps_link already exists, just clear the contact_info if it's identical
                    if ($place->google_maps_link === $place->contact_info) {
                        $place->contact_info = null;
                        $place->save();
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            //
        });
    }
};
