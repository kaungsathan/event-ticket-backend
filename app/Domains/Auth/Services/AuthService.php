<?php

namespace App\Domains\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Authenticate user and create token.
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Delete old tokens for this device
        $deviceName = $credentials['device_name'] ?? 'api-client';
        $user->tokens()->where('name', $deviceName)->delete();

        // Create new token
        $token = $user->createToken($deviceName);

        return [
            'user' => $this->formatUserData($user),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new user.
     */
    public function register(array $userData): array
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'phone' => $userData['phone'] ?? null,
        ]);

        // Assign default customer role
        $user->assignRole('customer');

        // Create token
        $deviceName = $userData['device_name'] ?? 'api-client';
        $token = $user->createToken($deviceName);

        return [
            'user' => $this->formatUserData($user),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Get authenticated user data with extended information.
     */
    public function getUserProfile(User $user): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
                'permissions_by_category' => $user->getPermissionsByCategory(),
                'primary_role' => $user->getPrimaryRole(),
                'primary_role_display' => $user->getRoleDisplayName(),
                'can_access_admin' => $user->canAccessAdmin(),
                'capabilities' => [
                    'can_manage_users' => $user->canManageUsers(),
                    'can_manage_events' => $user->canManageEvents(),
                    'can_manage_tickets' => $user->canManageTickets(),
                    'can_manage_orders' => $user->canManageOrders(),
                    'can_view_reports' => $user->canViewReports(),
                ],
                'available_actions' => [
                    'users' => $user->getAvailableActions('users'),
                    'events' => $user->getAvailableActions('events'),
                    'tickets' => $user->getAvailableActions('tickets'),
                    'orders' => $user->getAvailableActions('orders'),
                    'reports' => $user->getAvailableActions('reports'),
                ],
            ],
            'current_token' => [
                'name' => $user->currentAccessToken()->name,
                'abilities' => $user->currentAccessToken()->abilities,
                'last_used_at' => $user->currentAccessToken()->last_used_at,
            ],
        ];
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Logout user from all devices (revoke all tokens).
     */
    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Get user's active tokens.
     */
    public function getUserTokens(User $user): array
    {
        $tokens = $user->tokens()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);

        return [
            'tokens' => $tokens->map(function ($token) use ($user) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                    'is_current' => $token->id === $user->currentAccessToken()->id,
                ];
            }),
            'total' => $tokens->count(),
        ];
    }

    /**
     * Revoke a specific token.
     */
    public function revokeToken(User $user, int $tokenId): bool
    {
        $token = $user->tokens()->find($tokenId);

        if (!$token) {
            return false;
        }

        // Don't allow revoking current token
        if ($token->id === $user->currentAccessToken()->id) {
            throw new \InvalidArgumentException('Cannot revoke current token. Use logout instead.');
        }

        $token->delete();
        return true;
    }

    /**
     * Format user data for API responses.
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getPermissionNames(),
            'primary_role' => $user->getPrimaryRole(),
            'primary_role_display' => $user->getRoleDisplayName(),
            'can_access_admin' => $user->canAccessAdmin(),
        ];
    }
}
