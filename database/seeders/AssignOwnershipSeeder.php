<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Place;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignOwnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin user
        $superAdmin = User::role('super_admin', 'admin')->first();

        if (!$superAdmin) {
            $this->command->error('Super Admin not found! Please run AdminUserSeeder first.');
            return;
        }

        // Assign all existing places to super admin
        $placesUpdated = Place::whereNull('created_by')->update(['created_by' => $superAdmin->id]);
        $this->command->info("Assigned {$placesUpdated} places to Super Admin");

        // Assign all existing events to super admin
        $eventsUpdated = Event::whereNull('created_by')->update(['created_by' => $superAdmin->id]);
        $this->command->info("Assigned {$eventsUpdated} events to Super Admin");

        // Assign all existing posts to super admin
        $postsUpdated = Post::whereNull('created_by')->update(['created_by' => $superAdmin->id]);
        $this->command->info("Assigned {$postsUpdated} posts to Super Admin");

        $this->command->info('Ownership assignment completed successfully!');
    }
}
