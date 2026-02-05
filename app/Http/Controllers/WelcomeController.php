<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Event;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use App\Models\Post;
use App\Repositories\Contracts\PlaceRepositoryInterface;
use App\Services\GeoJsonService;
use App\Services\StaticDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    protected $placeRepository;

    protected $staticDataService;

    protected $geoJsonService;

    public function __construct(
        PlaceRepositoryInterface $placeRepository,
        StaticDataService $staticDataService,
        GeoJsonService $geoJsonService
    ) {
        $this->placeRepository = $placeRepository;
        $this->staticDataService = $staticDataService;
        $this->geoJsonService = $geoJsonService;
    }

    public function index(): View
    {
        $categories = Category::withCount('places')->get();

        // Detailed Statistics
        $totalPlaces = Place::count();

        // Destinasi: Exclude Kuliner/Hotel to get true "Tourist Spots"
        $countDestinasi = Place::whereHas('category', function ($q) {
            $q->whereNotIn('name', ['Kuliner', 'Hotel', 'Penginapan', 'Hotel & Penginapan']);
        })->count();

        // Kuliner Count
        $countKuliner = $this->staticDataService->getCulinaries()->count();

        // Event Count
        $countEvent = Event::whereYear('start_date', 2026)->count();

        // Desa Wisata / Wilayah
        $countDesa = Boundary::count();

        $totalCategories = $categories->count();
        $totalBoundaries = Boundary::count();
        $totalArea = Boundary::sum('area_hectares');
        $lastUpdate = Place::latest('updated_at')->first()?->updated_at;

        // Fetch Data via Repository/Models
        $places = $this->placeRepository->getPopular(6);
        $posts = Post::where('is_published', true)->latest('published_at')->take(3)->get();

        // Fetch Static Data
        $cultures = $this->staticDataService->getCultures();
        $culinaries = $this->staticDataService->getCulinaries();

        return view('public.home.welcome', compact(
            'categories',
            'totalPlaces',
            'countDestinasi',
            'countKuliner',
            'countEvent',
            'countDesa',
            'totalCategories',
            'totalBoundaries',
            'totalArea',
            'lastUpdate',
            'places',
            'posts',
            'cultures',
            'culinaries'
        ));
    }

    public function showCulture(string $slug): View
    {
        $culture = $this->staticDataService->getCultures()->firstWhere('slug', $slug);

        if (! $culture) {
            abort(404);
        }

        return view('public.culture.show', compact('culture'));
    }

    public function showCulinary(string $slug): View
    {
        $culinary = $this->staticDataService->getCulinaries()->firstWhere('slug', $slug);

        if (! $culinary) {
            abort(404);
        }

        return view('public.culinary.show', compact('culinary'));
    }

    public function geoJson(): JsonResponse
    {
        $places = Place::with('category')->get();

        return response()->json($this->geoJsonService->getPlacesGeoJson($places));
    }

    public function boundariesGeoJson(): JsonResponse
    {
        $boundaries = Boundary::all();

        return response()->json($this->geoJsonService->getBoundariesGeoJson($boundaries));
    }

    public function infrastructuresGeoJson(): JsonResponse
    {
        $infrastructures = Infrastructure::all();

        // Inline transformation for now as it's specific, or move to Service if reused
        $features = $infrastructures->map(function (Infrastructure $infrastructure) {
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
        $landUses = LandUse::all();

        $features = $landUses->map(function (LandUse $landUse) {
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

    public function exploreMap(): View
    {
        $categories = Category::withCount('places')->get();
        $totalPlaces = Place::count();
        $totalBoundaries = Boundary::count();

        return view('public.explore-map', compact(
            'categories',
            'totalPlaces',
            'totalBoundaries'
        ));
    }

    public function posts(): View
    {
        $featuredPost = Post::where('is_published', true)
            ->latest('published_at')
            ->first();

        $posts = Post::where('is_published', true)
            ->where('id', '!=', $featuredPost?->id)
            ->latest('published_at')
            ->paginate(9);

        return view('public.posts.index', compact('featuredPost', 'posts'));
    }

    public function showPost(Post $post): View
    {
        if (! $post->is_published) {
            abort(404);
        }

        $relatedPosts = Post::where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $recommendedPlaces = $this->placeRepository->getPopular(3);

        return view('public.posts.show', compact('post', 'relatedPosts', 'recommendedPlaces'));
    }

    public function places(): View
    {
        $categories = Category::withCount('places')->get();
        $places = Place::with('category')->latest()->get();

        return view('public.places.index', compact('places', 'categories'));
    }



    public function showPlace(Place $place): View
    {
        return view('public.places.show', compact('place'));
    }

    public function searchPlaces(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (! $query) {
            return response()->json([]);
        }

        // Search Places
        $places = $this->placeRepository->searchByName($query, 3)
            ->map(function ($place) {
                return [
                    'id' => $place->id,
                    'name' => $place->name,
                    'description' => Str::limit($place->description, 50),
                    'image_url' => $place->image_path ? asset($place->image_path) : null,
                    'type' => 'Destinasi',
                    'url' => route('places.show', $place->slug),
                    // For map features
                    'slug' => $place->slug, 
                ];
            });

        // Search Posts (Berita)
        $posts = Post::where('is_published', true)
            ->where('title', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'name' => $post->title,
                    'description' => Str::limit(strip_tags($post->content ?? ''), 50),
                    'image_url' => $post->image_path ? asset($post->image_path) : null,
                    'type' => 'Berita',
                    'url' => route('posts.show', $post->slug),
                ];
            });

        // Search Events (Agenda)
        $events = Event::where('is_published', true)
            ->where('title', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->title,
                    'description' => Str::limit($event->description, 50),
                    'image_url' => $event->image ? asset($event->image) : null,
                    'type' => 'Agenda',
                    'url' => route('events.public.show', $event->slug),
                ];
            });

        // Search Cultures (Budaya)
        $cultures = $this->staticDataService->getCultures()
            ->filter(function ($item) use ($query) {
                return Str::contains(strtolower($item->name), strtolower($query));
            })
            ->take(3)
            ->map(function ($item) {
                return [
                    'id' => $item->slug,
                    'name' => $item->name,
                    'description' => Str::limit($item->description, 50),
                    'image_url' => asset($item->image),
                    'type' => 'Budaya',
                    'url' => route('culture.show', $item->slug),
                ];
            })->values();

        // Search Culinaries (Kuliner)
        $culinaries = $this->staticDataService->getCulinaries()
            ->filter(function ($item) use ($query) {
                return Str::contains(strtolower($item->name), strtolower($query));
            })
            ->take(3)
            ->map(function ($item) {
                return [
                    'id' => $item->slug,
                    'name' => $item->name,
                    'description' => Str::limit($item->description, 50),
                    'image_url' => asset($item->image),
                    'type' => 'Kuliner',
                    'url' => route('culinary.show', $item->slug),
                ];
            })->values();

        // Merge all results
        $results = $places->merge($posts)->merge($events)->merge($cultures)->merge($culinaries);

        return response()->json($results);
    }
}
