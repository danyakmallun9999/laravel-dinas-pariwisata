<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Models\Place;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('place')->withCount('orders')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('place', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

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
        $request->validate([
            'place_id' => 'required|exists:places,id',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.type' => 'required|string|in:adult,child,foreigner,general',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.price_weekend' => 'nullable|numeric|min:0',
            'tickets.*.quota' => 'nullable|integer|min:1',
            'tickets.*.valid_days' => 'required|integer|min:1',
            'tickets.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($request->tickets as $ticketData) {
            Ticket::create([
                'place_id' => $request->place_id,
                'name' => $ticketData['name'],
                'type' => $ticketData['type'],
                'price' => $ticketData['price'],
                'price_weekend' => $ticketData['price_weekend'] ?? null,
                'quota' => $ticketData['quota'] ?? null,
                'valid_days' => $ticketData['valid_days'],
                'is_active' => isset($ticketData['is_active']) ? true : false,
            ]);
        }

        return redirect()->route('admin.tickets.index')
            ->with('success', count($request->tickets) . ' tiket berhasil ditambahkan!');
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

        // HIGH-05: Verify with payment gateway before marking as paid
        if ($validated['status'] === 'paid' && $order->status !== 'paid') {
            if ($order->payment_gateway_id) {
                try {
                    $midtransStatus = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                    $txStatus = $midtransStatus->transaction_status ?? null;

                    if (!in_array($txStatus, ['settlement', 'capture'])) {
                        return back()->with('error', 'Pembayaran belum diverifikasi oleh payment gateway (status: ' . ($txStatus ?? 'unknown') . ')');
                    }
                } catch (\Exception $e) {
                    Log::warning('Payment verification failed during admin status update', [
                        'order' => $order->order_number,
                        'error' => $e->getMessage(),
                    ]);
                    return back()->with('error', 'Gagal memverifikasi pembayaran. Silakan coba lagi.');
                }
            }

            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $order->generateTicketNumber();
        } else {
            $order->update($validated);
        }

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

    /**
     * Display ticket sales history (paid & used orders).
     */
    public function history(Request $request)
    {
        $query = TicketOrder::with(['ticket.place', 'user']);

        // Role-based filtering: Pengelola Wisata only sees orders for their places
        if (!auth()->user()->can('view all tickets') && !auth()->user()->hasRole('super_admin')) {
            $query->whereHas('ticket.place', function($q) {
                $q->where('created_by', auth()->id());
            });
        }

        // Default to paid/used status, but allow filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['paid', 'used']);
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

        // Calculate stats before pagination
        $statsQuery = clone $query;
        $totalQty = $statsQuery->sum('quantity');
        $totalRevenue = $statsQuery->sum('total_price');
        $totalTransactions = $statsQuery->count();

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.tickets.history', compact('orders', 'totalQty', 'totalRevenue', 'totalTransactions'));
    }
}
