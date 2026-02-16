<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use Admin model so Spatie Permission resolves the correct 'admin' guard
        $admin = Admin::create([
            'name' => 'Admin Pariwisata',
            'email' => 'admin@jepara.go.id',
            'password' => Hash::make('adminwisata'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Assign super_admin role
        $role = \Spatie\Permission\Models\Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($role) {
            $admin->assignRole($role);
        }

        $this->command->info('Super Admin created: admin@jepara.go.id');
    }
}
