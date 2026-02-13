<?php

namespace App\Services;



use App\Models\Category;
use App\Models\Event;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use App\Models\Post;
use App\Models\TicketOrder;

class DashboardService
{
    /**
     * Get all statistics for the admin dashboard.
     */
    public function getDashboardStats(): array
    {
        return [
            'places_count' => Place::count(),
            'infrastructures_count' => Infrastructure::count(),
            'land_uses_count' => LandUse::count(),
            'categories' => Category::withCount('places')->get(),
            'top_categories' => Category::withCount('places')->orderBy('places_count', 'desc')->take(3)->get(),
            'infrastructure_types' => Infrastructure::selectRaw('type, COUNT(*) as count, SUM(length_meters) as total_length')
                ->groupBy('type')
                ->get(),
            'land_use_types' => LandUse::selectRaw('type, COUNT(*) as count, SUM(area_hectares) as total_area')
                ->groupBy('type')
                ->get(),
            'total_land_use_area' => LandUse::sum('area_hectares'),
            'total_infrastructure_length' => Infrastructure::sum('length_meters'),
            'recent_places' => Place::with('category')->latest()->take(5)->get(),
            'recent_infrastructures' => Infrastructure::latest()->take(5)->get(),
            'recent_land_uses' => LandUse::latest()->take(5)->get(),
            'posts_count' => Post::count(),
            'posts_published' => Post::where('is_published', true)->count(),
            'posts_draft' => Post::where('is_published', false)->count(),
            'events_count' => Event::count(),
            'events_upcoming' => Event::where('start_date', '>=', now())->count(),
            'events_past' => Event::where('end_date', '<', now())->count(),
            'recent_posts' => Post::latest('published_at')->take(5)->get(),
            'upcoming_events' => Event::where('start_date', '>=', now())->orderBy('start_date')->take(5)->get(),
            'ticket_orders_count' => TicketOrder::count(),
            'ticket_orders_pending' => TicketOrder::where('status', 'pending')->count(),
            'ticket_orders_paid' => TicketOrder::where('status', 'paid')->count(),
            'ticket_revenue' => TicketOrder::whereIn('status', ['paid', 'used'])->sum('total_price'),
            
            // Visitor Analytics
            'total_visitors' => TicketOrder::whereIn('status', ['paid', 'used'])->sum('quantity'),
            'visitors_this_month' => TicketOrder::whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('quantity'),
            'visitors_last_month' => TicketOrder::whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('quantity'),
            
            // Revenue Metrics
            'revenue_this_month' => TicketOrder::whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price'),
            'revenue_last_month' => TicketOrder::whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total_price'),
            'average_order_value' => TicketOrder::whereIn('status', ['paid', 'used'])->count() > 0 
                ? TicketOrder::whereIn('status', ['paid', 'used'])->sum('total_price') / TicketOrder::whereIn('status', ['paid', 'used'])->count()
                : 0,
            
            // Booking Trends (Last 7 days)
            'bookings_last_7_days' => TicketOrder::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            // Top Destinations by ticket sales
            'top_destinations' => Place::withCount(['tickets as total_sales' => function($query) {
                $query->join('ticket_orders', 'tickets.id', '=', 'ticket_orders.ticket_id')
                    ->whereIn('ticket_orders.status', ['paid', 'used']);
            }])
            ->with('category')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get(),
        ];
    }
}
