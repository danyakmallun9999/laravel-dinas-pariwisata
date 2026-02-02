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
        
        // Detailed Statistics
        $totalPlaces = Place::count();
        
        // Destinasi: Exclude Kuliner/Hotel to get true "Tourist Spots"
        $countDestinasi = Place::whereHas('category', function($q) {
            $q->whereNotIn('name', ['Kuliner', 'Hotel', 'Penginapan', 'Hotel & Penginapan']);
        })->count();

        // Kuliner Count
        $countKuliner = $categories->first(fn($c) => \Illuminate\Support\Str::contains($c->name, 'Kuliner', true))?->places_count ?? 0;

        // Event Count
        $countEvent = \App\Models\Event::count();

        // Desa Wisata / Wilayah
        $countDesa = Boundary::count();

        $totalCategories = $categories->count();
        $totalBoundaries = Boundary::count(); // Represents Dukuh/Wilayah count
        $totalArea = Boundary::sum('area_hectares');
        // $totalInfrastructures = Infrastructure::count();
        // $totalLandUses = LandUse::count();
        $lastUpdate = Place::latest('updated_at')->first()?->updated_at;
        // $population = \App\Models\Population::first();
        $places = \App\Models\Place::with('category')
            ->whereDoesntHave('category', function ($query) {
                $query->whereIn('slug', ['wisata-kuliner', 'kebudayaan']);
            })
            ->latest()
            ->take(6)
            ->get();

        $cultures = \App\Models\Place::with('category')
            ->whereHas('category', function ($query) {
                $query->where('slug', 'kebudayaan');
            })
            ->latest()
            ->get();

        $posts = \App\Models\Post::where('is_published', true)->latest('published_at')->take(3)->get();

        return view('welcome', compact(
            'categories', 
            'totalPlaces',
            'countDestinasi',
            'countKuliner',
            'countEvent',
            'countDesa',
            'totalCategories', 
            'totalBoundaries', 
            'totalArea',
            // 'totalInfrastructures', 
            // 'totalLandUses', 
            'lastUpdate', 
            // 'population',
            'places',
            'posts',
            'cultures'
        ));
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
        $features = Infrastructure::all()
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
        $totalPlaces = Place::count();
        $totalBoundaries = Boundary::count();
        // $totalInfrastructures = Infrastructure::count();
        // $totalLandUses = LandUse::count();

        return view('explore-map', compact(
            'categories', 
            'totalPlaces', 
            'totalBoundaries', 
            // 'totalInfrastructures', 
            // 'totalLandUses'
        ));
    }

    public function posts()
    {
        $featuredPost = \App\Models\Post::where('is_published', true)
            ->latest('published_at')
            ->first();

        $posts = \App\Models\Post::where('is_published', true)
            ->where('id', '!=', $featuredPost?->id)
            ->latest('published_at')
            ->paginate(9);

        return view('public.posts.index', compact('featuredPost', 'posts'));
    }

    public function showPost(\App\Models\Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        $relatedPosts = \App\Models\Post::where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $recommendedPlaces = \App\Models\Place::inRandomOrder()
            ->take(3)
            ->get();

        return view('public.posts.show', compact('post', 'relatedPosts', 'recommendedPlaces'));
    }

    public function places()
    {
        $categories = \App\Models\Category::withCount('places')->get();
        $places = \App\Models\Place::with('category')
            ->whereDoesntHave('category', function ($query) {
                $query->whereIn('slug', ['wisata-kuliner', 'kebudayaan']);
            })
            ->latest()
            ->get();

        return view('public.places.index', compact('places', 'categories'));
    }

    public function showProduct(\App\Models\Product $product)
    {
        return view('public.products.show', compact('product'));
    }

    public function showPlace(\App\Models\Place $place)
    {
        return view('public.places.show', compact('place'));
    }

<<<<<<< HEAD
    public function showCulinary(\App\Models\Place $place)
    {
        return view('public.culinary.show', compact('place'));
    }

    public function showCulture(\App\Models\Place $place)
    {
        return view('public.culture.show', compact('place'));
=======
    public function searchPlaces(\Illuminate\Http\Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $places = Place::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'slug', 'description', 'image_path')
            ->take(5)
            ->get()
            ->map(function ($place) {
                return [
                    'id' => $place->id,
                    'name' => $place->name,
                    'slug' => $place->slug,
                    'description' => \Illuminate\Support\Str::limit($place->description, 50),
                    'image_url' => $place->image_path ? asset($place->image_path) : null,
                    'type' => 'Lokasi'
                ];
            });

        return response()->json($places);
>>>>>>> e68d8cfc47f65f548f6127d4eda4db82992c7dfb
    }
}
