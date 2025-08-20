<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the permission seeder first
        $this->call(PermissionSeeder::class);

        // Create test users with different roles
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->eventManager()->create([
            'name' => 'Event Manager',
            'email' => 'eventmanager@example.com',
        ]);

        User::factory()->customerService()->create([
            'name' => 'Customer Service',
            'email' => 'support@example.com',
        ]);

        User::factory()->customer()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
        ]);

        // Create additional test users
        User::factory(5)->customer()->create();
    }
}
