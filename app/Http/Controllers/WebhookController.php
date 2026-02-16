<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans payment notification (webhook)
     */
    public function handle(Request $request)
    {
        // SCAN-03: Only log safe fields — do NOT log full payload (contains signature_key)
        Log::info('Midtrans webhook received', [
            'order_id' => $request->input('order_id'),
            'transaction_status' => $request->input('transaction_status'),
            'payment_type' => $request->input('payment_type'),
        ]);

        try {
            $success = $this->midtransService->handleNotification($request);

            if (!$success) {
                Log::warning('Midtrans notification handling returned false');
            }

            // Always return 200 to Midtrans so it doesn't retry
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return 200 to prevent Midtrans from retrying
            // Do not expose internal error messages to external callers
            return response()->json(['success' => false], 200);
        }
    }
}
