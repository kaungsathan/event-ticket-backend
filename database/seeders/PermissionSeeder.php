<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Event Ticket System
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Event Management
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',

            // Ticket Management
            'view tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'validate tickets',

            // Order Management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'refund orders',

            // Report Management
            'view reports',
            'export reports',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view roles', 'create roles', 'edit roles',
            'view events', 'create events', 'edit events', 'publish events',
            'view tickets', 'create tickets', 'edit tickets', 'validate tickets',
            'view orders', 'create orders', 'edit orders', 'refund orders',
            'view reports', 'export reports',
        ]);

        $eventManagerRole = Role::create(['name' => 'event-manager']);
        $eventManagerRole->givePermissionTo([
            'view events', 'create events', 'edit events', 'publish events',
            'view tickets', 'create tickets', 'edit tickets',
            'view orders', 'edit orders',
            'view reports',
        ]);

        $customerServiceRole = Role::create(['name' => 'customer-service']);
        $customerServiceRole->givePermissionTo([
            'view users', 'edit users',
            'view events',
            'view tickets', 'validate tickets',
            'view orders', 'edit orders', 'refund orders',
        ]);

        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view events',
            'create orders',
        ]);

        $this->command->info('Permissions and roles created successfully!');
    }
}
