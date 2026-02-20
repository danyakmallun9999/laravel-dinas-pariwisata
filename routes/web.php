<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\BoundaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/explore-map', [WelcomeController::class, 'exploreMap'])->name('explore.map');
Route::get('/places.geojson', [WelcomeController::class, 'geoJson'])->name('places.geojson');
Route::get('/search/places', [WelcomeController::class, 'searchPlaces'])->name('search.places');
Route::get('/boundaries.geojson', [WelcomeController::class, 'boundariesGeoJson'])->name('boundaries.geojson');
Route::get('/infrastructures.geojson', [WelcomeController::class, 'infrastructuresGeoJson'])->name('infrastructures.geojson');
Route::get('/land_uses.geojson', [WelcomeController::class, 'landUsesGeoJson'])->name('land_uses.geojson');

Route::get('/berita', [WelcomeController::class, 'posts'])->name('posts.index');
Route::get('/berita/{post:slug}', [WelcomeController::class, 'showPost'])->name('posts.show');
Route::get('/destinasi', [WelcomeController::class, 'places'])->name('places.index');
Route::get('/destinasi/{place:slug}', [WelcomeController::class, 'showPlace'])->name('places.show');
Route::get('/calendar-of-events', [App\Http\Controllers\Public\EventController::class, 'index'])->name('events.public.index');
Route::get('/calendar-of-events/{event:slug}', [App\Http\Controllers\Public\EventController::class, 'show'])->name('events.public.show');
Route::get('/budaya/{slug}', [WelcomeController::class, 'showCulture'])->name('culture.show');
Route::get('/budaya', [WelcomeController::class, 'culture'])->name('culture.index');
Route::get('/kuliner/{slug}', [WelcomeController::class, 'showCulinary'])->name('culinary.show');

