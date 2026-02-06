<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }
    /**
     * Display a listing of available tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('place')->active();

        // Filter by place
        if ($request->filled('place_id')) {
            $query->where('place_id', $request->place_id);
        }

        $tickets = $query->get();

        return view('public.tickets.index', compact('tickets'));
    }

    /**
     * Display the specified ticket and booking form.
     */
    public function show(Ticket $ticket)
    {
        if (!$ticket->is_active) {
            abort(404, 'Tiket tidak tersedia');
        }

        $ticket->load('place');
        
        return view('public.tickets.show', compact('ticket'));
    }

    /**
     * Process ticket booking.
     */
    public function book(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'visit_date' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        // Check if ticket is available
        if (!$ticket->isAvailableOn($validated['visit_date'], $validated['quantity'])) {
            return back()->withErrors([
                'quantity' => 'Kuota tiket tidak mencukupi untuk tanggal yang dipilih.'
            ])->withInput();
        }

        // Calculate total price
        $validated['total_price'] = $ticket->price * $validated['quantity'];
        $validated['status'] = 'pending';
        $validated['payment_method'] = 'xendit'; // Set default to xendit

        // Create order
        $order = TicketOrder::create($validated);

        // Create Xendit invoice for online payment
        try {
            $invoice = $this->xenditService->createInvoice($order);
            
            // Redirect to payment page with Snap checkout
            return redirect()->route('tickets.payment', $order->order_number);
        } catch (\Exception $e) {
            // If invoice creation fails, still show confirmation but with manual payment
            return redirect()->route('tickets.confirmation', $order->order_number)
                ->with('warning', 'Pesanan dibuat, namun terjadi kesalahan pada sistem pembayaran. Silakan hubungi admin.');
        }
    }

    /**
     * Show booking confirmation.
     */
    public function confirmation($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('public.tickets.confirmation', compact('order'));
    }

    /**
     * Show my tickets page.
     */
    public function myTickets()
    {
        return view('public.tickets.my-tickets');
    }

    /**
     * Retrieve tickets by email.
     */
    public function retrieveTickets(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $orders = TicketOrder::with('ticket.place')
            ->where('customer_email', $validated['email'])
            ->latest()
            ->get();

        return view('public.tickets.my-tickets', compact('orders'));
    }

    /**
     * Download ticket as PDF.
     */
    public function downloadTicket($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // For now, just show the ticket view
        // You can implement PDF generation later with dompdf
        return view('public.tickets.download', compact('order'));
    }

    /**
     * Show payment page with Xendit invoice
     */
    public function payment($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // If already paid, redirect to confirmation
        if ($order->status === 'paid') {
            return redirect()->route('tickets.confirmation', $orderNumber)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        // If no invoice URL, create one
        if (!$order->xendit_invoice_url) {
            try {
                $this->xenditService->createInvoice($order);
                $order->refresh();
            } catch (\Exception $e) {
                return redirect()->route('tickets.confirmation', $orderNumber)
                    ->with('error', 'Gagal membuat invoice pembayaran.');
            }
        }

        return view('public.tickets.payment', compact('order'));
    }

    /**
     * Handle successful payment redirect
     */
    public function paymentSuccess($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Verify payment status with Xendit if still pending
        if ($order->status === 'pending' && $order->xendit_invoice_id) {
            try {
                $invoice = $this->xenditService->getInvoice($order->xendit_invoice_id);
                
                // Check if invoice is paid
                if ($invoice['status'] === 'PAID') {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'xendit_payment_method' => $invoice['payment_method'] ?? null,
                        'xendit_payment_channel' => $invoice['payment_channel'] ?? null,
                    ]);
                    
                    Log::info('Payment status updated manually', [
                        'order_number' => $orderNumber,
                        'invoice_id' => $order->xendit_invoice_id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to verify payment status', [
                    'order_number' => $orderNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('public.tickets.payment-success', compact('order'));
    }

    /**
     * Handle payment failed callback
     */
    public function paymentFailed($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('public.tickets.payment-failed', compact('order'));
    }
}
