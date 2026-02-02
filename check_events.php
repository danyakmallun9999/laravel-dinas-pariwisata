<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Event;

$events = Event::orderBy('start_date')->get();

echo "Total Events: " . $events->count() . "\n";

$grouped = $events->groupBy(function($e) {
    return $e->start_date->format('Y-m');
});

foreach($grouped as $month => $list) {
    echo "$month: " . $list->count() . " events\n";
}
