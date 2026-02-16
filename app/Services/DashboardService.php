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
        $user = auth('admin')->user();
        $viewAllPlaces = $user->can('view all destinations');
        $viewAllPosts = $user->can('view all posts');
        $viewAllEvents = $user->can('view all events');
        $viewAllTickets = $user->can('view all tickets');
        $userId = $user->id;

        // Base queries
        $placesQuery = Place::query();
        $infrastructuresQuery = Infrastructure::query(); // Assuming no filtering for now or add if needed
        $landUsesQuery = LandUse::query(); // Assuming no filtering for now
        $postsQuery = Post::query();
        $eventsQuery = Event::query();
        $ticketOrdersQuery = TicketOrder::query();

        // Apply filters
        if (! $viewAllPlaces) {
            $placesQuery->where('created_by', $userId);
        }
        
        if (! $viewAllPosts) {
            $postsQuery->where('created_by', $userId);
        }
        
        if (! $viewAllEvents) {
            $eventsQuery->where('created_by', $userId);
        }

        if (! $viewAllTickets) {
            // For tickets, we need to join with tickets table and then places table to filter by place owner
            $ticketOrdersQuery->whereHas('ticket.place', function($q) use ($userId) {
                $q->where('created_by', $userId);
            });
        }

        // Calculate Stats
        $stats = [
            'places_count' => $placesQuery->count(),
            'infrastructures_count' => $infrastructuresQuery->count(),
            'land_uses_count' => $landUsesQuery->count(),
            
            // Categories - filter places count within categories
            'categories' => Category::withCount(['places' => function($q) use ($viewAllPlaces, $userId) {
                if (! $viewAllPlaces) {
                    $q->where('created_by', $userId);
                }
            }])->get(),
            
            'top_categories' => Category::withCount(['places' => function($q) use ($viewAllPlaces, $userId) {
                if (! $viewAllPlaces) {
                    $q->where('created_by', $userId);
                }
            }])->orderBy('places_count', 'desc')->take(3)->get(),
            
            'infrastructure_types' => Infrastructure::selectRaw('type, COUNT(*) as count, SUM(length_meters) as total_length')
                ->groupBy('type')
                ->get(),
                
            'land_use_types' => LandUse::selectRaw('type, COUNT(*) as count, SUM(area_hectares) as total_area')
                ->groupBy('type')
                ->get(),
                
            'total_land_use_area' => LandUse::sum('area_hectares'),
            'total_infrastructure_length' => Infrastructure::sum('length_meters'),
            
            'recent_places' => $placesQuery->latest()->take(5)->with('category')->get(), 
            'recent_infrastructures' => Infrastructure::latest()->take(5)->get(),
            'recent_land_uses' => LandUse::latest()->take(5)->get(),
            
            'posts_count' => $postsQuery->count(),
            'posts_published' => (clone $postsQuery)->where('is_published', true)->count(),
            'posts_draft' => (clone $postsQuery)->where('is_published', false)->count(),
            
            'events_count' => $eventsQuery->count(),
            'events_upcoming' => (clone $eventsQuery)->where('start_date', '>=', now())->count(),
            'events_past' => (clone $eventsQuery)->where('end_date', '<', now())->count(),
            
            'recent_posts' => (clone $postsQuery)->latest('published_at')->take(5)->get(),
            'upcoming_events' => (clone $eventsQuery)->where('start_date', '>=', now())->orderBy('start_date')->take(5)->get(),
             'this_month_events' => (clone $eventsQuery)
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->orderBy('start_date')
                ->get()
                ->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->start_date)->format('j'); // Group by day (1-31)
                }),
            
            'featured_event' => (clone $eventsQuery)
                ->where('start_date', '>=', now())
                ->where('is_published', true)
                ->orderBy('start_date')
                ->first(),
            
            'ticket_orders_count' => $ticketOrdersQuery->count(),
            'ticket_orders_pending' => (clone $ticketOrdersQuery)->where('status', 'pending')->count(),
            'ticket_orders_paid' => (clone $ticketOrdersQuery)->where('status', 'paid')->count(),
            'ticket_orders_used' => (clone $ticketOrdersQuery)->where('status', 'used')->count(),
            'ticket_revenue' => (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])->sum('total_price'),
            
            // Visitor Analytics
            'total_visitors' => (clone $ticketOrdersQuery)->where('status', 'used')->sum('quantity'),
            'visitors_this_month' => (clone $ticketOrdersQuery)->where('status', 'used')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('quantity'),
            'visitors_last_month' => (clone $ticketOrdersQuery)->where('status', 'used')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('quantity'),
            'visitors_today' => (clone $ticketOrdersQuery)->where('status', 'used')
                ->whereDate('created_at', today())
                ->sum('quantity'),
            
            // Revenue Metrics
            // Revenue Metrics
            'revenue_this_month' => (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price'),
            'revenue_last_month' => (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total_price'),
            'revenue_today' => (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])
                ->whereDate('created_at', today())
                ->sum('total_price'),
            
            'average_order_value' => (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])->count() > 0 
                ? (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])->sum('total_price') / (clone $ticketOrdersQuery)->whereIn('status', ['paid', 'used'])->count()
                : 0,

            // Booking Trends (Last 7 days)
            'bookings_last_7_days' => (clone $ticketOrdersQuery)->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),



            'visitation_trends' => $this->getVisitationTrends($viewAllPlaces, $userId),
            'yearly_visitation_trends' => $this->getYearlyVisitationTrends($viewAllPlaces, $userId),

            // Recent Orders (Transactions)
            'recent_orders' => TicketOrder::with(['ticket.place', 'user']) 
                ->whereIn('status', ['paid', 'used'])
                ->when(!$viewAllTickets, function($q) use ($userId) {
                    $q->whereHas('ticket.place', function($subQ) use ($userId) {
                        $subQ->where('created_by', $userId);
                    });
                })
                ->latest()
                ->take(5)
                ->get(),

            // Top Selling Tickets
            'top_tickets' => \App\Models\Ticket::with('place')
                ->where('is_active', true)
                ->withSum(['orders as total_sold' => function($q) {
                    $q->whereIn('status', ['paid', 'used']);
                }], 'quantity')
                ->when(!$viewAllTickets, function($q) use ($userId) {
                    $q->whereHas('place', function($subQ) use ($userId) {
                        $subQ->where('created_by', $userId);
                    });
                })
                ->having('total_sold', '>', 0)
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get(),

            // Top Visitor Origins (by City/Kecamatan)
            'top_visitor_origins' => TicketOrder::select('customer_city', \DB::raw('SUM(quantity) as total_visitors'))
                ->where('status', 'used')
                ->when(!$viewAllTickets, function($q) use ($userId) {
                    $q->whereHas('ticket.place', function($subQ) use ($userId) {
                        $subQ->where('created_by', $userId);
                    });
                })
                ->whereNotNull('customer_city')
                ->groupBy('customer_city')
                ->orderBy('total_visitors', 'desc')
                ->take(5)
                ->get(),

            // Weekly Revenue Trend (Last 7 Days)
            'weekly_revenue' => $this->getWeeklyRevenueTrend($viewAllTickets, $userId),

            // Top Posts (Konten Terlaris)
            'top_posts' => Post::withCount('visits')
                ->when(!$viewAllPosts, function($q) use ($userId) {
                    $q->where('created_by', $userId);
                })
                ->orderBy('visits_count', 'desc')
                ->take(5)
                ->get(),

            // Reader Graph (Grafik Pembaca) - Last 30 Days
            'post_view_trends' => $this->getPostViewTrends($viewAllPosts, $userId),
        ];

        return $stats;
    }

    /**
     * Get post view trends for the last 30 days.
     */
    private function getPostViewTrends($viewAllPosts, $userId)
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $days->put(now()->subDays($i)->format('Y-m-d'), 0);
        }

        $views = \App\Models\Visit::query()
            ->join('posts', 'visits.post_id', '=', 'posts.id')
            ->selectRaw('DATE(visits.created_at) as date, count(*) as count')
            ->where('visits.created_at', '>=', now()->subDays(29)->startOfDay())
            ->when(!$viewAllPosts, function($q) use ($userId) {
                $q->where('posts.created_by', $userId);
            })
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        $data = $days->merge($views);

        return [
            'labels' => $data->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->translatedFormat('d M'))->values(),
            'data' => $data->values(),
        ];
    }

    /**
     * Get weekly revenue trend for the last 7 days.
     */
    private function getWeeklyRevenueTrend($viewAllTickets, $userId)
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $days->put(now()->subDays($i)->format('Y-m-d'), 0);
        }

        $revenue = TicketOrder::select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereIn('status', ['paid', 'used'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->when(!$viewAllTickets, function($q) use ($userId) {
                $q->whereHas('ticket.place', function($subQ) use ($userId) {
                    $subQ->where('created_by', $userId);
                });
            })
            ->groupBy('date')
            ->get()
            ->pluck('total_revenue', 'date');

        $data = $days->merge($revenue);

        return [
            'labels' => $data->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->translatedFormat('d M'))->values(),
            'data' => $data->values(),
        ];
    }

    private function getVisitationTrends(bool $viewAllPlaces, int $userId): array
    {
        // Fetch raw data
        $trends = TicketOrder::query()
            ->join('tickets', 'ticket_orders.ticket_id', '=', 'tickets.id')
            ->join('places', 'tickets.place_id', '=', 'places.id')
            ->where('ticket_orders.status', 'used')
            ->whereYear('ticket_orders.created_at', now()->year)
            ->when(!$viewAllPlaces, function($q) use ($userId) {
                $q->where('places.created_by', $userId);
            })
            ->selectRaw('places.id, places.name, MONTH(ticket_orders.created_at) as month, SUM(ticket_orders.quantity) as total_visitors')
            ->groupBy('places.id', 'places.name', 'month')
            ->get();

        // Organize by Place
        $placeData = [];
        foreach($trends as $trend) {
            if(!isset($placeData[$trend->id])) {
                $placeData[$trend->id] = [
                    'name' => $trend->name,
                    'data' => array_fill(1, 12, 0)
                ];
            }
            $placeData[$trend->id]['data'][$trend->month] = (int)$trend->total_visitors;
        }

        // If no data, return structure with empty datasets
        if (empty($placeData)) {
            return [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                'datasets' => []
            ];
        }

        // Format for Chart.js
        $datasets = [];
        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', 
            '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#06b6d4',
            '#84cc16', '#a855f7', '#d946ef', '#f43f5e', '#64748b'
        ];
        $colorIndex = 0;

        foreach ($placeData as $place) {
            $datasets[] = [
                'label' => $place['name'],
                'data' => array_values($place['data']), // reset keys to 0-11
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'tension' => 0.4,
                'fill' => false,
                'borderWidth' => 2,
                'pointRadius' => 3,
            ];
            $colorIndex++;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => $datasets
        ];
    }

    private function getYearlyVisitationTrends(bool $viewAllPlaces, int $userId): array
    {
        // Fetch raw data (From last year onwards)
        $trends = TicketOrder::query()
            ->join('tickets', 'ticket_orders.ticket_id', '=', 'tickets.id')
            ->join('places', 'tickets.place_id', '=', 'places.id')
            ->where('ticket_orders.status', 'used')
            ->whereYear('ticket_orders.created_at', '>=', now()->subYears(4)->year)
            ->when(!$viewAllPlaces, function($q) use ($userId) {
                $q->where('places.created_by', $userId);
            })
            ->selectRaw('places.id, places.name, YEAR(ticket_orders.created_at) as year, SUM(ticket_orders.quantity) as total_visitors')
            ->groupBy('places.id', 'places.name', 'year')
            ->orderBy('year')
            ->get();

        // Get years range (Last 5 years)
        $currentYear = now()->year;
        $years = range($currentYear - 4, $currentYear);
        
        // Organize by Place
        $placeData = [];
        foreach($trends as $trend) {
            if(!isset($placeData[$trend->id])) {
                $placeData[$trend->id] = [
                    'name' => $trend->name,
                    'data' => array_fill_keys($years, 0)
                ];
            }
            $placeData[$trend->id]['data'][$trend->year] = (int)$trend->total_visitors;
        }

        // If no data, return structure with empty datasets
        if (empty($placeData)) {
            return [
                'labels' => $years,
                'datasets' => []
            ];
        }

        // Format for Chart.js
        $datasets = [];
        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', 
            '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#06b6d4',
            '#84cc16', '#a855f7', '#d946ef', '#f43f5e', '#64748b'
        ];
        $colorIndex = 0;

        foreach ($placeData as $place) {
            $datasets[] = [
                'label' => $place['name'],
                'data' => array_values($place['data']),
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'tension' => 0.4,
                'fill' => false,
                'borderWidth' => 2,
                'pointRadius' => 3,
            ];
            $colorIndex++;
        }

        return [
            'labels' => $years,
            'datasets' => $datasets
        ];
    }
}
