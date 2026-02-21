<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\TravelAgency;
use App\Models\Place;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TourPackage::with(['travelAgency', 'place']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $packages = $query->latest()->paginate(10)->withQueryString();

        return view('admin.tour-packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agencies = TravelAgency::orderBy('name')->get();
        // Get all places, potentially we only want flagship ones, but it's safe to list all or let admin choose
        $places = Place::orderBy('name')->get(); 
        return view('admin.tour-packages.create', compact('agencies', 'places'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'travel_agency_id' => 'required|exists:travel_agencies,id',
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_start' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'inclusions' => 'nullable|string', // We'll store as string then cast to array in model, wait, model casts to array. If the form uses text area, we need to handle it. Or if it uses dynamic input. Let's assume standard text area JSON or comma separated. Wait, earlier seeder used array. We should validate as array.
        ]);

        // Handle inclusions and itinerary if they are passed as JSON strings from frontend, or arrays.
        // Assuming we pass them as arrays from dynamic inputs
        $validatedList = $request->validate([
            'inclusions' => 'nullable|array',
            'inclusions.*' => 'nullable|string',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'required|integer',
            'itinerary.*.time' => 'required|string',
            'itinerary.*.activity' => 'required|string',
        ]);

        $data = array_merge($validated, $validatedList);

        TourPackage::create($data);

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket Liburan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TourPackage $tourPackage)
    {
        $agencies = TravelAgency::orderBy('name')->get();
        $places = Place::orderBy('name')->get(); 
        return view('admin.tour-packages.edit', compact('tourPackage', 'agencies', 'places'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TourPackage $tourPackage)
    {
        $validated = $request->validate([
            'travel_agency_id' => 'required|exists:travel_agencies,id',
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_start' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
        ]);

        $validatedList = $request->validate([
            'inclusions' => 'nullable|array',
            'inclusions.*' => 'nullable|string',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'required|integer',
            'itinerary.*.time' => 'required|string',
            'itinerary.*.activity' => 'required|string',
        ]);

        $data = array_merge($validated, $validatedList);
        
        // Ensure empty arrays default to empty instead of null if submitted empty
        if (!isset($data['inclusions'])) $data['inclusions'] = [];
        if (!isset($data['itinerary'])) $data['itinerary'] = [];

        $tourPackage->update($data);

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket Liburan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TourPackage $tourPackage)
    {
        $tourPackage->delete();

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket Liburan berhasil dihapus.');
    }
}
