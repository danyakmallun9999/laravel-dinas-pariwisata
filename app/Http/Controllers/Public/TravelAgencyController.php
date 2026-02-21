<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TravelAgency;

class TravelAgencyController extends Controller
{
    public function show(TravelAgency $agency)
    {
        return view('public.travel-agencies.show', compact('agency'));
    }
}
