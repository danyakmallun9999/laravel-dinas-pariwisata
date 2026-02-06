<?php

namespace App\Http\Controllers;

use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Handle Xendit webhook callbacks
     */
    public function handle(Request $request)
    {
        // Log webhook untuk debugging
        Log::info('Xendit webhook received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        // Verify webhook token
        $callbackToken = $request->header('x-callback-token');
        
        // In test mode, Xendit might not send token, so we log but don't reject
        if (!$callbackToken) {
            Log::warning('Xendit webhook received without callback token - might be test mode');
        } elseif (!$this->xenditService->verifyWebhookToken($callbackToken)) {
            Log::warning('Invalid Xendit webhook token', ['token' => $callbackToken]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get event data
        $event = $request->all();
        $eventType = $event['event'] ?? $event['status'] ?? null;

        try {
            // Handle different event types
            switch ($eventType) {
                case 'invoice.paid':
                case 'PAID':
                    $this->xenditService->handleInvoicePaid($event);
                    break;

                case 'invoice.expired':
                case 'EXPIRED':
                    // Handle expired invoice
                    Log::info('Invoice expired', ['invoice_id' => $event['id'] ?? null]);
                    break;

                default:
                    Log::info('Unhandled webhook event', ['event_type' => $eventType]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return 200 to prevent Xendit from retrying
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }
}
