<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        
        $query = Category::withCount('places')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $categories = $query->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle slug collision just in case (simple append)
        if (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-'.time();
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        
        // Not used widely, maybe redirect to places filtered by category?
        return redirect()->route('admin.places.index', ['category_id' => $category->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        // Handle collision if name changed
        if ($category->name !== $validated['name'] && Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-'.time();
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        // Delete related places or restrict?
        // Places cascade? migration said: ->onDelete('cascade')?
        // Let's check. Yes create_places_table had constrained()->cascadeOnDelete().

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
