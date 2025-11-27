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

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Places routes
    Route::get('/places', [AdminController::class, 'index'])->name('places.index');
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
