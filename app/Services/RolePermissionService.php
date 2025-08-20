<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RolePermissionService
{
    /**
     * Create a new role with optional permissions.
     */
    public function createRole(string $roleName, array $permissions = []): Role
    {
        if (Role::where('name', $roleName)->exists()) {
            throw new \InvalidArgumentException("Role '{$roleName}' already exists.");
        }

        $role = Role::create(['name' => $roleName]);

        if (!empty($permissions)) {
            $this->assignPermissionsToRole($role, $permissions);
        }

        return $role;
    }

    /**
     * Create a new permission.
     */
    public function createPermission(string $permissionName): Permission
    {
        if (Permission::where('name', $permissionName)->exists()) {
            throw new \InvalidArgumentException("Permission '{$permissionName}' already exists.");
        }

        return Permission::create(['name' => $permissionName]);
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissionsToRole(Role $role, array $permissions): Role
    {
        $validPermissions = $this->validatePermissions($permissions);
        $role->givePermissionTo($validPermissions);

        return $role;
    }

    /**
     * Remove permissions from a role.
     */
    public function removePermissionsFromRole(Role $role, array $permissions): Role
    {
        $validPermissions = $this->validatePermissions($permissions);
        $role->revokePermissionTo($validPermissions);

        return $role;
    }

    /**
     * Assign a role to a user.
     */
    public function assignRoleToUser(User $user, string $roleName): User
    {
        if (!Role::where('name', $roleName)->exists()) {
            throw new ModelNotFoundException("Role '{$roleName}' not found.");
        }

        if ($user->hasRole($roleName)) {
            throw new \InvalidArgumentException("User already has the role '{$roleName}'.");
        }

        $user->assignRole($roleName);

        return $user;
    }

    /**
     * Remove a role from a user.
     */
    public function removeRoleFromUser(User $user, string $roleName): User
    {
        if (!$user->hasRole($roleName)) {
            throw new \InvalidArgumentException("User does not have the role '{$roleName}'.");
        }

        $user->removeRole($roleName);

        return $user;
    }

    /**
     * Give direct permissions to a user.
     */
    public function givePermissionToUser(User $user, array $permissions): User
    {
        $validPermissions = $this->validatePermissions($permissions);
        $user->givePermissionTo($validPermissions);

        return $user;
    }

    /**
     * Revoke direct permissions from a user.
     */
    public function revokePermissionFromUser(User $user, array $permissions): User
    {
        $validPermissions = $this->validatePermissions($permissions);
        $user->revokePermissionTo($validPermissions);

        return $user;
    }

    /**
     * Get all roles with their permissions.
     */
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get all permissions grouped by category.
     */
    public function getPermissionsByCategory(): array
    {
        $permissions = Permission::all()->pluck('name');
        $categorized = [];

        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission);
            $action = $parts[0] ?? 'other';
            $category = $parts[1] ?? 'general';

            $categorized[$category][] = $permission;
        }

        return $categorized;
    }

    /**
     * Get user's effective permissions (from roles and direct permissions).
     */
    public function getUserEffectivePermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    /**
     * Sync user roles (remove all current roles and assign new ones).
     */
    public function syncUserRoles(User $user, array $roleNames): User
    {
        // Validate all roles exist
        foreach ($roleNames as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                throw new ModelNotFoundException("Role '{$roleName}' not found.");
            }
        }

        $user->syncRoles($roleNames);

        return $user;
    }

    /**
     * Sync role permissions (remove all current permissions and assign new ones).
     */
    public function syncRolePermissions(Role $role, array $permissions): Role
    {
        $validPermissions = $this->validatePermissions($permissions);
        $role->syncPermissions($validPermissions);

        return $role;
    }

    /**
     * Get users with a specific role.
     */
    public function getUsersByRole(string $roleName): Collection
    {
        if (!Role::where('name', $roleName)->exists()) {
            throw new ModelNotFoundException("Role '{$roleName}' not found.");
        }

        return User::role($roleName)->get();
    }

    /**
     * Get users with a specific permission.
     */
    public function getUsersByPermission(string $permissionName): Collection
    {
        if (!Permission::where('name', $permissionName)->exists()) {
            throw new ModelNotFoundException("Permission '{$permissionName}' not found.");
        }

        return User::permission($permissionName)->get();
    }

    /**
     * Delete a role (only if no users are assigned to it).
     */
    public function deleteRole(string $roleName, bool $force = false): bool
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            throw new ModelNotFoundException("Role '{$roleName}' not found.");
        }

        // Check if role is assigned to any users
        if (!$force && $role->users()->count() > 0) {
            throw new \InvalidArgumentException("Cannot delete role '{$roleName}' because it is assigned to users. Use force=true to override.");
        }

        return $role->delete();
    }

    /**
     * Delete a permission (only if not assigned to any role or user).
     */
    public function deletePermission(string $permissionName, bool $force = false): bool
    {
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            throw new ModelNotFoundException("Permission '{$permissionName}' not found.");
        }

        // Check if permission is assigned to any roles or users
        if (!$force && ($permission->roles()->count() > 0 || $permission->users()->count() > 0)) {
            throw new \InvalidArgumentException("Cannot delete permission '{$permissionName}' because it is assigned to roles or users. Use force=true to override.");
        }

        return $permission->delete();
    }

    /**
     * Validate that permissions exist.
     */
    private function validatePermissions(array $permissions): array
    {
        $existingPermissions = Permission::whereIn('name', $permissions)->pluck('name')->toArray();
        $invalidPermissions = array_diff($permissions, $existingPermissions);

        if (!empty($invalidPermissions)) {
            throw new ModelNotFoundException("Permissions not found: " . implode(', ', $invalidPermissions));
        }

        return $existingPermissions;
    }
}

