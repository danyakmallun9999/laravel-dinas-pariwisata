<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Display the interactive map page
     */
    public function index(): View
    {
        $boundaries = Boundary::select('id', 'name', 'type', 'area_hectares', 'description')->get();
        $infrastructures = Infrastructure::with('category')
            ->select('id', 'name', 'type', 'length_meters', 'width_meters', 'condition', 'description', 'category_id')
            ->get();
        $landUses = LandUse::select('id', 'name', 'type', 'area_hectares', 'owner', 'description')->get();
        $places = Place::with('category')
            ->select('id', 'name', 'description', 'latitude', 'longitude', 'category_id', 'image_path')
            ->get()
            ->map(function ($place) {
                return [
                    'id' => $place->id,
                    'name' => $place->name,
                    'description' => $place->description,
                    'latitude' => $place->latitude,
                    'longitude' => $place->longitude,
                    'category' => $place->category ? [
                        'id' => $place->category->id,
                        'name' => $place->category->name,
                        'color' => $place->category->color,
                    ] : null,
                    'image_path' => $place->image_path,
                ];
            });

        // Filter roads from infrastructures
        $roads = $infrastructures->where('type', 'road')->values();

        return view('admin.map.index', compact(
            'boundaries',
            'infrastructures',
            'landUses',
            'roads',
            'places'
        ));
    }
}
