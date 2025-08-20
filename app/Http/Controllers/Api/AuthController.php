<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'sometimes|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Delete old tokens for this device (optional)
        $deviceName = $request->device_name ?? 'api-client';
        $user->tokens()->where('name', $deviceName)->delete();

        // Create new token
        $token = $user->createToken($deviceName);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getPermissionNames(),
                    'primary_role' => $user->getPrimaryRole(),
                    'primary_role_display' => $user->getRoleDisplayName(),
                    'can_access_admin' => $user->canAccessAdmin(),
                ],
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:20',
            'device_name' => 'sometimes|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        // Assign default customer role
        $user->assignRole('customer');

        // Create token
        $deviceName = $request->device_name ?? 'api-client';
        $token = $user->createToken($deviceName);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getPermissionNames(),
                    'primary_role' => $user->getPrimaryRole(),
                    'primary_role_display' => $user->getRoleDisplayName(),
                    'can_access_admin' => $user->canAccessAdmin(),
                ],
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
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
                    'name' => $request->user()->currentAccessToken()->name,
                    'abilities' => $request->user()->currentAccessToken()->abilities,
                    'last_used_at' => $request->user()->currentAccessToken()->last_used_at,
                ],
            ],
        ]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
        ]);
    }

    /**
     * Get user's active tokens
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'tokens' => $tokens->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'abilities' => $token->abilities,
                        'last_used_at' => $token->last_used_at,
                        'created_at' => $token->created_at,
                        'is_current' => $token->id === request()->user()->currentAccessToken()->id,
                    ];
                }),
                'total' => $tokens->count(),
            ],
        ]);
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->find($tokenId);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
            ], 404);
        }

        // Don't allow revoking current token
        if ($token->id === $request->user()->currentAccessToken()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot revoke current token. Use logout instead.',
            ], 422);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully',
        ]);
    }
}
