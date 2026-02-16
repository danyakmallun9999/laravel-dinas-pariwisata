<?php

namespace App\Http\Controllers\Public\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Ticket\ProcessCheckoutRequest;
use App\Http\Requests\Public\Ticket\StoreBookingRequest;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Store booking data in session (Step 1).
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::guard('web')->user();

        // Enforce user ownership
        $validated['customer_email'] = $user->email;
        $validated['user_id'] = $user->id;

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        // Resolve Location Names if Indonesia
        if ($validated['customer_country'] === 'Indonesia') {
            if (! empty($validated['customer_province'])) {
                $province = \Laravolt\Indonesia\Models\Province::find($validated['customer_province']);
                $validated['customer_province'] = $province ? $province->name : $validated['customer_province'];
            }
            if (! empty($validated['customer_city'])) {
                if (is_numeric($validated['customer_city'])) {
                    $city = \Laravolt\Indonesia\Models\City::find($validated['customer_city']);
                    $validated['customer_city'] = $city ? $city->name : $validated['customer_city'];
                }
            }
        }

        // Check availability
        if (! $ticket->isAvailableOn($validated['visit_date'], $validated['quantity'])) {
            return back()->withErrors([
                'quantity' => 'Kuota tiket tidak mencukupi untuk tanggal yang dipilih.',
            ])->withInput();
        }

        // Calculate prices
        $pricePerTicket = $ticket->getPriceForDate($validated['visit_date']);
        $validated['total_price'] = $pricePerTicket * $validated['quantity'];
        $validated['unit_price'] = $pricePerTicket;
        $validated['status'] = 'pending';
        $validated['payment_method'] = 'midtrans';
        $validated['expiry_time'] = now()->addMinutes(2); // Set expiry to 2 mins for testing

        // Store in session
        session(['ticket_booking' => $validated]);

        return redirect()->route('booking.checkout');
    }

    /**
     * Show Checkout Page (Step 2).
     */
    public function checkout()
    {
        $booking = session('ticket_booking');

        if (! $booking) {
            return redirect()->route('tickets.index');
        }

        $ticket = Ticket::with('place')->findOrFail($booking['ticket_id']);

        return view('user.booking.checkout', compact('booking', 'ticket'));
    }

    /**
     * Process Checkout -> Create Order -> Charge (Step 3).
     */
    public function process(ProcessCheckoutRequest $request)
    {
        $booking = session('ticket_booking');

        if (! $booking) {
            return redirect()->route('tickets.index')->with('error', 'Sesi pemesanan habis, silakan ulang kembali.');
        }

        $paymentType = $request->input('payment_type');
        $bank = $request->input('bank');

        try {
            return DB::transaction(function () use ($booking, $paymentType, $bank) {
                // Lock ticket for quota check
                $ticket = Ticket::lockForUpdate()->findOrFail($booking['ticket_id']);

                if (!$ticket->isAvailableOn($booking['visit_date'], $booking['quantity'])) {
                    throw new \Exception('Kuota tiket tidak mencukupi. Silakan pilih tanggal lain.');
                }

                // HIGH-02: Re-calculate price at checkout time from DB (not from session)
                // This prevents price manipulation if admin changes price between booking and checkout
                $pricePerTicket = $ticket->getPriceForDate($booking['visit_date']);
                $totalPrice = $pricePerTicket * $booking['quantity'];

                // Create order — use forceFill for guarded financial fields
                // total_price, unit_price, status are intentionally guarded from mass assignment
                // but safe to set here because values are calculated server-side
                $order = new TicketOrder();
                $order->fill($booking);
                $order->total_price = $totalPrice;
                $order->unit_price = $pricePerTicket;
                $order->status = 'pending';
                $order->save();

                $response = $this->midtransService->createCoreCharge($order, $paymentType, $bank);
                $paymentData = $this->midtransService->extractPaymentData($response, $paymentType, $bank);

                $order->payment_gateway_id = $response->transaction_id ?? null;
                $order->payment_method_detail = $paymentType;
                $order->payment_channel = $bank ?? $paymentType;
                $order->payment_info = $paymentData;
                $order->expiry_time = $response->expiry_time ?? now()->addMinutes(2);
                $order->save();

                session()->put("payment_data.{$order->order_number}", $paymentData);
                session()->forget('ticket_booking');

                return redirect()->route('payment.status', $order->order_number);
            });

        } catch (\Exception $e) {
            Log::error('Checkout processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            // SECURITY: Never expose raw exception/SQL messages to public users
            $userMessage = 'Gagal memproses pesanan. Silakan coba lagi atau hubungi admin.';

            // Only show specific message for known safe exceptions
            if (str_contains($e->getMessage(), 'Kuota tiket tidak mencukupi')) {
                $userMessage = $e->getMessage();
            }

            return redirect()->back()->with('error', $userMessage);
        }
    }

    /**
     * Show Confirmation (Optional step, usually skipped to payment status).
     */
    public function confirmation($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->authorize('view', $order);

        // Auto-cancel check
        if ($order->checkAutoCancel()) {
            return redirect()->route('payment.failed', $orderNumber);
        }

        return view('user.booking.confirmation', compact('order'));
    }
}