// Google OAuth routes (for public users)
if (config('features.google_login_enabled')) {
    Route::get('/auth/google/login', [GoogleAuthController::class, 'showLoginPage'])->name('auth.google.login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
}
Route::post('/auth/logout', [GoogleAuthController::class, 'logout'])->name('auth.user.logout');

// E-Ticket Feature (conditionally enabled via config/features.php)
if (config('features.e_ticket_enabled')) {
    // E-Ticket routes - listing (Public)
    Route::get('/e-tiket', [App\Http\Controllers\Public\TicketController::class, 'index'])->name('tickets.index');

    // Protected E-Ticket routes - require Google authentication
    // All user-specific ticket routes are grouped under /tiket-saya prefix
    Route::middleware('auth.user')->prefix('tiket-saya')->group(function () {
        // Ticket Management
        Route::get('/', [App\Http\Controllers\Public\TicketController::class, 'myTickets'])->name('tickets.my');
        Route::post('/retrieve', [App\Http\Controllers\Public\TicketController::class, 'retrieveTickets'])
            ->middleware('throttle:10,1')
            ->name('tickets.retrieve');
        Route::get('/download/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'downloadTicket'])->name('tickets.download');
        Route::get('/download-qr/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'downloadQrCode'])->name('tickets.download-qr');
        Route::get('/show-qr/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'showQrCode'])->name('tickets.show-qr');

        // Booking Routes
        Route::post('/book', [App\Http\Controllers\Public\Ticket\BookingController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('booking.store');
        Route::get('/book/checkout', [App\Http\Controllers\Public\Ticket\BookingController::class, 'checkout'])->name('booking.checkout');
        Route::post('/book/checkout', [App\Http\Controllers\Public\Ticket\BookingController::class, 'process'])
            ->middleware('throttle:5,1')
            ->name('booking.process');
        Route::get('/confirmation/{orderNumber}', [App\Http\Controllers\Public\Ticket\BookingController::class, 'confirmation'])->name('booking.confirmation');

        // Payment Routes
        Route::get('/payment/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'process'])->name('payment.process');
        Route::get('/payment-status/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'status'])->name('payment.status');
        Route::get('/payment-success/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment-failed/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'failed'])->name('payment.failed');

        // Payment Actions
        Route::get('/check-status/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'check'])->name('payment.check');
        Route::post('/cancel/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::post('/retry-payment/{orderNumber}', [App\Http\Controllers\Public\Ticket\PaymentController::class, 'retry'])->name('payment.retry');
    });

    // E-Ticket detail (Public) - wildcard route MUST be last to avoid catching specific routes above
    Route::get('/e-tiket/{ticket}', [App\Http\Controllers\Public\TicketController::class, 'show'])->name('tickets.show');

}

// Webhook route (Always active to handle pending transactions even if feature is disabled)
Route::post('/webhooks/midtrans', [App\Http\Controllers\WebhookController::class, 'handle'])
    ->middleware(['throttle:60,1', 'midtrans.ip'])
    ->name('webhooks.midtrans');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth:admin', 'verified'])->name('dashboard');

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Users routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Places routes
    Route::get('/places', [AdminController::class, 'placesIndex'])->name('places.index');
    Route::get('/places/create', [AdminController::class, 'create'])->name('places.create');
    Route::post('/places', [AdminController::class, 'store'])->name('places.store');
    Route::get('/places/{place}/edit', [AdminController::class, 'edit'])->name('places.edit');
    Route::put('/places/{place}', [AdminController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [AdminController::class, 'destroy'])->name('places.destroy');
    Route::delete('/places/images/{placeImage}', [AdminController::class, 'destroyImage'])->name('places.images.destroy');

    Route::delete('/places/images/{placeImage}', [AdminController::class, 'destroyImage'])->name('places.images.destroy');

    // Culture routes
    Route::delete('/cultures/images/{image}', [\App\Http\Controllers\Admin\CultureController::class, 'destroyImage'])->name('cultures.images.destroy');
    Route::resource('cultures', \App\Http\Controllers\Admin\CultureController::class);


    // Categories routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    // Product routes

    // Post routes
    Route::post('/posts/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('posts.uploadImage');
    Route::post('/posts/translate', [\App\Http\Controllers\Admin\TranslationController::class, 'translate'])->name('posts.translate');
    Route::resource('posts', \App\Http\Controllers\PostController::class);
    Route::post('/events/translate', [\App\Http\Controllers\Admin\TranslationController::class, 'translate'])->name('events.translate');
    Route::get('/events/calendar', [\App\Http\Controllers\Admin\EventController::class, 'calendar'])->name('events.calendar');
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);

    // Ticket routes — conditionally enabled via config/features.php
    if (config('features.e_ticket_enabled')) {
        // Ticket routes — require ticket-related permissions
        Route::middleware('permission:view all tickets')->group(function () {
            Route::get('/tickets/dashboard', [App\Http\Controllers\Admin\TicketDashboardController::class, 'index'])->name('tickets.dashboard');
            Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
            Route::get('ticket-orders', [\App\Http\Controllers\Admin\TicketController::class, 'orders'])->name('tickets.orders');
        });

        // Riwayat Penjualan — accessible by users with view all tickets OR view own financial reports
        Route::middleware('permission:view all tickets|view own financial reports')
            ->get('ticket-history', [\App\Http\Controllers\Admin\TicketController::class, 'history'])
            ->name('tickets.history');

        // HIGH-01: QR Scan routes — require explicit 'scan tickets' permission
        Route::middleware('permission:scan tickets')->group(function () {
            Route::get('/scan', [App\Http\Controllers\Admin\ScanController::class, 'index'])->name('scan.index');
            Route::post('/scan', [App\Http\Controllers\Admin\ScanController::class, 'store'])
                ->middleware('throttle:30,1')
                ->name('scan.store');
        });

        // HIGH-01: Financial Reports — require financial report permissions
        Route::middleware('permission:view all financial reports|view own financial reports')->prefix('reports/financial')->name('reports.financial.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FinancialReportController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\Admin\FinancialReportController::class, 'export'])
                ->middleware('permission:export financial reports')
                ->name('export');
        });
    }
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Localization Route
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Location API routes
Route::prefix('api/locations')->name('api.locations.')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\Public\LocationController::class, 'provinces'])->name('provinces');
    Route::get('/cities', [App\Http\Controllers\Public\LocationController::class, 'cities'])->name('cities');
});

require __DIR__.'/auth.php';
