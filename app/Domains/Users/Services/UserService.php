<?php

namespace App\Domains\Users\Services;

use App\Models\User;

class UserService
{
    public function getUserList(array $params = [])
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $role = $params['role'] ?? null;
        $search = $params['search'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortDirection = $params['sort_direction'] ?? 'desc';
        $orderBy = $params['order_by'] ?? 'desc';

        $query = User::query();

        // Role filtering
        if ($role) {
            $query->role($role);
        }

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $allowedSortFields = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortFields) && in_array(strtolower($sortDirection), $allowedSortDirections)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUserById(int $id)
    {
        $user = User::with('roles')->findOrFail($id);

        $userData = $user->toArray();
        $userData['role_id'] = $user->roles->first()?->id;
        $userData['role_name'] = $user->roles->first()?->name;

        return $userData;
    }

    public function update(int $id, array $data): array
    {
        $user = User::findOrFail($id);

        // Extract role_id if present
        $role = null;
        if (isset($data['role'])) {
            $role = $data['role'];
            unset($data['role']); // Remove from data array as it's not a user table field
        }

        // Update user data
        $user->update($data);

        // Update role if role_id was provided
        if ($role) {
            $user->syncRoles([$role]);
        }

        // Return user with role information
        $user = User::with('roles')->find($id);
        $userData = $user->toArray();
        $userData['role_id'] = $user->roles->first()?->id;
        $userData['role_name'] = $user->roles->first()?->name;

        return $userData;
    }

    public function store(array $data): array
    {
        $role = null;
        if (isset($data['role'])) {
            $role = $data['role'];
            unset($data['role']);
        }

        // dd($data);
        $user = User::create($data);

        if ($role) {
            $user->assignRole($role);
        }

        $user = User::with('roles')->find($user->id);
        $userData = $user->toArray();
        $userData['role_id'] = $user->roles->first()?->id;
        $userData['role_name'] = $user->roles->first()?->name;

        return $userData;
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
