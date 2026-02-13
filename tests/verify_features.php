<?php

use App\Models\User;
use App\Models\Place;
use App\Services\DashboardService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

// Ensure Roles exist
if (Role::count() == 0) {
    echo "Roles not found. Run seeder first.\n";
    exit;
}

// Clear cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Debug Roles
echo "Roles in DB: \n";
foreach(Role::all() as $r) {
    echo "- {$r->name} ({$r->guard_name})\n";
}

// 1. Setup Data
$superAdmin = User::role('super_admin')->first();
if (!$superAdmin) {
    echo "Creating Super Admin...\n";
    $superAdmin = User::factory()->create(['name' => 'Super Test', 'email' => 'supertest@example.com']);
    $role = Role::findByName('super_admin', 'admin');
    $superAdmin->assignRole($role);
}

$manager = User::where('email', 'managertest@example.com')->first();
if (!$manager) {
    echo "Creating Manager...\n";
    $manager = User::factory()->create(['name' => 'Manager Test', 'email' => 'managertest@example.com']);
    $role = Role::findByName('pengelola_wisata', 'admin');
    $manager->assignRole($role);
}

$place1 = Place::first();
if (!$place1) {
    echo "No places found. Please seed places.\n";
    exit;
}

// Assign Place 1 to Manager
echo "Assigning Place {$place1->name} to Manager {$manager->name}...\n";
$place1->update(['created_by' => $manager->id]);

// 2. Test Dashboard Service as Super Admin
echo "\n--- Testing Super Admin View ---\n";
Auth::login($superAdmin);
$service = new DashboardService();
$statsSuper = $service->getDashboardStats();
echo "Super Admin Places Count: " . $statsSuper['places_count'] . "\n";
echo "Total Places in DB: " . Place::count() . "\n";

if ($statsSuper['places_count'] == Place::count()) {
    echo "PASS: Super Admin sees all places.\n";
} else {
    echo "FAIL: Super Admin count mismatch.\n";
}

// 3. Test Dashboard Service as Manager
echo "\n--- Testing Manager View ---\n";
Auth::login($manager);
$statsManager = $service->getDashboardStats();
echo "Manager Places Count: " . $statsManager['places_count'] . "\n";
echo "Expected (Owned): " . Place::where('created_by', $manager->id)->count() . "\n";

if ($statsManager['places_count'] == Place::where('created_by', $manager->id)->count()) {
    echo "PASS: Manager sees only owned places.\n";
} else {
    echo "FAIL: Manager count mismatch.\n";
}

// 4. Test filtering specific stats
// Top Categories
$topCategories = $statsManager['categories'];
echo "Manager Categories Count: " . $topCategories->count() . "\n";
// Check if categories have correct places_count (should be filtered)
foreach($topCategories as $cat) {
    echo "Category {$cat->name}: {$cat->places_count} places\n";
}
