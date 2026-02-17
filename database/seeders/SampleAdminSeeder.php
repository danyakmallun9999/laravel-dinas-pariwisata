<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * SECURITY: This seeder should ONLY be run in development/testing environments.
     * Uses environment variables for passwords to avoid hardcoded credentials.
     */
    public function run(): void
    {
        // SECURITY: Check if we're in production - abort if so
        if (app()->environment('production')) {
            $this->command->error('SampleAdminSeeder should NOT be run in production!');
            return;
        }

        // SECURITY: Use environment variable for sample admin passwords
        $samplePassword = config('app.sample_admin_password', env('SAMPLE_ADMIN_PASSWORD', 'password'));
        
        if ($samplePassword === 'password') {
            $this->command->warn('⚠️  Using default password "password" for sample admins');
            $this->command->warn('⚠️  Set SAMPLE_ADMIN_PASSWORD in .env for better security');
        }

        // Admin Wisata - Tourism content manager
        $adminWisata = Admin::create([
            'name' => 'Admin Wisata',
            'email' => 'wisata@jepara.go.id',
            'password' => Hash::make($samplePassword),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        $roleWisata = \Spatie\Permission\Models\Role::where('name', 'admin_wisata')->where('guard_name', 'admin')->first();
        if ($roleWisata) {
            $adminWisata->assignRole($roleWisata);
        }
        $this->command->info('Admin Wisata created: wisata@jepara.go.id / ' . $samplePassword);

        // Admin Berita - News and events manager
        $adminBerita = Admin::create([
            'name' => 'Admin Berita',
            'email' => 'berita@jepara.go.id',
            'password' => Hash::make($samplePassword),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        $roleBerita = \Spatie\Permission\Models\Role::where('name', 'admin_berita')->where('guard_name', 'admin')->first();
        if ($roleBerita) {
            $adminBerita->assignRole($roleBerita);
        }
        $this->command->info('Admin Berita created: berita@jepara.go.id / ' . $samplePassword);
    }
}
