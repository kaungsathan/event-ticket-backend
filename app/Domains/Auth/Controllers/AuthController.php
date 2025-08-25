<?php

namespace App\Domains\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Auth\Services\AuthService;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $authData = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $authData,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Register new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $authData = $this->authService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => $authData,
            ], 201);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        $profileData = $this->authService->getUserProfile($request->user());

        return response()->json([
            'success' => true,
            'data' => $profileData,
        ]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

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
        $this->authService->logoutAll($request->user());

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
        $tokensData = $this->authService->getUserTokens($request->user());

        return response()->json([
            'success' => true,
            'data' => $tokensData,
        ]);
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        try {
            $revoked = $this->authService->revokeToken($request->user(), $tokenId);

            if (!$revoked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token revoked successfully',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
