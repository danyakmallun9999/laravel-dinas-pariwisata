<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        // Fetch all published events ordered by start date
        $events = Event::where('is_published', true)
                       ->orderBy('start_date', 'asc')
                       ->get();

        // Group events by Month and Year (e.g., "January 2026")
        $groupedEvents = $events->groupBy(function ($event) {
            return $event->start_date->translatedFormat('F Y');
        });

        return view('public.events.index', compact('groupedEvents'));
    }
}
