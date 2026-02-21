<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TravelAgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TravelAgency::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $agencies = $query->latest()->paginate(10)->withQueryString();

        return view('admin.travel-agencies.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.travel-agencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_wa' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('travel_agencies', 'public');
            $validated['logo_path'] = 'storage/' . $path;
        }

        TravelAgency::create($validated);

        return redirect()->route('admin.travel-agencies.index')
            ->with('success', 'Biro Wisata berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TravelAgency $travelAgency)
    {
        return view('admin.travel-agencies.edit', compact('travelAgency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelAgency $travelAgency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_wa' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($travelAgency->logo_path) {
                $oldPath = str_replace('storage/', '', $travelAgency->logo_path);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('logo')->store('travel_agencies', 'public');
            $validated['logo_path'] = 'storage/' . $path;
        }

        $travelAgency->update($validated);

        return redirect()->route('admin.travel-agencies.index')
            ->with('success', 'Biro Wisata berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelAgency $travelAgency)
    {
        if ($travelAgency->logo_path) {
            $oldPath = str_replace('storage/', '', $travelAgency->logo_path);
            Storage::disk('public')->delete($oldPath);
        }
        
        $travelAgency->delete();

        return redirect()->route('admin.travel-agencies.index')
            ->with('success', 'Biro Wisata berhasil dihapus.');
    }
}
