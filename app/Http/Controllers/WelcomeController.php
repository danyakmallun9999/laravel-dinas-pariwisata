<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Culture;
use App\Models\Event;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use App\Models\Post;
use App\Repositories\Contracts\PlaceRepositoryInterface;
use App\Services\GeoJsonService;
use App\Services\StaticDataService;
use App\Models\TourismStat;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $countEvent = Event::count();

        // Desa Wisata / Wilayah
        $countDesa = Boundary::count();

        $totalCategories = $categories->count();
        $totalBoundaries = Boundary::count();
        $totalArea = Boundary::sum('area_hectares');
        $lastUpdate = Place::latest('updated_at')->first()?->updated_at;

        // Fetch Data via Repository/Models
        $topDestinations = [
            'Taman Nasional Karimunjawa',
            'Pulau Panjang',
            'Pantai Kartini',
            'Pantai Tirta Samudra (Bandengan)',
            'Gua Manik',
            'Jepara Ourland Park'
        ];

        $places = Place::whereIn('name', $topDestinations)
            ->get()
            ->sortBy(function ($place) use ($topDestinations) {
                return array_search($place->name, $topDestinations);
            });
        $posts = Post::where('is_published', true)->latest('published_at')->take(3)->get();

        // Fetch Data from Database (Migrated from Static)
        $cultures = Culture::where('category', '!=', 'Kuliner Khas')->inRandomOrder()->take(5)->get();
        $culinaries = Culture::where('category', 'Kuliner Khas')->get();

        // Upcoming Events
        $upcomingEvents = Event::where('is_published', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $upcomingEvent = $upcomingEvents->first();
        $nextEvents = $upcomingEvents->skip(1);

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
            'culinaries',
            'upcomingEvent',
            'nextEvents'
        ));
    }

    public function showCulture(string $slug): View
    {
        $culture = Culture::where('slug', $slug)->firstOrFail();
        return view('public.culture.show', compact('culture'));
    }

    public function showCulinary(string $slug): View
    {
        // Culinary is also in Culture table now
        $culinary = Culture::where('slug', $slug)->firstOrFail();
        return view('public.culinary.show', compact('culinary'));
    }

    public function culture(): View
    {
        $cultures = Culture::all();
        
        $categoryOrder = [
            'Kemahiran & Kerajinan Tradisional (Kriya)',
            'Adat Istiadat, Ritus, & Perayaan Tradisional',
            'Seni Pertunjukan',
            'Kawasan Cagar Budaya & Sejarah',
            'Kuliner Khas'
        ];

        // Custom order for certain items if needed, or just let them be ordered by DB id/created_at
        // If we want specific items first within category, we might need a 'sort_order' column, but for now just default.
        
        $groupedCultures = $cultures->groupBy('category');

        return view('public.culture.index', compact('cultures', 'groupedCultures', 'categoryOrder'));
    }

    public function geoJson(): JsonResponse
    {
        $places = Place::with(['category', 'images'])->get();

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

        return view('public.explore-map.index', compact(
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

        // 1. Tracking: Record Visit
        Visit::create([
            'post_id' => $post->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // 2. Statistics: Post Stats
        $stats = [
            'total_views' => $post->visits()->count(),
            'views_today' => $post->visits()->whereDate('created_at', today())->count(),
            'unique_visitors' => $post->visits()->distinct('ip_address')->count('ip_address'),
        ];

        // 3. Statistics: Post View Graph (Last 30 Days)
        $viewsGraph = $post->visits()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Statistics: Tourism Stats (Jepara) - Real Data from Ticket Orders
        if (config('features.e_ticket_enabled')) {
            // 4. Statistics: Tourism Stats (Jepara) - Real Data from Ticket Orders
            $currentYear = now()->year;
            
            // Count TOTAL TICKETS SOLD (Paid + Used)
            $totalSold = \App\Models\TicketOrder::whereIn('status', ['paid', 'used'])
                ->whereYear('visit_date', $currentYear)
                ->sum('quantity');

            // Count REAL VISITORS (Used / Checked-in only)
            $totalVisitors = \App\Models\TicketOrder::where('status', 'used')
                ->whereYear('visit_date', $currentYear)
                ->sum('quantity');

            // Monthly data from ticket orders (Using SOLD count for trend)
            $monthlyData = \App\Models\TicketOrder::select(
                    DB::raw('MONTH(visit_date) as month'), 
                    DB::raw('SUM(quantity) as visitors')
                )
                ->whereIn('status', ['paid', 'used'])
                ->whereYear('visit_date', $currentYear)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Fill missing months with 0
            $fullMonthlyData = collect(range(1, 12))->map(function($month) use ($monthlyData) {
                $data = $monthlyData->firstWhere('month', $month);
                return [
                    'month' => $month,
                    'visitors' => $data ? $data->visitors : 0
                ];
            });

            $tourismStats = [
                'total_sold' => $totalSold,
                'total_visitors' => $totalVisitors,
                'monthly_data' => $fullMonthlyData,
                'year' => $currentYear,
            ];
        } else {
            $tourismStats = [
                'total_sold' => 0,
                'total_visitors' => 0,
                'monthly_data' => collect(range(1, 12))->map(fn($m) => ['month' => $m, 'visitors' => 0]),
                'year' => now()->year,
            ];
        }

        return view('public.posts.show', compact('post', 'relatedPosts', 'recommendedPlaces', 'stats', 'viewsGraph', 'tourismStats'));
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
                    'name' => $place->translated_name,
                    'description' => Str::limit($place->translated_description, 50),
                    'image_url' => $place->image_path ? asset($place->image_path) : null,
                    'type' => 'Destinasi',
                    'type_key' => 'location',
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
                    'type_key' => 'news',
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
                    'type_key' => 'event',
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
                    'type_key' => 'culture',
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
                    'type_key' => 'culinary',
                    'url' => route('culinary.show', $item->slug),
                ];
            })->values();

        // Merge all results
        $results = $places->merge($posts)->merge($events)->merge($cultures)->merge($culinaries);

        return response()->json($results);
    }
}
