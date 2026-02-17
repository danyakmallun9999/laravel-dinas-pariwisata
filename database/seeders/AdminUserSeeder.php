<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * SECURITY: This seeder should ONLY be run in development/testing environments.
     * For production, use a dedicated command or migration to create initial admin.
     */
    public function run(): void
    {
        // SECURITY: Check if we're in production - abort if so
        if (app()->environment('production')) {
            $this->command->error('AdminUserSeeder should NOT be run in production!');
            $this->command->error('Use a dedicated command or migration to create initial admin user.');
            return;
        }

        // SECURITY: Use environment variable for initial password, or generate random one
        $password = env('INITIAL_ADMIN_PASSWORD');
        
        if (empty($password)) {
            // Generate a random password that must be changed on first login
            $password = Str::random(32);
            $this->command->warn('⚠️  No INITIAL_ADMIN_PASSWORD set in .env');
            $this->command->warn('⚠️  Generated random password - MUST be changed on first login!');
            $this->command->warn('⚠️  Password: ' . $password);
        }

        // Use Admin model so Spatie Permission resolves the correct 'admin' guard
        $admin = Admin::create([
            'name' => 'Admin Pariwisata',
            'email' => 'admin@jepara.go.id',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Assign super_admin role
        $role = \Spatie\Permission\Models\Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($role) {
            $admin->assignRole($role);
        }

        $this->command->info('✅ Super Admin created: admin@jepara.go.id');
        
        if (empty(env('INITIAL_ADMIN_PASSWORD'))) {
            $this->command->warn('⚠️  IMPORTANT: Change password immediately after first login!');
        }
    }
}
