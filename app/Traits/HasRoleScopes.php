<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasRoleScopes
{
    /**
     * Scope to get users with admin roles.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->role(['super-admin', 'admin']);
    }

    /**
     * Scope to get users with event manager role.
     */
    public function scopeEventManagers(Builder $query): Builder
    {
        return $query->role('event-manager');
    }

    /**
     * Scope to get users with customer service role.
     */
    public function scopeCustomerService(Builder $query): Builder
    {
        return $query->role('customer-service');
    }

    /**
     * Scope to get users with customer role.
     */
    public function scopeCustomers(Builder $query): Builder
    {
        return $query->role('customer');
    }

    /**
     * Scope to get users with elevated privileges.
     */
    public function scopeWithElevatedPrivileges(Builder $query): Builder
    {
        return $query->role(['super-admin', 'admin']);
    }

    /**
     * Scope to get users who can manage events.
     */
    public function scopeCanManageEvents(Builder $query): Builder
    {
        return $query->permission(['create events', 'edit events', 'delete events']);
    }

    /**
     * Scope to get users who can manage users.
     */
    public function scopeCanManageUsers(Builder $query): Builder
    {
        return $query->permission(['create users', 'edit users', 'delete users']);
    }

    /**
     * Scope to get users who can view reports.
     */
    public function scopeCanViewReports(Builder $query): Builder
    {
        return $query->permission(['view reports', 'export reports']);
    }

    /**
     * Scope to get users excluding specific roles.
     */
    public function scopeExcludingRoles(Builder $query, array $roles): Builder
    {
        return $query->whereDoesntHave('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }

    /**
     * Scope to get users with any of the specified permissions.
     */
    public function scopeWithAnyPermission(Builder $query, array $permissions): Builder
    {
        return $query->where(function ($q) use ($permissions) {
            foreach ($permissions as $permission) {
                $q->orWhereHas('permissions', function ($permQuery) use ($permission) {
                    $permQuery->where('name', $permission);
                })->orWhereHas('roles.permissions', function ($rolePermQuery) use ($permission) {
                    $rolePermQuery->where('name', $permission);
                });
            }
        });
    }

    /**
     * Scope to get users with all of the specified permissions.
     */
    public function scopeWithAllPermissions(Builder $query, array $permissions): Builder
    {
        foreach ($permissions as $permission) {
            $query->where(function ($q) use ($permission) {
                $q->whereHas('permissions', function ($permQuery) use ($permission) {
                    $permQuery->where('name', $permission);
                })->orWhereHas('roles.permissions', function ($rolePermQuery) use ($permission) {
                    $rolePermQuery->where('name', $permission);
                });
            });
        }

        return $query;
    }

    /**
     * Scope to get users by role priority (highest first).
     */
    public function scopeByRolePriority(Builder $query): Builder
    {
        return $query->leftJoin('model_has_roles', function ($join) {
            $join->on('users.id', '=', 'model_has_roles.model_id')
                 ->where('model_has_roles.model_type', '=', static::class);
        })
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->orderByRaw("CASE roles.name
            WHEN 'super-admin' THEN 1
            WHEN 'admin' THEN 2
            WHEN 'event-manager' THEN 3
            WHEN 'customer-service' THEN 4
            WHEN 'customer' THEN 5
            ELSE 6 END")
        ->select('users.*')
        ->distinct();
    }

    /**
     * Scope to get users who can access admin panel.
     */
    public function scopeAdminAccess(Builder $query): Builder
    {
        return $query->role(['super-admin', 'admin', 'event-manager', 'customer-service']);
    }

    /**
     * Scope to get recently active users with specific roles.
     */
    public function scopeRecentlyActiveWithRole(Builder $query, string $role, int $days = 30): Builder
    {
        return $query->role($role)
                    ->where('updated_at', '>=', now()->subDays($days));
    }
}

