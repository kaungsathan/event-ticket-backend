<?php

namespace App\Traits;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

trait HasPermissionHelpers
{
    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is an admin (super-admin or admin).
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Check if user is an event manager.
     */
    public function isEventManager(): bool
    {
        return $this->hasRole('event-manager');
    }

    /**
     * Check if user is customer service.
     */
    public function isCustomerService(): bool
    {
        return $this->hasRole('customer-service');
    }

    /**
     * Check if user is a regular customer.
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Check if user can manage users.
     */
    public function canManageUsers(): bool
    {
        return $this->hasAnyPermission(['view users', 'create users', 'edit users', 'delete users']);
    }

    /**
     * Check if user can manage events.
     */
    public function canManageEvents(): bool
    {
        return $this->hasAnyPermission(['view events', 'create events', 'edit events', 'delete events']);
    }

    /**
     * Check if user can manage tickets.
     */
    public function canManageTickets(): bool
    {
        return $this->hasAnyPermission(['view tickets', 'create tickets', 'edit tickets', 'delete tickets']);
    }

    /**
     * Check if user can manage orders.
     */
    public function canManageOrders(): bool
    {
        return $this->hasAnyPermission(['view orders', 'create orders', 'edit orders', 'delete orders', 'refund orders']);
    }

    /**
     * Check if user can manage organizers.
     */
    public function canManageOrganizers(): bool
    {
        return $this->hasAnyPermission(['view organizers', 'create organizers', 'edit organizers', 'delete organizers']);
    }

    /**
     * Get user's permission names as array.
     */
    public function getAllPermissionNames(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get user's permissions grouped by category.
     */
    public function getPermissionsByCategory(): array
    {
        $permissions = $this->getAllPermissionNames()->pluck('name');
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
     * Get highest priority role for display purposes.
     */
    public function getPrimaryRole(): ?string
    {
        $roleHierarchy = [
            'super-admin' => 5,
            'admin' => 4,
            'event-manager' => 3,
            'customer-service' => 2,
            'customer' => 1,
        ];

        $userRoles = $this->roles->pluck('name')->toArray();
        $highestPriority = 0;
        $primaryRole = null;

        foreach ($userRoles as $role) {
            $priority = $roleHierarchy[$role] ?? 0;
            if ($priority > $highestPriority) {
                $highestPriority = $priority;
                $primaryRole = $role;
            }
        }

        return $primaryRole;
    }

    /**
     * Check if user has permission to perform action on model.
     */
    public function canPerformAction(string $action, string $model): bool
    {
        $permission = $action . ' ' . strtolower($model);
        return $this->can($permission);
    }

    /**
     * Get available actions for a model based on user permissions.
     */
    public function getAvailableActions(string $model): array
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $availableActions = [];
        $modelLower = strtolower($model);

        foreach ($actions as $action) {
            $permission = $action . ' ' . $modelLower;
            if ($this->can($permission)) {
                $availableActions[] = $action;
            }
        }

        return $availableActions;
    }

    /**
     * Check if user can access admin panel.
     */
    public function canAccessAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'event-manager', 'customer-service']);
    }

    /**
     * Get user's role display name.
     */
    public function getRoleDisplayName(): string
    {
        $primaryRole = $this->getPrimaryRole();

        $displayNames = [
            'super-admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'event-manager' => 'Event Manager',
            'customer-service' => 'Customer Service',
            'customer' => 'Customer',
        ];

        return $displayNames[$primaryRole] ?? 'Unknown';
    }

    /**
     * Check if user has elevated privileges.
     */
    public function hasElevatedPrivileges(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Get permissions that user has via roles (not direct permissions).
     */
    public function getRolePermissions(): Collection
    {
        return $this->getPermissionsViaRoles();
    }

    /**
     * Get direct permissions (not via roles).
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }
}
