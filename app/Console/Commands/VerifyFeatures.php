<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Place;
use App\Services\DashboardService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class VerifyFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Superadmin Dashboard and User Management Logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Verification...');

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Debug Roles
        $this->info("Roles in DB:");
        foreach(Role::all() as $r) {
            $this->info("- {$r->name} ({$r->guard_name})");
        }

        // 1. Setup Data
        $superAdmin = User::role('super_admin', 'admin')->first();
        if (!$superAdmin) {
            $this->info("Creating Super Admin...");
            $superAdmin = User::factory()->create(['name' => 'Super Test', 'email' => 'supertest@example.com']);
            $role = Role::findByName('super_admin', 'admin');
            $superAdmin->assignRole($role);
        }

        $manager = User::where('email', 'managertest@example.com')->first();
        if (!$manager) {
            $this->info("Creating Manager...");
            $manager = User::factory()->create(['name' => 'Manager Test', 'email' => 'managertest@example.com']);
            $role = Role::findByName('pengelola_wisata', 'admin');
            $manager->assignRole($role);
        }

        $place1 = Place::first();
        if (!$place1) {
            $this->error("No places found. Please seed places.");
            return;
        }

        // Assign Place 1 to Manager
        $this->info("Assigning Place {$place1->name} to Manager {$manager->name}...");
        $place1->update(['created_by' => $manager->id]);

        // 2. Test Dashboard Service as Super Admin
        $this->info("\n--- Testing Super Admin View ---");
        Auth::login($superAdmin);
        $service = new DashboardService();
        $statsSuper = $service->getDashboardStats();
        $this->info("Super Admin Places Count: " . $statsSuper['places_count']);
        $this->info("Total Places in DB: " . Place::count());

        if ($statsSuper['places_count'] == Place::count()) {
            $this->info("PASS: Super Admin sees all places.");
        } else {
            $this->error("FAIL: Super Admin count mismatch.");
        }

        // 3. Test Dashboard Service as Manager
        $this->info("\n--- Testing Manager View ---");
        Auth::login($manager);
        // Re-instantiate service just in case
        $service = new DashboardService();
        $statsManager = $service->getDashboardStats();
        $this->info("Manager Places Count: " . $statsManager['places_count']);
        $expected = Place::where('created_by', $manager->id)->count();
        $this->info("Expected (Owned): " . $expected);

        if ($statsManager['places_count'] == $expected) {
            $this->info("PASS: Manager sees only owned places.");
        } else {
            $this->error("FAIL: Manager count mismatch.");
        }

        // 4. Test filtering specific stats
        $topCategories = $statsManager['categories'];
        $this->info("Manager Categories Count: " . $topCategories->count());
        foreach($topCategories as $cat) {
            $this->info("Category {$cat->name}: {$cat->places_count} places");
        }
        
        $this->info("\nVerification Complete.");
    }
}
