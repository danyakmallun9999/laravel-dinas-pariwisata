<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Wisata - Tourism content manager
        $adminWisata = Admin::create([
            'name' => 'Admin Wisata',
            'email' => 'wisata@jepara.go.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        $roleWisata = \Spatie\Permission\Models\Role::where('name', 'admin_wisata')->where('guard_name', 'admin')->first();
        if ($roleWisata) {
            $adminWisata->assignRole($roleWisata);
        }
        $this->command->info('Admin Wisata created: wisata@jepara.go.id / password');

        // Admin Berita - News and events manager
        $adminBerita = Admin::create([
            'name' => 'Admin Berita',
            'email' => 'berita@jepara.go.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        $roleBerita = \Spatie\Permission\Models\Role::where('name', 'admin_berita')->where('guard_name', 'admin')->first();
        if ($roleBerita) {
            $adminBerita->assignRole($roleBerita);
        }
        $this->command->info('Admin Berita created: berita@jepara.go.id / password');
    }
}
