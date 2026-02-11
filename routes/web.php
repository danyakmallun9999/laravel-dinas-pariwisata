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
Route::get('/kuliner/{slug}', [WelcomeController::class, 'showCulinary'])->name('culinary.show');

// Google OAuth routes (for public users)
Route::get('/auth/google/login', [GoogleAuthController::class, 'showLoginPage'])->name('auth.google.login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/auth/logout', [GoogleAuthController::class, 'logout'])->name('auth.user.logout');

// E-Ticket routes - listing (Public)
Route::get('/e-tiket', [App\Http\Controllers\Public\TicketController::class, 'index'])->name('tickets.index');

// Protected E-Ticket routes - require Google authentication
// All user-specific ticket routes are grouped under /tiket-saya prefix
Route::middleware('auth.user')->prefix('tiket-saya')->group(function () {
    Route::get('/', [App\Http\Controllers\Public\TicketController::class, 'myTickets'])->name('tickets.my');
    Route::post('/book', [App\Http\Controllers\Public\TicketController::class, 'book'])->name('tickets.book');
    Route::get('/confirmation/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'confirmation'])->name('tickets.confirmation');
    Route::get('/download/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'downloadTicket'])->name('tickets.download');
    Route::get('/download-qr/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'downloadQrCode'])->name('tickets.download-qr');
    Route::get('/payment/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'payment'])->name('tickets.payment');
    Route::get('/payment-success/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'paymentSuccess'])->name('tickets.payment.success');
    Route::get('/payment-failed/{orderNumber}', [App\Http\Controllers\Public\TicketController::class, 'paymentFailed'])->name('tickets.payment.failed');
    Route::post('/retrieve', [App\Http\Controllers\Public\TicketController::class, 'retrieveTickets'])->name('tickets.retrieve');
});

// E-Ticket detail (Public) - wildcard route MUST be last to avoid catching specific routes above
Route::get('/e-tiket/{ticket}', [App\Http\Controllers\Public\TicketController::class, 'show'])->name('tickets.show');

// Webhook route (no CSRF protection)
Route::post('/webhooks/xendit', [App\Http\Controllers\WebhookController::class, 'handle'])->name('webhooks.xendit');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth:admin', 'verified'])->name('dashboard');

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Places routes
    Route::get('/places', [AdminController::class, 'placesIndex'])->name('places.index');
    Route::get('/places/create', [AdminController::class, 'create'])->name('places.create');
    Route::post('/places', [AdminController::class, 'store'])->name('places.store');
    Route::get('/places/{place}/edit', [AdminController::class, 'edit'])->name('places.edit');
    Route::put('/places/{place}', [AdminController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [AdminController::class, 'destroy'])->name('places.destroy');
    Route::delete('/places/images/{placeImage}', [AdminController::class, 'destroyImage'])->name('places.images.destroy');

    Route::delete('/places/images/{placeImage}', [AdminController::class, 'destroyImage'])->name('places.images.destroy');

    // Categories routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    // Product routes

    // Post routes
    Route::post('/posts/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('posts.uploadImage');
    Route::post('/posts/translate', [\App\Http\Controllers\Admin\TranslationController::class, 'translate'])->name('posts.translate');
    Route::resource('posts', \App\Http\Controllers\PostController::class);
    Route::post('/events/translate', [\App\Http\Controllers\Admin\TranslationController::class, 'translate'])->name('events.translate');
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);

    // Ticket routes
    Route::get('/tickets/dashboard', [App\Http\Controllers\Admin\TicketDashboardController::class, 'index'])->name('tickets.dashboard');
    Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
    Route::get('ticket-orders', [\App\Http\Controllers\Admin\TicketController::class, 'orders'])->name('tickets.orders');
    Route::post('ticket-orders/{order}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateOrderStatus'])->name('tickets.orders.updateStatus');
    Route::delete('ticket-orders/{order}', [\App\Http\Controllers\Admin\TicketController::class, 'destroyOrder'])->name('tickets.orders.destroy');
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

require __DIR__.'/auth.php';
