<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$app->make(Kernel::class)->bootstrap();

use App\Models\Place;

$place = Place::where('name', 'like', '%Bandengan%')->with('images')->first();

if ($place) {
    echo "Found Place: " . $place->name . "\n";
    echo "Main Image Path: " . $place->image_path . "\n";
    echo "Gallery Images Count: " . $place->images->count() . "\n";
    foreach ($place->images as $img) {
        echo " - " . $img->image_path . "\n";
    }
} else {
    echo "Place not found.\n";
}
