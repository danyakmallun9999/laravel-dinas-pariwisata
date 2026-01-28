<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoundaryController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\LandUseController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/explore-map', [WelcomeController::class, 'exploreMap'])->name('explore.map');
Route::get('/places.geojson', [WelcomeController::class, 'geoJson'])->name('places.geojson');
Route::get('/boundaries.geojson', [WelcomeController::class, 'boundariesGeoJson'])->name('boundaries.geojson');
Route::get('/infrastructures.geojson', [WelcomeController::class, 'infrastructuresGeoJson'])->name('infrastructures.geojson');
Route::get('/land-uses.geojson', [WelcomeController::class, 'landUsesGeoJson'])->name('land_uses.geojson');

Route::get('/berita', [WelcomeController::class, 'posts'])->name('posts.index');
Route::get('/berita/{post:slug}', [WelcomeController::class, 'showPost'])->name('posts.show');
Route::get('/produk/{product:slug}', [WelcomeController::class, 'showProduct'])->name('products.show');
Route::get('/destinasi', [WelcomeController::class, 'places'])->name('places.index');
Route::get('/destinasi/{place:slug}', [WelcomeController::class, 'showPlace'])->name('places.show');
Route::get('/calendar-of-events', [App\Http\Controllers\Public\EventController::class, 'index'])->name('events.public.index');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Places routes
    Route::get('/places', [AdminController::class, 'placesIndex'])->name('places.index');
    Route::get('/places/create', [AdminController::class, 'create'])->name('places.create');
    Route::post('/places', [AdminController::class, 'store'])->name('places.store');
    Route::get('/places/{place}/edit', [AdminController::class, 'edit'])->name('places.edit');
    Route::put('/places/{place}', [AdminController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [AdminController::class, 'destroy'])->name('places.destroy');
    
    // Boundaries routes
    Route::resource('boundaries', BoundaryController::class);
    
    // Infrastructures routes
    Route::resource('infrastructures', InfrastructureController::class);
    
    // Land Uses routes
    Route::resource('land-uses', LandUseController::class)->parameters(['land-uses' => 'landUse']);
    
    // Import routes
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'import'])->name('import.import');
    
    // Reports routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/html', [ReportController::class, 'exportHtml'])->name('reports.export.html');
    
    // Interactive Map route
    Route::get('/map', [MapController::class, 'index'])->name('map.index');

    // Population routes
    Route::get('/population', [\App\Http\Controllers\PopulationController::class, 'index'])->name('population.index');
    Route::get('/population/edit', [\App\Http\Controllers\PopulationController::class, 'edit'])->name('population.edit');
    Route::put('/population', [\App\Http\Controllers\PopulationController::class, 'update'])->name('population.update');

    // Categories routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    
    // Product routes
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    // Post routes
    Route::post('/posts/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('posts.uploadImage');
    Route::resource('posts', \App\Http\Controllers\PostController::class);
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
