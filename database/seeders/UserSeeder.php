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
        // $this->createBulkUsers();

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
            'username' => 'superadmin',
            'full_name' => 'Super Administrator',
            'email' => 'superadmin@eventticket.com',
            'phone' => '+1234567890',
            'status' => 'active',
            'password' => Hash::make('SuperAdmin123!'),
            'email_verified_at' => now(),
        ])->assignRole('super-admin');

        // System Admin
        User::factory()->create([
            'username' => 'admin',
            'full_name' => 'System Admin',
            'email' => 'admin@eventticket.com',
            'phone' => '+1234567891',
            'status' => 'active',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
        ])->assignRole('admin');

        // Backup Admin
        User::factory()->create([
            'username' => 'backupadmin',
            'full_name' => 'Backup Admin',
            'email' => 'backup@eventticket.com',
            'phone' => '+1234567892',
            'status' => 'active',
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
            'username' => 'alice',
            'full_name' => 'Alice Johnson',
            'email' => 'alice.manager@eventticket.com',
            'phone' => '+1234567893',
            'status' => 'active',
            'password' => Hash::make('Manager123!'),
            'email_verified_at' => now(),
        ])->assignRole('event-manager');

        User::factory()->create([
            'username' => 'bob',
            'full_name' => 'Bob Wilson',
            'email' => 'bob.manager@eventticket.com',
            'phone' => '+1234567894',
            'status' => 'active',
            'password' => Hash::make('Manager123!'),
            'email_verified_at' => now(),
        ])->assignRole('event-manager');

        // Customer Service Representatives
        User::factory()->create([
            'username' => 'carol',
            'full_name' => 'Carol Davis',
            'email' => 'carol.support@eventticket.com',
            'phone' => '+1234567895',
            'status' => 'active',
            'password' => Hash::make('Support123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer-service');

        User::factory()->create([
            'username' => 'david',
            'full_name' => 'David Brown',
            'email' => 'david.support@eventticket.com',
            'phone' => '+1234567896',
            'status' => 'active',
            'password' => Hash::make('Support123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer-service');

        // Premium Customers
        User::factory()->create([
            'username' => 'emma',
            'full_name' => 'Emma Smith',
            'email' => 'emma.customer@example.com',
            'phone' => '+1234567897',
            'status' => 'active',
            'password' => Hash::make('Customer123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer');

        User::factory()->create([
            'username' => 'frank',
            'full_name' => 'Frank Miller',
            'email' => 'frank.customer@example.com',
            'phone' => '+1234567898',
            'status' => 'active',
            'password' => Hash::make('Customer123!'),
            'email_verified_at' => now(),
        ])->assignRole('customer');

        // Unverified customer (for testing email verification)
        User::factory()->create([
            'username' => 'grace',
            'full_name' => 'Grace Taylor',
            'email' => 'grace.unverified@example.com',
            'phone' => '+1234567899',
            'status' => 'active',
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
