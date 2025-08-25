<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating users...');

        // Create predefined admin users
        $this->createAdminUsers();

        // Create test users for development
        $this->createTestUsers();

        // Create bulk users for testing
        $this->createBulkUsers();

        $this->command->info('Users created successfully!');
    }

    /**
     * Create admin users with known credentials.
     */
    private function createAdminUsers(): void
    {
        $this->command->info('Creating admin users...');

        // Super Admin
        User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@eventticket.com',
            'phone' => '+1234567890',
            'password' => Hash::make('SuperAdmin123!'),
            'email_verified_at' => now(),
        ])->assignRole('super-admin');

        // System Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@eventticket.com',
            'phone' => '+1234567891',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
        ])->assignRole('admin');

        // Backup Admin
        User::factory()->create([
            'name' => 'Backup Admin',
            'email' => 'backup@eventticket.com',
            'phone' => '+1234567892',
            'password' => Hash::make('Backup123!'),
            'email_verified_at' => now(),
        ])->assignRole('admin');
    }

    /**
     * Create test users for different scenarios.
     */
    private function createTestUsers(): void
    {
        $this->command->info('Creating test users...');

        // Event Managers
        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice.manager@eventticket.com',
            'phone' => '+1234567893',
            'password' => Hash::make('Manager123!'),
            'email_verified_at' => now(),
        ])->assignRole('event-manager');

        User::factory()->create([
            'name' => 'Bob Wilson',
            'email' => 'bob.manager@eventticket.com',
            'phone' => '+1234567894',
            'password' => Hash::make('Manager123!'),
            'email_verified_at' => now(),
        ])->assignRole('event-manager');

        // Customer Service Representatives
        User::factory()->create([
            'name' => 'Carol Davis',
            'email' => 'carol.support@eventticket.com',
            'phone' => '+1234567895',
            'password' => Hash::make('Support123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer-service');

        User::factory()->create([
            'name' => 'David Brown',
            'email' => 'david.support@eventticket.com',
            'phone' => '+1234567896',
            'password' => Hash::make('Support123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer-service');

        // Premium Customers
        User::factory()->create([
            'name' => 'Emma Smith',
            'email' => 'emma.customer@example.com',
            'phone' => '+1234567897',
            'password' => Hash::make('Customer123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer');

        User::factory()->create([
            'name' => 'Frank Miller',
            'email' => 'frank.customer@example.com',
            'phone' => '+1234567898',
            'password' => Hash::make('Customer123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer');

        // Unverified customer (for testing email verification)
        User::factory()->create([
            'name' => 'Grace Taylor',
            'email' => 'grace.unverified@example.com',
            'phone' => '+1234567899',
            'password' => Hash::make('Customer123!'),
            'email_verified_at' => null,
        ])->assignRole('customer');
    }

    /**
     * Create bulk users for performance testing.
     */
    private function createBulkUsers(): void
    {
        $this->command->info('Creating bulk users...');

        // Create multiple event managers
        User::factory(5)
            ->eventManager()
            ->create();

        // Create multiple customer service reps
        User::factory(3)
            ->customerService()
            ->create();

        // Create many customers with mixed verification status
        User::factory(20)
            ->customer()
            ->create();

        // Create some unverified customers
        User::factory(5)
            ->customer()
            ->unverified()
            ->create();

        // Create some customers with specific domains for testing
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'company.com'];

        foreach ($domains as $domain) {
            User::factory(2)
                ->customer()
                ->state(function (array $attributes) use ($domain) {
                    return [
                        'email' => fake()->unique()->userName() . '@' . $domain,
                    ];
                })
                ->create();
        }
    }
}
