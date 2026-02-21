<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\TravelAgency;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class FlagshipController extends Controller
{
    public function show(Place $place)
    {
        // Pastikan ini benar-benar unggulan
        if (!$place->is_flagship) {
            abort(404);
        }

        $search = request('search');

        // Ambil data Biro Wisata yang melayani tempat ini
        $query = TravelAgency::whereHas('tourPackages', function($q) use ($place) {
            $q->where('place_id', $place->id);
        })->with(['tourPackages' => function($q) use ($place) {
            $q->where('place_id', $place->id);
        }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $agencies = $query->paginate(6)->withQueryString();

        // Ambil paket unggulan (opsional) atau semua paket
        $packages = TourPackage::where('place_id', $place->id)
            ->with('travelAgency')
            ->get();

        return view('public.places.flagship-show', compact('place', 'agencies', 'packages', 'search'));
    }
}
