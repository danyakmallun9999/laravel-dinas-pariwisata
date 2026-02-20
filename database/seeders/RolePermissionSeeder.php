<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',

            // Destinations (Places)
            'view all destinations',
            'view own destinations',
            'create destinations',
            'edit all destinations',
            'edit own destinations',
            'delete all destinations',
            'delete own destinations',

            // Events
            'view all events',
            'view own events',
            'create events',
            'edit all events',
            'edit own events',
            'delete all events',
            'delete own events',

            // Posts (Berita)
            'view all posts',
            'view own posts',
            'create posts',
            'edit all posts',
            'edit own posts',
            'delete all posts',
            'delete own posts',

            // E-Tickets
            'view all tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'view all orders',
            'manage order status',
            'scan tickets',

            // Financial Reports
            'view all financial reports',
            'view own financial reports',
            'export financial reports',

            // System Settings
            'manage settings',
            'manage categories',
            'manage culture',
            'view audit logs',
        ];

        // Create all permissions with 'admin' guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin', // Use 'admin' guard for admin panel
            ]);
        }

        // Create roles and assign permissions

        // 1. Super Admin - Full access to everything
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin Wisata - Tourism content and tickets
        $adminWisata = Role::firstOrCreate([
            'name' => 'admin_wisata',
            'guard_name' => 'admin',
        ]);
        $adminWisata->syncPermissions([
            // Destinations - full access
            'view all destinations',
            'create destinations',
            'edit all destinations',
            'delete all destinations',

            // E-Tickets - full access
            'view all tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'view all orders',
            'manage order status',
            'scan tickets',

            // Categories - can manage
            'manage categories',

            // Financial Reports - can view and export all
            'view all financial reports',
            'export financial reports',
        ]);

        // 3. Admin Berita - News and events content
        $adminBerita = Role::firstOrCreate([
            'name' => 'admin_berita',
            'guard_name' => 'admin',
        ]);
        $adminBerita->syncPermissions([
            // Events - full access
            'view all events',
            'create events',
            'edit all events',
            'delete all events',

            // Posts - full access
            'view all posts',
            'create posts',
            'edit all posts',
            'delete all posts',
        ]);

        // 4. Pengelola Wisata - Tourism manager (future role, prepared but not assigned to anyone yet)
        $pengelolaWisata = Role::firstOrCreate([
            'name' => 'pengelola_wisata',
            'guard_name' => 'admin',
        ]);
        $pengelolaWisata->syncPermissions([
            // Destinations - own only
            'view own destinations',
            'create destinations',
            'edit own destinations',
            'delete own destinations',

            // E-Tickets - view related to own destinations
            // 'view all tickets', // REMOVED: Should only use barcode
            'scan tickets',

            // Financial Reports - own only
            'view own financial reports',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: super_admin, admin_wisata, admin_berita, pengelola_wisata');
        $this->command->info('Total permissions: ' . count($permissions));
    }
}
