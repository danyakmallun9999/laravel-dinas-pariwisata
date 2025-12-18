<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(): View
    {
        $places = Place::with('category')->latest()->paginate(10);

        // Statistics
        $stats = [
            'places_count' => Place::count(),
            'boundaries_count' => Boundary::count(),
            'infrastructures_count' => Infrastructure::count(),
            'land_uses_count' => LandUse::count(),
            'categories' => Category::withCount('places')->get(),
            'infrastructure_types' => Infrastructure::selectRaw('type, COUNT(*) as count, SUM(length_meters) as total_length')
                ->groupBy('type')
                ->get(),
            'land_use_types' => LandUse::selectRaw('type, COUNT(*) as count, SUM(area_hectares) as total_area')
                ->groupBy('type')
                ->get(),
            'boundary_types' => Boundary::selectRaw('type, COUNT(*) as count, SUM(area_hectares) as total_area')
                ->groupBy('type')
                ->get(),
            'total_boundary_area' => Boundary::sum('area_hectares'),
            'total_land_use_area' => LandUse::sum('area_hectares'),
            'total_infrastructure_length' => Infrastructure::sum('length_meters'),
            'recent_places' => Place::with('category')->latest()->take(5)->get(),
            'recent_boundaries' => Boundary::latest()->take(5)->get(),
            'recent_infrastructures' => Infrastructure::latest()->take(5)->get(),
            'recent_land_uses' => LandUse::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('places', 'stats'));
    }

    public function placesIndex(): View
    {
        $places = Place::with('category')->latest()->paginate(10);
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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatePlace($request);

        // Handle geometry from drawing component (if provided)
        if ($request->has('geometry') && $request->geometry) {
            $geometry = json_decode($request->geometry, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($geometry['type']) && $geometry['type'] === 'Point') {
                $data['latitude'] = $geometry['coordinates'][1];
                $data['longitude'] = $geometry['coordinates'][0];
            }
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }

        unset($data['image']);

        $place = Place::create($data);

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

    public function update(Request $request, Place $place): RedirectResponse|JsonResponse
    {
        $data = $this->validatePlace($request);

        // Handle geometry from drawing component (if provided)
        if ($request->has('geometry') && $request->geometry) {
            $geometry = json_decode($request->geometry, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($geometry['type']) && $geometry['type'] === 'Point') {
                $data['latitude'] = $geometry['coordinates'][1];
                $data['longitude'] = $geometry['coordinates'][0];
            }
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($place->image_path);
            $data['image_path'] = $this->storeImage($request);
        }

        unset($data['image']);

        $place->update($data);

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Place $place): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($place->image_path) {
            $this->deleteImage($place->image_path);
        }

        $place->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Lokasi berhasil dihapus.']);
        }

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Lokasi berhasil dihapus.');
    }

    protected function validatePlace(Request $request): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'geometry' => ['nullable', 'string'], // For drawing component
        ];

        // Latitude/longitude required only if geometry not provided
        if (!$request->has('geometry') || !$request->geometry) {
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
        }

        return $request->validate($rules);
    }

    protected function storeImage(Request $request): string
    {
        $disk = env('FILESYSTEM_DISK', 'public');
        $path = $request->file('image')->store('places', $disk);

        if ($disk === 'supabase') {
             return Storage::disk('supabase')->url($path);
        }

        return 'storage/' . $path;
    }

    protected function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = env('FILESYSTEM_DISK', 'public');

        // Handle Supabase URL format
        if ($disk === 'supabase') {
            $path = parse_url($path, PHP_URL_PATH);
             // Remove the bucket prefix if it exists in the path
             $path = ltrim($path, '/');
             $path = str_replace('storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/', '', $path);
        } else {
             $path = str_replace('storage/', '', $path);
        }

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
