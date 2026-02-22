<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Boundary;
use App\Models\Category;
use App\Models\Culture;
use App\Models\Event;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Legend;
use App\Models\Place;
use App\Models\Post;
use App\Repositories\Contracts\PlaceRepositoryInterface;
use App\Services\GeoJsonService;
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
    
    protected $geoJsonService;

    public function __construct(
        PlaceRepositoryInterface $placeRepository,
        GeoJsonService $geoJsonService
    ) {
        $this->placeRepository = $placeRepository;
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
        $countKuliner = Culture::where('category', 'Kuliner Khas')->count();

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
        $cultures = Culture::where('category', '!=', 'Kuliner Khas')->latest()->take(5)->get();
        $culinaries = Culture::where('category', 'Kuliner Khas')->get();

        // Upcoming Events
        $upcomingEvents = Event::where('is_published', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $upcomingEvent = $upcomingEvents->first();
        $nextEvents = $upcomingEvents->skip(1);

        $heroSetting = \App\Models\HeroSetting::first();
        if (!$heroSetting) {
            $heroSetting = new \App\Models\HeroSetting([
                'type' => 'map',
                'badge_id' => 'Portal Resmi Pariwisata',
                'badge_en' => 'Official Tourism Portal',
                'title_id' => 'Jelajah Jepara. <br> Ukir Ceritamu Di Sini',
                'title_en' => 'Explore Jepara. <br> Carve Your Story Here',
                'subtitle_id' => 'Temukan pesona pantai tropis, kekayaan sejarah, dan mahakarya ukiran kayu kelas dunia.',
                'subtitle_en' => 'Discover tropical beaches, rich history, and world-class wood carving masterpieces.',
                'button_text_id' => 'Mulai Jelajah',
                'button_text_en' => 'Start Exploring',
                'button_link' => '#explore'
            ]);
        }

        // Active Announcements untuk popup welcome (maks 4)
        $announcements = Announcement::active()->latest()->take(4)->get();

        // Historical Legends
        $legends = Legend::where('is_active', true)->orderBy('order')->get();

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
            'nextEvents',
            'heroSetting',
            'announcements',
            'legends'
        ));
    }

    public function showCulture(string $slug): View
    {
        $culture = Culture::with(['images', 'locations'])->where('slug', $slug)->firstOrFail();
        
        if ($culture->category === 'Kuliner Khas') {
            $culinary = $culture;
            return view('public.culinary.show', compact('culinary'));
        }

        return view('public.culture.show', compact('culture'));
    }


    public function showCulinary(string $slug): View
    {
        // Culinary is also in Culture table now
        $culinary = Culture::with(['images', 'locations'])->where('slug', $slug)->firstOrFail();
        return view('public.culinary.show', compact('culinary'));
    }

    public function culture(): View
    {
        $cultures = Culture::all();
        
        $databaseCategories = Culture::select('category')
            ->distinct()
            ->pluck('category')
            ->filter(fn($cat) => !str_contains($cat, 'Tarian'))
            ->toArray();

        $curatedCategories = [
            'Kemahiran & Kerajinan Tradisional (Kriya)' => [
                'id' => 'Kemahiran & Kerajinan Tradisional (Kriya)',
                'title' => 'Kemahiran & Kerajinan Tradisional (Kriya)', 
                'subtitle' => 'Kriya',
                'description' => 'Jepara is known as the World Carving Center, featuring exquisite wood carving, Troso weaving, and batik.',
                'image' => asset('images/culture/ukir.jpg')
            ],
            'Adat Istiadat, Ritus, & Perayaan Tradisional' => [
                'id' => 'Adat Istiadat, Ritus, & Perayaan Tradisional',
                'title' => 'Adat Istiadat, Ritus, & Perayaan Tradisional',
                'subtitle' => 'Tradisi',
                'description' => 'Sacred traditions like Perang Obor and Pesta Lomban that celebrate the gratitude and history of Jepara.',
                'image' => asset('images/culture/obor.png')
            ],
            'Seni Pertunjukan' => [
                'id' => 'Seni Pertunjukan',
                'title' => 'Seni Pertunjukan',
                'subtitle' => 'Seni',
                'description' => 'Experience the rhythm of Wayang Kulit, Kridhajati Dance, and the graceful movements of local arts.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuByC0plW4kR_o3v4HYNa2r2JTW_CZ4SqENWKfWjQKnwCW8gPPQOpS2euCZuK2OeaH8SFfMje5m8x607ts6J8tZ42M2egKoBZTvB5clgNfHI5xXHqUtxtzoD10NZ3hyL9-pRo4f0VHA-HuDIJ4NhiN5nuu6Kw9KPyJTxnKYc4xGSBqWrEQtl9SMLfOGt81e8wCupxUP5mG3AHEQiOj0tgP8DQKYU30VyXmT50XUYr7I_IV3EzciVPLhNkG6oCYU44ENsU_B8-yM9MA'
            ],
            'Kuliner Khas' => [
                'id' => 'Kuliner Khas',
                'title' => 'Kuliner Khas Jepara',
                'subtitle' => 'Kuliner',
                'description' => 'A journey through spice and tradition, exploring the diverse flavors of Jepara\'s legendary food.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAqBziuaRPIVdzVy6lqfQSsB1vBb-GOIriqfJv68H5uzzLAUP6poD5XP4FGglTwJaX3LPkeAVYOSVyEyjkH1Ci_b2WRORruNdhL1ugHYJ1HpMiTw2OjZYcC6UhsS1RyjaQLtpJOcndXvtAZiRea90NTMX6cNStTI40Wp2ql9UPdDTvP-MNpdm7kARbT4dh9eaLQM9DLE9TGujgtvbxjSnzbANWVaWMyVdOH60MHeE7J8OYDizNtb2aEGPvBqkX6FaHuR-28zuGNxA'
            ]
        ];

        $categoriesForAlpine = [];
        $categoryOrder = [];

        // First, add curated ones if they exist in DB
        foreach ($curatedCategories as $id => $data) {
            if (in_array($id, $databaseCategories)) {
                $categoriesForAlpine[] = $data;
                $categoryOrder[] = $id;
            }
        }

        // Then, add remaining categories from DB
        foreach ($databaseCategories as $categoryName) {
            if (!in_array($categoryName, $categoryOrder)) {
                $categoriesForAlpine[] = [
                    'id' => $categoryName,
                    'title' => $categoryName,
                    'subtitle' => 'Koleksi',
                    'description' => 'Discover more of Jepara\'s rich cultural diversity in this collection.',
                    'image' => null // Will have fallback in view
                ];
                $categoryOrder[] = $categoryName;
            }
        }

        $groupedCultures = $cultures->groupBy('category');

        return view('public.culture.index', compact('cultures', 'groupedCultures', 'categoryOrder', 'categoriesForAlpine'));
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

        // 2. Statistics: Post Stats (always shown - 3 cards)
        $stats = [
            'total_views' => $post->visits()->count(),
            'views_today' => $post->visits()->whereDate('created_at', today())->count(),
            'unique_visitors' => $post->visits()->distinct('ip_address')->count('ip_address'),
        ];

        // 3. Dynamic stat widgets (based on admin selection)
        $statWidgets = [];
        if (!empty($post->stat_widgets)) {
            $postStatService = app(\App\Services\PostStatService::class);
            $statWidgets = $postStatService->getWidgetsForPost($post);
        }

        return view('public.posts.show', compact('post', 'relatedPosts', 'recommendedPlaces', 'stats', 'statWidgets'));
    }



    public function places(): View
    {
        $categories = Category::withCount('places')->get();
        $places = Place::with('category')->latest()->get();

        return view('public.places.index', compact('places', 'categories'));
    }



    public function showPlace(Place $place)
    {
        if ($place->is_flagship) {
            return app(\App\Http\Controllers\Public\FlagshipController::class)->show($place);
        }
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
        $cultures = Culture::where('category', '!=', 'Kuliner Khas')
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => Str::limit($item->description, 50),
                    'image_url' => $item->image_url,
                    'type' => 'Budaya',
                    'type_key' => 'culture',
                    'url' => route('culture.show', $item->slug),
                ];
            });

        // Search Culinaries (Kuliner)
        $culinaries = Culture::where('category', 'Kuliner Khas')
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => Str::limit($item->description, 50),
                    'image_url' => $item->image_url,
                    'type' => 'Kuliner',
                    'type_key' => 'culinary',
                    'url' => route('culinary.show', $item->slug),
                ];
            });

        // Merge all results
        $results = $places->merge($posts)->merge($events)->merge($cultures)->merge($culinaries);

        return response()->json($results);
    }
}
