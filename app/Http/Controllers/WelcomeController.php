<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\Http\JsonResponse;

class WelcomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('places')->get();

        return view('welcome', compact('categories'));
    }

    public function geoJson(): JsonResponse
    {
        $features = Place::with('category')
            ->get()
            ->map(function (Place $place) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $place->id,
                        'name' => $place->name,
                        'description' => $place->description,
                        'image_url' => $place->image_path ? asset($place->image_path) : null,
                        'category' => [
                            'id' => $place->category?->id,
                            'name' => $place->category?->name,
                            'color' => $place->category?->color,
                            'icon_class' => $place->category?->icon_class,
                        ],
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

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function boundariesGeoJson(): JsonResponse
    {
        $features = Boundary::all()
            ->map(function (Boundary $boundary) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $boundary->id,
                        'name' => $boundary->name,
                        'type' => $boundary->type,
                        'description' => $boundary->description,
                        'area_hectares' => $boundary->area_hectares,
                    ],
                    'geometry' => $boundary->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function infrastructuresGeoJson(): JsonResponse
    {
        $features = Infrastructure::with('category')
            ->get()
            ->map(function (Infrastructure $infrastructure) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $infrastructure->id,
                        'name' => $infrastructure->name,
                        'type' => $infrastructure->type,
                        'length_meters' => $infrastructure->length_meters,
                        'width_meters' => $infrastructure->width_meters,
                        'condition' => $infrastructure->condition,
                        'description' => $infrastructure->description,
                        'category' => $infrastructure->category ? [
                            'id' => $infrastructure->category->id,
                            'name' => $infrastructure->category->name,
                            'color' => $infrastructure->category->color,
                        ] : null,
                    ],
                    'geometry' => $infrastructure->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function landUsesGeoJson(): JsonResponse
    {
        $features = LandUse::all()
            ->map(function (LandUse $landUse) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $landUse->id,
                        'name' => $landUse->name,
                        'type' => $landUse->type,
                        'area_hectares' => $landUse->area_hectares,
                        'owner' => $landUse->owner,
                        'description' => $landUse->description,
                    ],
                    'geometry' => $landUse->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function exploreMap()
    {
        $categories = Category::withCount('places')->get();
        $places = Place::with('category')->latest()->take(10)->get();
        $boundaries = Boundary::all();
        $infrastructures = Infrastructure::all();
        $landUses = LandUse::all();

        return view('explore-map', compact('categories', 'places', 'boundaries', 'infrastructures', 'landUses'));
    }
}
