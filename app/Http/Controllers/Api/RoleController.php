<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected RolePermissionService $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        $roles = $this->rolePermissionService->getAllRolesWithPermissions();

        return response()->json([
            'success' => true,
            'data' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $this->getRoleDisplayName($role->name),
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users()->count(),
                    'permissions' => $role->permissions->pluck('name'),
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];
            }),
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            DB::beginTransaction();

            $role = $this->rolePermissionService->createRole(
                $request->name,
                $request->permissions ?? []
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $this->getRoleDisplayName($role->name),
                    'permissions' => $role->permissions->pluck('name'),
                    'created_at' => $role->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions', 'users');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $this->getRoleDisplayName($role->name),
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'category' => $this->getPermissionCategory($permission->name),
                    ];
                }),
                'users' => $role->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                }),
                'users_count' => $role->users->count(),
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            DB::beginTransaction();

            if ($request->has('name')) {
                $role->update(['name' => $request->name]);
            }

            if ($request->has('permissions')) {
                $this->rolePermissionService->syncRolePermissions($role, $request->permissions);
            }

            DB::commit();

            $role->load('permissions');

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $this->getRoleDisplayName($role->name),
                    'permissions' => $role->permissions->pluck('name'),
                    'updated_at' => $role->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            // Check if role is assigned to users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users',
                ], 422);
            }

            $this->rolePermissionService->deleteRole($role->name);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get all available permissions grouped by category.
     */
    public function permissions(): JsonResponse
    {
        $permissions = $this->rolePermissionService->getPermissionsByCategory();

        return response()->json([
            'success' => true,
            'data' => $permissions,
        ]);
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            $this->rolePermissionService->assignPermissionsToRole($role, $request->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully',
                'data' => [
                    'role' => $role->name,
                    'permissions' => $request->permissions,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning permissions: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove permissions from a role.
     */
    public function revokePermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            $this->rolePermissionService->removePermissionsFromRole($role, $request->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions revoked successfully',
                'data' => [
                    'role' => $role->name,
                    'permissions' => $request->permissions,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error revoking permissions: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get role display name.
     */
    private function getRoleDisplayName(string $role): string
    {
        $displayNames = [
            'super-admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'event-manager' => 'Event Manager',
            'customer-service' => 'Customer Service',
            'customer' => 'Customer',
        ];

        return $displayNames[$role] ?? ucwords(str_replace('-', ' ', $role));
    }

    /**
     * Get permission category.
     */
    private function getPermissionCategory(string $permission): string
    {
        $parts = explode(' ', $permission);
        return $parts[1] ?? 'general';
    }
}
