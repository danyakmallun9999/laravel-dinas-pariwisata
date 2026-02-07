<?php

namespace App\Services;

use App\Models\Boundary;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;

class GeoJsonService
{
    /**
     * Convert Places to GeoJSON FeatureCollection.
     */
    public function getPlacesGeoJson(Collection $places): array
    {
        $features = $places->map(function (Place $place) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $place->id,
                    'name' => $place->name,
                    'description' => $place->description,
                    'image_url' => $place->image_path ? asset($place->image_path) : null,
                    'ticket_price' => $place->ticket_price,
                    'opening_hours' => $place->opening_hours,
                    'contact_info' => $place->contact_info,
                    'rating' => $place->rating,
                    'website' => $place->website,
                    'category' => [
                        'id' => $place->category?->id,
                        'name' => $place->category?->name,
                        'color' => $place->category?->color,
                        'icon_class' => $place->category?->icon_class,
                    ],
                    'address' => $place->address,
                    'google_maps_link' => $place->google_maps_link,
                    'notes' => $place->notes,
                    'slug' => $place->slug,
                    'images' => $place->images->map(fn($img) => asset($img->image_path))->toArray(),
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float) $place->longitude,
                        (float) $place->latitude,
                    ],
                ],
            ];
        });

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Convert Boundaries to GeoJSON.
     */
    public function getBoundariesGeoJson(Collection $boundaries): array
    {
        $features = $boundaries->map(function (Boundary $boundary) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $boundary->id,
                    'name' => $boundary->name,
                    'type' => $boundary->type,
                    'description' => $boundary->description,
                    'area_hectares' => $boundary->area_hectares,
                ],
                'geometry' => $boundary->geometry, // Assumed to be already in array/json format or casted
            ];
        });

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    // Similar methods for Infrastructure and LandUse if needed, keeping it DRY
}
