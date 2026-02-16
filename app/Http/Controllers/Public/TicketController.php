<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Services\MidtransService;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of available tickets.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Place::whereHas('tickets', function ($q) {
            $q->active();
        })->with(['tickets' => function ($q) {
            $q->active();
        }]);

        // Filter by place (if needed, though we are now listing places)
        if ($request->filled('place_id')) {
            $query->where('id', $request->place_id);
        }

        $places = $query->get();

        return view('public.tickets.index', compact('places'));
    }

    /**
     * Display the specified ticket and booking form.
     */
    public function show(Ticket $ticket)
    {
        if (! $ticket->is_active) {
            abort(404, 'Tiket tidak tersedia');
        }

        $ticket->load('place');

        return view('public.tickets.show', compact('ticket'));
    }

    /**
     * Verify that the authenticated user owns the order.
     * SCAN-04: Abort if user is null (defense-in-depth).
     */
    private function verifyOrderOwnership(TicketOrder $order)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->email !== $order->customer_email) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }
    }

    /**
     * Process ticket booking.
     */
    public function book(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_country' => 'required|string|max:100',
            'customer_province' => 'nullable|string|max:100',
            'customer_city' => 'nullable|string|max:100',
            'visit_date' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Force customer email to match authenticated user
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
                // Check if it's an ID (numeric) or "Lainnya" custom text
                if (is_numeric($validated['customer_city'])) {
                    $city = \Laravolt\Indonesia\Models\City::find($validated['customer_city']);
                    $validated['customer_city'] = $city ? $city->name : $validated['customer_city'];
                }
                // If it's text (custom input), keep as is
            }
        }

        // Check if ticket is available
        if (! $ticket->isAvailableOn($validated['visit_date'], $validated['quantity'])) {
            return back()->withErrors([
                'quantity' => 'Kuota tiket tidak mencukupi untuk tanggal yang dipilih.',
            ])->withInput();
        }

        // Calculate total price based on date (weekend/weekday)
        $pricePerTicket = $ticket->getPriceForDate($validated['visit_date']);
        $validated['total_price'] = $pricePerTicket * $validated['quantity'];
        $validated['unit_price'] = $pricePerTicket;
        $validated['status'] = 'pending';
        $validated['payment_method'] = 'midtrans';
        $validated['expiry_time'] = now()->addMinutes(2); // Set expiry to 2 mins for testing

        // Store booking data in session instead of creating order
        session(['ticket_booking' => $validated]);

        // Redirect to checkout page
        return redirect()->route('tickets.checkout');
    }

    /**
     * Show checkout page (step 2).
     */
    public function checkout()
    {
        $booking = session('ticket_booking');

        if (! $booking) {
            return redirect()->route('tickets.index');
        }

        $ticket = Ticket::with('place')->findOrFail($booking['ticket_id']);

        return view('user.tickets.checkout', compact('booking', 'ticket'));
    }

    /**
     * Process checkout (step 3) — Create Order & Charge
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:qris,gopay,shopeepay,bank_transfer,echannel',
            'bank' => 'required_if:payment_type,bank_transfer|nullable|in:bca,bni,bri',
        ]);

        $booking = session('ticket_booking');

        if (! $booking) {
            return redirect()->route('tickets.index')->with('error', 'Sesi pemesanan habis, silakan ulang kembali.');
        }

        $paymentType = $request->input('payment_type');
        $bank = $request->input('bank');

        try {
            return DB::transaction(function () use ($booking, $paymentType, $bank) {
                // Lock the ticket row to prevent concurrent oversell (CRIT-03)
                $ticket = Ticket::lockForUpdate()->findOrFail($booking['ticket_id']);

                // Re-verify quota inside the lock — prevents race condition
                if (!$ticket->isAvailableOn($booking['visit_date'], $booking['quantity'])) {
                    throw new \Exception('Kuota tiket tidak mencukupi. Silakan pilih tanggal lain.');
                }

                // Create Order
                $order = TicketOrder::create($booking);

                // Charge Midtrans
                $response = $this->midtransService->createCoreCharge($order, $paymentType, $bank);
                $paymentData = $this->midtransService->extractPaymentData($response, $paymentType, $bank);

                // Update Order with Payment Info
                $order->update([
                    'payment_gateway_id' => $response->transaction_id ?? null, // Depends on response structure
                    'payment_method_detail' => $paymentType,
                    'payment_channel' => $bank ?? $paymentType,
                    'payment_info' => $paymentData,
                    'expiry_time' => $response->expiry_time ?? now()->addMinutes(2),
                ]);

                // Store payment data in session for status page
                session()->put("payment_data.{$order->order_number}", $paymentData);

                // Clear booking session
                session()->forget('ticket_booking');

                return redirect()->route('tickets.payment.status', $order->order_number);
            });

        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Gagal memproses pesanan: '.$e->getMessage());
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

        $this->verifyOrderOwnership($order);

        return view('user.tickets.confirmation', compact('order'));
    }

    /**
     * Show my tickets page — auto-loads orders for the logged-in user.
     */
    public function myTickets()
    {
        $user = Auth::guard('web')->user();

        $orders = TicketOrder::with('ticket.place')
            ->where('customer_email', $user->email)
            ->latest()
            ->get();

        // Auto-sync pending orders with Midtrans (NEW-02: atomic updates)
        foreach ($orders->where('status', 'pending') as $order) {
            if ($order->payment_gateway_id) {
                try {
                    $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                    $transactionStatus = $status->transaction_status ?? null;
                    $fraudStatus = $status->fraud_status ?? 'accept';

                    if ($transactionStatus === 'settlement' ||
                        ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                        DB::transaction(function () use ($order, $status) {
                            $lockedOrder = TicketOrder::lockForUpdate()->find($order->id);
                            if ($lockedOrder && $lockedOrder->status !== 'paid') {
                                $lockedOrder->update([
                                    'status' => 'paid',
                                    'paid_at' => now(),
                                    'payment_method_detail' => $status->payment_type ?? null,
                                    'payment_channel' => $status->bank ?? $status->store ?? $status->payment_type ?? null,
                                ]);
                                $lockedOrder->generateTicketNumber();
                            }
                        });
                        $order->refresh();
                    } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                        $order->update(['status' => 'cancelled']);
                        $order->refresh();
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to sync order status', [
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return view('user.tickets.my-tickets', compact('orders'));
    }

    /**
     * Retrieve tickets by email.
     * NEW-01: Restricted to authenticated user's own email to prevent IDOR.
     */
    public function retrieveTickets(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = Auth::guard('web')->user();

        // Only allow retrieving own tickets — prevents IDOR
        $orders = TicketOrder::with('ticket.place')
            ->where('customer_email', $user->email)
            ->latest()
            ->get();

        return view('user.tickets.my-tickets', compact('orders'));
    }

    /**
     * Show ticket view (for printing/downloading).
     * SCAN-05: Filter by email in query to prevent order existence oracle.
     */
    public function downloadTicket($orderNumber)
    {
        $user = Auth::guard('web')->user();
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->where('customer_email', $user->email)
            ->firstOrFail();

        if (! $order->ticket_number) {
            abort(404, 'Tiket belum diterbitkan via pembayaran.');
        }

        return view('user.tickets.download', compact('order'));
    }

    /**
     * Download ticket QR code as PNG.
     */
    public function downloadQrCode($orderNumber)
    {
        $user = Auth::guard('web')->user();
        $order = TicketOrder::where('order_number', $orderNumber)
            ->where('customer_email', $user->email)
            ->firstOrFail();

        if (! $order->ticket_number) {
            abort(404, 'Tiket belum diterbitkan.');
        }

        // Generate QR Code Matrix from Ticket Number
        $matrix = Encoder::encode(
            $order->ticket_number,
            ErrorCorrectionLevel::H(),
            'UTF-8'
        )->getMatrix();

        // Render using GD
        // Target approx 1000px
        $matrixWidth = $matrix->getWidth();
        $borderSize = 10; // Increased padding modules (was 4)
        $totalModules = $matrixWidth + ($borderSize * 2);

        // Calculate pixel size to get closest to 1000px
        $pixelSize = (int) (1000 / $totalModules);
        $imageWidth = $totalModules * $pixelSize;

        $image = imagecreate($imageWidth, $imageWidth);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0); // Pure black for better contrast

        // Fill background
        imagefill($image, 0, 0, $white);

        // Draw QR code
        for ($y = 0; $y < $matrixWidth; $y++) {
            for ($x = 0; $x < $matrixWidth; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    imagefilledrectangle(
                        $image,
                        ($x + $borderSize) * $pixelSize,
                        ($y + $borderSize) * $pixelSize,
                        ($x + $borderSize + 1) * $pixelSize,
                        ($y + $borderSize + 1) * $pixelSize,
                        $black
                    );
                }
            }
        }

        // Capture output buffer as JPG
        ob_start();
        imagejpeg($image, null, 100); // 100% quality
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Return as download
        return response($imageData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="ticket-'.$order->ticket_number.'.jpg"');
    }

    /**
     * Show ticket QR code inline (for image src).
     */
    public function showQrCode($orderNumber)
    {
        $user = Auth::guard('web')->user();
        $order = TicketOrder::where('order_number', $orderNumber)
            ->where('customer_email', $user->email)
            ->firstOrFail();

        if (! $order->ticket_number) {
            abort(404);
        }

        // Generate QR Code Matrix from Ticket Number
        $matrix = Encoder::encode(
            $order->ticket_number,
            ErrorCorrectionLevel::H(),
            'UTF-8'
        )->getMatrix();

        // Render using GD
        // Target approx 500px for display
        $matrixWidth = $matrix->getWidth();
        $borderSize = 4;
        $totalModules = $matrixWidth + ($borderSize * 2);

        $pixelSize = (int) (500 / $totalModules);
        $imageWidth = $totalModules * $pixelSize;

        $image = imagecreate($imageWidth, $imageWidth);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $matrixWidth; $y++) {
            for ($x = 0; $x < $matrixWidth; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    imagefilledrectangle(
                        $image,
                        ($x + $borderSize) * $pixelSize,
                        ($y + $borderSize) * $pixelSize,
                        ($x + $borderSize + 1) * $pixelSize,
                        ($y + $borderSize + 1) * $pixelSize,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)
            ->header('Content-Type', 'image/png');
    }

    /**
     * Show payment method selection page (custom UI)
     */
    public function payment($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        // If already paid, redirect to success
        if ($order->status === 'paid') {
            return redirect()->route('tickets.payment.success', $orderNumber)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        // If cancelled, redirect to failed page
        if ($order->status === 'cancelled') {
            return redirect()->route('tickets.payment.failed', $orderNumber);
        }

        return view('user.tickets.payment', compact('order'));
    }

    /**
     * Process payment — charge via Core API with selected method
     */
    public function processPayment(Request $request, $orderNumber)
    {
        $request->validate([
            'payment_type' => 'required|in:qris,gopay,shopeepay,bank_transfer,echannel',
            'bank' => 'required_if:payment_type,bank_transfer|nullable|in:bca,bni,bri',
        ]);

        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        if ($order->status === 'paid') {
            return redirect()->route('tickets.payment.success', $orderNumber);
        }

        if ($order->status === 'cancelled') {
            $order->update([
                'status' => 'pending',
                'payment_gateway_id' => null,
            ]);
        }

        $paymentType = $request->input('payment_type');
        $bank = $request->input('bank');

        try {
            // If there's an existing pending transaction, cancel it first
            if ($order->payment_gateway_id) {
                try {
                    $this->midtransService->cancelTransaction($order->payment_gateway_id);
                } catch (\Exception $e) {
                    Log::info('Could not cancel previous transaction', [
                        'order_number' => $orderNumber,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $response = $this->midtransService->createCoreCharge($order, $paymentType, $bank);
            $paymentData = $this->midtransService->extractPaymentData($response, $paymentType, $bank);

            // Store payment data in session for the status page
            session()->put("payment_data.{$orderNumber}", $paymentData);

            $order->refresh();

            return redirect()->route('tickets.payment.status', $orderNumber);

        } catch (\Exception $e) {
            Log::error('Payment charge failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('tickets.payment', $orderNumber)
                ->with('error', 'Gagal memproses pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Show payment status page — displays QR code, VA number, or deeplink
     */
    public function paymentStatus($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        // If already paid, redirect to success
        if ($order->status === 'paid') {
            return redirect()->route('tickets.payment.success', $orderNumber);
        }

        // Get payment data from session or database
        $paymentData = session("payment_data.{$orderNumber}") ?? $order->payment_info;

        // If no payment data, try to get from Midtrans API
        if (! $paymentData && $order->payment_gateway_id) {
            try {
                $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                $paymentType = $status->payment_type ?? $order->payment_method_detail;
                $bank = $order->payment_channel;
                $paymentData = $this->midtransService->extractPaymentData($status, $paymentType, $bank);

                // Update order with latest info
                $order->update(['payment_info' => $paymentData]);
            } catch (\Exception $e) {
                return redirect()->route('tickets.payment', $orderNumber)
                    ->with('error', 'Sesi pembayaran expired. Silakan pilih metode pembayaran lagi.');
            }
        }

        if (! $paymentData) {
            return redirect()->route('tickets.payment', $orderNumber)
                ->with('error', 'Silakan pilih metode pembayaran.');
        }

        return view('user.tickets.payment-status', compact('order', 'paymentData'));
    }

    /**
     * Handle successful payment — show success page
     * NEW-02: Atomic payment status update with lockForUpdate.
     */
    public function paymentSuccess(Request $request, $orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        // If already paid, show success page
        if ($order->status === 'paid') {
            return view('user.tickets.payment-success', compact('order'));
        }

        // Try to verify with Midtrans API
        if ($order->status === 'pending' && $order->payment_gateway_id) {
            try {
                $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                $transactionStatus = $status->transaction_status ?? null;
                $fraudStatus = $status->fraud_status ?? 'accept';

                if ($transactionStatus === 'settlement' ||
                    ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                    // Atomic update with lock to prevent race condition
                    DB::transaction(function () use ($order, $status) {
                        $lockedOrder = TicketOrder::lockForUpdate()->find($order->id);
                        if ($lockedOrder && $lockedOrder->status !== 'paid') {
                            $lockedOrder->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                                'payment_method_detail' => $status->payment_type ?? null,
                                'payment_channel' => $status->bank ?? $status->store ?? $status->payment_type ?? null,
                            ]);
                            $lockedOrder->generateTicketNumber();
                        }
                    });
                    $order->refresh();

                    return view('user.tickets.payment-success', compact('order'));
                }

                if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $order->update(['status' => 'cancelled']);

                    return redirect()->route('tickets.payment.failed', $orderNumber);
                }
            } catch (\Exception $e) {
                Log::error('Failed to verify payment status', [
                    'order_number' => $orderNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Still pending — redirect to status page
        return redirect()->route('tickets.payment.status', $orderNumber);
    }

    /**
     * Handle payment failed callback
     */
    public function paymentFailed($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        return view('user.tickets.payment-failed', compact('order'));
    }

    /**
     * Check payment status via Midtrans API (AJAX)
     */
    public function checkStatus($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        if ($order->status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Pembayaran sudah diterima.',
            ]);
        }

        if (! $order->payment_gateway_id) {
            return response()->json([
                'success' => false,
                'status' => $order->status,
                'message' => 'Belum ada transaksi pembayaran.',
            ]);
        }

        try {
            $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
            $transactionStatus = $status->transaction_status ?? 'unknown';
            $fraudStatus = $status->fraud_status ?? 'accept';

            if ($transactionStatus === 'settlement' ||
                ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                // NEW-02: Atomic update with lock to prevent race condition
                DB::transaction(function () use ($order, $status) {
                    $lockedOrder = TicketOrder::lockForUpdate()->find($order->id);
                    if ($lockedOrder && $lockedOrder->status !== 'paid') {
                        $lockedOrder->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'payment_method_detail' => $status->payment_type ?? null,
                            'payment_channel' => $status->bank ?? $status->store ?? $status->payment_type ?? null,
                        ]);
                        $lockedOrder->generateTicketNumber();
                    }
                });

                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'message' => 'Pembayaran berhasil dikonfirmasi!',
                ]);
            }

            if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $order->update(['status' => 'cancelled']);

                return response()->json([
                    'success' => true,
                    'status' => 'cancelled',
                    'message' => 'Transaksi dibatalkan/kedaluwarsa.',
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $transactionStatus,
                'message' => $transactionStatus === 'pending' ? 'Menunggu pembayaran...' : "Status: {$transactionStatus}",
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to check payment status', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => $order->status,
                'message' => 'Gagal mengecek status pembayaran.',
            ]);
        }
    }

    /**
     * Cancel a pending order
     * SCAN-01: Uses DB::transaction + lockForUpdate to prevent cancel-vs-pay race.
     * SCAN-08: Logs successful cancellations.
     */
    public function cancelOrder($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        return DB::transaction(function () use ($order, $orderNumber) {
            // Re-fetch with lock to prevent race condition with webhook settlement
            $lockedOrder = TicketOrder::lockForUpdate()->find($order->id);

            if ($lockedOrder->status !== 'pending') {
                return back()->with('error', 'Hanya pesanan pending yang bisa dibatalkan.');
            }

            if ($lockedOrder->payment_gateway_id) {
                try {
                    $this->midtransService->cancelTransaction($lockedOrder->payment_gateway_id);
                } catch (\Exception $e) {
                    Log::warning('Could not cancel Midtrans transaction', [
                        'order_number' => $orderNumber,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $lockedOrder->update(['status' => 'cancelled']);

            Log::info('Order cancelled by user', [
                'order_number' => $orderNumber,
                'user_email' => auth()->user()->email,
                'had_payment' => (bool) $lockedOrder->payment_gateway_id,
            ]);

            return redirect()->route('tickets.my')
                ->with('success', 'Pesanan berhasil dibatalkan.');
        });
    }

    /**
     * Retry payment — redirect to payment method selection
     */
    public function retryPayment($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->verifyOrderOwnership($order);

        if (! in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Pembayaran tidak dapat diulang untuk pesanan ini.');
        }

        if ($order->status === 'cancelled') {
            $order->update([
                'status' => 'pending',
                'payment_gateway_id' => null,
            ]);
        }

        return redirect()->route('tickets.payment', $order->order_number);
    }
}
