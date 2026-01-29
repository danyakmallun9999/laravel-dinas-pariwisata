<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Event;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'posts_count' => Post::count(),
            'events_count' => Event::count(),
            'recent_posts' => Post::latest('published_at')->take(5)->get(),
            'upcoming_events' => Event::where('start_date', '>=', now())->orderBy('start_date')->take(5)->get(),
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

        // Generate Slug
        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Place::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;


        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }

        // Remove gallery_images from data before creating Place
        $galleryImages = $request->file('gallery_images');
        unset($data['gallery_images']);

        unset($data['image']);

        $place = Place::create($data);

        if ($galleryImages) {
            foreach ($galleryImages as $image) {
                // Manually store each gallery image using a similar logic to storeImage but adapted for generic file
                $disk = env('FILESYSTEM_DISK', 'public');
                $path = $image->store('place_gallery', $disk);
                $url = Storage::disk($disk)->url($path);
                
                $place->images()->create([
                    'image_path' => $url
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

        // Update Slug if name changed or slug is missing
        if ($request->name !== $place->name || !$place->slug) {
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Place::where('slug', $slug)->where('id', '!=', $place->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($place->image_path);
            $data['image_path'] = $this->storeImage($request);
        }

        unset($data['image']);

        // Handle Gallery Images for Update (Add new ones)
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $disk = env('FILESYSTEM_DISK', 'public');
                $path = $image->store('place_gallery', $disk);
                $url = Storage::disk($disk)->url($path);
                
                $place->images()->create([
                    'image_path' => $url
                ]);
            }
        }
        unset($data['gallery_images']);

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
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'geometry' => ['nullable', 'string'],
            'ticket_price' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'google_maps_link' => ['nullable', 'url', 'max:255'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
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

        return Storage::disk($disk)->url($path);
    }

    protected function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = env('FILESYSTEM_DISK', 'public');

        // Extract relative path from URL if needed
        // This is a basic implementation. For a robust solution, consider storing only filenames in DB.
        $relativePath = str_replace(Storage::disk($disk)->url(''), '', $path);
        
        // Fallback for local storage without full URL
        if ($relativePath === $path && $disk === 'public') {
             $relativePath = str_replace('/storage/', '', $path);
             $relativePath = str_replace('storage/', '', $relativePath);
        }

        if (Storage::disk($disk)->exists($relativePath)) {
            Storage::disk($disk)->delete($relativePath);
        }
    }
}
