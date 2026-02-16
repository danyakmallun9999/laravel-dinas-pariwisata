<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketOrder;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
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
     * Show my tickets page — auto-loads orders for the logged-in user.
     */
    public function myTickets()
    {
        $user = Auth::guard('web')->user();

        $orders = TicketOrder::with('ticket.place')
            ->where('customer_email', $user->email)
            ->latest()
            ->get();
            
        // Validasi status expiry untuk semua order pending
        $orders->each(function ($order) {
            $order->checkAutoCancel();
        });

        return response()->view('user.tickets.my-tickets', compact('orders'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Retrieve tickets by email.
     */
    public function retrieveTickets(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = Auth::guard('web')->user();

        $orders = TicketOrder::with('ticket.place')
            ->where('customer_email', $user->email)
            ->latest()
            ->get();

        // Validasi status expiry untuk semua order pending
        $orders->each(function ($order) {
            $order->checkAutoCancel();
        });

        return response()->view('user.tickets.my-tickets', compact('orders'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Show ticket view (for printing/downloading).
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
        $borderSize = 10;
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
}
