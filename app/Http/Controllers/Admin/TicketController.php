<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Models\Place;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index()
    {
        $tickets = Ticket::with('place')
            ->latest()
            ->paginate(15);

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $places = Place::orderBy('name')->get();
        return view('admin.tickets.create', compact('places'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:adult,child,foreigner,general',

            'price' => 'required|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',
            'valid_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Ticket::create($validated);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil ditambahkan!');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load('place', 'orders');
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Ticket $ticket)
    {
        $places = Place::orderBy('name')->get();
        return view('admin.tickets.edit', compact('ticket', 'places'));
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:255',

            'type' => 'required|string|in:adult,child,foreigner,general',
            'price' => 'required|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',
            'valid_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $ticket->update($validated);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil diperbarui!');
    }

    /**
     * Remove the specified ticket from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil dihapus!');
    }

    /**
     * Display all ticket orders.
     */
    public function orders(Request $request)
    {
        $query = TicketOrder::with(['ticket.place']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.tickets.orders', compact('orders'));
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, TicketOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,used,cancelled',
        ]);

        $order->update($validated);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Delete ticket order.
     */
    public function destroyOrder(TicketOrder $order)
    {
        $orderNumber = $order->order_number;
        $order->delete();

        return back()->with('success', "Pesanan {$orderNumber} berhasil dihapus!");
    }
}
