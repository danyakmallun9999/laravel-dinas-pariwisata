<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Attempting to create category...\n";
    $c = App\Models\Category::firstOrCreate(['name' => 'Debug Cat'], ['slug' => 'debug-cat', 'icon_class'=>'test']);
    echo "Category ID: " . $c->id . "\n";

    echo "Attempting to create place...\n";
    $p = App\Models\Place::create([
        'name' => 'Debug Place',
        'category_id' => $c->id,
        'description' => 'Test Desc',
        'ticket_price' => 'Gratis Test',
        'latitude' => -6.5,
        'longitude' => 110.5,
        'slug' => 'debug-place-' . rand(1000,9999)
    ]);
    echo "Place created! ID: " . $p->id . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
