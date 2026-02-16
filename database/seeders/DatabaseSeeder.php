<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First: Create roles and permissions
        $this->call(RolePermissionSeeder::class);
        
        // Populate Indonesia Region Data (Provinces, Cities, Districts, Villages)
        $this->call(\Laravolt\Indonesia\Seeds\DatabaseSeeder::class);

        // Second: Create admin users and assign roles
        $this->call(AdminUserSeeder::class);
        $this->call(SampleAdminSeeder::class);

        // Third: Create content
        // $this->call(CategorySeeder::class); // Removed as logic moved to PariwisataSeeder
        $this->call(PariwisataSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(BoundarySeeder::class);
        $this->call(DestinasiImageSeeder::class);
        $this->call(JeparaEventSeeder::class);
        // $this->call(DummyTicketSeeder::class);

        // Fourth: Assign ownership to existing content
        $this->call(AssignOwnershipSeeder::class);

    }
}
