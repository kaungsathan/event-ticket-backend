<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create {name} {--permissions=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new role with optional permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->argument('name');
        $permissions = $this->option('permissions');

        // Check if role already exists
        if (Role::where('name', $roleName)->exists()) {
            $this->error("Role '{$roleName}' already exists!");
            return 1;
        }

        // Create the role
        $role = Role::create(['name' => $roleName]);
        $this->info("Role '{$roleName}' created successfully!");

        // Assign permissions if provided
        if (!empty($permissions)) {
            $validPermissions = [];
            $invalidPermissions = [];

            foreach ($permissions as $permissionName) {
                if (Permission::where('name', $permissionName)->exists()) {
                    $validPermissions[] = $permissionName;
                } else {
                    $invalidPermissions[] = $permissionName;
                }
            }

            if (!empty($validPermissions)) {
                $role->givePermissionTo($validPermissions);
                $this->info("Assigned permissions: " . implode(', ', $validPermissions));
            }

            if (!empty($invalidPermissions)) {
                $this->warn("Invalid permissions (not assigned): " . implode(', ', $invalidPermissions));
            }
        }

        return 0;
    }
}

