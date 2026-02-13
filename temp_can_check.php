<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'santo@jepara.co.id')->first();
if ($user) {
    echo "Can view own destinations: " . ($user->can('view own destinations') ? 'YES' : 'NO') . "\n";
} else {
    echo "User not found\n";
}
