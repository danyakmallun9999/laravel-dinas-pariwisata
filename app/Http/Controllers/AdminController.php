<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Models\Category;
use App\Models\Place;
use App\Services\DashboardService;
use App\Services\PlaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    protected $dashboardService;

    protected $placeService;

    public function __construct(
        DashboardService $dashboardService,
        PlaceService $placeService
    ) {
        $this->dashboardService = $dashboardService;
        $this->placeService = $placeService;
    }

    public function index(): View
    {
        $places = Place::with('category')->latest()->paginate(10);
        $stats = $this->dashboardService->getDashboardStats();

        return view('admin.dashboard', compact('places', 'stats'));
    }

    public function placesIndex(Request $request): View
    {
        $query = Place::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
                  ->orWhere('address', 'like', '%'.$request->search.'%');
        }

        $places = $query->paginate(10);

        return view('admin.places.index', compact('places'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $place = new Place([
            'latitude' => -6.7289,
            'longitude' => 110.7485,
        ]);

        return view('admin.places.create', compact('categories', 'place'));
    }

    public function store(StorePlaceRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Parse Rides and Facilities
        if (isset($data['rides'])) {
            $data['rides'] = $this->placeService->parseRides($data['rides']);
        }
        if (isset($data['facilities'])) {
            $data['facilities'] = $this->placeService->parseFacilities($data['facilities']);
        }

        // Handle geometry from drawing component
        if ($request->has('geometry') && $request->geometry) {
            $geometry = json_decode($request->geometry, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($geometry['type']) && $geometry['type'] === 'Point') {
                $data['latitude'] = $geometry['coordinates'][1];
                $data['longitude'] = $geometry['coordinates'][0];
            }
        }

        // Generate Slug
        $data['slug'] = $this->placeService->generateSlug($data['name']);

        // Handle Main Image
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->placeService->uploadImage($request->file('image'), 'places');
        }

        // Remove gallery_images from data before creating Place
        // Note: gallery_images are validated in Request but not needed in Place::create
        unset($data['gallery_images']);
        unset($data['image']); // Remove file object

        $place = Place::create($data);

        // Handle Gallery Images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $url = $this->placeService->uploadImage($image, 'place_gallery');

                $place->images()->create([
                    'image_path' => $url,
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Lokasi berhasil ditambahkan.',
                'place' => $place->load('category'),
            ]);
        }

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Place $place): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.places.edit', compact('categories', 'place'));
    }

    public function update(UpdatePlaceRequest $request, Place $place): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Parse Rides and Facilities
        if (isset($data['rides'])) {
            $data['rides'] = $this->placeService->parseRides($data['rides']);
        }
        if (isset($data['facilities'])) {
            $data['facilities'] = $this->placeService->parseFacilities($data['facilities']);
        }

        // Handle geometry
        if ($request->has('geometry') && $request->geometry) {
            $geometry = json_decode($request->geometry, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($geometry['type']) && $geometry['type'] === 'Point') {
                $data['latitude'] = $geometry['coordinates'][1];
                $data['longitude'] = $geometry['coordinates'][0];
            }
        }

        // Update Slug if name changed or slug is missing
        if ($request->name !== $place->name || ! $place->slug) {
            $data['slug'] = $this->placeService->generateSlug($data['name'], $place->id);
        }

        // Handle Main Image
        if ($request->hasFile('image')) {
            $this->placeService->deleteImage($place->image_path);
            $data['image_path'] = $this->placeService->uploadImage($request->file('image'), 'places');
        }

        unset($data['image']);
        unset($data['gallery_images']);

        $place->update($data);

        // Handle Gallery Images (Add new ones)
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $url = $this->placeService->uploadImage($image, 'place_gallery');

                $place->images()->create([
                    'image_path' => $url,
                ]);
            }
        }

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Place $place): RedirectResponse|JsonResponse
    {
        if ($place->image_path) {
            $this->placeService->deleteImage($place->image_path);
        }

        $place->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Lokasi berhasil dihapus.']);
        }

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Lokasi berhasil dihapus.');
    }
}
