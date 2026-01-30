<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$app->make(Kernel::class)->bootstrap();

use App\Models\Place;

echo "Checking Place count...\n";
$count = Place::count();
echo "Total Places in DB: " . $count . "\n";

echo "Generating Features...\n";
$features = Place::with('category')
    ->get()
    ->map(function (Place $place) {
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $place->id,
                'name' => $place->name,
                'slug' => $place->slug,
            ],
        ];
    });

echo "Generated Features Count: " . $features->count() . "\n";

if ($features->isEmpty()) {
    echo "WARNING: No features generated!\n";
} else {
    echo "Sample Feature slug: " . $features->first()['properties']['slug'] . "\n";
}
