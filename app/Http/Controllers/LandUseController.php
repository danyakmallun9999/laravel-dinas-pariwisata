<?php

namespace App\Http\Controllers;

use App\Models\LandUse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandUseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $landUses = LandUse::latest()->paginate(10);

        return view('admin.land_uses.index', compact('landUses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $landUse = new LandUse([
            'type' => 'settlement',
        ]);

        return view('admin.land_uses.create', compact('landUse'));
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
            'area_hectares' => 'nullable|numeric|min:0',
            'owner' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        $landUse = LandUse::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Penggunaan lahan berhasil ditambahkan.',
                'land_use' => $landUse,
            ]);
        }

        return redirect()
            ->route('admin.land-uses.index')
            ->with('status', 'Penggunaan lahan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LandUse $landUse): View
    {
        return view('admin.land_uses.show', compact('landUse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LandUse $landUse): View
    {
        return view('admin.land_uses.edit', compact('landUse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LandUse $landUse): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|string',
            'area_hectares' => 'nullable|numeric|min:0',
            'owner' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Parse geometry JSON string to array
        $data['geometry'] = json_decode($data['geometry'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['geometry' => 'Format geometry tidak valid.'])->withInput();
        }

        $landUse->update($data);

        return redirect()
            ->route('admin.land-uses.index')
            ->with('status', 'Penggunaan lahan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LandUse $landUse): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $landUse->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Penggunaan lahan berhasil dihapus.']);
        }

        return redirect()
            ->route('admin.land-uses.index')
            ->with('status', 'Penggunaan lahan berhasil dihapus.');
    }
}
