<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $places = \App\Models\Place::with('category')->latest()->paginate(10);
        return view('dashboard', compact('places'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('places.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $disk = env('FILESYSTEM_DISK', 'public');
            $path = $request->file('image')->store('places', $disk);
            
            if ($disk === 'supabase') {
                 $validated['image_path'] = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
            } else {
                 $validated['image_path'] = 'storage/' . $path;
            }
        }

        \App\Models\Place::create($validated);

        return redirect()->route('places.index')->with('success', 'Place created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $place = \App\Models\Place::findOrFail($id);
        $categories = \App\Models\Category::all();
        return view('places.edit', compact('place', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $place = \App\Models\Place::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $disk = env('FILESYSTEM_DISK', 'public');
            $path = $request->file('image')->store('places', $disk);
            
            if ($disk === 'supabase') {
                 $validated['image_path'] = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
            } else {
                 $validated['image_path'] = 'storage/' . $path;
            }
        }

        $place->update($validated);

        return redirect()->route('places.index')->with('success', 'Place updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $place = \App\Models\Place::findOrFail($id);
        $place->delete();

        return redirect()->route('places.index')->with('success', 'Place deleted successfully.');
    }
}
