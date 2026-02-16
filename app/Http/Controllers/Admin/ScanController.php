<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    /**
     * Show the scanner page.
     */
    public function index()
    {
        return view('admin.scan.index');
    }

    /**
     * Validate scanned QR code.
     * Uses DB transaction + lockForUpdate to prevent race condition (double-scan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $inputData = $request->qr_data;
            Log::debug('QR Scan Input', ['prefix' => substr($inputData, 0, 10) . '...']);

            $ticketNumber = null;

            // 1. Try to decode as JSON
            $qrJson = json_decode($inputData, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($qrJson) && isset($qrJson['order_number'])) {
                // JSON format — look up by order_number to get ticket_number
                $orderByNumber = TicketOrder::where('order_number', $qrJson['order_number'])->first();
                $ticketNumber = $orderByNumber?->ticket_number;
            } else {
                // 2. Plain text = ticket number directly
                $ticketNumber = trim($inputData, '"\'  ');
            }

            if (empty($ticketNumber)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format QR tidak dikenali!',
                    'data' => ['ticket_number' => substr($inputData, 0, 20)]
                ], 400);
            }

            // Atomic scan using DB transaction + row lock to prevent double-scan
            $result = DB::transaction(function () use ($ticketNumber) {
                $order = TicketOrder::with('ticket.place')
                    ->where('ticket_number', $ticketNumber)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    return [
                        'code' => 404,
                        'response' => [
                            'status' => 'error',
                            'message' => 'Tiket tidak ditemukan di sistem!',
                            'data' => ['ticket_number' => substr($ticketNumber, 0, 8) . '***']
                        ]
                    ];
                }

                $orderData = [
                    'customer_name' => $order->customer_name,
                    'ticket_name' => $order->ticket->name,
                    'quantity' => $order->quantity,
                    'place_name' => $order->ticket->place->name,
                    'order_number' => $order->order_number,
                    'check_in_time' => $order->check_in_time ? $order->check_in_time->format('H:i:s') : null,
                    'visit_date' => $order->visit_date->format('d M Y'),
                    'status' => $order->status,
                ];

                // Check Payment Status
                if ($order->status !== 'paid') {
                    return [
                        'code' => 400,
                        'response' => [
                            'status' => 'error',
                            'message' => 'Tiket belum dibayar! Status: ' . ucfirst($order->status),
                            'data' => $orderData,
                        ]
                    ];
                }

                // Check Visit Date
                if (!$order->visit_date->isToday()) {
                    return [
                        'code' => 400,
                        'response' => [
                            'status' => 'error',
                            'message' => 'Tanggal tiket tidak sesuai! Tiket untuk: ' . $order->visit_date->format('d M Y'),
                            'data' => $orderData,
                        ]
                    ];
                }

                // Check if Already Used (inside lock — race-safe)
                if ($order->check_in_time !== null) {
                    Log::debug('Scan rejected: already used', ['order' => $order->order_number]);
                    return [
                        'code' => 400,
                        'response' => [
                            'status' => 'error',
                            'message' => 'Tiket SUDAH DIGUNAKAN pada ' . $order->check_in_time->format('H:i'),
                            'data' => $orderData,
                        ]
                    ];
                }

                // Mark as Used (atomic — inside transaction + lock)
                // SCAN-06: Track which operator scanned the ticket
                $order->check_in_time = now();
                $order->status = 'used';
                $order->scanned_by = auth()->id();
                $order->save();

                Log::info('Ticket scanned successfully', ['order' => $order->order_number]);

                return [
                    'code' => 200,
                    'response' => [
                        'status' => 'success',
                        'message' => 'Tiket Valid! Silakan Masuk.',
                        'data' => [
                            'customer_name' => $order->customer_name,
                            'ticket_name' => $order->ticket->name,
                            'quantity' => $order->quantity,
                            'place_name' => $order->ticket->place->name,
                            'order_number' => $order->order_number,
                            'check_in_time' => $order->check_in_time->format('H:i:s'),
                        ]
                    ]
                ];
            });

            return response()->json($result['response'], $result['code']);

        } catch (\Exception $e) {
            Log::error('Scan error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem.',
                'data' => []
            ], 500);
        }
    }
}

