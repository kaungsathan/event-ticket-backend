<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ListRolesPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:list {--permissions : Show permissions for each role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all roles and optionally their permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showPermissions = $this->option('permissions');

        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->info('No roles found.');
            return 0;
        }

        $this->info('Roles:');
        $this->newLine();

        foreach ($roles as $role) {
            $this->line("• <fg=green>{$role->name}</>");

            if ($showPermissions) {
                $permissions = $role->permissions;
                if ($permissions->isNotEmpty()) {
                    $this->line("  Permissions:");
                    foreach ($permissions as $permission) {
                        $this->line("    - {$permission->name}");
                    }
                } else {
                    $this->line("  <fg=yellow>No permissions assigned</>");
                }
                $this->newLine();
            }
        }

        if (!$showPermissions) {
            $this->newLine();
            $this->info('Use --permissions flag to see permissions for each role.');
        }

        return 0;
    }
}

