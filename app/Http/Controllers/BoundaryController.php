<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoundaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Boundary::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $boundaries = $query->latest()->paginate(10);

        return view('admin.boundaries.index', compact('boundaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $boundary = new Boundary([
            'type' => 'village_boundary',
        ]);

        return view('admin.boundaries.create', compact('boundary'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|string',
            'description' => 'nullable|string',
            'area_hectares' => 'nullable|numeric|min:0',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        $boundary = Boundary::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Batas wilayah berhasil ditambahkan.',
                'boundary' => $boundary,
            ]);
        }

        return redirect()
            ->route('admin.boundaries.index')
            ->with('status', 'Batas wilayah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Boundary $boundary): View
    {
        return view('admin.boundaries.show', compact('boundary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Boundary $boundary): View
    {
        return view('admin.boundaries.edit', compact('boundary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Boundary $boundary): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|string',
            'description' => 'nullable|string',
            'area_hectares' => 'nullable|numeric|min:0',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        $boundary->update($data);

        return redirect()
            ->route('admin.boundaries.index')
            ->with('status', 'Batas wilayah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Boundary $boundary): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $boundary->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Batas wilayah berhasil dihapus.']);
        }

        return redirect()
            ->route('admin.boundaries.index')
            ->with('status', 'Batas wilayah berhasil dihapus.');
    }
}
