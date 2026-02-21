<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\TravelAgency;
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

        // Ambil data Biro Wisata yang melayani destinasi ini
        $query = TravelAgency::whereHas('places', function($q) use ($place) {
            $q->where('places.id', $place->id);
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $agencies = $query->paginate(6)->withQueryString();

        return view('public.places.flagship-show', compact('place', 'agencies', 'search'));
    }
}
