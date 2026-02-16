<?php

namespace App\Http\Controllers\Public\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Ticket\ProcessPaymentRequest;
use App\Models\TicketOrder;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show payment method selection.
     */
    public function show($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->authorize('view', $order);

        // Auto-cancel check
        if ($order->checkAutoCancel()) {
            return redirect()->route('payment.failed', $orderNumber);
        }

        if ($order->status === 'paid') {
            return redirect()->route('payment.success', $orderNumber)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('payment.failed', $orderNumber);
        }

        return view('user.payment.show', compact('order'));
    }

    /**
     * Process payment selection (if retrying or changing method).
     */
    public function process(ProcessPaymentRequest $request, $orderNumber)
    {
        $order = TicketOrder::with('ticket.place')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $this->authorize('pay', $order);

        // Auto-cancel check
        if ($order->checkAutoCancel()) {
            return redirect()->route('payment.failed', $orderNumber);
        }

        if ($order->status === 'paid') {
            return redirect()->route('payment.success', $orderNumber);
        }

        if ($order->status === 'cancelled') {
            // Re-open logic could go here if business rules allow
            // For now, we update status to pending if we allow retry
            // But if it was auto-cancelled due to expiry, we might want to block retry?
            // User requirement: "If user manual URL -> redirect to failed".
            // So if cancelled, we should redirect to failed unless specific retry logic exists.
            // But retry() action exists. process() is form submission.
            // Getting here means they saw the form -> so it wasn't cancelled when they loaded the form.
            // If it is cancelled now, maybe race condition or they clicked late.
            return redirect()->route('payment.failed', $orderNumber);
        }

        $paymentType = $request->input('payment_type');
        $bank = $request->input('bank');

        try {
            // Cancel previous transaction if exists
            if ($order->payment_gateway_id) {
                try {
                    $this->midtransService->cancelTransaction($order->payment_gateway_id);
                } catch (\Exception $e) {
                    // Ignore cancellation errors
                }
            }

            $response = $this->midtransService->createCoreCharge($order, $paymentType, $bank);
            $paymentData = $this->midtransService->extractPaymentData($response, $paymentType, $bank);

            session()->put("payment_data.{$orderNumber}", $paymentData);
            
            // Check expiry again from response
            $expiry = $response->expiry_time ?? now()->addMinutes(2);
            $order->update(['expiry_time' => $expiry]);

            return redirect()->route('payment.status', $orderNumber);

        } catch (\Exception $e) {
            Log::error('Payment processing failed', ['order' => $orderNumber, 'error' => $e->getMessage()]);
            return redirect()->route('payment.show', $orderNumber)
                ->with('error', 'Gagal memproses pembayaran. Silakan coba lagi atau hubungi admin.');
        }
    }

    /**
     * Show Payment Status Page.
     */
    public function status($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')->where('order_number', $orderNumber)->firstOrFail();
        
        $this->authorize('view', $order);

        // Auto-cancel check
        if ($order->checkAutoCancel()) {
             return redirect()->route('payment.failed', $orderNumber);
        }

        if ($order->status === 'paid') {
            return redirect()->route('payment.success', $orderNumber);
        }

        $paymentData = session("payment_data.{$orderNumber}") ?? $order->payment_info;

        if (!$paymentData && $order->payment_gateway_id) {
            // Try to sync
            try {
                $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                $paymentType = $status->payment_type ?? $order->payment_method_detail;
                $bank = $order->payment_channel;
                $paymentData = $this->midtransService->extractPaymentData($status, $paymentType, $bank);
                $order->update(['payment_info' => $paymentData]);
            } catch (\Exception $e) {
                return redirect()->route('payment.show', $orderNumber)->with('error', 'Sesi pembayaran expired.');
            }
        }

        if (!$paymentData) {
            return redirect()->route('payment.show', $orderNumber);
        }

        return view('user.payment.status', compact('order', 'paymentData'));
    }

    /**
     * Success Page.
     */
    public function success($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')->where('order_number', $orderNumber)->firstOrFail();
        $this->authorize('view', $order);

        if ($order->status === 'paid') {
            return view('user.payment.success', compact('order'));
        }

        // Sync check
        if ($order->status === 'pending' && $order->payment_gateway_id) {
            try {
                $status = $this->midtransService->getTransactionStatus($order->payment_gateway_id);
                $transactionStatus = $status->transaction_status ?? null;

                if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                     DB::transaction(function() use ($order, $status) {
                        $locked = TicketOrder::lockForUpdate()->find($order->id);
                        if ($locked->status !== 'paid') {
                            $locked->status = 'paid';
                            $locked->paid_at = now();
                            $locked->payment_method_detail = $status->payment_type ?? null;
                            $locked->save();
                            $locked->generateTicketNumber();
                        }
                     });
                     $order->refresh();
                     return view('user.payment.success', compact('order'));
                }
            } catch (\Exception $e) {}
        }

        return redirect()->route('payment.status', $orderNumber);
    }

    /**
     * Failed Page.
     */
    public function failed($orderNumber)
    {
        $order = TicketOrder::with('ticket.place')->where('order_number', $orderNumber)->firstOrFail();
        $this->authorize('view', $order);

        // HIGH-06: Atomic cancel with DB lock to prevent race with webhook settlement
        if ($order->status === 'pending' && $order->expiry_time && now()->greaterThan($order->expiry_time)) {
            DB::transaction(function () use ($order) {
                $locked = TicketOrder::lockForUpdate()->find($order->id);
                if ($locked && $locked->status === 'pending') {
                    $locked->cancelAndInvalidate();
                }
            });
            $order->refresh();
        }

        return view('user.payment.failed', compact('order'));
    }

    /**
     * AJAX Status Check.
     */
    public function check($orderNumber)
    {
        $order = TicketOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->authorize('view', $order);

        // Check local expiry
        if ($order->checkAutoCancel()) {
             return response()->json([
                 'success' => true,
                 'status' => 'cancelled',
                 'message' => 'Waktu pembayaran habis.',
             ]);
        }

        if ($order->status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Pembayaran sudah diterima.',
            ]);
        }
        
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => true,
                'status' => 'cancelled',
                'message' => 'Transaksi dibatalkan.',
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

            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                DB::transaction(function () use ($order, $status) {
                    $lockedOrder = TicketOrder::lockForUpdate()->find($order->id);
                    if ($lockedOrder && $lockedOrder->status !== 'paid') {
                        $lockedOrder->status = 'paid';
                        $lockedOrder->paid_at = now();
                        $lockedOrder->payment_method_detail = $status->payment_type ?? null;
                        $lockedOrder->save();
                        $lockedOrder->generateTicketNumber();
                    }
                });
                return response()->json(['success' => true, 'status' => 'paid']);
            }

            if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                // MED-08: Atomic cancel with DB lock
                DB::transaction(function () use ($order) {
                    $locked = TicketOrder::lockForUpdate()->find($order->id);
                    if ($locked && $locked->status === 'pending') {
                        $locked->cancelAndInvalidate();
                    }
                });
                return response()->json(['success' => true, 'status' => 'cancelled']);
            }

            return response()->json(['success' => true, 'status' => $transactionStatus]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => $order->status]);
        }
    }

    /**
     * Cancel Action.
     */
    public function cancel(Request $request, $orderNumber)
    {
        $order = TicketOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->authorize('cancel', $order);

        DB::transaction(function() use ($order, $orderNumber) {
            $locked = TicketOrder::lockForUpdate()->find($order->id);
            if ($locked->status === 'pending') {
                if ($locked->payment_gateway_id) {
                    try {
                        $this->midtransService->cancelTransaction($locked->payment_gateway_id);
                    } catch(\Exception $e) {
                         Log::warning('Midtrans cancel failed', ['order' => $orderNumber, 'error' => $e->getMessage()]);
                    }
                }
                // CRIT-01: Use cancelAndInvalidate to clear ticket credentials
                $locked->cancelAndInvalidate();
                Log::info('Order cancelled by user', ['order' => $orderNumber]);
            }
        });

        return redirect()->route('tickets.my')->with('success', 'Pesanan dibatalkan.');
    }

    /**
     * Retry Action.
     */
    public function retry($orderNumber)
    {
        $order = TicketOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->authorize('pay', $order);

        if ($order->status === 'cancelled' || $order->status === 'paid' || 
            ($order->status === 'pending' && $order->expiry_time && now()->greaterThan($order->expiry_time))) {
            return redirect()->route('tickets.index')->with('error', 'Pesanan tidak valid/kadaluwarsa.');
        }

        return redirect()->route('payment.show', $orderNumber);
    }
}
