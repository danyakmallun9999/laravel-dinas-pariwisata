<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TravelAgency;

class TravelAgencyController extends Controller
{
    public function show(TravelAgency $agency)
    {
        $agency->load(['tourPackages' => function($q) {
            $q->orderBy('price_start', 'asc');
        }]);

        return view('public.travel-agencies.show', compact('agency'));
    }
}
