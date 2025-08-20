<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissionName = $this->argument('name');

        // Check if permission already exists
        if (Permission::where('name', $permissionName)->exists()) {
            $this->error("Permission '{$permissionName}' already exists!");
            return 1;
        }

        // Create the permission
        Permission::create(['name' => $permissionName]);
        $this->info("Permission '{$permissionName}' created successfully!");

        return 0;
    }
}

