<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Infrastructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfrastructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $infrastructures = Infrastructure::with('category')->latest()->paginate(10);

        return view('admin.infrastructures.index', compact('infrastructures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $infrastructure = new Infrastructure([
            'type' => 'road',
        ]);

        return view('admin.infrastructures.create', compact('categories', 'infrastructure'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|string',
            'length_meters' => 'nullable|numeric|min:0',
            'width_meters' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        Infrastructure::create($data);

        return redirect()
            ->route('admin.infrastructures.index')
            ->with('status', 'Infrastruktur berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Infrastructure $infrastructure): View
    {
        return view('admin.infrastructures.show', compact('infrastructure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Infrastructure $infrastructure): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.infrastructures.edit', compact('categories', 'infrastructure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Infrastructure $infrastructure): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|string',
            'length_meters' => 'nullable|numeric|min:0',
            'width_meters' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        $infrastructure->update($data);

        return redirect()
            ->route('admin.infrastructures.index')
            ->with('status', 'Infrastruktur berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Infrastructure $infrastructure): RedirectResponse
    {
        $infrastructure->delete();

        return redirect()
            ->route('admin.infrastructures.index')
            ->with('status', 'Infrastruktur berhasil dihapus.');
    }
}
