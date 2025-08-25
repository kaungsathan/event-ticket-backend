<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:users {--fresh : Truncate users table before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed users with predefined roles and test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Truncating users table...');
            DB::table('model_has_roles')->where('model_type', User::class)->delete();
            DB::table('model_has_permissions')->where('model_type', User::class)->delete();
            User::truncate();
        }

        $this->info('Starting user seeding...');

        $seeder = new UserSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $userCounts = $this->getUserCounts();

        $this->newLine();
        $this->info('User seeding completed!');
        $this->newLine();

        $this->table(
            ['Role', 'Count'],
            $userCounts
        );

        $this->newLine();
        $this->info('Test credentials:');
        $this->line('Super Admin: superadmin@eventticket.com / SuperAdmin123!');
        $this->line('Admin: admin@eventticket.com / Admin123!');
        $this->line('Manager: alice.manager@eventticket.com / Manager123!');
        $this->line('Support: carol.support@eventticket.com / Support123!');
        $this->line('Customer: emma.customer@example.com / Customer123!');
    }

    /**
     * Get user counts by role.
     */
    private function getUserCounts(): array
    {
        $roles = ['super-admin', 'admin', 'event-manager', 'customer-service', 'customer'];
        $counts = [];

        foreach ($roles as $role) {
            $count = User::role($role)->count();
            $counts[] = [ucwords(str_replace('-', ' ', $role)), $count];
        }

        $counts[] = ['Total Users', User::count()];
        $counts[] = ['Verified Users', User::whereNotNull('email_verified_at')->count()];
        $counts[] = ['Unverified Users', User::whereNull('email_verified_at')->count()];

        return $counts;
    }
}
