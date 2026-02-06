<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use App\Models\TicketOrder;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected $invoiceApi;
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key');
        Configuration::setXenditKey($this->secretKey);
        $this->invoiceApi = new InvoiceApi();
    }

    /**
     * Create Xendit invoice for ticket order
     */
    public function createInvoice(TicketOrder $order)
    {
        try {
            $externalId = 'TICKET-' . $order->order_number;
            $amount = $order->total_price;
            
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => "Pembayaran Tiket - {$order->ticket->name}",
                'invoice_duration' => 86400, // 24 hours
                'currency' => 'IDR',
                'reminder_time' => 1,
                'customer' => [
                    'given_names' => $order->customer_name,
                    'email' => $order->customer_email,
                    'mobile_number' => $order->customer_phone,
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email', 'whatsapp'],
                    'invoice_reminder' => ['email', 'whatsapp'],
                    'invoice_paid' => ['email', 'whatsapp'],
                ],
                'success_redirect_url' => route('tickets.payment.success', $order->order_number),
                'failure_redirect_url' => route('tickets.payment.failed', $order->order_number),
                'items' => [
                    [
                        'name' => $order->ticket->name,
                        'quantity' => $order->quantity,
                        'price' => $order->ticket->price,
                        'category' => 'Tiket Wisata',
                    ]
                ],
            ]);

            $invoice = $this->invoiceApi->createInvoice($createInvoiceRequest);

            // Update order with Xendit data
            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'xendit_invoice_url' => $invoice['invoice_url'],
            ]);

            Log::info('Xendit invoice created', [
                'order_number' => $order->order_number,
                'invoice_id' => $invoice['id'],
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Failed to create Xendit invoice', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get invoice details
     */
    public function getInvoice($invoiceId)
    {
        try {
            return $this->invoiceApi->getInvoiceById($invoiceId);
        } catch (\Exception $e) {
            Log::error('Failed to get Xendit invoice', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Expire invoice manually
     */
    public function expireInvoice($invoiceId)
    {
        try {
            return $this->invoiceApi->expireInvoice($invoiceId);
        } catch (\Exception $e) {
            Log::error('Failed to expire Xendit invoice', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook callback token
     */
    public function verifyWebhookToken($callbackToken)
    {
        $webhookToken = config('services.xendit.webhook_token');
        return hash_equals($webhookToken, $callbackToken);
    }

    /**
     * Handle invoice paid event
     */
    public function handleInvoicePaid($invoiceData)
    {
        try {
            $externalId = $invoiceData['external_id'];
            $orderNumber = str_replace('TICKET-', '', $externalId);
            
            $order = TicketOrder::where('order_number', $orderNumber)->first();
            
            if (!$order) {
                Log::warning('Order not found for paid invoice', ['order_number' => $orderNumber]);
                return false;
            }

            if ($order->status === 'paid') {
                Log::info('Order already marked as paid', ['order_number' => $orderNumber]);
                return true;
            }

            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
                'xendit_payment_method' => $invoiceData['payment_method'] ?? null,
                'xendit_payment_channel' => $invoiceData['payment_channel'] ?? null,
            ]);

            Log::info('Order marked as paid', [
                'order_number' => $orderNumber,
                'payment_method' => $invoiceData['payment_method'] ?? null,
            ]);

            // TODO: Send email notification with ticket

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to handle invoice paid event', [
                'error' => $e->getMessage(),
                'invoice_data' => $invoiceData,
            ]);
            return false;
        }
    }
}
