<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'santo@jepara.co.id')->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: " . $user->name . "\n";
echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
echo "Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n";

if (!$user->hasRole('pengelola_wisata')) {
    $user->assignRole('pengelola_wisata');
    echo "Assigned 'pengelola_wisata' role.\n";
} else {
    echo "User already has 'pengelola_wisata' role.\n";
}

// Check destinations
$places = $user->ownedPlaces;
echo "Owned Places: " . $places->count() . "\n";
if ($places->count() > 0) {
    foreach ($places as $place) {
        echo "- " . $place->name . " (ID: " . $place->id . ")\n";
    }
} else {
    echo "User has NO places.\n";
}
